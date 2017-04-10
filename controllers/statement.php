<?php
class statement extends CI_Controller {

	function __construct()
    {
		parent::__construct(); 
		$this->load->model('mainform_model');
		
    }
    public function index()
    {

        $account_array      = $this->mainform_model->get_all_accounts();

	foreach ($account_array as $value)
	{
		$this->mainform_model->delete_old_statement($value->login);
		$time_start = microtime(true);
		$later = TRUE;
		$later1 = TRUE;
		$later2 = TRUE;
		$write_history = TRUE;


		$fp = fsockopen("80.93.48.133", 10025, $errno, $errstr, 30);
			if (!$fp)
				{
					 $fp = $this->reconnect($value->login);
					if (!$fp) 	
					   {
						error_log("\r\n".date('H:i:s d-m-Y')." Connection failed before receivig statement - ".$value->login." termination aborted\r\n\r\n", 3, "test_engine.log");
						return NULL;
					   }
					 $later = FALSE;

				}
			else
				{
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
				}

			    $out = '65 '.$value->login;
                           
		    fwrite($fp, $out."\r\n");
		    $buffer = fgets($fp,128);
		    $buffer = fgets($fp,128);

		if (substr($buffer,0,4) =='9999')
		{
			$time_end = microtime(true);
			$time_result = $time_end - $time_start;
		 	$later = FALSE;
			$out = "exit\r\n";
		        fwrite($fp, $out);
			fclose($fp);
		}
       else
		{

					$buffer = substr($buffer,0,strlen($buffer)-2);
					        $crc = $buffer;

						if (!$fp)
						{
							$fp = $this->reconnect($value->login);
							if (!$fp) 	
							   {
								error_log("\r\n".date('H:i:s d-m-Y')." Connection failed during receivig statement - ".$value->login." termination aborted\r\n\r\n", 3, "test_engine.log");
								return NULL;
							   }

							$later = FALSE;
						}
						$out = '1';

						    fwrite($fp, $out."\r\n");

						    $delay_command = 0;
						    while (1 == 1)
						       {
							$buffer = fgets($fp,128);
							if (!isset($fp))
								return NULL;

								if (strlen($buffer) > 0)
								{
								 if ($buffer == "900000009")
								 {
									$later2 = FALSE;
									 echo "Error ".$buffer."\r\n";
								 	break;
								 }
								 break;
								}
								if ($delay_command > 8000)
								{
									$time_end = microtime(true);
									$time_result = $time_end - $time_start;
									echo ("delay exit  ".$value->login." time = ".$time_result."\r\n");
									echo "Disconnect delay start\r\n";	   
								    $out = "exit\r\n";
								    fwrite($fp, $out);
								    $later1 = FALSE;
								echo "Disconnect delay finished\r\n";	   
								break;
								}
						         $delay_command++;
							}

						 if ($later2)
						{
						$delay = 0;
						$f_history = fopen("history.txt","w+");
						while (1 == 1)
							{
							if (!$fp)
							{
								$fp = $this->reconnect($value->login);
								if (!$fp) 	
								   {
									error_log("\r\n".date('H:i:s d-m-Y')." Connection failed while receivig history - ".$value->login." termination aborted\r\n\r\n", 3, "test_engine.log");
									return NULL;
								   }

									 $later1 = FALSE;
							}
						$buffer = fgets($fp);
						if ($delay > 40000)
						{
							$time_end = microtime(true);
							$time_result = $time_end - $time_start;
							echo ("delay exit  ".$value->login." time = ".$time_result."\r\n");
							echo "Disconnect delay start\r\n";	   
						    $out = "exit\r\n";
						    fwrite($fp, $out);
						    fclose($fp);
						    $later1 = FALSE;
						echo "Disconnect delay finished\r\n";	   
						break;
						}
						if (substr($buffer,0,4) == '[END')
							{ 
							break;
								}
							//fwrite($f,$buffer);
							fwrite($f_history,substr($buffer,0,strlen($buffer)-2));
							 $delay++;
					        } // while 1 == 1

						fclose($f_history);
					        if ($later1)
						{
					  		$f2 = fopen("history.txt","r+") or die('Cant open history!');
					  		$f1 = fopen($value->login.".zip","w+") or die('Cant open zip!');
							while(!feof($f2))
							{
							 $buffer = fread($f2,2);
							 fwrite($f1,chr(base_convert($buffer,16,10)));
							}
	
							fclose($f2);	
							fclose($f1);	
							$zip = new ZipArchive;
							if ($zip->open($value->login.'.zip') === true)
							{
								$name = $value->login.".txt";
								$zip->renameName('temp.fil',$name);
								if (!$zip->extractTo('temp',$name))
									die('Error!');
								$zip->close();
							}
							else
							{
//								echo "Cant open zip!\r\n";
								echo $value->login." - zero history!\r\n";
								$write_history = FALSE;
							}
						// parsing file to the database
						if ($write_history)
							{
								$f_text = fopen("temp/".$value->login.".txt","r+");
								if ($f_text)
								{
									$i = 0;
									while (!feof($f_text))
									{
									  $s = fgets($f_text, 1024);
									  if (strlen($s) > 2)
										{
										  list($order,$number,$symbol,$cmd,$volume,$open_time,$open_price,$sl,$tp,$close_time,$commission,$storage,$close_price,$profit,$taxes,$comment)=preg_split('/©/',$s);
		                                                                  mysql_query("INSERT INTO pamm_statement VALUES('$order','$number','$cmd','$symbol','$volume','$open_time','$open_price','$sl','$tp','$close_time','$commission','$storage','$close_price','$profit','$taxes','$comment','','')");
										  $i++;
										}
									}
									fclose($f_text);
									unlink($value->login.'.zip');
									unlink('temp/'.$value->login.'.txt');
								}
								else
									echo "Cant open txt file!\r\n";							
							}
						}
					}  // later2
				   } //later1
	  	   $statement = $this->mainform_model->get_statement_common($value->login);
	  	   $number_inv = 0;
		   foreach ($statement as $key=>$value)
			{
			   if (substr(trim($value->comment),0,4) == "join")
				{
				  $number_inv++;
				}
			   elseif  (substr(trim($value->comment),0,6) == "unjoin")
				{
				  $number_inv--;
				}

			if (trim($value->comment) == 'div profit v1')
				{
			          $key_start = $key+1;
				  $key_end = $key + $number_inv +1;
				  for($j = $key_start;$j<= $key_end;$j++)
				  {
					$n = $statement[$j];
					if (substr(trim($n->comment),0,10) == "correction")
					{
				 	 $this->mainform_model->set_failed_divide_attribute(1,$value->order);
					}
				  }
				}
			if (trim($value->comment) == 'div profit v3')
				{
			          $key_start = $key+1;
				  $key_end = $key + $number_inv +1;
				  for($j = $key_start;$j<= $key_end;$j++)
				  {
				    if (isset($statement[$j]))
				     {
					$n = $statement[$j];
					if (substr(trim($n->comment),0,10) == "correction")
					{
				 	 $this->mainform_model->set_failed_divide_attribute(1,$value->order);
					}
				     }
				  }
				}
			if (trim($value->comment) == 'div loss v2')
				{
			          $key_start = $key+1;
				  $key_end = $key + $number_inv +1;
				  for($j = $key_start;$j<= $key_end;$j++)
				  {
				    if (isset($statement[$j]))
				     {
					$n = $statement[$j];
					if (substr(trim($n->comment),0,10) == "correction")
					{
				 	 $this->mainform_model->set_failed_divide_attribute(1,$value->order);
					}
				     }
				  }
				}
			if (trim($value->comment) == 'div profit v41')
				{
			          $key_start = $key+1;
				  $key_end = $key + $number_inv +1;                                  
				  for($j = $key_start;$j<= $key_end;$j++)
				  {
				    if (isset($statement[$j]))
				     {
					$n = $statement[$j];
					if (substr(trim($n->comment),0,10) == "correction")
					{
				 	 $this->mainform_model->set_failed_divide_attribute(1,$value->order);
					}
				     }
				  }
				}

			if (trim($value->comment) == 'div profit v42')
				{
			          $key_start = $key+1;
				  $key_end = $key + $number_inv +1;                                  
				  for($j = $key_start;$j<= $key_end;$j++)
				  {
				    if (isset($statement[$j]))
				     {
					$n = $statement[$j];
					if (substr(trim($n->comment),0,10) == "correction")
					{
				 	 $this->mainform_model->set_failed_divide_attribute(1,$value->order);
					}
				     }
				  }
				}


			}


		} // for

	} // index
} // class
?>