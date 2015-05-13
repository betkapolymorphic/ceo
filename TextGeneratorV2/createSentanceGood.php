<?php
/**
 * Created by PhpStorm.
 * User: Alexeev
 * Date: 12-May-15
 * Time: 09:13 AM
 */
include_once 'db.php';

    $str = "2,22,28,36|259,2296|16|2,23,29,36|1,23,32,36|";
$masks = preg_split("/\\|/",$str);
$dict = array();

$count = 0;
foreach($masks as $mask){
    $arr = array();
    if(/*is_numeric(substr($mask,0,1))*/$count++!=1){

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


        if($res){

            while($line = mysql_fetch_array($res,MYSQL_ASSOC)){
                array_push($arr,$line['id_word']);
            }
            array_push($dict,$arr);
        }
    }else{
        $words = preg_split('/\\,/',$mask);
        foreach($words as $w){

            array_push($arr,$w);

        }
        array_push($dict,$arr);
    }
}
$ARR = array();
for($i=0;$i<count($dict);$i++){
    array_push($ARR,array());
}

/*
for($i=0;$i<count($dict);$i++){
    for($j=0;$j<count($dict[$i]);$j++){
        $bfore_aftr = getBeforeAfter($dict[$i][$j]);
        if($i>0){
            $bfore = $bfore_aftr['before'];
        }
    }
}*/
$out = array();
$size = count($dict);
function brute($cur_word_index,$prev_word_index,$size,$step,$dict,$out){

    if($step >= 4){
        foreach($out as $o){
            echo $o." ";
        }

        die('~');
    }

    echo '`begin`<br>';
    echo "`$step`<br>";
    echo "count = `".count($dict[$step+1])."`<br>";
    echo "word = `".$dict[$step+1][$cur_word_index]."<br>";
    $bfr_aftr = getBeforeAfter($dict[$step][$cur_word_index]);
    $bfr = $bfr_aftr['before'];
    $aftr = $bfr_aftr['after'];
    die(print_r($bfr));
    for($i=0;$i<count($dict[$step+1]);$i++) {
        /*if($step==0){
            echo "`$step`<br>";
            array_push($out,$dict[$step+1][$i]);
            echo $dict[$step+1][$i]." -> $step ->".implode(',',$out)."<br>";
            brute($i,$cur_word_index,$size,$step+1,$dict,$out);
        }else if(isset($aftr[$dict[$step+1][$i].'']) ){

           // if($step>0 &&  !isset($bfr[$dict[$step-1][$prev_word_index].''])){
           //     continue;
          //  }


            array_push($out,$dict[$step+1][$i]);
            echo $dict[$step+1][$i]." -> $step".implode(',',$out)."<br>";
            brute($i,$cur_word_index,$size,$step+1,$dict,$out);
        }*/
        if($step==0){

            array_push($out,$dict[$step+1][$i]);
          //  echo $dict[$step+1][$i]." -> $step ->".implode(',',$out)."<br>";
            brute($i,$cur_word_index,$size,$step+1,$dict,$out);
        }elseif(isset($aftr[$dict[$step+1][$i].'']) ){
            echo $dict[$step+1][$i]." -> $step".implode(',',$out)."<br>";
            brute($i,$cur_word_index,$size,$step+1,$dict,$out);
        }
     //   brute($i,$cur_word_index,$size,$step+1,$dict,$out);
    }
    echo '`return`<br>';
    array_pop($out);

}
for($i=0;$i<count($dict[0]);$i++){
    brute(0,$i,$size,0,$dict,$out);
}


?>