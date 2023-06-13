<? 
  //отрисовка таблицы
  function drawTable($cols = 10, $rows = 10, $color = 'yellow') {
    //static $x = 0;
    //$x++;
    //echo "Таблица рисуется {$x}-й раз";
    echo "<table border='1'>";
    for($tr = 1; $tr <= $rows; $tr++) {
      echo "<tr>";
      for($td = 1; $td <= $cols; $td++) {
        if($tr == 1 || $td == 1)
          echo "<th style='background: $color'>" . $tr * $td . "</th>";
        else
          echo "<td>" . $tr * $td . "</td>";
      }
      echo "</tr>";
    }
    echo "</table>";
  }

  // функция обработок ошибок, $no-номер ошибок, $msg-сообщение об ошибке, $file- в каком файле, $line- на какой линии
  function myError($no, $msg, $file, $line) {
    $dt = date("d-m-Y H:i:s");
    $str = "[$dt] - $msg in $file:$line\n";
    switch($no) {
      case E_USER_ERROR:
      case E_USER_WARNING:
      case E_USER_NOTICE:
        echo $msg;
    }
    //error_log($str, 3, "error.log");
  }
  // меню


  function drawMenu($menu, $vertical = true) {

    if(!is_array($menu)) return false;
    
    $style = "";

    if(!$vertical) $style = " style='display:inline; margin-right:15px'";

    echo "<ul>";

    foreach ($menu as $item) {
      echo "<li$style>";
      echo "<a href='{$item['href']}'>{$item['link']}</a>";
      echo "</li>";
    }

    echo "</ul>";
    return true;

  }
