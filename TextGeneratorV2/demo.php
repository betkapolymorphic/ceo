<?php
include 'db.php';
set_time_limit (0);
error_reporting(0);

die();

$sql = "SELECT * , COUNT( * )
FROM ceoapp.masks
GROUP BY properties_str
HAVING COUNT( * ) >1
ORDER BY COUNT( * ) DESC
LIMIT 100";

$res = mysql_query($sql);
while($line=mysql_fetch_array($res,MYSQL_ASSOC)){
    $c = $line['count_idential_morph'];
    $sql = "select id_words from sentance where idsentance=$c";
    $res1 = mysql_query($sql);
    if($line1=mysql_fetch_array($res1,MYSQL_ASSOC)){
        $words = preg_split('/,/',$line1['id_words']);

        foreach ($words as $word) {
            $sql  = "select text from word where idword=$word";

            $res2 = mysql_query($sql);
            if($line2 = mysql_fetch_array($res2,MYSQL_ASSOC)){
                echo $line2['text'].' ';
            }
        }
    }
    echo " ->  ";
    echo $line['properties_str']."<br>";

}

?>