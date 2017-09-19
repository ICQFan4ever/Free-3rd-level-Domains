<?php
AutOnly();

if(isset($_POST['button']))
	{
		$error = array();
		
		if(isset($_POST['name']))
			{
				$name = FilterText($_POST['name'], 100);
			}
		else
			{
				$name = '';
			}
		
		if(!empty($_POST['email']))
			{
				if(IsMail($_POST['email']))
					{
						$email = $_POST['email'];
						if($email != $_INFO['email'])
							{
								$q = mysql_query("SELECT * FROM `users` WHERE `email` = '".$email."'");
								if(mysql_num_rows($q) != 0)
									{
										$error[] = 'Этот E-mail уже используется другим пользователем';
									}
							}
						else
							{
								$error[] = 'Введите новый E-mail или оставьте поле пустым, чтобы сохранить старый';
							}
					}
				else
					{
						$error[] = 'Некорректный формат E-mail';
					}
			}
		else
			{
				$email = $_INFO['email'];
			}
		
		if(!empty($_POST['password']))
			{
				$password = strrev(md5($_POST['password']).md5(md5($_POST['password'])));
				if($password == $_INFO['password'])
					{
						if(isset($_POST['pass1']))
							{
								if(Len($_POST['pass1']) < 6 | Len($_POST['pass1']) > 30)
									{
										$error[] = 'Выберите пароль от 6 до 30 символов длиной';
									}
								else
									{
										if(preg_match("#^([0-9]{1,})$#", $_POST['pass1']))
											{
												$error[] = 'Пароль не должен включать в себя только цифры';
											}
										else
											{
												$new_pass = strrev(md5($_POST['pass1']).md5(md5($_POST['pass1'])));
											}
									}
							}
						else
							{
								$error[] = 'Выберите пароль';
							}
					}
				else
					{
						$error[] = 'Старый пароль введен неверно';
					}
			}
		else
			{
				$new_pass = $_INFO['password'];
			}
		
		if(empty($error))
			{
				if(mysql_query("UPDATE `users` SET `name` = '".$name."', `email` = '".$email."', `password` = '".$new_pass."' WHERE `id`=".$_INFO['id']))
					{
						Redirect('/?r=aut/info&m=1');
					}
				else
					{
						FatalError(GetErrorText('mysql'));
					}
			}
	}

SetTitle('Личные данные');
GetHeader();
FormError(isset($error) ? $error : '');
if(isset($_GET['m']))
	{
		if($_GET['m'] == 1)
			{
				echo Good('Данные сохранены');
			}
	}
?>

<form action="/?r=aut/info" method="post">
Имя (в произвольной форме):<br />
<input type="text" name="name" maxlength="100" class="form-control" style="max-width: 300px;" value="<?= $_INFO['name'] ?>" /><br />
E-mail (оставьте пустым, если хотите оставить прежний):<br />
<input type="email" name="email" maxlength="100" class="form-control" style="max-width: 300px;" value="<? (isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '')?>" /><br /><br />
Изменение пароля (не трогайте, если не хотите менять):<br />
Старый пароль:<br />
<input type="password" class="form-control" style="max-width: 300px;" name="password" /><br />
Новый пароль:<br />
<input type="text" class="form-control" style="max-width: 300px;" name="pass1" /><br />
<input type="submit" name="button" value="Сохранить" class="btn btn-default"  /></form>

<?php
GetFooter();