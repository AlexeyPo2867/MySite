<?php
session_start();

require 'DbConnect.php';
$pdo = DbConnect::getConnection();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Главная страница</title>
  <link rel="stylesheet" href="style/style.css">
</head>
<body>
  <div class="main">
    <?php
      if( isset($_SESSION['valid_user']) ){// если пользователь авторизован
        // показываем какую-то персональную инфу
        if( isset($_SESSION['first_name'])){// если есть имя и фамилия
          // отобразить приветствие с их использованием
          echo "<h3>Добро пожаловать, $_SESSION[first_name] $_SESSION[last_name]</h3>";
          echo '<a href="cabinet.php">Личный кабинет</a><br>';
          echo '<a href="exit.php">Выход</a>';
        }else{// если их нет
          // отобразить приветствие используя логин - valid_user
          echo "<h3>Добро пожаловать, $_SESSION[valid_user]</h3>";
          echo '<a href="cabinet.php">Личный кабинет</a><br>';
          echo '<a href="exit.php">Выход</a>';
        }
      }else{ // если пользователь не авторизован
        // показываем ссылки на регистрацию и вход
        echo '<a href="enter.php">Вход</a>';
        echo '<a href="register.php">Регистрация</a>';
      }

    ?>
  </div>
</body>
</html>

