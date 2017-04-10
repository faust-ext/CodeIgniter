<form method="POST" name="form2" id="form2" action="/pamm/dsp/investorout_submit/<?php echo $acc_number?>">
<table><tr><td>
<select name="wallets" id="wallets">
<?php					
$message_not_enough_sum = "На данном кошельке недостаточно средств для активации счета.Выберите другой кошелек или пополните этот.";
	$sel = "";
	foreach ($wallet as $key=>$value)
		{  	
			print("<option value='".$key."'>".$key."</option>");
        	}			
?>					
</select></td><td style="padding-bottom:12px;"></td></tr>
<tr><td><?php echo $threshold?></td></tr>
<tr><td><?php echo $requests?></td><td style="padding-bottom:14px;"><?php echo $button?></td></tr>
<tr><td colspan="2">Сумма вывода со счета:<input type="text" name="sumb" id="sumb" value="1" size="5">USD
<BR>
<input type="hidden" name="sumw" id="sumw" value="1" size="1">
</td></tr>
<tr><td></td></tr>
<tr>
<td>Urgent<input type="checkbox" name="urgent" id="urgent" onChange="urgentpenalty(<?php echo $penalty?>);"></td>
</tr>
<tr><td>Penalty(<?php echo $penalty?>%):<input name="penalty" id="penalty" type="text" value="0" size="1" readonly><input name="penalty_origin" id="penalty_origin" type="hidden" value="0"></td></tr>
<tr><td>Out:<input name="out" id="out" type="text" value="0" size="10" readonly><input name="out_origin" id="out_origin" type="hidden" value="0"></td></tr>
</table>	
<BR>
<table cellspadding="5"><tr><td>
<a class="submit" href="/pamm/dsp/invest/<?php echo $acc_number?>"><span style="color:white;font-weight:bold;">Cancel</span></a></td>
<?php if ($avaliability_withdraw)
{
?>
<td>
<a class="submit" href="#" onClick="javascript:form2.submit();"><span style="color:white;font-weight:bold;">Charge</span></a>
</td>
<?php
}
?>

</tr></table>
</form>