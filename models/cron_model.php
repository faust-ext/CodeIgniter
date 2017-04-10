<?php 

class Cron_model extends CI_Model 
{
	function __construct()
    {
		parent::__construct();
    }
	
	function save_quotes($symbol, $price)
	{
	// insert current quotes to the local database

		return $this->db->set('symbol', $symbol)
		->set('price', $price)
		->set('add_datetime', 'NOW()', FALSE)
		->insert('investroom_system_quotes');
	}

	function delete_quotes()
	{
	// delete current quotes from local database
		$this->db->query("DELETE FROM investroom_system_quotes ORDER BY id ASC LIMIT 6");
	}
}
?>