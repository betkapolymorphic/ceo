<?php
/*
$hostname = "master038.unihost.com";
$username = "test_login";
$password = "test test test";
$dbName = "testdatabase";*/
$hostname = "localhost";
$username = "root";
$password = "";
$dbName = "ceoapp";
mysql_connect($hostname, $username, $password) OR DIE("Не могу создать соединение ");
mysql_select_db($dbName) or die(mysql_error());
mysql_set_charset('utf8');

function updateWordInDB($word,$properties){

    $q = "select idword  from word where text like '$word'";



    $id_word  = 1;
   $id = mysql_result(mysql_query($q),0);
    if($id){
       $id_word = $id;
    }else {
        $q = "insert into word VALUE (null,'$word')";
        mysql_query($q);
        $id_word = mysql_insert_id();
        $q = "insert into word_propertie_relat value(null,$id_word,
      (select idword_propertie from word_propertie where word_propertie.key like 'kind' and val  like '$properties->kind'))";
        mysql_query($q);

        $q = "insert into word_propertie_relat value(null,$id_word,
      (select idword_propertie from word_propertie where word_propertie.key like 'number' and val  like '$properties->number'))";
        mysql_query($q);

        $q = "insert into word_propertie_relat value(null,$id_word,
      (select idword_propertie from word_propertie where word_propertie.key like 'case' and val  like '$properties->case'))";
        mysql_query($q);

        $q = "insert into word_propertie_relat value(null,$id_word,
      (select idword_propertie from word_propertie where word_propertie.key like 'naturable' and val  like '$properties->naturable'))";
        mysql_query($q);

        $q = "insert into word_propertie_relat value(null,$id_word,
      (select idword_propertie from word_propertie where word_propertie.key like 'p_speech' and val  like '$properties->partofspeech'))";
        mysql_query($q);



    }
    return $id_word;
}
function updateSentance($word_ids){


        $q = "insert into sentance value(null,'$word_ids')";
//        echo $q;
    mysql_query($q);
}
function getInfoAboutSentence($id)
{
    $q = "select id_words from sentance where idsentance=$id";
    $res = mysql_query($q);

    if($res){
        $ar = array();

        if($line = mysql_fetch_array($res, MYSQL_ASSOC)){
            $id_s = split(",",$line["id_words"]);

            foreach($id_s as $id){
                $q = "select * from
                    (
                    select word.text as t,id_propertie,idword from word
                    left join word_propertie_relat
                    on  word_propertie_relat.id_word = word.idword
                     where idword=$id
                    )q
                    left join word_propertie
                    on q.id_propertie = idword_propertie";
                $res = mysql_query($q);
                $ar[$id.""]=array();
                $ar[$id.""]["info"]=array();
                $flag = false;
                if($res){

                     while($line = mysql_fetch_array($res, MYSQL_ASSOC)){
                         if(!$flag){
                             $flag = true;
                             $ar[$id.""]["word"]=$line['t'];
                         }
                         array_push($ar[$id.""]["info"],$line['key']."=".$line['val']);
                     }
                }
            }



        }
        return $ar;




    }else{
        return array();
    }
}
function getAllSentence()
{
    $q = "SELECT * FROM sentance";
    $res = mysql_query($q);
    $arr  = array();
    while ($line = mysql_fetch_array($res, MYSQL_ASSOC)) {
       // array_push($arr,trim($line['id_words'],','));


        echo "<a href='info.php?id=".$line['idsentance']."' target='_blank'>";
        $q = "SELECT * FROM ceoapp.word where idword IN (".trim($line['id_words'],',').")";
        $res1 = mysql_query($q);
        while ($line1 = mysql_fetch_array($res1, MYSQL_ASSOC)) {
            echo $line1['text']." ";
        }
        echo "</a><br>";

    }
    /*foreach($arr as $a){

    }*/


}


?>