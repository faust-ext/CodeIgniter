<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class mainform_model extends CI_Model 
{
	function __construct()
    {
		parent::__construct();
    }
	
	
	function get_wallet($id_trader) 
	{
	// obtain wallets for all currencies
		return $this->db->select('USD,EUR,GBP,JPY,CHF,TRY,RUR')
		->where('id',$id_trader)
		->get('investroom_personal_accounts')->row();
	}

	public function get_country_list() 
	{
	// obtain select in the "open pamm account" form options
		return $this->db->select('num_code')
		->select('country_name')
		->select('num_code')
		->from('system_iso3166_l10n')
		->order_by('country_name', 'ASC')	
		->get()->result();
	}
	public function get_quotes() 
	{
	// obtain quotes for all currencies
		return $this->db->select('price')
		->from('investroom_system_quotes')
		->limit(6)
		->order_by('id', 'DESC')	
		->get()->result();
	}
	public function get_join_requests($acc_number) 
	{
	// obtain investors' join- and in-requests
		return $this->db->select('pamm_requests.*, pamm_requests_status_type.pamm_requests_status_name AS status_word')
		->from('pamm_requests')
		->join('pamm_requests_status_type', 'pamm_requests.request_status=pamm_requests_status_type.pamm_requests_status_code', 'left')
		->where('(INSTR(request_comment,"eque")>0 OR INSTR(request_comment,"lose")>0)')
		->where('request_acc_number',$acc_number)	
		->get()->result();
	}

	public function get_rating() 
	{
	// obtain account data for "PAMM account rating" form
		return $this->db->select()
		->from('pamm_accounts')
		->where("active IN ('1','2','5')")
		->order_by('login', 'ASC')	
		->get()->result();
	}

	public function implement_local_activation($sum,$symbol,$id_trader,$acc_number)
	{	
	    // withdraw activation summ from the wallet - dummy until the wallet system will be integrated to the investroom
            $this->db->trans_start();
		    $this->db->query("UPDATE investroom_personal_accounts SET $symbol = $symbol-$sum WHERE id=$id_trader");
            $this->db->trans_complete();

	   $data['active'] = '1';
	   return $this->db->where('login',$acc_number)
	   ->update('pamm_accounts', $data);

	}
	public function implement_request_in($symbol,$data_ri)
	{	
	    // withdraw activation summ from the wallet - dummy until the wallet system will be integrated to the investroom
            $sum       = $data_ri['request_summ_origin'];
	    $id_trader = $data_ri['request_cid'];
            	
            $this->db->trans_start();
	    $this->db->query("UPDATE investroom_personal_accounts SET $symbol = $symbol-$sum WHERE id=$id_trader");
            $this->db->trans_complete();
		return $this->db->set('request_acc_number', $data_ri['request_acc_number'])
        	->set('request_date', 'NOW()', FALSE)
        	->set('request_cid', $data_ri['request_cid'])
		->set('request_comment',$data_ri['request_comment'])
		->set('request_summ',$data_ri['request_summ'])
		->set('request_summ_origin',$data_ri['request_summ_origin'])
		->set('request_quote',$data_ri['request_quote'])
		->set('request_status','0')
		->set('request_type',$data_ri['request_type'])
		->set('request_urgent',$data_ri['request_urgent'])
		->set('request_wallet','N/A')
		->insert('pamm_requests');
	}
	public function implement_request_out($symbol,$data_ri)
	{	
		return $this->db->set('request_acc_number', $data_ri['request_acc_number'])
        	->set('request_date', 'NOW()', FALSE)
        	->set('request_cid', $data_ri['request_cid'])
		->set('request_comment',$data_ri['request_comment'])
		->set('request_summ',$data_ri['request_summ'])
		->set('request_summ_origin',$data_ri['request_summ_origin'])
		->set('request_quote',$data_ri['request_quote'])
		->set('request_status','0')
		->set('request_type',$data_ri['request_type'])
		->set('request_urgent',$data_ri['request_urgent'])
		->set('request_wallet',$symbol)
		->set('request_tp_id',$data_ri['request_tp_id'])
		->set('request_common_id',$data_ri['request_common_id'])
		->insert('pamm_requests');
	}

	public function implement_join_in($symbol,$data_ri)
	{	
	    // withdraw activation summ from the wallet - dummy until the wallet system will be integrated to the investroom
	    $sum = $data_ri['request_summ'];
	    $id_trader = $data_ri['request_cid'];
            $this->db->trans_start();
	    $this->db->query("UPDATE investroom_personal_accounts SET $symbol = $symbol-$sum WHERE id=$id_trader");
            $this->db->trans_complete();
		return $this->db->set('request_acc_number', $data_ri['request_acc_number'])
        	->set('request_date', 'NOW()', FALSE)
        	->set('request_cid', $id_trader)
		->set('request_comment',$data_ri['request_comment'])
		->set('request_summ',$data_ri['request_summ'])
		->set('request_summ_origin',$data_ri['request_summ_origin'])
		->set('request_quote',$data_ri['request_quote'])
		->set('request_status','0')
		->set('request_type',$data_ri['request_type'])
		->set('request_urgent',$data_ri['request_urgent'])
		->set('request_wallet',$symbol)
		->insert('pamm_requests');
	}
	public function implement_join_out($symbol,$data_ri)
	{	
//	    $this->db->query("UPDATE investroom_personal_accounts SET $symbol = $symbol+$sum WHERE id=$id_trader");
		return $this->db->set('request_acc_number', $data_ri['request_acc_number'])
        	->set('request_date', 'NOW()', FALSE)
        	->set('request_cid', $data_ri['request_cid'])
		->set('request_comment',$data_ri['request_comment'])
		->set('request_summ',$data_ri['request_summ'])
		->set('request_summ_origin',$data_ri['request_summ_origin'])
		->set('request_quote',$data_ri['request_quote'])
		->set('request_status','0')
		->set('request_type',$data_ri['request_type'])
		->set('request_urgent',$data_ri['request_urgent'])
        	->set('request_wallet', $symbol)
		->insert('pamm_requests');
	}
	public function implement_close($symbol,$data_ri)
	{	
		return $this->db->set('request_acc_number', $data_ri['request_acc_number'])
        	->set('request_date', 'NOW()', FALSE)
        	->set('request_cid', $data_ri['request_cid'])
		->set('request_comment',$data_ri['request_comment'])
		->set('request_summ',$data_ri['request_summ'])
		->set('request_summ_origin',$data_ri['request_summ_origin'])
		->set('request_quote',$data_ri['request_quote'])
		->set('request_status','0')
		->set('request_urgent',$data_ri['request_urgent'])
		->set('request_type',$data_ri['request_type'])
        	->set('request_wallet', $symbol)
		->insert('pamm_requests');
	}
	public function implement_close_investor($data_ci)
	{	
		return $this->db->set('request_acc_number', $data_ci['request_acc_number'])
        	->set('request_date', 'NOW()', FALSE)
        	->set('request_cid', $data_ci['request_cid'])
		->set('request_comment',$data_ci['request_comment'])
		->set('request_summ',$data_ci['request_summ'])
		->set('request_summ_origin',$data_ci['request_summ_origin'])
		->set('request_quote','1.00')
		->set('request_status','3')
		->set('request_urgent','0')
		->set('request_type',$data_ci['request_type'])
        	->set('request_wallet', 'USD')
		->insert('pamm_requests');
	}
	public function commission_request($data_ci)
	{	
		return $this->db->set('request_acc_number', $data_ci['request_acc_number'])
        	->set('request_date', $data_ci['request_date'])
        	->set('request_cid', $data_ci['request_cid'])
		->set('request_comment',$data_ci['request_comment'])
		->set('request_summ',$data_ci['request_summ'])
		->set('request_summ_origin',$data_ci['request_summ_origin'])
		->set('request_quote','1.00')
		->set('request_status','3')
		->set('request_urgent','1')
		->set('request_type',$data_ci['request_type'])
        	->set('request_wallet', $data_ci['request_wallet'])
        	->set('request_common_id', $data_ci['request_common_id'])
        	->set('request_tp_id', $data_ci['request_tp_id'])
		->insert('pamm_requests');
	}

	public function generate_reinvestment_request($data_ci)
	{	
		return $this->db->set('request_acc_number', $data_ci['request_acc_number'])
        	->set('request_date', 'NOW()', FALSE)
        	->set('request_cid', $data_ci['request_cid'])
		->set('request_comment',$data_ci['request_comment'])
		->set('request_summ',$data_ci['request_summ'])
		->set('request_summ_origin',$data_ci['request_summ_origin'])
		->set('request_quote','1.00')
		->set('request_status','0')
		->set('request_urgent','0')
		->set('request_type',$data_ci['request_type'])
        	->set('request_wallet', 'USD')
		->insert('pamm_requests');
	}

	public function implement_unactive_request_join_in($tid,$acc_number)
	{
		return $this->db->set('pamm_invested_accounts_tid', $tid)
		->set('pamm_invested_accounts_login',$acc_number)
		->set('pamm_invested_accounts_deposit',0)
		->set('pamm_invested_accounts_profit',0)
		->insert('pamm_invested_accounts');
	}

	public function implement_unactive_request_join($tid,$acc_number)
	{
	   $data['pamm_invested_accounts_status'] = '1';
	   return $this->db->where('pamm_invested_accounts_tid',$tid)
			->where('pamm_invested_accounts_login',$acc_number)
			->update('pamm_invested_accounts', $data);
	}
	public function implement_unactive_request_unjoint($tid,$acc_number)
	{
	   $data['pamm_invested_accounts_status'] = '2';
	   return $this->db->where('pamm_invested_accounts_tid',$tid)
			->where('pamm_invested_accounts_login',$acc_number)
			->update('pamm_invested_accounts', $data);
	}
	public function decline_join($tid,$acc_number)
	{
	   return $this->db->where('pamm_invested_accounts_tid',$tid)
			->where('pamm_invested_accounts_login',$acc_number)
			->delete('pamm_invested_accounts');
	}

	public function implement_unactive_request_pending($tid,$acc_number)
	{
	   $data['pamm_invested_accounts_status'] = '4';
	   return $this->db->where('pamm_invested_accounts_tid',$tid)
			->where('pamm_invested_accounts_login',$acc_number)
			->update('pamm_invested_accounts', $data);
	}
	public function implement_unactive_request_rejoin($tid,$acc_number)
	{
	   $data['pamm_invested_accounts_status'] = '1';
	   return $this->db->where('pamm_invested_accounts_tid',$tid)
			->where('pamm_invested_accounts_login',$acc_number)
			->update('pamm_invested_accounts', $data);
	}

	public function get_manage_pamm_accounts($id_trader)
	{	
	// obtain account data for "Manage PAMM accounts" form
	    return $this->db->select()
		->from('pamm_accounts')
		->where('tid', $id_trader)	
		->where("active !='4'")	
		->get()->result();
		
	}

	public function cancel_local_activation($sum,$symbol,$id_trader,$login)
	{	
	// cancel the activation: return activation sum to the wallet and set the account non-active
            $this->db->trans_start();
	    $this->db->query("UPDATE investroom_personal_accounts SET $symbol = $symbol+$sum WHERE id=$id_trader");
            $this->db->trans_complete();
	   $data['active'] = '0';
	   return $this->db->where('login',$login)
	   ->update('pamm_accounts', $data);
	}
	public function charge_wallet($tid,$sum,$symbol)
	{
            $this->db->trans_start();
	    $this->db->query("UPDATE investroom_personal_accounts SET $symbol = $symbol+$sum WHERE id=$tid");
            $this->db->trans_complete();
	}
	public function mainform_get_quotes()
	{
	// obtain quotes for all currencies
		$quotes = $this->get_quotes();
	// process the quotes
		foreach($quotes as $key => &$value)
		{
		 $tmp = (array) $value;
		 $quotes[$key] = $tmp['price'];
		}        
		ksort($quotes);
		$data['quotes'] = $quotes;
		return $quotes;
	}
	public function get_in12_requests()
	{
	// obtain all 'in' requests
	    return $this->db->select('request_cid,request_acc_number,request_summ,request_quote,request_id,request_comment,request_type,request_wallet')
		->from('pamm_requests')
		->where('request_status', '0')
		->where('request_urgent', '0')
		->where("request_type IN ('1','2')")
		->order_by('request_type', 'ASC')	
		->get()->result();

	}
	public function get_other_requests($acc_number)
	{
	// obtain all usual requests
	    return $this->db->select('request_cid,request_acc_number,request_summ,request_summ_origin,request_quote,request_id,request_comment,request_type,request_wallet')
		->from('pamm_requests')
		->where('request_status', '0')
		->where('request_acc_number', $acc_number)
		->order_by('request_type', 'ASC')	
		->get()->result();

	}
	public function get_trader_urgent_requests($acc_number)
	{
	// obtain all urgent requests
	    return $this->db->select('request_cid,request_acc_number,request_summ,request_summ_origin,request_quote,request_id,request_comment,request_type,request_wallet')
		->from('pamm_requests')
		->where('request_status', '0')
		->where('request_type', '7')
		->where('request_urgent', '1')
		->where('request_acc_number', $acc_number)
		->order_by('request_type', 'ASC')	
		->get()->result();

	}
	public function get_investor_urgent_requests($acc_number)
	{
	// obtain all urgent requests
	    return $this->db->select('request_cid,request_date,request_acc_number,request_summ,request_summ_origin,request_quote,request_id,request_comment,request_type,request_wallet,request_common_id,request_tp_id')
		->from('pamm_requests')
		->where('request_status', '0')
		->where('request_type', '8')
		->where('request_urgent', '1')
		->where('request_acc_number', $acc_number)
		->order_by('request_date', 'ASC')	
		->get()->result();

	}
	function open_account($data)
	{
	// create the account record in the local database
		if(count($data)>0){
			$this->db->insert('pamm_accounts',$data);
		}
	}
	function open_account_correction($data)
	{
	// create the account record in the local database
			$this->db->insert('pamm_accounts_correction',$data);
	}

	function open_deposit($data)
	{
	// create the deposit record in the local database
			$this->db->insert('pamm_summaries',$data);
	}
	function change_statement($cid,$acc_number,$sum,$role,$order_id)
	{
	// change the client statement (id or account number are the entities)

		return $this->db->set('pamm_clients_stat_date', 'NOW()', FALSE)
        	->set('pamm_clients_stat_cid', $cid)
		->set('pamm_clients_stat_acc_number',$acc_number)
		->set('pamm_clients_stat_sum',$sum)
		->set('pamm_clients_stat_role',$role)
		->set('pamm_clients_order_number',trim($order_id))
		->insert('pamm_clients_statement');
	}
	function charge_deposit($acc_number,$sum)
	{
	// charge the existent deposit record in the local database in case of separate activation
	    $this->db->query("UPDATE pamm_summaries SET deposit = deposit+$sum WHERE acc_number=$acc_number");
	}
	function get_requests($acc_number)
	{
	// get requests for the certain account
	    return $this->db->select('pamm_requests.*, pamm_requests_status_type.pamm_requests_status_name AS status_word')
		->from('pamm_requests')
		->join('pamm_requests_status_type', 'pamm_requests.request_status=pamm_requests_status_type.pamm_requests_status_code', 'left')
		->where('request_acc_number', $acc_number)	
		->order_by('request_date', 'ASC')
		->order_by('request_comment', 'DESC')
		->get()->result();
	}
	function get_invested_accounts($tid)
	{
	// get list of invested accounts
	   return $this->db->select()
		  ->from('pamm_accounts') 
		  ->where("login IN (select pamm_invested_accounts_login FROM pamm_invested_accounts WHERE pamm_invested_accounts_tid=$tid)")
		  ->where("pamm_accounts.active !='4'")	
		  ->get()->result();
	}
	function get_invested_accounts_status($tid,$acc_number)
	{
	// get statuses of invested accounts
	   return $this->db->select('pamm_invested_accounts_status')
		  ->from('pamm_invested_accounts') 
		  ->where('pamm_invested_accounts_login',$acc_number)
		  ->where('pamm_invested_accounts_tid',$tid)
		  ->get()->result();
	}
	function log_open_account($reason)
	{
	// logging account operations
		return $this->db->set('reason', $reason)
		->set('log_datetime', 'NOW()', FALSE)
		->insert('pamm_log');
	}
	function history_write($history)
	{
	// logging deposit operations
		return $this->db->set('id','')
			        ->set('login',$history['login'])
			        ->set('comment',$history['comment'])
			        ->set('ticket',$history['ticket'])
			        ->set('sum',$history['sum'])
			        ->set('tid',$history['tid'])
				->set('date', 'NOW()', FALSE)
			->insert('pamm_history');
	}
	function change_request_status($id,$status)
	{
        	    $this->db->query("UPDATE pamm_requests SET request_status='$status' WHERE request_id=$id");
	}
	function tp_prev_res_date($acc_number)
	{
	// logging trade period results	
		return $this->db->select_max('pamm_clients_stat_date')
		  	->from('pamm_clients_statement') 
		  	->where("pamm_clients_stat_acc_number",$acc_number)
		  	->where("pamm_clients_stat_role",'PL')
			->get()->result();
	}
	function get_total_u($acc_number)
	{
	// obtain trader total for trade period writing
		return $this->db->select_sum('pamm_clients_stat_sum')
		  	->from('pamm_clients_statement') 
		  	->where("pamm_clients_stat_acc_number",$acc_number)
		  	->where("pamm_clients_stat_role IN ('U','PLU')")
			->get()->result();
	}
	function get_total_i($acc_number)
	{
	// obtain investor total for trade period writing
		return $this->db->select_sum('pamm_clients_stat_sum')
		  	->from('pamm_clients_statement') 
		  	->where("pamm_clients_stat_acc_number",$acc_number)
		  	->where("pamm_clients_stat_role IN ('I','PLI')")
			->get()->result();
	}

	function tp_results_insert($acc_number,$total,$earn,$u_total,$i_total,$profitable)
	{	
	    // store trade period results

		return $this->db->set('pamm_tp_id', '')
		->set('pamm_tp_account', $acc_number)
		->set('pamm_tp_total', $total)
		->set('pamm_tp_profitloss', $earn)
        	->set('pamm_tp_timestamp', 'NOW()', FALSE)
		->set('pamm_tp_trader',$u_total)
		->set('pamm_tp_investor',$i_total)
		->set('pamm_tp_profitable',$profitable)
		->insert('pamm_tp_results');
	}
	function empty_tp_result($acc_number)
	{	
	    // insert empty trade period

		return $this->db->set('pamm_tp_id', '')
		->set('pamm_tp_account', $acc_number)
		->set('pamm_tp_total', 0)
		->set('pamm_tp_profitloss', 0)
        	->set('pamm_tp_timestamp', 'NOW()', FALSE)
		->set('pamm_tp_trader', 0)
		->set('pamm_tp_investor',0)
		->insert('pamm_tp_results');
	}
	function activation_tp_result($sum,$acc_number)
	{	
	    // insert trade period  with activation fee only

		return $this->db->set('pamm_tp_id', '')
		->set('pamm_tp_account', $acc_number)
		->set('pamm_tp_total', $sum)
		->set('pamm_tp_profitloss', 0)
        	->set('pamm_tp_timestamp', 'NOW()', FALSE)
		->set('pamm_tp_trader', $sum)
		->set('pamm_tp_investor',0)
		->insert('pamm_tp_results');
	}

	function get_client_statement($acc_number,$cid)
	{
	// get account history
	   return $this->db->select_sum('pamm_clients_stat_sum')
		  ->from('pamm_clients_statement') 
		  ->where("pamm_clients_stat_acc_number",$acc_number)
		  ->where("pamm_clients_stat_cid",$cid)
		  ->get()->result();
	}

	public function store_mt_errors($mt_errors)
	{	
	    // store mt server erros in separate table for the further use

		return $this->db->set('pamm_mt_errors_id', '')
        	->set('pamm_mt_errors_date', 'NOW()', FALSE)
		->set('pamm_mt_errors_account',$mt_errors['pamm_mt_errors_account'])
		->set('pamm_mt_errors_code',$mt_errors['pamm_mt_errors_code'])
		->insert('pamm_mt_errors');
	}
	function oh($acc_number,$date)
	{
	// get account history
	   return $this->db->select_sum('pamm_clients_stat_sum')
		  ->from('pamm_clients_statement') 
		  ->where("pamm_clients_stat_acc_number",$acc_number)
		  ->where("pamm_clients_stat_role IN ('I','U','PLI','PLU')")
		  ->where("pamm_clients_stat_date > ", $date)
		  ->get()->result();
	}
	function ohi($acc_number,$cid,$date)
	{
	// get certain investor history
	   return $this->db->select_sum('pamm_clients_stat_sum')
		  ->from('pamm_clients_statement') 
		  ->where("pamm_clients_stat_acc_number",$acc_number)
		  ->where("pamm_clients_stat_cid",$cid)
		  ->get()->result();
	}

	function ohwt($acc_number,$date)
	{
	// get account history
	   return $this->db->select_sum('pamm_clients_stat_sum')
		  ->from('pamm_clients_statement') 
		  ->where("pamm_clients_stat_acc_number",$acc_number)
		  ->where("pamm_clients_stat_role IN ('I','PLI')")
		  ->where("pamm_clients_stat_date > ", $date)
		  ->get()->result();
	}
	function obtain_history_only_trader($acc_number)
	{
	// get account history
	   return $this->db->select_sum('pamm_clients_stat_sum')
		  ->from('pamm_clients_statement') 
		  ->where("pamm_clients_stat_acc_number",$acc_number)
		  ->where("pamm_clients_stat_role IN ('U','PLU')")
		  ->get()->result();
	}
	function oph($acc_number,$date_start,$date)
	{
	// get account history
	   $dateRange = "date BETWEEN '$date_start' AND '$date'";
	   $letters=array('R','I');
	   return $this->db->select()
		  ->from('pamm_history') 
		  ->where("login",$acc_number)
		  ->where("substring(ticket,1,7) !=",'9999999')
		  ->where_in("substring(comment,1,1)",$letters)
		  ->where($dateRange, NULL, FALSE)
		  ->get()->result();
	}
	function oph1($acc_number)
	{
	// get trade period profitloss
	   return $this->db->select('pamm_tp_total')
		  ->from('pamm_tp_results') 
		  ->where("pamm_tp_account",$acc_number)
		  ->order_by('pamm_tp_timestamp', 'DESC')
		  ->limit(1)
		  ->get()->result();
	}
	function oph2($acc_number)
	{
	// get trade period profitloss
	   return $this->db->select('pamm_tp_total')
		  ->from('pamm_tp_results') 
		  ->where("pamm_tp_account",$acc_number)
		  ->order_by('pamm_tp_timestamp', 'DESC')
		  ->limit(2,1)
		  ->get()->result();
	}

	function odh($acc_number,$date)
	{
	// get account history
	   return $this->db->select_sum('pamm_clients_stat_sum')
		  ->select('pamm_clients_stat_cid')
		  ->from('pamm_clients_statement') 
		  ->where("pamm_clients_stat_acc_number",$acc_number)
		  ->where("pamm_clients_stat_date > ", $date)
		  ->where("pamm_clients_stat_role IN ('I','PLI')")
		  ->having("sum(pamm_clients_stat_sum) > 0")
		  ->group_by("pamm_clients_stat_cid")
		  ->get()->result();
	}
	function obtain_detailed_history_became_zero($acc_number)
	{
	// get account history
	   return $this->db->select_sum('pamm_clients_stat_sum')
		  ->select('pamm_clients_stat_cid')
		  ->from('pamm_clients_statement') 
		  ->where("pamm_clients_stat_acc_number",$acc_number)
		  ->where("pamm_clients_stat_role IN ('I','PLI')")
		  ->group_by("pamm_clients_stat_cid")
		  ->get()->result();
	}
	function obtain_common_history($acc_number)
	{
	// get account common history
	   return $this->db->select_sum('pamm_clients_stat_sum')
		  ->select('pamm_clients_stat_cid')
		  ->from('pamm_clients_statement') 
		  ->where("pamm_clients_stat_acc_number",$acc_number)
		  ->get()->result();
	}
	function obtain_trader_history($acc_number)
	{
	// get account history
	   return $this->db->select_sum('pamm_clients_stat_sum')
		  ->from('pamm_clients_statement') 
		  ->where("pamm_clients_stat_role IN ('U','PLU')")
		  ->where("pamm_clients_stat_acc_number",$acc_number)
		  ->get()->result();
	}
	function obtain_invest_history($acc_number)
	{
	// get account history
	   return $this->db->select_sum('pamm_clients_stat_sum')
		  ->from('pamm_clients_statement') 
		  ->where("pamm_clients_stat_role IN ('I','PLI')")
		  ->where("pamm_clients_stat_acc_number",$acc_number)
		  ->get()->result();
	}
	function obtain_investor_history($acc_number,$cid)
	{
	// get CERTAIN INVESTOR history
	   return $this->db->select_sum('pamm_clients_stat_sum')
		  ->from('pamm_clients_statement') 
		  ->where("pamm_clients_stat_role IN ('I','PLI')")
		  ->where("pamm_clients_stat_cid",$cid)
		  ->where("pamm_clients_stat_acc_number",$acc_number)
		  ->get()->result();
	}

	function obtain_start_balance($acc_number)
	{
	// get start balance
	   return $this->db->select('pamm_tp_profitloss')
		  ->from('pamm_tp_results') 
		  ->where('pamm_tp_account',$acc_number)
		  ->limit(1)
		  ->order_by('pamm_tp_id', 'DESC')	
		  ->get()->result();
	}
	function obtain_start_balance_prev($acc_number)
	{
	// get start balance
	   return $this->db->select('pamm_tp_total')
		  ->from('pamm_tp_results') 
		  ->where('pamm_tp_account',$acc_number)
		  ->limit(1)
		  ->order_by('pamm_tp_id', 'DESC')	
		  ->get()->result();
	}

	function get_percentage($acc_number)
	{
	// get percentage
	   return $this->db->select('distr_upr,distr_inv')
		  ->from('pamm_accounts') 
		  ->where('login',$acc_number)
		  ->get()->result();
	}
	function obtain_trader_id($acc_number)
	{
	// get percentage
	   return $this->db->select('tid')
		  ->from('pamm_accounts') 
		  ->where('login',$acc_number)
		  ->get()->result();
	}
	function change_total_statement($action,$acc_number,$tid,$summ)	
	{
	  // get client's id
	 if ($tid == 0)
		{
		$tidObject = $this->obtain_trader_id($acc_number);
		$tid = $tidObject[0]->tid;
		}
	 // calculate trade period number
   	 $max_tp_id = $this->db->select_max('pamm_ts_tp_id')
		      ->from('pamm_total_statement') 
		      ->where('pamm_ts_acc_number',$acc_number)
		      ->get()->result();
	 if ($max_tp_id[0]->pamm_ts_tp_id == 0)
		$tp_id = 1;
	 else 
		$tp_id = $max_tp_id[0]->pamm_ts_tp_id +1;
	}
	function get_divide_accounts()
	{
	   return $this->db->select('login')
		  ->from('pamm_accounts')
		  ->where("active IN ('1','3','5')")
		  ->get()->result();
	}
	function get_all_accounts()
	{
	   return $this->db->select('login')
		  ->from('pamm_accounts')
		  ->get()->result();
	}
	function get_tp_accounts()
	{
	   return $this->db->select('login,of_t_p,date_reg')
		  ->from('pamm_accounts')
		  ->where("active IN ('1','3')")
		  ->get()->result();
	}

	function get_test_divide_accounts()
	{
	   return $this->db->select('login')
		  ->from('pamm_accounts_test') 
		  ->get()->result();
	}

	function obtain_acc_total($acc_number)
	{
	// get percentage
	   return $this->db->select('pamm_tp_total')
		  ->from('pamm_tp_results') 
		  ->where('pamm_tp_account',$acc_number)
		  ->order_by('pamm_tp_timestamp', 'DESC')
		  ->limit(1)
		  ->get()->result();
	}
	function obtain_acc_profitloss($acc_number)
	{
	// get percentage
	   return $this->db->select('pamm_tp_profitloss')
		  ->from('pamm_tp_results') 
		  ->where('pamm_tp_account',$acc_number)
		  ->order_by('pamm_tp_timestamp', 'DESC')
		  ->limit(1)
		  ->get()->result();
	}

	function change_request($summ,$summ_origin,$quote,$comm,$id)
	{
	// change request in case of rest withdraw
		$data['request_summ'] =$summ;
      		$data['request_summ_origin'] =$summ_origin;
		$data['request_comment'] =$comm;
		$data['request_quote'] =$quote;
	   return $this->db->update('pamm_requests', $data, "request_id = $id");
	}
	function change_request_comment($comm,$id)
	{
	// change request in case of decline inside engine
		$data['request_comment'] =$comm;
	   return $this->db->update('pamm_requests', $data, "request_id = $id");
	}
	function change_request_summ($summ,$origin,$quote,$id)
	{
	// change request in case of decline inside engine
		$data['request_summ'] =$summ;
		$data['request_summ_origin'] =$origin;
		$data['request_quote'] =$quote;
	   return $this->db->update('pamm_requests', $data, "request_id = $id");
	}
        function obtain_requests_out($acc_number,$id)
	{
		return $this->db->select_sum('request_summ')
		->select('count(*) AS request_count')
		->from('pamm_requests')
		  ->where('request_acc_number',$acc_number)
		  ->where('request_cid',$id)
		  ->where('request_status','0')
		->get()->result();
	}
	function obtain_min_withdraw($acc_number)
	{
		return $this->db->select('min_withdraw')
		->from('pamm_accounts')
	        ->where('login',$acc_number)
		->get()->result();
	}
	function get_penalty($acc_number)
	{
		return $this->db->select('penalty')
		->from('pamm_accounts')
	        ->where('login',$acc_number)
		->get()->result();
	}
	function check_join($acc_number,$cid)
	{
		return $this->db->select('pamm_invested_accounts_login,pamm_invested_accounts_status')
		->from('pamm_invested_accounts')
	        ->where('pamm_invested_accounts_login',$acc_number)
	        ->where('pamm_invested_accounts_tid',$cid)
		->get()->result();
	}
	function request_correct_summ($id,$summ)
	{
	   $data['request_summ'] = $summ;
	   return $this->db->where('request_id',$id)
	   ->update('pamm_requests', $data);
	}
	function get_moment_quote($wallet)
	{
		return $this->db->select('price')
		->from('investroom_system_quotes')
	        ->where('substring(symbol,4,3)',$wallet)
	        ->where('substring(symbol,1,3)','USD')
		->order_by('id', 'DESC')	
		->limit(1)
		->get()->result();
	}
	function get_moment_quote_gbp($wallet)
	{
		return $this->db->select('price')
		->from('investroom_system_quotes')
	        ->where('substring(symbol,1,3)',$wallet)
	        ->where('substring(symbol,4,3)','USD')
		->order_by('id', 'DESC')	
		->limit(1)
		->get()->result();
	}
	function closing_account($acc_number)
	{
	   $data['active'] = '5';
	   return $this->db->where('login',$acc_number)
	   ->update('pamm_accounts', $data);
	}
	function close_account($acc_number)
	{
	   $data['active'] = '2';
	   $data['date_close'] = date('y-m-d H:i:s');
	   return $this->db->where('login',$acc_number)
	   ->update('pamm_accounts', $data);
	    
	}
	function obtain_deposit($acc_number)
	{
		return $this->db->select_sum('pamm_clients_stat_sum')
		->from('pamm_clients_statement')
	        ->where('pamm_clients_stat_acc_number',$acc_number)
		->where("pamm_clients_stat_role IN ('I','U','PLI','PLU')")
		->get()->result();
	}
	function obtain_own_means($acc_number)
	{
		return $this->db->select_sum('pamm_clients_stat_sum')
		->from('pamm_clients_statement')
	        ->where('pamm_clients_stat_acc_number',$acc_number)
		->where("pamm_clients_stat_role IN ('U')")
		->get()->result();
	}
	function obtain_inv_means($acc_number)
	{
		return $this->db->select_sum('pamm_clients_stat_sum')
		->from('pamm_clients_statement')
	        ->where('pamm_clients_stat_acc_number',$acc_number)
		->where("pamm_clients_stat_role IN ('I')")
		->get()->result();
	}

	function obtain_own_profit($acc_number)
	{
		return $this->db->select_sum('pamm_clients_stat_sum')
		->from('pamm_clients_statement')
	        ->where('pamm_clients_stat_acc_number',$acc_number)
		->where("pamm_clients_stat_role IN ('PLU')")
		->get()->result();
	}


	function obtain_sum_means($acc_number)
	{
		return $this->db->select_sum('pamm_clients_stat_sum')
		->from('pamm_clients_statement')
	        ->where('pamm_clients_stat_acc_number',$acc_number)
		->where("pamm_clients_stat_role IN ('I','PLI','U','PLU')")
		->get()->result();
	}
	function obtain_sum_means_week_ago($acc_number)
	{
		return $this->db->select_sum('pamm_clients_stat_sum')
		->from('pamm_clients_statement')
	        ->where('pamm_clients_stat_acc_number',$acc_number)
		->where("pamm_clients_stat_role IN ('I','PLI','U','PLU')")
		->where("pamm_clients_stat_date < date_sub(curdate(),INTERVAL 7 DAY)")
		->get()->result();
	}
	function obtain_sum_means_month_ago($acc_number)
	{
		return $this->db->select_sum('pamm_clients_stat_sum')
		->from('pamm_clients_statement')
	        ->where('pamm_clients_stat_acc_number',$acc_number)
		->where("pamm_clients_stat_role IN ('I','PLI','U','PLU')")
		->where("pamm_clients_stat_date < date_sub(curdate(),INTERVAL 30 DAY)")
		->get()->result();
	}

	function obtain_acc_means_for_inv($acc_number)
	{
		return $this->db->select_sum('pamm_clients_stat_sum')
		->from('pamm_clients_statement')
	        ->where('pamm_clients_stat_acc_number',$acc_number)
		->where("pamm_clients_stat_role IN ('U','PLU')")
		->get()->result();
	}
	function obtain_own_means_for_inv($acc_number,$cid)
	{
		return $this->db->select_sum('pamm_clients_stat_sum')
		->from('pamm_clients_statement')
	        ->where('pamm_clients_stat_acc_number',$acc_number)
		->where("pamm_clients_stat_role IN ('I')")
	        ->where('pamm_clients_stat_cid',$cid)
		->get()->result();
	}
	function obtain_all_means_for_inv($acc_number,$cid)
	{
		return $this->db->select_sum('pamm_clients_stat_sum')
		->from('pamm_clients_statement')
	        ->where('pamm_clients_stat_acc_number',$acc_number)
		->where("pamm_clients_stat_role IN ('I','PLI')")
	        ->where('pamm_clients_stat_cid',$cid)
		->get()->result();
	}
	function obtain_timestart_for_tra($acc_number,$cid)
	{
		return $this->db->select('pamm_clients_stat_date')
		->from('pamm_clients_statement')
	        ->where('pamm_clients_stat_acc_number',$acc_number)
		->where("pamm_clients_stat_role IN ('U')")
	        ->where('pamm_clients_stat_cid',$cid)
	        ->order_by('pamm_clients_stat_date', 'ASC')
	        ->limit(1)
                ->get()->result();
	}
	function obtain_timeend($acc_number)
	{
		return $this->db->select('date_close')
		->from('pamm_accounts')
	        ->where('login',$acc_number)
                ->get()->result();
	}

	function obtain_timestart_for_inv($acc_number,$cid)
	{
		return $this->db->select('request_date')
		->from('pamm_requests')
	        ->where('request_acc_number',$acc_number)
	        ->where('request_comment','Join request to the account')
	        ->where('request_cid',$cid)
	        ->order_by('request_date', 'DESC')
	        ->limit(1)
                ->get()->result();
	}
	function obtain_inv_profit($acc_number)
	{
		return $this->db->select_sum('pamm_clients_stat_sum')
		->from('pamm_clients_statement')
	        ->where('pamm_clients_stat_acc_number',$acc_number)
		->where("pamm_clients_stat_role IN ('PLI')")
		->get()->result();
	}
	function obtain_own_profit_for_inv($acc_number,$cid)
	{
		return $this->db->select_sum('pamm_clients_stat_sum')
		->from('pamm_clients_statement')
	        ->where('pamm_clients_stat_acc_number',$acc_number)
	        ->where('pamm_clients_stat_cid',$cid)
		->where("pamm_clients_stat_role IN ('PLI')")
		->get()->result();
	}

	function obtain_inv_number($acc_number)
	{

		return $this->db->select('pamm_clients_stat_sum,pamm_clients_stat_cid')
		->from('pamm_clients_statement')
	        ->where('pamm_clients_stat_acc_number',$acc_number)
		->where("pamm_clients_stat_role IN ('I','PLI')")
		->having("sum(pamm_clients_stat_sum) > 0")
	        ->group_by("pamm_clients_stat_cid")
		->get()->result();
	}
	function obtain_inv_number_week_ago($acc_number)
	{
		return $this->db->select('pamm_clients_stat_sum,pamm_clients_stat_cid')
		->from('pamm_clients_statement')
	        ->where('pamm_clients_stat_acc_number',$acc_number)
		->where("pamm_clients_stat_role IN ('I')")
	        ->where("pamm_clients_stat_date < date_sub(curdate(),INTERVAL 7 DAY)")
		->having("sum(pamm_clients_stat_sum) > 0")
	        ->group_by("pamm_clients_stat_cid")
		->get()->result();
	}
	function obtain_inv_number_month_ago($acc_number)
	{
		return $this->db->select('pamm_clients_stat_sum,pamm_clients_stat_cid')
		->from('pamm_clients_statement')
	        ->where('pamm_clients_stat_acc_number',$acc_number)
		->where("pamm_clients_stat_role IN ('I')")
		->where("pamm_clients_stat_date < date_sub(curdate(),INTERVAL 30 DAY)")
		->having("sum(pamm_clients_stat_sum) > 0")
	        ->group_by("pamm_clients_stat_cid")
		->get()->result();
	}

	function count_requests_in($acc_number)
	{
		return $this->db->from('pamm_requests')
	        ->where('request_acc_number',$acc_number)
		->where('request_status', '0')
		->where('request_urgent', '0')
		->where("request_type IN ('5','6')")
		->count_all_results();
	}
	function count_requests_out($acc_number)
	{
		return $this->db->from('pamm_requests')
	        ->where('request_acc_number',$acc_number)
		->where('request_status', '0')
		->where('request_urgent', '0')
		->where("request_type IN ('7','8')")
		->count_all_results();
	}
	function get_previous_total($acc_number)
	{
		return $this->db->select('pamm_tp_total,pamm_tp_id')
		->from('pamm_tp_results')
	        ->where('pamm_tp_account',$acc_number)
	        ->order_by('pamm_tp_id', 'DESC')
	        ->limit(1)
		->get()->result();
	}
	function correct_previous_total($acc_number,$id,$pamm_tp_total)
	{
	   $data['pamm_tp_total']  = $pamm_tp_total;
	   return $this->db->where('pamm_tp_account',$acc_number)
	   		   ->where('pamm_tp_id',$id)
	   		   ->update('pamm_tp_results', $data);


	}
	function correct_previous_trader($acc_number,$id,$pamm_tp_trader)
	{
	   $data['pamm_tp_trader']  = $pamm_tp_trader;
	   return $this->db->where('pamm_tp_account',$acc_number)
	   		   ->where('pamm_tp_id',$id)
	   		   ->update('pamm_tp_results', $data);


	}
	function correct_previous_invest($acc_number,$id,$pamm_tp_investor)
	{
	   $data['pamm_tp_investor']  = $pamm_tp_investor;
	   return $this->db->where('pamm_tp_account',$acc_number)
	   		   ->where('pamm_tp_id',$id)
	   		   ->update('pamm_tp_results', $data);


	}
	function correct_previous_profitable($acc_number,$id,$pamm_tp_profitable)
	{
	   $data['pamm_tp_profitable']  = $pamm_tp_profitable;
	   return $this->db->where('pamm_tp_account',$acc_number)
	   		   ->where('pamm_tp_id',$id)
	   		   ->update('pamm_tp_results', $data);


	}
	function zero_profitloss($acc_number,$id)
	{
	   $data['pamm_tp_profitloss']  = 0;
	   return $this->db->where('pamm_tp_account',$acc_number)
	   		   ->where('pamm_tp_id',$id)
	   		   ->update('pamm_tp_results', $data);


	}
	function get_previous_total_for_ctpp($acc_number)
	{
		return $this->db->select('pamm_tp_total')
		->from('pamm_tp_results')
	        ->where('pamm_tp_account',$acc_number)
	        ->order_by('pamm_tp_id', 'DESC')
		->limit(1,1)
		->get()->result();
	}

	function get_aggr($acc_number)
	{
		return $this->db->select('pamm_tp_profitable,pamm_tp_profitloss')
		->from('pamm_tp_results')
	        ->where('pamm_tp_account',$acc_number)
	        ->order_by('pamm_tp_id', 'DESC')
		->get()->result();
	}
	function get_urgent_total($acc_number)
	{
		return $this->db->select_sum('request_summ')
		->from('pamm_requests')
	        ->where('request_acc_number',$acc_number)
		->where('request_status', '0')
		->where('request_urgent', '1')
		->get()->result();
	}
	function obtain_dynamic($acc_number)
	{
		return $this->db->select('balance,day_profit')
		->from('pamm_accounts')
	        ->where('login',$acc_number)
		->get()->result();
	}
	function obtain_down_from_db($acc_number)
	{
		return $this->db->select('down')
		->from('pamm_accounts')
	        ->where('login',$acc_number)
		->get()->result();
	}
	function store_dynamic($acc_number,$balance,$day_profit,$down)
	{
	   $data['balance'] = $balance;
	   $data['day_profit'] = $day_profit;
	   $data['down']       = $down;
	   return $this->db->where('login',$acc_number)
	   ->update('pamm_accounts', $data);
	}
	function write_debt($acc_number,$debt)
	{
        	    $this->db->query("UPDATE pamm_accounts SET debt=debt+'$debt' WHERE login='$acc_number'");
	}
	function correct_debt($acc_number,$debt)
	{
        	    $this->db->query("UPDATE pamm_accounts SET debt=debt+'$debt' WHERE login='$acc_number'");
	}
	function correct_debt_for_trade($acc_number,$debt_for_trade)
	{
        	    $this->db->query("UPDATE pamm_accounts SET debt_for_trade=debt_for_trade+'$debt_for_trade' WHERE login='$acc_number'");
	}

	function write_debt_zero($acc_number)
	{
        	    $this->db->query("UPDATE pamm_accounts SET debt=0 WHERE login='$acc_number'");
	}
	function write_debt_inout($acc_number,$debt_inout)
	{
        	    $this->db->query("UPDATE pamm_accounts SET debt_inout=debt_inout+'$debt_inout' WHERE login='$acc_number'");
	}
	function write_debt_inout_stopout($acc_number,$debt_inout_stopout)
	{
        	    $this->db->query("UPDATE pamm_accounts SET debt_inout_stopout=debt_inout_stopout+'$debt_inout_stopout' WHERE login='$acc_number'");
	}
	function write_balance_before_divide($acc_number,$earn)
	{
        	    $this->db->query("UPDATE pamm_accounts SET earn='$earn' WHERE login='$acc_number'");
	}
	function write_fsb($acc_number,$fsb)
	{
        	    $this->db->query("UPDATE pamm_accounts SET fsb='$fsb' WHERE login='$acc_number'");
	}
	function write_debt_inout_zero($acc_number)
	{
        	    $this->db->query("UPDATE pamm_accounts SET debt_inout=0 WHERE login='$acc_number'");
	}
	function write_debt_inout_stopout_zero($acc_number)
	{
        	    $this->db->query("UPDATE pamm_accounts SET debt_inout_stopout=0 WHERE login='$acc_number'");
	}
	function write_debt_urgent_out($acc_number,$debt_urgent_out)
	{
        	    $this->db->query("UPDATE pamm_accounts SET debt_urgent_out=debt_urgent_out+'$debt_urgent_out' WHERE login='$acc_number'");
	}
	function write_debt_urgent_out_zero($acc_number)
	{
        	    $this->db->query("UPDATE pamm_accounts SET debt_urgent_out=0 WHERE login='$acc_number'");
	}
	function write_balance_before_divide_zero($acc_number)
	{
        	    $this->db->query("UPDATE pamm_accounts SET earn=0 WHERE login='$acc_number'");
	}

	function obtain_debt($acc_number)
	{
		return $this->db->select('debt')
		->from('pamm_accounts')
	        ->where('login',$acc_number)
		->get()->result();
	}
	function obtain_debt_for_trade($acc_number)
	{
		return $this->db->select('debt_for_trade')
		->from('pamm_accounts')
	        ->where('login',$acc_number)
		->get()->result();
	}
	function write_debt_for_trade($acc_number,$debt_for_trade)
	{
        	    $this->db->query("UPDATE pamm_accounts SET debt_for_trade='$debt_for_trade' WHERE login='$acc_number'");
	}

	function write_debt_for_trade_zero($acc_number)
	{
        	    $this->db->query("UPDATE pamm_accounts SET debt_for_trade=0 WHERE login='$acc_number'");
	}

	function obtain_debt_inout($acc_number)
	{
		return $this->db->select('debt_inout')
		->from('pamm_accounts')
	        ->where('login',$acc_number)
		->get()->result();
	}
	function obtain_balance_before_divide($acc_number)
	{
		return $this->db->select('earn')
		->from('pamm_accounts')
	        ->where('login',$acc_number)
		->get()->result();
	}

	function obtain_debt_inout_stopout($acc_number)
	{
		return $this->db->select('debt_inout_stopout')
		->from('pamm_accounts')
	        ->where('login',$acc_number)
		->get()->result();
	}
	function obtain_debt_urgent_out_for_restrict($acc_number,$cid,$tp_id)
	{
		return $this->db->select_sum('request_summ')
		->from('pamm_requests')
	        ->where('request_acc_number',$acc_number)
	        ->where('request_cid',$cid)
	        ->where('request_tp_id',$tp_id)
	        ->where('request_status','3')
	        ->where('request_urgent','1')
	        ->where('request_type','8')
		->get()->result();
	}

	function obtain_debt_urgent_out($acc_number)
	{
		return $this->db->select('debt_urgent_out')
		->from('pamm_accounts')
	        ->where('login',$acc_number)
		->get()->result();

	}

	function obtain_fsb($acc_number)
	{
		return $this->db->select('fsb')
		->from('pamm_accounts')
	        ->where('login',$acc_number)
		->get()->result();
	}


	function obtain_profitable($acc_number)
	{
		return $this->db->select('pamm_tp_profitable')
		->from('pamm_tp_results')
	        ->where('pamm_tp_account',$acc_number)
	        ->order_by('pamm_tp_id', 'DESC')
	        ->limit(1)
		->get()->result();
	}

	function get_graph_data($acc_number)
	{
		return $this->db->select('pamm_tp_total')
		->from('pamm_tp_results')
	        ->where('pamm_tp_account',$acc_number)
	        ->order_by('pamm_tp_id', 'ASC')
		->get()->result();
	}

	function return_join_paid($symbol,$id_trader,$sum)
	{
            $this->db->trans_start();
	    $this->db->query("UPDATE investroom_personal_accounts SET $symbol = $symbol+$sum WHERE id=$id_trader");
            $this->db->trans_complete();
	}
	function obtain_single($acc_number,$tid)
	{
	// get single pamm_invested_accounts record
	   return $this->db->select('pamm_invested_accounts_status')
		  ->from('pamm_invested_accounts') 
		  ->where("pamm_invested_accounts_login",$acc_number)
		  ->where("pamm_invested_accounts_tid",$tid)
		  ->get()->result();
	}
	function obtain_offer($acc_number)
	{
	// get offer for stat page
	   return $this->db->select()
		  ->from('pamm_accounts') 
		  ->where("login",$acc_number)
		  ->get()->result();
	}
	function get_duration($acc_number)
	{
	// get duration for generate first trade period
	   return $this->db->select('of_t_p')
		  ->from('pamm_accounts') 
		  ->where("login",$acc_number)
		  ->get()->result();
	}
	function obtain_month_profitable($acc_number)
	{
	// obtain month profitable for stat page
	   return $this->db->select('AVG(pamm_tp_profitable) AS ptp,monthname(pamm_tp_timestamp) AS month, year(pamm_tp_timestamp) AS year')
		  ->from('pamm_tp_results') 
		  ->where("pamm_tp_account",$acc_number)
		  ->group_by('monthname(pamm_tp_timestamp)')
		  ->order_by('pamm_tp_timestamp','ASC')
		  ->get()->result();
	}
	function obtain_year_profitable($acc_number)
	{
	// obtain year profitable for stat page
	   return $this->db->select('AVG(pamm_tp_profitable) AS ptp,year(pamm_tp_timestamp) AS year')
		  ->from('pamm_tp_results') 
		  ->where("pamm_tp_account",$acc_number)
		  ->group_by('year(pamm_tp_timestamp)')
		  ->get()->result();
	}
	function obtain_invested_means($acc_number)
	{
	// obtain invested means for stat page
	   return $this->db->select_sum('pamm_clients_stat_sum')
		  ->from('pamm_clients_statement') 
		  ->where("pamm_clients_stat_acc_number",$acc_number)
		  ->where("pamm_clients_stat_role IN ('I','U')")
		  ->where("pamm_clients_stat_sum > 0")
		  ->get()->result();
	}
	function obtain_invested_means_week_ago($acc_number)
	{
	// obtain invested means week ago for stat page
	   return $this->db->select_sum('pamm_clients_stat_sum')
		  ->from('pamm_clients_statement') 
		  ->where("pamm_clients_stat_acc_number",$acc_number)
		  ->where("pamm_clients_stat_role IN ('I','U')")
		  ->where("pamm_clients_stat_sum > 0")
		  ->where("pamm_clients_stat_date < date_sub(curdate(),INTERVAL 7 DAY)")
		  ->get()->result();
	}

	function obtain_invested_means_month_ago($acc_number)
	{
	// obtain invested means month ago for stat page
	   return $this->db->select_sum('pamm_clients_stat_sum')
		  ->from('pamm_clients_statement') 
		  ->where("pamm_clients_stat_acc_number",$acc_number)
		  ->where("pamm_clients_stat_role IN ('I','U')")
		  ->where("pamm_clients_stat_sum > 0")
		  ->where("pamm_clients_stat_date < date_sub(curdate(),INTERVAL 30 DAY)")
		  ->get()->result();
	}
	function obtain_invested_means_first_period_week($acc_number)
	{
	// is there first week or not
	   return $this->db->select()
		  ->from('pamm_clients_statement') 
		  ->where("pamm_clients_stat_acc_number",$acc_number)
		  ->where("pamm_clients_stat_date < date_sub(curdate(),INTERVAL 7 DAY)")
		  ->get()->result();
	}

	function obtain_invested_means_first_period_month($acc_number)
	{
	// is there first month or not
	   return $this->db->select()
		  ->from('pamm_clients_statement') 
		  ->where("pamm_clients_stat_acc_number",$acc_number)
		  ->where("pamm_clients_stat_date < date_sub(curdate(),INTERVAL 30 DAY)")
		  ->get()->result();
	}

	function obtain_common_profit_week_ago($acc_number)
	{
	// obtain invested means week ago for stat page
	   return $this->db->select_sum('pamm_clients_stat_sum')
		  ->from('pamm_clients_statement') 
		  ->where("pamm_clients_stat_acc_number",$acc_number)
		  ->where("pamm_clients_stat_role IN ('PLI','PLU')")
		  ->where("pamm_clients_stat_date < date_sub(curdate(),INTERVAL 7 DAY)")
		  ->where("pamm_clients_stat_date > date_sub(curdate(),INTERVAL 14 DAY)")
		  ->get()->result();
	}
	function obtain_common_profit_month_ago($acc_number)
	{
	// obtain invested means month ago for stat page
	   return $this->db->select_sum('pamm_clients_stat_sum')
		  ->from('pamm_clients_statement') 
		  ->where("pamm_clients_stat_acc_number",$acc_number)
		  ->where("pamm_clients_stat_role IN ('PLI','PLU')")
		  ->where("pamm_clients_stat_date > date_sub(curdate(),INTERVAL 30 DAY)")
		  ->get()->result();
	}
	function get_statement_common($acc_number)
	{
	// obtain common statement for account
	   return $this->db->select()
		  ->from('pamm_statement') 
		  ->where("number",$acc_number)
	          ->order_by('order', 'ASC')
		  ->get()->result();
	}
	function tp_write($acc_number,$start,$end,$r_start,$r_end)
	{
	// write trade periods to the pamm_tp table
		return $this->db->set('id','')
		->set('number', $acc_number)
		->set('tp_start', $start)
        	->set('tp_end', $end)
        	->set('rolover_start', $r_start)
        	->set('rolover_end', $r_end)
        	->set('divide', 0)
		->insert('pamm_tp');
	}
	function get_previous_tp_date($acc_number)
	{
	// obtain last trade period rolover end date
	   return $this->db->select('rolover_end')
		  ->from('pamm_tp') 
		  ->where("number",$acc_number)
		  ->where("divide",0)
	          ->order_by('rolover_end', 'DESC')
		  ->limit(1)
		  ->get()->result();
	}
	function get_time_boundaries($acc_number)
	{
	// obtain time boundaries for last trade period
	   return $this->db->select('id, tp_start, tp_end, rolover_start, rolover_end, divide, failed_divide, failed_stopout, second_day')
		  ->from('pamm_tp') 
		  ->where("number",$acc_number)
	          ->order_by('id', 'DESC')
		  ->limit(1)
		  ->get()->result();
	}
	function set_divide_flag($id,$acc_number)
	{
	// set flag after success divide
	   $data['divide']       = 1;
	   return $this->db->where('number',$acc_number)
		  ->where('id',$id)		  
	  	  ->limit(1)
	   	  ->update('pamm_tp', $data);
	}
	function set_second_day_flag($id,$acc_number)
	{
	// set flag after success divide
	   $data['second_day']       = 1;
	   return $this->db->where('number',$acc_number)
		  ->where('id',$id)		  
	  	  ->limit(1)
	   	  ->update('pamm_tp', $data);
	}
	function clear_second_day_flag($acc_number)
	{
	// clear second day flag in case of failed telnet request
	   $data['second_day']       = 0;
	   return $this->db->where('number',$acc_number)
		  ->order_by('id','DESC')		  
	  	  ->limit(1)
	   	  ->update('pamm_tp', $data);
	}

	function get_divide_flag($acc_number)
	{
	// get divide flag for second day divide
	   return $this->db->select('divide')
		  ->from('pamm_tp') 
		  ->where("number",$acc_number)
	          ->order_by('id', 'DESC')
		  ->limit(1)
		  ->get()->result();
	}
	function set_account_status($acc_number,$status,$tp_start,$tp_end,$rolover_start,$rolover_end)
	{
	// set trader period and rolover
	 if ($status != 0 )
	 {
		$data['timeline'] = date('H:i:s d-m-Y',$tp_start)." - ".date('H:i:s d-m-Y',$tp_end);
		$data['timeline1'] = date('H:i:s d-m-Y',$rolover_start)." - ".date('H:i:s d-m-Y',$rolover_end);
	 }
	 else
	 {
		$data['timeline'] = "Not calculated";
		$data['timeline1'] = "Not calculated";
	 }
	   return $this->db->where('login',$acc_number)
	  	  ->limit(1)
	   	  ->update('pamm_accounts', $data);
	}
	function get_account_status($acc_number)
	{
	// get account status for viewing
	   return $this->db->select('timeline,timeline1')
		  ->from('pamm_accounts') 
		  ->where("login",$acc_number)
		  ->get()->result();
	}
	function get_account_color($acc_number)
	{
	// get account status for viewing
	   return $this->db->select('color')
		  ->from('pamm_accounts') 
		  ->where("login",$acc_number)
		  ->get()->result();
	}
	function get_debt_for_view($acc_number)
	{
	// get account status for viewing
	   return $this->db->select('debt')
		  ->from('pamm_accounts') 
		  ->where("login",$acc_number)
		  ->get()->result();
	}

	function set_color_account_status($acc_number,$color)
	{
	// set color account status
	  switch($color):
	        case('1'): $data['color'] = '#00FF00';
		break;
	        case('2'): $data['color'] = '#FF0000';
		break;
	        case('3'): $data['color'] = '#FFFFFF';
		break;
	        case('4'): $data['color'] = '#999999';
		break;
	        case('5'): $data['color'] = '#00F0F0';
		break;
		otherwise:
			$data['color'] = '#FFFFFF';
	  endswitch;	

	   return $this->db->where('login',$acc_number)
	  	  ->limit(1)
	   	  ->update('pamm_accounts', $data);
	}

	public function get_requests12_single($acc_number)
	{
	// obtain requests having 1 and 2 statuses
	    return $this->db->select('request_cid,request_acc_number,request_summ,request_quote,request_id,request_comment,request_type,request_wallet')
		->from('pamm_requests')
		->where('request_status', '0')
		->where('request_urgent', '0')
		->where("request_type IN ('1','2')")
		->where("request_acc_number", $acc_number)
		->order_by('request_type', 'ASC')	
		->get()->result();

	}
	public function obtain_requests_to_decline($acc_number)
	{
	// obtain all inout requests to decline after closing account
	    return $this->db->select('request_id')
		->from('pamm_requests')
		->where('request_status', '0')
		->where('request_urgent', '0')
		->where("request_type IN ('3','4','5','6','7','8','11')")
		->where("request_acc_number", $acc_number)
		->order_by('request_type', 'ASC')	
		->get()->result();


	}
	public function obtain_max_dip($acc_number)
	{
	// obtain max dip for certain account
	   return $this->db->select('max_dip')
		  ->from('pamm_accounts') 
		  ->where('login',$acc_number)
		  ->get()->result();

	}
	public function obtain_tp_data($acc_number)
	{
	// obtain data for trade period generation
	   return $this->db->select('date_reg,of_t_p')
		  ->from('pamm_accounts')
		  ->where('login',$acc_number)
		  ->get()->result();
	}
	public function set_stopout_flag($acc_number)
	{
	// account was closed because of stopout

	   $data['stopout_close'] = '1';
	   return $this->db->where('login',$acc_number)
	  	  ->limit(1)
	   	  ->update('pamm_accounts', $data);
	}
	public function get_stopout_flag($acc_number)
	{
	// obtain stopout flag
	   return $this->db->select('stopout_close')
		  ->from('pamm_accounts') 
		  ->where("login",$acc_number)
		  ->get()->result();
	}

	public function eliminate_trade_period($acc_number)
	{
	// account haven't trade period after closing

	   $data['timeline'] = 'closed';
	   $data['timeline1'] = 'closed';
	   return $this->db->where('login',$acc_number)
	  	  ->limit(1)
	   	  ->update('pamm_accounts', $data);
	}
	public function get_account_active($acc_number)
	{
	// whether account is active or not
	   return $this->db->select('active')
		  ->from('pamm_accounts') 
		  ->where("login",$acc_number)
		  ->get()->result();
	}
	public function obtain_max_dip_from_db($acc_number)
	{
	// obtain max dip for the account
	   return $this->db->select('max_dip')
		  ->from('pamm_accounts') 
		  ->where("login",$acc_number)
		  ->get()->result();
	}
	public function update_activation_request($acc_number,$summ)
	{
	// account haven't trade period after closing

	   $data['request_summ'] = $summ;
	   $data['request_summ_origin'] = $summ;

	   return $this->db->where('request_acc_number',$acc_number)
	  	  ->limit(1)
	   	  ->update('pamm_requests', $data);
	}
	public function obtain_request_summ($id)
	{
	// obtain max dip for the account
	   return $this->db->select('request_summ')
		  ->from('pamm_requests') 
		  ->where("request_id",$id)
		  ->get()->result();
	}
	public function obtain_inoutdebt($acc_number)
	{
	// obtain inoutdebt flag from offer
	   return $this->db->select('inoutdebt')
		  ->from('pamm_accounts') 
		  ->where("login",$acc_number)
		  ->get()->result();
	}
	public function obtain_reinv($acc_number)
	{
	// obtain inoutdebt flag from offer
	   return $this->db->select('reinv')
		  ->from('pamm_accounts') 
		  ->where("login",$acc_number)
		  ->get()->result();
	}
	public function obtain_of_i_p($acc_number)
	{
	// obtain minimal start invest from offer
	   return $this->db->select('of_i_p')
		  ->from('pamm_accounts') 
		  ->where("login",$acc_number)
		  ->get()->result();
	}

	public function get_urgent_flag($acc_number)
	{
	// obtain urgent out flag from offer
	   return $this->db->select('w_b')
		  ->from('pamm_accounts') 
		  ->where("login",$acc_number)
		  ->get()->result();
	}
	function set_stopout_share_flag($acc_number)
	{
	// set flag before urgent out
	   $data['stopout_flag']       = 1;
	   return $this->db->where('login',$acc_number)
	  	  ->limit(1)
	   	  ->update('pamm_accounts', $data);
	}
	function drop_stopout_share_flag($acc_number)
	{
	// drop flag after urgent out
	   $data['stopout_flag']       = 0;
	   return $this->db->where('login',$acc_number)
	  	  ->limit(1)
	   	  ->update('pamm_accounts', $data);
	}
	public function get_stopout_share_flag($acc_number)
	{
	// get stopout flag
	   return $this->db->select('stopout_flag')
		  ->from('pamm_accounts') 
		  ->where("login",$acc_number)
		  ->get()->result();
	}
	public function get_last_tp_id($acc_number)
	{
	// get last tp id for correction
	   return $this->db->select_max('pamm_tp_id')
		  ->from('pamm_tp_results') 
		  ->where("pamm_tp_account",$acc_number)
		  ->get()->result();
	}
	public function correct_tp_results($id,$acc_number,$total,$trader,$investor)
	{
	// get last tp id for correction

  		    $this->db->query("UPDATE pamm_tp_results SET pamm_tp_total = pamm_tp_total + $total, pamm_tp_trader = pamm_tp_trader + $trader, pamm_tp_investor = pamm_tp_investor + $investor WHERE pamm_tp_id=$id AND pamm_tp_account=$acc_number");
	}
	public function delete_old_statement($acc_number)
	{
	   return $this->db->where('number',$acc_number)
			->delete('pamm_statement');
	}
	public function get_rejoin_number($id,$acc_number)
	{
       		return $this->db->select('count(*) AS request_count')
		->from('pamm_requests')
		  ->where('request_acc_number',$acc_number)
		  ->where('request_cid',$id)
		  ->where('request_status','0')
		  ->where('request_type','3')
		  ->where('substring(request_comment,1,1)','R')
		->get()->result();

	}
	function obtain_tp_id($acc_number)
	{
	// obtain time boundaries for last trade period
	   return $this->db->select('id')
		  ->from('pamm_tp') 
		  ->where("number",$acc_number)
	          ->order_by('id', 'DESC')
		  ->limit(1)
		  ->get()->result();
	}
	public function obtain_last_id()
	{
       		return $this->db->select_max('request_id')
		->from('pamm_requests')
		->get()->result();

	}
	function transfer_urgent_requests($acc_number)
	{
	   $data['request_urgent'] = '0';
	   return $this->db->where('request_acc_number',$acc_number)
	   ->where('request_status','0')
	   ->update('pamm_requests', $data);
	}
	function write_last_order_number($acc_number,$id,$lon)
	{
	   $data['last_order_number']  = $lon;
	   return $this->db->where('number',$acc_number)
	   		   ->where('id',$id)
	   		   ->update('pamm_tp', $data);
	}
	function write_first_order_number($acc_number,$id,$fon)
	{
	   $data['first_order_number']  = $fon;
	   return $this->db->where('number',$acc_number)
	   		   ->where('id',$id)
	   		   ->update('pamm_tp', $data);


	}

	function get_start_of_trade_period($acc_number)
	{
	// obtain starts of trade period
	   return $this->db->select('first_order_number')
		  ->from('pamm_tp') 
		  ->where("number",$acc_number)
	          ->order_by('first_order_number', 'DESC')
		  ->get()->result();
	}
	function get_end_of_trade_period($acc_number)
	{
	// obtain ends of trade period
	   return $this->db->select('last_order_number')
		  ->from('pamm_tp') 
		  ->where("number",$acc_number)
	          ->order_by('first_order_number', 'DESC')
		  ->get()->result();
	}
	function get_start_tp($order)
	{
	// obtain ends of trade period
	   return $this->db->select('last_order_number,first_order_number,tp_start,tp_end')
		  ->from('pamm_tp') 
		  ->where("last_order_number",$order)
		  ->get()->result();
	}
	function get_end_tp($order)     
	{
	// obtain starts of trade period
	   return $this->db->select('last_order_number,first_order_number,tp_start,tp_end')
		  ->from('pamm_tp') 
		  ->where("first_order_number",$order)
		  ->get()->result();
	}
	function get_first_tp_start_end($acc_number)     
	{
	// obtain starts of trade period
	   return $this->db->select('tp_start,tp_end')
		  ->from('pamm_tp') 
		  ->where("number",$acc_number)
		  ->order_by('id','DESC')
		  ->limit(1)
		  ->get()->result();
	}

	function get_number_of_tp($acc_number)
	{
	// obtain starts of trade period
	   return $this->db->select('count(*) as c')
		  ->from('pamm_tp') 
		  ->where("number",$acc_number)
		  ->get()->result();
	}
	function write_correction_data($acc_number, $summ, $order_id, $type, $type_divide)
	{
	// write summ to the correction table
		return $this->db->set('order_id',$order_id)
		->set('summ',$summ)
		->set('login',$acc_number)
		->set('type',$type)
		->set('type_divide',$type_divide)
		->insert('pamm_accounts_correction');
	}
	function clear_pamm_accounts_correction()
	{
	// clear pamm accounts correction table
	    $this->db->query("DELETE FROM pamm_accounts_correction");
	}
	function correct_account($acc_number,$balance)
	{
	// correct balance
	    $this->db->query("UPDATE pamm_accounts SET balance = balance + $balance WHERE login=$acc_number");
	}
	function correct_pamm_clients_statement($order_id,$acc_number,$summ)
	{
	// delete the record because of correction

	   return $this->db->where('pamm_clients_stat_acc_number',$acc_number)
			->where('pamm_clients_order_number',$order_id)
			->delete('pamm_clients_statement');

	}
	function obtain_accounts_to_correct()
	{
	// obtain accounts to be corrected
	   return $this->db->select('pamm_accounts.login,pamm_tp.failed_divide,pamm_tp.id')
		  ->from('pamm_accounts')
		  ->join('pamm_tp', 'pamm_accounts.login=pamm_tp.number', 'left')
		  ->where('pamm_tp.failed_divide','1') 
		  ->where("pamm_accounts.active IN ('1','3','5')")
		  ->order_by('pamm_tp.id','DESC')
		  ->limit(1)
		  ->get()->result();
	}
	function set_failed_divide_flag($acc_number)
	{
	// set failed divide flag
	$data['failed_divide'] = 1;
        return $this->db->where('number',$acc_number)
		  ->order_by('id','DESC')
		  ->limit(1)
   	          ->update('pamm_tp', $data);

        }
	function clear_divide_flag($acc_number)
	{
	// clear divide flag in case of fail
	$data['divide'] = 0;
        return $this->db->where('number',$acc_number)
		  ->order_by('id','DESC')
		  ->limit(1)
   	          ->update('pamm_tp', $data);

        }
	function set_divide_flag_second_day($acc_number)
	{
	// set divide flag in the second day
	$data['divide'] = 1;
        return $this->db->where('number',$acc_number)
		  ->order_by('id','DESC')
		  ->limit(1)
   	          ->update('pamm_tp', $data);

        }
	function set_failed_stopout_flag($acc_number)
	{
	// set failed stopout flag
	$data['failed_stopout'] = 1;
        return $this->db->where('number',$acc_number)
		  ->order_by('id','DESC')
		  ->limit(1)
   	          ->update('pamm_tp', $data);

        }
	function get_failed_stopout_flag($acc_number)
	{
	// get failed stopout flag
	   return $this->db->select('failed_stopout')
		  ->from('pamm_tp') 
		  ->where("number",$acc_number)
	          ->order_by('id', 'DESC')
		  ->limit(1)
		  ->get()->result();
	}
	function obtain_correct_operations($acc_number)
	{
	// obtain operations to be corrected
	   return $this->db->select('*')
		  ->from('pamm_accounts_correction')
		  ->where('login',$acc_number)
		  ->get()->result();
	}
	function clear_failed_divide_flag($id)
	{
	// clear failed trade period divide flag after correction
	   $data['failed_divide'] = '0';
	   return $this->db->where('id',$id)
	   ->update('pamm_tp', $data);

	}
	function set_failed_divide_attribute($n,$order)
	{
	// clear failed trade period divide flag after correction
	   $data['failed'] = $n;
	   return $this->db->where('order',$order)
	   ->update('pamm_statement', $data);

	}
	function restore_debt($debt,$acc_number)
	{
	// restore debt
	   $data['debt']       = $debt;
	   return $this->db->where('login',$acc_number)
	  	  ->limit(1)
	   	  ->update('pamm_accounts', $data);
	}
	function add_stopout_list($data)
	{
	// add to stopout list
		$this->db->insert('pamm_stopout_list',$data);
	}
	function delete_from_stopout_list($data)
	{
	// delete from stopout list
	   return $this->db->where('number',$data['number'])
			->where('fsb',$data['fsb'])
			->where('md',$data['md'])
			->where('action',$data['action'])
			->delete('pamm_stopout_list');
	}
	function select_from_stopout_list($number,$action)
	{
	// select from stopout list
	   return $this->db->select('*')
		  ->from('pamm_stopout_list')
		  ->where('number',$number)
		  ->where('action',$action)
		  ->get()->result();
	}
	function get_failed_stopout_accounts()
	{
	// get failed stopout accounts 
	   return $this->db->select()
		  ->from('pamm_stopout_list')
		  ->get()->result();
	}
	function get_divide_denial_flag($acc_number)
	{
	   return $this->db->select('divide_denial')
		  ->from('pamm_accounts')
		  ->where('login',$acc_number)
		  ->get()->result();
	}
	function clear_divide_denial_flag($acc_number)
	{
	// clear divide denial flag
	   $data['divide_denial']       = '0';
	   return $this->db->where('login',$acc_number)
	  	  ->limit(1)
	   	  ->update('pamm_accounts', $data);
	}
	function set_divide_denial_flag($acc_number)
	{
	// set divide denial flag
	   $data['divide_denial']       = '1';
	   return $this->db->where('login',$acc_number)
	  	  ->limit(1)
	   	  ->update('pamm_accounts', $data);
	}
	function get_divide_denial_accounts()
	{
	// get divide denial accounts 
	   return $this->db->select()
		  ->from('pamm_accounts')
		  ->where('divide_denial','1')
		  ->get()->result();
	}

}  

?>