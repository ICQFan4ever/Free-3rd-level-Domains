<?php

if(isset($_GET['mode']))
	{
		# follow
		if($_GET['mode'] == 'go')
			{
				if(isset($_GET['id']) && isset($_GET['rand']))
					{
						$id = (int)$_GET['id'];
						$rand = mysql_real_escape_string($_GET['rand']);
						$q = mysql_query("SELECT * FROM `shortener` WHERE `id` = ".$id." AND `rand` = '".$rand."' LIMIT 1");
						if(mysql_num_rows($q) != 1)
							{
								Redirect('/?r=shortener');
							}
						else
							{
								$site = mysql_fetch_assoc($q);
								if($_GET['safe'] == 1)
									{
										die($site['url']);
									}
								Redirect($site['url']);
							}
					}
			}
		
		if($_GET['mode'] == 'create')
			{
				SetTitle('Сокращение ссылок');
				SetDescription('Сервис, который позволит Вам сократить длинную некрасивую ссылку');
				GetHeader();
				if(isset($_POST['button']))
					{
						$error = array();
						
						if(!empty($_POST['url']))
							{
								$url = mysql_real_escape_string($_POST['url']);
							}
						else
							{
								$error[] = 'Введите оригинальный URL';
							}
						
						if($_SESSION['code'] != $_POST['code'])
							{
								$error[] = 'Неверное число с картинки';
							}
						
						if(empty($error))
							{
								if(mysql_query("INSERT INTO `shortener`(`url`, `time`) VALUES ('".$url."', ".time().")"))
									{
										$_id = mysql_insert_id();
										$rnd = substr(md5(time().$_id), 0, 4);
										if(mysql_query("UPDATE `shortener` SET `rand` = '".$rnd."' WHERE `id` = ".$_id))
											{
												?>
												<span style="font-size: xx-large;">Короткая ссылка: <a href="https://4nmv.ru/<?=$rnd.'_'.$_id?>.html" style="color: 007700;" target="_blank" title="Go...">https://4nmv.ru/<?=$rnd.'_'.$_id?>.html</a></span><br /><br />
												<?php
											}
										else
											{
												echo error(mysql_error());
											}
									}
								else
									{
										echo mysql_error();
									}
							}
					}
				
				FormError(isset($error) ? $error : null);
				?>
				Не забывайте указывать протокол (<b>http:// https:// ftp:// telnet:// ...</b>)
				<form action="/short" method="post">
				<input type="text" name="url" placeholder="Введите оригинальный URL..." value="http://" required="required" class="form-control" /><br />
				<img src="/?r=captcha&rand=<?=rand(11111,99999)?>" alt="" /><br />
				Число с картинки:<br />
				<input type="text" name="code" required="required" class="form-control" /><br />
				<input type="submit" name="button" value="Получить новый URL" class="btn btn-success" /></form>
				<a href="/Shortener.crx" title="Плагин для Chrome" style="font-weight: bold;">Плагин для Chrome</a><br />
				* после установки справа от адресной строки появится значок &quot;цепи&quot;, нажмите его на нужной странице, и короткая ссылка будет скопирована в буфер обмена.
				<?php
				GetFooter();
				exit;
			}
		exit;
	}

Redirect('/short');