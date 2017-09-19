<?php
AdminOnly();
SetTitle('Все пользователи');
GetHeader();

$c = mysql_num_rows(mysql_query("SELECT * FROM `users`"));

if($c == 0)
	{
		echo Error('Нет пользователей? O_o Куда админ блядь проебался?');
	}
else
	{
		$num = ceil($c / $_INFO['onpage']);
		$page = CheckPage($num);
		
		$q = mysql_query("SELECT * FROM `users` ORDER BY `time_reg` DESC LIMIT ".MysqlLimit($page, $_INFO['onpage']));
		while($user = mysql_fetch_assoc($q))
			{
				$num2 = mysql_num_rows(mysql_query("SELECT * FROM `subdomains` WHERE `id_user` = ".$user['id']));
				echo '<div class="user"><a href="/?r=admin/view_user&id='.$user['id'].'" class="big">'.$user['login'].'</a><br />
				Дата регистрации: '.ShowTime($user['time_reg']).', последний вход: '.ShowTime($user['last_visit']).'<br />
				IP: <b>'.long2ip($user['ip']).'</b> (<a href="/?r=admin/search_related_ip&ip='.$user['ip'].'" class="focus">найти похожие</a>), UA: <b>'.$user['ua'].'</b><br />
				Зарегистрировано доменов: <b>'.$num2.'</b> из <b>'.$user['max_domains'].'</b> (<a href="/?r=admin/view_user_domains&id='.$user['id'].'" class="focus">просмотреть список</a>)<br />
				<a href="/?r=admin/edit_user&id='.$user['id'].'"><div class="edit">Редактировать профиль</div></a> <a href="/?r=admin/delete_user&id='.$user['id'].'"><div class="delete">Удалить пользователя</div></a>
				</div>';
				echo "\n\n";
			}
		
		if($num > 1)
			{
				PageNavi('?r=admin/users', $num, $page);
			}
	}

GetFooter();