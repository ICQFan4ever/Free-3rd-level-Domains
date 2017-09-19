<?php
AutOnly();
$c_all = mysql_result(mysql_query("SELECT COUNT(*) FROM `subdomains` WHERE `id_user`=".$_INFO['id']), 0);

if($_INFO['max_domains'] <= $c_all)
	{
		FatalError('Вы зарегистрировали максимально возможное количество доменов');
	}

if(isset($_POST['button']))
	{
		$error = array();
		
		if(isset($_POST['domain']))
			{
				$domain = mysql_real_escape_string($_POST['domain']);
				$q = mysql_query("SELECT * FROM `domains` WHERE `domain`='".$domain."'");
				if(mysql_num_rows($q) != 1)
					{
						$error[] = 'Pwn3d';
					}
				else
					{
						$info = mysql_fetch_array($q);
						if(isset($_POST['sub']))
							{
								$sub = mysql_real_escape_string($_POST['sub']);
								$q = mysql_query("SELECT * FROM `subdomains` WHERE `name`='".$sub."' AND `id_domain`=".$info['id']);
								if(mysql_num_rows($q) != 0)
									{
										$error[] = 'Извините, домен <b>'.$sub.'.'.$domain.'</b> уже занят';
									}
								else
									{
										$stop = array
											(
												'wap' => '1',
												'mail' => '1',
												'www' => '1',
												'pda' => '1',
												'm' => '1',
												'info' => '1',
												'mobile' => '1',
												'pda' => '1',
												'test' => '1',
												'example' => '1',
												'help' => '1',
												'faq' => '1',
												'ru' => '1',
												'server' => '1'
											);
										if(isset($stop[$sub]))
											{
												$error[] = 'Домен запрещен для регистрации';
											}
										else
											{
												# Премиум
												$premium = array
													(
														'xxx' => '1',
														'pro' => '1',
														'icq' => '1',
														'jabber' => '1',
														'porn' => '1',
														'radio' => '1',
														'tv' => '1',
														'777' => '1',
														'666' => '1',
														'000' => '1',
														'http' => '1',
														'vk' => '1',
														'bot' => '1',
														'sex' => '1',
														'video' => '1',
														'audio' => '1',
														'music' => '1',
														'books' => '1',
														'book' => '1',
														'mp3' => '1',
														'spaces' => '1',
														'2ch' => '1',
														'google' => '1',
														'yandex' => '1',
														'search' => '1',
														'money' => '1',
														'hosting' => '1',
														'112' => '1',
														'android' => '1',
														'player' => '1',
														'ios' => '1',
														'java' => '1'
													);
												
												if(isset($premium[$sub]) && $_INFO['vip'] != 1)
													{
														$error[] = 'Этот домен зарезервирован для наиболее интересных проектов. Свяжитесь с нами, если Вы готовы предложить интересную идею для связанного проекта.';
													}
												else
													{
														if($info['type'] == 'ru')
															{
																$pattern = '#^([а-яА-ЯёЁ\-0-9]{1,50})$#u';
															}
														else
															{
																$pattern = '#^([a-zA-Z0-9\-]{1,50})$#';
															}
														if(preg_match($pattern, $sub))
															{
																if(Cut($sub, 0, 1) == '-' || Cut($sub, -1, 1) == '-')
																	{
																		$error[] = 'Домен не может начинаться или заканчиваться с дефиса';
																	}
																else
																	{
																		if(Len($sub) < 2 && $_INFO['vip'] != 1)
																			{
																				$error[] = 'Пока что нельзя регистрировать односимвольные домены. Эта возможность появится позже.';
																			}
																	}
															}
														else
															{
																$error[] = 'Некорректный формат домена 3 уровня. Для кириллических доменов невозможна регистрация поддоменов на латинице; для обычных доменов невозможно использование русских букв';
															}
													}
											}
									}
							}
						else
							{
								$error[] = 'Выберите субдомен';
							}
					}
			}
		else
			{
				$error[] = 'Выберите основной домен';
			}
		
		if(isset($_POST['hidden']))
			{
				switch($_POST['hidden'])
					{
						case '1': $hidden = 1; break;
						case '2': $hidden = 0; break;
						default: $hidden = 0; break;
					}
			}
		else
			{
				$error[] = 'Выберите видимость домена';
			}

		if(empty($error))
			{
				$query1 = mysql_query("INSERT INTO `subdomains`(`id_user`, `id_domain`, `name`, `time_created`, `time_updated`, `hidden`) VALUES (".$_INFO['id'].", ".$info['id'].", '".$sub."', ".time().", ".time().", ".$hidden.")");
				$query2 = mysql_query("UPDATE `domains` SET `counter` = `counter` + 1 WHERE `id` = ".$info['id']);
				if($query1 && $query2)
					{
						OutputExit('Ваш домен успешно создан!<br /><a href="/?r=aut/domains" title="Список всех доменов">Перейти к списку доменов</a>');
					}
				else
					{
						FatalError(GetErrorText('mysql'));
					}
			}
	}

SetTitle("Регистрация домена");
GetHeader();
FormError(isset($error) ? $error : '');
?>

<form action="/?r=aut/add" method="post">
Выберите доменное имя:<br />
<input type="text" name="sub" maxlength="50" required="required" class="form-control" value="<?=isset($_POST['sub']) ? htmlspecialchars($_POST['sub']) : ''; ?>" style="max-width: 300px;"/><br />
<select name="domain" class="form-control" style="max-width: 300px;">';
<?php
$q = mysql_query("SELECT * FROM `domains` ORDER BY `domain` ASC");
while($dom = mysql_fetch_array($q))
	{
		echo '<option value="'.$dom['domain'].'">.'.$dom['domain'].'</option>';
		echo "\n";
	}
?>
</select><br />
Скрыть домен (не отображается в каталоге):<br />
<input type="radio" name="hidden" value="1"<?=(isset($_POST['hidden']) && @$_POST['hidden'] == 1 ? ' checked="checked"' : '')?> /> Скрыть<br />
<input type="radio" name="hidden" value="2"<?=(isset($_POST['hidden']) && @$_POST['hidden'] == 2 ? ' checked="checked"' : '')?> /> Не скрывать<br />
<input type="submit" name="button" class="btn btn-default" value="Зарегистрировать" /></form>

<?php
GetFooter();
?>