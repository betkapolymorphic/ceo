<?php
/**
 * Created by PhpStorm.
 * User: Alexeev
 * Date: 13-May-15
 * Time: 04:33 PM
 */
set_time_limit (0);
ini_set('memory_limit', '2048M');
include_once 'db.php';

function correctSentance($s,$masks)
{

    $glas = array('й','у','е','ы','а','о','э','я','ю');
    $str = "";
    $split= preg_split("/ /",$s);
    for($i=0;$i<count($split)-1;$i++)
    {
        $cur_s = $split[$i];

        if($cur_s=="в" || $cur_s=="с"){
            if(strpos($masks[$i+1],"2,")!==false){
                $count = 0;
                foreach($glas as $g){

                    if(strpos($split[$i+1],$g)!==false){
                        $count++;
                    }
                }
                if($count==2) {
                    $cur_s .= "о";
                }
            }

        }
        $str.=$cur_s." ";
    }
    return $str;

}




$GLOBALS['ignore'] = getIngoreWords();
function ignoreThis($idword){
    return in_array($idword."",$GLOBALS['ignore']);
}
$GLOBALS['nreturn'] = false;
function brute($step,$curIndex,$prevNext,$out,$synonyms,$predlog,$masks)
{


    if($GLOBALS['nreturn']){
        return;
    }
    if($step+1>=count($prevNext)){
        $GLOBALS['nreturn']  =true;
        echo correctSentance(idsToText($out),$masks);


        /*foreach ($synonyms as $i) {
            $tmp=$out[$i];
            foreach($prevNext[$i] as $synonym){
                $out[$i] = $synonym;
              //  echo implode(",",$out);
            }
        }*/


        array_pop($out);
        return;

    }

    $beforeAfter  = getBeforeAfter($prevNext[$step][$curIndex]);
    for($i=0;$i<count($prevNext[$step+1]);$i++)
    {
        $word = $prevNext[$step+1][$i];
        if(isset($predlog[($step+1).""])){
            $word = findWordByText($predlog[($step+1).""]);
        }

        if(ignoreThis($word)){
            continue;
        }
        if(in_array($word,$beforeAfter['after'])){

            array_push($out,$word);
            brute($step+1,$i,$prevNext,$out,$synonyms,$predlog,$masks);
        }

    }
    if(count($out)>0){
        array_pop($out);
    }
}




$masks = preg_split("/\\|/","2,22,28,36|секс,трах|16|2,23,29,36|1,23,32,36|");
$text = "классический секс с небритой киской";
$synonyms = array();
$predlog = array();
$prevNext = array();

for($i=0;$i<count($masks);$i++)
{
    if($masks[$i]=="") continue;
    $al = array();
   // echo substr($masks[$i],0,1).."<br>";
    if(strlen($masks[$i])>0 && preg_match( '#[а-яё]#i',substr($masks[$i],0,1))){

        $split = preg_split("/\\,/",$masks[$i]);
        foreach($split as $str)
        {
            array_push($al,findWordByText($str));
        }


        array_push($synonyms,$i);
    }else{
        if($masks[$i]=="16"){

            $asd = preg_split("/ /",$text);
            $predlog[$i.""] = $asd[$i];


        }
        $al = getWords($masks[$i]);

    }
    array_push($prevNext,$al);
}
//var_dump($prevNext);
$out = array();
array_push($out,$prevNext[0][0]);
brute(0,0,$prevNext,$out,$synonyms,$predlog,$masks);


?>