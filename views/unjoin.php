<form method="POST" name="form5" id="form5" action="/pamm/dsp/unjoin_submit/<?php echo $acc_number?>">
<table><tr><td colpan="2">Вы хотите отсоединиться от PAMM счета №&nbsp;<?php echo $acc_number?>?</td><tr>
<tr><td colspan="2">При выполнении данной операции, остаток ваших средств на  PAMM счете будут выведены на ваш кошелек.</td></tr>
<tr><td colspan="2">Выполнение операции произойдет в роловер.</td></tr>
<tr><td colspan="2">Отменить операцию можно будет удалив заявку на отсоединение от PAMM счета, до момента ее исполнения.</td></tr>
<tr><td colspan="2">Выберите кошелек, на который будет произведен вывод:<input type="hidden" name="input1" value="1"><select name="wallets" id="wallets">
<?php					
	foreach ($wallet as $key=>$value)
		{  	
			print("<option value='".$key."'>".$key."</option>");
        	}			
?>					
</select></td></tr>
<tr><td style="width:100px;"><a class="submit" href="/pamm/dsp/invest"><span style="color:white;font-weight:bold;">Cancel</span></a></td><td><a class="submit" href="#" onClick="javascript:form5.submit();"><span style="color:white;font-weight:bold;">Unjoint</span></a></td></td></tr>
</table>
</form>