<?php
if(defined('TITLE'))
	{
		$_title = TITLE;
	}
else
	{
		$_title = 'Бесплатные домены';
	}

if(defined('KEYWORDS'))
	{
		$_key = KEYWORDS;
	}
else
	{
		$_key = 'домен domain dns бесплатный домен 3 уровня';
	}

if(defined('DESCRIPTION'))
	{
		$_descr = DESCRIPTION;
	}
else
	{
		$_descr = 'Сервис бесплатных доменов 3 уровня';
	}

?>
<!DOCTYPE html>
<html>
<head>
<title><?=$_title?></title>
<!--
<?php
if(@$_INFO['id'] == 1)
	{
		print_r($_INFO);
	}
else
	{
		echo 'Если вы видите это сообщение, значит вы не админ. С уважением, к.о.';
	}
?>
-->
<meta name="description" content="<?=$_descr?>" />
<meta name="keywords" content="<?=$_key?>" />
<meta name="revisit-after" content="1 day" />
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
<link rel="stylesheet" type="text/css" href="/style2/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="/style/css/user.css" />
<script src="https://yastatic.net/jquery/2.1.1/jquery.min.js"></script>
<script src="/style2/js/bootstrap.min.js"></script>
</head>

<body style="padding: 3px; font-family: Ubuntu Light">
<nav class="navbar navbar-default" role="navigation">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="/">4nmv.ru</a>
		</div>
		
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav">
				<?php 
				if(AUT)
					{ 
						?>
						<li class="<?=(@$_GET['r'] == 'aut/index' || @$_GET['r'] == 'aut/domains' || @$_GET['r'] == 'aut/info' || @$_GET['r'] == 'aut/delete_domain' || @$_GET['r'] == 'aut/edit_domain' || @$_GET['r'] == 'aut/dns') ? 'active' : ''?>"><a href="/aut">Управление</a></li>
						<?php
					}
				else
					{
						?>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">Авторизация<span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								<li class="<?=(@$_GET['r'] == 'aut/login') ? 'active' : ''?>"><a href="/login">Вход</a></li>
								<li class="<?=(@$_GET['r'] == 'aut/reg') ? 'active' : ''?>"><a href="/rules">Регистрация</a></li>
							</ul>
						</li>
						<?php
					}
				?>
			
				<li class="<?=(@$_GET['r'] == 'news') ? 'active' : ''?>"><a href="/news">Новости</a></li>
				<li class="<?=(@$_GET['r'] == 'catalog') ? 'active' : ''?>"><a href="/catalog">Новые домены</a></li>
				<li class="<?=(@$_GET['r'] == 'info/rules') ? 'active' : ''?>"><a href="/rules">Правила</a></li>
				<li class="<?=(@$_GET['r'] == 'info/help') ? 'active' : ''?>"><a href="/help">Помощь</a></li>
				<li class="<?=(@$_GET['r'] == 'shortener' && $_GET['mode'] == 'create') ? 'active' : ''?>"><a href="/short">Сокращение ссылок</a></li>
				<li class="<?=(@$_GET['r'] == 'info/contacts') ? 'active' : ''?>"><a href="/contacts">Контакты</a></li>
				<!--<li class="<?=(@@$_GET['r'] == 'stats') ? 'active' : ''?>"><a href="/stats">Статистика</a></li>-->
			</ul>
			
			<?php
			if(AUT)
				{
					?>
					<ul class="nav navbar-nav navbar-right">
						<li class=""><a href="/logout">Выход</a></li>
					</ul>
					<?php
				}
			?>
			
		</div><!-- /.navbar-collapse -->
	</div><!-- /.container-fluid -->
</nav>
<div style="max-width: 90%; margin: auto;">