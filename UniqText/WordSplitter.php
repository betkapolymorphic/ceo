<?php
    function splitWord($text){
        //$result = preg_split('/((^\p{P}+)|(\p{P}*\s+\p{P}*)|(\p{P}+$))/', $text, -1, PREG_SPLIT_NO_EMPTY);
        //return $result;
        $result = array();
        $delim = ' \n\t,.!?:;-\"';

        $tok = strtok($text, $delim);

        while ($tok !== false) {
            array_push($result,$tok);
            //echo "Word=$tok<br />";
            $tok = strtok($delim);
        }
        return $result;
    }
?>