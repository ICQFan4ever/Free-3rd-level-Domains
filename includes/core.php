<?php
# Ядро

########### Некоторые глобальные настройки ###########
error_reporting(E_ALL);
set_time_limit(0);
ignore_user_abort(true);

########### Константы ###########
define("START", microtime());
define("R", $_SERVER['DOCUMENT_ROOT']);
define("IP", $_SERVER['REMOTE_ADDR']);
define("LONGIP", ip2long($_SERVER['REMOTE_ADDR']));

########### MySQL ###########
@mysql_connect(':/var/run/mysqld/mysqld.sock','root','password') or die(mysql_error());
mysql_select_db('free_domains') or die(mysql_error());
mysql_set_charset('utf8');
define("UA", htmlspecialchars(mysql_real_escape_string(trim($_SERVER['HTTP_USER_AGENT']))));



########### Стартуем сессию ###########
session_name("sess");
session_start();

########### Функции авторизации ###########
## Проверяем наличие авторизации
if(isset($_COOKIE['sid']))
	{
		$sid = mysql_real_escape_string($_COOKIE['sid']);
		$q = mysql_query("SELECT * FROM `users` WHERE `sid`='".$sid."' LIMIT 1");
		if(mysql_num_rows($q) != 1)
			{
				define("AUT", false);
				$_INFO['onpage'] = 10;
			}
		else
			{
				define("AUT", true);
				$_INFO = mysql_fetch_array($q);
			}
	}
else
	{
		define("AUT", false);
		$_INFO['onpage'] = 10;
	}

## Проверяем, админ ли
if(isset($_INFO) && AUT)
	{
		if($_INFO['level'] > 1)
			{
				define("ADM", true);
			}
		else
			{
				define("ADM", false);
			}
	}
else
	{
		define("ADM", false);
	}

## Переназначаем показ ошибок
if(ADM)
	{
		error_reporting(~E_NOTICE);
	}

## Если авторизован - обновляем дату последнего визита, IP-шник и UA
if(AUT && isset($_INFO))
	{
		mysql_query("UPDATE `users` SET `last_visit` = ".time().", `ip`=".LONGIP.", `ua` = '".UA."' WHERE `id`=".$_INFO['id']);
	}

## Создаем функции доступа
# Только авторизованным
function AutOnly()
	{
		if(!AUT)
			{
				header("Location: /?r=aut/login&d=".(isset($_GET['r']) ? $_GET['r'] : ''));
				exit;
			}
	}

# Только админу
function AdminOnly()
	{
		if(!ADM)
			{
				header("Location: /?r=aut/index");
				exit;
			}
	}
############# Конструктор ##############

# Задать заголовок
function SetTitle($title = 'Default')
	{
		define("TITLE", $title);
	}

# Задать ключевые слова
function SetKeywords($words = '')
	{
		define("KEYWORDS", $words);
	}

# Задать описание
function SetDescription($descr = '')
	{
		define("DESCRIPTION", $descr);
	}

# Роботы?
function RobotAccess($bool = 'true')
	{
		if($bool)
			{
				define("ROBOTS", true);
			}
		else
			{
				define("ROBOTS", false);
			}
	}

# Голова
function GetHeader()
	{
		GLOBAL $_INFO;
		include_once R.'/includes/head.php';
	}
	
# Ноги
function GetFooter()
	{
		GLOBAL $_INFO;
		include_once R.'/includes/foot.php';
		exit;
	}

############# Базовые функции ##############

# Редирект + exit
function Redirect($to = "/")
	{
		header("Location: ".$to);
		exit;
	}


# Генератор хэшей #
function GenMD5()
	{
		return md5(md5(time().microtime().rand(100000,999999).$_SERVER['REMOTE_ADDR']));
	}

# Генератор паролей
function GenPass($length = 8)
	{
		$array = array
		('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j',
		'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't',
		'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D',
		'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N',
		'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
		'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7',
		'8', '9');
		$pass = '';
		for($i = 1; $i <= $length; $i++)
			{
				$pass .= $array[rand(0, count($array) - 1)];
			}
		return $pass;
	}

# Алиас mb_strlen()
function Len($string = '')
	{
		return mb_strlen($string, 'utf-8');
	}

# Алиас mb_substr()
function Cut($string = '', $start = 0, $num = 1)
	{
		return mb_substr($string, $start, $num, 'utf-8');
	}

######### Функции даты и времени ###########

function TimeOfDay()
	{
		$hour = date("H");
		if($hour >= 23 && $hour < 5)
			{
				$tof = "Доброй ночи";
			}
		elseif($hour >= 5 && $hour < 11)
			{
				$tof = "Доброе утро";
			}
		elseif($hour >= 11 && $hour < 17)
			{
				$tof = "Добрый день";
			}
		else
			{
				$tof = "Добрый вечер";
			}
		return $tof;
	}

### Выводим месяц по номеру
function GetMonthName($num = 0)
	{
		switch($num)
			{
				case '1': $month = "января"; break;
				case '2': $month = "февраля"; break;
				case '3': $month = "марта"; break;
				case '4': $month = "апреля"; break;
				case '5': $month = "мая"; break;
				case '6': $month = "июня"; break;
				case '7': $month = "июля"; break;
				case '8': $month = "августа"; break;
				case '9': $month = "сентября"; break;
				case '10': $month = "октября"; break;
				case '11': $month = "ноября"; break;
				case '12': $month = "декабря"; break;
				case '01': $month = "января"; break;
				case '02': $month = "февраля"; break;
				case '03': $month = "марта"; break;
				case '04': $month = "апреля"; break;
				case '05': $month = "мая"; break;
				case '06': $month = "июня"; break;
				case '07': $month = "июля"; break;
				case '08': $month = "августа"; break;
				case '09': $month = "сентября"; break;
				default: $month = "мартабря"; break;
			}
		return $month;
	}

# День недели
function GetDayName($name = 1)
	{
		switch($name)
			{
				case '1': $day = "Понедельник"; break;
				case '2': $day = "Вторник"; break;
				case '3': $day = "Среда"; break;
				case '4': $day = "Четверг"; break;
				case '5': $day = "Пятница"; break;
				case '6': $day = "Суббота"; break;
				case '7': $day = "Воскресенье"; break;
				default: $day = "O_o";
			}
		return $day;
	}

# Форматирование метки времени
function ShowTime($time = 0)
	{
		if(date("d.m.Y", $time) == date("d.m.Y"))
			{
				$return = 'сегодня в '.date("H:i", $time);
			}
		elseif(date("m",$time) == date("m") && date("Y",$time) == date("Y") && date("d", $time) == date("d") - 1)
			{
				$return = 'вчера';
			}
		else
			{
				$return = date("d.m.Y", $time);
			}
		return $return;
	}

########### Обработка данных #############

# Фильтрация текста #
function FilterText($text="",$len=100)
	{
		$output=mb_substr(trim(htmlspecialchars(mysql_real_escape_string($text))),0,(int)$len,'utf-8');
		if(mb_strlen($output,'utf-8')==0)
			{
				return false;
			}
		else
			{
				return $output;
			}
	}


# Проверка E-mail на валидность
function IsMail($mail = '') 
	{
		if(preg_match("/^[0-9a-zA-Zа-яА-Я\_\-\.]{1,50}@[0-9a-zA-Zа-яА-Я\.\-]{2,40}\.[a-zA-Zа-яА-Я]{2,6}$/u", $mail)) 
			{
				return true;
			} 
		else 
			{
				return false;
			}
	}

## Проверка домена на валидность
function CheckDomain($subdomain = '', $type = 'en')
	{
		if(Len($subdomain) < 2)
			{
				return false;
			}
		else
			{
				if(mb_substr($subdomain, -1, 1, 'utf-8') == '-' || mb_substr($subdomain, 0, 1, 'utf-8') == '-')
					{
						return false;
					}
				else
					{
						if($type == 'en')
							{
								$pattern = "#^([a-zA-Z0-9\-]{2,50})$#";
							}
						else
							{
								$pattern = "#^([а-яА-ЯЁё0-9\-]{2,50})$#";
							}
						if(preg_match($pattern, $subdomain))
							{
								return true;
							}
						else
							{
								return false;
							}
					}
			}
	}

######### Обработка ошибок ########
# Обычный вывод ошибки #
function Error($error = '')
	{
		return '<div class="alert alert-danger" role="alert">'.$error.'</div>';
	}

# Фатальная ошибка #
function FatalError($error = '')
	{
		GLOBAL $_INFO;
		SetTitle('Ошибка');
		include_once R.'/includes/head.php';
		echo Error($error);
		include_once R.'/includes/foot.php';
		$string = date("d.m.Y H:i:s").', IP: '.$_SERVER['REMOTE_ADDR'].', UA: '.UA."\r\n".$error;
		if(AUT)
			{
				$string .= "\r\nID юзера: ".$_INFO['id'].', логин: '.$_INFO['login'];
			}
		$string .= "\r\n\r\n";
		$f = @fopen(R.'/includes/error.log', 'a');
		flock($f, LOCK_UN);
		fputs($f, $string);
		flock($f, LOCK_EX);
		fclose($f);
		exit;
	}

# вывод статического содержимого
function OutputExit($text = '')
	{
		SetTitle('Бесплатные домены 3 уровня');
		GLOBAL $_INFO;
		include_once R.'/includes/head.php';
		echo '<div class="alert alert-success" role="alert">'.$text.'</div>';
		include_once R.'/includes/foot.php';
		exit;
	}
	
# Доступ к ошибке по ее коду #
function GetErrorText($code = '')
	{
		switch($code)
			{
				case 'ck': $error = "Подозрение на CSRF-атаку. Вернитесь назад и повторите запрос"; break;
				case 'email': $error = "Некорректный формат E-mail"; break;
				case 'code': $error = "Код с картинки введен неверно"; break;
				case 'empty': $error = "Поле не может быть пустым"; break;
				case 'other': $error = "Введенные данные не совпадают"; break;
				case 'aut': $error = "Авторизуйтесь для просмотра раздела"; break;
				case 'access': $error = "Ошибка доступа к разделу"; break;
				case '404': $error = "Страница не найдена"; break;
				case 'mysql': $error = "MySQL-сервер сообщил об ошибке:<br />".mysql_error(); break;
				default: $error = "Неизвестная ошибка"; break;
			}
			return $error;
	}
	
# Ошибка формы #
function FormError($error = array())
	{
		if(!empty($error))
			{
				echo '<div class="alert alert-danger" role="alert">';
				foreach($error as $text)
					{
						echo $text."<br />\n";
					}
				echo '</div>';
			}
	}

# Не ошибка
function Good($text = '')
	{
		echo '<div class="alert alert-success">'.$text.'</div>';
	}

####### Оформление текста #######

// xuj

####### PageNavi #######

#### Панель навигации 
function PageNavi($r = '?', $num, $page)
	{
		# Разброс страниц
		$tmp['r'] = 2;
		# не трогать
		$tmp['num'] = $tmp['r'] + 2;
		$tmp['i'] = $tmp['r'];
		echo '<ul class="pagination pagination-sm">';
		### Начнем с левой части
		if($page <= $tmp['num'])
			{
				for($i = 1; $i < $page; $i++)
					{
						echo '<li><a href="'.$r.'&page='.$i.'" title="Перейти на '.$i.' страницу">'.$i.'</a></li>';
						echo "\n";
					}
			}
		else
			{
				echo '<li><a href="'.$r.'&page=1" title="На первую страницу">1</a></li>';
				echo '<li><a href="#">..</a></li>';
				for($i = $page - $tmp['i']; $i <= $page - 1; $i++)
					{
						echo '<li><a href="'.$r.'&page='.$i.'" title="Перейти на '.$i.' страницу">'.$i.'</a></li>';
						echo "\n";
					}
			}
		echo '<li class="active"><a href="#">'.$page.'<span class="sr-only">(current)</span></a></li>';
		echo "\n";
		
		### Правая часть
		$razn = $num - $page;
		if($razn < $tmp['num'])
			{
				for($i = $page + 1; $i <= $num; $i++)
					{
						echo '<li><a href="'.$r.'&page='.$i.'" title="Перейти на '.$i.' страницу">'.$i.'</a></li>';
						echo "\n";
					}
			}
		else
			{
				for($i = $page + 1; $i <= $page + $tmp['i']; $i++)
					{
						echo '<li><a href="'.$r.'&page='.$i.'" title="Перейти на '.$i.' страницу">'.$i.'</a></li>';
						echo "\n";
					}
				echo '<li><a href="#">..</a></li>';
				echo '<li><a href="'.$r.'&page='.$num.'" title="Перейти на последнюю страницу">'.$num.'</a></li>';
			}
		echo '</ul>';
	}

#### Для LIMIT
function MysqlLimit($page = 1, $onpage = 10) 
	{
		$start = ($page - 1) * $onpage;
		return $start.', '.$onpage;
	}

#### Проверка страницы

function CheckPage($num = 1)
	{
		if(isset($_GET['page']))
			{
				$page = (int)$_GET['page'];
				if($page < 1 OR $page > $num)
					{
						$page = 1;
					}
			}
		else
			{
				$page = 1;
			}
		return $page;
	}


#### Блок Share

function ShareButtons($_TITLE = '')
	{
		$_URL = urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		$_LOGO = urlencode('http://'.$_SERVER['HTTP_HOST'].'/style/logo.png');
		$_TITLE = urlencode($_TITLE);
		?>
		<div id="sharing_buttons" style="margin: auto;">
		<div class="sps sps-theme-color sps-small">
		Расскажи друзьям о сайте:<br />
		<a title="Поделиться с друзьями на Facebook" target="_blank" href="http://www.facebook.com/sharer.php?u=<?=$_URL?>&amp;t=<?=$_TITLE?>" class="sps-facebook trans"></a>
		
		<a title="Поделиться с друзьями ВКонтакте" target="blank" href="http://vk.com/share.php?url=<?=$_URL?>&amp;title=<?=$_TITLE?>" class="sps-vkontakte trans"></a>
		
		<a title="Поделиться с друзьями на Одноклассниках" target="_blank" href="http://www.odnoklassniki.ru/dk?st.cmd=addShare&amp;st.s=1&amp;st._surl=<?=$_URL?>" class="sps-odnoklassniki trans"></a>
		
		<a title="Отправить в ленту Twitter" target="_blank" href="http://twitter.com/share?url=<?=$_URL?>&amp;text=<?=$_TITLE?>" class="sps-twitter trans"></a>
		
		<a title="Отправить в Google Plus" target="_blank" href="https://plus.google.com/share?url=<?=$_URL?>&amp;hl=ru" class="sps-google trans"></a>
		
		<a title="Отправить в Мой.Мир" target="_blank" href="http://connect.mail.ru/share?url=<?=$_URL?>&amp;title=<?=$_TITLE?>&amp;imageurl=<?=$_LOGO?>" class="sps-moimir trans"></a>
		
		<a title="Отправить в Живой Журнал" target="_blank" href="http://www.livejournal.com/update.bml?subject=<?=$_TITLE?>'&amp;event=<?=$_URL?>" class="sps-livejournal trans"></a>
		
		<a title="Отправить ссылку по почте" target="_blank" href="mailto:?subject=<?=$_TITLE?>&amp;body=<?=$_URL?>" class="sps-email trans"></a>
		
		
		</div>
		</div>
		<?php
	}

// To be continued