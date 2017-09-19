<?php
AdminOnly();

$ip = (int)$_GET['ip'];

SetTitle('Поиск похожих IP: '.long2ip($ip));
GetHeader();

$q = mysql_query("SELECT * FROM `users` WHERE `ip` - ".$ip." < 86400 OR ".$ip." - `ip` > 86400 OR `ip` = ".$ip);

while($user = mysql_fetch_assoc($q))
	{
		$num = mysql_num_rows(mysql_query("SELECT * FROM `subdomains` WHERE `id_user` = ".$user['id']));
		echo '<div class="user"><a href="/?r=admin/view_user&id='.$user['id'].'">'.$user['login'].'</a><br />
		Дата регистрации: '.ShowTime($user['time_reg']).', последний вход: '.ShowTime($user['last_visit']).'<br />
		IP: <b>'.long2ip($user['ip']).'</b> (<a href="/?r=admin/search_related_ip?ip='.$user['ip'].'">найти похожие</a>), UA: '.$user['ua'].'<br />
		Зарегистрировано доменов: <b>'.$num.'</b> из <b>'.$user['max_domains'].'</b> (<a href="/?r=admin/view_user_domains&id='.$user['id'].'">просмотреть список</a>)<br />
		<a href="/?r=admin/edit_user&id='.$user['id'].'" class="edit">[редактировать профиль]</a> / <a href="/?r=admin/delete_user&id='.$user['id'].'" class="delete">[удалить пользователя]</a>
		</div>';
	}
GetFooter();