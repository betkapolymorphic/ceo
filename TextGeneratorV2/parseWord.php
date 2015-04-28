<?php
/**
 * Created by PhpStorm.
 * User: Alexeev
 * Date: 26-Apr-15
 * Time: 05:03 PM
 */

function parse($morphy,$word){
    $c = new stdClass();
    $info = "";
    $ar = $morphy->getAllFormsWithGramInfo($word);

    if($ar === false){
        return null;
    }
    if(false === ($paradigms = $morphy->findWord($word))) {
        return null;
    }


    for($i=0;$i<count($ar) && $info=="";$i++){
        $cur_form = $ar[$i]["forms"];
        for($j=0;$j<count($cur_form);$j++){
            $form  =$cur_form[$j];
            if($form==$word){
                $info = split(',',$ar[$i]["all"][$j]);
              //  $c->part =
                $part_of_speech = split(' ',$ar[$i]["all"][$j]);
                if(count($part_of_speech)>0){
                    $c->partofspeech = $part_of_speech[0];
                }
                break;
            }
        }
    }

    $kind =array('МР','ЖР','СР','МР-ЖР');
    $number = array('ЕД','МН');
    $case=array('ИМ','РД','ДТ','ВН','ТВ','ПР','ЗВ','2');
    $type  =array('СВ','НС');
    $naturable  =array('ОД','НО');
    $c->kind = parseMorphyInfo($c,$kind,$info);
    $c->number = parseMorphyInfo($c,$number,$info);
    $c->case = parseMorphyInfo($c,$case,$info);
    $c->type = parseMorphyInfo($c,$type,$info);
    $c->naturable = parseMorphyInfo($c,$naturable,$info);

    return $c;
}
?>