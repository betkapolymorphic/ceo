<?php
/**
 * Created by PhpStorm.
 * User: Alexeev
 * Date: 24-Apr-15
 * Time: 07:01 PM
 */
set_time_limit (0);
error_reporting(0);
// Подключите файл common.php. phpmorphy-0.3.2 - для версии 0.3.2,
// если используется иная версия исправьте код.
require_once( './phpmorphy-0.3.7/src/common.php');
$dir = 'Z:\home\color.com\www\TextGeneratorV2\phpmorphy-0.3.7\dicts';

$lang = 'ru_RU';
include_once('parseWord.php');
include_once('db.php');


// Укажите опции
// Список поддерживаемых опций см. ниже
$opts = array(
    'storage' => PHPMORPHY_STORAGE_FILE,
    'predict_by_suffix' => false,
    'predict_by_db' => false
);
$morphy = new phpMorphy($dir, $lang, $opts);



// создаем экземпляр класса phpMorphy
// обратите внимание: все функции phpMorphy являются throwable т.е.
// могут возбуждать исключения типа phpMorphy_Exception (конструктор тоже)
try {

    $myfile = fopen("1.csv", "r") or die("Unable to open file!");
// Output one line until end-of-file
    $flag = false;
    $i = 0;
    while(!feof($myfile)) {

        $str = fgets($myfile);

       $s = (split('"',$str));



    $morphy = new phpMorphy($dir, $lang, $opts);





    $ids = "";
    $ar = array();
    $flag = false;
    $bad = false;

    $s = split(' ',$s[3]);

    foreach($s as $word){
        if (!preg_match('/[^A-Za-z0-9]/', $word)) // '/[^a-z\d]/i' should also work.
        {
            $flag = true;
            break;
        }
        $y = parse($morphy,mb_strtoupper($word, "utf-8"));
        if($y==null){
            $bad = true;
        }




       array_push($ar,$y);


    }
        if($flag){
            continue;
        }



    for($i = 0;$i<count($ar);$i++){
       $ids.=updateWordInDB($s[$i],$ar[$i]).",";
    }

    updateSentance($ids,$bad);

    }
    fclose($myfile);

} catch(phpMorphy_Exception $e) {
    die('Error occured while creating phpMorphy instance: ' . $e->getMessage());
}

?>