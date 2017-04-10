<?php
    function calculate_time($time)
    {

		if ( $time > mktime(9,0,0,date('m'),date('j'),date('Y')) AND  $time < mktime(9,34,0,date('m'),date('j'),date('Y')))
			return "First trade period - Trade - day 1 (09:00 - 09:34)";
		elseif ( $time > mktime(9,34,0,date('m'),date('j'),date('Y')) AND  $time < mktime(10,8,0,date('m'),date('j'),date('Y')))
			return "First trade period - Trade - day 2 (09:34 - 10:08)";
		elseif ( mktime(10,8,0,date('m'),date('j'),date('Y')) AND  $time < mktime(10,42,0,date('m'),date('j'),date('Y')))
			return "First trade period - Trade - day 3 (10:08 - 10:42)";
		elseif ( $time > mktime(10,42,0,date('m'),date('j'),date('Y')) AND  $time < mktime(11,16,0,date('m'),date('j'),date('Y')))
			return "First trade period - Trade - day 4 (10:42 - 11:16)";
		elseif ( $time > mktime(11,16,0,date('m'),date('j'),date('Y')) AND  $time < mktime(11,50,0,date('m'),date('j'),date('Y')))
			return "First trade period - Trade - day 5 (11:16 - 11:50)";
		elseif ( $time > mktime(11,50,0,date('m'),date('j'),date('Y')) AND  $time < mktime(12,24,0,date('m'),date('j'),date('Y')))
			return "First trade period - Rolover - day 1 (11:50 - 12:24)";
		elseif ( $time > mktime(12,24,0,date('m'),date('j'),date('Y')) AND  $time < mktime(12,58,0,date('m'),date('j'),date('Y')))
			return "First trade period - Rolover - day 2 (12:24 - 12:58)";
		elseif ( $time > mktime(12,58,0,date('m'),date('j'),date('Y')) AND  $time < mktime(13,32,0,date('m'),date('j'),date('Y')))
			return "Second trade period - Trade - day 1 (12:58 - 13:32)";
		elseif ( $time > mktime(13,32,0,date('m'),date('j'),date('Y')) AND  $time < mktime(14,6,0,date('m'),date('j'),date('Y')))
			return "Second trade period - Trade - day 2 (13:32 - 14:06)";
		elseif ( $time > mktime(14,6,0,date('m'),date('j'),date('Y')) AND  $time < mktime(14,40,0,date('m'),date('j'),date('Y')))
			return "Second trade period - Trade - day 3 (14:06 - 14:40)";
		elseif ( $time > mktime(14,40,0,date('m'),date('j'),date('Y')) AND  $time < mktime(15,14,0,date('m'),date('j'),date('Y')))
			return "Second trade period - Trade - day 4 (14:40 - 15:14)";
		elseif ( $time > mktime(15,14,0,date('m'),date('j'),date('Y')) AND  $time < mktime(15,48,0,date('m'),date('j'),date('Y')))
			return "Second trade period - Trade - day 5 (15:14 - 15:48)";
		elseif ( $time > mktime(15,48,0,date('m'),date('j'),date('Y')) AND  $time < mktime(16,22,0,date('m'),date('j'),date('Y')))
			return "Second trade period - Rolover - day 1 (15:48 - 16:22)";
		elseif ( $time > mktime(16,22,0,date('m'),date('j'),date('Y')) AND  $time < mktime(16,56,0,date('m'),date('j'),date('Y')))
			return "Second trade period - Rolover - day 2 (16:22 - 16:58)";
		else
			return "Not calculated";

    }



$time1 = calculate_time(time());
echo "ID=".$this->session->userdata('TRADER_ID')."&nbsp;&nbsp;Now:".date('H:i:s j-m-Y',time())." ".$time1."<BR>";

$message_not_enough_sum = "На данном кошельке недостаточно средств для активации счета.Выберите другой кошелек или пополните этот.";
?>
<a class="submit" href="http://77.239.241.202:8081/engine_test1.php"><span style="color:white;font-weight:bold;font-size:15px;">Exit</span></a>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>PAMM service example STRENGTHED TIME SCALE</title>

<link rel="stylesheet" href="/frontend/css/example.css" TYPE="text/css" MEDIA="screen">
<style type="text/css">
  .hoverRow { background-color: yellow; }
</style>

<script type="text/javascript" src="/frontend/js/main_form.js"></script>
<script type="text/javascript" src="/frontend/js/hilight.js"></script>
<script type="text/javascript">

document.write('<style type="text/css">.tabber{display:none;}</style>');

var tabberOptions = {

  'manualStartup':true,


  'onLoad': function(argsObj) {

    if (argsObj.tabber.id == 'tab2') {
      alert('Finished loading tab2!');
    }
  },

  'onClick': function(argsObj) {

    var t = argsObj.tabber; /* Tabber object */
    var id = t.id; /* ID of the main tabber DIV */
    var i = argsObj.index; /* Which tab was clicked (0 is the first tab) */
    var e = argsObj.event; /* Event object */

    if (id == 'tab2') {
      return confirm('Swtich to '+t.tabs[i].headingText+'?\nEvent type: '+e.type);
    }
  },

  'addLinkId': true

};
</script>


<script type="text/javascript" src="/frontend/js/tabber.js"></script>

</head>
<?php
switch ($from):
	case("3"):
        	print('<body onLoad="focus();">');
	break;
	case("5"):
        	print('<body onLoad="focus();">');
	break;
	case("6"):
        	print('<body onLoad="focus();">');
	break;
	case("7"):
        	print('<body onLoad="focus();">');
	break;
	case("8"):
        	print('<body onLoad="focus();">');
	break;
	case("10"):
        	print('<body onLoad="focus1();">');
	break;
	case("11"):
        	print('<body onLoad="focus1();">');
	break;
	case("13"):
        	print('<body onLoad="focus1();">');
	break;
	case("14"):
        	print('<body onLoad="focus1();">');
	break;
	case("15"):
        	print('<body onLoad="focus2();">');
	break;
	case("16"):
        	print('<body onLoad="focus2();">');
	break;
 	case("17"):
        	print('<body onLoad="focus();">');
	break;
	case("18"):
        	print('<body onLoad="focus1();">');
	break;
	case("19"):
        	print('<body onLoad="focus1();">');
	break;
	case("20"):
        	print('<body onLoad="focus1();">');
	break;
	case("21"):
        	print('<body onLoad="focus2();">');
	break;
	case("24"):
        	print('<body onLoad="focus1();">');
	break;
	case("25"):
        	print('<body onLoad="focus1();">');
	break;
	case("26"):
        	print('<body onLoad="focus1();">');
	break;
	case("27"):
        	print('<body onLoad="focus();">');
	break;
	case("28"):
        	print('<body onLoad="focus();">');
	break;
	case("29"):
        	print('<body>');
	break;

	case("31"):
        	print('<body onLoad="focus2();">');
	break;
	case("32"):
        	print('<body onLoad="focus();">');
	break;
	case("33"):
        	print('<body onLoad="focus();">');   //return to the manager tab
	break;
	case("34"):
        	print('<body onLoad="focus1();">');   //return to the investor tab
	break;
	case("35"):
        	print('<body onLoad="focus1();">');   //return to the investor tab
	break;
	case("36"):
        	print('<body onLoad="focus();">');    // separate activalion summ is less than 300
	break;
	case("41"):
        	print('<body onLoad="focus2();">');
	break;

	default:
        	print('<body>');
	break;
endswitch;
?>
<h1><font color="magenta">13082013 - <b>STRETCHED TIME SCALE</b></font></h1>

<div class="tabber" id="tab1">

  <div class="tabbertab">
    <h2><a name="tab1">PAMM account rating</a></h2>
    <?php  
	switch ($from):
	case ("0"):
        	include("header.php");
		print('<table id="jointable" style="border:1px solid;border-spacing:0;border-collapse:collapse;">');
		print("<tr id='header'><td style='width:100px;border:1px solid;text-align:center;'>РАММ счет</td><td style='width:50px;border:1px solid;text-align:center;'>Система</td><td style='width:70px;border:1px solid;text-align:center;'>Открыт</td><td style='width:200px;border:1px solid;text-align:center;'>Управляющий</td><td style='width:50px;border:1px solid;text-align:center;'>%</td><td style='width:20px;border:1px solid;text-align:center;'>dip&nbsp;%&nbsp;(USD)</td><td style='width:200px;border:1px solid;text-align:center;'>Торговый период</td><td style='width:200px;border:1px solid;text-align:center;'>Rolover</td><td style='width:50px;border:1px solid;text-align:center;'>Срок работы</td><td style='width:50px;border:1px solid;text-align:center;'>График</td><td style='width:50px;border:1px solid;text-align:center;'>Агр</td><td style='width:50px;border:1px solid;text-align:center;'>Капитал счета</td><td style='width:50px;border:1px solid;text-align:center;'>Средняя доходность</td><td style='width:50px;border:1px solid;text-align:center;'>Доходность за текущий<BR>торговый период</td><td style='width:50px;border:1px solid;text-align:center;'>Дневной прирост</td><td style='width:50px;border:1px solid;text-align:center;'>Общая текущая просадка</td><td style='width:50px;border:1px solid;text-align:center;'>Максимальная<BR>просадка</td><td style='width:50px;border:1px solid;text-align:center;'>Мин.<BR>инв.</td><td style='width:50px;border:1px solid;text-align:center;'>Мин.<BR>вывод</td><td style='width:100px;border:1px solid;text-align:center;'>Статус</td><td style='width:100px;border:1px solid;text-align:center;'>Action</td></tr>");		
	        foreach ($rpa as $value)
		{
			switch ($value->active):
					case("0"): $active = "Неактивный";
					break;
					case("1"): $active = "Активный";
					break;
					case("2"): $active = "Закрытый";
					break;
					case("3"): $active = "Невидимый";
					break;
					case("4"): $active = "Забаненный";
					break;
					case("5"): $active = "Закрываемый";
					break;
			endswitch;
			$id	       = $this->mainform_model->obtain_trader_id($value->login);
			$timestart     = $this->mainform_model->obtain_timestart_for_tra($value->login,$id[0]->tid);
			$timeend       = $this->mainform_model->obtain_timeend($value->login);

			if ($timeend[0]->date_close == '0000-00-00 00:00:00')
				$timelive      = round((strtotime(date('y-m-d H:i:s')) - strtotime($timestart[0]->pamm_clients_stat_date))/86400);
			else
				$timelive      = round((strtotime($timeend[0]->date_close) - strtotime($timestart[0]->pamm_clients_stat_date))/86400);

			 $agg = $this->mainform_model->get_aggr($value->login);
			 $sum = 0;
			 $count_agg = 0;

			 foreach($agg as $value1)
				{
				  $sum = $sum + $value1->pamm_tp_profitable;
				    $count_agg++;
				}

			if ($count_agg > 1)
				 $aggr_value = $sum/($count_agg-1);
			else
				$aggr_value = 0;

 			if (abs($aggr_value) >= 0 AND abs($aggr_value) <= 0.051)
				$aggressive_scale = "<img src='/frontend/img/scale1.gif'>";
 			elseif (abs($aggr_value) >= 0.051 AND abs($aggr_value) <= 0.11)
				$aggressive_scale = "<img src='/frontend/img/scale2.gif'>";
 			elseif (abs($aggr_value) > 0.11 AND abs($aggr_value) <= 0.21)
				$aggressive_scale = "<img src='/frontend/img/scale3.gif'>";
 			elseif (abs($aggr_value) > 0.21 AND abs($aggr_value) <= 0.51)
				$aggressive_scale = "<img src='/frontend/img/scale4.gif'>";
 			elseif (abs($aggr_value) > 0.51)
				$aggressive_scale = "<img src='/frontend/img/scale5.gif'>";

			$p_total       = $this->mainform_model->get_previous_total_for_ctpp($value->login);
			$urgent_total  = $this->mainform_model->get_urgent_total($value->login);
			$dynamic       = $this->mainform_model->obtain_dynamic($value->login);
			$sum_dep       = $this->mainform_model->obtain_sum_means($value->login);
			$profitable    = $this->mainform_model->obtain_profitable($value->login);
			$max_dip       = $this->mainform_model->obtain_max_dip_from_db($value->login);

				$current_tp_profit = number_format(round(100*$profitable[0]->pamm_tp_profitable,2),2,'.','')."%";

			if ($value->active == '2')
			{
//				$current_tp_profit = "-";
				$disp_down_value = "-";
				$disp_dayprofit_value = "-";
				$disp_common_dip = "-";
			}
			else
			{
				$disp_down_value = number_format($value->down,2,'.','')."%";
				$disp_dayprofit_value = number_format($dynamic[0]->day_profit,2,'.','');
				$disp_common_dip = number_format($value->debt,2,'.','');
			}

			if ($value->active == '1')
			   if ($this->session->userdata('TRADER_ID') != $id[0]->tid )
                             $joinlink = '<a id ="joinlink" class="submit" href="/pamm/dsp/join/'.$value->login.'"><span style="color:white;font-weight:bold;">Join to account</span></a>';
			   else
			     $joinlink = "N/A";
			else
			     $joinlink ="&nbsp;";

       			$print = $this->mainform_model->get_account_status($value->login);
			$color = $this->mainform_model->get_account_color($value->login); 
			$debt_for_view = $this->mainform_model->get_debt_for_view($value->login); 
			$debt_inout = $this->mainform_model->obtain_debt_inout($value->login); 
			$fsb = $this->mainform_model->obtain_fsb($value->login); 

			$fsb = $this->mainform_model->obtain_fsb($value->login); 
			        if ($debt_for_view[0]->debt != 0)
					$d_f_w  = "<b>".$debt_for_view[0]->debt."</b>";
				else
					$d_f_w = "";	
				
				if ($value->active == '2')
				   {
				        $trade = '&#151';
				        $rolover = '&#151';
					$color_account = '#999999';
				   }
				elseif ($value->active == '5')
				   {
				        $trade = 'closing';
				        $rolover = 'closing';
					$color_account = '#00F0F0';
				   }

				else
				   {
				        $trade = $print[0]->timeline;
				        $rolover = $print[0]->timeline1;
				        $color_account = $color[0]->color;
				   }

			$dip_in_usd = $max_dip[0]->max_dip*$fsb[0]->fsb/100;
			$aggr_value_100 = $aggr_value*100;

			print("<tr id='".$value->login."' ><td style='width:100px;border:1px solid;text-align:center;background-color:".$color_account."'>".$value->login."</td><td style='width:50px;border:1px solid;text-align:center;'>MT4</td><td style='width:70px;border:1px solid;text-align:center;'>".substr($value->date_reg,8,2)."-".substr($value->date_reg,5,2)."-".substr($value->date_reg,2,2)."</td><td style='width:200px;border:1px solid;text-align:center;'>".$value->fio."</td><td style='width:50px;border:1px solid;text-align:center;'>".$value->distr_upr."/".$value->distr_inv."</td><td style='width:20px;border:1px solid;text-align:center;'>".$max_dip[0]->max_dip."&nbsp;(".number_format($dip_in_usd,2,'.','').")</td><td style='width:200px;border:1px solid;text-align:center;'>".$trade."</td><td style='width:200px;border:1px solid;text-align:center;'>".$rolover."</td><td style='width:50px;border:1px solid;text-align:center;'>".$timelive."</td><td style='width:100px;border:1px solid;text-align:center;'><span><img src='/mainform/graph/".$value->login."'></span></td><td style='width:100px;border:1px solid;text-align:center;'>".$aggressive_scale."</td><td style='width:50px;border:1px solid;text-align:center;'>".number_format($sum_dep[0]->pamm_clients_stat_sum,2,'.','')."</td><td style='width:50px;border:1px solid;text-align:center;'>".number_format($aggr_value_100,2,'.','')."%</td><td style='width:50px;border:1px solid;text-align:center;'>".$current_tp_profit."</td><td style='width:50px;border:1px solid;text-align:center;'>".$disp_dayprofit_value."</td><td style='width:50px;border:1px solid;text-align:center;'>".$disp_common_dip."</td><td style='width:50px;border:1px solid;text-align:center;'>".$disp_down_value."</td><td style='width:50px;border:1px solid;text-align:center;'>".$value->of_i_p."</td><td style='width:50px;border:1px solid;text-align:center;'>".$value->min_withdraw."</td><td style='width:100px;border:1px solid;text-align:center;'>".$active."</td><td style='width:100px;border:1px solid;text-align:center;'>".$joinlink."</td></tr>");		

		}
		print("</table>");
	?>
		<script type="text/javascript">
		highlightTableRows2("jointable","","hoverRow",false);
		</script>
	<table>
	<tr><td><a class="submit" href="/pamm/dsp/add"><span style="color:white;font-weight:bold;">Open PAMM account</span></a></td></tr>
	</table>
	<?php
	 break;
	case ("1"):
?>	
<form method="POST" name="form1" id="form1" action="/pamm/dsp/submit">
ФИО:<input type="text" name="fio1" id="fio1" value="TEST"><input type="text" name="fio2" id="fio2" value="PAMM"><input type="text" name="fio3" value="GENERAL MODULE">
<BR><BR>
<b>Контактная информация</b>
<BR><BR>
Дата рождения:<input type="text" id="dateofbirth" name="dateofbirth" value="01-01-1970">
<BR>
Электронная почта:<input type="text" id="email" name="email" value="aaa@bbb.com">
<BR>
Телефон:<input type="text" name="phone" id="phone" value="5178220">
<BR>
Почтовый индекс:<input type="text" name="zipcode" id="zipcode" value="194354">
<BR>
Страна:
<select name="country">
          <option value="" selected="selected">Select a country</option>
          <?php if(!empty($country_list_array)) { foreach($country_list_array as $country): ?>
          <option value="<?php=$country->country_name ?>" dialingcode="<?php echo $country->dialing_code?>" <?php if($country->dialing_code=='7')echo' selected="selected"'; ?>>
          <?php echo $country->country_name ?>
          </option>
          <?php endforeach; } ?>
	  </select>
<BR>
Населенный пункт:<input type="text" id="city" name="city" value="Санкт-Петербург">
<BR>
Регион:<input type="text" name="region" id="region" value="Ленинградская область">
<BR>
Адрес:<input type="text" name="address" id="address" value="ул. Кима, д. 1, кв. 1">
<BR>
Телефонный пароль:<input type="text" name="tp" value="пароль телефонный произвольный">
<BR>
<BR>
<b>Оферта PAMM счета</b>
<BR><br>

Торговая платформа:<select name="system"><option value="0">MT4<option value="1" disabled>MT5</option><option value="2" disabled>CTrader</option></select>
<BR>
Минимальный вклад (сумма начальных инвестиций): <input type="text" id="ofip" name="ofip" value="300.00" size="2">$
<BR>
Минимальное пополнение/снятие:<input type="text" id="of_m_p" name="of_m_p" value="10.00" size="1">$
<BR>
Торговый период:<select name="of_t_p">
				<?php
				for($i=1;$i<50;$i++)
				{
					print("<option>".$i." нед.</option>");
				}
				?>
				</select>
<BR>
Распределение прибыли:<select name="disp"><option>90% / 10%</option><option>80% / 20%</option><option>70% / 30%</option><option>60% / 40%</option><option selected>50% / 50%</option><option>40% / 60%</option><option>30% / 70%</option><option>20% / 80%</option><option>10% / 90%</option><option>0% / 100%</option></select>
<BR>
Максимальная просадка по счету:<select name="max_dip"><option value="10">10%</option><option value="20">20%</option><option value="30" selected>30%</option><option value="40">40%</option><option value="50">50%</option><option value="60">60%</option><option value="70">70%</option><option value="80">80%</option><option value="90">90%</option><option value="100">100%</option></select>
<BR>
Возможность досрочного вывода:<input type="checkbox" name="w_b" checked>
<BR>
Штраф инвестора за досрочный вывод:<input type="text" id="penalty" name="penalty" value="1.25" size="1">%
<BR>
Разрешение на операции ввода/вывода при долге счета:<input type="checkbox" name="inoutdebt" checked>
<BR>
Реинвестиции:<select name="reinv"><option value="0">автоматические</option><option value="1">вывод всей прибыли</option></select>
<BR>
Открытая статистика для всех:<input type="checkbox" name="openstat" checked>
<BR>
Открытая статистика для инвесторов:<input type="checkbox" name="openstatinv" checked>
<BR>
<BR>
<b>Активация PAMM счета</b>
<BR>
<?php
$data['wallet'] = $wallet;
$data['quotes'] = $quotes;

$this->load->view('act',$data);
?>
</form>
<?php
	break;
// submit new PAMM account
	case("2"):
	echo "<BR><BR>";
	echo "PAMM account is created";
	echo "<BR>";
	echo "Number:".$acc_number;
	echo "<BR>";
	echo "Password: ".$passwd;
	echo "<BR>";
	echo "Investor's password: ".$passwd_inv;
	echo "<BR>";
	echo $result;
	?>
		<table cellspadding="5"><tr><td>
		<a class="submit" href="/pamm/dsp/manage"><span style="color:white;font-weight:bold;">Manage PAMM accounts</span></a>
		</td></tr></table>
	<?php
	break;
	case ("3"):
	
		print('<table id="jointable" style="border:1px solid;border-spacing:0;border-collapse:collapse;">');
		print("<tr><td style='width:100px;border:1px solid;text-align:center;'>РАММ счет</td><td style='width:50px;border:1px solid;text-align:center;'>Система</td><td style='width:70px;border:1px solid;text-align:center;'>Открыт</td><td style='width:200px;border:1px solid;text-align:center;'>Управляющий</td><td style='width:50px;border:1px solid;text-align:center;'>%</td><td style='width:50px;border:1px solid;text-align:center;'>dip&nbsp;%&nbsp;(USD)</td><td style='width:200px;border:1px solid;text-align:center;'>Торговый период</td><td style='width:200px;border:1px solid;text-align:center;'>Rolover</td><td style='width:50px;border:1px solid;text-align:center;'>Срок работы</td><td style='width:50px;border:1px solid;text-align:center;'>График</td><td style='width:50px;border:1px solid;text-align:center;'>Агр</td><td style='width:50px;border:1px solid;text-align:center;'>Капитал счета</td><td style='width:50px;border:1px solid;text-align:center;'>Средняя доходность</td><td style='width:50px;border:1px solid;text-align:center;'>Доходность за текущий<BR>торговый период</td><td style='width:50px;border:1px solid;text-align:center;'>Дневной прирост</td><td style='width:50px;border:1px solid;text-align:center;'>Общая текущая просадка</td><td style='width:50px;border:1px solid;text-align:center;'>Максимальная<BR>просадка</td><td style='width:50px;border:1px solid;text-align:center;'>Мин.<BR>инв.</td><td style='width:50px;border:1px solid;text-align:center;'>Мин.<BR>вывод</td><td style='width:100px;border:1px solid;text-align:center;'>Статус</td><td style='width:100px;border:1px solid;text-align:center;'>Action</td></tr>");		
	        foreach ($rpa as $value)
		{
			switch ($value->active):
					case("0"): $active = "Неактивный";
					break;
					case("1"): $active = "Активный";
					break;
					case("2"): $active = "Закрытый";
					break;
					case("3"): $active = "Невидимый";
					break;
					case("4"): $active = "Забаненный";
					break;
					case("5"): $active = "Закрываемый";
					break;
			endswitch;
			$id	       = $this->mainform_model->obtain_trader_id($value->login);
			$timestart     = $this->mainform_model->obtain_timestart_for_tra($value->login,$id[0]->tid);
			$timeend       = $this->mainform_model->obtain_timeend($value->login);

			if ($timeend[0]->date_close == '0000-00-00 00:00:00')
				$timelive      = round((strtotime(date('y-m-d H:i:s')) - strtotime($timestart[0]->pamm_clients_stat_date))/86400);
			else
				$timelive      = round((strtotime($timeend[0]->date_close) - strtotime($timestart[0]->pamm_clients_stat_date))/86400);

			 $agg = $this->mainform_model->get_aggr($value->login);
			 $sum = 0;
			 $count_agg = 0;
			 foreach($agg as $value1)
				{
				  $sum = $sum + $value1->pamm_tp_profitable;
				    $count_agg++;
				}
			if ($count_agg > 1)
				 $aggr_value = $sum/($count_agg-1);
			else
				$aggr_value = 0;

 			if (abs($aggr_value) >= 0 AND abs($aggr_value) <= 0.051)
				$aggressive_scale = "<img src='/frontend/img/scale1.gif'>";
 			elseif (abs($aggr_value) >= 0.051 AND abs($aggr_value) <= 0.11)
				$aggressive_scale = "<img src='/frontend/img/scale2.gif'>";
 			elseif (abs($aggr_value) > 0.11 AND abs($aggr_value) <= 0.21)
				$aggressive_scale = "<img src='/frontend/img/scale3.gif'>";
 			elseif (abs($aggr_value) > 0.21 AND abs($aggr_value) <= 0.51)
				$aggressive_scale = "<img src='/frontend/img/scale4.gif'>";
 			elseif (abs($aggr_value) > 0.51)
				$aggressive_scale = "<img src='/frontend/img/scale5.gif'>";

			$p_total       = $this->mainform_model->get_previous_total_for_ctpp($value->login);
			$urgent_total  = $this->mainform_model->get_urgent_total($value->login);
			$dynamic       = $this->mainform_model->obtain_dynamic($value->login);

			$sum_dep     = $this->mainform_model->obtain_sum_means($value->login);
			$profitable     = $this->mainform_model->obtain_profitable($value->login);
			$max_dip       = $this->mainform_model->obtain_max_dip_from_db($value->login);

			if ($value->active == '2')
			{
				$current_tp_profit = "-";
				$disp_down_value = "-";
				$disp_dayprofit_value = "-";
				$disp_common_dip = "-";
			}
			else
			{
				$current_tp_profit = number_format(round(100*$profitable[0]->pamm_tp_profitable,2),2,'.','')."%";
				$disp_down_value = number_format($value->down,2,'.','')."%";
				$disp_dayprofit_value = $dynamic[0]->day_profit;
				$disp_common_dip = number_format($value->debt,2,'.','');
			}

			if ($value->active == '1')
			   if ($this->session->userdata('TRADER_ID') != $id[0]->tid )
                             $joinlink = '<a id ="joinlink" class="submit" href="/pamm/dsp/join/'.$value->login.'"><span style="color:white;font-weight:bold;">Join to account</span></a>';
			   else
			     $joinlink = "N/A";
			else
			     $joinlink ="&nbsp;";

       			$print = $this->mainform_model->get_account_status($value->login);
			$color = $this->mainform_model->get_account_color($value->login); 

				if ($value->active == '2')
				   {
				        $trade = '&#151';
				        $rolover = '&#151';
					$color_account = '#999999';
				   }
				elseif ($value->active == '5')
				   {
				        $trade = 'closing';
				        $rolover = 'closing';
					$color_account = '#00F0F0';
				   }

				else
				   {
				        $trade = $print[0]->timeline;
				        $rolover = $print[0]->timeline1;
				        $color_account = $color[0]->color;
				   }

			$fsb = $this->mainform_model->obtain_fsb($value->login); 

			$dip_in_usd = $max_dip[0]->max_dip*$fsb[0]->fsb/100;
			$aggr_value_100 = $aggr_value*100;

			print("<tr id='".$value->login."'><td style='width:100px;border:1px solid;text-align:center;background-color:".$color_account."'>".$value->login."</td><td style='width:50px;border:1px solid;text-align:center;'>MT4</td><td style='width:70px;border:1px solid;text-align:center;'>".substr($value->date_reg,8,2)."-".substr($value->date_reg,5,2)."-".substr($value->date_reg,2,2)."</td><td style='width:200px;border:1px solid;text-align:center;'>".$value->fio."</td><td style='width:50px;border:1px solid;text-align:center;'>".$value->distr_upr."/".$value->distr_inv."</td><td style='width:20px;border:1px solid;text-align:center;'>".$max_dip[0]->max_dip."&nbsp;(".number_format($dip_in_usd,2,'.','').")</td><td style='width:50px;border:1px solid;text-align:center;'>".$trade."</td><td style='width:200px;border:1px solid;text-align:center;'>".$rolover."</td><td style='width:50px;border:1px solid;text-align:center;'>".$timelive."</td><td style='width:100px;border:1px solid;text-align:center;'><img src='/mainform/graph/".$value->login."'></td><td style='width:100px;border:1px solid;text-align:center;'>".$aggressive_scale."</td><td style='width:50px;border:1px solid;text-align:center;'>".number_format($sum_dep[0]->pamm_clients_stat_sum,2,'.','')."</td><td style='width:50px;border:1px solid;text-align:center;'>".number_format($aggr_value_100,2,'.','')."%</td><td style='width:50px;border:1px solid;text-align:center;'>".$current_tp_profit."</td><td style='width:50px;border:1px solid;text-align:center;'>".$disp_dayprofit_value."</td><td style='width:50px;border:1px solid;text-align:center;'>".$disp_common_dip."</td><td style='width:50px;border:1px solid;text-align:center;'>".$disp_down_value."</td><td style='width:50px;border:1px solid;text-align:center;'>".$value->of_i_p."</td><td style='width:50px;border:1px solid;text-align:center;'>".$value->min_withdraw."</td><td style='width:100px;border:1px solid;text-align:center;'>".$active."</td><td style='width:100px;border:1px solid;text-align:center;'>".$joinlink."</td></tr>");		
		}
		print("</table>");

	?>
		<script type="text/javascript">
		highlightTableRows2("jointable","","hoverRow",false);
		</script>
	<table>
	<tr><td><a class="submit" href="/pamm/dsp/add"><span style="color:white;font-weight:bold;">Open PAMM account</span></a></td></tr>
	</table>
	<?php
        break;
	case ("4"):
	echo "<BR>";
	echo $error_msg;
	echo "<BR>";
	?>
	<table><tr><td><a class="submit" href="/pamm/dsp/manage"><span style="color:white;font-weight:bold;">Manage PAMM accounts</span></a></td></tr></table>
	<?php break;
	default:
	
		print('<table id="jointable" style="border:1px solid;border-spacing:0;border-collapse:collapse;">');
		print("<tr id='header'><td style='width:100px;border:1px solid;text-align:center;'>РАММ счет</td><td style='width:50px;border:1px solid;text-align:center;'>Система</td><td style='width:70px;border:1px solid;text-align:center;'>Открыт</td><td style='width:200px;border:1px solid;text-align:center;'>Управляющий</td><td style='width:50px;border:1px solid;text-align:center;'>%</td><td style='width:20px;border:1px solid;text-align:center;'>dip&nbsp;%&nbsp;(USD)</td><td style='width:200px;border:1px solid;text-align:center;'>Торговый период</td><td style='width:200px;border:1px solid;text-align:center;'>Rolover</td><td style='width:50px;border:1px solid;text-align:center;'>Срок работы</td><td style='width:50px;border:1px solid;text-align:center;'>График</td><td style='width:50px;border:1px solid;text-align:center;'>Агр</td><td style='width:50px;border:1px solid;text-align:center;'>Капитал счета</td><td style='width:50px;border:1px solid;text-align:center;'>Средняя доходность</td><td style='width:50px;border:1px solid;text-align:center;'>Доходность за текущий<BR>торговый период</td><td style='width:50px;border:1px solid;text-align:center;'>Дневной прирост</td><td style='width:50px;border:1px solid;text-align:center;'>Общая текущая просадка</td><td style='width:50px;border:1px solid;text-align:center;'>Максимальная<BR>просадка</td><td style='width:50px;border:1px solid;text-align:center;'>Мин.<BR>инв.</td><td style='width:50px;border:1px solid;text-align:center;'>Мин.<BR>вывод</td><td style='width:100px;border:1px solid;text-align:center;'>Статус</td><td style='width:100px;border:1px solid;text-align:center;'>Action</td></tr>");		
	        foreach ($rpa as $value)
		{
			switch ($value->active):
					case('0'): $active = "Неактивный";
					break;
					case('1'): $active = "Активный";
					break;
					case('2'): $active = "Закрытый";
					break;
					case('3'): $active = "Невидимый";
					break;
					case('4'): $active = "Забаненный";
					break;
					case("5"): $active = "Закрываемый";
					break;
			endswitch;
			$id	       = $this->mainform_model->obtain_trader_id($value->login);
			$timestart     = $this->mainform_model->obtain_timestart_for_tra($value->login,$id[0]->tid);
			$timeend       = $this->mainform_model->obtain_timeend($value->login);

			if ($timeend[0]->date_close == '0000-00-00 00:00:00')
				$timelive      = round((strtotime(date('y-m-d H:i:s')) - strtotime($timestart[0]->pamm_clients_stat_date))/86400);
			else
				$timelive      = round((strtotime($timeend[0]->date_close) - strtotime($timestart[0]->pamm_clients_stat_date))/86400);

			 $agg = $this->mainform_model->get_aggr($value->login);
			 $sum = 0;
			 $count_agg = 0;
			 foreach($agg as $value1)
				{
				  $sum = $sum + $value1->pamm_tp_profitable;
				    $count_agg++;
				}
			if ($count_agg > 1)
				 $aggr_value = $sum/($count_agg-1);
			else
				$aggr_value = 0;

 			if (abs($aggr_value) >= 0 AND abs($aggr_value) <= 0.051)
				$aggressive_scale = "<img src='/frontend/img/scale1.gif'>";
 			elseif (abs($aggr_value) > 0.051 AND abs($aggr_value) <= 0.1)
				$aggressive_scale = "<img src='/frontend/img/scale2.gif'>";
 			elseif (abs($aggr_value) > 0.11 AND abs($aggr_value) <= 0.2)
				$aggressive_scale = "<img src='/frontend/img/scale3.gif'>";
 			elseif (abs($aggr_value) > 0.21 AND abs($aggr_value) <= 0.5)
				$aggressive_scale = "<img src='/frontend/img/scale4.gif'>";
 			elseif (abs($aggr_value) > 0.51)
				$aggressive_scale = "<img src='/frontend/img/scale5.gif'>";

			$p_total       = $this->mainform_model->get_previous_total_for_ctpp($value->login);
			$urgent_total  = $this->mainform_model->get_urgent_total($value->login);
			$dynamic       = $this->mainform_model->obtain_dynamic($value->login);
			$sum_dep     = $this->mainform_model->obtain_sum_means($value->login);
			$profitable     = $this->mainform_model->obtain_profitable($value->login);
			$max_dip       = $this->mainform_model->obtain_max_dip_from_db($value->login);

			if ($value->active == '2')
			{
				$current_tp_profit = "-";
				$disp_down_value = "-";
				$disp_dayprofit_value = "-";
				$disp_common_dip = "-";
			}
			else
			{
				$current_tp_profit = number_format(round(100*$profitable[0]->pamm_tp_profitable,2),2,'.','')."%";
				$disp_down_value = number_format($value->down,2,'.','')."%";
				$disp_dayprofit_value = $dynamic[0]->day_profit;
				$disp_common_dip = number_format($value->debt,2,'.','');
			}

			if ($value->active == '1')
			   if ($this->session->userdata('TRADER_ID') != $id[0]->tid )
                             $joinlink = '<a id ="joinlink" class="submit" href="/pamm/dsp/join/'.$value->login.'"><span style="color:white;font-weight:bold;">Join to account</span></a>';
			   else
			     $joinlink = "N/A";
			else
			     $joinlink ="&nbsp;";

       			$print = $this->mainform_model->get_account_status($value->login);
			$color = $this->mainform_model->get_account_color($value->login); 

				if ($value->active == '2')
				   {
				        $trade = '&#151';
				        $rolover = '&#151';
					$color_account = '#999999';
				   }
				elseif ($value->active == '5')
				   {
				        $trade = 'closing';
				        $rolover = 'closing';
					$color_account = '#00F0F0';
				   }

				else
				   {
				        $trade = $print[0]->timeline;
				        $rolover = $print[0]->timeline1;
				        $color_account = $color[0]->color;
				   }

			$dip_in_usd = $max_dip[0]->max_dip*$fsb[0]->fsb/100;
			$aggr_value_100 = $aggr_value*100;

			print("<tr id='".$value->login."'><td style='width:100px;border:1px solid;text-align:center;background-color:".$color_account."'>".$value->login."</td><td style='width:50px;border:1px solid;text-align:center;'>MT4</td><td style='width:70px;border:1px solid;text-align:center;'>".substr($value->date_reg,8,2)."-".substr($value->date_reg,5,2)."-".substr($value->date_reg,2,2)."</td><td style='width:200px;border:1px solid;text-align:center;'>".$value->fio."</td><td style='width:50px;border:1px solid;text-align:center;'>".$value->distr_upr."/".$value->distr_inv."</td><td style='width:20px;border:1px solid;text-align:center;'>".$max_dip[0]->max_dip."&nbsp;(".number_format($dip_in_usd,2,'.','').")</td><td style='width:50px;border:1px solid;text-align:center;'>".$trade."</td><td style='width:200px;border:1px solid;text-align:center;'>".$rolover."</td><td style='width:50px;border:1px solid;text-align:center;'>".$timelive."</td><td style='width:100px;border:1px solid;text-align:center;'><img src='/mainform/graph/".$value->login."'></td><td style='width:100px;border:1px solid;text-align:center;'>".$aggressive_scale."</td><td style='width:50px;border:1px solid;text-align:center;'>".$sum_dep[0]->oamm_cvlients_stat_sum."</td><td style='width:50px;border:1px solid;text-align:center;'>".number_format($aggr_value_100,2,'.','')."%</td><td style='width:50px;border:1px solid;text-align:center;'>".$current_tp_profit."</td><td style='width:50px;border:1px solid;text-align:center;'>".$dynamic[0]->day_profit."</td><td style='width:50px;border:1px solid;text-align:center;'>".round($value->down,2)."</td><td style='width:50px;border:1px solid;text-align:center;'>".$value->of_i_p."</td><td style='width:50px;border:1px solid;text-align:center;'>".$value->min_withdraw."</td><td style='width:100px;border:1px solid;text-align:center;'>".$active."</td><td style='width:100px;border:1px solid;text-align:center;'>".$joinlink."</td></tr>");		
		}
		print("</table>");
	?>
	<table>
	<tr><td><a class="submit" href="/pamm/dsp/add"><span style="color:white;font-weight:bold;">Open PAMM account</span></a></td></tr>
	</table>
	highlightTableRows2("jointable","","hoverRow",false);
	<?php break;
      case("9"):
		$this->load->view('join');
      break;	 
      case("22"):
                print("Unpaid loss on account ".$acc_number."!<BR>Join is not avaliable at the moment!<BR>");
		print("<table><tr><td><a class='submit' href='/mainform/index/".$this->session->userdata('TRADER_ID')."'><span style='color:white;font-weight:bold;'>Return</span></a></td></tr></table>");
      break;	
      case("23"):
                print($message."<BR>");
		print("<table><tr><td><a class='submit' href='/mainform/index/".$this->session->userdata('TRADER_ID')."'><span style='color:white;font-weight:bold;'>Return</span></a></td></tr></table>");
      break;	
      case("29"):
               $this->load->view('stat');
      break;	
      case("30"):
		print("Account ".$acc_number_joint." is already joint!<BR>");
		print("<table><tr><td><a class='submit' href='/'><span style='color:white;font-weight:bold;'>Return</span></a></td></tr></table>");
      break;	
      default:
		print('<table id="jointable" style="border:1px solid;border-spacing:0;border-collapse:collapse;">');
		print("<tr id='header'><td style='width:100px;border:1px solid;text-align:center;'>РАММ счет</td><td style='width:50px;border:1px solid;text-align:center;'>Система</td><td style='width:70px;border:1px solid;text-align:center;'>Открыт</td><td style='width:200px;border:1px solid;text-align:center;'>Управляющий</td><td style='width:50px;border:1px solid;text-align:center;'>%</td><td style='width:20px;border:1px solid;text-align:center;'>dip&nbsp;%&nbsp;(USD)</td><td style='width:200px;border:1px solid;text-align:center;'>Торговый период</td><td style='width:200px;border:1px solid;text-align:center;'>Rolover</td><td style='width:50px;border:1px solid;text-align:center;'>Срок работы</td><td style='width:50px;border:1px solid;text-align:center;'>График</td><td style='width:50px;border:1px solid;text-align:center;'>Агр</td><td style='width:50px;border:1px solid;text-align:center;'>Капитал счета</td><td style='width:50px;border:1px solid;text-align:center;'>Средняя доходность</td><td style='width:50px;border:1px solid;text-align:center;'>Доходность за текущий<BR>торговый период</td><td style='width:50px;border:1px solid;text-align:center;'>Дневной прирост</td><td style='width:50px;border:1px solid;text-align:center;'>Общая текущая просадка</td><td style='width:50px;border:1px solid;text-align:center;'>Максимальная<BR>просадка</td><td style='width:50px;border:1px solid;text-align:center;'>Мин.<BR>инв.</td><td style='width:50px;border:1px solid;text-align:center;'>Мин.<BR>вывод</td><td style='width:100px;border:1px solid;text-align:center;'>Статус</td><td style='width:100px;border:1px solid;text-align:center;'>Action</td></tr>");		
	        foreach ($rpa as $value)
		{
			switch ($value->active):
					case('0'): $active = "Неактивный";
					break;
					case('1'): $active = "Активный";
					break;
					case('2'): $active = "Закрытый";
					break;
					case('3'): $active = "Невидимый";
					break;
					case('4'): $active = "Забаненный";
					break;
					case("5"): $active = "Закрываемый";
					break;
			endswitch;
			$id	       = $this->mainform_model->obtain_trader_id($value->login);
			$timestart     = $this->mainform_model->obtain_timestart_for_tra($value->login,$id[0]->tid);
			$timeend       = $this->mainform_model->obtain_timeend($value->login);

			if ($timeend[0]->date_close == '0000-00-00 00:00:00')
				$timelive      = round((strtotime(date('y-m-d H:i:s')) - strtotime($timestart[0]->pamm_clients_stat_date))/86400);
			else
				$timelive      = round((strtotime($timeend[0]->date_close) - strtotime($timestart[0]->pamm_clients_stat_date))/86400);

			 $agg = $this->mainform_model->get_aggr($value->login);
			 $sum = 0;
			 $count_agg = 0;
			 foreach($agg as $value1)
				{
				  $sum = $sum + $value1->pamm_tp_profitable;
				    $count_agg++;
				}
			if ($count_agg > 1)
				 $aggr_value = $sum/($count_agg-1);
			else
				$aggr_value = 0;

 			if (abs($aggr_value) >= 0 AND abs($aggr_value) <= 0.051)
				$aggressive_scale = "<img src='/frontend/img/scale1.gif'>";
 			elseif (abs($aggr_value) > 0.051 AND abs($aggr_value) <= 0.11)
				$aggressive_scale = "<img src='/frontend/img/scale2.gif'>";
 			elseif (abs($aggr_value) > 0.11 AND abs($aggr_value) <= 0.21)
				$aggressive_scale = "<img src='/frontend/img/scale3.gif'>";
 			elseif (abs($aggr_value) > 0.21 AND abs($aggr_value) <= 0.51)
				$aggressive_scale = "<img src='/frontend/img/scale4.gif'>";
 			elseif (abs($aggr_value) > 0.51)
				$aggressive_scale = "<img src='/frontend/img/scale5.gif'>";

			$p_total       = $this->mainform_model->get_previous_total_for_ctpp($value->login);
			$urgent_total  = $this->mainform_model->get_urgent_total($value->login);
			$dynamic       = $this->mainform_model->obtain_dynamic($value->login);
			$sum_dep     = $this->mainform_model->obtain_sum_means($value->login);
			$profitable     = $this->mainform_model->obtain_profitable($value->login);
			$max_dip       = $this->mainform_model->obtain_max_dip_from_db($value->login);

			if ($value->active == '2')
			{
				$current_tp_profit = "-";
				$disp_down_value = "-";
				$disp_dayprofit_value = "-";
				$disp_common_dip = "-";
			}
			else
			{
				$current_tp_profit = number_format(round(100*$profitable[0]->pamm_tp_profitable,2),2,'.','')."%";
				$disp_down_value = number_format($value->down,2,'.','')."%";
				$disp_dayprofit_value = $dynamic[0]->day_profit;
				$disp_common_dip = number_format($value->debt,2,'.','');
			}

			if ($value->active == '1')
			   if ($this->session->userdata('TRADER_ID') != $id[0]->tid )
                             $joinlink = '<a id ="joinlink" class="submit" href="/pamm/dsp/join/'.$value->login.'"><span style="color:white;font-weight:bold;">Join to account</span></a>';
			   else
			     $joinlink = "N/A";
			else
			     $joinlink ="&nbsp;";

       			$print = $this->mainform_model->get_account_status($value->login);
			$color = $this->mainform_model->get_account_color($value->login); 

				if ($value->active == '2')
				   {
				        $trade = '&#151';
				        $rolover = '&#151';
					$color_account = '#999999';
				   }
				elseif ($value->active == '5')
				   {
				        $trade = 'closing';
				        $rolover = 'closing';
					$color_account = '#00F0F0';
				   }

				else
				   {
				        $trade = $print[0]->timeline;
				        $rolover = $print[0]->timeline1;
				        $color_account = $color[0]->color;
				   }

			$fsb = $this->mainform_model->obtain_fsb($value->login); 		
			$dip_in_usd = $max_dip[0]->max_dip*$fsb[0]->fsb/100;
			$aggr_value_100 = $aggr_value*100;

			print("<tr id='".$value->login."'><td style='width:100px;border:1px solid;text-align:center;background-color:".$color_account."'>".$value->login."</td><td style='width:50px;border:1px solid;text-align:center;'>MT4</td><td style='width:70px;border:1px solid;text-align:center;'>".substr($value->date_reg,8,2)."-".substr($value->date_reg,5,2)."-".substr($value->date_reg,2,2)."</td><td style='width:200px;border:1px solid;text-align:center;'>".$value->fio."</td><td style='width:50px;border:1px solid;text-align:center;'>".$value->distr_upr."/".$value->distr_inv."</td><td style='width:20px;border:1px solid;text-align:center;'>".$max_dip[0]->max_dip."&nbsp;(".number_format($dip_in_usd,2,'.','').")</td><td style='width:50px;border:1px solid;text-align:center;'>".$trade."</td><td style='width:200px;border:1px solid;text-align:center;'>".$rolover."</td><td style='width:50px;border:1px solid;text-align:center;'>".$timelive."</td><td style='width:100px;border:1px solid;text-align:center;'><img src='/mainform/graph/".$value->login."'></td><td style='width:100px;border:1px solid;text-align:center;'>".$aggressive_scale."</td><td style='width:50px;border:1px solid;text-align:center;'>".number_format($sum_dep[0]->pamm_clients_stat_sum,2,'.','')."</td><td style='width:50px;border:1px solid;text-align:center;'>".number_format($aggr_value_100,2,'.','')."%</td><td style='width:50px;border:1px solid;text-align:center;'>".$current_tp_profit."</td><td style='width:50px;border:1px solid;text-align:center;'>".$disp_dayprofit_value."</td><td style='width:50px;border:1px solid;text-align:center;'>".$disp_common_dip."</td><td style='width:50px;border:1px solid;text-align:center;'>".$disp_down_value."</td><td style='width:50px;border:1px solid;text-align:center;'>".$value->of_i_p."</td><td style='width:50px;border:1px solid;text-align:center;'>".$value->min_withdraw."</td><td style='width:100px;border:1px solid;text-align:center;'>".$active."</td><td style='width:100px;border:1px solid;text-align:center;'>".$joinlink."</td></tr>");		
		}
		print("</table>");
	?>
		<script type="text/javascript">
		highlightTableRows2("jointable","","hoverRow",false);
		</script>
	<table>
	<tr><td><a class="submit" href="/pamm/dsp/add"><span style="color:white;font-weight:bold;">Open PAMM account</span></a></td></tr>
	</table>
	<?php
	 break;

      endswitch; ?>
	
    </p>
  </div>

  <div class="tabbertab">
    <h2>Invest to PAMM account</h2>
    <?php
      switch ($from):
      case("13"):
		$this->load->view('injoin_requests');
      break;
      case("18"):
		$this->load->view('inout1');
      break;	 
      case("19"):
		$this->load->view('in1');
      break;	 
      case("20"):
		$this->load->view('out1');
      break;	 
      case("24"):
		$this->load->view('unjoin');
      break;	 
      case("25"):
                print($message."<BR>");
		print("<table><tr><td><a class='submit' href='/pamm/dsp/invest'><span style='color:white;font-weight:bold;'>Return</span></a></td></tr></table>");
      break;	
      case("26"):
		$this->load->view('rejoin');
      break;	
      case("34"):
		$this->load->view('stat_inv');
      break;	
      case("23"):
                print($message."<BR>");
		print("<table><tr><td><a class='submit' href='/mainform/index/".$this->session->userdata('TRADER_ID')."'><span style='color:white;font-weight:bold;'>Return</span></a></td></tr></table>");
      break;	
      default:
        	include("invest.php");
                print('<input type="hidden" id="jpa_trader_id" value="'.$this->session->userdata('TRADER_ID').'">');
		print('<table id="jpa_table" style="border:1px solid;border-spacing:0;border-collapse:collapse;">');
		print("<tr id='header'><td style='width:100px;border:1px solid;text-align:center;'>РАММ счет</td><td style='width:50px;border:1px solid;text-align:center;'>Система</td><td style='width:90px;border:1px solid;text-align:center;'>Открыт</td><td style='width:200px;border:1px solid;text-align:center;'>Управляющий</td><td style='width:50px;border:1px solid;text-align:center;'>%</td><td style='width:20px;border:1px solid;text-align:center;'>dip&nbsp;%&nbsp;(USD)</td><td style='width:200px;border:1px solid;text-align:center;'>Торговый период</td><td style='width:200px;border:1px solid;text-align:center;'>Rolover</td><td style='width:50px;border:1px solid;text-align:center;'>Срок работы</td><td style='width:50px;border:1px solid;text-align:center;'>График</td><td style='width:50px;border:1px solid;text-align:center;'>Агр</td><td style='width:50px;border:1px solid;text-align:center;'>Капитал счета</td><td style='width:50px;border:1px solid;text-align:center;'>Средняя доходность</td><td style='width:50px;border:1px solid;text-align:center;'>Доходность за текущий<BR>торговый период</td><td style='width:50px;border:1px solid;text-align:center;'>Дневной прирост</td><td style='width:50px;border:1px solid;text-align:center;'>Общая текущая просадка</td><td style='width:50px;border:1px solid;text-align:center;'>Максимальная<BR>просадка</td><td style='width:50px;border:1px solid;text-align:center;'>Свой капитал</td><td style='width:50px;border:1px solid;text-align:center;'>Своя прибыль</td><td style='width:50px;border:1px solid;text-align:center;'>Свои средства</td><td style='width:100px;border:1px solid;text-align:center;'>Статус</td><td style='width:20px;border:1px solid;text-align:center;'>Состояние</td><td style='width:100px;border:1px solid;text-align:center;'>Action</td><td style='width:100px;border:1px solid;text-align:center;'>Injoin</td><td style='width:100px;border:1px solid;text-align:center;'>Requests</td></tr>");		
	        foreach ($jpa as $value)
		{
			$action = "";
		        $status = $this->mainform_model->get_invested_accounts_status($this->session->userdata('TRADER_ID'),$value->login);
			$single = $this->mainform_model->get_rejoin_number($this->session->userdata('TRADER_ID'),$value->login);
				 switch ($value->active):
					case('0'): $active = "Неактивный";
					break;
					case('1'): $active = "Активный";
					break;
					case('2'): $active = "Закрытый";
					$action="&nbsp;";
					break;
					case('3'): $active = "Невидимый";
					break;
					case('4'): $active = "Забаненный";
					break;
					case("5"): $active = "Закрываемый";
					$action="&nbsp;";
					break;
			endswitch;
			switch ($status[0]->pamm_invested_accounts_status):
					case('1'): $pia_status = "Присоединен";
						   if ($action != "&nbsp;")
							   $action = '<a class="submit" href="/pamm/dsp/unjoin/'.$value->login.'"><span style="color:white;font-weight:bold;">Unjoint</span></a>';
					break;
					case('2'): $pia_status = "Отсоединен";
						   if ($action != "&nbsp;")
						      {
							if ($single[0]->request_count == 0)
							   $action = '<a class="submit" id="investorinoutlink" href="/pamm/dsp/rejoin/'.$value->login.'"><span style="color:white;font-weight:bold;">Rejoin</span></a>';
							else
							   $action = "";
					break;        }
					case('3'): $pia_status = "Забанен";
						   if ($action != "&nbsp;")
							   $action = "Not applicable";						
					break;
					case('4'): $pia_status = "В ожидании";
						   $action = "";						

			endswitch;
			$own_means     = $this->mainform_model->obtain_own_means_for_inv($value->login,$this->session->userdata('TRADER_ID'));
       		        $sum_means     = $this->mainform_model->obtain_sum_means($value->login);
			$timestart     = $this->mainform_model->obtain_timestart_for_inv($value->login,$this->session->userdata('TRADER_ID'));
			$own_profit    = $this->mainform_model->obtain_own_profit_for_inv($value->login,$this->session->userdata('TRADER_ID'));

			$timeend       = $this->mainform_model->obtain_timeend($value->login);
			if (count($timestart) > 0)
			{
				if ($timeend[0]->date_close == '0000-00-00 00:00:00')
					$timelive      = round((strtotime(date('y-m-d H:i:s')) - strtotime($timestart[0]->request_date))/86400);
				else
					$timelive      = round((strtotime($timeend[0]->date_close) - strtotime($timestart[0]->request_date))/86400);
			}
			else
			$timelive= "N/A";
			 $agg = $this->mainform_model->get_aggr($value->login);
			 $sum = 0;
			 $count_agg = 0;
			 foreach($agg as $value1)
				{
				  $sum = $sum + $value1->pamm_tp_profitable;
				    $count_agg++;
				}
			if ($count_agg > 1)
				 $aggr_value = $sum/($count_agg-1);
			else
				$aggr_value = 0;

 			if (abs($aggr_value) >= 0 AND abs($aggr_value) <= 0.051)
				$aggressive_scale = "<img src='/frontend/img/scale1.gif'>";
 			elseif (abs($aggr_value) >= 0.051 AND abs($aggr_value) <= 0.1)
				$aggressive_scale = "<img src='/frontend/img/scale2.gif'>";
 			elseif (abs($aggr_value) >= 0.11 AND abs($aggr_value) <= 0.2)
				$aggressive_scale = "<img src='/frontend/img/scale3.gif'>";
 			elseif (abs($aggr_value) >= 0.21 AND abs($aggr_value) <= 0.5)
				$aggressive_scale = "<img src='/frontend/img/scale4.gif'>";
 			elseif (abs($aggr_value) >= 0.51)
				$aggressive_scale = "<img src='/frontend/img/scale5.gif'>";

			$p_total       = $this->mainform_model->get_previous_total_for_ctpp($value->login);
			$urgent_total  = $this->mainform_model->get_urgent_total($value->login);
			$dynamic       = $this->mainform_model->obtain_dynamic($value->login);
			$profitable     = $this->mainform_model->obtain_profitable($value->login);
			$max_dip       = $this->mainform_model->obtain_max_dip_from_db($value->login);
			$debt = $this->mainform_model->obtain_debt($value->login);

			if ($value->active == '2')
			{
				$current_tp_profit = "-";
				$disp_down_value = "-";
				$disp_dayprofit_value = "-";
				$own_profit_value = "-";
				$own_means_value = "-";
				$pia_status = "-";
				$max_common_dip = "-";

			}
			else
			{
				$current_tp_profit = number_format(round(100*$profitable[0]->pamm_tp_profitable,2),2,'.','')."%";
				$disp_down_value = number_format($value->down,2,'.','')."%";
				$disp_dayprofit_value = $dynamic[0]->day_profit;
				$own_profit_value = number_format($own_profit[0]->pamm_clients_stat_sum,2,'.','');
				$own_means_value = number_format($own_means[0]->pamm_clients_stat_sum,2,'.','');
				$max_common_dip = number_format($debt[0]->debt,2,'.',''); 
			}

				$inoutlink =   '<a class="submit" id="investorinoutlink" href="/pamm/dsp/inout1/'.$value->login.'"><span style="color:white;font-weight:bold;">In/Out</span></a>';
	
			$requestlink =   '<a class="submit" id="investorinoutlink" href="/pamm/dsp/join_requests/'.$value->login.'"><span style="color:white;font-weight:bold;">Requests</span></a>';

       			$print = $this->mainform_model->get_account_status($value->login);
			$color = $this->mainform_model->get_account_color($value->login); 

				if ($value->active == '2')
				   {
				        $trade = '&#151';
				        $rolover = '&#151';
					$color_account = '#999999';
				   }
				elseif ($value->active == '5')
				   {
				        $trade = 'closing';
				        $rolover = 'closing';
					$color_account = '#00F0F0';
				   }

				else
				   {
				        $trade = $print[0]->timeline;
				        $rolover = $print[0]->timeline1;
				        $color_account = $color[0]->color;
				   }

			$fsb = $this->mainform_model->obtain_fsb($value->login); 

			$dip_in_usd = $max_dip[0]->max_dip*$fsb[0]->fsb/100;
			$aggr_value_100 = $aggr_value*100;
			$own_value = $own_means_value + $own_profit_value;

			// in case of unjoint we make own_means and common_means equal zero

		$status = $this->mainform_model->get_invested_accounts_status($this->session->userdata('TRADER_ID'),$value->login);

		if ($status[0]->pamm_invested_accounts_status != "1")	// to throw out unjoint investors
		{
			$own_value = "0.00";
			$own_means_value = "0.00";
		}

			print("<tr id='".$value->login."' ><td style='width:100px;border:1px solid;text-align:center;background-color:".$color_account."'>".$value->login."</td><td style='width:50px;border:1px solid;text-align:center;'>MT4</td><td style='width:90px;border:1px solid;text-align:center;'>".substr($value->date_reg,8,2)."-".substr($value->date_reg,5,2)."-".substr($value->date_reg,2,2)."</td><td style='width:200px;border:1px solid;text-align:center;'>".$value->fio."</td><td style='width:50px;border:1px solid;text-align:center;'>".$value->distr_upr."/".$value->distr_inv."</td><td style='width:20px;border:1px solid;text-align:center;'>".$max_dip[0]->max_dip."&nbsp;(".number_format($dip_in_usd,2,'.','').")</td><td style='width:200px;border:1px solid;text-align:center;'>".$trade."</td><td style='width:200px;border:1px solid;text-align:center;'>".$rolover."</td><td style='width:50px;border:1px solid;text-align:center;'>".$timelive."</td><td style='width:100px;border:1px solid;text-align:center;'><img src='/mainform/graph/".$value->login."'></td><td style='width:100px;border:1px solid;text-align:center;'>".$aggressive_scale."</td><td style='width:50px;border:1px solid;text-align:center;'>".number_format($sum_means[0]->pamm_clients_stat_sum,2,'.','')."</td><td style='width:50px;border:1px solid;text-align:center;'>".number_format($aggr_value_100,2,'.','')."%</td><td style='width:50px;border:1px solid;text-align:center;'>".$current_tp_profit."</td><td style='width:50px;border:1px solid;text-align:center;'>".$disp_dayprofit_value."</td><td style='width:50px;border:1px solid;text-align:center;'>".$max_common_dip."</td><td style='width:50px;border:1px solid;text-align:center;'>".$disp_down_value."</td><td style='width:50px;border:1px solid;text-align:center;'>".number_format($own_value,2,'.','')."</td><td style='width:50px;border:1px solid;text-align:center;'>".$own_profit_value."</td><td style='width:50px;border:1px solid;text-align:center;'>".$own_means_value."</td><td style='width:100px;border:1px solid;text-align:center;'>".$active."</td><td style='width:20px;border:1px solid;text-align:center;'>".$pia_status."</td><td style='width:100px;border:1px solid;padding-left:40px;'>".$action."</td><td style='width:100px;border:1px solid;padding-left:40px;'>".$inoutlink."</td><td style='width:100px;border:1px solid;padding-left:40px;'>".$requestlink."</td></tr>");		
		}
		print("</table>");

	?>
		<script type="text/javascript">
		highlightTableRowsInv("jpa_table","","hoverRow",false);
		</script>
        <?php
	break;
	endswitch;
	?>
  </div>

<div class="tabbertab">
    <h2>Manage PAMM accounts</h2>
     <?php
       switch($from):
	case("5"):
		$this->load->view('act_sep');
	break;
	case("6"):
		$this->load->view('inout');
	break;
	case("7"):
		$this->load->view('in');
	break;
	case("17"):
		$this->load->view('out');
	break;
	case("27"):
		$this->load->view('close');
	break;
        case("32"):
                $this->load->view('stat_upr');
        break;	
      case ("36"):
      echo "<BR>";
      echo $error_msg;
      echo "<BR>";
      ?>
      <table><tr><td><a class="submit" href="/pamm/dsp/manage"><span style="color:white;font-weight:bold;">Manage PAMM accounts</span></a></td></tr></table>
      <?php break;

	default:
        	include("header.php");
		print('<table id="mpa_table" style="border:1px solid;border-spacing:0;border-collapse:collapse;">');
		print("<tr id='header'><td style='width:100px;border:1px solid;text-align:center;'>РАММ счет</td><td style='width:50px;border:1px solid;text-align:center;'>Система</td><td style='width:110px;border:1px solid;text-align:center;'>Открыт</td><td style='width:50px;border:1px solid;text-align:center;'>%</td><td style='width:20px;border:1px solid;text-align:center;'>dip&nbsp;%&nbsp;(USD)</td><td style='width:50px;border:1px solid;text-align:center;'>Общая текущая просадка</td><td style='width:200px;border:1px solid;text-align:center;'>Торговый период</td><td style='width:200px;border:1px solid;text-align:center;'>Роловер</td><td style='width:50px;border:1px solid;text-align:center;'>Капитал счета</td><td style='width:50px;border:1px solid;text-align:center;'>Средняя доходность</td><td style='width:50px;border:1px solid;text-align:center;'>Свой капитал</td><td style='width:50px;border:1px solid;text-align:center;'>Текущая прибыль</td><td style='width:50px;border:1px solid;text-align:center;'>Свои средства</td><td style='width:50px;border:1px solid;text-align:center;'>Инвесторы</td><td style='width:50px;border:1px solid;text-align:center;'>Общий капитал<BR>инвесторов</td><td style='width:50px;border:1px solid;text-align:center;'>Текущая прибыль<BR>инвесторов</td><td style='width:50px;border:1px solid;text-align:center;'>Свои средства<BR>инвесторов</td><td style='width:50px;border:1px solid;text-align:center;'>Заявки на ввод</td><td style='width:50px;border:1px solid;text-align:center;'>Заявки на вывод</td><td style='width:100px;border:1px solid;text-align:center;'>Статус</td><td style='width:50px;border:1px solid;text-align:center;'>Action</td><td style='width:50px;border:1px solid;text-align:center;'>Activate</td><td style='width:50px;border:1px solid;text-align:center;'>In/Out</td></tr>");		
	        foreach ($mpa as $value)
		{
			$action="<a class='submit' href='/pamm/dsp/close/".$value->login."'><span style='color:white;font-weight:bold;'>Close</span></a>";
			switch ($value->active):
					case('0'): $active = "Неактивный";
					$action="&nbsp;";
					break;
					case('1'): $active = "Активный";
					break;
					case('2'): $active = "Закрытый";
					$action="&nbsp;";
					break;
					case('3'): $active = "Невидимый";
					break;
					case('4'): $active = "Забаненный";
					break;
					case("5"): $active = "Закрываемый";
					$action="&nbsp;";
					break;
			endswitch;

			$sum_dep     = $this->mainform_model->obtain_deposit($value->login);
			$own_profit  = $this->mainform_model->obtain_own_profit($value->login);
			$own_means   = $this->mainform_model->obtain_own_means($value->login);
			$inv_number  = $this->mainform_model->obtain_inv_number($value->login);
			$inv_means   = $this->mainform_model->obtain_inv_means($value->login);
			$inv_profit  = $this->mainform_model->obtain_inv_profit($value->login);
			$inv_common  = $inv_means[0]->pamm_clients_stat_sum + $inv_profit[0]->pamm_clients_stat_sum;
			$own_common  = $own_means[0]->pamm_clients_stat_sum + $own_profit[0]->pamm_clients_stat_sum;
			$in          = $this->mainform_model->count_requests_in($value->login);
			$out         = $this->mainform_model->count_requests_out($value->login);
			$max_dip     = $this->mainform_model->obtain_max_dip_from_db($value->login);

			 $agg = $this->mainform_model->get_aggr($value->login);
			 $sum = 0;
			 $count_agg = 0;
			 foreach($agg as $value1)
				{
				  $sum = $sum + $value1->pamm_tp_profitable;
				    $count_agg++;
				}
			if ($count_agg > 1)
				 $aggr_value = $sum/($count_agg-1);
			else
				$aggr_value = 0;

       			$print = $this->mainform_model->get_account_status($value->login);
			$color = $this->mainform_model->get_account_color($value->login); 

				if ($value->active == '2')
				   {
				        $trade = '&#151';
				        $rolover = '&#151';
					$color_account = '#999999';
				   }
				elseif ($value->active == '5')
				   {
				        $trade = 'closing';
				        $rolover = 'closing';
					$color_account = '#00F0F0';
				   }

				else
				   {
				        $trade = $print[0]->timeline;
				        $rolover = $print[0]->timeline1;
				        $color_account = $color[0]->color;
				   }


			if ($value->active == '0')
			{
				$actlink ='<a class="submit" id="actlink" href="/pamm/dsp/activate/'.$value->login.'"><span style="color:white;font-weight:bold;">Activate account</span></a>';
				$inoutlink = "&nbsp;";
			}
			else
			{
				$actlink   = "&nbsp;";
				$inoutlink = '<a class="submit" id="actlink" href="/pamm/dsp/inout/'.$value->login.'"><span style="color:white;font-weight:bold;">Inout</span></a>';
			}
			
			$fsb = $this->mainform_model->obtain_fsb($value->login); 

			$dip_in_usd = $max_dip[0]->max_dip*$fsb[0]->fsb/100;
			$debt = $this->mainform_model->obtain_debt($value->login); 

			print("<tr id='".$value->login."'><td style='width:100px;border:1px solid;text-align:center;background-color:".$color_account."'>".$value->login."</td><td style='width:50px;border:1px solid;text-align:center;'>MT4</td><td style='width:110px;border:1px solid;text-align:center;'>".substr($value->date_reg,8,2)."-".substr($value->date_reg,5,2)."-".substr($value->date_reg,2,2)."</td><td style='width:50px;border:1px solid;text-align:center;'>".$value->distr_upr."/".$value->distr_inv."</td><td style='width:20px;border:1px solid;text-align:center;'>".$max_dip[0]->max_dip."&nbsp;(".number_format($dip_in_usd,2,'.','').")</td><td style='width:50px;border:1px solid;text-align:center;'>".number_format($debt[0]->debt,2,'.','')."</td><td style='width:50px;border:1px solid;text-align:center;'>".$trade."</td><td style='width:200px;border:1px solid;text-align:center;'>".$rolover."</td><td style='width:50px;border:1px solid;text-align:center;'>".number_format($sum_dep[0]->pamm_clients_stat_sum,2,'.','')."</td><td style='width:50px;border:1px solid;text-align:center;'>".round($aggr_value*100,2)."%</td><td style='width:50px;border:1px solid;text-align:center;'>".number_format($own_common,2,'.','')."</td><td style='width:50px;border:1px solid;text-align:center;'>".number_format($own_profit[0]->pamm_clients_stat_sum,2,'.','')."</td><td style='width:50px;border:1px solid;text-align:center;'>".number_format($own_means[0]->pamm_clients_stat_sum,2,'.','')."</td><td style='width:50px;border:1px solid;text-align:center;'>".count($inv_number)."</td><td style='width:50px;border:1px solid;text-align:center;'>".number_format($inv_common,2,'.','')."</td><td style='width:50px;border:1px solid;text-align:center;'>".number_format($inv_profit[0]->pamm_clients_stat_sum,2,'.','')."</td><td style='width:50px;border:1px solid;text-align:center;'>".number_format($inv_means[0]->pamm_clients_stat_sum,2,'.','')."</td><td style='width:50px;border:1px solid;text-align:center;'>".$in."</td><td style='width:50px;border:1px solid;text-align:center;'>".$out."</td><td style='width:100px;border:1px solid;text-align:center;'>".$active."</td><td style='width:50px;border:1px solid;text-align:center;padding-left:7px;padding-bottom:12px;'>".$action."</td><td style='width:50px;border:1px solid;text-align:center;padding-left:7px;padding-bottom:12px;'>".$actlink."</td><td style='width:50px;border:1px solid;text-align:center;padding-left:7px;padding-bottom:12px;'>".$inoutlink."</td></tr>");		
		}
		print("</table>");
     ?>
		<script type="text/javascript">
		highlightTableRowsUpr("mpa_table","","hoverRow",false);
		</script>
     <?php
        break;
	endswitch;
     ?>
  </div>

  <div class="tabbertab">
    <h2>Wallets</h2>
    <p><table style="border:1px solid;border-spacing:0;border-collapse:collapse;">
	<tr>
	<?php
	foreach ($wallet as $key=>$value)
		{
		  echo '<td style="border:1px solid;">';
		  echo "<h1>    ".$key."</h1>    ";
		  echo "</td>";
		}
	?>
	</tr>
	<tr>
	<?php
	foreach ($wallet as $key=>$value)
		{
		  echo '<td align="center" style="border:1px solid;">';
		  echo $value;
		  echo "</td>";
		}
	?>
	</tr>

	</table>
	</p>

</div>
  <div class="tabbertab">
    <h2>Engine controls</h2>
     <?php
       switch($from):
	case("15"):
		print("<BR><BR>Implemented ".$count_trader_requests." trader requests<BR><BR>");
		print("<BR><BR>Implemented ".$count_investor_requests." investor requests<BR><BR>");
	break;
	case("16"):

  	        print("Foo:".$foo."<BR>Foo_array:");
  	        print_r($foo_array);
	//	print("<BR>Upr=".$inv6." Inv5=".$inv5." Inv7=".$inv7);
	//	print_r($count);

	break;
	case("21"):
                  	        print("Account:".$acc_number." Balance database:".$b_db." Balance MT4:".$b_mt4."<BR><BR>");
				if (date('i')<45)
				       print("<font color='red'>TRADING IS NOT FINISHED YET!</font>");
	break;
	case("31"):
	               	        print("Result:".$result);
	break;
	case("41"):
               	        print("Calculated");
	break;

       endswitch
      ?>	
	<!--<table cellspadding="5"><tr><td><a class="submit" href="/pamm/dsp/imp_requests"><span style="color:white;font-weight:bold;">Implement queries</span></a></td><td><a class="submit" href="/pamm/dsp/divide/319029"><span style="color:white;font-weight:bold;">Divide profit Igor</span></a></td><td><a class="submit" href="/pamm/dsp/divide/318976"><span style="color:white;font-weight:bold;">Divide profit Fedor max</span></a></td><td><a class="submit" href="/pamm/dsp/divide/319011"><span style="color:white;font-weight:bold;">Divide struggle for loss cent</span></a></td></tr></table>-->
        <!-- <table cellspadding="5"><tr><td><a class="submit" href="/pamm/dsp/divide"><span style="color:white;font-weight:bold;">Divide</span></a></td></tr></table> -->
        <!--<table cellspadding="5"><tr><td><a class="submit" href="/pamm/dsp/test/100/320015/22/comm100"><span style="color:white;font-weight:bold;">Lock</span></a></td></tr></table>-->
        <table cellspadding="5"><tr><td><a class="submit" href="/pamm/dsp/test/337377"><span style="color:white;font-weight:bold;">Calculate TP</span></a></td></tr></table>
	<!--<table cellspadding="5"><tr><td>No controls</td></tr></table>-->
</div>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         

</div>
<script type="text/javascript">

/* Since we specified manualStartup=true, tabber will not run after
   the onload event. Instead let's run it now, to prevent any delay
   while images load.
*/

tabberAutomatic(tabberOptions);

</script>

</body>
</html>
