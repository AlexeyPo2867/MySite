<?php

echo '<h2>Метод загрузки страницы - '. $_SERVER['REQUEST_METHOD'] .' </h2>';
session_start();

require 'DbConnect.php';
$pdo = DbConnect::getConnection();

$login = $_SESSION['valid_user'];
//echo $login;

// делаем запрос к бд на получение данных о пользователе
//'first_name', 'last_name', 'login', 'email', 'password', 'image'
$query = "SELECT id, login, first_name, last_name, email, password, image, add_date
          FROM users
          WHERE login = ?";
$result = $pdo->prepare($query);
$result->execute([$login]);
$user = $result->fetch();
//d($user);

// если отправлена форма на изменение пароля - 2 шаг
if( isset($_POST['edit_password']) ){
    d($_POST);
    $edit_password = htmlspecialchars(trim($_POST['edit_password']));

    // проверяем пароль
    $reg_exp = "/^.{8,}$/u";
    if( !preg_match($reg_exp, $edit_password) ){ // если НЕ соответствует
        // выводим сообщение об ошибке
        header('Location: cabinet.php?error_edit_password=Пароль должен быть не менее 8 символов');
    }else{ // шифруем новый пароль и записываем в бд
      // шифрование пароля
      $edit_password = password_hash($edit_password, PASSWORD_DEFAULT);

      // запись нового пароля в бд у текущего пользователя
      $query = "UPDATE users SET password = ? WHERE id = ?";
      $result = $pdo->prepare($query);
      $result->execute([$edit_password, $user['id']]);
      header('Location: cabinet.php');
    }


}


?>
<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <title><?=$user['first_name'] . ' ' . $user['last_name']?></title>
  <link rel="stylesheet" href="style/style.css">
</head>
<body>
    <div class="main">
      <h1>Добро пожаловать, <?=$user['first_name'] . ' ' . $user['last_name']?></h1>

        <div class="field"><!-- аватар  -->
            <img src="<?=$user['image']?>" alt="<?=$user['first_name'] . ' ' . $user['last_name']?>">
        </div>

        <div class="field"><!-- логин  -->
            <p>Ваш логин: <span><?=$user['login']?></span></p>
        </div>

        <div class="field"><!-- имя  -->
            <p>Ваше имя: <span><?=$user['first_name']?></span></p>
        </div>

        <div class="field"><!-- фамилия  -->
            <p>Ваша фамилия: <span><?=$user['last_name']?></span></p>
        </div>

        <div class="field"><!-- email  -->
            <p>Ваша электронная почта: <span><?=$user['email']?></span></p>
        </div>

        <div class="field"><!-- Дата регистрации  -->
            <p>Дата регистрации: <span><?=$user['add_date']?></span></p>
        </div>

        <div class="field"><!-- изменение пароля  -->
            <a href="?edit_password">Изменить пароль</a>
            <span><?php echo $_GET['error_edit_password'] ?? '';?></span>

            <?php // отображаем форму для изменения пароля - 1 шаг
                if( isset($_GET['edit_password']) ){ // если нажата ссылка edit_password
                    // показываем форму
                    //d($_GET);
                    echo <<<_HTML_
                        <form action="?" method="POST">
                            <div>
                                <label for="edit_password">Введите новый пароль</label>
                                <input type="password" name="edit_password">
                            </div>
                            <input type="submit" value="Изменить">
                        </form>                 
_HTML_;
                }
            ?>
        </div>



        <a href="exit.php">Выход</a>
        <a href="/">На главную</a>

    </div>
</body>
</html>
