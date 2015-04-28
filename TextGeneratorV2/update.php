<?php
/**
 * Created by PhpStorm.
 * User: Alexeev
 * Date: 28-Apr-15
 * Time: 05:35 PM
 */
    include_once 'db.php';
   updateWord($_GET['id'],0,$_GET['p_speech'],$_GET['kind'],$_GET['number'],$_GET['case'],$_GET['naturable']);
    echo "updated";
?>