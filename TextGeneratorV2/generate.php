<?php
/**
 * Created by PhpStorm.
 * User: Alexeev
 * Date: 24-Apr-15
 * Time: 07:01 PM
 */

// Подключите файл common.php. phpmorphy-0.3.2 - для версии 0.3.2,
// если используется иная версия исправьте код.
require_once( './phpmorphy-0.3.7/src/common.php');
$dir = 'Z:\home\color.com\www\TextGeneratorV2\phpmorphy-0.3.7\dicts';
$lang = 'ru_RU';
include_once('parseWord.php');
include_once('db.php');
function parseMorphyInfo($out,$array,$info){
    foreach($array as $k){
        if(in_array($k,$info)){
            return $k;

        }
    }
    return "";
}

// Укажите опции
// Список поддерживаемых опций см. ниже
$opts = array(
    'storage' => PHPMORPHY_STORAGE_FILE,
);

// создаем экземпляр класса phpMorphy
// обратите внимание: все функции phpMorphy являются throwable т.е.
// могут возбуждать исключения типа phpMorphy_Exception (конструктор тоже)
try {


    $morphy = new phpMorphy($dir, $lang, $opts);




    $str = "натянул грязную шлюшку на улице берлина";
    $s = split(" ",$str);
    $ids = "";
    $ar = array();
    foreach($s as $word){

        $y = parse($morphy,mb_strtoupper($word, "utf-8"));

        if($y==null)
            return;

       array_push($ar,$y);

    }

    for($i = 0;$i<count($ar);$i++){
       $ids.=updateWordInDB($s[$i],$ar[$i]).",";
    }
    updateSentance($ids);



} catch(phpMorphy_Exception $e) {
    die('Error occured while creating phpMorphy instance: ' . $e->getMessage());
}

?>