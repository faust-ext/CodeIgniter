<form method="POST" name="form5" id="form5" action="/pamm/dsp/close_submit/<?php echo $acc_number?>">
<table><tr><td colpan="2">Вы хотите закрыть PAMM счет №&nbsp;<?php echo $acc_number?>?</td><tr>
<tr><td colspan="2">При выполнении данной операции, остаток Ваших средств на  PAMM счете будут выведены на указанный Вами кошелек.</td></tr>
<tr><td colspan="2">При выполнении данной операции, остатки средств инвесторов на PAMM счете будут выведены на их кошельки.</td></tr>	
<tr><td colspan="2">Выполнение операции произойдет в роловер.</td></tr>
<tr><td colspan="2">Отменить операцию можно будет удалив заявку на закрытие PAMM счета, до момента ее исполнения.</td></tr>
<tr><td colspan="2">Выберите кошелек, на который будет произведен вывод Ваших средств:<input type="hidden" name="input1" value="1"><select name="wallets" id="wallets">
<?php					
	foreach ($wallet as $key=>$value)
		{  	
			print("<option value='".$key."'>".$key."</option>");
        	}			
?>					
</select></td></tr>
<tr><td style="width:100px;"><a class="submit" href="/pamm/dsp/manage"><span style="color:white;font-weight:bold;">Cancel</span></a></td><td><a class="submit" href="#" onClick="javascript:form5.submit();"><span style="color:white;font-weight:bold;">Close</span></a></td></td></tr>
</table>
</form>