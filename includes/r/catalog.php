<?php
SetTitle('Новые домены');
SetDescription('Последние зарегистрированные домены');
GetHeader();

$c = mysql_num_rows(mysql_query("SELECT * FROM `subdomains` WHERE `hidden` = 0"));
if($c == 0)
	{
		echo Error('Пока что нет доменов');
	}
else
	{
		echo '<h3>Каталог созданных доменов</h3>';
		echo '<div class="list-group">';
		$num = ceil($c / $_INFO['onpage']);
		$page = CheckPage($num);
		$q = mysql_query("SELECT subdomains.id_domain, subdomains.name, domains.domain FROM subdomains, domains WHERE domains.id = subdomains.id_domain AND subdomains.hidden = 0 ORDER BY subdomains.id DESC LIMIT ".MysqlLimit($page, $_INFO['onpage']));
		while($site = mysql_fetch_assoc($q))
			{
				echo '<a href="http://'.$site['name'].'.'.$site['domain'].'" target="_blank" class="list-group-item">'.$site['name'].'.'.$site['domain'].'</a>'.PHP_EOL;
			}
		echo '</div>';
		if($num > 1)
			{
				PageNavi("?", $num, $page);
			}
	}

GetFooter();