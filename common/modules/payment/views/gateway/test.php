<form action="/client/payment/gateway/start">
	<select name="gatewayName">
		<option value="robokassa">Robokassa</option>
	</select>
    <input type="text" name="id" value="2">
	<input type="text" name="amount" value="10">
	<input type="text" name="description" value="Тестовый платеж">
	<button type="submit">Заплатить</button>
</form>