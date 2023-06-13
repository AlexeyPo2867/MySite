<?php
// регистрация, проверка данных пользователя, внесение данных в бд,
// старт сессии, внесение в нее данных о пользователе
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
 * функции для работы с данными пользователя
 */


/**
 * validate_form - проверка данных
 */
function validate_form(){

  // массивы для ошибок и данных
  $errors = [];
  $input = [];

  // 'first_name', 'last_name', 'login', 'email', 'password', 'image'
  // экранируем данные
  $input['first_name'] = htmlspecialchars(trim( $_POST['first_name'] ));
  $input['last_name'] = htmlspecialchars(trim( $_POST['last_name'] ));
  $input['login'] = htmlspecialchars(trim( $_POST['login'] ));
  $input['email'] = htmlspecialchars(trim( $_POST['email'] ));
  $input['password'] = htmlspecialchars(trim( $_POST['password'] ));
  $input['image'] = $_FILES['image'];
  //d($input);

  /**
   * проверка имени
   */
  function validate_first_name($first_name){
    $reg_exp = "/^[а-яё]{2,}$/iu";

    if(empty($first_name)){
      return 'Введите имя';
    }elseif( !preg_match($reg_exp, $first_name) ){
      return 'Имя должно состоять не менее чем из двух русских букв';
    }
  }
  $error_first_name = validate_first_name($input['first_name']);
  if($error_first_name){
    $errors['first_name'] = $error_first_name;
  }

  /**
   * проверка фамилии
   */
  function validate_last_name($last_name){
    $reg_exp = "/^[а-яё]{2,}$/iu";

    if(empty($last_name)){
      return 'Введите фамилию';
    }elseif( !preg_match($reg_exp, $last_name) ){
      return 'Фамилия должна состоять не менее чем из двух русских букв';
    }
  }
  $error_last_name = validate_last_name($input['last_name']);
  if($error_last_name){
    $errors['last_name'] = $error_last_name;
  }

  /**
   * проверка логина
   */
  function validate_login($login){
    $reg_exp = "/^[a-z][a-z0-9]{2,}$/i";

    if( strlen($login) === 0 ){
      return 'Введите логин';
    }elseif( !preg_match($reg_exp, $login) ){
      return 'Логин должен содержать только латинские буквы или цифры и должен начинаться с буквы';
    }

    // проверка логина по бд на уникальность
    $query = "SELECT login FROM users WHERE login = ?";
    $result = $GLOBALS['pdo']->prepare($query);
    $result->execute([$login]);
    $rouCount = $result->rowCount();// получаем кол-во полученных строк из бд  - целое  число

    if( $rouCount > 0 ){// если есть хотя бы одна строка
      return 'Такой логин уже занят';
    }

  }
  $error_login = validate_login($input['login']);
  if($error_login){
    $errors['login'] = $error_login;
  }

  /**
   * проверка электронной почты
   */
  function validate_email($email){
    $reg_exp = "/@/";

    if( strlen($email) === 0 ){
      return 'Введите адрес электронной почты';
    }elseif( !preg_match($reg_exp, $email) ){
      return 'Адрес электронной почты введен в неверном формате';
    }

    // проверка емейла на уникальность
    $query = "SELECT email FROM users WHERE email = ?";
    $result = $GLOBALS['pdo']->prepare($query);
    $result->execute([$email]);
    $rouCount = $result->rowCount();// получаем кол-во полученных строк из бд  - целое  число

    if( $rouCount > 0 ){// если есть хотя бы одна строка
      return 'Такой адрес электронной почты уже зарегистрирован';
    }
  }
  $error_email = validate_email($input['email']);
  if($error_email){
    $errors['email'] = $error_email;
  }

  /**
   * проверка пароля
   */
  function validate_password($password){
    $reg_exp = "/^.{8,}$/u";

    if(strlen($password) === 0){
      return 'Введите пароль';
    }elseif( !preg_match($reg_exp, $password) ){
      return 'Пароль должен состоять не менее чем из восьми произвольных символов';
    }
  }

  $error_password = validate_password($input['password']);
  if($error_password){
    $errors['password'] = $error_password;
  }
// Почему ругается на функцию strlen?
  // Fatal error: Uncaught TypeError: strlen(): Argument #1 ($str) must be of type string, array given in
  /**
   * проверка картинки
   */
  function validate_image($image){

    if( empty($image['name']) ){
      return 'Фото не выбрано';
    }elseif( $image['size'] > 500000 ){
      return 'Размер фото должен быть не более 500кб';
    }elseif( $image['type'] !== 'image/jpeg' ){
      return 'Вы можете загружать картинки только в формате jpeg';
    }elseif( $image['error'] !== 0 ){
      return 'При загрузке файла произошла ошибка';
    }

  }
  $error_image = validate_image($input['image']);
  if($error_image){
    $errors['image'] = $error_image;
  }

 return [$errors, $input];
} // validate_form()


/**
 * process_form - добавление данных в бд, старт сессии
 *  'first_name', 'last_name', 'login', 'email', 'password', 'image'
 */
function process_form($input){
  // шифрование пароля
  $input['password'] = password_hash($input['password'], PASSWORD_DEFAULT);

  // перемещение картинки
  $input['image']['name'] = 'images/'.$input['image']['size'].'_'.$input['image']['name'];
  move_uploaded_file($input['image']['tmp_name'], $input['image']['name']);

  // добавление всех данных в бд
  $query = "INSERT INTO users (login, first_name, last_name, email, password, image)
                            VALUES(?, ?, ?, ?, ?, ?)";
  $result = $GLOBALS['pdo']->prepare($query);
  $result->execute([
    $input['login'],
    $input['first_name'],
    $input['last_name'],
    $input['email'],
    $input['password'],
    $input['image']['name']
    ]);


  // старт сессии, добавление данных в сессию
  session_start();
  $_SESSION['valid_user'] = $input['login'];
  $_SESSION['first_name'] = $input['first_name'];
  $_SESSION['last_name'] = $input['last_name'];

  header('Location: /');
}

/**
 * show_form - отображение формы регистрации
 */
function show_form($errors = [], $input = []){

  // массив с полями
  $fields = ['first_name', 'last_name', 'login', 'email', 'password', 'image'];

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
      <title>Регистрация</title>
      <link rel="stylesheet" href="registr/style/style.css">
    </head>
    <body>
        <div class="main">
        <form action="" method="POST" enctype="multipart/form-data">
            <h2>Регистрация</h2>
            <div>
                <label for="first_name">Имя:</label>
                <input type="text" name="first_name" placeholder="Только русские буквы" value="$input[first_name]">
                <span>$errors[first_name]</span>
            </div>
            <div>
                <label for="last_name">Фамилия:</label>
                <input type="text" name="last_name" placeholder="Только русские буквы" value="$input[last_name]">
                <span>$errors[last_name]</span>
            </div>
            <div>
                <label for="login">Логин:</label>
                <input type="text" name="login" placeholder="Только латинские буквы и цифры" size="28" value="$input[login]">
                <span>$errors[login]</span>
            </div>  
            <div>
                <label for="email">Электронная почта:</label>
                <input type="email" name="email" value="$input[email]">
                <span>$errors[email]</span>
            </div>   
            <div>
                <label for="password">Пароль:</label>
                <input type="password" name="password" placeholder="От восьми произвольных символов" size="28" value="$input[password]">
                <span>$errors[password]</span>
            </div>  
            <div>
                <label for="image">Картинка профиля:</label>
                <input type="file" name="image">
                <span>$errors[image]</span>
            </div>  
            
            <input type="submit" value="Отправить данные">                                           
            
        </form>
        <a href="enter.php">Вход</a>
        </div>
    </body>
    </html>
_HTML_;

}

