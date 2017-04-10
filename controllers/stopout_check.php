<?php

class stopout_check extends CI_Controller {

	function __construct()
    {
		parent::__construct(); 
		$this->load->model('mainform_model');
		
    }
    public function index()
    {

	// loop to check divide denial accounts
        $account_array      = $this->mainform_model->get_divide_denial_accounts();
	foreach ($account_array as $value)
	{
		$ret = $this->stopout_status($value->login,'from stopout check');
		if (strlen($ret) !=0 AND $ret != '999999990')
		{
			$this->mainform_model->clear_divide_denial_flag($value->login);			
			echo "Stopout on account ".$value->login." restored\r\n";
		}
	}

        $account_array      = $this->mainform_model->get_failed_stopout_accounts();

	foreach ($account_array as $value)
	{
	    if ($value->action == 'DEL')
	    {
		    $ret = $this->stopout('DEL',$value->number,NULL,NULL,"from stopout check");		 
		    if (strlen($ret) == 0 OR trim($ret) == '999999990')  // application not run or MT4 not connected
		       {
				echo "Deleting from stopout list account ".$value->number." failed!\r\n";
		       }
		    else    // delete from  stopout check list
		       {
			  $data['number'] = $value->number;
			  $data['md']     = $value->md;
			  $data['fsb']    = $value->fsb;
			  $data['action'] = $value->action;
			  $this->mainform_model->delete_from_stopout_list($data);
			  $this->mainform_model->clear_divide_denial_flag($value->number);
		       }
	    }
	    elseif ($value->action == 'ADD')
	    {
		   $ret = $this->stopout('ADD',$value->number,$value->fsb,$value->md,"from stopout check");		 
		   if (strlen($ret) == 0 OR trim($ret) == '999999990')
		      {
				echo "Adding from stopout list account ".$value->number." failed!\r\n";
		      }
		    else     // delete from  stopout check list
		      {
			  $data['number'] = $value->number;
			  $data['md']     = $value->md;
			  $data['fsb']    = $value->fsb;
			  $data['action'] = $value->action;
			  $this->mainform_model->delete_from_stopout_list($data);
			  $this->mainform_model->clear_divide_denial_flag($value->number);
		      }

	    }
	}

    }	// index
}  // class