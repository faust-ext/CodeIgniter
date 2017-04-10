<?php
class Cron_quotes extends CI_Controller {

	function __construct()
    {
		parent::__construct(); 
		$this->load->model('Cron_model');
		
    }
	
	private function get_bid_ask_from_broker($quotes)
	{
	       // query to the remote server in order to get bids

		$quotes_array = array();		
		$ptr = @fsockopen(BROKER_HOST, BROKER_PORT, $errno, $errstr, BROKER_TIMEOUT);	
		if(fputs($ptr,"WQUOTES-$quotes\nQUIT\n")!=FALSE)
		{
			while(!feof($ptr))
			{
				if(($line = fgets($ptr,128)) == "end\r\n" ) break;			
				if(empty($line)) continue;			
				if (isset($line[0]) && ($line[0]=='u' || $line[0]=='d'))
				{
					$answer = $line;
					$tmp = explode(' ',$line);
					array_push($quotes_array,$tmp);
				}		
			}				
		}
		
		fclose($ptr);
		return $quotes_array[0];
	}
	
	public function index()
	{
		$quotes_array = array(1 => 'EURUSD,',2 => 'USDTRY,',3 => 'USDCHF,',4 => 'USDJPY,',5 => 'USDCBR,',6 => 'GBPUSD,');
		$requested_quotes_array = array();

		foreach($quotes_array as $quotes_str)
		{
			$requested_quotes_array = $this->get_bid_ask_from_broker($quotes_str);
			
			$symbol = $requested_quotes_array[1];
			$bid = $requested_quotes_array[2];
			$ask = $requested_quotes_array[3];

			$price = ($bid + $ask)/2; // take the mean value 

			// insert fresh quotes
			// for JPY symbol we have 3 digits

			if ($symbol != "USDJPY")
				$this->Cron_model->save_quotes($symbol, round($price,5,PHP_ROUND_HALF_UP));
			else
				$this->Cron_model->save_quotes($symbol, round($price,3,PHP_ROUND_HALF_UP));

		}	
		// delete old quotes
		$this->Cron_model->delete_quotes();

	}
}

/* End of file cron_qutoes.php */
/* Location: ./system/application/controllers/cron.php */
?>