<?php
AutOnly();

@setcookie('sid', '', time() - 86400, '/', $_SERVER['HTTP_HOST']);
if(isset($_GET['all']))
	{
		mysql_query("UPDATE `users` SET `sid` = '' WHERE `id` = ".$_INFO['id']);
	}
Redirect('/');