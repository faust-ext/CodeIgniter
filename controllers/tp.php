<?php
class tp extends CI_Controller {

	function __construct()
    {
		parent::__construct(); 
		$this->load->model('mainform_model');
		
    }
    public function index()
    {
      $this->generate_tp();
    }   // index
}         // class
?>