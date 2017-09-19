<?php
AdminOnly();
$id = (int)$_GET['id'];
$q = mysql_query("SELECT * FROM `domains` WHERE `id`=".$id);
if(mysql_num_rows($q) != 1)
	{
		FatalError(GetErrorText('404'));
	}
$domain = mysql_fetch_array($q);

if(isset($_POST['button']))
	{
		$error = array();
		
		if(isset($_POST['token']))
			{
				$token = mysql_real_escape_string($_POST['token']);
			}
		else
			{
				$error[] = 'Введите ключ';
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
				if(mysql_query("UPDATE `domains` SET `token`='".$token."', `type`='".$type."' WHERE `id`=".$domain['id']))
					{
						Redirect("/?r=admin/domains");
					}
				else
					{
						FatalError(GetErrorText('mysql'));
					}
			}
	}

SetTitle("Редактирование домена");
GetHeader();
FormError(isset($error) ? $error : '');

?>
<form action="/?r=admin/edit_domain&id=<?= $domain['id'] ?>" method="post">
Домен:<br />
<input type="text" name="domain" value="<?= $domain['domain']?>" readonly="readonly" /><br />
API-токен:<br />
<input type="text" name="token" maxlength="100" required="required" value="<?=(isset($domain['token']) ? $domain['token'] : '')?>" /><br />
Тип домена:<br />
<input type="radio" name="type" value="en" required="required"<?=($domain['type'] == 'en' ? ' checked="checked"' : '')?> /> Обычный<br />
<input type="radio" name="type" value="ru" required="required"<?=($domain['type'] == 'ru' ? ' checked="checked"' : '')?> /> Кириллический<br />
<input type="submit" name="button" value="Редактировать"></form><br />
<a href="/?r=admin/delete_domain&id=<?= $domain['id'] ?>" title="Удалить домен">Удалить домен</a>
<?php
GetFooter();