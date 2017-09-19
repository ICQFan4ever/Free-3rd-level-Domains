<?php
AutOnly();
SetTitle('Личный кабинет');
GetHeader();
$c_all = mysql_result(mysql_query("SELECT COUNT(*) FROM `subdomains` WHERE `id_user`=".$_INFO['id']), 0);
?>
<ol class="breadcrumb">Доменов создано: <span class="badge"><?=$c_all.'/'.$_INFO['max_domains']?></span></ol>
<div class="list-group">
<?php
if(ADM)
	{
		?>
		<a href="/admin" title="Админ-панель" class="list-group-item">Админ-панель</a>
		<?php
	}
?>

<a href="/add" title="Зарегистрировать новый домен" class="list-group-item">Зарегистрировать домен</a>
<a href="/domains" title="Управление существующими доменами" class="list-group-item">Созданные домены</a>
<a href="/info" title="Личная информация" class="list-group-item">Личная информация</a>
<?=($_INFO['email_verified'] != 1) ? '<a href="/?r=aut/validate" title="Подтвердить E-mail" class="list-group-item">Подтвердить E-mail</a>' : ''?>
<a href="/help" title="Помощь" class="list-group-item">Помощь</a>
<a href="/promo" title="Активировать промо-код" class="list-group-item">Активировать промо-код</a>
<a href="/logout" title="Выйти" class="list-group-item">Выйти с этого компьютера</a>
<a href="/logout/all" title="Выйти" class="list-group-item">Завершить все сессии</a>
</div>
<?php
GetFooter();