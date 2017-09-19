<?php
AutOnly();
SetTitle('Мои домены');
GetHeader();

$q = mysql_query("SELECT * FROM `subdomains` WHERE `id_user`=".$_INFO['id']." ORDER BY `time_created` DESC");
if(mysql_num_rows($q) == 0)
	{
		echo Error('Пока что нет созданных доменов');
	}
else
	{
		echo (isset($_GET['page']) ? $_GET['page'] : '');
		while($sub = mysql_fetch_array($q))
			{
				$dom = mysql_fetch_array(mysql_query("SELECT * FROM `domains` WHERE `id`=".$sub['id_domain']." LIMIT 1"));
				?>
				<a href="http://<?= $sub['name'].'.'.$dom['domain']?>" target="_blank"><?= $sub['name'].'.'.$dom['domain']?></a><br />
				Создан: <span class="label label-default"><?=ShowTime($sub['time_created'])?></span>, обновлен: <span class="label label-default"><?=ShowTime($sub['time_updated'])?></span><br />
				<a href="/?r=aut/edit_domain&id=<?= $sub['id']?>" title="Редактировать домен"><span class="label label-primary">Изменить</span></a> <a href="/?r=aut/delete_domain&id=<?= $sub['id']?>" title="Удалить домен"><span class="label label-danger">Удалить</span></a><br /><br />
				<?
			}
	}
GetFooter();