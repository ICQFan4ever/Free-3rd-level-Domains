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
### Запрашиваем домен
$domain = mysql_fetch_array(mysql_query("SELECT * FROM `domains` WHERE `id`=".$sub['id_domain']));

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
		
		if(empty($error))
			{
				if(!empty($sub['type']))
					{
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
						
						// Таки удаляем
						$var = RemoveDNSRecord($domain['domain'], $domain['token'], $_ID);
						if($var != true)
							{
								FatalError('Ошибка удаления домена');
							}
					}
				else
					{
						$var = true;
					}
				if($var === true)
					{
						$query1 = mysql_query("DELETE FROM `subdomains` WHERE `id`=".$id);
						$query2 = mysql_query("UPDATE `domains` SET `counter` = `counter` - 1 WHERE `id`=".$domain['id']);
						if($query1 && $query2)
							{
								OutputExit('Домен успешно удален');
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

SetTitle('Удалить домен');
GetHeader();
FormError(isset($error) ? $error : '');
?>
Вы действительно хотите удалить домен?<br />
<form action="/?r=aut/delete_domain&id=<?=$id?>" method="post">
<img src="/?r=captcha" alt="" style="margin-bottom: 4px;" /><br />
<input type="text" name="code" class="form-control" style="max-width: 300px;" required="required" placeholder="Число выше" /><br />
<input type="submit" name="button" value="Удалить" class="btn btn-danger" /></form>

<?php
GetFooter();