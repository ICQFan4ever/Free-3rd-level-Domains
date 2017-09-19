<?php
AdminOnly();
$id = (int)$_GET['id'];
$q = mysql_query("SELECT * FROM `domains` WHERE `id`=".$id);
if(mysql_num_rows($q) != 1)
	{
		FatalError(GetErrorText('404'));
	}
$domain = mysql_fetch_array($q);
if(isset($_GET['sure']))
	{
		mysql_query("DELETE FROM `subdomains` WHERE `id` = ".$domain['id']);
		mysql_query("DELETE FROM `domains` WHERE `id` = ".$id);
		Redirect('/?r=admin/domains');
	}
else
	{
		SetTitle('Подтверждение удаления');
		GetHeader();
		?>
		<div class="alert">Вы действительно хотите удалить домен и все связанные с ним поддомены? <b>Это действие нельзя отменить!</b></div>
		<form action="/" method="get">
		<input type="hidden" name="r" value="admin/delete_domain" />
		<input type="hidden" name="id" value="<?=$id?>" />
		<input type="hidden" name="sure" value="1" />
		<input type="submit" value="Удалить" class="btn btn-primary" />
		</form>
		<?php
		GetFooter();
	}