<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>


<body>

<?php
/**
 * Created by PhpStorm.
 * User: Alexeev
 * Date: 29-Apr-15
 * Time: 05:57 PM
 */

include_once 'db.php';
header('Content-Type: text/html; charset=utf-8');



function getSentance($mask,$id)
{
    $tor = "";
    $str = $mask;
    $sentance = $id;

    $masks = preg_split("/\\|/",$str);
    $count = -1;

    $prev_word = "";
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
                        for(;$count<count($A);$count++){
                            $ps = getParthOfSpeech($A[$count]);
                            if($ps=="16"){
                                $q = "select word.text from word where idword=".$A[$count];
                                $res = mysql_query($q);
                                if($res){
                                    if($line = mysql_fetch_array($res,MYSQL_ASSOC)){
                                        $tor.=$line['text']." ";

                                        $prev_word  =$line['text'];
                                    }
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
            ";

            $res =  mysql_query($q) or die(mysql_error());


            if($res){
                $last_seen_word = "";
                $flag = false;
                if($prev_word!="")
                    $prev_next_arr = getBeforeAfter($prev_word);
                while($line = mysql_fetch_array($res,MYSQL_ASSOC)){
                    $last_seen_word=$line['text'];

                    if($prev_word==""){
                        $tor.=$line['text']." ";
                        $prev_word  =$line['text'];
                        $flag = true;
                        break;

                    }
                    $w = $line['text'];
                    if(isset($prev_next_arr['after'][$w])){
                        $tor.=$line['text']." ";
                        $prev_word  =$line['text'];
                        $flag = true;
                        break;

                    }


                }
                if(!$flag){
                    $tor.=$last_seen_word." ";
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

$count = 0;
echo '<form method="post" action="bestMaskVote.php">';
$res = mysql_query($q);
while($line = mysql_fetch_array($res,MYSQL_ASSOC)){
    $id = $line['count_idential_morph'];
    $mask = $line['properties_str'];
    echo "<input type=\"checkbox\" name='mask[]$count' value='$count'>".$count++.")".getSentance($mask,$id)."<br>";


}
echo "<input type='submit'>";
echo '</form>';



?>
</body>

</html>

