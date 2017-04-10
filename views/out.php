<form method="POST" name="form2" id="form2" action="/pamm/dsp/out_submit/<?php echo $acc_number?>">
<table><tr><td>
<select name="wallets" id="wallets">
<?php					
$message_not_enough_sum = "На данном кошельке недостаточно средств для активации счета.Выберите другой кошелек или пополните этот.";
	foreach ($wallet as $key=>$value)
		{  	
			print("<option value='".$key."'>".$key."</option>");
        	}			
?>					
</select></td><td style="padding-bottom:12px;"></td></tr>
<tr><td><php echo $threshold?>&nbsp;USD</td></tr>
<tr><td><?php echo $requests?></td><td style="padding-bottom:14px;"><?php echo $button?></td></tr>
<tr><td colspan="2">Сумма вывода со счета:<input type="text" name="sumb" id="sumb" value="1" size="1">USD
<BR>
</td></tr>
<tr>
<td>Urgent<input type="checkbox" name="urgent"></td>
</tr>
</table>	
<BR>
<table cellspadding="5"><tr><td>
<a class="submit" href="/pamm/dsp/inout/<?php echo $acc_number?>"><span style="color:white;font-weight:bold;">Cancel</span></a></td>
<?php if ($avaliability_withdraw)
{
?>
<td>
<a class="submit" href="#" onClick="javascript:form2.submit()"><span style="color:white;font-weight:bold;">Charge</span></a>
</td>
<?php
}
?>
</tr></table>
</form>