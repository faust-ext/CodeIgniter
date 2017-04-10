<?php
class Engine extends CI_Controller {

	function __construct()
    {
		parent::__construct(); 
		$this->load->model('mainform_model');
		
    }
    private function change_query($query)
    {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $query);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$a = curl_exec($ch);
			return $a;

    }

    private function obtain_dayprofit($acc_number)
    {
		// Obtain the dayprofit from MT4 server

			$query = "http://80.93.48.133/pamm_utils.aspx?key=50&login=".$acc_number;
	
			  $ret_cd      = $this->change_query($query);
			    if(strlen($ret_cd)>11 OR strlen($ret_cd)==0)
				{
				 $ret_cd = $this->retry_query($query);
					if (strlen($ret_cd)>11 OR strlen($ret_cd)==0)  // Fatal DoS after 5 loops
						{
						 $ret1 = substr($ret_cd,0,strpos($ret_cd,'??'));
						 $ret_call="Fatal DoS cd- ".substr($ret_cd,strpos($ret_cd,'??')+2);
						 error_log("change deposit return - ".$ret_cd."\r\n", 3, "test_stat_engine.log");
						 error_log("change deposit - ### ".$ret_call. " ".date('Y-m-d H:i:s')."\r\n", 3, "test_stat_engine.log");
						 error_log($acc_number."-----------".$ret1, 3, "test_stat_engine.log");
						 error_log("\r\ncd - ### \r\n\r\n", 3, "test_stat_engine.log");
						 exit();
						}

				 $ret1 = substr($ret_cd,0,strpos($ret_cd,'??'));
				 $ret_call="Return entry - ".substr($ret_cd,strpos($ret_cd,'??')+2);
				error_log("change deposit - ### ".$ret_call. " ".date('Y-m-d H:i:s')."\r\n", 3, "test_stat_engine.log");
				error_log($acc_number."-----------".$ret1, 3, "test_stat_engine.log");
				error_log("\r\ncd - ### \r\n\r\n", 3, "test_stat_engine.log");
				}	

	      return round($ret_cd/100,2);

    }

     private function retry_query($query)
    {
	$ret = '';	
	$iteration = 0;
	$cycle= TRUE;
		for($i=1;$i<5;$i++)
		   { 
		    if ($cycle)	
			{
				$iteration++;
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $query);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_TIMEOUT, 10);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$ret = curl_exec($ch);
				if(strlen($ret)<11 && strlen($ret)>0 )
				  $cycle=FALSE;
			}
			sleep(1);
		   }
	return $ret."??".$iteration;
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
    private function implement_remote_activation($action,$summ,$acc_number,$comm)
    {
		// Deposit the activation fee on MT4 server

                // request to the remote server
		// key = 100 or 200 : deposit or request-in OR withdraw or request-out


			$query = "http://80.93.48.133/pamm_inout.aspx?key=".$action."&login=".$acc_number."&summ=".$summ."&comm=".$comm;

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $query);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$a = curl_exec($ch);
			return $a;

    }

    private function obtain_balance($acc_number)
    {
		// Obtain the balacne from MT4 server

			$query = "http://80.93.48.133/pamm_inout.aspx?key=10&login=".$acc_number;

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $query);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$a = curl_exec($ch);
			return $a;

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
    private function change_deposit_query($query)
    {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $query);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$a = curl_exec($ch);
			return $a;

    }
    private function change_deposit($key,$acc_number,$summ,$comm)
    {
		// Deposit the activation fee on MT4 server

                // request to the remote server
		// key = 100: deposit or request-in
		// key=200 : write-off


			$query = "http://80.93.48.133/pamm_inout.aspx?key=".$key."&login=".$acc_number."&summ=".$summ."&comm=".$comm;
	
			  $ret_cd      = $this->change_deposit_query($query);
			    if(strlen($ret_cd)>11 OR strlen($ret_cd)==0)
				{
				 $ret_cd = $this->retry_query($query);
					if (strlen($ret_cd)>11 OR strlen($ret_cd)==0)  // Fatal DoS after 5 loops
						{
						 $ret1 = substr($ret_cd,0,strpos($ret_cd,'??'));
						 $ret_call="Fatal DoS cd- ".substr($ret_cd,strpos($ret_cd,'??')+2);
						 error_log("change deposit - ### ".$ret_call. " ".date('Y-m-d H:i:s')."\r\n", 3, "test_engine.log");
						 error_log($acc_number."-----------".$ret1, 3, "test_engine.log");
						 error_log("\r\ncd - ### \r\n\r\n", 3, "test_engine.log");
						 exit();
						}

				 $ret1 = substr($ret_cd,0,strpos($ret_cd,'??'));
				 $ret_call="Return entry - ".substr($ret_cd,strpos($ret_cd,'??')+2);
				error_log("change deposit - ### ".$ret_call. " ".date('Y-m-d H:i:s')."\r\n", 3, "test_engine.log");
				error_log($acc_number."-----------".$ret1, 3, "test_engine.log");
				error_log("\r\ncd - ### \r\n\r\n", 3, "test_engine.log");
				}	

	      return $ret_cd;
    }
	
	public function index()
	{
	// divide profit
	        $account_array      = $this->mainform_model->get_divide_accounts();

		$result_array = Array();
		$detailed_history1 = Array();
	        foreach($account_array as $value)
			{
			  $ret_balance      = $this->obtain_balance($value->login);
			    
			    if(strlen($ret_balance)>10 OR strlen($ret_balance)==0)
				{
				error_log("obtain balance - ### ".$ret_call. " ".date('Y-m-d H:i:s')."\r\n", 3, "test_engine_telnet.log");
				error_log($value->login."-----------".$ret1, 3, "test_engine_telnet.log");
				error_log("\r\nob - ### \r\n\r\n", 3, "test_engine_telnet.log");
				die('Scorecard obtain balance \r\n');
				}	

			  $result_array['pamm_tp_account']    = $value->login;
                          $result_array['pamm_tp_total']      = $ret_balance/100;
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
//					  $this->change_deposit(200,$value->login,round($earn,2)*100,$comm_nibz);                    // all profit is written-off
//					  $this->change_deposit(100,$value->login,round($earn,2)*100,$comm_nibz);                    // trader profit is returned
//			  		  $this->mainform_model->change_statement($trader_id_for_divide[0]->tid,$value->login,round($earn,2),"PLU");      // change the trader statement
					  $this->change_deposit_write(200,$value->login,round($earn,2)*100,$comm_nibz);                    // all profit is written-off
					  $this->change_deposit_write(100,$value->login,round($earn,2)*100,$comm_nibz);                    // trader profit is returned
				
			  	}
				elseif ($prev_tp_result <0 and abs($prev_tp_result) >= abs($earn))  // loss in previous trade period and earn is less than loss
				{
						if (count($detailed_history_became_zero) > 0)
							$comm_nibz = "rolover_profit_divide_bz_3";
						else
							$comm_nibz = "rolover_profit_divide_ni_3";	
//					  $this->change_deposit(200,$value->login,round($earn,2)*100,$comm_nibz);                    // all profit is written-off
//					  $this->change_deposit(100,$value->login,round($earn,2)*100,$comm_nibz);                    // trader profit is returned
//			  		  $this->mainform_model->change_statement($trader_id_for_divide[0]->tid,$value->login,round($earn,2),"PLU");      // change the trader statement
					  $this->change_deposit_write(200,$value->login,round($earn,2)*100,$comm_nibz);                    // all profit is written-off
					  $this->change_deposit_write(100,$value->login,round($earn,2)*100,$comm_nibz);                    // trader profit is returned


				}
				elseif ($prev_tp_result <0 and abs($prev_tp_result) < abs($earn))  //
				{
						if (count($detailed_history_became_zero) > 0)
							$comm_nibz = "rolover_profit_divide_bz_4";
						else
							$comm_nibz = "rolover_profit_divide_ni_4";	
//					  $this->change_deposit(200,$value->login,round($earn,2)*100,$comm_nibz);                    // all profit is written-off
//					  $this->change_deposit(100,$value->login,round($earn,2)*100,$comm_nibz);                    // trader profit is returned
//			  		  $this->mainform_model->change_statement($trader_id_for_divide[0]->tid,$value->login,round($earn,2),"PLU");      // change the trader statement
					  $this->change_deposit_write(200,$value->login,round($earn,2)*100,$comm_nibz);                    // all profit is written-off
					  $this->change_deposit_write(100,$value->login,round($earn,2)*100,$comm_nibz);                    // trader profit is returned
					  

				}
			      }
			      else  // there are investors
			      {	
	   			if ($prev_tp_result >= 0 )   // no loss in previous trade period
				{

					  $this->change_deposit(200,$value->login,round($earn,2)*100,"rolover_profit_divide_1");                    // all profit is written-off
					  $percentage = $this->mainform_model->get_percentage($value->login);	                          // obtain trader percent
					  $u_divide = round((($percentage[0]->distr_upr*$earn)/100),2);                              // trader profit calculation
					  $this->change_deposit(100,$value->login,$u_divide*100,"trader_profit_divide");                 	  // trader profit is returned
			  		  $this->mainform_model->change_statement($trader_id_for_divide[0]->tid,$value->login,$u_divide,"PLU");      // change the trader statement
					  $i_divide = $earn - $u_divide;
			  
					  foreach ($detailed_history as $value1)
						{
						if ($value1->pamm_clients_stat_sum > 0) // zero investors are not taken part into divide
						 {
						  $comm = "investor_".$value1->pamm_clients_stat_cid."_profit_divide";
						  $i_divide_by_account = round($i_divide*($value1->pamm_clients_stat_sum)/($investor_history[0]->pamm_clients_stat_sum),2);                       // profit per account
//						  $this->change_deposit(100, $value->login, $i_divide_by_account*100, $comm);  						         		// write profit to mt4	
// 				  		  $this->mainform_model->change_statement($value1->pamm_clients_stat_cid,$value->login,$i_divide_by_account,"PLI");        				// change the investor statement
						  $this->change_deposit_write(100, $value->login, $i_divide_by_account*100, $comm);
//						  $this->mainform_model->change_wallet($value1->pamm_clients_stat_cid,$i_divide_by_account);  			 				// add profit to wallet 
                                                  $detailed_history1[]['tid'] = $value1->pamm_clients_stat_cid;
                                                  $detailed_history1[]['sum'] = $i_divide_by_account;						 
						 }
						}

					  $this->data['foo']            = $result_array['pamm_tp_total']." - ".$result_array['pamm_tp_start'][0]->pamm_tp_profitloss." - ".$common_history[0]->pamm_clients_stat_sum." Earn:".$earn." 1 ________ Trader quota:".$u_divide." Investors' quota:".$i_divide."<BR>";
					  $this->data['foo_array']      = $detailed_history1;
				}
				elseif ($prev_tp_result <0 and abs($prev_tp_result) >= abs($earn))  // loss in previous trade period and earn is less than loss
				{
//                                          $this->change_deposit(200,$value->login,round($earn,2)*100,"rolover_bg_divide_3");   // all profit is written-off
                                          $this->change_deposit_write(200,$value->login,round($earn,2)*100,"rolover_bg_divide_3");
					  $u_divide = round(($trader_history[0]->pamm_clients_stat_sum)/($common_history[0]->pamm_clients_stat_sum)*$earn,2);    				// trader profit write-off
//  					    $this->change_deposit(100,$value->login,$u_divide*100,"trader_loss_bg_earn");   										// trader profit is returned
					  $this->change_deposit_write(100,$value->login,$u_divide*100,"trader_loss_bg_earn");
			  		  $this->mainform_model->change_statement($trader_id_for_divide[0]->tid,$value->login,$u_divide,"PLU");     							        // change the trader statement
			  
					  $detailed_history = $this->obtain_detailed_history($value->login);
					  foreach ($detailed_history as $value1)
						{
						if ($value1->pamm_clients_stat_sum > 0) // zero investors are not taken part into divide
						 {
						  $comm = "investor_".$value1->pamm_clients_stat_cid."_loss_bg_earn";
						  $i_divide_by_account = round($earn*($value1->pamm_clients_stat_sum)/($common_history[0]->pamm_clients_stat_sum),2);    			// loss per account
						  $this->change_deposit(100, $value->login, $i_divide_by_account*100, $comm);    			       						// write off loss to mt4	
 				  		  $this->mainform_model->change_statement($value1->pamm_clients_stat_cid,$value->login,$i_divide_by_account,"PLI");        				// change the investor statement
//						  $this->mainform_model->change_wallet($value1->pamm_clients_stat_cid,$i_divide_by_account);  							// subtract loss from wallet 
                                                  $detailed_history1[]['tid'] = $value1->pamm_clients_stat_cid;
                                                  $detailed_history1[]['sum'] = $i_divide_by_account;						 
						 }
						}

					  $this->data['foo']            = $result_array['pamm_tp_total']." - ".$result_array['pamm_tp_start'][0]->pamm_tp_profitloss." - ".$common_history[0]->pamm_clients_stat_sum." Earn:".$earn." 3 - previous loss bigger than earn Trader's loss:".$u_divide."<BR>";
					  $this->data['foo_array']      = $detailed_history1;
					
				}
				elseif ($prev_tp_result <0 and abs($prev_tp_result) < abs($earn))   													// loss in previous trade period and earn is bigger than loss
				{
                                          $this->change_deposit(200,$value->login,round($prev_tp_result,2)*100,"rolover_less_loss_divide_4");   										// loss is written-off
					  $u_divide = round(($trader_history[0]->pamm_clients_stat_sum)/($common_history[0]->pamm_clients_stat_sum)*$prev_tp_result,2);  				// trader profit write-off
                                          $this->change_deposit(100,$value->login,$u_divide*100,"rolover_less_loss_trader");   										// trader loss is written-off
					  $u_divide = 0 - $u_divide;
			  		  $this->mainform_model->change_statement($trader_id_for_divide[0]->tid,$value->login,$u_divide,"PLU");      									// change the trader statement
					  $detailed_history = $this->obtain_detailed_history($value->login);
					  foreach ($detailed_history as $value1)
						{
						if ($value1->pamm_clients_stat_sum > 0) // zero investors are not taken part into divide
						 {
						  $comm = "investor_".$value1->pamm_clients_stat_cid."_less_loss";
						  $i_divide_by_account = round($prev_tp_result*($value1->pamm_clients_stat_sum)/($common_history[0]->pamm_clients_stat_sum),2);		 // loss per account
						  $this->change_deposit(100, $value->login, $i_divide_by_account*100, $comm);           							 // write off loss to mt4	
						  $i_divide_by_account_neg = 0 - $i_divide_by_account;
 				  		  $this->mainform_model->change_statement($value1->pamm_clients_stat_cid,$value->login,$i_divide_by_account_neg,"PLI");        			 // change the investor statement
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
                                          $this->change_deposit(200,$value->login,round($rest_divide*100,0),"rolover_less_profit_divide_4");   // profit is written-off

					  $percentage = $this->mainform_model->get_percentage($value->login);	 			// obtain trader percent
					  $u_divide = round(($percentage[0]->distr_upr)*$rest_divide,2);  	      		// trader profit calculation
					  $this->change_deposit(100,$value->login,round($u_divide,0),"trader_less_profit");   			// trader profit is returned
					  $u_divide = $u_divide/100;
			  		  $this->mainform_model->change_statement($trader_id_for_divide[0]->tid,$value->login,$u_divide,"PLU");  	// change the trader statement
					  $i_divide = $rest_divide - $u_divide;
			  
					  $detailed_history = $this->obtain_detailed_history($value->login);
					  foreach ($detailed_history as $value1)
						{
						if ($value1->pamm_clients_stat_sum > 0) // zero investors are not taken part into divide
						 {
						  $comm = "investor_".$value1->pamm_clients_stat_cid."_less_profit";
						  $i_divide_by_account = round($i_divide*($value1->pamm_clients_stat_sum)/($investor_history[0]->pamm_clients_stat_sum),2);                       // profit per account
						  $this->change_deposit(100, $value->login, $i_divide_by_account*100, $comm);           								  // write profit to mt4	
 				  		  $this->mainform_model->change_statement($value1->pamm_clients_stat_cid,$value->login,$i_divide_by_account,"PLI");       				  // change the investor statement
//						  $this->mainform_model->change_wallet($value1->pamm_clients_stat_cid,$i_divide_by_account);				  			  // add profit to wallet 
                                                  $detailed_history1[]['tid'] = $value1->pamm_clients_stat_cid;
                                                  $detailed_history1[]['sum'] = $i_divide_by_account;	
					          $detailed_history1[]['pamm_clients_stat_sum'] = $value1->pamm_clients_stat_sum;
                                                  $detailed_history1[]['inv_history'] = $investor_history[0]->pamm_clients_stat_sum;
                                                  $detailed_history1[]['com_history'] = $common_history[0]->pamm_clients_stat_sum;
						 }
						}

					  $this->data['foo']            = $result_array['pamm_tp_total']." - ".$result_array['pamm_tp_start'][0]->pamm_tp_profitloss." - ".$common_history[0]->pamm_clients_stat_sum." Earn:".$earn." 4 - previous loss less than earn".$prev_tp_result." Rest after subtracting the loss (To be added to trader's deposit):".$u_divide." Rest after subtracting the loss (To be distributed between investors)".$i_divide."<BR>";
					  $this->data['foo_array']      = $detailed_history1;
				}
			     } // there are investors
			  }
                          

			  elseif ($earn < 0)    // loss after trade period
			  {
			  $detailed_history = $this->obtain_detailed_history($value->login);
			  if (count($detailed_history) == 0)
			  {
	                          $this->change_deposit(100,$value->login,abs(round($earn,2))*100,"rolover_loss_divide_ni");   										// loss is written-off
	                          $this->change_deposit(200,$value->login,abs(round($earn,2))*100,"rolover_loss_trader_ni");   										// whole trader profit is written-off
				  $this->mainform_model->change_statement($trader_id_for_divide[0]->tid,$value->login,round($earn,2),"PLU");   							// change the trader statement
				  $u_divide=0;
			  }
			  else
			  {
	                          $this->change_deposit(100,$value->login,abs(round($earn,2))*100,"rolover_less_loss_divide_case2");   										// loss is written-off
				  $u_divide = round(($trader_history[0]->pamm_clients_stat_sum)/($common_history[0]->pamm_clients_stat_sum)*$earn,2);		  				// trader profit write-off
	                          $this->change_deposit(200,$value->login,$u_divide*100,"rolover_less_loss_trader");   										// trader loss is written-off
				  $this->mainform_model->change_statement($trader_id_for_divide[0]->tid,$value->login,$u_divide,"PLU");      									// change the trader statement

				  foreach ($detailed_history as $value1)
					{
					if ($value1->pamm_clients_stat_sum > 0) // zero investors are not taken part into divide
					 {
					  $comm = "investor_".$value1->pamm_clients_stat_cid."_less_loss";
					  $i_divide_by_account = round($earn*($value1->pamm_clients_stat_sum)/($common_history[0]->pamm_clients_stat_sum),2);		 	// loss per account
					  $this->change_deposit(200, $value->login, $i_divide_by_account*100, $comm);           							 // write off loss to mt4	
//					  $i_divide_by_account_neg = 0 - $i_divide_by_account;
	 				  $this->mainform_model->change_statement($value1->pamm_clients_stat_cid,$value->login,$i_divide_by_account,"PLI");        			 // change the investor statement
//					  $this->mainform_model->change_wallet($value1->pamm_clients_stat_cid,$i_divide_by_account);						 // subtract loss from wallet 
                	                  $detailed_history1[]['tid'] = $value1->pamm_clients_stat_cid;
	                                  $detailed_history1[]['sum'] = $i_divide_by_account;						 
					 }
					}
			  }	
			  $this->data['foo']            = $result_array['pamm_tp_total']." - ".$result_array['pamm_tp_start'][0]->pamm_tp_profitloss." - ".$common_history[0]->pamm_clients_stat_sum." Earn:".$earn." 2____ To be written off from investor:".$u_divide;
			  $this->data['foo_array']      = $detailed_history1;

			  }
			  elseif ($earn == 0)  // balance is intact after trade period or there were no operations
			  {

			  $this->change_deposit(200,$value->login,0,"trader_divide_zero_case5");  							 								// trader divide zero 
	  		  $this->mainform_model->change_statement($trader_id_for_divide[0]->tid,$value->login,0,"PLU");  					 								// change the trader statement
			  $detailed_history = $this->obtain_detailed_history($value->login);
			  foreach ($detailed_history as $value1)
				{
				if ($value1->pamm_clients_stat_sum > 0) // zero investors are not taken part into divide
				 {
				  $comm = "investor_".$value1->pamm_clients_stat_cid."_divide_zero";
				  $this->change_deposit(200, $value->login, 0, $comm);        				                        								 // write zero divide to mt4
		  		  $this->mainform_model->change_statement($value1->pamm_clients_stat_cid,$value->login,0,"PLI");			        						 // change the investor statement	
			//	  $this->mainform_model->change_wallet($value1->pamm_clients_stat_cid,round($i_divide*($value1->pamm_clients_stat_sum)/($common_history[0]->pamm_clients_stat_sum),2));  // subtract loss from wallet 

                                                  $detailed_history1[]['tid'] = $value1->pamm_clients_stat_cid;
                                                  $detailed_history1[]['sum'] = 0;
                                 }
				}

			  $this->data['foo']            = " 5- zero<BR>";
			  $this->data['foo_array']      = $detailed_history1;
			  }

       
			}  //foreach

      
}
?>