<?php
AutOnly();
SetTitle('Обработка платежа');
GetHeader();

echo Error('Ошибка транзакции.<br />Покупка не совершена.');
?>
<a href="/?r=info/contacts" title="Контакты">Сообщить об ошибке</a>
<?php
GetFooter():