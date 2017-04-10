<?php
class teen extends CI_Controller {

	function __construct()
    {
		parent::__construct(); 
		$this->load->model('mainform_model');
		
    }
    private function obtain_balance1($query)
    {
		// Obtain the balacne from MT4 server                                    

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $query);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$a = curl_exec($ch);
			return $a;

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

    public function index()
    {

	        $account_array      = $this->mainform_model->get_test_divide_accounts();
	        foreach($account_array as $value)
	        {
			$ret_call = '';
			$query = "http://80.93.48.133/pamm_inout.aspx?key=10&login=".$value->login."&comm=".date('H').date('i').date('s');
			$ret = $this->obtain_balance1($query);
		    if(strlen($ret)>10 OR strlen($ret)==0)
			{
			 $ret = $this->retry_query($query);
			 $ret1 = substr($ret,0,strpos($ret,'???'));
			 $ret_call="Return entry - ".substr($ret,strpos($ret,'???')+3);
			error_log("10 - ### ".$ret_call. " ".date('Y-m-d H:i:s')."\r\n", 3, "test_engine.log");
			error_log($value->login."-----------".$ret1, 3, "test_engine.log");
			error_log("\r\n10 - ### \r\n\r\n", 3, "test_engine.log");
			}	
	        }	
	        foreach($account_array as $value)
	        {
			$ret_call = '';
			$query = "http://80.93.48.133/pamm_inout.aspx?key=100&login=".$value->login."&summ=1&comm=100check".date('H').date('i').date('s');
			$ret = $this->obtain_balance1($query);
		    if(strlen($ret)>10 OR strlen($ret)==0)
			{
			 $ret = $this->retry_query($query);
			 $ret1 = substr($ret,0,strpos($ret,'???'));
			 $ret_call="Return entry - ".substr($ret,strpos($ret,'???')+3);
			 error_log("100 - ### ".$ret_call. " ".date('Y-m-d H:i:s')."\r\n", 3, "test_engine.log");
			 error_log($value->login."-----------".$ret1, 3, "test_engine.log");
			 error_log("\r\n100 - ### \r\n\r\n", 3, "test_engine.log");
			}	
	        }	
	        foreach($account_array as $value)
	        {
			$ret_call = '';
			$query = "http://80.93.48.133/pamm_inout.aspx?key=200&login=".$value->login."&summ=1&comm=200check".date('H').date('i').date('s');
			$ret = $this->obtain_balance1($query);
		    if(strlen($ret)>10 OR strlen($ret)==0)
			{
			 $ret = $this->retry_query($query);
			 $ret1 = substr($ret,0,strpos($ret,'???'));
			 $ret_call="Return entry - ".substr($ret,strpos($ret,'???')+3);
			 error_log("200 - ### ".$ret_call. " ".date('Y-m-d H:i:s')."\r\n", 3, "test_engine.log");
			 error_log($value->login."-----------".$ret1, 3, "test_engine.log");
			 error_log("\r\n200 - ### \r\n\r\n", 3, "test_engine.log");
			}	
	        }	



    }
}
?>