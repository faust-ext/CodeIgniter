<?php
class Engine extends CI_Controller {

	function __construct()
    {
		parent::__construct(); 
		$this->load->model('mainform_model');
		
    }

	function reconnect()
       {       
	error_log("\r\nReconnect start - ### \r\n\r\n", 3, "test_engine.log");
	sleep(5);
	$fp = fsockopen("80.93.48.133", 10023, $errno, $errstr, 30);
	if (!$fp)
		{
		 sleep(5);
		 $fp = fsockopen("80.93.48.133", 10023, $errno, $errstr, 30);
		 if (!$fp)
			{
			 sleep(5);
			 $fp = fsockopen("80.93.48.133", 10023, $errno, $errstr, 30);
			 if (!$fp)
				{
				error_log("\r\nReconnect failed - ### \r\n\r\n", 3, "test_engine.log");
				die();
				}
			}
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


	error_log("\r\nReconnect finished - ### \r\n\r\n", 3, "test_engine.log");
	return $fp;

	}

	private function obtain_balance($fp,$acc_number)
	{

	    if (!$fp)
		{
			$fp = reconnect();
		}

	    $out = '10 '.$acc_number;
	    fwrite($fp, $out."\r\n");

	    $buffer = fgets($fp, 128);   // throw output which repeats the input (acc_number)
	    $buffer = fgets($fp, 128);   // obtain the balance
		if (substr($buffer,0,5) == '99999')
			{
			    error_log("\r\nObtain balance ".$acc_number." code=".$buffer."\r\n\r\n", 3, "test_engine.log");
			    fwrite($fp, $out."\r\n");

		 	    $buffer = fgets($fp, 128);
			    $buffer = fgets($fp, 128);

			if (substr($buffer,0,5) == '99999')
				{
			    	    error_log("\r\nRetry obtain balance ".$acc_number." code=".$buffer."\r\n\r\n", 3, "test_engine.log");
                        	    fwrite($fp, $out."\r\n");

			 	    $buffer = fgets($fp, 128);
				    $buffer = fgets($fp, 128);
				if (substr($buffer,0,5) == '99999')
					{
					error_log("\r\nError obtain balance - ".$acc_number." code=".$buffer."\r\n\r\n", 3, "test_engine.log");
					die();
				        }
				}
			}

            return $buffer;
	}

    private function change_deposit($fp,$key,$acc_number,$summ,$comm)
    {

	    if (!$fp)
		{
			$fp = reconnect();
		}
	    $out = $key.' '.$acc_number.' '.$summ.' '.$comm;
	    fwrite($fp, $out."\r\n");

 	    $buffer = fgets($fp, 128);
	    $buffer = fgets($fp, 128);

		if (substr($buffer,0,5) == '99999')
			{
			    error_log("\r\nRetry change deposit - ".$acc_number." amount=".$summ." code=".$buffer."\r\n\r\n", 3, "test_engine.log");
			    fwrite($fp, $out."\r\n");

		 	    $buffer = fgets($fp, 128);
			    $buffer = fgets($fp, 128);

			if (substr($buffer,0,5) == '99999')
				{
			            error_log("\r\nRe-retry change deposit - ".$acc_number." amount=".$summ." code=".$buffer."\r\n\r\n", 3, "test_engine.log");
                        	    fwrite($fp, $out."\r\n");

			 	    $buffer = fgets($fp, 128);
				    $buffer = fgets($fp, 128);
				if (substr($buffer,0,5) == '99999')
					{
					error_log("\r\nError change deposit - ".$acc_number." amount=".$summ." code=".$buffer."\r\n\r\n", 3, "test_engine.log");
					die();
				        }
				}
			}
    }


    private function get_quote($symbol)
    {
		switch($symbol):
			case('GBP'):
				   $q = $this->mainform_model->get_moment_quote_gbp($symbol);
				   $quote  = 1/($q[0]->price);
			break;
			case('EUR'):
				   $q = $this->mainform_model->get_moment_quote_gbp($symbol);
				   $quote  = 1/($q[0]->price);
			break;
			case('RUR'):
				   $q = $this->mainform_model->get_moment_quote('CBR');
				   $quote  = $q[0]->price;
			break;
			default:
				   $q = $this->mainform_model->get_moment_quote($symbol);
				   $quote  = $q[0]->price;
			endswitch;
       return $quote;
    }

	private function obtain_history($acc_number)
	{	
		   $time = mktime(23,59,59,date('n')-1,date('j'),date('Y')); // trial trade period - 5 days 
		   return $this->mainform_model->oh($acc_number,$time);
	}
	private function obtain_history_investor($acc_number,$tid)
	{	
		   $time = mktime(23,59,59,date('n')-1,date('j'),date('Y')); // trial trade period - 5 days 
		   return $this->mainform_model->ohi($acc_number,$tid,$time);
	}

	private function obtain_history_without_trader($acc_number)
	{	
		   $time = mktime(23,59,59,date('n')-1,date('j'),date('Y')); // trial trade period - 5 days 
		   return $this->mainform_model->ohwt($acc_number,$time);
	}
	private function obtain_history_only_trader($acc_number)
	{	
		   $time = mktime(23,59,59,date('n')-1,date('j'),date('Y')); // trial trade period - 5 days 
		   return $this->mainform_model->ohot($acc_number,$time);
	}

	private function obtain_previous_history($acc_number)
	{	   
		   $oph1 = $this->mainform_model->oph1($acc_number);
		   $oph2 = $this->mainform_model->oph2($acc_number);
		   if (count($oph2) > 0)
			   $ret = $oph1[0]->pamm_tp_total - $oph2[0]->pamm_tp_total;
		   else
			   $ret = $oph1[0]->pamm_tp_total;
		   return $ret;
	}
	private function obtain_detailed_history($acc_number)
	{	
		   $time = mktime(23,59,59,date('n')-1,date('j'),date('Y')); // trial trade period - 5 days 
		   return $this->mainform_model->odh($acc_number,$time);
	}

	private function tp_results($result_array,$acc_number)
	{
	        $prev_date = $this->mainform_model->tp_prev_res_date($acc_number);
		if (is_null($prev_date[0]->pamm_clients_stat_date))
			$prev_date[0]->pamm_clients_stat_date = mktime(23,59,59,date('n'),date('j'),date('Y')-1);
		$u_total  = $this->mainform_model->get_total($acc_number,$prev_date[0]->pamm_clients_stat_date,'U');
		$i_total  = $this->mainform_model->get_total($acc_number,$prev_date[0]->pamm_clients_stat_date,'I');

		$total    = round($result_array['pamm_tp_total'],2);

		$this->mainform_model->tp_results_insert($acc_number,$total,round($result_array['pamm_tp_profitloss'],2),round($u_total[0]->pamm_clients_stat_sum,2),round($i_total[0]->pamm_clients_stat_sum),$result_array['pamm_tp_profitable']);
        }	

	public function index()
	{
	// telnet connection initialization

	$fp = fsockopen("80.93.48.133", 10023, $errno, $errstr, 30);
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
		
	// implementation of join, rejoin and 2 corrective requests (type 1,2,3)
	$requests = (array) $this->mainform_model->get_in12_requests();
		foreach ($requests as $value)
		{

		$request_summ = $value->request_summ;

	                   $this->change_deposit($fp,$action,$value->request_acc_number,$request_summ,$comm);

				 if (substr($value->request_comment,0,1) == "U")
				 {
				  $this->mainform_model->charge_wallet($tid,$request_summ_origin,$value->request_wallet);
				 }

			$status = '3';	

		  $this->mainform_model->change_request_status($value->request_id,$status);
 		  // log request_in history	

		  $history['id']      = '';
		  $history['login']   = $value->request_acc_number;
		  $history['ticket']  = substr($ret,1,strlen($ret)-1); // ticket id or error code (99**** or zero)
		  $history['comment'] = $value->request_comment;
		  $history['sum']     = $value->request_summ;
		  $history['tid']     = $tid;

	  	  $this->mainform_model->history_write($history);

		}

	// divide profit
	        $account_array      = $this->mainform_model->get_divide_accounts();

		$result_array = Array();
		$detailed_history1 = Array();
	        foreach($account_array as $value)
			{
			  $ret_balance      = $this->obtain_balance($fp,$value->login);
			    
			  $result_array['pamm_tp_account']    = $value->login;
                          $result_array['pamm_tp_total']      = $ret_balance;
			  $p_total  = $this->mainform_model->get_previous_total($value->login);
			   if (count($p_total) == 0)
				$p_total[0]->pamm_tp_total = 0;

  			  $debt_old = $this->mainform_model->obtain_debt($value->login);
			  $debt = $result_array['pamm_tp_total'] - $p_total[0]->pamm_tp_total;
				$this->mainform_model->write_debt($value->login,$debt);
  			  $debt = $this->mainform_model->obtain_debt($value->login);
				if ($debt[0]->debt < 0)
			          $prev_tp_result = $debt_old[0]->debt;
				else
				  {
				        $prev_tp_result = $debt_old[0]->debt;
					$debt_negative = 0 - $debt[0]->debt;
					$this->mainform_model->write_debt($value->login,$debt_negative);
				  }

			  $result_array['pamm_tp_total_prev'] = $this->mainform_model->obtain_start_balance_prev($value->login);
			  $result_array['pamm_tp_start']      = $this->mainform_model->obtain_start_balance($value->login);
			  $common_history		      = (array) $this->obtain_history($value->login);		
			  $investor_history		      = (array) $this->obtain_history_without_trader($value->login);		// for investor's divide trader's money are not added
			  $trader_history		      = (array) $this->obtain_history_only_trader($value->login);		// for equal divide without trader's percent

//			  print($value->login."\r\n");
			  $result_array['pamm_tp_profitable'] = round(($result_array['pamm_tp_total'] - $investor_history[0]->pamm_clients_stat_sum - $trader_history[0]->pamm_clients_stat_sum)/($investor_history[0]->pamm_clients_stat_sum + $trader_history[0]->pamm_clients_stat_sum),4);	

			  $trader_id_for_divide 	    = $this->mainform_model->obtain_trader_id($value->login);			// trader id for statement records

                          $earn = $result_array['pamm_tp_total'] - $common_history[0]->pamm_clients_stat_sum;				
			  $earn =round($earn,2);


			  $result_array['pamm_tp_profitloss'] = $earn + $result_array['pamm_tp_start'][0]->pamm_tp_profitloss;

			  $this->tp_results($result_array,$value->login);  									 // write results to the pamm_tp_results table

			  if ($earn >0)      // profit after trade period
			  {
			      $detailed_history = $this->obtain_detailed_history($value->login);
			      if(count($detailed_history) == 0)  // no investors
			      {
				  $detailed_history_became_zero =  $this->mainform_model->obtain_detailed_history_became_zero($value->login);			
	   			if ($prev_tp_result >= 0 )   // no loss in previous trade period
				{
						if (count($detailed_history_became_zero) > 0)
							$comm_nibz = "rolover_profit_divide_bz_1";
						else
							$comm_nibz = "rolover_profit_divide_ni_1";	
					  $this->change_deposit($fp,200,$value->login,round($earn,2),$comm_nibz);         			          // all profit is written-off
					  $this->change_deposit($fp,100,$value->login,round($earn,2),$comm_nibz);                  			  // trader profit is returned
			  		  $this->mainform_model->change_statement($trader_id_for_divide[0]->tid,$value->login,round($earn,2),"PLU");      // change the trader statement
			  	}
				elseif ($prev_tp_result <0 and abs($prev_tp_result) >= abs($earn))  // loss in previous trade period and earn is less than loss
				{
						if (count($detailed_history_became_zero) > 0)
							$comm_nibz = "rolover_profit_divide_bz_3";
						else
							$comm_nibz = "rolover_profit_divide_ni_3";	
					  $this->change_deposit($fp,200,$value->login,round($earn,2),$comm_nibz);                  			  // all profit is written-off
					  $this->change_deposit($fp,100,$value->login,round($earn,2),$comm_nibz); 	                   		  // trader profit is returned
			  		  $this->mainform_model->change_statement($trader_id_for_divide[0]->tid,$value->login,round($earn,2),"PLU");      // change the trader statement

				}
				elseif ($prev_tp_result <0 and abs($prev_tp_result) < abs($earn))  //
				{
						if (count($detailed_history_became_zero) > 0)
							$comm_nibz = "rolover_profit_divide_bz_4";
						else
							$comm_nibz = "rolover_profit_divide_ni_4";	
					  $this->change_deposit($fp,200,$value->login,round($earn,2),$comm_nibz);                 			  // all profit is written-off
					  $this->change_deposit($fp,100,$value->login,round($earn,2),$comm_nibz);                  			  // trader profit is returned
			  		  $this->mainform_model->change_statement($trader_id_for_divide[0]->tid,$value->login,round($earn,2),"PLU");      // change the trader statement

				}
			      }
			      else  // there are investors
			      {	
	   			if ($prev_tp_result >= 0 )   // no loss in previous trade period
				{

					  $this->change_deposit($fp,200,$value->login,round($earn,2),"rolover_profit_divide_1");  	                // all profit is written-off
					  $percentage = $this->mainform_model->get_percentage($value->login);	                         		// obtain trader percent
					  $u_divide = round((($percentage[0]->distr_upr*$earn)/100),2);                              			// trader profit calculation
					  $this->change_deposit($fp,100,$value->login,$u_divide,"trader_profit_divide");                 	 	// trader profit is returned
			  		  $this->mainform_model->change_statement($trader_id_for_divide[0]->tid,$value->login,$u_divide,"PLU");         // change the trader statement
					  $i_divide = $earn - $u_divide;
			  
					  foreach ($detailed_history as $value1)
						{
						if ($value1->pamm_clients_stat_sum > 0) // zero investors are not taken part into divide
						 {
						  $comm = "investor_".$value1->pamm_clients_stat_cid."_profit_divide";
						  $i_divide_by_account = round($i_divide*($value1->pamm_clients_stat_sum)/($investor_history[0]->pamm_clients_stat_sum),2);                     // profit per account
						  $this->change_deposit($fp,100, $value->login, $i_divide_by_account, $comm);  						         		// write profit to mt4	
 				  		  $this->mainform_model->change_statement($value1->pamm_clients_stat_cid,$value->login,$i_divide_by_account,"PLI");        			// change the investor statement
//						  $this->mainform_model->change_wallet($value1->pamm_clients_stat_cid,$i_divide_by_account);  			 				// add profit to wallet 
                                                  $detailed_history1[]['tid'] = $value1->pamm_clients_stat_cid;
                                                  $detailed_history1[]['sum'] = $i_divide_by_account;						 
						 }
						}

				}
				elseif ($prev_tp_result <0 and abs($prev_tp_result) >= abs($earn))  // loss in previous trade period and earn is less than loss
				{
                                          $this->change_deposit($fp,200,$value->login,round($earn,2),"rolover_bg_divide_3");   									// all profit is written-off                             
					  $u_divide = round(($trader_history[0]->pamm_clients_stat_sum)/($common_history[0]->pamm_clients_stat_sum)*$earn,2);    				// trader profit write-off
					  $this->change_deposit($fp,100,$value->login,$u_divide,"trader_loss_bg_earn");   									// trader profit is returned
			  		  $this->mainform_model->change_statement($trader_id_for_divide[0]->tid,$value->login,$u_divide,"PLU");     					        // change the trader statement
			  
					  $detailed_history = $this->obtain_detailed_history($value->login);
					  foreach ($detailed_history as $value1)
						{
						if ($value1->pamm_clients_stat_sum > 0) // zero investors are not taken part into divide
						 {
						  $comm = "investor_".$value1->pamm_clients_stat_cid."_loss_bg_earn";
						  $i_divide_by_account = round($earn*($value1->pamm_clients_stat_sum)/($common_history[0]->pamm_clients_stat_sum),2);    		// loss per account
						  $this->change_deposit($fp,100, $value->login, $i_divide_by_account, $comm);    			       				// write off loss to mt4	
 				  		  $this->mainform_model->change_statement($value1->pamm_clients_stat_cid,$value->login,$i_divide_by_account,"PLI");        		// change the investor statement
//						  $this->mainform_model->change_wallet($value1->pamm_clients_stat_cid,$i_divide_by_account);  						// subtract loss from wallet 
                                                  $detailed_history1[]['tid'] = $value1->pamm_clients_stat_cid;
                                                  $detailed_history1[]['sum'] = $i_divide_by_account;						 
						 }
						}

		
				}
				elseif ($prev_tp_result <0 and abs($prev_tp_result) < abs($earn))   											// loss in previous trade period and earn is bigger than loss
				{
                                          $this->change_deposit($fp,200,$value->login,round($prev_tp_result,2),"rolover_less_loss_divide_4");   					// loss is written-off
					  $u_divide = round(($trader_history[0]->pamm_clients_stat_sum)/($common_history[0]->pamm_clients_stat_sum)*$prev_tp_result,2);  		// trader profit write-off
                                          $this->change_deposit($fp,100,$value->login,$u_divide,"rolover_less_loss_trader");   								// trader loss is written-off
					  $u_divide = 0 - $u_divide;
			  		  $this->mainform_model->change_statement($trader_id_for_divide[0]->tid,$value->login,$u_divide,"PLU");      									// change the trader statement
					  $detailed_history = $this->obtain_detailed_history($value->login);
					  foreach ($detailed_history as $value1)
						{
						if ($value1->pamm_clients_stat_sum > 0) // zero investors are not taken part into divide
						 {
						  $comm = "investor_".$value1->pamm_clients_stat_cid."_less_loss";
						  $i_divide_by_account = round($prev_tp_result*($value1->pamm_clients_stat_sum)/($common_history[0]->pamm_clients_stat_sum),2);		 // loss per account
						  $this->change_deposit($fp,100, $value->login, $i_divide_by_account, $comm);           						 // write off loss to mt4	
						  $i_divide_by_account_neg = 0 - $i_divide_by_account;                                                                                  
				 		  $this->mainform_model->change_statement($value1->pamm_clients_stat_cid,$value->login,$i_divide_by_account_neg,"PLI");        		 // change the investor statement
//						  $this->mainform_model->change_wallet($value1->pamm_clients_stat_cid,$i_divide_by_account);						 // subtract loss from wallet 
                                                  $detailed_history1[]['tid'] = $value1->pamm_clients_stat_cid;
                                                  $detailed_history1[]['sum'] = $i_divide_by_account;						 
                                                 }
						}
				      // for second part of divide we have to recalculate the totals

					  $common_history		      = (array) $this->obtain_history($value->login);		
					  $investor_history		      = (array) $this->obtain_history_without_trader($value->login);		// for investor's divide trader's money are not added
					  $trader_history		      = (array) $this->obtain_history_only_trader($value->login);		// for equal divide without trader's percent

				          $rest_divide = $earn + $prev_tp_result; 
                                          $this->change_deposit($fp,200,$value->login,round($rest_divide,0),"rolover_less_profit_divide_4");   		// profit is written-off

					  $percentage = $this->mainform_model->get_percentage($value->login);	 					// obtain trader percent
					  $u_divide = round(($percentage[0]->distr_upr)*$rest_divide,2);  	      					// trader profit calculation
					  $this->change_deposit($fp,100,$value->login,round($u_divide,0),"trader_less_profit");   			// trader profit is returned

/////////////////////////////////////////////////////////////////
//					  $u_divide = $u_divide/100;
/////////////////////////////////////////////////////////////////////////////////////////

			  		  $this->mainform_model->change_statement($trader_id_for_divide[0]->tid,$value->login,$u_divide,"PLU");  	// change the trader statement
					  $i_divide = $rest_divide - $u_divide;
			  
					  $detailed_history = $this->obtain_detailed_history($value->login);
					  foreach ($detailed_history as $value1)
						{
						if ($value1->pamm_clients_stat_sum > 0) // zero investors are not taken part into divide
						 {
						  $comm = "investor_".$value1->pamm_clients_stat_cid."_less_profit";
						  $i_divide_by_account = round($i_divide*($value1->pamm_clients_stat_sum)/($investor_history[0]->pamm_clients_stat_sum),2);             // profit per account
						  $this->change_deposit($fp,100, $value->login, $i_divide_by_account, $comm);           						// write profit to mt4	
 				  		  $this->mainform_model->change_statement($value1->pamm_clients_stat_cid,$value->login,$i_divide_by_account,"PLI");       		// change the investor statement
//						  $this->mainform_model->change_wallet($value1->pamm_clients_stat_cid,$i_divide_by_account);				  		// add profit to wallet 
                                                  $detailed_history1[]['tid'] = $value1->pamm_clients_stat_cid;
                                                  $detailed_history1[]['sum'] = $i_divide_by_account;	
					          $detailed_history1[]['pamm_clients_stat_sum'] = $value1->pamm_clients_stat_sum;
                                                  $detailed_history1[]['inv_history'] = $investor_history[0]->pamm_clients_stat_sum;
                                                  $detailed_history1[]['com_history'] = $common_history[0]->pamm_clients_stat_sum;
						 }
						}
				}
			     } // there are investors
			  }
                          

			  elseif ($earn < 0)    // loss after trade period
			  {
			  $detailed_history = $this->obtain_detailed_history($value->login);
				  if (count($detailed_history) == 0)
				  {
		                          $this->change_deposit($fp,100,$value->login,abs(round($earn,2)),"rolover_loss_divide_ni");   						// loss is written-off
		                          $this->change_deposit($fp,200,$value->login,abs(round($earn,2)),"rolover_loss_trader_ni");   						// whole trader profit is written-off
					  $this->mainform_model->change_statement($trader_id_for_divide[0]->tid,$value->login,round($earn,2),"PLU");   				// change the trader statement
					  $u_divide=0;
				  }
				  else
				  {
		                          $this->change_deposit($fp,100,$value->login,abs(round($earn,2)),"rolover_less_loss_divide_case2"); 					// loss is written-off
					  $u_divide = round(($trader_history[0]->pamm_clients_stat_sum)/($common_history[0]->pamm_clients_stat_sum)*$earn,2);		  	// trader profit write-off
		                          $this->change_deposit($fp,200,$value->login,$u_divide,"rolover_less_loss_trader");   							// trader loss is written-off
					  $this->mainform_model->change_statement($trader_id_for_divide[0]->tid,$value->login,$u_divide,"PLU");      									// change the trader statement

					  foreach ($detailed_history as $value1)
						{
						if ($value1->pamm_clients_stat_sum > 0) // zero investors are not taken part into divide
						 {
						  $comm = "investor_".$value1->pamm_clients_stat_cid."_less_loss";
						  $i_divide_by_account = round($earn*($value1->pamm_clients_stat_sum)/($common_history[0]->pamm_clients_stat_sum),2);		 	// loss per account
						  $this->change_deposit($fp,200, $value->login, $i_divide_by_account, $comm);           						// write off loss to mt4	
		 				  $this->mainform_model->change_statement($value1->pamm_clients_stat_cid,$value->login,$i_divide_by_account,"PLI");        		// change the investor statement
//						  $this->mainform_model->change_wallet($value1->pamm_clients_stat_cid,$i_divide_by_account);						 // subtract loss from wallet 
	                	                  $detailed_history1[]['tid'] = $value1->pamm_clients_stat_cid;
		                                  $detailed_history1[]['sum'] = $i_divide_by_account;						 
						 }
						}
				  }	

			  }
			  elseif ($earn == 0)  // balance is intact after trade period or there were no operations
			  {

			  $this->change_deposit($fp,200,$value->login,0,"trader_divide_zero_case5");  							 								// trader divide zero 
	  		  $this->mainform_model->change_statement($trader_id_for_divide[0]->tid,$value->login,0,"PLU");  					 								// change the trader statement
			  $detailed_history = $this->obtain_detailed_history($value->login);
			  foreach ($detailed_history as $value1)
				{
				if ($value1->pamm_clients_stat_sum > 0) // zero investors are not taken part into divide
				 {
				  $comm = "investor_".$value1->pamm_clients_stat_cid."_divide_zero";
				  $this->change_deposit($fp,200, $value->login, 0, $comm);        				                        				 // write zero divide to mt4
		  		  $this->mainform_model->change_statement($value1->pamm_clients_stat_cid,$value->login,0,"PLI");						 	 // change the investor statement	
			//	  $this->mainform_model->change_wallet($value1->pamm_clients_stat_cid,round($i_divide*($value1->pamm_clients_stat_sum)/($common_history[0]->pamm_clients_stat_sum),2));  // subtract loss from wallet 

                                                  $detailed_history1[]['tid'] = $value1->pamm_clients_stat_cid;
                                                  $detailed_history1[]['sum'] = 0;
                                 }
				}

			  }
       
			}  //foreach

	// implement queries exept having 3 - 12 statuses

		$requests_other = (array) $this->mainform_model->get_other_requests();
		foreach ($requests_other as $value)
		{

		  $request_summ = $value->request_summ;
		  $neg = FALSE;
		  $implement = TRUE;
		if (substr($value->request_comment,0,1) == "J" AND $value->request_type == '3')  // join to the account
		     {
	  		  $debt = $this->mainform_model->obtain_debt($value->request_acc_number);
				if ($debt[0]->debt < 0)    // only positive profitloss allows to join to the account
				 {
					// decline the request
					  $implement = FALSE;
					  $status = '2';
					  $ret = "DDeclined";
					  $tid = $value->request_cid;
					  $comm_changed = 'Join request declined because of '.$debt[0]->debt.' debt';
					  $this->mainform_model->change_request_comment($comm_changed,$value->request_id);
					  $this->mainform_model->decline_join($tid,$value->request_acc_number);
				 }
			else
				{

					  $comm="investor_join_".$value->request_cid;
					  $status = "I";
					  $tid = $value->request_cid;
					  $action = 100;
					  $neg = FALSE;
					  $this->mainform_model->implement_unactive_request_join($tid,$value->request_acc_number);

				}
		     }
		  elseif (substr($value->request_comment,0,1) == "R" AND $value->request_type == '3')  // rejoin to the account
		     {
	  		  $debt = $this->mainform_model->obtain_debt($value->request_acc_number);
				if ($debt[0]->debt < 0)    // only positive profitloss allows to join to the account
				 {
					  $implement = FALSE;
					  $status = '2';
					  $ret = "DDeclined";
					  $tid = $value->request_cid;
					  $comm_changed = 'Rejoin request declined because of '.$debt[0]->debt.' debt';
					  $this->mainform_model->change_request_comment($comm_changed,$value->request_id);
					  $this->mainform_model->decline_join($tid,$value->request_acc_number);
				 }
			else
				{
				   if ($single == 0)  // join request is single
					{
                                       		$comm="investor_rejoin_".$value->request_cid;
						$status = "I";
						$tid = $value->request_cid;
						$action = 100;
						$neg = FALSE;
						$this->mainform_model->implement_unactive_request_rejoin($tid,$value->request_acc_number);
						$single = 1;
					}
			 	   else
					{
					// return money to the wallet and decline the request

					  $this->mainform_model->return_join_paid($value->request_wallet,$value->request_cid,$value->request_summ_origin);

					  $implement = FALSE;
					  $status = '2';
					  $ret = "DDeclined";
					  $tid = $value->request_cid;
					  $comm_changed = 'Rejoin request declined - already joint';
					  $this->mainform_model->change_request_comment($comm_changed,$value->request_id);
					  $this->mainform_model->decline_join($tid,$value->request_acc_number);

					}
				}
		     }

		  if ($value->request_type == '5')
		     {
			$comm="trader_charge";
			$status = "U";
			$tid = $value->request_cid;
			$action = 100;
			$neg = FALSE;
		     }
		  elseif ($value->request_type == '6')
		     {
			$comm="investor_charge_".$value->request_cid;
			$status = "I";
			$tid = $value->request_cid;
			$action = 100;
			$neg = FALSE;
		     }
		  elseif ($value->request_type == '7')
		     {
			$comm="trader_withdraw";
			$status = "U";
			$tid = $value->request_cid;
			$action = 200;
			$neg = TRUE;
		  	$trader_history = $this->obtain_history_only_trader($value->request_acc_number);	
			$th = $trader_history[0]->pamm_clients_stat_sum;
			if ($th -300 > $value->request_summ)
				{
				// summ is enough
					  if ($value->request_wallet != 'USD')
						   $quote  = $this->get_quote($value->request_wallet);
					  else
						   $quote = 1;

					   $request_summ_origin = $request_summ*$quote;
				}
			elseif ($th -300 < $value->request_summ AND $th > 300)
				{
				// rest of the sum could be withdrawn
					   $request_summ = $th - 300;
					  if ($value->request_wallet != 'USD')
						   $quote  = $this->get_quote($value->request_wallet);
					  else
						   $quote = 1;
					   $request_summ_origin = $request_summ*$quote;
		
					   $comm = 'Request of '.$value->request_summ.' withdraw implemented of '.$request_summ.' amount';
					   $this->mainform_model->change_request($request_summ,$request_summ_origin,$quote,$comm,$value->request_id);
				}
			elseif ($th <= 300)
				{
				// decline the request
	
					  $implement = FALSE;
					  $status = '2';
					  $ret = "DDeclined";
				}
		     }
		  elseif ($value->request_type == '8')		     {
			$comm="investor_withdraw_".$value->request_cid;
			$status = "I";
			$tid = $value->request_cid;
			$action = 200;
			$neg = TRUE;
		        $investor_history = $this->obtain_history_investor($value->request_acc_number,$tid);	
			$ih = $investor_history[0]->pamm_clients_stat_sum;
			// echo("IH=".$ih."\n");
			if ($ih > $value->request_summ)
				{
				// summ is enough
					  if ($value->request_wallet != 'USD')
						   $quote  = $this->get_quote($value->request_wallet);
					  else
						   $quote = 1;
					   $request_summ_origin = $request_summ*$quote;
				}
			elseif ($ih < $value->request_summ AND $ih > 0)
				{
				// rest of the sum could be withdrawn
				   $request_summ = $ih;
					  if ($value->request_wallet != 'USD')
						   $quote  = $this->get_quote($value->request_wallet);
					  else
						   $quote = 1;
  				   $request_summ_origin = $request_summ*$quote;
				   $comm_changed = 'Request of '.$value->request_summ.' withdraw implemented of '.$request_summ.' USD';
				   $this->mainform_model->change_request($request_summ,$request_summ_origin,$quote,$comm_changed,$value->request_id);
				}
			elseif ($ih <= 0)
				{
				// decline the request
				  $implement = FALSE;
				  $status = '2';
				  $ret = "DDeclined";
				}
			
		     }
		  elseif ($value->request_type == '11')  // unjoint to the account
		     {
			$comm="investor_unjoint_".$value->request_cid;
			$avaliable_summ = $this->mainform_model->ohi($value->request_acc_number,$value->request_cid,'');     // calculating investor's rest on the account
			$request_summ = $avaliable_summ[0]->pamm_clients_stat_sum;                            

			$comm_changed = 'Request of unjoint implemented of '.$request_summ.' USD';
  		        
			// calculating the actual quote
					  if ($value->request_wallet != 'USD')
						   $quote  = $this->get_quote($value->request_wallet);
					  else
						   $quote = 1;

				   $request_summ_origin =  $request_summ*$quote;


		        $this->mainform_model->change_request($request_summ,$request_summ_origin,$quote,$comm_changed,$value->request_id);   // correct the summ into request
			$status = "I";
			$tid = $value->request_cid;
			$action = 200;
			$neg = TRUE;
			$this->mainform_model->implement_unactive_request_unjoint($tid,$value->request_acc_number);
		     }
		  elseif ($value->request_type == '12')  									 // close account
		     {
			// get trader anount of money			
			   $trader_history = $this->obtain_history_only_trader($value->request_acc_number);
			
			// write off trader money in MT4
                           $this->change_deposit($fp,200, $value->request_acc_number, $trader_history[0]->pamm_clients_stat_sum, 'trader_close_account');  
			   $trader_neg = 0 - ($trader_history[0]->pamm_clients_stat_sum);                                                        // money is WITHDRAWN - negative value
	  		   $this->mainform_model->change_statement($value->request_cid,$value->request_acc_number,$trader_neg,"U");    		 // change the investor statement

					  if ($value->request_wallet != 'USD')
						   $quote  = $this->get_quote($value->request_wallet);
					  else
						   $quote = 1;

			   $request_summ_origin =  $trader_history[0]->pamm_clients_stat_sum*$quote;

			   $this->mainform_model->charge_wallet($value->request_cid,$request_summ_origin,$value->request_wallet);		 // charge investor's wallet 
			   $comm = 'Request of closing implemented of '.$trader_history[0]->pamm_clients_stat_sum.' USD';
			   $this->mainform_model->change_request($trader_history[0]->pamm_clients_stat_sum,$request_summ_origin,$quote,$comm,$value->request_id);


 			// get investor's amount of money and write-off
			   $detailed_history = $this->obtain_detailed_history($value->request_acc_number);

					  foreach ($detailed_history as $value1)
						{
						  $comm = "investor_".$value1->pamm_clients_stat_cid."_close_account";
						  $this->change_deposit($fp,200, $value->request_acc_number, $value1->pamm_clients_stat_sum, $comm);  	         	 // write off loss in mt4	
						  $i_neg = 0 - ($value1->pamm_clients_stat_sum);									 // money is WITHDRAWN - negative value
 				  		  $this->mainform_model->change_statement($value1->pamm_clients_stat_cid,$value->request_acc_number,$i_neg,"I");    	 // change the investor statement
						  $this->mainform_model->charge_wallet($value1->pamm_clients_stat_cid,$value1->pamm_clients_stat_sum,'USD');		 // charge investor's wallet 

                                                  // generate the request (already implemented one)
						  $data_ri['request_acc_number']  = $value->request_acc_number; 
						  $data_ri['request_cid'] 	  = $value1->pamm_clients_stat_cid;
						  $data_ri['request_comment'] 	  = "Close account by trader with ".$value1->pamm_clients_stat_sum." USD withdrawal";
						  $data_ri['request_summ'] 	  = $value1->pamm_clients_stat_sum;
						  $data_ri['request_summ_origin'] = $value1->pamm_clients_stat_sum;
						  $data_ri['request_quote']       = '1.00';
						  $data_ri['request_type']        = '13';
						  $data_ri['request_status']      = '3';

						  $this->mainform_model->implement_close_investor($data_ri);
						}


		      //  change account_status
			  $this->mainform_model->close_account($value->request_acc_number);

		      //  internal implementation
			  $implement = FALSE;
			  $status = '3';
		          $ret = "CClosed";
			  $tid = $value->request_cid;
			}


		   if ($implement)
		        {
			    echo $action.','.$request_summ.','.$value->request_acc_number.','.$comm.'\r\n';
	                    $this->change_deposit($fp,$action,$value->request_acc_number,$request_summ,$comm);

				  // change client statement
					if ($neg)
						$rs = 0 - $request_summ;
					else
						$rs = $request_summ;
					  $this->mainform_model->change_statement($tid,$value->request_acc_number,$rs,$status);

				 // charge wallet in case of out of unjoint - ONLY AFTER IMPLEMENTING THE REQUEST
				 if ((substr($value->request_comment,0,1) == "I" and strpos($value->request_comment,'in')===FALSE) OR substr($value->request_comment,0,1) == "U" OR (substr($value->request_comment,0,1) == "T" and strpos($value->request_comment,'in')===FALSE))
				 {
				  $this->mainform_model->charge_wallet($tid,$request_summ_origin,$value->request_wallet);
				 }

			$status = '3';	
		 	}
			  $this->mainform_model->change_request_status($value->request_id,$status);

		}

      	    $out = "exit\r\n";
	    fwrite($fp, $out);

	}
}
?>