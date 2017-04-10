<?php
class Urgent_engine extends CI_Controller {

	function __construct()
    {
		parent::__construct(); 
		$this->load->model('mainform_model');
		
    }
	private function obtain_history_only_trader($acc_number)
	{	
		   $time = mktime(23,59,59,date('n')-1,date('j'),date('Y')); // trial trade period - 5 days 
		   return $this->mainform_model->ohot($acc_number,$time);
	}
	private function obtain_history_investor($acc_number,$tid)
	{	
		   $time = mktime(23,59,59,date('n')-1,date('j'),date('Y')); // trial trade period - 5 days 
		   return $this->mainform_model->ohi($acc_number,$tid,$time);
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
				if(strlen($ret)<11)
				  $cycle=FALSE;
			}
			sleep(1);
		   }
	return $ret."???".$iteration;
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
    private function obtain_open_positions($acc_number)
    {
			$query = "http://80.93.48.133/pamm_inout.aspx?key=20&login=".$acc_number;

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
			    if(strlen($ret_cd)>10 OR strlen($ret_cd)==0)
				{
				 $ret_cd = $this->retry_query($query);
				 $ret1 = substr($ret_cd,0,strpos($ret_cd,'???'));
				 $ret_call="Return entry - ".substr($ret_cd,strpos($ret_cd,'???')+3);
				error_log("change deposit - ### ".$ret_call. " ".date('Y-m-d H:i:s')."\r\n", 3, "test_engine.log");
				error_log($acc_number."-----------".$ret1, 3, "test_engine.log");
				error_log("\r\ncd - ### \r\n\r\n", 3, "test_engine.log");
				}	

	      return $ret_cd;
    }
	
	public function index()
	{


	// implement queries

		$requests = (array) $this->mainform_model->get_urgent_requests();
		foreach ($requests as $value)
		{
		  $request_summ = $value->request_summ;
		  $neg = FALSE;
		  $implement = TRUE;
		  if($this->obtain_open_positions($value->request_acc_number)==0)
			  $implement = FALSE;
		  if (substr($value->request_comment,0,1) == "T" and strpos($value->request_comment,'in')===FALSE)
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
				}
			elseif ($th -300 < $value->request_summ AND $th > 300)
				{
				// rest of the sum could be withdrawn
				   $request_summ = $th - 300;
				   $request_summ_origin = $request_summ*($value->request_quote);
				   $comm = 'Request of '.$value->request_summ.' withdraw implemented of '.$request_summ.'value';
				   $this->mainform_model->change_request($request_summ,$request_summ_origin,$comm,$value->request_id);
				}
			elseif ($th <= 300)
				{
				// decline the request
				  $implement = FALSE;
				  $status = '2';
				  $ret = "Declined";
				}
		     }
		  elseif (substr($value->request_comment,0,1) == "I" and strpos($value->request_comment,'in')===FALSE)
		     {
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
				}
			elseif ($ih < $value->request_summ AND $ih > 0)
				{
				// rest of the sum could be withdrawn
				   $request_summ = $ih;
				   $request_summ_origin = $request_summ*($value->request_quote);
				   $comm_changed = 'Request of '.$value->request_summ.' withdraw implemented of '.$request_summ.' amount';
				   $this->mainform_model->change_request($request_summ,$request_summ_origin,$comm_changed,$value->request_id);
				}
			elseif ($ih <= 0)
				{
				// decline the request
				  $implement = FALSE;
				  $status = '2';
				  $ret = "Declined";
				}


		     }
		   if ($implement)
		        {
	                  $summ = $request_summ*100;
			  $ret = $this->implement_remote_activation($action,$summ,$value->request_acc_number,$comm);

			    if(strlen($ret)>10 OR strlen($ret)==0)
				{
				 $query = "http://80.93.48.133/pamm_inout.aspx?key=".$action."&login=".$value->request_acc_number."&summ=".$summ."&comm=".$comm;
				 $ret = $this->retry_query($query);
				 $ret1= substr($ret,0,strpos($ret,'???'));
				 $ret_call="Return entry - ".substr($ret,strpos($ret,'???')+3);
				error_log("implement remote activation - ### ".$ret_call. " ".date('Y-m-d H:i:s')."\r\n", 3, "test_engine.log");
				error_log($value->request_acc_number."-----------".$ret1, 3, "test_engine.log");
				error_log("\r\nira - ### \r\n\r\n", 3, "test_engine.log");
				}	

				  // ret analyze 

				  // storing in pamm_mt_errors table
				if (substr($ret,0,7) == "9999999")
				{
				  $mt_errors['pamm_mt_errors_account'] = $value->request_acc_number;
				  $mt_errors['pamm_mt_errors_code'] = $ret;
				  $this->mainform_model->store_mt_errors($mt_errors); 

				}
				else
				{
				  // change clients' deposit
				  $this->mainform_model->charge_deposit($value->request_acc_number,$request_summ);
				  // change client statement
					if ($neg)
						$rs = 0 - $request_summ;
					else
						$rs = $request_summ;
					  $this->mainform_model->change_statement($tid,$value->request_acc_number,$rs,$status,0);

				}
			$status = '3';	
		 	}
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


	}
}
?>