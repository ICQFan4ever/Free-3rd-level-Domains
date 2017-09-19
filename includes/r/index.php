<?php
SetTitle('Бесплатные домены 3 уровня');
SetDescription("Сервис регистрации бесплатных доменов 3 уровня");
GetHeader();
$q = mysql_query("SELECT * FROM `domains` ORDER BY `id` ASC");
$c = mysql_num_rows($q);
$q2 = mysql_query("SELECT * FROM `subdomains` WHERE `hidden` = 0");
$c2 = mysql_num_rows($q2);
?>


<h1>Добро пожаловать</h1>
<div class="well">
Здесь Вы можете бесплатно и быстро зарегистрировать доменное имя 3 уровня.<br />
На выбор <span class="label label-default"><?=$c?></span> &quot;зон&quot; (т.е. доменов 2 уровня).<br />
Уже зарегистрировано <span class="label label-default"><?=$c2?></span> доменных имен 3-го уровня.<br /><br />
<?php
if(isset($_POST['button']) && isset($_POST['subdomain']) && isset($_POST['id_domain']))
	{
		$error = array();
		$id_domain = (int)$_POST['id_domain'];
		if(preg_match("#^([a-zA-Z0-9\-]{1,50})$#iu", $_POST['subdomain']))
			{
				$subdomain = mysql_real_escape_string($_POST['subdomain']);
				if(Cut($subdomain, 0, 1) == '-' || Cut($subdomain, -1, 1) == '-')
					{
						$error[] = 'Домен не может начинаться или заканчиваться с дефиса';
					}
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
				if(isset($stop[$subdomain]))
					{
						$error[] = 'Домен запрещен для регистрации';
					}
				if(Len($sub) < 2 && $_INFO['vip'] != 1)
					{
						$error[] = 'Минимальная длина домена - 2 символа';
					}
			}
		else
			{
				$error[] = 'Неверный формат домена';
			}
		
		$qc = mysql_query("SELECT * FROM `domains` WHERE `id` = ".$id_domain);
		if(mysql_num_rows($qc) > 0)
			{
				$host = mysql_fetch_assoc($qc);
			}
		else
			{
				$error[] = 'Подмена опций списка - прямая дорога в ад';
			}
		
		if(empty($error))
			{
				$q_t = mysql_query("SELECT * FROM `subdomains` WHERE `name` = '".$subdomain."' AND `id_domain` = ".$id_domain);
				if(mysql_num_rows($q_t) == 0)
					{
						$success = true;
						$result = 'Доменное имя свободно';
					}
				else
					{
						$success = false;
						$result = 'Домен занят.';
						# проверим другие зоны
						$qz = mysql_query("SELECT * FROM `domains`");
						$other = array();
						while($inff = mysql_fetch_assoc($qz))
							{
								$q = mysql_query("SELECT * FROM `subdomains` WHERE `name` = '".$subdomain."' AND `id_domain` = ".$inff['id']);
								if(mysql_num_rows($q) == 0)
									{
										$other[] = $inff['domain'];
									}
							}
						
						if(!empty($other))
							{
								$result .= '<br />Вы можете зарегистрировать следующие домены: <br /><b>';
								foreach($other as $value)
									{
										$result .= $subdomain.'.'.$value.'<br />'.PHP_EOL;
									}
							}
					}
				echo '<div class="alert alert-'.($success ? 'success' : 'warning').'">'.$result.'</div>';
			}
		else
			{
				echo formError($error);
			}
	}

?>
<b>Проверьте занятость домена:<br />
<form action="/?" method="post">
<input type="text" name="subdomain" value="" placeholder="Желаемое имя" maxlength="20" class="form-control" required="required" />
<select name="id_domain" class="form-control">
<?php
$qd = mysql_query("SELECT * FROM `domains` ORDER BY `id` ASC");
while($inf = mysql_fetch_assoc($qd))
	{
		echo '<option value="'.$inf['id'].'">'.$inf['domain'].'</option>'.PHP_EOL;
	}
?>
</select>
<input type="submit" name="button" value="Проверить" class="btn btn-success" /><br />
</form>

Не создавайте отдельные домены для одноразовых ссылок - воспользуйтесь <a href="/short" title="Сокращение URL">сокращалкой ссылок</a> (доступно без регистрации).<br /><br />

Для жалоб и предложений воспользуйтесь <a href="/contact" title="Обратная связь">обратной связью</a>.


</div>

<?php
GetFooter();