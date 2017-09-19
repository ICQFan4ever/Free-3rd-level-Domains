<?php
AdminOnly();
$id =(int)$_GET['id'];

$q = mysql_query("SELECT * FROM `users` WHERE `id`=".$id);
if(mysql_num_rows($q) != 1)
	{
		FatalError(GetErrorText('404'));
		
	}
else
	{
		
		$user = mysql_fetch_assoc($q);
		SetTitle('Домены пользователя '.$user['login']);
		GetHeader();
		$q2 = mysql_query("SELECT subdomains.*, domains.domain, domains.id FROM subdomains, domains WHERE domains.id = subdomains.id_domain AND subdomains.id_user = ".$id);
		if(mysql_num_rows($q2) < 1)
			{
				echo Error('Пока что нет доменов');
			}
		else
			{
				while($dom = mysql_fetch_array($q2))
					{
						$_ID = $dom[0];
						$_TYPE = $dom[3];
						echo '<a href="http://'.$dom['name'].'.'.$dom['domain'].'" target="_blank">'.$dom['name'].'.'.$dom['domain'].'</a><br />
						Дата регистрации: '.ShowTime($dom['time_created']).', дата последнего обновления: '.ShowTime($dom['time_updated']).'<br />
						Тип делегирования: <b>'.(empty($_TYPE) ? 'не делегирован' : strtoupper($_TYPE)).'</b><br />';
						switch($_TYPE)
							{
								case 'a': echo 'IP-адрес: <b>'.$dom['a'].'</b>'; break;
								case 'cname': echo 'Адрес: <b>'.$dom['cname'].'</b>'; break;
								case 'ns': echo 'Первичный DNS: <b>'.$dom['ns1'].'<br />Вторичный DNS: <b>'.$dom['ns2'].'</b>'; break;
								default: echo '';
							}
						echo '
						<a href="/?r=aut/edit_domain&id='.$_ID.'"><div class="edit">Редактировать</div></a> <a href="/?r=aut/delete_domain&id='.$_ID.'"><div class="delete">Удалить</div></a><br /><br />';
						echo "\n\n";
					}
			}
		GetFooter();
	}