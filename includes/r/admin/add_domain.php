<?php
AdminOnly();

if(isset($_POST['button']))
	{
		$error = array();
		
		if(isset($_POST['domain']))
			{
				if(preg_match("#^([a-zA-Z0-9\-\.]{1,50})$#u", $_POST['domain']) XOR preg_match("#^([0-9а-яА-ЯёЁ\-\.]{1,50})$#u", $_POST['domain']))
					{
						$domain = $_POST['domain'];
						$q = mysql_query("SELECT * FROM `domains` WHERE `domain`='".$domain."' LIMIT 1");
						if(mysql_num_rows($q) != 0)
							{
								$error[] = 'Домен уже существует';
							}
					}
				else
					{
						$error[] = 'Некоректный формат домена';
					}
			}
		else
			{
				$error[] = 'Введите домен';
			}
				
		if(isset($_POST['type']))
			{
				if($_POST['type'] == 'ru')
					{
						$type = 'ru';
					}
				else
					{
						$type = 'en';
					}
			}
		else
			{
				$error[] = 'Выберите тип домена';
			}
		
		if(empty($error))
			{
				if(mysql_query("INSERT INTO `domains`(`domain`, `type`, `time`) VALUES ('".$domain."', '".$type."', ".time().")"))
					{
						$__id = mysql_insert_id();
						OutputExit('Домен успешно добавлен. Перейдите по ссылке: <a href="https://pddimp.yandex.ru/token/index.xml?domain='.$domain.'" target="_blank">https://pddimp.yandex.ru/token/index.xml?domain='.$domain.'</a> для получения API-токена<br /><br />
						Далее перейдите на <a href="/?r=admin/edit_domain&id='.$__id.'">страницу редактирования домена и введите ключ</a>');
					}
				else
					{
						FatalError(GetErrorText('mysql'));
					}
			}
	}

SetTitle('Добавить новый домен');
GetHeader();
FormError(isset($error) ? $error : '');
?>
<form action="/?r=admin/add_domain" method="post">
Домен (без http://):<br />
<input type="text" name="domain" maxlength="50" required="required" class="form-control" style="max-width: 300px;" /><br />
Тип домена:<br />
<input type="radio" name="type" value="en" required="required" /> Обычный<br />
<input type="radio" name="type" value="ru" required="required" /> Кириллический<br />
<input type="submit" name="button" value="Создать" class="btn btn-primary"></form>
<?php
GetFooter();