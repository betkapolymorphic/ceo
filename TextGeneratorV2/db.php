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
    $q = "select id_words where idsentance=$id";
    $res = mysql_query($q);
    if($res){
        $ar = array();
        if($line = mysql_fetch_array($res, MYSQL_ASSOC)){
            $id_s = split(",",$line["id_words"]);

            foreach($id_s as $id){
                $q = "select word_propertie.key as k,val from word_propertie_relat
                    left join word_propertie
                    on  word_propertie.idword_propertie = id_propertie
                     where id_word=$id";
                $res = mysql_query($q);
                $ar[$id.""]=array();
                if($res){
                     while($line = mysql_fetch_array($res, MYSQL_ASSOC)){
                         array_push($ar[$id.""],$line['k']." => ".$line['val']);
                     }
                }
            }



        }
        return $ar;




    }else{
        return array();
    }
}



?>