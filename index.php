<?php 

  error_reporting(0);

  require_once "inc/lib.inc.php";
  require_once "inc/data.inc.php";

  //обработчик ошибок

  set_error_handler("myError");

  //Установка локали и даты

  setlocale(LC_ALL, "russian");
  $day = strftime('%d');
  $mon = strftime('%B');
  $mon = iconv('windows-1251', 'utf-8', $mon);
  $year = strftime('%Y');

  //Приветствие

  $hour = (int) strftime('%H');
  $welcome = '';
  if($hour >= 0 && $hour < 6) $welcome = 'Доброй ночи';
  elseif($hour >= 6 && $hour < 12) $welcome = 'Доброе утро';
  elseif($hour >= 12 && $hour < 18) $welcome = 'Добрый день';
  elseif($hour >= 18 && $hour < 23) $welcome = 'Добрый вечер';
  else $welcome = 'Доброй ночи';

  //Инициализация заголовков страницы

  $title = 'Сайт нашей школы';
  $header = "$welcome, Гость!";

  $id = strtolower(strip_tags(trim($_GET['id'])));

  switch($id) {

    case 'about':
      $title = 'О сайте';
      $header = 'О нашем сайте';
      break;

    case 'contact':
      $title = 'Контакты';
      $header = 'Обратная связь';
      break;

    case 'table':
      $title = 'Таблица умножения';
      $header = 'Таблица умножения';
      break;
      
    case 'calc':
      $title = 'Он-лайн калькулятор';
      $header = 'Калькулятор';
      break;

  }
?>

<!DOCTYPE html>
<html>

<head>
  <title><?=$title?></title>
  <meta charset="utf-8" />
  <link rel="stylesheet" href="style.css" />
</head>

<body>

  <div id="header">
    <!-- Верхняя часть страницы -->
    <?php 
      require_once "inc/top.inc.php";
    ?>
    <!-- Верхняя часть страницы -->
  </div>

  <div id="content">
    <!-- Заголовок -->
    <h2><?=$header?></h2>
    <!-- Заголовок -->
    <blockquote>
      <?="<h3>Сегодня $day число, $mon месяц, $year год.</h3>"?>
    </blockquote>
    <!-- Область основного контента -->
    <?php 
      switch($id) {
        case 'about':
          require_once 'about.php';
          break;
        case 'contact':
          require_once 'contact.php';
          break;
        case 'table':
          require_once 'table.php';
          break;
        case 'calc':
          require_once 'calc.php';
          break;
        default:
          require_once 'inc/index.inc.php';
          break;
      }
    ?>
    <!-- Область основного контента -->
  </div>
  <div id="nav">
    <!-- Навигация -->
    <?php 
      require_once "inc/menu.inc.php";
    ?>
    <!-- Навигация -->
  </div>
  <div id="footer">
    <!-- Нижняя часть страницы -->
    <?php 
      require_once "inc/bottom.inc.php";
    ?>
    <!-- Нижняя часть страницы -->
  </div>
</body>

</html>