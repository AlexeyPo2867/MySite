<?

  if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $num1 = (int) $_POST['num1'];
    $num2 = (int) $_POST['num2'];
    $op = trim(strip_tags($_POST['operator']));
  }

  $result = NULL;
  switch($op) {
    case '+':
      $result = $num1 + $num2;
      break;
    case '-':
      $result = $num1 - $num2;
      break;
    case '*':
      $result = $num1 * $num2;
      break;
    case '/':
      if($num2 == 0)
        $result = "Делить на ноль нельзя!";
      else 
        $result = $num1 / $num2;
      break;
    default:
      $result = "Введите целые числа и необходимый оператор";
  }
  echo '<div>
          <h3 style="color:red">Результат операции: '. $result. '</h3>
        </div></br>';
?>

  <!-- Область основного контента -->

  <form action='<?=$_SERVER['REQUEST_URI']?>' method='POST'>
    <label>Число 1:</label>
    <br />
    <input name='num1' type='text' value=""/>
    <br />
    <label>Оператор: </label>
    <br />
    <input name='operator' type='text' value=""/>
    <br />
    <label>Число 2: </label>
    <br />
    <input name='num2' type='text' value=""/>
    <br />
    <br />
    <input type='submit' value='Считать'>
  </form>

  <!-- Область основного контента -->
  