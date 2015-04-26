<?php
include_once('simple_html_dom.php');
include_once('db.php');
include_once('MoreWordForm.php');
$GLOBALS['STOP_WORDS'] = null;
function isStopWord($word)
{
    if($GLOBALS['STOP_WORDS']==null){
        $GLOBALS['STOP_WORDS'] = array();
        $query = "select * from stop_words";
        $result = mysql_query($query);
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            /*$morphs = getMorph($line['word']);
            foreach($morphs as $morph){
                array_push($GLOBALS['STOP_WORDS'],
                    mb_strtolower($morph,"utf-8"));
            }*/
            array_push($GLOBALS['STOP_WORDS'],
                mb_strtolower($line['word'],"utf-8"));
        }
    }
    return in_array(mb_strtolower($word,"utf-8"),$GLOBALS['STOP_WORDS']);
}
/*
$html = file_get_html('http://1y.ru/stop-slova.php');
foreach($html->find('p[class=stop-slova-p]') as $element) {
    $word = $element->innertext;
    $query = "insert into stop_words VALUES (DEFAULT ,'$word');";
    echo $query."<br>";
    //try{mysql_query($query);}
    //catch(Exception $e){}

}*/

?>