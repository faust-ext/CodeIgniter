<!-- $Id: example.html,v 1.4 2006/03/27 02:44:36 pat Exp $ -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>Simple Tabber Example</title>

<script type="text/javascript" src="/frontend/js/tabber1.js"></script>
<link rel="stylesheet" href="/frontend/css/example1.css" TYPE="text/css" MEDIA="screen">
<script type="text/javascript">

/* Optional: Temporarily hide the "tabber" class so it does not "flash"
   on the page as plain HTML. After tabber runs, the class is changed
   to "tabberlive" and it will appear. */

document.write('<style type="text/css">.tabber1{display:none;}<\/style>');
</script>

</head>
<body>
<h1>Statistic on account <?php echo $acc_number?></h1>

<div class="classtab1">

     <div class="classtabtab1">
          <div>
		
	  <center>PAMM счет № <?php echo $acc_number?> [<?php echo $name?>]</center>
	  <p>Оферта PAMM счета</p>
  </div>
		<div style="height:600px;">
		  <table>
		  <tr><td style="width:400px;">Минимальный вклад (сумма начальных инвестиций)</td><td><?php echo $of_i_p?>$</td></tr>
		  <tr><td style="width:400px;">Минимальное пополнение/снятие</td><td><?php echo $min_withdraw?>$</td></tr>
		  <tr><td style="width:400px;">Торговый период</td><td><?php echo $of_t_p?> нед.</td></tr>		  
		  <tr><td style="width:400px;">Распределение прибыли</td><td><?$distr?></td></tr>		  
		  <tr><td style="width:400px;">Максимальная просадка по счету</td><td><?php echo $max_dip?></td></tr>		  
		  <tr><td style="width:400px;">Возможность досрочного вывода</td><td><?php echo $w_b?></td></tr>		  
		  <tr><td style="width:400px;">Штраф инвестора за досрочный вывод средств в торговый период</td><td><?php echo $penalty?>%</td></tr>		  
		  <tr><td style="width:400px;">Реинвестирование средств</td><td><?php echo $reinv?></td></tr>		  
		  <tr><td style="width:400px;">Открытая статистика для всех</td><td><?php echo $openstat?></td></tr>		  
		  <tr><td style="width:400px;">Открытая статистика для Инвесторов</td><td><?php echo $openstatinv?></td></tr>		  
		  </table>		  
		  </div>
     </div>


     <div class="classtabtab1">
         <div style="border-bottom:1px solid;">
	  	  <center>PAMM счет № <?php echo $acc_number?> [<?php echo $name?>]</center>
	  <p>Показатели PAMM счета</p>
	  <table cellspadding=0 cellspacing=0>
		<tr style='background-color:#999999;'><td>Ордер</td><td style='padding-left:20px;'>Время откр.</td><td style='padding-left:55px;'>Тип</td><td style='padding-left:25px;'>Кол.</td><td style='padding-left:5px;'>Пункт</td><td style='padding-left:15px;'>Цена от.</td><td style='padding-left:2px;'>SL</td><td style='padding-left:35px;'>TP</td><td style='padding-left:50px;'>Время закр.</td><td style='padding-left:65px;'>Цена закр.</td><td style='padding-left:5px;'>Нал.</td><td style='padding-left:5px;'>Своп</td><td style='padding-left:25px;text-align:right;'>Прибыль</td></tr>
	  </table>
	</div>
		<div style="height:430px;overflow-y:scroll;">
		<table>
		<?php
		   function color($value_color)
			{
			 if ($value_color < 0)
				return "<font color='red'>".$value_color."</font>";
			 else
				return "<font color='green'>".$value_color."</font>";

			}
		   function inv_search($value_number,$arr)
			{
				foreach ($arr as $key=>$value)
				{
				 if ($value['number'] == $value_number)
					return $key+1;
				}
			return ('Failed');
			}

	  	   $statement = $this->mainform_model->get_statement_common($acc_number);
		   $start_of_trade_period      =  $this->mainform_model->get_start_of_trade_period($acc_number);
		   $end_of_trade_period      =  $this->mainform_model->get_end_of_trade_period($acc_number);
		   $i = 0;
		   $fon =Array();
		   $lon =Array();
		    foreach($start_of_trade_period as $value)
			{

			 $fon[$i] = $value->first_order_number;
				$i++;
			}
		   $i = 0;
		    foreach($end_of_trade_period as $value)
			{

			 $lon[$i] = $value->last_order_number;
				$i++;
			}

		   $total_account = 0;
		   $balance_account = 0;
		   $pl = 0;
		   $deposit = 0;
		   $count_inv = 0;
		   $inv = Array();
		   $balance_ind =TRUE;
		   $count_period = 1;

		   $number_inv = 0;
		   foreach ($statement as $key=>$value)
			{
			 $print = TRUE;

			 $number_of_trade_period = $count_period;

					if (array_search($value->order,$fon) !== FALSE && substr(trim($value->comment),0,7) != "stopout")
					{
						$number_of_trade_period_1 = $number_of_trade_period;
						$s_e_tp = $this->mainform_model->get_end_tp($value->order);
						print("<tr><td colspan='13' align='left'><b>End of trade period ".$number_of_trade_period_1."</b>&nbsp;(".date('d-m-Y',$s_e_tp[0]->tp_start)." &#151; ".date('d-m-Y',$s_e_tp[0]->tp_end).")&nbsp;(Distribution Profit/Loss)</td></tr>");
					        $count_period++;
					}


			 if ($value->cmd == 'Buy ')
				{
				 $printstr = "<tr><td>".$value->order."</td><td style='padding-left:5px;'>".date('d-m-Y h:i:s',$value->open_time)."</td><td style='padding-left:5px;'>".$value->cmd."</td><td style='padding-left:5px;'>".$value->volume."</td><td style='padding-left:5px;'>".$value->symbol."</td><td style='padding-left:5px;'>".$value->open_price."</td><td style='padding-left:5px;'>".$value->sl."</td><td style='padding-left:5px;'>".$value->tp."</td><td style='padding-left:5px;'>".date('d-m-Y h:i:s',$value->close_time)."</td><td style='padding-left:5px;'>".$value->close_price."</td><td style='padding-left:25px;'>".$value->taxes."</td><td style='padding-left:25px;'>".$value->storage."</td><td style='padding-left:25px;text-align:right;'>".number_format($value->profit,2, '.', '')."</td></tr>";
				 $balance_account = $balance_account + $value->profit;
				 $pl = $pl + $value->profit;
				 $balance_ind = FALSE;
				}
			 else
			     {

				if (trim($value->comment) == 'Activation' or trim($value->comment) == 'activation account trader')
				 {
					 print("<tr><td>".$value->order."</td><td style='padding-left:5px;'>".date('d-m-Y h:i:s',$value->open_time)."</td><td style='padding-left:5px;'>".$value->cmd."</td><td colspan='9' align='right'>Manager activation</td><td style='padding-left:25px;text-align:right;'>".number_format($value->profit,2, '.', '')."</td></tr>");
	        			 print("<tr><td colspan='13' align='left'>&nbsp;</td></tr>");
				         $s_e_f_tp = $this->mainform_model->get_first_tp_start_end($acc_number);
	        			 print("<tr><td colspan='13' align='left'><b>Trade period 1</b>&nbsp;(".date('d-m-Y',$s_e_f_tp[0]->tp_start)." &#151; ".date('d-m-Y',$s_e_f_tp[0]->tp_end).")</td></tr>");
	        			 $print = FALSE;
					 $balance_account = $balance_account + $value->profit;
					 $deposit = $deposit + $value->profit;
				 }
				elseif (substr(trim($value->comment),0,13) == 'join investor')
				{

					 $inv[$count_inv]['number'] = trim(substr($value->comment,14));
					 $inv[$count_inv]['active'] = 1;
					 $printstr ="<tr><td>".$value->order."</td><td style='padding-left:5px;'>".date('d-m-Y h:i:s',$value->open_time)."</td><td style='padding-left:5px;'>".$value->cmd."</td><td colspan='9' align='right'>Join deposit investor #".count($inv)."</td><td style='padding-left:25px;text-align:right;'>".number_format($value->profit,2, '.', '')."</td></tr>";
					 $balance_account = $balance_account + $value->profit;
					 $deposit = $deposit + $value->profit;
					 $count_inv++;
				}
				elseif (substr(trim($value->comment),0,16) == 'unjoint investor')
				{

					 $inv_number = trim(substr($value->comment,17));
					 $key_s = inv_search(trim(substr(trim($value->comment),17)),$inv);
					foreach ($inv as $key2=>$value2)
					{
					 if ($value2['number'] == $inv_number)
						{
							$inv[$key2]['active'] = 0;
						}
					}

					 $key_unjoint = $key_s;
					 $printstr ="<tr><td>".$value->order."</td><td style='padding-left:5px;'>".date('d-m-Y h:i:s',$value->open_time)."</td><td style='padding-left:5px;'>".$value->cmd."</td><td colspan='9' align='right'>Unjoint deposit investor #".$key_unjoint."</td><td style='padding-left:25px;text-align:right;'>".number_format($value->profit,2, '.', '')."</td></tr>";
					 $balance_account = $balance_account + $value->profit;
					 $deposit = $deposit + $value->profit;
				}
				elseif (substr(trim($value->comment),0,15) == 'rejoin investor')
				{
					 $key_s = inv_search(trim(substr(trim($value->comment),16)),$inv);
					foreach ($inv as $key2=>$value2)
					{
					 if ($value2['number'] == $inv_number)
						{
							$inv[$key2]['active'] = 1;
						}
					}

					 $printstr ="<tr><td>".$value->order."</td><td style='padding-left:5px;'>".date('d-m-Y h:i:s',$value->open_time)."</td><td style='padding-left:5px;'>".$value->cmd."</td><td colspan='9' align='right'>Rejoin investor #".$key_s."</td><td style='padding-left:25px;text-align:right;'>".number_format($value->profit,2, '.', '')."</td></tr>";
					 $balance_account = $balance_account + $value->profit;
					 $deposit = $deposit + $value->profit;
					 $count_inv++;

				}
				elseif (substr(trim($value->comment),0,11) == 'wd investor')
				{
					 $key_s = inv_search(trim(substr($value->comment,12)),$inv);
					 $balance_account = $balance_account + $value->profit;
					 $printstr ="<tr><td>".$value->order."</td><td style='padding-left:5px;'>".date('d-m-Y h:i:s',$value->open_time)."</td><td style='padding-left:5px;'>".$value->cmd."</td><td colspan='9' align='right'>Withdraw deposit investor #".$key_s."</td><td style='padding-left:25px;text-align:right;'>".number_format($value->profit,2, '.', '')."</td></tr>";
					 $deposit = $deposit + $value->profit;
				}
				elseif (substr(trim($value->comment),0,16) == 'deposit investor')
				{
					 $key_s = inv_search(trim(substr($value->comment,17)),$inv);
					 $balance_account = $balance_account + $value->profit;
					 $printstr ="<tr><td>".$value->order."</td><td style='padding-left:5px;'>".date('d-m-Y h:i:s',$value->open_time)."</td><td style='padding-left:5px;'>".$value->cmd."</td><td colspan='9' align='right'>Charge deposit investor #".$key_s."</td><td style='padding-left:25px;text-align:right;'>".number_format($value->profit,2, '.', '')."</td></tr>";
					 $deposit = $deposit + $value->profit;
				}
				elseif (substr(trim($value->comment),0,26) == 'urgent commission investor')
				{
					 $key_s = inv_search(trim(substr($value->comment,27)),$inv);
					 $balance_account = $balance_account + $value->profit;
					 $printstr ="<tr><td>".$value->order."</td><td style='padding-left:5px;'>".date('d-m-Y h:i:s',$value->open_time)."</td><td style='padding-left:5px;'>".$value->cmd."</td><td colspan='9' align='right'>Urgent commission wd investor #".$key_s."</td><td style='padding-left:25px;text-align:right;'>".number_format($value->profit,2, '.', '')."</td></tr>";
					 $deposit = $deposit + $value->profit;
				}
				elseif (substr(trim($value->comment),0,18) == 'urgent wd investor')
				{
					 $key_s = inv_search(substr(trim($value->comment),19,2),$inv);
					 $balance_account = $balance_account + $value->profit;
					 $printstr ="<tr><td>".$value->order."</td><td style='padding-left:5px;'>".date('d-m-Y h:i:s',$value->open_time)."</td><td style='padding-left:5px;'>".$value->cmd."</td><td colspan='9' align='right'>Urgent wd investor #".$key_s."</td><td style='padding-left:25px;text-align:right;'>".number_format($value->profit,2, '.', '')."</td></tr>";
					 $deposit = $deposit + $value->profit;
				}

				elseif (trim($value->comment) == 'deposit trader')
				{

					 $balance_account = $balance_account + $value->profit;
					 $printstr ="<tr><td>".$value->order."</td><td style='padding-left:5px;'>".date('d-m-Y h:i:s',$value->open_time)."</td><td style='padding-left:5px;'>".$value->cmd."</td><td colspan='9' align='right'>Charge deposit trader</td><td style='padding-left:25px;text-align:right;'>".number_format($value->profit,2, '.', '')."</td></tr>";
					 $deposit = $deposit + $value->profit;
				}
				elseif (trim($value->comment) == 'wd trader')
				{

					 $balance_account = $balance_account + $value->profit;
					 $printstr ="<tr><td>".$value->order."</td><td style='padding-left:5px;'>".date('d-m-Y h:i:s',$value->open_time)."</td><td style='padding-left:5px;'>".$value->cmd."</td><td colspan='9' align='right'>Withdraw deposit trader</td><td style='text-align:right;padding-left:25px;text-align:right;'>".number_format($value->profit,2, '.', '')."</td></tr>";
					 $deposit = $deposit + $value->profit;
				}
				elseif (trim($value->comment) == 'wd profit trader')
				{
					 $balance_account = $balance_account + $value->profit;
					 $printstr ="<tr><td>".$value->order."</td><td style='padding-left:5px;'>".date('d-m-Y h:i:s',$value->open_time)."</td><td style='padding-left:5px;'>".$value->cmd."</td><td colspan='9' align='right'>Withdraw deposit trader</td><td style='text-align:right;padding-left:25px;text-align:right;'>".number_format($value->profit,2, '.', '')."</td></tr>";
					 $deposit = $deposit + $value->profit;

				}
				elseif (substr(trim($value->comment),0,18) == 'wd profit investor')
				{
					 $key_s = inv_search(trim(substr(trim($value->comment),19)),$inv);
					 $balance_account = $balance_account + $value->profit;
					 $printstr ="<tr><td>".$value->order."</td><td style='padding-left:5px;'>".date('d-m-Y h:i:s',$value->open_time)."</td><td style='padding-left:5px;'>".$value->cmd."</td><td colspan='9' align='right'>Withdraw deposit investor #".$key_s."</td><td style='text-align:right;padding-left:25px;text-align:right;'>".number_format($value->profit,2, '.', '')."</td></tr>";
					 $deposit = $deposit + $value->profit;

				}

				elseif (trim($value->comment) == 'urgent wd trader')
				{

					 $balance_account = $balance_account + $value->profit;
					 $printstr ="<tr><td>".$value->order."</td><td style='padding-left:5px;'>".date('d-m-Y h:i:s',$value->open_time)."</td><td style='padding-left:5px;'>".$value->cmd."</td><td colspan='9' align='right'>Urgent withdraw deposit trader</td><td style='text-align:right;padding-left:25px;text-align:right;'>".number_format($value->profit,2, '.', '')."</td></tr>";
					 $deposit = $deposit + $value->profit;
				}

				elseif (substr(trim($value->comment),0,17) == "div zero investor")
				{
					$print=FALSE;
				}
				elseif (substr(trim($value->comment),0,7) == "stopout")
				{
					 $balance_account = $balance_account + $value->profit;
					 $deposit = $deposit + $value->profit;
					 $printstr ="<tr><td>".$value->order."</td><td style='padding-left:5px;'>".date('d-m-Y H:i:s',$value->open_time)."</td><td style='padding-left:5px;'>".$value->cmd."</td><td colspan='9' align='right'>".$value->comment."</td><td style='text-align:right;padding-left:25px;text-align:right;'>".number_format($value->profit,2, '.', '')."</td></tr>";
				}
				elseif (substr(trim($value->comment),0,19) == "div profit investor")
				{
					$print=FALSE;
				}
				elseif (trim($value->comment) == 'div profit trader v1')
				{
					$print=FALSE;
				}
				elseif (trim($value->comment) == "close account trader wd")
				{
					 $balance_account = $balance_account + $value->profit;
					 $printstr ="<tr><td>".$value->order."</td><td style='padding-left:5px;'>".date('d-m-Y h:i:s',$value->open_time)."</td><td style='padding-left:5px;'>".$value->cmd."</td><td colspan='9' align='right'>".$value->comment."</td><td style='text-align:right;padding-left:25px;text-align:right;'>".number_format($value->profit,2, '.', '')."</td></tr>";

				}
				elseif (substr(trim($value->comment),0,22) == "close account investor")
				{
					 $key_s = inv_search(trim(substr($value->comment,24,2)),$inv);

					 $balance_account = $balance_account + $value->profit;
					 $printstr ="<tr><td>".$value->order."</td><td style='padding-left:5px;'>".date('d-m-Y h:i:s',$value->open_time)."</td><td style='padding-left:5px;'>".$value->cmd."</td><td colspan='9' align='right'>close account investor #".$key_s." wd</td><td style='text-align:right;padding-left:25px;text-align:right;'>".number_format($value->profit,2, '.', '')."</td></tr>";

				}
     				elseif (trim($value->comment) == 'div zero trader')

				{
					$print=FALSE;
					print("<tr><td colspan='12' align='right'><b>Total profit at the end of the trading period:</b></td><td style='padding-left:25px;text-align:right;'>0.00</td></tr>");
							print("<tr><td colspan='12' align='right'><b>Manager profit:</b></td><td style='padding-left:25px;text-align:right;'>0.00</td></tr>");
					if (count($inv)>0)
					    	foreach ($inv as $key1=>$value1)
							{
							$key_print = $key1 + 1;
							if ($inv[$key1]['active'] == 1)
								print("<tr><td colspan='12' align='right'><b>Investor #".$key_print." profit:</b></td><td style='padding-left:25px;text-align:right;'>0.00</td></tr>");
							}
					print("<tr><td colspan='12' align='right'><b>Balance:</b></td><td style='padding-left:25px;text-align:right;'>".number_format($balance_account,2, '.', '')."</td></tr>");
					print("<tr><td colspan='13'>&nbsp;</td></tr>");

				}
				elseif (trim($value->comment) == 'div loss v2')
				{

					$print=FALSE;
					$key_new = $key+1;
					if ($value->failed == '1')
					{
						print("<tr><td colspan='13' align='right'><b>Divide failed!</b></td></tr>");
							$number_of_trade_period_1 = $number_of_trade_period + 1;
					        print("<tr><td colspan='13' align='left'>&nbsp;</td></tr>");
						print("<tr><td colspan='13' align='left'><b>Trading period ".$number_of_trade_period_1."</b>&nbsp;(".date('d-m-Y',$s_e_tp[0]->tp_start)." &#151; ".date('d-m-Y',$s_e_tp[0]->tp_end).")</td></tr>");

					}
					else
					{

					print("<tr><td colspan='12' align='right'><b>Total profit at the end of the trading period:</b></td><td style='padding-left:25px;text-align:right;'>".color(number_format((0-$value->profit),2, '.', ''))."</td></tr>");
							print("<tr><td colspan='12' align='right'><b>Manager profit:</b></td><td style='padding-left:25px;text-align:right;'>".color(number_format($statement[$key_new]->profit,2, '.', ''))."</td></tr>");
						foreach ($inv as $key1=>$value1)
							{
							$key_new = $key_new + 1;
							$key_print = $key1 + 1;
							if ($inv[$key1]['active'] == 1)
								print("<tr><td colspan='12' align='right'><b>Investor #".$key_print." profit:</b></td><td style='padding-left:25px;text-align:right;'>".color(number_format($statement[$key_new]->profit,2, '.', ''))."</td></tr>");
							}
					print("<tr><td colspan='12' align='right'><b>Balance:</b></td><td style='padding-left:25px;text-align:right;'>".number_format($balance_account,2, '.', '')."</td></tr>");
					print("<tr><td colspan='13'>&nbsp;</td></tr>");
					}
				}
				elseif (trim($value->comment) == 'div loss trader v2')
				{
					$print=FALSE;
				}
				elseif (substr(trim($value->comment),0,17) == 'div loss investor')
				{
					$print=FALSE;
				}
				elseif (substr(trim($value->comment),0,20) == 'div profit trader v4')
				{
					$print=FALSE;
				}
				elseif (substr(trim($value->comment),0,20) == 'div profit trader v3')
				{
					$print=FALSE;
				}

				elseif (trim($value->comment) == 'div profit v42')
				{

					$print=FALSE;
					$key_new = $key+1;
					if ($value->failed == '1')
					{
						print("<tr><td colspan='13' align='right'><b>Divide failed!</b></td></tr>");
							$number_of_trade_period_1 = $number_of_trade_period + 1;
					        print("<tr><td colspan='13' align='left'>&nbsp;</td></tr>");
						print("<tr><td colspan='13' align='left'><b>Trading period ".$number_of_trade_period_1."</b>&nbsp;(".date('d-m-Y',$s_e_tp[0]->tp_start)." &#151; ".date('d-m-Y',$s_e_tp[0]->tp_end).")</td></tr>");

					}
					else
					{

					$n = $statement[$key_new];
					print("<tr><td colspan='12' align='right'>Total profit at the end of the trading period<b>:</b></td><td style='padding-left:25px;text-align:right;'>".color(number_format((0-$value->profit),2, '.', ''))."</td></tr>");
							print("<tr><td colspan='12' align='right'><b>Manager profit:</b></td><td style='padding-left:25px;text-align:right;'>".color(number_format($n->profit,2, '.', ''))."</td></tr>");
					$key_new = $key_new+1;
						foreach ($inv as $key1=>$value1)
							{
							$key_2 = $key_new+$key1;
							$n = $statement[$key_2];
							$key_print = $key1 + 1;
							if ($inv[$key1]['active'] == 1)
								print("<tr><td colspan='12' align='right'><b>Investor #".$key_print." profit:</b></td><td style='padding-left:25px;text-align:right;'>".color(number_format($n->profit,2, '.', ''))."</td></tr>");
							else
								$key_new = $key_new - 1;
							}
					print("<tr><td colspan='12' align='right'><b>Balance:</b></td><td style='padding-left:25px;text-align:right;'>".number_format($balance_account,2, '.', '')."</td></tr>");
					print("<tr><td colspan='13'>&nbsp;</td></tr>");
					}

				}
				elseif (trim($value->comment) == 'div profit v3')
				{

					$print=FALSE;
					$key_new = $key+1;
					$n = $statement[$key_new];

					if ($value->failed == '1')
					{
						print("<tr><td colspan='13' align='right'><b>Divide failed!</b></td></tr>");
							$number_of_trade_period_1 = $number_of_trade_period + 1;
					        print("<tr><td colspan='13' align='left'>&nbsp;</td></tr>");
						print("<tr><td colspan='13' align='left'><b>Trading period ".$number_of_trade_period_1."</b>&nbsp;(".date('d-m-Y',$s_e_tp[0]->tp_start)." &#151; ".date('d-m-Y',$s_e_tp[0]->tp_end).")</td></tr>");

					}
					else
					{

						print("<tr><td colspan='12' align='right'><b>Total profit to cover previous losses (partial):</b></td><td style='padding-left:25px;text-align:right;'>".color(number_format((0-$value->profit),2, '.', ''))."</td></tr>");
							print("<tr><td colspan='12' align='right'><b>Manager refund:</b></td><td style='padding-left:25px;text-align:right;'>".color(number_format($n->profit,2, '.', ''))."</td></tr>");
						$key_new = $key_new + 1;
						foreach ($inv as $key1=>$value1)
							{
							$key_2 = $key_new+$key1;
							$n = $statement[$key_2];
							$key_print = $key1 + 1;
							if ($inv[$key1]['active'] == 1)
								print("<tr><td colspan='12' align='right'><b>Investor #".$key_print." refund:</b></td><td style='padding-left:25px;text-align:right;'>".color(number_format($n->profit,2, '.', ''))."</td></tr>");
							else
								$key_new = $key_new - 1;
							}
						print("<tr><td colspan='12' align='right'><b>Balance:</b></td><td style='padding-left:25px;text-align:right;'>".number_format($balance_account,2, '.', '')."</td></tr>");
						print("<tr><td colspan='13'>&nbsp;</td></tr>");
				      }
				}

				elseif (trim($value->comment) == 'div profit v41')
				{
					$print=FALSE;
					$key_new = $key+1;
					if ($value->failed == '1')
					{
						print("<tr><td colspan='13' align='right'><b>Divide failed!</b></td></tr>");
							$number_of_trade_period_1 = $number_of_trade_period + 1;
					        print("<tr><td colspan='13' align='left'>&nbsp;</td></tr>");
						print("<tr><td colspan='13' align='left'><b>Trading period ".$number_of_trade_period_1."</b>&nbsp;(".date('d-m-Y',$s_e_tp[0]->tp_start)." &#151; ".date('d-m-Y',$s_e_tp[0]->tp_end).")</td></tr>");

					}
					else
					{

					$n4 = $statement[$key_new];
					print("<tr><td colspan='12' align='right'><b>Total profit to cover previous losses:</b></td><td style='padding-left:25px;text-align:right;'>".color(number_format((0-$value->profit),2, '.', ''))."</td></tr>");
							print("<tr><td colspan='12' align='right'><b>Manager refund:</b></td><td style='padding-left:25px;text-align:right;'>".color(number_format($n4->profit,2, '.', ''))."</td></tr>");
					$key_new = $key_new+1;
						foreach ($inv as $key1=>$value1)
							{
							$key_2 = $key_new+$key1;
							$n = $statement[$key_2];
							$key_print = $key1 + 1;
							if ($inv[$key1]['active'] == 1)
								print("<tr><td colspan='12' align='right'><b>Investor #".$key_print." refund:</b></td><td style='padding-left:25px;text-align:right;'>".color(number_format($n->profit,2, '.', ''))."</td></tr>");
							else
								$key_new = $key_new - 1;
							}
					print("<tr><td colspan='13' align='right'>&nbsp;</td></tr>");
					}
				}
				elseif (trim($value->comment) == 'div loss trader ni')
				{
					$print=FALSE;
				}
				elseif (trim($value->comment) == 'div loss ni')
				{

					$print=FALSE;
					print("<tr><td colspan='12' align='right'><b>Total profit at the end of the trading period:</b></td><td style='padding-left:25px;text-align:right;'>".color(number_format((0-$value->profit),2,'.',''))."</td></tr>");
					print("<tr><td colspan='12' align='right'><b>Manager profit:</b></td><td style='padding-left:25px;text-align:right;'>".color(number_format((0-$value->profit),2,'.',''))."</td></tr>");
					print("<tr><td colspan='12' align='right'><b>Balance:</b></td><td style='padding-left:25px;text-align:right;'>".number_format($balance_account,2, '.', '')."</td></tr>");
					print("<tr><td colspan='13'>&nbsp;</td></tr>");

				}
				elseif (trim($value->comment) == 'div profit trader ni1')				
				{
					$print=FALSE;
				}
				elseif (trim($value->comment) == 'div profit ni1')
				{

					$print=FALSE;
					print("<tr><td colspan='12' align='right'><b>Total profit at the end of the trading period:</b></td><td style='padding-left:25px;text-align:right;'>".color(number_format((0-$value->profit),2,'.',''))."</td></tr>");
					print("<tr><td colspan='12' align='right'><b>Manager profit:</b></td><td style='padding-left:25px;text-align:right;'>".color(number_format((0-$value->profit),2,'.',''))."</td></tr>");
					print("<tr><td colspan='12' align='right'><b>Balance:</b></td><td style='padding-left:25px;text-align:right;'>".number_format($balance_account,2, '.', '')."</td></tr>");
					print("<tr><td colspan='13'>&nbsp;</td></tr>");

				}
 				elseif (trim($value->comment) == 'div trader bz3')				
				{
					$print=FALSE;
				}
				elseif (trim($value->comment) == 'div profit bz3')
				{

					$print=FALSE;
					print("<tr><td colspan='12' align='right'><b>Total refund at the end of the trading period :</b></td><td style='padding-left:25px;text-align:right;'>".color(number_format((0-$value->profit),2,'.',''))."</td></tr>");
					print("<tr><td colspan='12' align='right'><b>Manger refund:</b></td><td style='padding-left:25px;text-align:right;'>".color(number_format((0-$value->profit),2,'.',''))."</td></tr>");
					print("<tr><td colspan='12' align='right'><b>Balance:</b></td><td style='padding-left:25px;text-align:right;'>".number_format($balance_account,2, '.', '')."</td></tr>");
					print("<tr><td colspan='13'>&nbsp;</td></tr>");

				}
 				elseif (trim($value->comment) == 'div trader bz4')				
				{
					$print=FALSE;
				}
				elseif (trim($value->comment) == 'div profit bz4')
				{

					$print=FALSE;
					print("<tr><td colspan='12' align='right'><b>Total profit at the end of the trading period :</b></td><td style='padding-left:25px;text-align:right;'>".color(number_format((0-$value->profit),2,'.',''))."</td></tr>");
					print("<tr><td colspan='12' align='right'><b>Manger profit:</b></td><td style='padding-left:25px;text-align:right;'>".color(number_format((0-$value->profit),2,'.',''))."</td></tr>");
					print("<tr><td colspan='12' align='right'><b>Balance:</b></td><td style='padding-left:25px;text-align:right;'>".number_format($balance_account,2, '.', '')."</td></tr>");
					print("<tr><td colspan='13'>&nbsp;</td></tr>");

				}
				elseif(substr(trim($value->comment),0,10) == "correction")
				{
					$print = FALSE;
				        $balance_account = $balance_account + $value->profit;
				}
				elseif (trim($value->comment) == 'div profit v1')
				{
					$print=FALSE;

					$key_new = $key+1;
					$n = $statement[$key_new];

					
					if ($value->failed == '1')
					{
						print("<tr><td colspan='13' align='right'><b>Divide failed!</b></td></tr>");
							$number_of_trade_period_1 = $number_of_trade_period + 1;
					        print("<tr><td colspan='13' align='left'>&nbsp;</td></tr>");
						print("<tr><td colspan='13' align='left'><b>Trading period ".$number_of_trade_period_1."</b>&nbsp;(".date('d-m-Y',$s_e_tp[0]->tp_start)." &#151; ".date('d-m-Y',$s_e_tp[0]->tp_end).")</td></tr>");

					}
					else
					{
					print("<tr><td colspan='12' align='right'><b>Total profit at the end of the trading period:</b></td><td style='padding-left:25px;text-align:right;'>".color(number_format((0-$value->profit),2, '.', ''))."</td></tr>");
							print("<tr><td colspan='12' align='right'><b>Manager profit:</b></td><td style='padding-left:25px;text-align:right;'>".color(number_format($n->profit,2, '.', ''))."</td></tr>");
					$key_new = $key_new + 1;
						foreach ($inv as $key1=>$value1)
							{
							$key_2 = $key_new+$key1;
							$n = $statement[$key_2];
							$key_print = $key1 + 1;

								if ($inv[$key1]['active'] == 1)
									print("<tr><td colspan='12' align='right'><b>Investor #".$key_print." profit:</b></td><td style='padding-left:25px;text-align:right;'>".color(number_format($statement[$key_new]->profit,2, '.', ''))."</td></tr>");
								else
									$key_new = $key_new - 1;
							}
					print("<tr><td colspan='12' align='right'><b>Balance:</b></td><td style='padding-left:25px;text-align:right;'>".number_format($balance_account,2, '.', '')."</td></tr>");
					print("<tr><td colspan='13'>&nbsp;</td></tr>");
					}
				}

				else
					 $printstr ="<tr><td>".$value->order."</td><td style='padding-left:5px;'>".date('d-m-Y H:i:s',$value->open_time)."</td><td style='padding-left:5px;'>".$value->cmd."</td><td colspan='9' align='right'>".$value->comment."</td><td style='padding-left:25px;text-align:right;'>".number_format($value->profit,2, '.', '')."</td></tr>";					
			     }
			 if ($print)
			  echo $printstr;
					if (array_search($value->order,$lon) !== FALSE)
					{
						$s_e_tp = $this->mainform_model->get_start_tp($value->order);
						                                                                                
						if ($s_e_tp[0]->first_order_number == $s_e_tp[0]->last_order_number)
							$number_of_trade_period_1 = $number_of_trade_period + 1;
						else
							$number_of_trade_period_1 = $number_of_trade_period;
					        print("<tr><td colspan='13' align='left'>&nbsp;</td></tr>");
						print("<tr><td colspan='13' align='left'><b>Trading period ".$number_of_trade_period_1."</b>&nbsp;(".date('d-m-Y',$s_e_tp[0]->tp_start)." &#151; ".date('d-m-Y',$s_e_tp[0]->tp_end).")</td></tr>");
					}

					if (array_search($value->order,$fon) !== FALSE && substr(trim($value->comment),0,7) == "stopout")
					{
						$number_of_trade_period_1 = $number_of_trade_period;
						$s_e_tp = $this->mainform_model->get_end_tp($value->order);
						print("<tr><td colspan='13' align='left'><b>End of trade period ".$number_of_trade_period_1."</b>&nbsp;(".date('d-m-Y',$s_e_tp[0]->tp_start)." &#151; ".date('d-m-Y',$s_e_tp[0]->tp_end).")&nbsp;(by stopout)</td></tr>");
					        $count_period++;
					}



			}


			print("<tr><td colspan='13'>&nbsp;</td></tr>");
			print("<tr><td colspan='12' align='right'><b>Closed P/L:</b></td><td style='padding-left:25px;text-align:right;'><b>".number_format($pl,2, '.', '')."</b></td></tr>");
			print("<tr><td colspan='12' align='right'><b>Deposit/withdrawal:</b></td><td style='padding-left:25px;text-align:right;'><b>".number_format($deposit,2, '.', '')."</b></td></tr>");
			print("<tr><td colspan='12' align='right'><b>Balance:</b></td><td style='padding-left:25px;text-align:right;'><b>".number_format(abs($balance_account),2, '.', '')."</b></td></tr>");
		?>
		</table>
		</div>
     </div>


     <div class="classtabtab1">
	<div>
		<center>PAMM счет № <?php echo $acc_number?> [<?php echo $name?>]</center>
	  <p>Доходность по месяцам</p>
	</div>
		<div style="height:600px;">
		<table><tr><td>
		<table>
		<?php
	  	   $year_profitable = $this->mainform_model->obtain_year_profitable($acc_number);
			foreach($year_profitable as $value_yp)
			 print("<tr><td>".$value_yp->year."</td><td>&nbsp;</td><td>".number_format(round(100*$value_yp->ptp,2),2,'.','')."</td></tr>");
	  	   $month_profitable = $this->mainform_model->obtain_month_profitable($acc_number);
		   foreach ($month_profitable as $value)
			{
			 print("<tr><td>".$value->month."</td><td>".$value->year."</td><td>".number_format(round(100*$value->ptp,2),2,'.','')."</td></tr>");
			}
		?>
		</table>
		</td><td>
		<img src='/mainform/graph1/<?php echo $acc_number?>'>
		</td></tr></table>
		</div>
     </div>
     <div class="classtabtab1">
	<div>
	     <center>PAMM счет № <?php echo $acc_number?> [<?php echo $name?>]</center>
	    <p>Инвестиции в PAMM счет</p>	
	</div>
		<div style="height:600px;">
			<table><tr><td>
		<table>
		<?php
			$foo = "None";
			$sum_dep     = $this->mainform_model->obtain_sum_means($acc_number);
			$sum_dep_week_ago     = $this->mainform_model->obtain_sum_means_week_ago($acc_number);
			$sum_dep_month_ago     = $this->mainform_model->obtain_sum_means_month_ago($acc_number);
			$own_means   = $this->mainform_model->obtain_own_means($acc_number);
			$inv_number  = $this->mainform_model->obtain_inv_number($acc_number);
			$inv_number_week_ago  = $this->mainform_model->obtain_inv_number_week_ago($acc_number);
			$inv_number_month_ago  = $this->mainform_model->obtain_inv_number_month_ago($acc_number);
		

			$inv_means   = $this->mainform_model->obtain_inv_means($acc_number);
			$own_profit   = $this->mainform_model->obtain_own_profit($acc_number);
			$inv_profit   = $this->mainform_model->obtain_inv_profit($acc_number);
			$common_profit = $own_profit[0]->pamm_clients_stat_sum + $inv_profit[0]->pamm_clients_stat_sum;
			$invested_means   = $this->mainform_model->obtain_invested_means($acc_number);
			$invested_means_week_ago   = $this->mainform_model->obtain_invested_means_week_ago($acc_number);
			$invested_means_month_ago   = $this->mainform_model->obtain_invested_means_month_ago($acc_number);

			$common_profit_week_ago = $this->mainform_model->obtain_common_profit_week_ago($acc_number);
			$common_profit_month_ago =$this->mainform_model->obtain_common_profit_month_ago($acc_number);

			$first_period_week   = $this->mainform_model->obtain_invested_means_first_period_week($acc_number);
			$first_period_month  = $this->mainform_model->obtain_invested_means_first_period_month($acc_number);

			$inv_number  = $this->mainform_model->obtain_inv_number($acc_number);
			$avg_deposit = $invested_means[0]->pamm_clients_stat_sum/(count($inv_number)+1);
			$inv_number_week_add = count($inv_number)-count($inv_number_week_ago);
			$inv_number_month_add = count($inv_number)-count($inv_number_month_ago);

			if ($inv_number_week_add != 0)
				$inv_number_week_add_old = (count($inv_number)/$inv_number_week_add)*100;
			else
				$inv_number_week_add_old = 0;
	                 	
			if ($inv_number_month_add != 0)
				$inv_number_month_add_old = (count($inv_number)/$inv_number_month_add)*100;
			else
				$inv_number_month_add_old = 0;

			if (count($first_period_week) != 0)
			    if ($invested_means_week_ago[0]->pamm_clients_stat_sum != $invested_means[0]->pamm_clients_stat_sum)
				$invested_means_week_old  = ($invested_means[0]->pamm_clients_stat_sum/$invested_means_week_ago[0]->pamm_clients_stat_sum)*100;
			    else
				$invested_means_week_old  =  0;
			else
				$invested_means_week_old  = 100;

			if (count($first_period_month) != 0)
			    if ($invested_means_month_ago[0]->pamm_clients_stat_sum != $invested_means[0]->pamm_clients_stat_sum)
				$invested_means_month_old  = ($invested_means[0]->pamm_clients_stat_sum/$invested_means_month_ago[0]->pamm_clients_stat_sum)*100;
			    else
				$invested_means_month_old  =0;
			else
				$invested_means_month_old  = 100;

			if (count($first_period_week) != 0)
			    if ($sum_dep_week_ago[0]->pamm_clients_stat_sum != $sum_dep[0]->pamm_clients_stat_sum)
				$sum_dep_week_old  = ($sum_dep[0]->pamm_clients_stat_sum/$sum_dep_week_ago[0]->pamm_clients_stat_sum)*100;
			    else
				$sum_dep_week_old  =  0;
			else
				$sum_dep_week_old  = 100;

			if (count($first_period_month) != 0)
			    if ($sum_dep_month_ago[0]->pamm_clients_stat_sum != $sum_dep[0]->pamm_clients_stat_sum)
				$sum_dep_month_old  = ($sum_dep[0]->pamm_clients_stat_sum/$sum_dep_month_ago[0]->pamm_clients_stat_sum)*100;
			    else
				$sum_dep_month_old  =0;
			else
				$sum_dep_month_old  = 100;

			if (count($first_period_week) != 0)
			    if ($common_profit_week_ago[0]->pamm_clients_stat_sum != $common_profit && $common_profit_week_ago[0]->pamm_clients_stat_sum != 0)
				$common_profit_week_old  = ($common_profit/$common_profit_week_ago[0]->pamm_clients_stat_sum)*100;
			    else
				$common_profit_week_old  =  0;
			else
				$common_profit_week_old  = 100;

			if (count($first_period_month) != 0)
			    if ($common_profit_month_ago[0]->pamm_clients_stat_sum != $common_profit && $common_profit_week_ago[0]->pamm_clients_stat_sum != 0)
				$common_profit_month_old  = ($common_profit/$common_profit_month_ago[0]->pamm_clients_stat_sum)*100;
			    else
				$common_profit_month_old  =  0;
			else
				$common_profit_month_old  = 100;
			if ($sum_dep[0]->pamm_clients_stat_sum != 0)
				$upr_percent = ($own_means[0]->pamm_clients_stat_sum/$sum_dep[0]->pamm_clients_stat_sum)*100;
			else
				$upr_percent = 0;

			 print("<tr><td><b>Сумма в управлении:</b></td><td>".number_format($sum_dep[0]->pamm_clients_stat_sum,2,'.','')."</td></tr>");
			 print("<tr><td>Капитал управляющего:</td><td>".number_format($own_profit[0]->pamm_clients_stat_sum,2,'.','')."</td></tr>");
			 print("<tr><td>Средства управляющего:</td><td>".number_format($own_means[0]->pamm_clients_stat_sum,2,'.','')."<font color='grey'>(".number_format($upr_percent,2,'.','')."%)</font></td></tr>");
			 print("<tr><td>Средства инвесторов:</td><td>".number_format($inv_means[0]->pamm_clients_stat_sum,2,'.','')."</td></tr>");
			 print("<tr><td></td></tr>");
			 print("<tr><td><b>Совокупная прибыль:</b></td><td>".number_format($common_profit,2,'.','')."</td></tr>");
			 print("<tr><td>Вложенные средства:</td><td>".number_format($invested_means[0]->pamm_clients_stat_sum,2,'.','')."</td></tr>");
			 print("<tr><td>Количество инвесторов:</td><td>".count($inv_number)."</td></tr>");
			 print("<tr><td>Средняя сумма вклада:</td><td>".number_format($avg_deposit,2,'.','')."</td></tr>");			 
			 print("<tr><td></td></tr>");
			 print("<tr><td><b>Прирост за последние 7 дней:</b></td><td></td></tr>");
			 print("<tr><td>Сумма в управлении:</td><td>".number_format($sum_dep[0]->pamm_clients_stat_sum - $sum_dep_week_ago[0]->pamm_clients_stat_sum,2,'.','')."<font color='grey'>(".number_format($sum_dep_week_old,2,'.','')."%)</font></td></tr>");
			 print("<tr><td>Совокупная прибыль:</td><td>".number_format($common_profit_week_ago[0]->pamm_clients_stat_sum,2,'.','')."<font color='grey'>(".number_format($common_profit_week_old,2,'.','')."%)</font></td></tr>");
			 print("<tr><td>Вложенные средства:</td><td>".number_format($invested_means[0]->pamm_clients_stat_sum - $invested_means_week_ago[0]->pamm_clients_stat_sum,2,'.','')."<font color='grey'>(".number_format($invested_means_week_old,2,'.','')."%)</font></td></tr>");
			 print("<tr><td>Количество инвесторов:</td><td>".$inv_number_week_add."<font color='grey'>(".number_format($inv_number_week_add_old,2,'.','')."%)</font></td></tr>");
			 print("<tr><td></td></tr>");
			 print("<tr><td><b>Прирост за последние 30 дней:</b></td><td></td></tr>");
			 print("<tr><td>Сумма в управлении:</td><td>".number_format($sum_dep[0]->pamm_clients_stat_sum - $sum_dep_month_ago[0]->pamm_clients_stat_sum,2,'.','')."<font color='grey'>(".number_format($sum_dep_month_old,2,'.','')."%)</font></td></tr>");
			 print("<tr><td>Совокупная прибыль:</td><td>".number_format($common_profit_month_ago[0]->pamm_clients_stat_sum,2,'.','')."<font color='grey'>(".number_format($common_profit_month_old,2,'.','')."%)</font></td></tr>");
			 print("<tr><td>Вложенные средства:</td><td>".number_format($invested_means[0]->pamm_clients_stat_sum - $invested_means_month_ago[0]->pamm_clients_stat_sum,2,'.','')."<font color='grey'>(".number_format($invested_means_month_old,2,'.','')."%)</font></td></tr>");
			 print("<tr><td>Количество инвесторов:</td><td>".$inv_number_month_add."<font color='grey'>(".number_format($inv_number_month_add_old,2,'.','')."%)</font></td></tr>");
			 
	  	?>   
		</table>
		</td>
		<td>
		<table><tr><td><img src="/mainform/graph2/<?php echo $acc_number?>"></td></tr><tr><td><img src="/mainform/graph3/<?php echo $acc_number?>"></td></tr></table>
		</td></tr></table>
		</div>
     </div>

     <div class="classtabtab1">
	<div>
	     <center>PAMM счет № <?php echo $acc_number?> [<?php echo $name?>]</center>
	  <p>Партнерская программа</p>
	</div>
		<div style="height:600px;">
		 <table>
		  <tr><td style="width:100px;">Номер</td><td style="width:100px;">Наименование</td><td style="width:100px;">Партнерский процент</td><td style="width:100px;">Сумма инвестиций:от</td><td style="width:100px;">Сумма инвестиций:до</td></tr>
		  <tr><td style="width:100px;">1</td><td style="width:100px;">Программа 1</td><td style="width:100px;"><?php echo $partbonus1_per?>%</td><td style="width:100px;"><?php echo $partbonus1_lb?></td><td style="width:100px;"><?php echo $partbonus1_ub?></td></tr>
		 <tr><td style="width:100px;">2</td><td style="width:100px;">Программа 2</td><td style="width:100px;"><?php echo $partbonus2_per?>%</td><td style="width:100px;"><?php echo $partbonus2_lb?></td><td style="width:100px;"><?php echo $partbonus2_ub?></td></tr>
		 <tr><td style="width:100px;">3</td><td style="width:100px;">Программа 3</td><td style="width:100px;"><?php echo $partbonus3_per?>%</td><td style="width:100px;"><?php echo $partbonus3_lb?></td><td style="width:100px;"><?php echo $partbonus3_ub?></td></tr>
		 <tr><td style="width:100px;">4</td><td style="width:100px;">Программа 4</td><td style="width:100px;"><?php echo $partbonus4_per?>%</td><td style="width:100px;"><?php echo $partbonus4_lb?></td><td style="width:100px;"><?php echo $partbonus4_ub?></td></tr>
		 <tr><td style="width:100px;">5</td><td style="width:100px;">Программа 5</td><td style="width:100px;"><?php echo $partbonus5_per?>%</td><td style="width:100px;"><?php echo $partbonus5_lb?></td><td style="width:100px;"><?php echo $partbonus5_ub?></td></tr>		 
		 </table>
		 </div>
     </div>

</div>
<table cellspadding="5"><tr><td>
<a class="submit" href="/mainform/index/<?php echo $this->session->userdata('TRADER_ID')?>"><span style="color:white;font-weight:bold;">Return</span></a></td>
</tr></table>
</body>

</body>
</html>
