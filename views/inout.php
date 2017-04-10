<?php
include("inout_header.php");
		$inoutdebt = $this->mainform_model->obtain_inoutdebt($acc_number);
		$debt = $this->mainform_model->obtain_debt($acc_number);
		$state = $this->mainform_model->get_account_active($acc_number);
                if ($debt[0]->debt < 0)	
		{	
	                if ($inoutdebt[0]->inoutdebt == 0)
			      {
				$active = "onclick='return false;'";		
				print("Inouts are prohibited because of debt ".$debt[0]->debt);
			      }
			else   
				$active = "";
		}
		else
			$active = "";

		if ($state[0]->active != "1")
			     {
				print("Inouts are prohibited because of account status!");
				$active = "onclick='return false;'";		
		             }

		print('<table id="requests_table" style="border:1px solid;border-spacing:0;border-collapse:collapse;">');
		print("<tr id='header'><td style='width:200px;border:1px solid;text-align:center;'>Дата заявки</td><td style='width:50px;border:1px solid;text-align:center;'>ID</td><td style='width:100px;border:1px solid;text-align:center;'>РАММ счет</td><td style='width:200px;border:1px solid;text-align:center;'>Комментарий</td><td style='width:50px;border:1px solid;text-align:center;'>Зачисление</td><td style='width:50px;border:1px solid;text-align:center;'>Снятие</td><td style='width:100px;border:1px solid;text-align:center;'>Статус</td><td style='width:50px;border:1px solid;text-align:center;'>Срочность</td></tr>");		
	        foreach ($requests as $value)
		{ 

			$urgent = ($value->request_urgent) ? "Срочная" : "Обычная";
			switch ($value->request_type):
				case('3'):				
					$summ_neg = "";
					$summ_pos = $value->request_summ;
                                				break;
				case('5'):
					$summ_neg = "";
					$summ_pos = $value->request_summ;
                                				break;
				break;
				case('6'):
					$summ_neg = "";
					$summ_pos = $value->request_summ;
                                				break;
				break;
				case('7'):
					$summ_pos = "";
					$summ_neg = $value->request_summ;
                                				break;
				break;
				case('8'):
					$summ_pos = "";
					$summ_neg = $value->request_summ;
                                				break;
				break;
				case('11'):
					if ($value->request_summ != 0 )   //  unjoint upon request
					{
						$summ_pos = "";
						$summ_neg = $value->request_summ;
					}
					else                   		// forced unjoint
					{
						$summ_pos = "";
						$summ_neg = "";
					}

				break;
				case('12'):
					$summ_pos = "";
					$summ_neg = $value->request_summ;
                                				break;
				break;
				case('13'):
					$summ_pos = "";
					$summ_neg = $value->request_summ;
				break;
				case('14'):
					$summ_pos = $value->request_summ;
					$summ_neg = "";
				break;
				case('15'):
					$summ_pos = "";
					$summ_neg = $value->request_summ;
                                				break;
				break;
				case('16'):
					$summ_pos = "";
					$summ_neg = $value->request_summ;
                                				break;
				break;


			endswitch;
			
			print("<tr><td style='width:200px;border:1px solid;text-align:center;'>".$value->request_date."</td><td style='width:50px;border:1px solid;text-align:center;'>".$value->request_cid."</td><td style='width:100px;border:1px solid;text-align:center;'>".$value->request_acc_number."</td><td style='width:200px;border:1px solid;text-align:center;'>".$value->request_comment."</td><td style='width:50px;border:1px solid;text-align:center;'>".$summ_pos."</td><td style='width:50px;border:1px solid;text-align:center;'>".$summ_neg."</td><td style='width:100px;border:1px solid;text-align:center;'>".$value->status_word."</td><td style='width:50px;border:1px solid;text-align:center;'>".$urgent."</td></tr>");		
		}
		print("</table>");
?>
		<script type="text/javascript">
		highlightTableRows1("requests_table","","hoverRow",false);
		</script>

<BR>
<table cellspadding="5"><tr><td>
<a class="submit" <?php echo $active;?> href="/pamm/dsp/in/<?php echo $acc_number;?>"><span style="color:white;font-weight:bold;">&nbsp;&nbsp;In&nbsp;&nbsp;</span></a></td><td><a class="submit" <?php echo $active;?> href="/pamm/dsp/out/<?php echo $acc_number?>"><span style="color:white;font-weight:bold;">&nbsp;&nbsp;Out&nbsp;&nbsp;</span></a></td><td><a class="submit" href="/pamm/dsp/manage"><span style="color:white;font-weight:bold;">Return</span></a>
</td></tr></table>