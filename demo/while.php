<?
    $var = 'HELLO';
    $i = 0;
    $len = strlen($var);// один раз посчитали, вместо вызова функции 6 раз в цикле)
    while($i < $len) {
        echo $var[$i++] . '<br>';
        //$i++;
    }
    echo '<hr>';
    $a = 100;
    do {
        echo $a++;
    }
    while($a < 10);
    echo '<hr>';
    $i = 1;
    $j = 1;
    while($j <= 10) {
        while($i <= 10) {
            echo $i++ . '<br>';
            if($i == 5) break 2;
        }
        $j++;
    }