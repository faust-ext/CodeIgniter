<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation {
	var $_error_array			= array(); // $_error_array protected by default CI_Form_validation but we need access to it.
}
