<?php
AutOnly();
SetTitle('Подтверждение E-mail');
GetHeader();
if($_INFO['email_verified'] != 1)
	{
		if(isset($_GET['v']))
			{
				$v = mysql_real_escape_string(trim($_GET['v']));
				$q = mysql_query("SELECT * FROM `users` WHERE `password` = '".$v."' AND `id`=".$_INFO['id']);
				if(mysql_num_rows($q) == 1)
					{
						if(mysql_query("UPDATE `users` SET `email_verified` = '1' WHERE `id`=".$_INFO['id']) && mysql_query("UPDATE `users` SET `max_domains` = `max_domains` + 2 WHERE `id`=".$_INFO['id']))
							{
								Redirect('/aut');
							}
						else
							{
								FatalError(mysql_error());
							}
					}
				else
					{
						FatalError('Вы вообще кто?');
					}
			}
		else
			{
				if(isset($_GET['action']))
					{
						if($_GET['action'] == 'resend')
							{
								if(!isset($_SESSION['cooldown']))
									{
										$_SESSION['cooldown'] = 0;
									}
								if($_SESSION['cooldown'] + 60 < time())
									{
										if(mail($_INFO['email'], 'Код подтверждения', 'Для подтверждения E-mail перейдите по ссылке:<br /><a href="https://4nmv.ru/?r=aut/validate&v='.$_INFO['password'].'" target="_blank">https://4nmv.ru/?r=aut/validate&v='.$_INFO['password'].'</a>. Также Вы можете подтвердить E-mail, перейдя в соответствующий раздел в личном кабинете и вставив туда код <b>'.$_INFO['password'].'</b>', "From: 4nmv <system@4nmv.ru>\e\nContent-type: text/html; charset=utf-8"))
											{
												echo '<div class="alert alert-info">Код подтверждения отправлен</div>';
												$_SESSION['cooldown'] = time();
											}
										else
											{
												echo Error('А у нас почта поломалась :( Обратитесь к администрации, пожалуйста.');
											}
									}
								else
									{
										echo Error('Нельзя так часто высылать письма. Пожалуйста, не делайте больше одного запроса в минуту');
									}
							}
					}
				?>
				<form action="/" method="get">
				<input type="hidden" name="r" value="aut/validate" />
				Введите код подтверждения из письма: (<a href="/validate/resend" title="Отправить код подтверждения" style="color: #777;">нажмите здесь, чтобы выслать повторно</a>)<br />
				<input type="text" name="v" value="" /><br />
				<input type="submit" value="Подтвердить" />
				</form>
				<?php
			}
	}
else
	{
		'<div class="alert alert-info">Ваш E-mail уже подтвержден</div>';
	}

GetFooter();