<?php
include_once('db.php');
include_once('WordSplitter.php');
function log_tome($title,$text){
    $query = "Insert into logger_wordfrequency VALUES (DEFAULT ,now(),'$text','$title')";
    mysql_query($query);
}
function getId(){
    $q = "select s.value from settings s where s.kkey like 'parse_storyTable_lastid' ";
    $result = mysql_query($q) or die('Запрос не удался: ' . mysql_error());

    $array = mysql_fetch_array($result);
    if (count($array) == 0) {
        return null;
    }
    return $array[0];
}
$id = getId();
$q = "select s.text from story s where s.id=$id";
$result = mysql_query($q) or die('Запрос не удался: ' . mysql_error());

while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
    echo $line['text'];

}






?>