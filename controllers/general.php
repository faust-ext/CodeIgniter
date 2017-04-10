<?php
class general extends CI_Controller {

	function __construct()
    {
		parent::__construct(); 
		$this->load->model('mainform_model');
		
    }
    public function index()
    {
	$f = fopen("f.txt","a+") or die('Cant open!');
        // loop for all accounts
        $account_array      = $this->mainform_model->get_divide_accounts();

	foreach ($account_array as $value)
	{
	  $calculate = TRUE;
	  $account_time_boundaries = $this->mainform_model->get_time_boundaries($value->login);
	  $current_time = time();
	  $fsb = 0;

		  if (count($account_time_boundaries) == 0)
			{
		          fwrite($f,"Now:".date('H:i:s d-m-Y',$current_time)." ".$value->login.": periods havent been calculated!\r\n");
			  $calculate = FALSE;
			}
		  else
		       {
			  fwrite($f,"Now:".date('H:i:s d-m-Y',$current_time)." Periods: ".date('H:i:s d-m-Y',$account_time_boundaries[0]->tp_start)." - ".date('H:i:s d-m-Y',$account_time_boundaries[0]->tp_end)." Rolover: ".date('H:i:s d-m-Y',$account_time_boundaries[0]->rolover_start)." - ".date('H:i:s d-m-Y',$account_time_boundaries[0]->rolover_end)."\r\n");	
			  if ($account_time_boundaries[0]->second_day == 1)	
			  {
			            fwrite($f,"Now:".date('H:i:s d-m-Y',$current_time)." ".$value->login.": second day of the rolover!\r\n");
				    $calculate = FALSE;
	                 	    $this->mainform_model->set_color_account_status($value->login,2);   // set the color - rolover is continuing

				    // only not closed because of stopout  accounts are participated into divide
			            $stopout_close = $this->mainform_model->get_stopout_flag($value->login);

				$fp = @fsockopen("80.93.48.133", 10025, $errno, $errstr, 30);
				if (!$fp)
				{
					$fp = $this->reconnect($value->login);
					if (!$fp) 	
					   {
						error_log("\r\n".date('H:i:s d-m-Y')." Connection failed before correction - ".$value->login." termination aborted\r\n\r\n", 3, "test_engine.log");
						$this->mainform_model->clear_second_day_flag($value->login);
						return NULL;
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

							echo "Correction start \r\n";

				 $number = $this->correct_accounts($fp);

							echo "Correction end \r\n";
			// closing the socket
			    $out = "exit correction\r\n";
			    fwrite($fp, $out);
			    @fclose($fp);

			    if ($stopout_close[0]->stopout_close != "1")
			    {
			                $stopout_flag = $this->mainform_model->get_stopout_share_flag($value->login);
					    if (($this->stopout_status($value->login,'from general') != "999999006") AND ($this->stopout_status($value->login,'from general already') != "1"))
						  if ($stopout_flag[0]->stopout_flag != "1")
						  {
						  // telnet add crash analyze
						    $ret = $this->stopout('DEL',$value->login,NULL,NULL,"from general (divide)");		 
						    if (strlen($ret) == 0)
						       {
							    $this->mainform_model->set_failed_stopout_flag($value->login);
							    $this->mainform_model->set_divide_denial_flag($value->login);
							    $this->mainform_model->clear_divide_flag($value->login);


							    $sl =  $this->mainform_model->select_from_stopout_list($value->login,'DEL');

							    $this->mainform_model->clear_divide_flag($value->login);
							    if (count($sl) == 0) // check whether it pesents in table or not
							    {
								    $data['number'] = $value->login;
								    $data['fsb']    = '';
								    $data['md']     = '';
								    $data['action'] = 'DEL';
								    $data['fail_datetime'] = date('U');
								    $this->mainform_model->add_stopout_list($data);
							    }
						       }
						   elseif (trim($ret) == "999999990")
						       {
						            echo "Telnet is not connected on account ".$value->login."\r\n";
							    $this->mainform_model->set_failed_stopout_flag($value->login);
							    $this->mainform_model->set_divide_denial_flag($value->login);
							    $this->mainform_model->clear_divide_flag($value->login);


							    $sl =  $this->mainform_model->select_from_stopout_list($value->login,'DEL');

							    if (count($sl) == 0) // check whether it pesents in table or not
							    {
								    $data['number'] = $value->login;
								    $data['fsb']    = '';
								    $data['md']     = '';
								    $data['action'] = 'DEL';
								    $data['fail_datetime'] = date('U');
								    $this->mainform_model->add_stopout_list($data);
							    }
						       }
						  }
						  else
						   fwrite($f,date('H:i:s d-m-Y',$current_time)." ".$value->login." Deleting denied becuse of stopout flag\r\n");
	
					$fp = @fsockopen("80.93.48.133", 10025, $errno, $errstr, 30);
					if (!$fp)
					{
						$fp = $this->reconnect($value->login);
						if (!$fp) 	
						   {
							error_log("\r\n".date('H:i:s d-m-Y')." Connection failed before divide - ".$acc_number." termination aborted\r\n\r\n", 3, "test_engine.log");
							$this->mainform_model->set_failed_divide_flag($value->login);
							$this->mainform_model->clear_divide_flag($value->login);
							return NULL;
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

					    $divide     	   = $this->mainform_model->get_divide_flag($value->login);	
					    $failed_stopout_flag   = $this->mainform_model->get_failed_stopout_flag($value->login);	

					    if ($divide[0]->divide == "0" AND $failed_stopout_flag[0]->failed_stopout != "0")
						{
						    $GLOBALS['divide_flag'] = 1;

						    $divide_denial = $this->mainform_model->get_divide_denial_flag($value->login);
						    if ($divide_denial[0]->divide_denial == "0")
						    {
							    fwrite($f,date('H:i:s d-m-Y',$current_time)." ".$value->login." - SECOND DAY divide start\r\n");
							    $profit_per_ps = $this->divide_single($fp,$value->login);	
							    echo "Profit per ps\r\n";
							    print_r($profit_per_ps);
							    fwrite($f,date('H:i:s d-m-Y',$current_time)." ".$value->login." - SECOND DAY divide done\r\n");
							    $GLOBALS['divide_flag'] = 0;			    
							    $this->mainform_model->set_divide_flag_second_day($value->login);
						    }                                                                                      
						    else
						    {       
							    $this->mainform_model->clear_divide_flag($value->login);
							    $this->mainform_model->clear_second_day_flag($value->login);
							    fwrite($f,date('H:i:s d-m-Y',$current_time)." ".$value->login." - SECOND DAY divide DENIAL\r\n");
						    }
						    $reinv = $this->mainform_model->obtain_reinv($value->login);
						    if ($reinv[0]->reinv == 1)   
						      {
							    if (count($profit_per_ps) > 0)   // earn is present 
							         $this->generate_reinv_requests($profit_per_ps,$value->login);
						      }
				
		                                }
					    $this->other_requests_single($fp,$value->login);

					//  float stopout balance

					    $debt       = $this->mainform_model->obtain_debt($value->login);
					    $debt_inout_stopout = $this->mainform_model->obtain_debt_inout_stopout($value->login);
					    $fsb_old    = $this->mainform_model->obtain_fsb($value->login);

		                            echo " d_i_s second day=".$debt_inout_stopout[0]->debt_inout_stopout." debt second day=".$debt[0]->debt."\r\n";

					    if ($debt[0]->debt >= 0)
						$fsb = $this->obtain_balance($fp,$value->login,'from general second day');
					    else
						{
						   if ($debt_inout_stopout[0]->debt_inout_stopout < 0)
						      {
							// correcting debt   
							$earn = $this->mainform_model->obtain_balance_before_divide($value->login);
							$e  = $earn[0]->earn - $debt_inout_stopout[0]->debt_inout_stopout;

							echo "Second day rolover debt correction : e = ".$e."\r\n";

							if ($e > 0)
							{
								$correction = ($debt_inout_stopout[0]->debt_inout_stopout/$e)*$debt[0]->debt;
								echo "SECON DAY: earn = ".$e." summ=".$debt_inout_stopout[0]->debt_inout_stopout." debt=".$debt[0]->debt."\r\n";
								echo "Debt on account ".$value->login." corrected in value of ".round($correction,2)."\r\n";
								$this->mainform_model->correct_debt($value->login,abs($correction));
								$this->mainform_model->correct_debt_for_trade($value->login,abs($correction));
	
							        $debt       = $this->mainform_model->obtain_debt($value->login);

								echo "SECOND DAY Balance =".$this->obtain_balance($fp,$value->login,'for log')." debt =".$debt[0]->debt."\r\n";
							}
						

							$fsb = $this->obtain_balance($fp,$value->login,'for fsb') - $debt[0]->debt;
					       }
					       else
						$fsb = $this->obtain_balance($fp,$value->login,'for fsb') - $debt[0]->debt;

					}
		
				    fwrite($f,"SECOND DAY ".date('H:i:s d-m-Y',$current_time)." ".$value->login." - fsb =  ".trim($fsb)."\r\n");
			
				    if ($fsb > 0)
					    $this->mainform_model->write_fsb($value->login,$fsb);
				    else
					    fwrite($f,date('H:i:s d-m-Y',$current_time)." ".$value->login." zero fsb value write deny!\r\n");			

	                            $this->mainform_model->write_debt_inout_stopout_zero($value->login);
	
			        //  add to stopout list
				    $max_dip         = $this->mainform_model->obtain_max_dip($value->login);
				    $md              = $max_dip[0]->max_dip;
				  
			            $stopout_flag = $this->mainform_model->get_stopout_share_flag($value->login);
		                    $stopout_close = $this->mainform_model->get_stopout_flag($value->login); 		//re-request stopout flag - may change in case of implementing close request
 		
				 if ($stopout_close[0]->stopout_close != "1")
				  if ($stopout_flag[0]->stopout_flag != "1")
				  {
	             		    $ret = $this->stopout('ADD',$value->login,$fsb,$md,"from general (divide)");		 
				    if (strlen($ret) == 0)
				       {
					    $this->mainform_model->set_failed_stopout_flag($value->login);
					    $this->mainform_model->clear_divide_flag($value->login);
					    $this->mainform_model->set_divide_denial_flag($value->login);
					    $sl =  $this->mainform_model->select_from_stopout_list($value->login,'ADD');
					    if (count($sl) == 0) // check whether it pesents in table or not
					    {
						    $data['number'] = $value->login;
						    $data['fsb']    = $fsb;
						    $data['md']     = $md;
						    $data['action'] = 'ADD';
						    $data['fail_datetime'] = date('U');
						    $this->mainform_model->add_stopout_list($data);
					    }
				       }
				    elseif (trim($ret) == "999999990")
				       {
				            echo "Telnet is not connected on account ".$value->login."\r\n";
					    $this->mainform_model->set_divide_denial_flag($value->login);
					    $this->mainform_model->clear_divide_flag($value->login);
					    $this->mainform_model->set_failed_stopout_flag($value->login);
					    $sl =  $this->mainform_model->select_from_stopout_list($value->login,'ADD');
					    if (count($sl) == 0) // check whether it pesents in table or not
					    {

						    $data['number'] = $value->login;
						    $data['fsb']    = $fsb;
						    $data['md']     = $md;
						    $data['action'] = 'ADD';
						    $data['fail_datetime'] = date('U');
						    $this->mainform_model->add_stopout_list($data);
					   }
				       }
				  }
				  else
				    fwrite($f,date('H:i:s d-m-Y',$current_time)." ".$value->login." - Addnig denied becuse of stopout flag\r\n");

				// closing the socket
				    $out = "exit 2nd day rolover\r\n";
				    fwrite($fp, $out);
				    @fclose($fp);


			            fwrite($f,"Now:".date('H:i:s d-m-Y',$current_time)." ".$value->login.":SECOND DAY end\r\n");

			       // calculate the next trade period
		  		  $this->calculate_tp($value->login);

			    } // stopout_status != 1
		      }
		  }


	 if ($calculate)
	 {
		 $this->mainform_model->set_account_status($value->login,1,$account_time_boundaries[0]->tp_start,$account_time_boundaries[0]->tp_end,$account_time_boundaries[0]->rolover_start,$account_time_boundaries[0]->rolover_end);
                 	 $this->mainform_model->set_color_account_status($value->login,1);
	  if ($current_time > $account_time_boundaries[0]->tp_start AND $current_time < $account_time_boundaries[0]->tp_end)
		{
		 fwrite($f,$value->login." - trade (nothing to do)\r\n");
		 fwrite($f,$this->stopout_status($value->login,'from general')."\r\n");
		 $ret_rolover = trim($this->stopout_status($value->login,'from general'));
			   if ($ret_rolover =="999999006")
				{
					 fwrite($f,"Adding account to stopout\r\n");

				    $fsb 	     = $this->mainform_model->obtain_fsb($value->login);
  
				    $max_dip         = $this->mainform_model->obtain_max_dip($value->login);
				    $md              = $max_dip[0]->max_dip;
		                    $stopout_close = $this->mainform_model->get_stopout_flag($value->login);

				    fwrite($f,"md=".$md." stopout_flag =".$stopout_close[0]->stopout_close."\r\n");

			            $stopout_flag = $this->mainform_model->get_stopout_share_flag($value->login);

					if ($stopout_close[0]->stopout_close != "1")
						  if ($stopout_flag[0]->stopout_flag != "1")
						   {
						    $ret = $this->stopout('ADD',$value->login,$fsb[0]->fsb,$md,"from general (trade)");		 
						    if (strlen($ret) == 0)
						       {
							    $this->mainform_model->set_failed_stopout_flag($value->login);
							    $this->mainform_model->clear_divide_flag($value->login);
							    $this->mainform_model->set_divide_denial_flag($value->login);
							    $sl =  $this->mainform_model->select_from_stopout_list($value->login,'ADD');
							    if (count($sl) == 0) // check whether it pesents in table or not
							    {
								    $data['number'] = $value->login;
								    $data['fsb']    = $fsb[0]->fsb;
								    $data['md']     = $md;
								    $data['action'] = 'ADD';
								    $data['fail_datetime'] = date('U');
								    $this->mainform_model->add_stopout_list($data);
							    }
						       }
						    elseif (trim($ret) == "999999990")
						       {
						            echo "Telnet is not connected on account ".$value->login."\r\n";
							    $this->mainform_model->set_divide_denial_flag($value->login);
							    $this->mainform_model->clear_divide_flag($value->login);
							    $this->mainform_model->set_failed_stopout_flag($value->login);
							    $sl =  $this->mainform_model->select_from_stopout_list($value->login,'ADD');
							    if (count($sl) == 0) // check whether it pesents in table or not
							    {

								    $data['number'] = $value->login;
								    $data['fsb']    = $fsb[0]->fsb;
								    $data['md']     = $md;
								    $data['action'] = 'ADD';
								    $data['fail_datetime'] = date('U');
								    $this->mainform_model->add_stopout_list($data);
							     }
						       }


						   }
						  else
						    fwrite($f,date('H:i:s d-m-Y',$current_time)." ".$value->login." Addnig denied becuse of stopout flag\r\n");
					else
						    fwrite($f,date('H:i:s d-m-Y',$current_time)." ".$value->login." Addnig denied becuse of stopout share flag\r\n");

				}
				elseif ($ret_rolover == "999999990" OR strlen($ret_rolover)==0)
				{
			            echo "Failed stopout before rolover: account ".$value->login."\r\n";
				    $fsb 	     = $this->mainform_model->obtain_fsb($value->login);
				    $max_dip         = $this->mainform_model->obtain_max_dip($value->login);
				    $md              = $max_dip[0]->max_dip;
				    $this->mainform_model->set_divide_denial_flag($value->login);
				    $this->mainform_model->clear_divide_flag($value->login);
				    $this->mainform_model->set_failed_stopout_flag($value->login);
				    $sl =  $this->mainform_model->select_from_stopout_list($value->login,'ADD');
				    if (count($sl) == 0) // check whether it pesents in table or not
				    {
					    $data['number'] = $value->login;
					    $data['fsb']    = $fsb[0]->fsb;
					    $data['md']     = $md;
					    $data['action'] = 'ADD';
					    $data['fail_datetime'] = date('U');
					    $this->mainform_model->add_stopout_list($data);
				     }

				}
		}
	  elseif ($current_time > $account_time_boundaries[0]->rolover_start AND $current_time < $account_time_boundaries[0]->rolover_end)
		{
			 $id = $account_time_boundaries[0]->id;
                 	 $this->mainform_model->set_second_day_flag($id,$value->login);
                 	 $this->mainform_model->set_divide_flag($id,$value->login);
                 	 $this->mainform_model->set_color_account_status($value->login,2);
		fwrite($f,date('H:i:s d-m-Y',$current_time)." ".$value->login."- Rolover start\r\n");
		$divide_denial = $this->mainform_model->get_divide_denial_flag($value->login);
		$GLOBALS['divide_flag'] = 1;
		if ($account_time_boundaries[0]->divide == 0)
		{
		    // only not closed because of stopout  accounts are participated into divide
	            $stopout_close = $this->mainform_model->get_stopout_flag($value->login);
 		    if ($stopout_close[0]->stopout_close != "1")
		    {

				 $this->mainform_model->set_divide_flag($account_time_boundaries[0]->id,$value->login);

				$fp = @fsockopen("80.93.48.133", 10025, $errno, $errstr, 30);
				if (!$fp)
				{
				$fp = $this->reconnect($value->login);
					if (!$fp) 	
					   {
						error_log("\r\n".date('H:i:s d-m-Y')." Connection failed before rolover first day - ".$value->login." termination aborted\r\n\r\n", 3, "test_engine.log");
						
						// clear second day flag
						$this->mainform_model->clear_second_day_flag($value->login);
						return NULL;
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

			    $this->requests12_single($fp,$value->login);

			//  close all orders
			    $this->close_orders($fp,$value->login);		 

			// if account is present there, delete it from stopout list

		           $stopout_close = $this->mainform_model->get_stopout_flag($value->login);
		           $stopout_flag = $this->mainform_model->get_stopout_share_flag($value->login);

			   if ($stopout_close[0]->stopout_close != "1")
				   if (($this->stopout_status($value->login,'from general absent') != "999999006" ) AND ($this->stopout_status($value->login,'from general already') != "1"))
					  if ($stopout_flag[0]->stopout_flag != "1")
					  {
					    $ret = $this->stopout('DEL',$value->login,NULL,NULL,"from general (rolover)");		 
						    if (strlen($ret) == 0)
						       {
							    $this->mainform_model->set_failed_stopout_flag($value->login);
							    $this->mainform_model->clear_divide_flag($value->login);
							    $this->mainform_model->set_divide_denial_flag($value->login);
							    $sl =  $this->mainform_model->select_from_stopout_list($value->login,'DEL');

							    if (count($sl) == 0) // check whether it pesents in table or not
							    {
								    $data['number'] = $value->login;
								    $data['fsb']    = '';
								    $data['md']     = '';
								    $data['action'] = 'DEL';
								    $data['fail_datetime'] = date('U');
								    $this->mainform_model->add_stopout_list($data);
							    }
						       }
						   elseif (trim($ret) == "999999990")
						       {
						            echo "Telnet is not connected on account ".$value->login."\r\n";
							    $this->mainform_model->set_divide_denial_flag($value->login);
							    $this->mainform_model->clear_divide_flag($value->login);
							    $this->mainform_model->set_failed_stopout_flag($value->login);

							    $sl =  $this->mainform_model->select_from_stopout_list($value->login,'DEL');
							    if (count($sl) == 0) // check whether it pesents in table or not
							    {

								    $data['number'] = $value->login;
								    $data['fsb']    = '';
								    $data['md']     = '';
								    $data['action'] = 'DEL';
								    $data['fail_datetime'] = date('U');
								    $this->mainform_model->add_stopout_list($data);
							    }
						       }
					  }
					  else
					    fwrite($f,date('H:i:s d-m-Y',$current_time)." ".$value->login." Deleting denied because of stopout flag\r\n");

			    fwrite($f,date('H:i:s d-m-Y',$current_time)." ".$value->login." - divide start\r\n");
			    $divide_denial = $this->mainform_model->get_divide_denial_flag($value->login);
			    if ($divide_denial[0]->divide_denial == "0")
			    {
				    fclose($f);
				    $profit_per_ps = $this->divide_single($fp,$value->login);		 
			  	    $f = fopen("f.txt","w+") or die('Cant open!');
				    fwrite($f,date('H:i:s d-m-Y',$current_time)." ".$value->login." - divide done\r\n");
			    
				    print_r($profit_per_ps);			 
				// reinvestment
				    $reinv = $this->mainform_model->obtain_reinv($value->login);
				    if ($reinv[0]->reinv == 1)   
				      {
					    if (count($profit_per_ps) > 0)   // earn is present 
					         $this->generate_reinv_requests($profit_per_ps,$value->login);
				      }
			    }
			    else
			    {
				    $this->mainform_model->clear_divide_flag($value->login);
				    fwrite($f,date('H:i:s d-m-Y',$current_time)." ".$value->login." - first day divide DENIAL\r\n");
			    }


			    $this->other_requests_single($fp,$value->login);

			//  float stopout balance

			    $debt       = $this->mainform_model->obtain_debt($value->login);
			    $fsb_old    = $this->mainform_model->obtain_fsb($value->login);
			    $debt_inout_stopout = $this->mainform_model->obtain_debt_inout_stopout($value->login);

			    fwrite($f,date('H:i:s d-m-Y',$current_time)." ".$value->login." - obtain debt - ".$debt[0]->debt."\r\n");
			    fwrite($f,date('H:i:s d-m-Y',$current_time)." ".$value->login." - obtain fsb_old - ".$fsb_old[0]->fsb."\r\n");
			    fwrite($f,date('H:i:s d-m-Y',$current_time)." ".$value->login." - obtain debt_inout_stopout - ".$debt_inout_stopout[0]->debt_inout_stopout."\r\n");

                            echo " d_i_s=".$debt_inout_stopout[0]->debt_inout_stopout." debt=".$debt[0]->debt."\r\n";

			    if ($debt[0]->debt >= 0)
				$fsb = $this->obtain_balance($fp,$value->login,'for fsb');
			    else
				{
				   if ($debt_inout_stopout[0]->debt_inout_stopout < 0)
				      {
						// correcting debt   
						$earn = $this->mainform_model->obtain_balance_before_divide($value->login);
						$e  = $earn[0]->earn - $debt_inout_stopout[0]->debt_inout_stopout;

						echo "e = ".$e."\r\n";

						if ($e > 0)
						{
							$correction = ($debt_inout_stopout[0]->debt_inout_stopout/$e)*$debt[0]->debt;
							echo "e = ".$e." d_i_s=".$debt_inout_stopout[0]->debt_inout_stopout." debt=".$debt[0]->debt."\r\n";
							echo "Debt on account ".$value->login." corrected in value of ".round($correction,2)."\r\n";
							$this->mainform_model->correct_debt($value->login,abs($correction));
							$this->mainform_model->correct_debt_for_trade($value->login,abs($correction));
	
						        $debt       = $this->mainform_model->obtain_debt($value->login);

							echo "Balance =".$this->obtain_balance($fp,$value->login,'for log')." debt =".$debt[0]->debt."\r\n";
						}
						

					$fsb = $this->obtain_balance($fp,$value->login,'for fsb') - $debt[0]->debt;
				       }
				       else
					$fsb = $this->obtain_balance($fp,$value->login,'for fsb') - $debt[0]->debt;


				}

			    fwrite($f,date('H:i:s d-m-Y',$current_time)." ".$value->login." - fsb =  ".$fsb."\r\n");
			    if ($fsb > 0 )
				    $this->mainform_model->write_fsb($value->login,$fsb);
			    else
				    fwrite($f,date('H:i:s d-m-Y',$current_time)." ".$value->login." zero fsb value write deny!\r\n");			

			    /////////////////////////////////////////  for the second day rolover
			    /////////////////                   $this->mainform_model->write_balance_before_divide_zero($value->login);
			    /////////////////////////////////////////

                            $this->mainform_model->write_debt_inout_stopout_zero($value->login);

		        //  add to stopout list
			    $max_dip         = $this->mainform_model->obtain_max_dip($value->login);
			    $md              = $max_dip[0]->max_dip;

		            $stopout_flag = $this->mainform_model->get_stopout_share_flag($value->login);
		            $stopout_close = $this->mainform_model->get_stopout_flag($value->login); 		//re-request stopout flag - may change in case of implementing close request
		
			    if ($stopout_close[0]->stopout_close != "1")
				{
				  if ($stopout_flag[0]->stopout_flag != "1")
				  {
				   $ret = $this->stopout('ADD',$value->login,$fsb,$md,"from general (rolover)");		 
						    if (strlen($ret) == 0)
						       {
							    $this->mainform_model->set_failed_stopout_flag($value->login);
							    $this->mainform_model->clear_divide_flag($value->login);
							    $this->mainform_model->set_divide_denial_flag($value->login);
							    $sl =  $this->mainform_model->select_from_stopout_list($value->login,'ADD');
							    if (count($sl) == 0) // check whether it pesents in table or not
							    {
								    $data['number'] = $value->login;
								    $data['fsb']    = $fsb[0]->fsb;
								    $data['md']     = $md;
								    $data['action'] = 'ADD';
								    $data['fail_datetime'] = date('U');
								    $this->mainform_model->add_stopout_list($data);
							    }
						       }
						   elseif (trim($ret) == "999999990")
						       {
						            echo "Telnet is not connected on account ".$value->login."\r\n";
							    $this->mainform_model->set_divide_denial_flag($value->login);
							    $this->mainform_model->clear_divide_flag($value->login);
							    $this->mainform_model->set_failed_stopout_flag($value->login);
							    $sl =  $this->mainform_model->select_from_stopout_list($value->login,'ADD');
							    if (count($sl) == 0) // check whether it pesents in table or not
							    {

								    $data['number'] = $value->login;
								    $data['fsb']    = $fsb;
								    $data['md']     = $md;
								    $data['action'] = 'ADD';
								    $data['fail_datetime'] = date('U');
								    $this->mainform_model->add_stopout_list($data);
							    }
						       }
				  }
				  else
				        fwrite($f,date('H:i:s d-m-Y',$current_time)." ".$value->login." Deleting denied becuse of stopout flag\r\n");
				}

			    $this->stat_after_divide($fp,$value->login);

			// closing the socket
			    $out = "exit\r\n";
			    fwrite($fp, $out);
			    @fclose($fp);
		    } // stopout_status != 1

		$GLOBALS['divide_flag'] = 0;
		}
		else
			 fwrite($f,"Flag is already set!\r\n");
		}
	  elseif ($current_time > $account_time_boundaries[0]->rolover_end)
		{
			 fwrite($f,$value->login." - non-existent day ".date('H:i:s d-m-Y',$current_time)."\r\n");
                 	 $this->mainform_model->set_color_account_status($value->login,3);
                }

	else    {
               	 $this->mainform_model->set_color_account_status($value->login,3);
		 fwrite($f,$value->login." - trade period not found\r\n");
		 $this->mainform_model->set_account_status($value->login,0,NULL,NULL,NULL,NULL);
	        }
	 }
	} // foreach
	fclose($f);
    }   // index

} // class

?>