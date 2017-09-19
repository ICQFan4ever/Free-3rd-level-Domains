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
$sub = mysql_fetch_array($q);

if(empty($sub['type']))
	{
		Redirect('/?r=aut/dns&id='.$id);
	}
### Запрашиваем домен
$domain = mysql_fetch_array(mysql_query("SELECT * FROM `domains` WHERE `id`=".$sub['id_domain']));

### API
include_once R.'/includes/DNS-API.php';

### Для начала запросим все записи на домене

$records = GetDNSRecords($domain['domain'], $domain['token']);
### И найдем нужную запись
foreach($records as $rec_id => $record)
	{
		if($record['type'] == $sub['type'] && $record['subdomain'] == $sub['name'])
			{
				$found = true;
				$_ID = $rec_id;
				break;
			}
	}

if(!isset($found))
	{
		FatalError('Запись на DNS-сервере не обнаружена. Обратитесь к администратору');
	}

if($sub['type'] == 'NS')
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
						#### Обновляем DNS
						$var[1] = EditDNSRecord($domain['domain'], $domain['token'], $_ID, $sub['name'], $ns1, 'ns');
						$var[2] = EditDNSRecord($domain['domain'], $domain['token'], $_ID, $sub['name'], $ns2, 'ns');
						if($var[1] === true && $var[2] === true)
							{
								if(mysql_query("UPDATE `subdomains` SET `ns1` = '".$ns1."', `ns2` = '".$ns2."', `time_updated` = ".time()." WHERE `id`=".$id))
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
		<form action="/?r=aut/edit_subdomain&id=<?=$_GET['id']?>" method="post">
		Укажите 2 рабочих DNS-сервера (можно узнать в службе поддержки хостинг-провайдера или у владельца сервера).<br />
		NS1:<br />
		<input type="text" name="ns1" maxlength="100" required="required" class="form-control" style="max-width: 300px;" value="<?= $sub['ns1'] ?>" /><br />
		NS2:<br />
		<input type="text" name="ns2" maxlength="100" required="required" class="form-control" style="max-width: 300px;" value="<?= $sub['ns2'] ?>" /><br />
		<img src="/?r=captcha" alt="" /><br />
		Код с картинки:<br />
		<input type="text" name="code" class="form-control" style="max-width: 300px;" required="required" /><br />
		<input type="submit" name="button" value="Сохранить" class="btn btn-primary" /></form><br />
		
		<?php
		GetFooter();
		exit;
	}

if($sub['type'] == 'A')
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
						### Редактируем DNS
						$var[1] = EditDNSRecord($domain['domain'], $domain['token'], $_ID, $sub['name'], $_POST['ip'], 'a');
						if($var[1] === true)
							{
								if(mysql_query("UPDATE `subdomains` SET `a` = '".$_POST['ip']."', `time_updated` = ".time()." WHERE `id`=".$id))
									{
										OutputExit('IP-адрес сервера успешно сохранен. Полное делегирование домена может занять до 24 часов');
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
		SetTitle('Изменить A-запись');
		GetHeader();
		FormError(isset($error) ? $error : '');
		?>
		<form action="/?r=aut/edit_domain&id=<?= $id ?>" method="post">
		Укажите IP-адрес сервера:<br />
		<input type="text" name="ip" maxlength="15" required="required" value="<?= $sub['a'] ?>" class="form-control" style="max-width: 300px;" /><br />
		<img src="/captcha" alt="" /><br />
		Код с картинки:<br />
		<input type="text" name="code" required="required" class="form-control" style="max-width: 300px;" /><br />
		<input type="submit" name="button" value="Сохранить" class="btn btn-primary" /></form><br />
		
		<?php
		GetFooter();
		exit;
	}

if($sub['type'] == 'CNAME')
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
						$var[1] = EditDNSRecord($domain['domain'], $domain['token'], $_ID, $sub['name'], $url, 'cname');
						if($var[1] === true)
							{
								if(mysql_query("UPDATE `subdomains` SET `cname` = '".$url."', `time_updated` = ".time()." WHERE `id` = ".$id))
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
		SetTitle('Изменить CNAME-запись');
		GetHeader();
		FormError(isset($error) ? $error : '');
		?>
		<form action="/?r=aut/dns&mode=cname&id=<?= $id ?>" method="post">
		Укажите основной домен сайта (например, 4nmv.ru)<br />
		<input type="text" name="url" maxlength="100" required="required" class="form-control" style="max-width: 300px;" value="<?= $sub['cname'] ?>" /><br />
		<img src="/captcha" alt="" /><br />
		Код с картинки:<br />
		<input type="text" name="code" required="required" class="form-control" style="max-width: 300px;" /><br />
		<input type="submit" name="button" value="Сохранить" class="btn btn-primary" /></form><br />
		<a href="/?r=aut/dns&id=<?= $id ?>" title="Другая запись">Выбрать другой вариант</a><br />
		
		<?php
		GetFooter();
		exit;
	}
#print_r($sub);