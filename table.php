<?
  if($_SERVER['REQUEST_METHOD'] == 'POST') {

    $cols = abs((int) $_POST['cols']);
    $rows = abs((int) $_POST['rows']);
    $color = trim(strip_tags($_POST['color']));

  }

  $cols = $cols ? $cols : 10;
  $rows = $rows ? $rows : 10;
  $color = $color ? $color : 'yellow';

?>

  <!-- Область основного контента -->

  <form action='<?=$_SERVER['REQUEST_URI']?>' method='POST'>
    <label>Количество колонок: </label>
    <br />
    <input name='cols' type='text' value="<?=$cols?>" />
    <br />
    <label>Количество строк: </label>
    <br />
    <input name='rows' type='text' value="<?=$rows?>" />
    <br />
    <label>Цвет: </label>
    <br />
    <input name='color' type='text' value="<?=$color?>" />
    <br />
    <br />
    <input type='submit' value='Создать' />
    <br />
  </form><br>

  <!-- Таблица -->

  <? 
    drawTable($cols, $rows, $color);
  ?>
  
  <!-- Таблица -->
  <!-- Область основного контента -->
  