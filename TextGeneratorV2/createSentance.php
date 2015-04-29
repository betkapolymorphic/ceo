<?php
/**
 * Created by PhpStorm.
 * User: Alexeev
 * Date: 29-Apr-15
 * Time: 05:57 PM
 */

include_once 'db.php';


function getSentance($mask,$id)
{
    $tor = "";
    $str = $mask;
    $sentance = $id;

    $masks = preg_split("/\\|/",$str);
    $count = -1;
    foreach($masks as $mask){
        $count++;
        if($mask!=""){

            //IF ПРЕДЛОГ
            if($mask=="16"){
                $q = "select id_words from sentance where idsentance=$sentance";
                $res=mysql_query($q);
                if($res){
                    if($line = mysql_fetch_array($res,MYSQL_ASSOC)){
                        $wordes = $line['id_words'];

                        $A  =preg_split("/,/",$wordes);


                        if($count<count($A)){
                            $q = "select word.text from word where idword=".$A[$count];
                            $res = mysql_query($q);
                            if($res){
                                if($line = mysql_fetch_array($res,MYSQL_ASSOC)){
                                   $tor.=$line['text']." ";
                                }
                            }
                        }
                    }
                }
                continue;
            }
            $q = "select * from
            (
            select * from
            (
            SELECT *,group_concat(id_propertie order by id_propertie) as gr FROM



                ceoapp.word_propertie_relat


            group by(id_word)
            having gr like '$mask'

            )q1
            left join word
            on id_word=idword
                )q
            order by rand()
            limit 1";

            $res =  mysql_query($q) or die(mysql_error());


            if($res){

                if($line = mysql_fetch_array($res,MYSQL_ASSOC)){
                    $tor.=$line['text']." ";

                }
            }
        }


    }
    return $tor;
}


$q = "SELECT * , COUNT( * )
FROM ceoapp.masks
GROUP BY properties_str
HAVING COUNT( * ) >1
ORDER BY COUNT( * ) DESC
LIMIT 20";


$res = mysql_query($q);
while($line = mysql_query($q,MYSQL_ASSOC)){
    $id = $line['count_idential_morph'];
    $mask = $line['properties_str'];
    echo getSentance($mask,$id)."<br>";
    
}



?>