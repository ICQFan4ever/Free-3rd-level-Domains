<?php
SetTitle('Статистика сервиса');
GetHeader();

$c_users = mysql_num_rows(mysql_query("SELECT * FROM `users`"));
$c_new_users = mysql_num_rows(mysql_query("SELECT * FROM `users` WHERE `time_reg` + 86400 > ".time()));

$c_zones = mysql_num_rows(mysql_query("SELECT * FROM `domains`"));
$c_domains = mysql_num_rows(mysql_query("SELECT * FROM `subdomains`"));
$c_new_domains = mysql_num_rows(mysql_query("SELECT * FROM `subdomains` WHERE `time_created` + 86400 > ".time()));
?>
<span class="muted">Всего пользователей: <b><?=$c_users?></b></span><br />
<span class="text-success">Новых пользователей за сутки: <b>+<?=$c_new_users?></b></span><br />
<span class="text-warning">Всего доменных зон: <b><?=$c_zones?></b></span><br />
<span class="muted">Всего доменов создано: <b><?=$c_domains?></b></span><br />
<span class="text-success">Новых доменов за сутки: <b>+<?=$c_new_domains?></b></span>

<?php
GetFooter();
?>