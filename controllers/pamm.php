<?php
class pamm extends CI_Controller
{
        private $data = Array();
        private $wallet = Array();
	function __construct()
	{
		parent::__construct();

		$this->load->model('mainform_model');
		$this->load->library('session');
		
	}
	private function obtain_view_data()
	{	
                global $TRADER_ID;                 			
		$TRADER_ID = $this->session->userdata('TRADER_ID');
		$this->data['mpa']    = (array) $this->mainform_model->get_manage_pamm_accounts($TRADER_ID);
		$this->data['rpa']    = (array) $this->mainform_model->get_rating();
		$this->data['jpa']    =         $this->mainform_model->get_invested_accounts($TRADER_ID);
        }	

	private function obtain_history_mt($acc_number)
	{	
		include_once(BASEPATH."libraries/mq.php");		
		   $time = mktime(23,59,59,date('n'),date('j'),date('Y'));
                   $res = MQ_Query('USERHISTORY-login='.$acc_number.'|password=jnu89gvty|from='.($time-2592000).'|to='.$time
                   ,T_CACHEDIR
                   ,T_CACHETIME
                   ,$acc_number.'/');
		return $res;
	}
	private function obtain_history_pamm($acc_number)
	{	
		   $time = mktime(23,59,59,date('n')-1,date('j'),date('Y')); // trial trade period - 5 days 
		   return $this->mainform_model->oh($acc_number,$time);
	}
	private function obtain_history_investor_pamm($acc_number,$tid)
	{	
		   $time = mktime(23,59,59,date('n')-1,date('j'),date('Y')); // trial trade period - 5 days 
		   return $this->mainform_model->ohi($acc_number,$tid,$time);
	}

	private function obtain_history_without_trader_pamm($acc_number)
	{	
		   $time = mktime(23,59,59,date('n')-1,date('j'),date('Y')); // trial trade period - 5 days 
		   return $this->mainform_model->ohwt($acc_number,$time);
	}
	private function obtain_previous_history_pamm($acc_number)
	{	   
		   return $this->mainform_model->oph1($acc_number);
	}
	private function obtain_detailed_history_pamm($acc_number)
	{	
		   $time = mktime(23,59,59,date('n')-1,date('j'),date('Y')); // trial trade period - 5 days 
		   return $this->mainform_model->odh($acc_number,$time);
	}

	function dsp()
	{
		// obtain global values acceptable in all tabs
		// - wallets for all currencies values
                $TRADER_ID = $this->session->userdata('TRADER_ID');
  		$this->data['quotes'] = $this->mainform_model->mainform_get_quotes();
		$this->wallet  = $this->mainform_model->get_wallet($TRADER_ID);
		$this->wallet  = (array) $this->wallet;
		$this->data['wallet'] = $this->wallet;

		// data for "PAMM account rating" and "Manage PAMM accounts" forms

		$this->obtain_view_data();

		// internal routing according to the tabs

		if($this->uri->segment(3)=="add")$this->add();
		else if($this->uri->segment(3)=="submit")$this->submit();
		else if($this->uri->segment(3)=="manage")$this->manage();
		else if($this->uri->segment(3)=="activate")$this->separate_activation($this->uri->segment(4));
		else if($this->uri->segment(3)=="actsubmit")$this->separate_activation_submit($this->uri->segment(4));
		else if($this->uri->segment(3)=="inout")$this->inout($this->uri->segment(4));
		else if($this->uri->segment(3)=="inout1")$this->inout1($this->uri->segment(4));
		else if($this->uri->segment(3)=="in")$this->request_in($this->uri->segment(4));
		else if($this->uri->segment(3)=="out")$this->request_out($this->uri->segment(4));
		else if($this->uri->segment(3)=="in_submit")$this->request_in_submit($this->uri->segment(4));
		else if($this->uri->segment(3)=="out_submit")$this->request_out_submit($this->uri->segment(4));
		else if($this->uri->segment(3)=="join")$this->join_account($this->uri->segment(4));
		else if($this->uri->segment(3)=="join_submit")$this->join_submit($this->uri->segment(4));
		else if($this->uri->segment(3)=="investorin")$this->investorin($this->uri->segment(4));
		else if($this->uri->segment(3)=="investorin_submit")$this->investorin_submit($this->uri->segment(4));
		else if($this->uri->segment(3)=="investorout")$this->investorout($this->uri->segment(4));
		else if($this->uri->segment(3)=="investorout_submit")$this->investorout_submit($this->uri->segment(4));
		else if($this->uri->segment(3)=="join_requests")$this->join_requests($this->uri->segment(4));
		else if($this->uri->segment(3)=="invest")$this->invest();
		else if($this->uri->segment(3)=="dead_link")$this->dead_link();
		else if($this->uri->segment(3)=="imp_requests")$this->implement_requests();
		else if($this->uri->segment(3)=="divide")$this->divide($this->uri->segment(4));
/*************		else if($this->uri->segment(3)=="check")$this->check($this->uri->segment(4));  *****************/
		else if($this->uri->segment(3)=="unjoin")$this->unjoint_account($this->uri->segment(4));
		else if($this->uri->segment(3)=="unjoin_submit")$this->unjoint_submit($this->uri->segment(4));
		else if($this->uri->segment(3)=="rejoin")$this->rejoint_account($this->uri->segment(4));
		else if($this->uri->segment(3)=="rejoin_submit")$this->rejoint_submit($this->uri->segment(4));
		else if($this->uri->segment(3)=="close")$this->close($this->uri->segment(4));
		else if($this->uri->segment(3)=="close_submit")$this->close_submit($this->uri->segment(4));
		else if($this->uri->segment(3)=="graph")$this->graph($this->uri->segment(4));
		else if($this->uri->segment(3)=="stat")$this->stat($this->uri->segment(4));
		else if($this->uri->segment(3)=="stat_upr")$this->stat_upr($this->uri->segment(4));
		else if($this->uri->segment(3)=="stat_inv")$this->stat_inv($this->uri->segment(4));
		else if($this->uri->segment(3)=="test")$this->change_deposit_test($this->uri->segment(4));
	}

    private function stat($acc_number)
    {
		// statistic output 

			$this->data['from'] = 29;
			$this->data['acc_number'] = $acc_number;
			$offer =$this->mainform_model->obtain_offer($acc_number);
			$this->data['name']  	     =  $offer[0]->fio;
			$this->data['min_withdraw']  =  $offer[0]->min_withdraw;
			$this->data['of_t_p']  	     =  $offer[0]->of_t_p;
			$this->data['of_i_p']  	     =  $offer[0]->of_i_p;
			$this->data['distr']         =  $offer[0]->distr_upr."<font size='3'><b>/</b></font>".$offer[0]->distr_inv;
			$this->data['max_dip']       =  $offer[0]->max_dip;
			$this->data['w_b']  	     =  ($offer[0]->w_b==1)?"<font color='green'>Yes</font>":"<font color='red'>No</font>";
			$this->data['penalty']       =  $offer[0]->penalty;
			$this->data['reinv']  	     =  ($offer[0]->reinv==1)?"<font color='green'>Yes</font>":"<font color='red'>No</font>";
			$this->data['openstat']      =  ($offer[0]->openstat==1)?"<font color='green'>Yes</font>":"<font color='red'>No</font>";
			$this->data['openstatinv']   =  ($offer[0]->openstatinv==1)?"<font color='green'>Yes</font>":"<font color='red'>No</font>";
			$this->data['partbonus1_per']   =  $offer[0]->partbonus1_per;
			$this->data['partbonus1_ub']   =  $offer[0]->partbonus1_ub;
			$this->data['partbonus1_lb']   =  $offer[0]->partbonus1_lb;
			$this->data['partbonus2_per']   =  $offer[0]->partbonus2_per;
			$this->data['partbonus2_ub']   =  $offer[0]->partbonus2_ub;
			$this->data['partbonus2_lb']   =  $offer[0]->partbonus2_lb;
			$this->data['partbonus3_per']   =  $offer[0]->partbonus3_per;
			$this->data['partbonus3_ub']   =  $offer[0]->partbonus3_ub;
			$this->data['partbonus3_lb']   =  $offer[0]->partbonus3_lb;
			$this->data['partbonus4_per']   =  $offer[0]->partbonus4_per;
			$this->data['partbonus4_ub']   =  $offer[0]->partbonus4_ub;
			$this->data['partbonus4_lb']   =  $offer[0]->partbonus4_lb;
			$this->data['partbonus5_per']   =  $offer[0]->partbonus5_per;
			$this->data['partbonus5_ub']   =  $offer[0]->partbonus5_ub;
			$this->data['partbonus5_lb']   =  $offer[0]->partbonus5_lb;

			$this->load->view('main_form',$this->data);
    }
    private function stat_upr($acc_number)
    {
		// statistic output 

			$this->data['from'] = 32;
			$this->data['acc_number'] = $acc_number;
			$offer =$this->mainform_model->obtain_offer($acc_number);
			$this->data['name']  	     =  $offer[0]->fio;
			$this->data['min_withdraw']  =  $offer[0]->min_withdraw;
			$this->data['of_t_p']  	     =  $offer[0]->of_t_p;
			$this->data['of_i_p']  	     =  $offer[0]->of_i_p;
			$this->data['distr']         =  $offer[0]->distr_upr."<font size='3'><b>/</b></font>".$offer[0]->distr_inv;
			$this->data['max_dip']       =  $offer[0]->max_dip;
			$this->data['w_b']  	     =  ($offer[0]->w_b==1)?"<font color='green'>Yes</font>":"<font color='red'>No</font>";
			$this->data['penalty']       =  $offer[0]->penalty;
			$this->data['reinv']  	     =  ($offer[0]->reinv==1)?"<font color='green'>Yes</font>":"<font color='red'>No</font>";
			$this->data['openstat']      =  ($offer[0]->openstat==1)?"<font color='green'>Yes</font>":"<font color='red'>No</font>";
			$this->data['openstatinv']   =  ($offer[0]->openstatinv==1)?"<font color='green'>Yes</font>":"<font color='red'>No</font>";
			$this->data['partbonus1_per']   =  $offer[0]->partbonus1_per;
			$this->data['partbonus1_ub']   =  $offer[0]->partbonus1_ub;
			$this->data['partbonus1_lb']   =  $offer[0]->partbonus1_lb;
			$this->data['partbonus2_per']   =  $offer[0]->partbonus2_per;
			$this->data['partbonus2_ub']   =  $offer[0]->partbonus2_ub;
			$this->data['partbonus2_lb']   =  $offer[0]->partbonus2_lb;
			$this->data['partbonus3_per']   =  $offer[0]->partbonus3_per;
			$this->data['partbonus3_ub']   =  $offer[0]->partbonus3_ub;
			$this->data['partbonus3_lb']   =  $offer[0]->partbonus3_lb;
			$this->data['partbonus4_per']   =  $offer[0]->partbonus4_per;
			$this->data['partbonus4_ub']   =  $offer[0]->partbonus4_ub;
			$this->data['partbonus4_lb']   =  $offer[0]->partbonus4_lb;
			$this->data['partbonus5_per']   =  $offer[0]->partbonus5_per;
			$this->data['partbonus5_ub']   =  $offer[0]->partbonus5_ub;
			$this->data['partbonus5_lb']   =  $offer[0]->partbonus5_lb;

			$this->load->view('main_form',$this->data);
    }
    private function stat_inv($acc_number)
    {
		// statistic output 

			$this->data['from'] = 34;
			$this->data['acc_number'] = $acc_number;
			$offer =$this->mainform_model->obtain_offer($acc_number);
			$this->data['name']  	     =  $offer[0]->fio;
			$this->data['min_withdraw']  =  $offer[0]->min_withdraw;
			$this->data['of_t_p']  	     =  $offer[0]->of_t_p;
			$this->data['of_i_p']  	     =  $offer[0]->of_i_p;
			$this->data['distr']         =  $offer[0]->distr_upr."<font size='3'><b>/</b></font>".$offer[0]->distr_inv;
			$this->data['max_dip']       =  $offer[0]->max_dip;
			$this->data['w_b']  	     =  ($offer[0]->w_b==1)?"<font color='green'>Yes</font>":"<font color='red'>No</font>";
			$this->data['penalty']       =  $offer[0]->penalty;
			$this->data['reinv']  	     =  ($offer[0]->reinv==1)?"<font color='green'>Yes</font>":"<font color='red'>No</font>";
			$this->data['openstat']      =  ($offer[0]->openstat==1)?"<font color='green'>Yes</font>":"<font color='red'>No</font>";
			$this->data['openstatinv']   =  ($offer[0]->openstatinv==1)?"<font color='green'>Yes</font>":"<font color='red'>No</font>";
			$this->data['partbonus1_per']   =  $offer[0]->partbonus1_per;
			$this->data['partbonus1_ub']   =  $offer[0]->partbonus1_ub;
			$this->data['partbonus1_lb']   =  $offer[0]->partbonus1_lb;
			$this->data['partbonus2_per']   =  $offer[0]->partbonus2_per;
			$this->data['partbonus2_ub']   =  $offer[0]->partbonus2_ub;
			$this->data['partbonus2_lb']   =  $offer[0]->partbonus2_lb;
			$this->data['partbonus3_per']   =  $offer[0]->partbonus3_per;
			$this->data['partbonus3_ub']   =  $offer[0]->partbonus3_ub;
			$this->data['partbonus3_lb']   =  $offer[0]->partbonus3_lb;
			$this->data['partbonus4_per']   =  $offer[0]->partbonus4_per;
			$this->data['partbonus4_ub']   =  $offer[0]->partbonus4_ub;
			$this->data['partbonus4_lb']   =  $offer[0]->partbonus4_lb;
			$this->data['partbonus5_per']   =  $offer[0]->partbonus5_per;
			$this->data['partbonus5_ub']   =  $offer[0]->partbonus5_ub;
			$this->data['partbonus5_lb']   =  $offer[0]->partbonus5_lb;

			$this->load->view('main_form',$this->data);
    }

    private function implement_remote_activation($key,$summ,$acc_number,$comm)
    {
		// Deposit the activation fee on MT4 server

                // request to the remote server
		// key = 100 or 200 : deposit or request-in OR withdraw or request-out


		$fp = fsockopen("80.93.48.133", 10023, $errno, $errstr, 30);
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

	    $out = $key.' '.$acc_number.' '.$summ.' '.$comm;
	    fwrite($fp, $out."\r\n");

 	    $buffer = fgets($fp, 128);
	    $buffer = fgets($fp, 128);

		if (substr($buffer,0,5) == '99999')
			{
			    error_log("\r\nRetry ira - ".$acc_number." amount=".$summ." code=".$buffer."\r\n\r\n", 3, "test_engine.log");
			    fwrite($fp, $out."\r\n");

		 	    $buffer = fgets($fp, 128);
			    $buffer = fgets($fp, 128);

			if (substr($buffer,0,5) == '99999')
				{
			            error_log("\r\nRe-retry ira - ".$acc_number." amount=".$summ." code=".$buffer."\r\n\r\n", 3, "test_engine.log");
                        	    fwrite($fp, $out."\r\n");

			 	    $buffer = fgets($fp, 128);
				    $buffer = fgets($fp, 128);
				if (substr($buffer,0,5) == '99999')
					{
					error_log("\r\nError ira - ".$acc_number." amount=".$summ." code=".$buffer."\r\n\r\n", 3, "test_engine.log");
					die();
				        }
				}
			}

       	    $out = "exit ira\r\n";
	    fwrite($fp, $out);
    }

    private function change_deposit_test($acc_number)
    {
                     $this->calculate_tp($acc_number);

			$this->data['from'] = 41;
			$this->load->view('main_form',$this->data);
   }
    private function change_deposit_pamm($key,$acc_number,$summ,$comm)
    {

		$fp = fsockopen("80.93.48.133", 10023, $errno, $errstr, 30);
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
	    $out = $key.' '.$acc_number.' '.$summ.' '.$comm;
	    fwrite($fp, $out."\r\n");

 	    $buffer = fgets($fp, 128);
	    $buffer = fgets($fp, 128);

		if (substr($buffer,0,5) == '99999')
			{
			    error_log("\r\nRetry change deposit - ".$acc_number." amount=".$summ." code=".$buffer."\r\n\r\n", 3, "test_engine.log");
			    fwrite($fp, $out."\r\n");

		 	    $buffer = fgets($fp, 128);
			    $buffer = fgets($fp, 128);

			if (substr($buffer,0,5) == '99999')
				{
			            error_log("\r\nRe-retry change deposit - ".$acc_number." amount=".$summ." code=".$buffer."\r\n\r\n", 3, "test_engine.log");
                        	    fwrite($fp, $out."\r\n");

			 	    $buffer = fgets($fp, 128);
				    $buffer = fgets($fp, 128);
				if (substr($buffer,0,5) == '99999')
					{
					error_log("\r\nError change deposit - ".$acc_number." amount=".$summ." code=".$buffer."\r\n\r\n", 3, "test_engine.log");
					die();
				        }
				}
			}
       	    $out = "exit cd\r\n";
	    fwrite($fp, $out);
    }

    private function join_account($acc_number)
    {
		// join to the existent pamm account - for investors only
	         $total = $this->mainform_model->obtain_acc_total($acc_number);
		 $trader_id_for_join     = $this->mainform_model->obtain_trader_id($acc_number);			// trader id for statement records
		 $invested               = $this->mainform_model->check_join($acc_number,$this->session->userdata('TRADER_ID'));   // check for double join

		 $debt     = $this->mainform_model->obtain_debt($acc_number);			// debt for possibility of join


		 if($trader_id_for_join[0]->tid == $this->session->userdata('TRADER_ID'))
		 {
			$this->data['from'] = 23;
			$this->data['message'] = 'Can\'t join to your own account as an investor';
			$this->load->view('main_form',$this->data);
			return;
		 }
		 elseif (count($invested) > 0)
		 {
		   if ($invested[0]->pamm_invested_accounts_login == $acc_number AND $invested[0]->pamm_invested_accounts_status == '1')
			{
			$this->data['from'] = 23;
			$this->data['message'] = 'Can\'t join to the account twice!';
			$this->load->view('main_form',$this->data);
			return;
			}
		 }

		 if ($debt[0]->debt < 0)
		 {
			$this->data['from'] = 23;
			$this->data['message'] = 'Can\'t join to the account with negative debt!';
			$this->load->view('main_form',$this->data);
			return;
		}	
		
			$this->data['from'] = 9;
			$this->data['acc_number'] = $acc_number;
			$this->load->view('main_form',$this->data);

    }
    private function rejoint_account($acc_number)
    {
		// rejoin to the pamm account - for investors only

		 $trader_id_for_join     = $this->mainform_model->obtain_trader_id($acc_number);			// trader id for statement records
		 $invested               = $this->mainform_model->check_join($acc_number,$this->session->userdata('TRADER_ID'));   // check for double join


		 if($trader_id_for_join[0]->tid == $this->session->userdata('TRADER_ID'))
		 {
			$this->data['from'] = 23;
			$this->data['message'] = 'Can\'t join to your own account as an investor';
			$this->load->view('main_form',$this->data);
			return;
		 }
		 elseif (count($invested) > 0)
		 {
		   if ($invested[0]->pamm_invested_accounts_login == $acc_number AND $invested[0]->pamm_invested_accounts_status == '1')
			{
			$this->data['from'] = 23;
			$this->data['message'] = 'Can\'t join to the account twice!';
			$this->load->view('main_form',$this->data);
			return;
			}
		 }

			$this->data['from'] = 26;
			$this->data['acc_number'] = $acc_number;
			$this->load->view('main_form',$this->data);

    }
	private function rejoint_submit($acc_number)
 	{
		// preparing rejoin data to the insert to the local database
		$TRADER_ID = $this->session->userdata('TRADER_ID');
		// check for mimimal join sum
		$of_i_p = $this->mainform_model->obtain_of_i_p($acc_number);

		   if ($_POST['sumb'] < $of_i_p[0]->of_i_p)
			{
			$this->data['from'] = 23;
			$this->data['message'] = 'Rejoin summ must be greater or equal than '.$of_i_p[0]->of_i_p.'$!';
			$this->load->view('main_form',$this->data);
			return;
			}


		$data_ri['request_acc_number']  = mysql_real_escape_string($acc_number); 
		$data_ri['request_date'] 	= 'NOW()';
		$data_ri['request_cid'] 	= $TRADER_ID;
		$data_ri['request_comment'] 	= "Rejoin request to the account";
		$data_ri['request_summ'] 	= mysql_real_escape_string($_POST['sumb']);
		$data_ri['request_summ_origin'] = mysql_real_escape_string($_POST['sumw']);
		$data_ri['request_quote'] 	= mysql_real_escape_string($_POST['quote']);
		$data_ri['request_type'] 	= '3';
		$data_ri['request_urgent'] 	= 0;

		$this->mainform_model->implement_join_in(mysql_real_escape_string($_POST['wallets']),$data_ri);
		$this->mainform_model->implement_unactive_request_pending($TRADER_ID,mysql_real_escape_string($acc_number));

		// preparing request-in accept data to the view
			$this->data['from'] = 14;


			$this->data['acc_number'] = $acc_number;
		        $this->data['jpa'] = (array) $this->mainform_model->get_invested_accounts($TRADER_ID);
			$this->data['ri_accept'] = "В данный момент сервис недоступен, повторите операцию открытия счета позднее";
			$this->load->view('main_form',$this->data);

	}

	private function join_submit($acc_number)
 	{
		// preparing request-in data to the insert to the local database
		$TRADER_ID = $this->session->userdata('TRADER_ID');

		$of_i_p = $this->mainform_model->obtain_of_i_p($acc_number);

		   if ($_POST['sumb'] < $of_i_p[0]->of_i_p)
			{
			$this->data['from'] = 23;
			$this->data['message'] = 'Join summ must be greater or equal than '.$of_i_p[0]->of_i_p.'$!';
			$this->load->view('main_form',$this->data);
			return;
			}


		$data_ri['request_acc_number']  = mysql_real_escape_string($acc_number); 
		$data_ri['request_date'] 	= 'NOW()';
		$data_ri['request_cid'] 	= $TRADER_ID;
		$data_ri['request_comment'] 	= "Join request to the account";
		$data_ri['request_summ'] 	= mysql_real_escape_string($_POST['sumb']);
		$data_ri['request_summ_origin'] = mysql_real_escape_string($_POST['sumw']);
		$data_ri['request_quote'] 	= mysql_real_escape_string($_POST['quote']);
		$data_ri['request_type'] 	= '3';
		$data_ri['request_urgent'] 	= 0;

		  		  $single = $this->mainform_model->obtain_single($acc_number,$TRADER_ID); // only one join or rejoin requests at once, no doublets
				if (count($single) == 0)
				   {
					$this->mainform_model->implement_join_in(mysql_real_escape_string($_POST['wallets']),$data_ri);
					$this->mainform_model->implement_unactive_request_join_in($TRADER_ID,mysql_real_escape_string($acc_number));
					$this->mainform_model->implement_unactive_request_pending($TRADER_ID,mysql_real_escape_string($acc_number));
					// preparing request-in accept data to the view
					$this->data['from'] = 14; 


					$this->data['acc_number'] = $acc_number;
				        $this->data['jpa'] = (array) $this->mainform_model->get_invested_accounts($TRADER_ID);
					$this->data['ri_accept'] = "В данный момент сервис недоступен, повторите операцию открытия счета позднее";
				   }
				else
				   {
				     if ($single[0]->pamm_invested_accounts_status == 2)
				     {
					$this->mainform_model->implement_join_in(mysql_real_escape_string($_POST['wallets']),$data_ri);
					$this->mainform_model->implement_unactive_request_join_in($TRADER_ID,mysql_real_escape_string($acc_number));
					$this->mainform_model->implement_unactive_request_pending($TRADER_ID,mysql_real_escape_string($acc_number));
					// preparing request-in accept data to the view
					$this->data['from'] = 14; 


					$this->data['acc_number'] = $acc_number;
				        $this->data['jpa'] = (array) $this->mainform_model->get_invested_accounts($TRADER_ID);
					$this->data['ri_accept'] = "В данный момент сервис недоступен, повторите операцию открытия счета позднее";

				     }
				     else
				     {
					$this->data['from'] = 30;
					$this->data['acc_number_joint'] = $acc_number;
				     }
				   }

			$this->load->view('main_form',$this->data);

	}

    private function close($acc_number)
    {
		// close existent pamm account - for trader only

			$this->data['from'] = 27;
			$this->data['acc_number'] = $acc_number;
			$this->load->view('main_form',$this->data);
    }
	private function close_submit($acc_number)
 	{
		// preparing request-in data to the insert to the local database
		$TRADER_ID = $this->session->userdata('TRADER_ID');

		$data_ri['request_acc_number']  = mysql_real_escape_string($acc_number); 
		$data_ri['request_date'] 	= 'NOW()';
		$data_ri['request_cid'] 	= $TRADER_ID;
		$data_ri['request_comment'] 	= "Close account";
		$data_ri['request_summ'] 	= 999999.00;
		$data_ri['request_summ_origin'] = 1.00;
		$data_ri['request_quote'] 	= 1.00;
		$data_ri['request_type'] 	= '12';
		$data_ri['request_urgent'] 	= 0;

		$this->mainform_model->implement_close(mysql_real_escape_string($_POST['wallets']),$data_ri);

		// set the pre-close status
		$this->mainform_model->closing_account($acc_number);

		 // cyan status
               	 $this->mainform_model->set_color_account_status($acc_number,5);

		// eliminate the trade preiod
		$this->mainform_model->eliminate_trade_period($acc_number);

		// preparing request-in accept data to the view
			$this->data['from'] = 28;
			$this->data['mpa']    = (array) $this->mainform_model->get_manage_pamm_accounts($TRADER_ID);

			$this->data['acc_number'] = $acc_number;
			$this->load->view('main_form',$this->data);

	}

    private function unjoint_account($acc_number)
    {
		// join to the existent pamm account - for investors only

			$this->data['from'] = 24;
			$this->data['acc_number'] = $acc_number;
			$this->load->view('main_form',$this->data);
    }
	private function unjoint_submit($acc_number)
 	{
		// preparing request-in data to the insert to the local database
		$TRADER_ID = $this->session->userdata('TRADER_ID');
		$data_ri['request_acc_number']  = mysql_real_escape_string($acc_number); 
		$data_ri['request_date'] 	= 'NOW()';
		$data_ri['request_cid'] 	= $TRADER_ID;
		$data_ri['request_comment'] 	= "Unjoint request to the account";
		$data_ri['request_summ'] 	= 999999.00;
		$data_ri['request_summ_origin'] = 1.00;
		$data_ri['request_quote'] 	= 1.00;
		$data_ri['request_urgent'] 	= 0;
		$data_ri['request_type'] 	= '11';

		$this->mainform_model->implement_join_out(mysql_real_escape_string($_POST['wallets']),$data_ri);
		$this->mainform_model->implement_unactive_request_unjoint($TRADER_ID,mysql_real_escape_string($acc_number)); // link in the pamm_invested_accounts table

		// preparing request-in accept data to the view
			$this->data['from'] = 14;
			$this->data['acc_number'] = $acc_number;
		        $this->data['jpa'] = (array) $this->mainform_model->get_invested_accounts($TRADER_ID);
			$this->data['ri_accept'] = "Request to unjoint was accepted.";
			$this->load->view('main_form',$this->data);

	}


	private function activation($summ_corrected, $acc_number, $passwd, $passwd_inv, $sumb, $sumw, $wallets)
	{
		$TRADER_ID = $this->session->userdata('TRADER_ID');

	                // query to the MT server

                       	$this->implement_remote_activation(100,$summ_corrected, $acc_number, "activation account trader");

			        $this->data['from'] = 2;  // signal to the view

		// preparing user profile data to the view
	        $this->data['acc_number'] = $acc_number;
	        $this->data['result'] = "Success";
	        $this->data['passwd'] = $passwd;
	        $this->data['passwd_inv'] = $passwd_inv;
		$this->load->view('main_form',$this->data);


	}
	private function inout($acc_number)
	{
		$TRADER_ID = $this->session->userdata('TRADER_ID');			
			$acc_number = mysql_real_escape_string($acc_number);

		// preparing request-inout form data to the view
			$this->data['from'] = 6;
			$this->data['acc_number'] = $acc_number;
		        $this->data['requests'] = $this->mainform_model->get_requests($acc_number);
			$this->load->view('main_form',$this->data);

	}
	private function inout1($acc_number)
	{
			$acc_number = mysql_real_escape_string($acc_number);

		// preparing request-inout form data to the view
			$this->data['from'] = 18;
			$this->data['acc_number'] = $acc_number;
		        $this->data['requests'] =   $this->mainform_model->get_requests($acc_number);
			$this->load->view('main_form',$this->data);

	}
	private function join_requests($acc_number)
	{
			$acc_number = mysql_real_escape_string($acc_number);

		// preparing join request-inout form data to the view
			$this->data['from'] = "13";
			$this->data['acc_number'] = $acc_number;
		        $this->data['join_requests'] = (array) $this->mainform_model->get_join_requests($acc_number);
			$this->load->view('main_form',$this->data);

	}
	private function request_in_submit($acc_number)
 	{


		$TRADER_ID = $this->session->userdata('TRADER_ID');
		// preparing request-in data to the insert to the local database

		$data_ri['request_acc_number']  = mysql_real_escape_string($acc_number); 
		$data_ri['request_date'] 	= 'NOW()';
		$data_ri['request_cid'] 	= $TRADER_ID;
		$data_ri['request_comment'] 	= "Trader's request-in";
		$data_ri['request_summ'] 	= mysql_real_escape_string($_POST['sumb']);
		$data_ri['request_summ_origin'] = mysql_real_escape_string($_POST['sumw']);
		$data_ri['request_quote'] 	= mysql_real_escape_string($_POST['quote']);
		$data_ri['request_type'] 	= '5';
		$data_ri['request_urgent'] 	= 0;

		$this->mainform_model->implement_request_in(mysql_escape_string($_POST['wallets']),$data_ri);

		// preparing request-in accept data to the view
			$this->data['from'] = 6;

			$this->data['acc_number'] = $acc_number;
		        $this->data['requests'] = (array) $this->mainform_model->get_requests($acc_number);
			$this->data['ri_accept'] = "В данный момент сервис недоступен, повторите операцию открытия счета позднее";
			$this->load->view('main_form',$this->data);

	}
	private function request_out_submit($acc_number)
 	{

		// preparing request-out data to the insert to the local database
		$TRADER_ID = $this->session->userdata('TRADER_ID');

		// obtain trade period id (for urgent outs)
		$tp_id = $this->mainform_model->obtain_tp_id($acc_number);

		// obtain last request id
		$last_id = $this->mainform_model->obtain_last_id();


		$data_ri['request_acc_number']  = mysql_real_escape_string($acc_number); 
		$data_ri['request_date'] 	= 'NOW()';
		$data_ri['request_cid'] 	= $TRADER_ID;
		$data_ri['request_comment'] 	= "Trader's request-out";
		$data_ri['request_summ'] 	= mysql_real_escape_string($_POST['sumb']);
		$data_ri['request_tp_id']       = $tp_id[0]->id;

				   if ($_POST['wallets'] != 'GBP')
					{
					  if ($_POST['wallets'] != 'USD')
						{
					   	   $q = $this->mainform_model->get_moment_quote($_POST['wallets']);
						   $quote  = $q[0]->price;
						}
					  else
						   $quote = 1;
					}
				   else
					{
					   $q = $this->mainform_model->get_moment_quote_gbp($_POST['wallets']);
					   $quote  = 1/($q[0]->price);
					}
					   $data_ri['request_summ_origin'] = $data_ri['request_summ']*$quote;

		$data_ri['request_quote'] 	= $quote;
		$data_ri['request_type'] 	= '7';

		if (isset($_POST['urgent']))
			{
			$data_ri['request_urgent'] 	= 1;
			$data_ri['request_common_id'] 	= $last_id[0]->request_id + 1;			
			}
		else
			{
			$data_ri['request_urgent'] 	= 0;
			$data_ri['request_common_id'] 	= 0;			
			}

		$this->mainform_model->implement_request_out(mysql_real_escape_string($_POST['wallets']),$data_ri);

		// preparing request-out accept data to the view
			$this->data['from'] = 6;

			$this->data['acc_number'] = $acc_number;
		        $this->data['requests'] = $this->mainform_model->get_requests($acc_number);
			$this->data['ri_accept'] = "В данный момент сервис недоступен, повторите операцию открытия счета позднее";
			$this->load->view('main_form',$this->data);

	}
	private function request_in($acc_number)
	{
		// preparing reuest-in form data to the view
			$this->data['from'] = 7;
			$this->data['acc_number'] = $acc_number;
			$this->load->view('main_form',$this->data);

	}
	private function request_out($acc_number)
	{
		$TRADER_ID = $this->session->userdata('TRADER_ID');
	        //   calaculating the acceptable amount of withdrawal
			  $trader_history = $this->mainform_model->obtain_history_only_trader($acc_number);	
		 $min_withdraw = $this->mainform_model->obtain_min_withdraw($acc_number);
		 $threshold = $trader_history[0]->pamm_clients_stat_sum - 300;

		// preparing reuest-in form data to the view
			if ($threshold <= 0)
				{
				$this->data['threshold']  = 'No money to withdraw at the moment';
				$this->data['avaliability_withdraw'] = FALSE;
				}
			else
				{
				$this->data['threshold']  = 'Avaliable amount of money to withdraw at the moment:'.$threshold;
				$this->data['avaliability_withdraw'] = TRUE;
				}

			$trader_requests_out = $this->mainform_model->obtain_requests_out($acc_number,$TRADER_ID);	

			if ($trader_requests_out[0]->request_summ > 0)
				{
				$this->data['requests']   = 'Number of requests: '.$trader_requests_out[0]->request_count.' Withdraw amount (due to requests):'.$trader_requests_out[0]->request_summ;
				$this->data['button']     = '<a class="submit" href="/pamm/dsp/inout/'.$acc_number.'"><span style="color:white;font-weight:bold;">Out requests</span></a>';
				}
			else
				{
				$this->data['requests']="Number of requests: 0";
				$this->data['button']     = '';
				}

			$this->data['min_withdraw'] = $min_withdraw[0]->min_withdraw;
			$this->data['from'] = 17;
			$this->data['acc_number'] = $acc_number;
			$this->load->view('main_form',$this->data);

	}
	private function investorin($acc_number)
	{
		// preparing reuest-in form data to the view
			$this->data['from'] = 19;
			$this->data['acc_number'] = $acc_number;
			$this->load->view('main_form',$this->data);

	}
 	private function investorout($acc_number)
	{
		$TRADER_ID = $this->session->userdata('TRADER_ID');
	        //   calaculating the acceptable amount of withdrawal
			$min_withdraw = $this->mainform_model->obtain_min_withdraw($acc_number);
			$penalty = $this->mainform_model->get_penalty($acc_number);
		        $investor_history = $this->obtain_history_investor_pamm($acc_number,$TRADER_ID);	
			$investor_requests_out = $this->mainform_model->obtain_requests_out($acc_number,$TRADER_ID);	
			$this->data['threshold']  = 'Avaliable amount of money to withdraw at the moment:'.$investor_history[0]->pamm_clients_stat_sum;
			if ($investor_requests_out[0]->request_summ > 0)
				{
				$this->data['requests']   = 'Number of requests: '.$investor_requests_out[0]->request_count.' Withdraw amount (due to requests):'.$investor_requests_out[0]->request_summ;
				$this->data['button']     = '<a class="submit" href="/pamm/dsp/inout/'.$acc_number.'"><span style="color:white;font-weight:bold;">Out requests</span></a>';
				}
			else
				{
				$this->data['requests']   = "Number of requests: 0";
				$this->data['button']     = '';
				}

		 $threshold = $investor_history[0]->pamm_clients_stat_sum;

		// preparing reuest-in form data to the view
			if ($threshold <= 0)
				{
				$this->data['threshold']  = 'No money to withdraw at the moment';
				$this->data['avaliability_withdraw'] = FALSE;
				}
			else
				{
				$this->data['threshold']  = 'Avaliable amount of money to withdraw at the moment:'.$threshold;
				$this->data['avaliability_withdraw'] = TRUE;
				}


		// preparing reuest-out form data to the view
			$this->data['min_withdraw'] = $min_withdraw[0]->min_withdraw;
			$this->data['penalty'] = $penalty[0]->penalty;
			$this->data['from'] = 20;
			$this->data['acc_number'] = $acc_number;
			$this->load->view('main_form',$this->data);

	}
	private function investorout_submit($acc_number)
 	{
		// check for minimal withdraw
		$min_withdraw = $this->mainform_model->obtain_min_withdraw($acc_number);

		   if ($_POST['sumb'] < $min_withdraw[0]->min_withdraw)
			{
			$this->data['from'] = 23;
			$this->data['message'] = 'Withdrawal summ must be greater or equal than '.$min_withdraw[0]->min_withdraw.'$!';
			$this->load->view('main_form',$this->data);
			return;
			}

		// preparing investor's request-in data to the insert to the local database
		$TRADER_ID = $this->session->userdata('TRADER_ID');

		// obtain trade period id (for urgent outs)
		$tp_id = $this->mainform_model->obtain_tp_id($acc_number);

		// obtain last request id
		$last_id = $this->mainform_model->obtain_last_id();

		$data_ri['request_acc_number']  = mysql_real_escape_string($acc_number); 
		$data_ri['request_cid']         = $TRADER_ID;
		$data_ri['request_date'] 	= 'NOW()';
		$data_ri['request_comment'] 	= "Ivnestor's request-out";
		$data_ri['request_summ'] 	= mysql_real_escape_string($_POST['sumb']);
		$data_ri['request_tp_id']       = $tp_id[0]->id;
	
				   if ($_POST['wallets'] != 'GBP')
					{
					  if ($_POST['wallets'] != 'USD')
						{
					   	   $q = $this->mainform_model->get_moment_quote($_POST['wallets']);
						   $quote  = $q[0]->price;
						}
					  else
						   $quote = 1;
					}
				   else
					{
					   $q = $this->mainform_model->get_moment_quote_gbp($_POST['wallets']);
					   $quote  = 1/($q[0]->price);
					}

					   $data_ri['request_summ_origin'] = $data_ri['request_summ']*$quote;

		$data_ri['request_quote'] 	= $quote;
		$data_ri['request_type'] 	= '8';

		if (isset($_POST['urgent']))
			{
			$data_ri['request_urgent'] 	= 1;
			$data_ri['request_common_id'] 	= $last_id[0]->request_id + 1;			
			}
		else
			{
			$data_ri['request_urgent'] 	= 0;
			$data_ri['request_common_id'] 	= 0;			
			}

		

		$this->mainform_model->implement_request_out(mysql_real_escape_string($_POST['wallets']),$data_ri);

		// preparing request-in accept data to the view
			$this->data['from'] = 18;

			$this->data['acc_number'] = $acc_number;
		        $this->data['requests'] = (array) $this->mainform_model->get_requests($acc_number);
			$this->data['ri_accept'] = "В данный момент сервис недоступен, повторите операцию открытия счета позднее";
			$this->load->view('main_form',$this->data);

	}


	private function tp_results($result_array,$acc_number)
	{
	        $prev_date = $this->mainform_model->tp_prev_res_date($acc_number);
		if (is_null($prev_date[0]->pamm_clients_stat_date))
			$prev_date[0]->pamm_clients_stat_date = mktime(23,59,59,date('n'),date('j'),date('Y')-1);
		$u_total  = $this->mainform_model->get_total($acc_number,$prev_date[0]->pamm_clients_stat_date,'U');
		$i_total  = $this->mainform_model->get_total($acc_number,$prev_date[0]->pamm_clients_stat_date,'I');
		$total    = round($result_array['pamm_tp_total'],2);
		$this->mainform_model->tp_results_insert($acc_number,$total,round($result_array['pamm_tp_profitloss'],2),round($u_total[0]->pamm_clients_stat_sum,2),round($i_total[0]->pamm_clients_stat_sum));
        }	
	private function investorin_submit($acc_number)
 	{

		$min_withdraw = $this->mainform_model->obtain_min_withdraw($acc_number);

		   if ($_POST['sumb'] < $min_withdraw[0]->min_withdraw)
			{
			$this->data['from'] = 23;
			$this->data['message'] = 'Deposit summ must be greater or equal than '.$min_withdraw[0]->min_withdraw.'$!';
			$this->load->view('main_form',$this->data);
			return;
			}


				$TRADER_ID = $this->session->userdata('TRADER_ID');
		// preparing investor's request-in data to the insert to the local database

		$data_ri['request_acc_number']  = mysql_real_escape_string($acc_number); 
		$data_ri['request_date'] 	= 'NOW()';
		$data_ri['request_cid'] 	= $TRADER_ID;
		$data_ri['request_comment'] 	= "Ivnestor's request-in";
		$data_ri['request_summ'] 	= mysql_real_escape_string($_POST['sumb']);
		$data_ri['request_summ_origin'] = mysql_real_escape_string($_POST['sumw']);
		$data_ri['request_quote'] 	= mysql_real_escape_string($_POST['quote']);
		$data_ri['request_type'] 	= '6';
		$data_ri['request_urgent'] 	= 0;

		$this->mainform_model->implement_request_in(mysql_real_escape_string($_POST['wallets']),$data_ri);

		// preparing request-in accept data to the view
			$this->data['from'] = 11;

			$this->data['acc_number'] = $acc_number;
		        $this->data['requests'] = (array) $this->mainform_model->get_requests($acc_number);
			$this->data['ri_accept'] = "В данный момент сервис недоступен, повторите операцию открытия счета позднее";
			$this->load->view('main_form',$this->data);

	}

	public function separate_activation($acc_number)
	{
	        $this->data['from'] = 5; // signal to the view
		$this->data['acc_number'] = $acc_number;
                $this->load->view('main_form',$this->data);
	}
	public function separate_activation_submit($acc_number)
	{
		$TRADER_ID = $this->session->userdata('TRADER_ID');
		$acc_number = mysql_real_escape_string($acc_number);
		if ($_POST['sumb'] >= 300)
		{

			$this->mainform_model->implement_local_activation(mysql_real_escape_string($_POST['sumw']),mysql_real_escape_string($_POST['wallets']),$TRADER_ID,$acc_number);
			$this->mainform_model->update_activation_request($acc_number,$_POST['sumb']);
        
			// deposit activation fee outside our server
			// add the sum which is entered in $_POST['sumb'] value;
	
			$summ_corrected = (float) $_POST['sumb']; // for avoiding full stop and comma in the value				

			// float stopout balance
						
			$this->mainform_model->write_fsb($acc_number,$_POST['sumb']);


		   	$this->acc_lock_unlock($acc_number,1);

	              	$ret_act = $this->implement_remote_activation(100,$summ_corrected, $acc_number,"Activation");

			// generate first trade period
			$dur = $this->mainform_model->get_duration($acc_number);
			$duration = $dur[0]->of_t_p;
	       		$this->calculate_tp($acc_number);

			        $this->data['from'] = 5;  // signal to the view

			// update the request
			$this->mainform_model->update_activation_request($acc_number,$_POST['sumb']);

			// change the client statement
		
			$this->mainform_model->change_statement($TRADER_ID,$acc_number,mysql_real_escape_string($_POST['sumb']),"U",0);

			// refresh the acivated account data
			$this->obtain_view_data();
		        $this->data['from'] = 3; // signal to the view
	                $this->load->view('main_form',$this->data);
		}
		else
		{

		        $this->data['from'] = 36; // signal to the view
			$this->data['error_msg'] = "Activation summ must be greater or equal than 300 USD!";
	                $this->load->view('main_form',$this->data);
		}

	}

	public function add()
	{
	        // add PAMM account controller

	        $this->data['from'] = 1; // signal to the view

	        // obtain select options
		$this->data['country_list_array'] =  $this->mainform_model->get_country_list();
                $this->load->view('main_form',$this->data);
	}

	public function submit()
	{
                                          $TRADER_ID = $this->session->userdata('TRADER_ID');
	     // processing $_POST values and fill $pamm array

		$pamm['fio'] 		= mysql_real_escape_string($_POST['fio1']." ".$_POST['fio2']." ".$_POST['fio3']);
		$pamm['date_of_birth'] 	= mysql_real_escape_string($_POST['dateofbirth']);
		$pamm['email'] 		= mysql_real_escape_string($_POST['email']);
		$pamm['phone'] 		= mysql_real_escape_string($_POST['phone']);
		$pamm['zipcode'] 	= mysql_real_escape_string($_POST['zipcode']);
		$pamm['country'] 	= mysql_real_escape_string($_POST['country']);
		$pamm['city'] 		= mysql_real_escape_string($_POST['city']);
		$pamm['region']  	= mysql_real_escape_string($_POST['region']);
		$pamm['address'] 	= mysql_real_escape_string($_POST['address']);
		$pamm['tp'] 	 	= mysql_real_escape_string($_POST['tp']);
		$pamm['system'] 	= mysql_real_escape_string($_POST['system']);
		$pamm['of_m_p'] 	= mysql_real_escape_string($_POST['of_m_p']);
		$pamm['min_withdraw'] 	= mysql_real_escape_string($_POST['of_m_p']);
		$pamm['of_i_p'] 	= mysql_real_escape_string($_POST['ofip']);
		$pamm['reinv'] 		= mysql_real_escape_string($_POST['reinv']);
      		$pamm['of_t_p'] 	= mysql_real_escape_string(substr($_POST['of_t_p'],0,2));
      		$pamm['distr_upr'] 	= mysql_real_escape_string(substr($_POST['disp'],0,2));
      		$pamm['distr_inv'] 	= mysql_real_escape_string(substr($_POST['disp'],strlen($_POST['disp'])-3,2));
		$pamm['max_dip'] 	= mysql_real_escape_string($_POST['max_dip']);
		if (isset($_POST['w_b']))
			$pamm['w_b'] 	= '1';
		else
			$pamm['w_b'] 	= '0';

		if (isset($_POST['inoutdebt']))
			$pamm['inoutdebt'] 	= '1';
		else
			$pamm['inoutdebt'] 	= '0';
		$pamm['penalty'] 	= mysql_real_escape_string($_POST['penalty']);
		$pamm['reinv'] 		= mysql_real_escape_string($_POST['reinv']);
		if (isset($_POST['openstat']))
			$pamm['openstat'] 	= '1';
		else
			$pamm['openstat'] 	= '0';
		if (isset($_POST['openstatinv']))
			$pamm['openstatinv'] 	= '1';
		else
			$pamm['openstatinv'] 	= '0';

		$pamm['partbonus1_lb']  = 0;
		$pamm['partbonus1_ub']  = 0;
		$pamm['partbonus1_per'] = 0;
		$pamm['partbonus2_lb']  = 0;
		$pamm['partbonus2_ub']  = 0;
		$pamm['partbonus2_per'] = 0;
		$pamm['partbonus3_lb']  = 0;
		$pamm['partbonus3_ub']  = 0;
		$pamm['partbonus3_per'] = 0;
		$pamm['partbonus4_lb']  = 0;
		$pamm['partbonus4_ub']  = 0;
		$pamm['partbonus4_per'] = 0;
		$pamm['partbonus5_lb']  = 0;
		$pamm['partbonus5_ub']  = 0;
		$pamm['partbonus5_per'] = 0;

		// select group denending of the maximum drawdown

		switch ($pamm['max_dip']):
			case("100"): $group = "9";
			break;
			case("90"): $group = "10";
			break;
			case("80"): $group = "11";
			break;
			case("70"): $group = "12";
			break;
			case("60"): $group = "13";
			break;
			case("50"): $group = "14";
			break;
			case("40"): $group = "15";
			break;
			case("30"): $group = "16";
			break;
			case("20"): $group = "17";
			break;
			case("10"): $group = "18";
			break;
		endswitch;

		// generating passwords 

	        $passwd = chr(rand(97,122)).chr(rand(48,57)).chr(rand(65,90)).chr(rand(48,57)).chr(rand(97,122)).chr(rand(48,57));
	        $passwd_inv = chr(rand(97,122)).chr(rand(48,57)).chr(rand(65,90)).chr(rand(48,57)).chr(rand(97,122)).chr(rand(48,57));

	        // build query to the remote server

	        $query="NEWACCOUNT MASTER=hbyu785ggb67g|IP=195.182.156.154|GROUP=".$group."|NAME=".iconv("UTF-8","CP1251",$pamm['fio'])."|".
        	       "PASSWORD=".$passwd."|INVESTOR=".$passwd_inv."|EMAIL=".$pamm['email']."|COUNTRY=".iconv("UTF-8","CP1251",$pamm['country'])."|".
	               "STATE=".iconv("UTF-8","CP1251",$pamm['region'])."|CITY=".iconv("UTF-8","CP1251",$pamm['city'])."|ADDRESS=".iconv("UTF-8","CP1251",$pamm['address'])."|COMMENT=|".
        	       "PHONE=".$pamm['phone']."|PHONE_PASSWORD=".iconv("UTF-8","CP1251",$pamm['tp'])."|STATUS=|ZIPCODE=".$pamm['zipcode']."|".
   	               "ID=|LEVERAGE=100|AGENT=|SEND_REPORTS=|DEPOSIT=";

   	        // open remote server socket

 		$ptr=fsockopen("80.93.48.133",443,$errno,$errstr,15); 

 		// get the response

		if (!$ptr) 
		    {
		    $ret =  $errstr.$errno;
		    }
  		   if($ptr)
		     {
		      if(fputs($ptr,"W$query\nQUIT\n")!=FALSE)
		        {
		         $ret='';
		         while(!feof($ptr)) 
			          {
			           $line=fgets($ptr,128);
			           if($line=="end\r\n") break; 
			           $ret.= $line;
			          } 
		       	}
		      fclose($ptr);
		     }
		// parse the return code (success or not)
               
		switch (substr($ret,0,2)):
			
		case("OK"):

		        $this->data['from'] = 2; // signal to the view
			$acc_number = rtrim(substr($ret,strpos($ret,"=")+1));

			$pamm['login'] = $acc_number;

		    // crypting the password

		    $ey_mcrypt = 'oHi6feSh';

		    $td = mcrypt_module_open(MCRYPT_DES, '', 'ecb', '') or die('Cant open!');
		    $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		    mcrypt_generic_init($td, 'oHi6feSh', $iv);
		    $passwd_enc = base64_encode(mcrypt_generic($td, $passwd));

		    // preparing the account data to store into local database

			$pamm['password']     = $passwd_enc;
			$pamm['password_inv'] = $passwd_inv;
			$pamm['active'] = (isset($_POST['activate'])) ? "1" : "0";
			$pamm['banned'] = '0';
			$pamm['ban_reason'] = "";
			$pamm['date_reg'] = date('Y-m-d H:i:s');
			$pamm['tid'] = $TRADER_ID;


 		   // local opening account
			$this->mainform_model->open_account($pamm);

		    // opening account correction
			$pamm1['login'] = $acc_number;
			$pamm1['summ']  = 0.00;

		   // local opening account
			$this->mainform_model->open_account_correction($pamm1);

		   // log opening account
			$this->mainform_model->log_open_account($ret);
		   // store empty trade period values in pamm_tp_results table
			$this->mainform_model->empty_tp_result($acc_number);

		   // activate the account or not
			if (isset($_POST['activate']))
			   {
				// to be done: here should be sent a letter to the client about opening account with activalion

				// deposit activation fee inside our server

					$this->mainform_model->implement_local_activation(mysql_real_escape_string($_POST['sumw']),$_POST['wallets'],$TRADER_ID,$acc_number);
				// generate first trade period
				$dur = $this->mainform_model->get_duration($acc_number);
				$duration = $dur[0]->of_t_p;

				$this->calculate_tp($acc_number);

				// deposit activation fee outside our server
				// add the sum which is entered in $_POST['sumb'] value;
		
					$summ_corrected = (float) $_POST['sumb']; // for avoiding full stop and comma in the value				

				// POSTPONED - opening deposit with non-zero value

				// change the client statement
				        $this->mainform_model->change_statement($TRADER_ID,$acc_number,$_POST['sumb'],"U",0);
				        $this->mainform_model->activation_tp_result($_POST['sumb'],$acc_number);

			       //   for the immediate view
		                	 $account_time_boundaries = $this->mainform_model->get_time_boundaries($acc_number);
					 $this->mainform_model->set_account_status($acc_number,1,$account_time_boundaries[0]->tp_start,$account_time_boundaries[0]->tp_end,$account_time_boundaries[0]->rolover_start,$account_time_boundaries[0]->rolover_end);
					 $this->mainform_model->set_color_account_status($acc_number,1);
				        $this->activation($summ_corrected,$acc_number,$passwd,$passwd_inv,$_POST['sumb'],mysql_real_escape_string($_POST['sumw']),mysql_real_escape_string($_POST['wallets']));

                                                  // generate the request (already implemented one)
						  $data_ri['request_acc_number']  = $acc_number; 
						  $data_ri['request_cid'] 	  = $TRADER_ID;
						  $data_ri['request_comment']	  = 'Activation (immediate)';
						  $data_ri['request_summ'] 	  = $_POST['sumb'];
						  $data_ri['request_summ_origin'] = $_POST['sumb'];
						  $data_ri['request_quote']       = '1.00';
						  $data_ri['request_type']        = '14';
						  $data_ri['request_status']      = '3';

						  $this->mainform_model->implement_close_investor($data_ri);

						  // float stopout balance
						
						  $this->mainform_model->write_fsb($acc_number,$_POST['sumb']);

				              	// add to stopout
					       $this->stopout('ADD',$acc_number,$_POST['sumb'],$pamm['max_dip'],"after activation (pamm)");		 


			   }
			else   // opening without activation
			  {
				// here should be sent a letter to the client about opening account without activalion

				// POSTPONED - opening deposit with zero value

				//	$data_od['acc_number'] = $acc_number;
				//	$data_od['deposit'] = 0;
				//	$this->mainform_model->open_deposit($data_od);

				// locking the account

			   	$this->acc_lock_unlock($acc_number,0);

		 	        $this->data['from'] = 4; // signal to the view


                               // generate the request (already implemented one)
				  $data_ri['request_acc_number']  = $acc_number; 
				  $data_ri['request_cid'] 	  = $TRADER_ID;
				  $data_ri['request_comment']	  = 'Activation (postponed)';
				  $data_ri['request_summ'] 	  = 0;
				  $data_ri['request_summ_origin'] = 0;
				  $data_ri['request_quote']       = '1.00';
				  $data_ri['request_type']        = '14';
				  $data_ri['request_status']      = '3';

				  $this->mainform_model->implement_close_investor($data_ri);

		
				// preparing create account data to the view

				$this->data['acc_number'] = $acc_number;
				$this->data['error_msg'] = "Login = ".$acc_number."<BR>Password = ".$passwd."<BR>Investor's password=".$passwd_inv."<BR>Активация счета не производилась";
				$this->load->view('main_form',$this->data);
			   }
			break;		
        	case("ER"):
		// error during the opening the accont
			$this->mainform_model->log_open_account($ret);

		        $this->data['from'] = 4; // signal to the view

		// preparing error create account data to the view
			$this->data['error_msg'] = "В данный момент сервис недоступен, повторите операцию открытия счета позднее";
			$this->load->view('main_form',$this->data);
		break;
        	default:
		// error opening the account without response
			$this->mainform_model->log_open_account('Empty response');
		        $this->data['from'] = 4; // signal to the view

		// preparing error create account data to the view
			$this->data['error_msg'] = "В данный момент сервис недоступен, повторите операцию открытия счета позднее";
			$this->load->view('main_form',$this->data);
		break;

		endswitch;

	}
	public function manage()
	{
	        $this->data['from'] = 3; // signal to the view
		$this->load->view('main_form',$this->data);
	}
	public function invest()
	{
	        $this->data['from'] = 14; // signal to the view
		$this->load->view('main_form',$this->data);
	}


}
?>