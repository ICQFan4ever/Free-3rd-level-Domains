<?php
$key = 'Spaces';
if(AUT)
	{
		Redirect("/?r=aut/index");
	}

if(isset($_GET['id']))
	{
		$id = (int)$_GET['id'];
		$q = mysql_query("SELECT * FROM `users` WHERE `id`=".$id);
		if(mysql_num_rows($q) == 0)
			{
				$ref['id'] = 0;
				$ref['login'] = '';
			}
		else
			{
				$_tmp = mysql_fetch_array($q);
				$ref['id'] = $_tmp['id'];
				$ref['login'] = $_tmp['login'];
			}
	}
else
	{
		$ref['id'] = 0;
		$ref['login'] = '';
	}

if(isset($_POST['button']))
	{
		$error = array();
		################
		##################
		
		if(isset($_POST['login']))
			{
				if(preg_match("#^([a-zA-Z0-9]{4,30})$#", $_POST['login']))
					{
						$login = $_POST['login'];
						$q = mysql_query("SELECT * FROM `users` WHERE `login`='".$login."' LIMIT 1");
						if(mysql_num_rows($q) != 0)
							{
								$error[] = 'Логин <b>'.$login.'</b> уже занят. Выберите, пожалуйста, другой';
							}
					}
				else
					{
						$error[] = 'Некорректный формат логина. Используйте только латинские буквы и цифры (от 4 до 30 символов)';
					}
			}
		else
			{
				$error[] = 'Выберите логин для входа';
			}

		if(isset($_POST['pass']))
			{
				if(Len($_POST['pass']) < 6 | Len($_POST['pass']) > 30)
					{
						$error[] = 'Выберите пароль от 6 до 30 символов длиной';
					}
				else
					{
						if(preg_match("#^([0-9]{1,})$#", $_POST['pass']))
							{
								$error[] = 'Пароль не должен включать в себя только цифры';
							}
						else
							{
								$password = strrev(md5($_POST['pass']).md5(md5($_POST['pass'])));
							}
					}
			}
		else
			{
				$error[] = 'Выберите пароль';
			}
		
		if(isset($_POST['email']))
			{
				if(IsMail($_POST['email']))
					{
						$email = $_POST['email'];
						$q = mysql_query("SELECT * FROM `users` WHERE `email`='".$email."'");
						if(mysql_num_rows($q) != 0)
							{
								$error[] = 'Этот адрес E-mail уже используется';
							}
					}
				else
					{
						$error[] = 'Некорректный формат E-mail';
					}
			}
		else
			{
				$error[] = 'Введите E-mail адрес';
			}
		
		if(isset($_POST['code']))
			{
				if($_POST['code'] != $_SESSION['code'])
					{
						$error[] = 'Неверный код с картинки';
					}
			}
		else
			{
				$error[] = 'Введите проверочный код с картинки';
			}
		
		if(empty($error) && !empty($password))
			{
				$sid = GenMD5();
				if(mysql_query("INSERT INTO `users`(`id_referer`, `login`, `password`, `email`, `sid`, `vip`, `max_domains`, `level`, `time_reg`, `last_visit`, `ip`, `ua`, `onpage`) VALUES (".$ref['id'].", '".$login."', '".$password."', '".$email."', '".$sid."', 0, 1, 1, ".time().", ".time().", ".LONGIP.", '".UA."', 10)"))
					{
						setcookie('sid', $sid, time() + 86400 * 7, '/', $_SERVER['HTTP_HOST']);
						$headers = "From: SystemBot <system@4nmv.ru>\r\n";
						$headers .= "Subject: Регистрационные данные\r\n";
						$headers .= "Content-type: text/plain; charset=utf-8";
						$subject = 'Регистрационные данные';
						$body = "Благодарим Вас за регистрацию на нашем сервисе!\r\nДанные для входа:\r\nЛогин: ".$login."\r\nПароль: ".$_POST['pass']."\r\nДля подтверждения E-mail перейдите по ссылке: https://4nmv.ru/validate/".$password."\r\n\r\nПожалуйста, не отвечайте на это письмо. Оно было отправлено автоматически\r\nС уважением, Администрация проекта ".$_SERVER['HTTP_HOST'];
						if(mail($email, $subject, $body, $headers))
							{
								OutputExit('Благодарим Вас за регистрацию на нашем сервисе! Логин и пароль отправлены на указанный Вами E-mail<br /><a href="/?r=aut/index" title="Личный кабинет">Войти на сайт</a>');
							}
						else
							{
								FatalError("Ошибка отправки Email-подтверждения");
							}
					}
				else
					{
						FatalError(GetErrorText('mysql'));
					}
			}
	}

SetTitle('Регистрация');
SetDescription('Зарегистрируйтесь на нашем сервисе для регистрации бесплатных доменов 3-го уровня');
SetKeywords('домены, бесплатно, dns, ns, cname, a, регистрация домена');
RobotAccess(true);
GetHeader();
FormError(isset($error) ? $error : '');
echo '<form action="/?r=aut/reg&id='.$ref['id'].'" method="post">
Логин (4-30 символов на латинице или цифр):<br />
<input type="text" name="login" required="required" maxlength="30" value="'.(isset($_POST['login']) ? htmlspecialchars($_POST['login']) : '').'" autofocus="autofocus" /><br />
Пароль (от 6 до 30 символов, не одни лишь цифры, например <b>'.GenPass(rand(6,8)).'</b>)<br />
<input type="text" name="pass" required="required" maxlength="30" value="'.(isset($_POST['pass']) ? htmlspecialchars($_POST['pass']) : '').'" /><br />
E-mail:<br />
<input type="email" name="email" required="required" maxlength="100" value="'.(isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '').'" /><br />
<img src="/?r=captcha&rand='.rand(11111,99999).'" alt="" /><br />
Число с картинки:<br />
<input type="text" name="code" required="required" /><br />';
if(!empty($ref['login']))
	{
		echo 'Пригласил:<br />
		<input type="text" name="ref" maxlength="20" readonly="readonly" value="'.$ref['login'].'" /><br />';
		echo "\n";
	}
echo "\n";
echo '<input type="submit" name="button" value="Регистрация" /></form>';
GetFooter();