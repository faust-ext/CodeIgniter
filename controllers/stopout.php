<?php

class stopout extends CI_Controller {

	function __construct()
    {
		parent::__construct(); 
		$this->load->model('mainform_model');
		
    }
    public function index()
    {
	$f1 = fopen("f1.txt","a+") or die('Cant open!');

        $account_array      = $this->mainform_model->get_divide_accounts();

	foreach ($account_array as $value)
	{

	   if (trim($this->stopout_status($value->login,'from stopout')) == "1")
	      {
		fwrite($f1,$value->login." is dipped!\r\n");
	        $this->stopout('DEL',$value->login,NULL,NULL,"from stopout");		 
		fwrite($f1,$value->login." is deleted from stopout database!\r\n");


		// decline all requests


		$urgent_u_requests = $this->mainform_model->get_trader_urgent_requests($value->login);

			foreach ($urgent_u_requests as $value_u)
			{
			   $this->mainform_model->change_request_status($value_u->request_id,'2');
			   $comm_changed = 'Urgent trader withdraw request declined due to the stopout (stopout)';
			   $this->mainform_model->change_request_comment($comm_changed,$value_u->request_id);
			}
		


		$urgent_i_requests = $this->mainform_model->get_investor_urgent_requests($value->login);

			foreach ($urgent_i_requests as $value_i)
			{
			   $this->mainform_model->change_request_status($value_i->request_id,'2');
			   $comm_changed = 'Urgent investor withdraw request declined due to the stopout (stopout)';
			   $this->mainform_model->change_request_comment($comm_changed,$value_i->request_id);
			}
	
		// clients statement correction
		$base_history = $this->obtain_history($value->login);
		$bh = $base_history[0]->pamm_clients_stat_sum;

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


			    $current_balance = $this->obtain_balance($fp,$value->login,' from stopout');

			    $out = "exit\r\n";
			    fwrite($fp, $out);
			    @fclose($fp);

			// obtain trader id
			   $trader_id = $this->mainform_model->obtain_trader_id($value->login);
			   $cid = $trader_id[0]->tid; 


			$stopout_diff = $current_balance - $bh;

			  $common_history		   = $this->obtain_history($value->login);		
		      	  $detailed_history 		   = $this->obtain_detailed_history($value->login);
			  $trader_history		   = $this->mainform_model->obtain_history_only_trader($value->login);		// for equal divide without trader's percent



		                  if ($common_history[0]->pamm_clients_stat_sum != 0)
					{
					  $u_divide = round(($trader_history[0]->pamm_clients_stat_sum)/($common_history[0]->pamm_clients_stat_sum)*$stopout_diff,2);	  	// stopout loss write-off
					  $this->mainform_model->change_statement($cid,$value->login,$u_divide,"PLU",0);      							// change the trader statement

					  foreach ($detailed_history as $value1)
						{
						if ($value1->pamm_clients_stat_sum > 0) // zero investors are not taken part into divide
						 {
						  $comm = "investor_".$value1->pamm_clients_stat_cid."_stopout_loss";
						  $i_divide_by_account = round($stopout_diff*($value1->pamm_clients_stat_sum)/($common_history[0]->pamm_clients_stat_sum),2);		// stopout correction per account
		 				  $this->mainform_model->change_statement($value1->pamm_clients_stat_cid,$value->login,$i_divide_by_account,"PLI",0);        		// change the investor statement
	                	                  $detailed_history1[]['tid'] = $value1->pamm_clients_stat_cid;
		                                  $detailed_history1[]['sum'] = $i_divide_by_account;						 
						 }
						}
					 fwrite($f1,$value->login." is corrected due to the stopout summ!\r\n");
					}
				else
					echo "Common history on account ".$value->login." is zero in the stopout correction summ!\r\n";

	        $this->mainform_model->set_stopout_flag($value->login);		 

	        $this->close_single($value->login,1,NULL);		 
		fwrite($f1,$value->login." is closed!\r\n");
	      }
	  elseif (trim($this->stopout_status($value->login, 'from stopout')) == "0")
	      {
		fwrite($f1,$value->login." isn't dipped!\r\n");
	      }
	  else
	      {
		fwrite($f1,$value->login." has status ".trim($this->stopout_status($value->login,"from stopout"))."\r\n");
	      }

	}
	fclose($f1);
    }
}
?>