<?php
SetTitle('Новости');
SetDescription('Последние новости ресурса');
GetHeader();

# добавить навигацию
$q = mysql_query("SELECT * FROM `news` ORDER BY `id` DESC"); 

$c = mysql_num_rows($q);

if($c > 0)
	{
		
		while($news = mysql_fetch_assoc($q))
			{
				?>
				<div class="well">
				<b><?=date('d.m.Y', $news['time'])?></b><br />
				<?=$news['text']?>
				</div>
				<?php
			}
	}
else
	{
		echo error('Новостей нет');
	}

getFooter();