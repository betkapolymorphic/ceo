<?php

if(!isset($_GET['text']))
    die('asd');

require_once( './phpmorphy-0.3.7/src/common.php');

$dir = 'Z:\home\color.com\www\TextGeneratorV2\phpmorphy-0.3.7\dicts';

include_once('parseWord.php');
include_once('db.php');
$opts = array(
    'storage' => PHPMORPHY_STORAGE_FILE,
);
$lang = 'ru_RU';

$ar = array();
$morphy = new phpMorphy($dir, $lang, $opts);
$words = preg_split('/ /',$_GET['text']);
foreach($words as $word){

    $properties= parse($morphy,mb_strtoupper($word, "utf-8"));
    /*echo $c->partofspeech.",";
    echo  $c->kind."," ;
   echo $c->number.",";
   echo $c->case.",";
   echo $c->naturable."|";
    */
    $sql =  "select   (select idword_propertie from word_propertie where word_propertie.key like 'kind' and val  like '$properties->kind'),
     (select idword_propertie from word_propertie where word_propertie.key like 'number' and val  like '$properties->number'),
      (select idword_propertie from word_propertie where word_propertie.key like 'case' and val  like '$properties->case'),
      (select idword_propertie from word_propertie where word_propertie.key like 'naturable' and val  like '$properties->naturable'),
      (select idword_propertie from word_propertie where word_propertie.key like 'p_speech' and val  like '$properties->partofspeech')";

    $res = mysql_query($sql);
    if($line = mysql_fetch_array($res,MYSQL_ASSOC)){
        $ss = "";
        foreach($line as $k=>$v){
            if($v)
            $ss.= $v.',';
        }

        $ss = trim($ss,',');
        echo $ss.'|';
    }
}
?>