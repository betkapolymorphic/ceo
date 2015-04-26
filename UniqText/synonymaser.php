
<?php
include_once('db.php');
    function getSynonyms($text)
    {
        $query = "SELECT w.word from
                (SELECT * from(SELECT * FROM words
                where lower(word) like lower('$text')) t
                left join
                synonyms s
                on s.w_id = t.id) t
                inner join
                words w
                on w.id = t.s_id

                order by rand()
                ";
        $result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());;

        $array = array();
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
            array_push($array,str_replace(")","",str_replace("(","",$line['word'])));
        }
        return $array;
    }
?>