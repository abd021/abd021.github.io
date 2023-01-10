<?php
try {

$host = "localhost";
$dbname = "bbsysdb";
$user = "root";
$pass = "";

  $dbh = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
  $dbh->exec("set names utf8");
  $dbh->query("SET sql_mode = ''");

}
catch(PDOException $e) {
    echo $e->getMessage();
}
?>