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
?>