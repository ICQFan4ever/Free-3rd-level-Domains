<?php

$arrww = explode('.', $_SERVER['HTTP_HOST']);
if($arrww[0] == '123')
	{
		header('Location: http://4nmv.ru/123.pdf');
		exit;
	}

if(isset($_GET['r']))
	{
		$r = str_replace(']', '', str_replace('[', '', $_GET['r']));
		if(substr($r, 0, 1) != '.')
			{
				if(file_exists('includes/r/'.$r.'.php'))
					{
						include_once 'includes/core.php';
						include_once 'includes/r/'.$r.'.php';
					}
				else
					{
						header("Location: /?error=404");
					}
			}
		else
			{
				header("Location: /?hack");
			}
	}
else
	{
		include_once 'includes/core.php';
		include_once 'includes/r/index.php';
	}
