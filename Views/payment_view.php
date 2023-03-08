<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Оплата заказа</title>
</head>
<body>
<h1>Оплата заказа</h1>
<form method="POST" action="<?php echo $action_url; ?>">
    <input type="hidden" name="amount" value="<?php echo $amount; ?>">
    <input type="hidden" name="currency" value="<?php echo $currency; ?>">
    <input type="hidden" name="description" value="<?php echo $description; ?>">
    <input type="submit" value="Оплатить">
</form>
</body>
</html>
