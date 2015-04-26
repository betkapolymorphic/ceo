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
    $c = new stdClass();

    $morphy = new phpMorphy($dir, $lang, $opts);
    $word = 'КОФЕ';
    /*$info->partOfSpeech = $morphy->getPartOfSpeech($word);

    var_dump($info->partOfSpeech);

    if(false === ($paradigms = $morphy->findWord($word))) {
        die('Can`t find word');
    }
    foreach($paradigms as $paradigm) {
        echo 'Все формы: ', implode(',', $paradigm->getAllForms()), PHP_EOL;
        $info->animativation = $paradigm->hasGrammems('ОД');
    }*/

    $info = "";
    $ar = $morphy->getAllFormsWithGramInfo($word);
    for($i=0;$i<count($ar) && $info=="";$i++){
        $cur_form = $ar[$i]["forms"];
        for($j=0;$j<count($cur_form);$j++){
            $form  =$cur_form[$j];
            if($form==$word){
                $info = split(',',$ar[$i]["all"][$j]);

                break;
            }
        }
    }

    $kind =array('МР','ЖР','СР','МР-ЖР');
    $number = array('ЕД','МН');
    $case=array('ИМ','РД','ДТ','ВН','ТВ','ПР','ЗВ','2');
    $type  =array('СВ','НС');
    $naturable  =array('ОД','НО');
    $c->kind = parseMorphyInfo($c,$kind,$info);
    $c->number = parseMorphyInfo($c,$number,$info);
    $c->case = parseMorphyInfo($c,$case,$info);
    $c->type = parseMorphyInfo($c,$type,$info);
    $c->naturable = parseMorphyInfo($c,$naturable,$info);

    var_dump($c);

} catch(phpMorphy_Exception $e) {
    die('Error occured while creating phpMorphy instance: ' . $e->getMessage());
}

?>