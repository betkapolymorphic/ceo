<?php
/**
 * Created by PhpStorm.
 * User: Alexeev
 * Date: 28-Apr-15
 * Time: 12:01 PM
 */
header('Content-Type: text/html; charset=utf-8');
error_reporting(0);
include_once 'db.php';
$q = getInfoAboutSentence($_GET['id']);

foreach($q as $el){
    echo "<b>".$el['word']."</b> - ";

    foreach($el['info'] as $info){
        echo $info." , ";
    }
    echo "<br>";
}
?>