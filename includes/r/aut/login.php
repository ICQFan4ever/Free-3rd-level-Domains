<?php
if(AUT)
	{
		Redirect("/aut");
	}

$d = str_replace(']', '', str_replace('[', '', (isset($_GET['d']) ? $_GET['d'] : '')));

if(isset($_POST['button']))
	{
		$error = array();
		
		if(isset($_POST['login']))
			{
				$login = FilterText($_POST['login'], 20);
			}
		else
			{
				$error[] = 'Введите логин';
			}
		
		if(isset($_POST['password']))
			{
				$password = strrev(md5($_POST['password']).md5(md5($_POST['password'])));
			}
		else
			{
				$error[] = 'Введите пароль';
			}
		
		if(empty($error))
			{
				$q = mysql_query("SELECT * FROM `users` WHERE `login`='".$login."' AND `password`='".$password."' LIMIT 1");
				if(mysql_num_rows($q) != 1)
					{
						$error[] = 'Неверный логин или пароль';
					}
				else
					{
						$_INFO = mysql_fetch_array($q);
						if(empty($_INFO['sid']))
							{
								$sid = GenMD5();
							}
						else
							{
								$sid = $_INFO['sid'];
							}
						setcookie('sid', '', time() - 86400, '/', $_SERVER['HTTP_HOST']);
						setcookie('sid', $sid, time() + 86400 * 7, '/', $_SERVER['HTTP_HOST']);
						mysql_query("UPDATE `users` SET `sid`='".$sid."' WHERE `id`=".$_INFO['id']);
						if(isset($_GET['d']))
							{
								if(substr($d, 0, 1) != '.')
									{
										if(file_exists('includes/r/'.$d.'.php'))
											{
												Redirect("/?r=".$d);
											}
									}
							}
						Redirect("/aut");
					}
			}
	}

SetTitle('Вход');
SetDescription('Войдите в аккаунт для управления доменами');
GetHeader();
FormError(isset($error) ? $error : '');

?>
<form action="/?r=aut/login&d=<?=$d?>" method="post">
Логин:<br />
<input type="text" name="login" maxlength="20" class="form-control" style="max-width: 300px;" value="<?=(isset($_POST['login']) ? htmlspecialchars($_POST['login']) : '')?>" required="required" /><br />
Пароль:<br />
<input type="password" name="password" class="form-control" required="required" /><br />
<input type="submit" name="button" value="Вход" class="btn btn-default" /></form><br />
<div class="list-group">
<a href="/contact" title="Восстановление пароля" class="list-group-item">Восстановить пароль</a>
<a href="/rules" title="Регистрация" class="list-group-item">Регистрация</a>
</div>
<?php
GetFooter();