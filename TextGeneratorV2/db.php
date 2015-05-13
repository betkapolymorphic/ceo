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
        if($properties==null){
            $q = "insert into word VALUE (null,'$word',1)";
            mysql_query($q);
            $id_word = mysql_insert_id();
        }else {


            $q = "insert into word VALUE (null,'$word',0)";
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



    }
    return $id_word;
}
function updateSentance($word_ids,$bad){

        if($bad) return;
        $q = "insert into sentance value(null,'$word_ids')";
//
    mysql_query($q);
}

function getBeforeAfter($word)
{
    /*$sql_a  = "(SELECT idword FROM ceoapp.word
        where text like '$word')";
    $a = "";
    $res = mysql_query($sql_a);
    if($line = mysql_fetch_array($res,MYSQL_ASSOC))
        $a = $line['idword'];
    */
    $a = $word;

    $word_middle = ','.$a.',';
    $as = "%$a%";


    $sql = "



        select aftr from
        (
        select idsentance,aftr from
        (
        select *,substring( lastPart.end,1,locate(',',lastPart.end) - 1) as aftr

        from
        (
            select *,
                substring(id_words,locate('$word_middle',id_words)+length($a)+2) as end,
                substring(id_words,1,locate('$word_middle',id_words)-1) as begin from
                (
                    select * from sentance where id_words like '$as'
                ) dataset
        )lastPart

        )bfr_aft_data
        )mma";
   // echo $sql."<br>";

    $bfore_dublicate = array();

    $after_dublicate = array();



    $res = mysql_query($sql) or die(mysql_error());


    while($line = mysql_fetch_array($res,MYSQL_ASSOC)){

        if(!isset($after_dublicate[$line['aftr']])){
            $after_dublicate[$line['aftr']] = true;
           // array_push($after,$line['aftr']);
        }
    }
    $res = array();
    $res['after'] = $after_dublicate;

    return $res;
}
function getWords($mask)
{
    $arr = array();
    $q = "select id_word from
            (
            select * from
            (
            SELECT *,group_concat(id_propertie order by id_propertie) as gr FROM
                ceoapp.word_propertie_relat
            group by(id_word)
            having gr like '$mask'

            )q1
            left join word
            on id_word=idword)q
            order by rand()

            ";
    $res =  mysql_query($q) or die(mysql_error());
$dict = array();

    if($res){

        while($line = mysql_fetch_array($res,MYSQL_ASSOC)){

            array_push($arr,$line['id_word']);
        }
        array_push($dict,$arr);
    }
    return $arr;
}

function findWordByText($text){
    $sql = "select idword from word where text like '$text'";
    $res = mysql_query($sql) or die(mysql_error());


    while($line = mysql_fetch_array($res,MYSQL_ASSOC)){
        return $line['idword'];
    }
    return "0";
}
function getAfter($word)
{
    /*$sql_a  = "(SELECT idword FROM ceoapp.word
        where text like '$word')";
    $a = "";
    $res = mysql_query($sql_a);
    if($line = mysql_fetch_array($res,MYSQL_ASSOC))
        $a = $line['idword'];
    */
    $a = $word;

    $word_middle = ','.$a.',';
    $asa = "%$a%";


    $sql = "select (select text from word where idword=aftr) as aftr,
            (select text from word where idword=bfore) as bfore from
        (
        select idsentance,aftr,bfore from
        (
        select *,substring( lastPart.end,1,locate(',',lastPart.end) - 1) as aftr,
        substring(lastPart.begin,LENGTH(lastPart.begin) - LOCATE(',', REVERSE(lastPart.begin))+2) as bfore

        from
        (
            select *,
                substring(id_words,locate('$word_middle',id_words)+length($a)+2) as end,
                substring(id_words,1,locate('$word_middle',id_words)-1) as begin from
                (
                    select * from sentance where id_words like concat('%,',$a,',%')
                ) dataset
        )lastPart

        )bfr_aft_data
        )mma";

    $bfore_dublicate = array();

    $after_dublicate = array();
    $before = array();
    $after = array();


    $res = mysql_query($sql) or die(mysql_error());


    while($line = mysql_fetch_array($res,MYSQL_ASSOC)){
        if(!isset($bfore_dublicate[$line['bfore']])){
            $bfore_dublicate[$line['bfore']] = true;

        }
        if(!isset($after_dublicate[$line['aftr']])){
            $after_dublicate[$line['aftr']] = true;
        }

        $res['after'] = $after_dublicate;
        $res['before']=$bfore_dublicate;
    }

    return $after_dublicate;
}
function getParthOfSpeech($idword)
{
    if($idword=="")
        return -1;
    $q = "select id_propertie from word_propertie_relat where id_word = $idword";

    $res = mysql_query($q);
    while($line = mysql_fetch_array($res,MYSQL_ASSOC)){

        if(intval($line['id_propertie'])<=21){

            return $line['id_propertie'];
        }
    }
    return -1;
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
    $q = "SELECT * FROM sentance limit 1000";
    $res = mysql_query($q);
    $arr  = array();
    while ($line = mysql_fetch_array($res, MYSQL_ASSOC)) {
       // array_push($arr,trim($line['id_words'],','));


        echo "<a href='info.php?id=".$line['idsentance']."' target='_blank'>";
        $q = "SELECT * FROM ceoapp.word where idword IN (".trim($line['id_words'],',').")
            order by field(idword,".trim($line['id_words'],',').")";
        $res1 = mysql_query($q);
        while ($line1 = mysql_fetch_array($res1, MYSQL_ASSOC)) {
            echo $line1['text']." ";
        }
        echo "</a><br>";

    }

    /*foreach($arr as $a){

    }*/


}
function getBadWords()
{
    $q = "SELECT * FROM ceoapp.word where isbadword=1";
    $res = mysql_query($q);
    $ar = array();

    if($res) {


        while($line = mysql_fetch_array($res, MYSQL_ASSOC)) {
            array_push($ar,$line);

        }
    }
    return $ar;
}
function getPropertie($val){
    $q = "  select * from word_propertie where word_propertie.key like '$val' UNION select '-1','','' ";
    $res = mysql_query($q);
    $ar = array();

    if($res) {
        while($line = mysql_fetch_array($res, MYSQL_ASSOC)) {
            $as = array();
            $as['id']=$line['idword_propertie'];
            $as['prop']=$line['val'];
            array_push($ar,$as);
        }
    }

    return array_reverse($ar);
}
function updateWord($id,$badWord,$ps,$kind,$number,$case,$naturable)
{
    $q = "update word set isbadword=$badWord where idword=$id";
    mysql_query($q);


    $q = "insert into word_propertie_relat values(null,$id,$ps)";
    if($ps!="-1")
    mysql_query($q);


    $q = "insert into word_propertie_relat values(null,$id,$kind)";
    if($kind!="-1")
    mysql_query($q);


    $q = "insert into word_propertie_relat values(null,$id,$number)";
    if($number!="-1")
    mysql_query($q);

    $q = "insert into word_propertie_relat values(null,$id,$case)";
    if($case!="-1")
    mysql_query($q);

    $q = "insert into word_propertie_relat values(null,$id,$naturable)";
    if($naturable!="-1")
    mysql_query($q);

}
function getAnalyzeSentance()
{
    $q = "select * from sentance where idsentance NOT IN(select id_sentance from analized_sentance);";
    mysql_query($q);


    $res = mysql_query($q);
    $arr = array();

    if($res) {
        while ($line = mysql_fetch_array($res, MYSQL_ASSOC)) {

            //$arr['id'.$name]=array();
            $arr[$line['idsentance']]=$line;
            //echo $line[];
        }
    }
    return $arr;
}


?>