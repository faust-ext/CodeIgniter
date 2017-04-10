<?php
class urgent extends CI_Controller {

	function __construct()
    {
		parent::__construct(); 
		$this->load->model('mainform_model');
		
    }
    public function index()
    {


        $account_array      = $this->mainform_model->get_divide_accounts();

	foreach ($account_array as $value)
	{

        // not to run urgent engine into rolover

	  $account_time_boundaries = $this->mainform_model->get_time_boundaries($value->login);
	  $current_time = time();

	  if ($current_time > $account_time_boundaries[0]->rolover_start AND $current_time < $account_time_boundaries[0]->rolover_end)
	    {
	     echo "Urgent module dont run into rolover!\r\n";
	     return true;

	    }
	       if (isset($GLOBALS['urgent_flag']) AND $GLOBALS['urgent_flag'] == "1")
		{
			 echo "Urgent out module suppressed on account ".$value->login."\r\n";
			 return true;
		}



		$urgent_u_requests = $this->mainform_model->get_trader_urgent_requests($value->login);
		$urgent_flag     = $this->mainform_model->get_urgent_flag($value->login);

		if (trim($this->stopout_status($value->login,'from urgent')) == "1")
	        {

			foreach ($urgent_u_requests as $value_u)
			{
			   $this->mainform_model->change_request_status($value_u->request_id,'2');
			   $comm_changed = 'Urgent trader withdraw request declined due to the stopout (urgent)';
			   $this->mainform_model->change_request_comment($comm_changed,$value_u->request_id);
			}
		
		 echo "Account ".$value->login." is dipped - from urgent trader\r\n";
		 return true;
		 
	        }


		foreach ($urgent_u_requests as $value_u)
		{
		   $divide = $this->mainform_model->get_divide_flag($value->login);
		   if ($divide[0]->divide == '1')
	           {
			echo "Urgent trader request on account ".$value->login." skipped because of divide\r\n";
	           }
		   else
		   {

		   $implement = TRUE;
	           $add_to_stopout = TRUE;
			if ($urgent_flag[0]->w_b == 0)
			   {
			   // decline urgent requests
			   $this->mainform_model->change_request_status($value_u->request_id,'2');
			   $comm_changed = 'Urgent withdraw request declined due to the offer';
			   $this->mainform_model->change_request_comment($comm_changed,$value_u->request_id);
			   }
			else
			   {
			        
				$fp = fsockopen("80.93.48.133", 10025, $errno, $errstr, 30);
				if (!$fp)
				{
				$fp = reconnect();
				}
				   $out = "11\r\n";
				   fwrite($fp, $out);
				   $out = "11\r\n";
				   fwrite($fp, $out);

				   fgets($fp,128);
				   fgets($fp,128);
				   fgets($fp,128);
				   fgets($fp,128);
				   fgets($fp,128);
				   fgets($fp,128);

			       // obtain previous trade period result and current balance
			       $balance         = $this->obtain_balance($fp, $value_u->request_acc_number);
			       $debt_inout      = $this->mainform_model->obtain_debt_inout($value_u->request_acc_number);
	       		       $history_common  = $this->mainform_model->obtain_common_history($value_u->request_acc_number);
	       		       $history_invest  = $this->mainform_model->obtain_invest_history($value_u->request_acc_number);
	       		       $history_trader  = $this->mainform_model->obtain_trader_history($value_u->request_acc_number);
			       $p_total         = $this->mainform_model->get_previous_total($value_u->request_acc_number);
		
				   if (count($p_total) == 0)
					$p_total[0]->pamm_tp_total = 0;

				$earn = $this->mainform_model->obtain_balance_before_divide($value_u->request_acc_number);
					$p_total[0]->pamm_tp_total = $earn[0]->earn;				



			       // obtain debt and inoutdebt flag
			       $debt = $balance - $p_total[0]->pamm_tp_total + $debt_inout[0]->debt_inout; 


                       	       $inoutdebt = $this->mainform_model->obtain_inoutdebt($value_u->request_acc_number);
			        	
			       if ($debt < 0  AND $inoutdebt[0]->inoutdebt == 0)  // withdrawal are impossible
			           {
					   $this->mainform_model->change_request_status($value_u->request_id,'2');
					   $comm_changed = 'Urgent withdraw request declined because of owed negative debt';
					   $this->mainform_model->change_request_comment($comm_changed,$value_u->request_id);
        				   }
			           else				   			   //implement urgent record
				   {

				    $number_of_open_orders = $this->open_orders($fp,$value_u->request_acc_number);

				    if (trim($number_of_open_orders) != '0' AND trim($number_of_open_orders) != '999999035')  // postpone it until next launch of urgent engine
				    {
				          echo "Urgent trader withdrawal on ".$value_u->request_acc_number." skipped because of ".$number_of_open_orders." open orders\r\n";

				    }
				    else  // implement requests without postpone
				    {
				       // locking the account
				       $this->acc_lock_unlock($value_u->request_acc_number,0);
	
				       // implementing withdrawal
				       $comm = 'urgent wd trader';
				       echo "Urgent out trader -".$value_u->request_summ.",".$value_u->request_acc_number.",".$comm."\r\n";
				       $profit = $this->divide_not_into_rolover($fp,$value_u->request_acc_number);


				       // last check to current profit before changing deposit
					 $trader = $profit[0];
				         if ($trader[1] < 0)  // loss
						   $real_balance = $history_trader[0]->pamm_clients_stat_sum + $trader[1];
					 else
						   $real_balance = $history_trader[0]->pamm_clients_stat_sum;


					echo " Real balance = ".$real_balance."\r\n";

                                    	if ($real_balance - 300 > $value_u->request_summ)
						{

						// summ is enough
						  if ($value_u->request_wallet != 'USD')
						   $quote  = $this->get_quote($value_u->request_wallet);
						  else
						   $quote = 1;
						   $request_summ = $value_u->request_summ;
                				   $request_summ_origin = $value_u->request_summ*$quote;
						  echo "1 case - Summ - 300 > out \r\n" ;
						}
					elseif ($real_balance - 300 < $value_u->request_summ AND $real_balance > 300)
						{

						   echo " comission = ".$commission." request_summ= ".$request_summ."\r\n";
						// rest of the sum could be withdrawn
						   $request_summ = $real_balance - 300;
						  if ($value_u->request_wallet != 'USD')
							   $quote  = $this->get_quote($value_u->request_wallet);
						  else
							   $quote = 1;
						   $request_summ_origin = $request_summ*$quote;
						   $comm = 'Urgent trader request of '.$value_u->request_summ.' withdraw implemented of '.$request_summ.' amount';		
						   $this->mainform_model->change_request($request_summ,$request_summ_origin,$quote,$comm,$value_u->request_id);
						   $comm = 'urgent wd trader ($'.$value_u->request_summ.'|$'.$request_summ.')';
						  echo "2 case - between \r\n" ;
						}
				      elseif ($real_balance <= 300)
						{
						// decline the request
						   $this->mainform_model->change_request_status($value_u->request_id,'2');
						   $comm_changed = 'Urgent withdraw request declined because of '.$real_balance.' means';
						   $this->mainform_model->change_request_comment($comm_changed,$value_u->request_id);
						   $implement = FALSE;			
						   echo "3 case - deny \r\n" ;				
						   $add_to_stopout = FALSE;
						}
				       if ($implement)
					{
					       // set stopout flag
					       $this->mainform_model->set_stopout_share_flag($value_u->request_acc_number);

					       // delete from stopout
					       echo "Del from stopout - urgent withdrawal\r\n";
					       $this->stopout('DEL',$value_u->request_acc_number,NULL,NULL,"from urgent");		 

					       $this->change_deposit($fp,200,$value_u->request_acc_number,$request_summ,$comm,'1-4');


						$debt = $this->mainform_model->obtain_debt($value_u->request_acc_number);
						$d = $debt[0]->debt;

						$debt_for_trade = $this->mainform_model->obtain_debt_for_trade($value_u->request_acc_number);

					       $summ_to_stopout = $history_common[0]->pamm_clients_stat_sum - $request_summ;
					       $this->mainform_model->correct_previous_total($value_u->request_acc_number, $p_total[0]->pamm_tp_id, $summ_to_stopout);

					       $rs = 0 - $request_summ;
					       $this->mainform_model->change_statement($value_u->request_cid,$value_u->request_acc_number,$rs,'U');

			       		       $history_trader  = $this->mainform_model->obtain_trader_history($value_u->request_acc_number);

					       $this->mainform_model->correct_previous_trader($value_u->request_acc_number, $p_total[0]->pamm_tp_id, $history_trader[0]->pamm_clients_stat_sum);
					       $this->mainform_model->correct_previous_invest($value_u->request_acc_number, $p_total[0]->pamm_tp_id, $history_invest[0]->pamm_clients_stat_sum);

					       // MAKE SOURCE PROFITLOS EQUAL ZERO BECAUSE THERE IS NO PROFITOSS BEFORE ROLOVER!

					       $this->mainform_model->zero_profitloss($value_u->request_acc_number, $p_total[0]->pamm_tp_id);

					       // NOT TO CHANGE SOURCE PROFITABLE!
						
					       $summ_to_stopout = $summ_to_stopout + abs($d);

					       // obtain divide not into rolover results

						echo "---------------------\r\n";
						print_r($profit);
						echo "---------------------\r\n";

					       // setting request status
					       $this->mainform_model->change_request_status($value_u->request_id,'3');

					       // unlocking the account
					       $this->acc_lock_unlock($value_u->request_acc_number,1);

					      // writing debt_urgent_out
					      $this->mainform_model->write_debt_urgent_out($value_u->request_acc_number, $request_summ);

					     // operate with corrrection means ONLY CURRENT summ - not ACCUMULATED one! (this is for divide)


						if ($d < 0)
						{
						        // subtract ALL debt - to add CORRECTED one

						        $summ_to_stopout = $summ_to_stopout - abs($d);

							$correction = ($request_summ/($p_total[0]->pamm_tp_total + $debt_inout[0]->debt_inout))*$debt_for_trade[0]->debt_for_trade;
							echo "Urgent balance_before_withdrawal = ".$p_total[0]->pamm_tp_total." debt_inout = ".$debt_inout[0]->debt_inout." debt_for_trade =".$debt_for_trade[0]->debt_for_trade." d_u_o=".$request_summ." debt=".$debt[0]->debt."\r\n";
							echo "Debt on account ".$value->login." corrected in value of ".round($correction,2)."\r\n";
							$this->mainform_model->correct_debt($value->login,abs($correction));

							$debt = $this->mainform_model->obtain_debt($value_u->request_acc_number);
							$d    = $debt[0]->debt;

							// add to stopout CORRECTED debt
							$summ_to_stopout = $summ_to_stopout + abs($d);

							// reset the debt_urgent_out
							$this->mainform_model->write_debt_urgent_out_zero($value_u->request_acc_number);

						}

				      //  add to stopout list
					      $max_dip         = $this->mainform_model->obtain_max_dip($value_u->request_acc_number);
					      $md              = $max_dip[0]->max_dip;
					      echo "Add to stopout - ".$summ_to_stopout." urgent withdrawal\r\n";			    
					      $this->stopout('ADD',$value_u->request_acc_number,$summ_to_stopout,$md,"from urgent trader");

					      // write fsb to indicate in manager's tab immediately		 
					      $this->mainform_model->write_fsb($value_u->request_acc_number,$summ_to_stopout);

				              // drop stopout flag
					      $this->mainform_model->drop_stopout_share_flag($value_u->request_acc_number);

					}

	
				    }	
				   }
				}  // negative debt

			    $out = "exit\r\n";
			    fwrite($fp, $out);
			    @fclose($fp);
			} // divide flag

		} // foreach urgent trader request

		$urgent_i_requests = $this->mainform_model->get_investor_urgent_requests($value->login);

		if (trim($this->stopout_status($value->login,'form urgent')) == "1")
	        {

			foreach ($urgent_i_requests as $value_i)
			{
			   $this->mainform_model->change_request_status($value_i->request_id,'2');
			   $comm_changed = 'Urgent investor withdraw request declined due to the stopout (urgent)';
			   $this->mainform_model->change_request_comment($comm_changed,$value_i->request_id);
			}
		
		 echo "Account ".$value->login." is dipped - from urgent investor\r\n";
		 return true;
		 
	        }


		foreach ($urgent_i_requests as $value_i)
		{

		   $divide = $this->mainform_model->get_divide_flag($value->login);
		   if ($divide[0]->divide == '1')
	           {
			echo "Urgent investor request on account ".$value->login." skipped because of divide\r\n";
	           }
		   else
		   {
		   $request_summ_begin = $value_i->request_summ;
		   $implement = TRUE;
	           $add_to_stopout = TRUE;
			if ($urgent_flag[0]->w_b == 0)
			   {
			   // decline urgent requests
			   $this->mainform_model->change_request_status($value_i->request_id,'2');
			   $comm_changed = 'Urgent withdraw investor request declined due to the offer';
			   $this->mainform_model->change_request_comment($comm_changed,$value_i->request_id);
			   }
			else
			   {
				$fp = fsockopen("80.93.48.133", 10025, $errno, $errstr, 30);
				if (!$fp)
				{
				$fp = $this->reconnect();
				}
				   $out = "11\r\n";
				   fwrite($fp, $out);
				   $out = "11\r\n";
				   fwrite($fp, $out);

				   fgets($fp,128);
				   fgets($fp,128);
				   fgets($fp,128);
				   fgets($fp,128);
				   fgets($fp,128);
				   fgets($fp,128);

			       // obtain previous trade period result and current balance
			       $balance         = $this->obtain_balance($fp, $value_i->request_acc_number);
			       $debt_inout      = $this->mainform_model->obtain_debt_inout($value_i->request_acc_number);
	       		       $history_common  = $this->mainform_model->obtain_common_history($value_i->request_acc_number);
	       		       $history_investor  = $this->mainform_model->obtain_investor_history($value_i->request_acc_number, $value_i->request_cid);
			       $p_total         = $this->mainform_model->get_previous_total($value_i->request_acc_number);
		
				   if (count($p_total) == 0)
					$p_total[0]->pamm_tp_total = 0;

				$earn = $this->mainform_model->obtain_balance_before_divide($value_i->request_acc_number);
					$p_total[0]->pamm_tp_total = $earn[0]->earn;				


			       // obtain debt and inoutdebt flag
			       $debt = $balance - $p_total[0]->pamm_tp_total + $debt_inout[0]->debt_inout; 


                       	       $inoutdebt = $this->mainform_model->obtain_inoutdebt($value_i->request_acc_number);
			        	
			       if ($debt < 0  AND $inoutdebt[0]->inoutdebt == 0)  // withdrawal are impossible
			           {
					   $this->mainform_model->change_request_status($value_i->request_id,'2');
					   $comm_changed = 'Urgent investor withdraw request declined because of owed negative debt';
					   $this->mainform_model->change_request_comment($comm_changed,$value_i->request_id);
        				   }
			           else				   			   //implement urgent record
				   {

				    $number_of_open_orders = $this->open_orders($fp,$value_i->request_acc_number);

				    if (trim($number_of_open_orders) != '0' AND trim($number_of_open_orders) != '999999035')  // postpone it until next launch of urgent engine
				    {
				          echo "Urgent investor withdrawal on ".$value_i->request_acc_number." skipped because of ".$number_of_open_orders." open orders\r\n";

				    }
				    else  // implement requests without postpone
				    {
				       // locking the account
				       $this->acc_lock_unlock($value_i->request_acc_number,0);

				       $profit = $this->divide_not_into_rolover($fp,$value_i->request_acc_number);

					$profit_investor = 0;

				       // last check to current profit before changing deposit
					 foreach ($profit as $value_p)
						{
						 if ($value_p[0] == $value_i->request_cid)
						    $profit_investor = $value_p[1];
						}
				         if ($profit_investor < 0)  // loss
						   $real_balance = $history_investor[0]->pamm_clients_stat_sum + $profit_investor;
					 else
						   $real_balance = $history_investor[0]->pamm_clients_stat_sum;

	
				       // implementing withdrawal
				       echo "Urgent out investor ".$value_i->request_cid."-".$value_i->request_summ.",".$value_i->request_acc_number."\r\n";
					echo " Real balance = ".$real_balance."  profit_investor - ".$profit_investor."\r\n";

						// calculate stopout restriction
						   $balance_from_base = $this->mainform_model->obtain_all_means_for_inv($value_i->request_acc_number,$value_i->request_cid);
						   $max_dip = $this->mainform_model->obtain_max_dip($value_i->request_acc_number);
						   $d_u_o   = $this->mainform_model->obtain_debt_urgent_out_for_restrict($value_i->request_acc_number,$value_i->request_cid,$value_i->request_tp_id);

 						   $restricted_summ = ($d_u_o[0]->request_summ + $balance_from_base[0]->pamm_clients_stat_sum)*(100-$max_dip[0]->max_dip)/100 - $d_u_o[0]->request_summ;							

						   if ($restricted_summ < $value_i->request_summ)
							{
							 	$value_i->request_summ = $restricted_summ;
								echo "value_i->request_summ corrected -".$value_i->request_summ."\r\n";
							}
						   echo " restricted_summ = ".$restricted_summ." b_f_b ".$balance_from_base[0]->pamm_clients_stat_sum." d_u_o ".$d_u_o[0]->request_summ."\r\n";

				      if (round($value_i->request_summ,2) <= 0 || $real_balance < 0 )
						{
						   echo "Decline:  restricted_summ = ".$value_i->request_summ." real_balance =".$real_balance."\r\n"; 
						// decline the request
						   $this->mainform_model->change_request_status($value_i->request_id,'2');
						   $comm_changed = 'Urgent investor withdraw request declined';
						   $this->mainform_model->change_request_comment($comm_changed,$value_i->request_id);
						   $implement = FALSE;			
						   echo "3 case - deny \r\n" ;				
						   $add_to_stopout = FALSE;
						}
				     if ($implement === TRUE)

					{
                                    	if ($request_summ_begin  == $value_i->request_summ)
						{
							// summ is enough
						// calculate commission and rest
						   $penalty = $this->mainform_model->get_penalty($value_i->request_acc_number);
						   $commission = $value_i->request_summ*$penalty[0]->penalty/100;
						   $request_summ = round($value_i->request_summ - $commission,2);

						   echo " comission = ".$commission." request_summ= ".$request_summ."\r\n";


						  if ($value_i->request_wallet != 'USD')
						   $quote  = $this->get_quote($value_i->request_wallet);
						  else
						   $quote = 1;

						   $commission_origin = $commission*$quote;
						   $request_summ_origin    = $request_summ*$quote;

						   $comm = 'urgent wd investor '.$value_i->request_cid;

						// proceed commission
						   $tid = $this->mainform_model->obtain_trader_id($value_i->request_acc_number);
						   $nc  =  0 - $commission;
						   $comm_comm = 'urgent commission investor '.$value_i->request_cid;
						   $this->change_deposit($fp,200,$value_i->request_acc_number,$commission,$comm_comm,'1-4');

					           $this->mainform_model->change_statement($value_i->request_cid,$value_i->request_acc_number,$nc,'I');
						   $this->mainform_model->charge_wallet($tid[0]->tid,round($commission,2),$value_i->request_wallet);

						   $this->mainform_model->change_request_summ($request_summ, $request_summ_origin, $quote, $value_i->request_id);  // decrease request summ in commission amount

					        // writing commission to debt_urgent_out
						   $this->mainform_model->write_debt_urgent_out($value_i->request_acc_number, $commission);

					        // correct preivous result
				       		   $history_investor  = $this->mainform_model->obtain_investor_history($value_i->request_acc_number, $value_i->request_cid);
						   $this->mainform_model->correct_previous_invest($value_i->request_acc_number, $p_total[0]->pamm_tp_id, $history_investor[0]->pamm_clients_stat_sum);

						   $total_with_commission = round($p_total[0]->pamm_tp_total - $commission,2);

					           $this->mainform_model->correct_previous_total($value_i->request_acc_number, $p_total[0]->pamm_tp_id, $total_with_commission);



                                                // generate the commission out request (already implemented one)
						
 						   $data_ri['request_acc_number']  = $value_i->request_acc_number; 
 						   $data_ri['request_date']        = $value_i->request_date; 
						   $data_ri['request_cid'] 	   = $value_i->request_cid;
						   $data_ri['request_comment'] 	   = "Commission for urgent out";
						   $data_ri['request_summ'] 	   = $commission;
						   $data_ri['request_summ_origin'] = $commission_origin;
						   $data_ri['request_quote']       = $quote;
						   $data_ri['request_type']        = '8';                                                                                                                                                                                                                 
						   $data_ri['request_urgent']      = '1';                                                                                                                                                                                                                 
						   $data_ri['request_status']      = '3';
						   $data_ri['request_wallet']      = $value_i->request_wallet;
 						   $data_ri['request_common_id']   = $value_i->request_id; 
 						   $data_ri['request_tp_id']       = $value_i->request_tp_id; 

						  $this->mainform_model->commission_request($data_ri);


                				   $request_summ_origin = $value_i->request_summ*$quote;
						  echo "1 case - Summ > out \r\n" ;
						}
					elseif ($request_summ_begin > $value_i->request_summ )
						{
					  

						// calculate commission and rest
						   $penalty = $this->mainform_model->get_penalty($value_i->request_acc_number);
						   $commission = $value_i->request_summ*$penalty[0]->penalty/100;
						   $request_summ = $value_i->request_summ - $commission;

						   echo " comission = ".$commission." request_summ= ".$request_summ."\r\n";

						  if ($value_i->request_wallet != 'USD')
							   $quote  = $this->get_quote($value_i->request_wallet);
						  else
							   $quote = 1;
						   $commission_origin = $commission*$quote;
						   $request_summ_origin    = $request_summ*$quote;
		
						// proceed commission
						   $tid = $this->mainform_model->obtain_trader_id($value_i->request_acc_number);
						   $nc  =  0 - $commission;

						   $comm_comm = 'urgent commission investor '.$value_i->request_cid;
						   $this->change_deposit($fp,200,$value_i->request_acc_number,$commission,$comm_comm,'1-4');
					           $this->mainform_model->change_statement($value_i->request_cid,$value_i->request_acc_number,$nc,'I');
						   $this->mainform_model->charge_wallet($tid[0]->tid,$commission,$value_i->request_wallet);

					        // writing commission to debt_urgent_out
						   $this->mainform_model->write_debt_urgent_out($value_i->request_acc_number, $commission);

					        // correct preivous result
				       		   $history_investor  = $this->mainform_model->obtain_investor_history($value_i->request_acc_number, $value_i->request_cid);
						   $this->mainform_model->correct_previous_invest($value_i->request_acc_number, $p_total[0]->pamm_tp_id, $history_investor[0]->pamm_clients_stat_sum);

						   $total_with_commission = round($p_total[0]->pamm_tp_total - $commission,2);

					           $this->mainform_model->correct_previous_total($value_i->request_acc_number, $p_total[0]->pamm_tp_id, $total_with_commission);


                                                // generate the commission out request (already implemented one)
						
 						   $data_ri['request_acc_number']  = $value_i->request_acc_number; 
 						   $data_ri['request_date']        = $value_i->request_date; 
						   $data_ri['request_cid'] 	   = $value_i->request_cid;
						   $data_ri['request_comment'] 	   = "Commission for urgent out";
						   $data_ri['request_summ'] 	   = $commission;
						   $data_ri['request_summ_origin'] = $commission_origin;
						   $data_ri['request_quote']       = $quote;
						   $data_ri['request_urgent']        = '1';                                                                                                                                                                                                                 
						   $data_ri['request_type']        = '8';                                                                                                                                                                                                                 
						   $data_ri['request_status']      = '3';
						   $data_ri['request_wallet']      = $value_i->request_wallet;
 						   $data_ri['request_common_id']   = $value_i->request_id; 
 						   $data_ri['request_tp_id']       = $value_i->request_tp_id; 


						  $this->mainform_model->commission_request($data_ri);


						   $comm = 'Urgent investor request of '.$request_summ_begin.' withdraw implemented of '.round($request_summ,2).' amount';
						   $this->mainform_model->change_request($request_summ,$request_summ_origin,$quote,$comm,$value_i->request_id);
						   $print_comm = $request_summ + $commission;

						   $comm = 'urgent wd investor '.$value_i->request_cid.' ($'.$request_summ_begin.'|$'.$print_comm.')';


					 	  if (round($real_balance,2) == 0)       // unjoint
					 	        {

						 	   $this->mainform_model->implement_unactive_request_unjoint($value_i->request_cid,$value_i->request_acc_number);

	                                                // generate the unjoint request (already implemented one)
							
 							   $data_ri['request_acc_number']  = $value_i->request_acc_number; 
							   $data_ri['request_cid'] 	   = $value_i->request_cid;
							   $data_ri['request_comment'] 	   = "Unjoint because of urgent outing all means";
							   $data_ri['request_summ'] 	   = 0;
							   $data_ri['request_summ_origin'] = 0;
							   $data_ri['request_quote']       = '1.00';
							   $data_ri['request_type']        = '11';                                                                                                                                                                                                                 
							   $data_ri['request_status']      = '3';

							  $this->mainform_model->implement_close_investor($data_ri);
							 }

						  echo "2 case - between \r\n" ;
						}
					} // not 3rd case

				       if ($implement)
					{

					       // recalculate the total because of commission
					       // $p_total[0]->pamm_tp_total = $this->mainform_model->get_previous_total($value_i->request_acc_number);

					       // set stopout flag
					       $this->mainform_model->set_stopout_share_flag($value_i->request_acc_number);

					       // delete from stopout
					       echo "Del from stopout - urgent withdrawal\r\n";
					       $this->stopout('DEL',$value_i->request_acc_number,NULL,NULL,"from urgent");		 

					       $this->change_deposit($fp,200,$value_i->request_acc_number,$request_summ,$comm,'1-4');
					       $this->mainform_model->charge_wallet($value_i->request_cid,round($request_summ,2),$value_i->request_wallet);

						$debt = $this->mainform_model->obtain_debt($value_i->request_acc_number);
						$d = $debt[0]->debt;

						$debt_for_trade = $this->mainform_model->obtain_debt_for_trade($value_i->request_acc_number);

					       $summ_to_stopout = $history_common[0]->pamm_clients_stat_sum - $request_summ - $commission;
					       $this->mainform_model->correct_previous_total($value_i->request_acc_number, $p_total[0]->pamm_tp_id, $summ_to_stopout);

					       $rs = 0 - $request_summ;
					       $this->mainform_model->change_statement($value_i->request_cid,$value_i->request_acc_number,$rs,'I');

			       		       $history_investor  = $this->mainform_model->obtain_investor_history($value_i->request_acc_number, $value_i->request_cid);

					       $this->mainform_model->correct_previous_invest($value_i->request_acc_number, $p_total[0]->pamm_tp_id, $history_investor[0]->pamm_clients_stat_sum);

					       // MAKE SOURCE PROFITLOS EQUAL ZERO BECAUSE THERE IS NO PROFITOSS BEFORE ROLOVER!

					       $this->mainform_model->zero_profitloss($value_i->request_acc_number, $p_total[0]->pamm_tp_id);

					       // NOT TO CHANGE SOURCE PROFITABLE!
						
					       $summ_to_stopout = $summ_to_stopout + abs($d);

					       // obtain divide not into rolover results

						echo "---------------------\r\n";
						print_r($profit);
						echo "---------------------\r\n";

					       // setting request status
					       $this->mainform_model->change_request_status($value_i->request_id,'3');

					       // unlocking the account
					       $this->acc_lock_unlock($value_i->request_acc_number,1);

					      // writing debt_urgent_out
					      $this->mainform_model->write_debt_urgent_out($value_i->request_acc_number, $request_summ);

					     // operate with corrrection means ONLY CURRENT summ - not ACCUMULATED one! (this is for divide)


						if ($d < 0)
						{
						        // subtract ALL debt - to add CORRECTED one

						        $summ_to_stopout = $summ_to_stopout - abs($d);

							$correction = (($request_summ+$commission)/($p_total[0]->pamm_tp_total + $debt_inout[0]->debt_inout))*$debt_for_trade[0]->debt_for_trade;
							$temp = $request_summ + $commission;
							echo "Urgent balance_before_withdrawal = ".$p_total[0]->pamm_tp_total." debt_inout = ".$debt_inout[0]->debt_inout." debt_for_trade =".$debt_for_trade[0]->debt_for_trade." d_u_o=".$temp." debt=".$debt[0]->debt."\r\n";
							echo "Debt on account ".$value->login." corrected in value of ".round($correction,2)."\r\n";
							$this->mainform_model->correct_debt($value->login,abs($correction));

							$debt = $this->mainform_model->obtain_debt($value_i->request_acc_number);
							$d    = $debt[0]->debt;

							// add to stopout CORRECTED debt
							$summ_to_stopout = $summ_to_stopout + abs($d);

							// reset the debt_urgent_out
							$this->mainform_model->write_debt_urgent_out_zero($value_i->request_acc_number);

						}

				      //  add to stopout list
					      $max_dip         = $this->mainform_model->obtain_max_dip($value_i->request_acc_number);
					      $md              = $max_dip[0]->max_dip;
					      echo "Add to stopout - ".$summ_to_stopout." urgent withdrawal\r\n";			    
					      $this->stopout('ADD',$value_i->request_acc_number,$summ_to_stopout,$md,"from urgent inv");

					      // write fsb to indicate in manager's tab immediately		 
					      $this->mainform_model->write_fsb($value_i->request_acc_number,$summ_to_stopout);

				              // drop stopout flag
					      $this->mainform_model->drop_stopout_share_flag($value_i->request_acc_number);

					}
				      	
	
				    }	
				   }
				}  // negative debt

			    $out = "exit\r\n";
			    fwrite($fp, $out);
		}  // divise flag
			   
		}  // foreach urgent investor requests

	   }
		
    }

}