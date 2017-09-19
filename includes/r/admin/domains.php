<?php
AdminOnly();
SetTitle('Все домены');
GetHeader();

$q = mysql_query("SELECT * FROM `domains` ORDER BY `domain` ASC");
while($domain = mysql_fetch_array($q))
	{
		echo '<a href="/?r=admin/view_domains&id='.$domain['id'].'" title="Все домены" class="medium">'.$domain['domain'].'</a> ('.$domain['counter'].')<br />
		<a href="/?r=admin/edit_domain&id='.$domain['id'].'" title="Редактировать"><span class="label label-success">Изменить</span></a> <a href="/?r=admin/delete_domain&id='.$domain['id'].'" title="Удалить"><span class="label label-important">Удалить</span></a><br />';
		echo "\n\n";
	}

GetFooter();
?>