<?php
class Stat_engine extends CI_Controller {

	function __construct()
    {
		parent::__construct(); 
		$this->load->model('mainform_model');
		
    }
	function reconnect()
       {       
	error_log("\r\nReconnect start - ### \r\n\r\n", 3, "test_stat_engine.log");
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
				error_log("\r\nReconnect failed - ### \r\n\r\n", 3, "test_stat_engine.log");
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


	error_log("\r\nReconnect finished - ### \r\n\r\n", 3, "test_stat_engine.log");
	return $fp;

	}

	
   	public function index()
	{

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

	        $account_array      = $this->mainform_model->get_divide_accounts();
	 foreach ($account_array as $value)
		{
		  $balance   = $this->obtain_balance($fp,$value->login, 'from stat engine');
		  $dayprofit   = $this->obtain_dayprofit($fp,$value->login);

			$profitable     = $this->mainform_model->obtain_profitable($value->login);
			$down           = round($profitable[0]->pamm_tp_profitable*100,2);

		        $old_down =  $this->mainform_model->obtain_down_from_db($value->login);
			if ($down <0 AND abs($old_down[0]->down) < abs($down) )
				$down_to_write = $down;
			else
				$down_to_write = $old_down[0]->down;
		  $this->mainform_model->store_dynamic($value->login,$balance,$dayprofit,$down_to_write);
		}
	// closing the socket
		    $out = "exit\r\n";
		    fwrite($fp, $out);


	}
	
}