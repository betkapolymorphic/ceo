<?php
/**
 * Created by PhpStorm.
 * User: Alexeev
 * Date: 28-Apr-15
 * Time: 06:42 PM
 */

include_once 'db.php';
set_time_limit (0);
//$words = getTable("words");
$sentances = getAnalyzeSentance();
//$w_property = getTable("word_propertie_relat");


$arr = array();
echo "loaded<br>";

$p  =0;
foreach($sentances  as $id=>$sentance){

   if($p++>20000){
       break;
   }
    ///$c_words = preg_split('/,/',$sentance['id_words']);
    $q = "SELECT id_propertie,group_concat(id_propertie order by id_propertie) as gi FROM ceoapp.word_propertie_relat where id_word in(".trim($sentance['id_words'],',').") group by id_word";
    $res = mysql_query($q);
    $str = "";
    while($line = mysql_fetch_array($res,MYSQL_ASSOC)){
        $str.=$line['gi'].'|';
    }
    /*if(!isset($arr[$str])){
        $arr[$str]=0;
    }
    $arr[$str]++;*/
    $sql = "insert into masks VALUES (null,'$str',$id)";
    mysql_query($sql);

    $sql = "insert into analized_sentance values(null,$id)";
    mysql_query($sql);
}


asort($arr);
$i=0;
foreach($arr as $k=>$v){
    if($i++ >10){
        die();
    }
    echo $k."  =>  ".$v."<br>";
}
echo count($arr);
?>
