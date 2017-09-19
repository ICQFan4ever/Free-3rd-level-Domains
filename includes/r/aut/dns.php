<?php
AutOnly();
$id = (int)$_GET['id'];
if(ADM)
	{
		$query = 'SELECT * FROM `subdomains` WHERE `id`='.$id;
	}
else
	{
		$query = 'SELECT * FROM `subdomains` WHERE `id`='.$id.' AND `id_user`='.$_INFO['id'];
	}
$q = mysql_query($query);
if(mysql_num_rows($q) != 1)
	{
		FatalError(GetErrorText('404'));
	}
else
	{
		$sub = mysql_fetch_array($q);
		if(!empty($sub['type']))
			{
				FatalError('Для вашего домена уже указаны записи. Если Вы хотите их сменить, обратитесь в тех.поддержку');
			}
		else
			{
				$dom = mysql_fetch_array(mysql_query("SELECT * FROM `domains` WHERE `id`=".$sub['id_domain']));
				include_once R.'/includes/DNS-API.php';
				# Свитчим варианты записей
				if(isset($_GET['mode']))
					{
						########### NS
						if($_GET['mode'] == 'ns')
							{
								if(isset($_POST['button']))
									{
										$error = array();
										
										if(isset($_POST['code']))
											{
												if($_POST['code'] != $_SESSION['code'])
													{
														$error[] = 'Неверный код с картинки';
													}
											}
										else
											{
												$error[] = 'Введите код с картинки';
											}
										
										if(isset($_POST['ns1']))
											{
												if(preg_match("#^([a-zA-Z0-9а-яА-ЯёЁ\.\-]{1,100})$#u", $_POST['ns1']))
													{
														$ns1 = $_POST['ns1'];
													}
												else
													{
														$error[] = 'Некорректный формат адреса первичного DNS-сервера';
													}
											}
										else
											{
												$error[] = 'Укажите первичный DNS-сервер';
											}
										
										if(isset($_POST['ns2']))
											{
												if(preg_match("#^([a-zA-Z0-9а-яА-ЯёЁ\.\-]{1,100})$#u", $_POST['ns2']))
													{
														$ns2 = $_POST['ns2'];
													}
												else
													{
														$error[] = 'Некорректный формат адреса первичного DNS-сервера';
													}
											}
										else
											{
												$error[] = 'Укажите вторичный DNS-сервер';
											}
										
										if(isset($ns1) && isset($ns2))
											{
												if($ns1 == $ns2)
													{
														$error[] = 'Введите разные DNS-сервера';
													}
											}
										
										if(empty($error))
											{
												# Пилим NS-запись. Ыыыы
												$var[1] = CreateDNSRecord($dom['domain'], $dom['token'], $sub['name'], 'ns', $ns1);
												$var[2] = CreateDNSRecord($dom['domain'], $dom['token'], $sub['name'], 'ns', $ns2);
												if($var[1] === true && $var[2] === true)
													{
														if(mysql_query("UPDATE `subdomains` SET `type` = 'ns', `ns1` = '".$ns1."', `ns2` = '".$ns2."', `time_updated` = ".time()." WHERE `id` = ".$id))
															{
																OutputExit('DNS-сервера успешно сохранены. Полное делегирование домена может занять до 24 часов');
															}
														else
															{
																FatalError(GetErrorText('mysql'));
															}
													}
												else
													{
														FatalError('DNS-сервер сообщил об ошибке:<br />NS1: '.(isset($var[1]['error']) ? ParseDNSResult($var[1]['error']) : 'Нет ошибок').'<br />NS2: '.(isset($var[2]['error']) ? ParseDNSResult($var[2]['error']) : 'Нет ошибок'));
													}
											}
									}
								SetTitle('Сконфигурировать NS-записи');
								GetHeader();
								FormError(isset($error) ? $error : '');
								?>
								<form action="/?r=aut/dns&mode=ns&id=<?=$_GET['id']?>" method="post">
								Укажите 2 рабочих DNS-сервера (можно узнать в службе поддержки хостинг-провайдера или у владельца сервера).<br /><br />
								NS1:<br />
								<input type="text" name="ns1" maxlength="100" required="required" value="<?= (isset($_POST['ns1']) ? htmlspecialchars($_POST['ns1']) : ''); ?>" class="form-control" style="max-width: 300px;" /><br />
								NS2:<br />
								<input type="text" name="ns2" maxlength="100" required="required" value="<?= (isset($_POST['ns2']) ? htmlspecialchars($_POST['ns2']) : ''); ?>" class="form-control" style="max-width: 300px;" /><br />
								<img src="/captcha" alt="" /><br />
								Код с картинки:<br />
								<input type="text" name="code" required="required" class="form-control" style="max-width: 300px;" /><br />
								<input type="submit" name="button" value="Сохранить" class="btn btn-default" /></form><br />
								<a href="/?r=aut/dns&id=<?= $_GET['id']?>" title="Другая запись">Выбрать другой вариант</a><br />
								
								<?php
								GetFooter();
								exit;
							}
						
						########## A-запись
						if($_GET['mode'] == 'a')
							{
								if(isset($_POST['button']))
									{
										$error = array();
										
										if(isset($_POST['code']))
											{
												if($_POST['code'] != $_SESSION['code'])
													{
														$error[] = 'Неверный код с картинки';
													}
											}
										else
											{
												$error[] = 'Введите код с картинки';
											}
										
										if(isset($_POST['ip']))
											{
												$_tmp = explode(".", $_POST['ip']);
												if(count($_tmp) == 4)
													{
														for($i = 1; $i < 4; $i++)
															{
																if($_tmp[$i] < 0 | $_tmp[$i] > 255)
																	{	
																		$_var = true;
																	}
																if(isset($_var))
																	{
																		$error[] = 'Некорректный формат IP-адреса';
																	}
															}
													}
												else
													{
														$error[] = 'Некорректный формат IP-адреса';
													}
											}
										else
											{
												$error[] = 'Введите IP-адрес сервера';
											}
										
										if(empty($error))
											{
												# Пилим NS-запись. Ыыыы
												$var[1] = CreateDNSRecord($dom['domain'], $dom['token'], $sub['name'], 'a', $_POST['ip']);
												if($var[1] === true)
													{
														if(mysql_query("UPDATE `subdomains` SET `type` = 'a', `a` = '".$_POST['ip']."', `time_updated` = ".time()." WHERE `id` = ".$id))
															{
																OutputExit('A-запись успешно добавлена.');
															}
														else
															{
																FatalError(GetErrorText('mysql'));
															}
													}
												else
													{
														FatalError('DNS-сервер сообщил об ошибке:<br />'.(isset($var[1]['error']) ? ParseDNSResult($var[1]['error']) : ''));
													}
											}
									}
								SetTitle('Создать A-запись');
								GetHeader();
								FormError(isset($error) ? $error : '');
								?>
								<form action="/?r=aut/dns&mode=a&id=<?=$_GET['id']?>" method="post">
								Укажите IP-адрес сервера:<br /><br />
								<input type="text" name="ip" maxlength="15" required="required" value="<?= (isset($_POST['ip']) ? htmlspecialchars($_POST['ip']) : ''); ?>" class="form-control" style="max-width: 300px;" /><br />
								<img src="/captcha" alt="" /><br />
								Код с картинки:<br />
								<input type="text" name="code" required="required" class="form-control" style="max-width: 300px;" /><br />
								<input type="submit" name="button" value="Сохранить" class="btn btn-default" /></form><br />
								<a href="/?r=aut/dns&id=<?= $_GET['id']?>" title="Другая запись">Выбрать другой вариант</a><br />
								
								<?php
								GetFooter();
								exit;
							}
						
						######### CNAME
						if($_GET['mode'] == 'cname')
							{
								if(isset($_POST['button']))
									{
										$error = array();
										
										if(isset($_POST['code']))
											{
												if($_POST['code'] != $_SESSION['code'])
													{
														$error[] = 'Неверный код с картинки';
													}
											}
										else
											{
												$error[] = 'Введите код с картинки';
											}
										
										if(isset($_POST['url']))
											{
												if(preg_match("#^([a-zA-Zа-яА-ЯёЁ0-9\-\.]{3,100})$#u", $_POST['url']))
													{
														$url = $_POST['url'];
													}
												else
													{
														$error[] = 'Неверный формат URL-адреса';
													}
											}
										else
											{
												$error[] = 'Введите адрес для переадресации';
											}
										
										if(empty($error))
											{
												# Пилим NS-запись. Ыыыы
												$var[1] = CreateDNSRecord($dom['domain'], $dom['token'], $sub['name'], 'cname', $url);
												if($var[1] === true)
													{
														if(mysql_query("UPDATE `subdomains` SET `type` = 'cname', `cname` = '".$url."', `time_updated` = ".time()." WHERE `id` = ".$id))
															{
																OutputExit('CNAME-запись успешно добавлена.');
															}
														else
															{
																FatalError(GetErrorText('mysql'));
															}
													}
												else
													{
														FatalError('DNS-сервер сообщил об ошибке:<br />'.(isset($var[1]['error']) ? ParseDNSResult($var[1]['error']) : ''));
													}
											}
									}
								SetTitle('Создать CNAME-запись');
								GetHeader();
								FormError(isset($error) ? $error : '');
								?>
								<form action="/?r=aut/dns&mode=cname&id=<?=$_GET['id']?>" method="post">
								Укажите основной домен сайта (например, 4nmv.ru)<br /><br />
								<input type="text" name="url" maxlength="100" required="required" value="<?= (isset($_POST['url']) ? htmlspecialchars($_POST['url']) : ''); ?>" class="form-control" style="max-width: 300px;" /><br />
								<img src="/captcha" alt="" /><br />
								Код с картинки:<br />
								<input type="text" name="code" required="required" class="form-control" style="max-width: 300px;" /><br />
								<input type="submit" name="button" value="Сохранить" class="btn btn-default" /></form><br />
								<a href="/?r=aut/dns&id=<?= $_GET['id']?>" title="Другая запись">Выбрать другой вариант</a><br />
								
								<?php
								GetFooter();
								exit;
							}
						Redirect("/?r=aut/dns.php&id=".$_GET['id']);
					}
				else
					{
						SetTitle('Настройка DNS-записей');
						GetHeader();
						?>
						<b>Выберите один из вариантов настройки домена. Помните, что выбрать тип записей можно только один раз, для изменения типа делегирования нужно обратиться в тех.поддержку.</b><br /><br />
						Не забудьте &quot;прикрепить&quot; домен на сервере.<br />
						<a href="/?r=aut/dns&mode=ns&id=<?= $_GET['id']?>" class="label label-primary" title="Установить NS-записи">
						NS-сервера</a><br />
						Данный вариант следует использовать, если Вы прикрепляете домен к другому DNS-серверу, например, при размещении сайта у хостинг-провайдера. Вам необходимо узнать два адреса (первичный и вторичный) DNS-серверов у владельца сервера.<br /><br />
						
						<a href="/?r=aut/dns&mode=a&id=<?= $_GET['id']?>" title="Установить A-запись" class="label label-primary">
						A-запись</a><br />
						При использовании A-записи домен будет прикреплен к указанному IP-адресу. Идеально, если сайт находится на VDS-сервере с одним IP-адресом<br /><br />
						
						<a href="/?r=aut/dns&mode=cname&id=<?= $_GET['id']?>" class="label label-primary" title="Установить CNAME-запись">
						CNAME-запись</a><br />
						CNAME запись позволяет сделать DNS-редирект на уже существующий домен
						<?php
						GetFooter();
					}
			}
	}