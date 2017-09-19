<?php
AdminOnly();
$id = (int)$_GET['id'];
$q = mysql_query("SELECT * FROM `domains` WHERE `id`=".$id);
if(mysql_num_rows($q) != 1)
	{
		FatalError(GetErrorText('404'));
	}
$domain = mysql_fetch_array($q);

SetTitle('Все поддомены в зоне *.'.$domain['domain']);
GetHeader();

$c = mysql_num_rows(mysql_query("SELECT * FROM `subdomains` WHERE `id_domain`=".$id));
if($c == 0)
	{
		echo Error('Пока что не поддоменов');
	}
else
	{
		$num = ceil($c / $_INFO['onpage']);
		$page = CheckPage($num);
		$q = mysql_query("SELECT * FROM `subdomains` WHERE `id_domain` = ".$id." ORDER BY `time_created` DESC LIMIT ".MysqlLimit($page, $_INFO['onpage']));
		while($sub = mysql_fetch_array($q))
			{
				echo '<a href="http://'.$sub['name'].'.'.$domain['domain'].'" target="_blank" class="medium">'.$sub['name'].'.'.$domain['domain'].'</a><br />
				<a href="/?r=aut/edit_domain&id='.$sub['id'].'" title="Исправить записи"><div class="edit">Изм</div></a><a href="/?r=aut/delete_domain&id='.$sub['id'].'" title="Удалить домен"><div class="delete">Удал</div></a><br />
				Создан: '.ShowTime($sub['time_created']).', обновлен '.ShowTime($sub['time_updated']).'<br /><br />';
				echo "\n";
			}
		$num = 10000;
		$page = 7000;
		if($num > 1)
			{
				PageNavi('?r=admin/view_domains&id='.$id, $num, $page);
			}
	}

GetFooter();