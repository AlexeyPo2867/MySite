<?php
// файл входа пользователя на сайт
// получаем данные, проверяем по бд на соответствие

require 'DbConnect.php';
$pdo = DbConnect::getConnection();

// обработка формы
if( $_SERVER['REQUEST_METHOD'] === "POST" ){// если форма отправлена

  // проверяем данные
  list($errors, $input) = validate_form();

  if($errors){// если есть ошибки
    show_form($errors, $input); // показываем форму снова
  }else{ // если ошибок нет
    process_form($input); // заносим данные в бд, стартуем сессию, записываем в нее данные
  }

}else{ // если стр загружена впервые
  // отображаем форму
  show_form();
}


/**
 * validate_form - проверка данных
 */
function validate_form(){

  // массивы для ошибок и данных
  $errors = [];
  $input = [];
  //d($_POST);

  // экранируем
  $input['login'] = htmlspecialchars(trim( $_POST['login'] ));
  $input['password'] = htmlspecialchars(trim( $_POST['password'] ));

  /**
   * функция проверки логина
   */
  function validate_login($login){
    $reg_exp = "/^[a-z][a-z0-9]{2,}$/i";

    if( strlen($login) === 0 ){
      return 'Введите логин';
    }elseif( !preg_match($reg_exp, $login) ){
      return 'Логин должен содержать только латинские буквы или цифры и должен начинаться с буквы';
    }

    // проверка логина по бд
    $query = "SELECT login FROM users WHERE login = :login";
    $result = $GLOBALS['pdo']->prepare($query);
    $result->bindValue('login', $login, PDO::PARAM_STR);
    $result->execute();

    $rowCount = $result->rowCount(); // получаем кол-во строк при выборку из бд
    // если указанный пользователем логин нашелся в бд - идем дальше проверять пароль

    if( $rowCount === 0 ){// если поиск по бд не дал результатов (строк нет)
      return 'Указанный логин не зарегистрирован';
    }

  }
  $error_login = validate_login($input['login']);
  if($error_login){
    $errors['login'] = $error_login;
  }


  /**
   * функция проверки пароля
   */
  function validate_password($password, $login){
    $reg_exp = "/^.{8,}$/u";

    if(strlen($password) === 0){
      return 'Введите пароль';
    }elseif( !preg_match($reg_exp, $password) ){
      return 'Пароль должен состоять не менее чем из восьми произвольных символов';
    }

    // выборка пароля из бд и его проверка
    // получаем пароль из бд по логину, который представил пользователь при входе
    $query = "SELECT password FROM users WHERE login = ?;";
    $result = $GLOBALS['pdo']->prepare($query);
    $result->execute([$login]);

    $pass_db = $result->fetch();// $pass_db - массив с одним элементом, ключ - password

    // 123456789 === $2y$10$We2kU6ETemFutmLYdqZKiuDx2sj5rYf4H3hhBRCi.RtfkbJQSh4t
    $hash = password_verify($password, $pass_db['password']);

    if(!$hash){ // если пароли НЕ совпадают
      return 'Пароль неверен';
    }
  }
  $error_password = validate_password($input['password'], $input['login']);
  if($error_password){
    $errors['password'] = $error_password;
  }


  return [$errors, $input];
}


/**
 * process_form - старт сессии
 */
function process_form($input){

  // старт сессии
  session_start();
  // записываем логин в сессию
  $_SESSION['valid_user'] = $input['login'];
  header('Location: /');
}


/**
 * show_form - отображение формы регистрации
 */
function show_form($errors = [], $input = []){

  // массив с полями
  $fields = ['login','password'];

  foreach ($fields as $field) {// перебираем массив с полями
    if( !isset($errors[$field]) ) $errors[$field] = '';// если элемент с указанным полем отсутствует, присваиваем
    // пустую строку
    if( !isset($input[$field]) ) $input[$field] = '';
  }

  echo <<<_HTML_
    <!doctype html>
    <html>
    <head>
      <meta charset="UTF-8">
      <title>Вход</title>
      <link rel="stylesheet" href="style/style.css">
    </head>
    <body>
        <div class="main">
            <form action="" method="POST">
                <h2>Вход</h2>
                
                <div>
                  <label for="login">Логин:</label>
                  <input type="text" name="login" placeholder="Только латинские буквы и цифры" size="28" value="$input[login]">
                  <span>$errors[login]</span>
                </div>
                
                <div>
                    <label for="password">Пароль:</label>
                    <input type="password" name="password" placeholder="От восьми произвольных символов" size="30" 
                    value="$input[password]">
                    <span>$errors[password]</span>
                </div>                 
            
                <input type="submit" value="Отправить данные">
            </form>
            <a href="register.php">Регистрация</a>
        </div>
    </body>
    </html>
_HTML_;

}