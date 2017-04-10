<?php
class testsocket extends CI_Controller {

	function __construct()
    {
		parent::__construct(); 
		$this->load->model('mainform_model');
		
    }
 public function index()
{
 $fp = fsockopen("80.93.48.133", 10024, $errno, $errstr, 30);
		$f = fopen("test-socket.txt","a+");
       if ($fp)
	fwrite($f,"Now:".date('H:i:s d-m-Y')." Socket is open successfully\r\n");
       else
	fwrite($f,"Now:".date('H:i:s d-m-Y')." Socket open failed!\r\n");

	fclose($f);
}


 }

?>