<?php
include_once 'db.php';
if(!empty($_POST['mask'])) {
    foreach($_POST['mask'] as $check) {
        $i = intval($check);
        $q = "insert into mask_vote VALUES ($i,1) ON DUPLICATE KEY UPDATE count = count+1";
        $res = mysql_query($q);

    }
}



?>