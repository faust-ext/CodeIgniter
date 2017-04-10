<form method="POST" name="form6" id="form6" action="/pamm/dsp/actsubmit/<?php echo $acc_number?>">
<table><tr><td>
<select name="wallets" id="wallets" onChange="cur(<?php echo $quotes[0]?>,<?php echo $quotes[1]?>,<?php echo $quotes[2]?>,<?php echo $quotes[3]?>,<?php echo $quotes[4]?>,<?php echo $quotes[5]?>);">
<?php					
$message_not_enough_sum = "На данном кошельке недостаточно средств для активации счета.Выберите другой кошелек или пополните этот.";
	$sel = "";
	foreach ($wallet as $key=>$value)
		{  	
			if ($value == "0.00")
			    {
				$dis = " disabled";
				$sel = "";	
			    }
			else
			    {
				$dis = "";
				if ($sel == "")
					{
					$sel = " selected";
					$val = $key;
					switch($key):
						case('EUR'): $quote = $quotes[5];
						break;
						case('USD'): $quote=1;
						break;
						case('CHF'): $quote = $quotes[3];
						break;
						case('JPY'): $quote = $quotes[2];
						break;
						case('RUR'): $quote = $quotes[1];
						break;
						case('TRY'): $quote =  $quotes[4];
						break;
						case('GBP'): $quote =  $quotes[0];
						break;
					endswitch;
					$initval = $quote * 300;
					}
				else
					$sel = "";	
			    }

			print("<option value='".$key."' ".$dis.$sel.">".$key."(".$value.")</option>");
        	}			
?>					
</select></td><td style="padding-bottom:12px;"><a class="submit" href="#wallets"><span style="color:white;font-weight:bold;">Prepaid</span></a></td></tr>
<tr><td colspan="2">Сумма внесения:<input type="text" name="sumb" id="sumb" value="300" size="1" onChange="conv(<?php echo $quotes[0]?>,<?php echo $quotes[1]?>,<?php echo $quotes[2]?>,<?php echo $quotes[3]?>,<?php echo $quotes[4]?>,<?php echo $quotes[5]?>);">USD
<BR>
</td></tr>
<tr><td colspan="2">Курс конвертации:<input type="text" name="course" id="course" value="<?php echo $quote?>" size="3">
<BR>
</td></tr>
<tr><td colspan="2">Сумма снятия:<input type="text" style="text-align:right;" name="sumw" id="sumw" value="<?php echo $initval?>" size="8" readonly><label id="currency"><?php echo $val?></label>
<BR>
</td></tr>
</table>	
<BR>
<table cellspadding="5"><tr><td>
<a class="submit" href="/index.php"><span style="color:white;font-weight:bold;">Cancel</span></a></td><td><a class="submit" href="#" onClick="javascript:form6.submit();"><span style="color:white;font-weight:bold;">Submit</span></a>
</td></tr></table>
</form>