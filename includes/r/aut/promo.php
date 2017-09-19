<?php
SetTitle('Активировать промо-код');
GetHeader();

if(isset($_POST['button']))
	{
		if(isset($_POST['code']))
			{
				if($_POST['code'] == $_SESSION['code'])
					{
						if(isset($_POST['promo']))
							{
								$promo = mysql_real_escape_string(trim($_POST['promo']));
								$q = mysql_query("SELECT * FROM `promo_codes` WHERE `code` = '".$promo."' AND `time_valid` > ".time());
								if(mysql_num_rows($q) == 1)
									{
										$code = mysql_fetch_assoc($q);
										$q2 = mysql_query("SELECT * FROM `activated_promo_codes` WHERE `id_user` = ".$_INFO['id']." AND `id_code` = ".$code['id']);
										if(mysql_num_rows($q2) == 0)
											{
												if(mysql_query($code['command']." WHERE `id` = ".$_INFO['id']) && mysql_query("INSERT INTO `activated_promo_codes`(`id_user`, `id_code`, `time`) VALUES (".$_INFO['id'].", ".$code['id'].", ".time().")"))
													{
														echo Good('Промо-код &quot;<b>'.$code['title'].'</b>&quot; активирован');
													}
												else
													{
														echo Error('Системная ошибка! Обратитесь к администратору.');
													}
											}
										else
											{
												$_info = mysql_fetch_assoc($q2);
												echo Error('Код уже был активирован Вами '.ShowTime($_info['time']).'.');
											}
									}
								else
									{
										echo Error('Введен неверный код, или срок его действия истек.');
									}
							}
						else
							{
								echo Error('Введите промо-код.');
							}
					}
				else
					{
						echo Error('Неверный код с картинки');
					}
			}
		else
			{
				echo Error('Введите код с картинки');
			}
	}
?>

<form action="/promo" method="post">
Промо-код:<br />
<input type="text" name="promo" required="required" class="form-control" /><br />
<img src="/captcha" alt="" /><br />
Число с картинки:<br />
<input type="text" name="code" /><br />
<input type="submit" name="button" class="btn btn-primary" value="Активировать" />
</form>

<?php
GetFooter();