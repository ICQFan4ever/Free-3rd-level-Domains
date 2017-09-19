<?php

if(@$_GET['key'] == 'ILoveLiza')
	{
		$error = array();
		
		if(!empty($_GET['url']))
			{
				$url = mysql_real_escape_string($_GET['url']);
			}
		else
			{
				$error[] = 'Введите оригинальный URL';
			}
		
		if(empty($error))
			{
				if(mysql_query("INSERT INTO `shortener`(`url`, `time`) VALUES ('".$url."', ".time().")"))
					{
						$_id = mysql_insert_id();
						$rnd = substr(md5(time().$_id), 0, 4);
						if(mysql_query("UPDATE `shortener` SET `rand` = '".$rnd."' WHERE `id` = ".$_id))
							{
								$link = 'https://conf.work/'.$rnd.'_'.$_id.'.html';
								echo $link;
							}
						else
							{
								$link = mysql_error();
								echo $link;
							}
					}
				else
					{
						$link = mysql_error();
						echo $link;
					}
			}
	}
