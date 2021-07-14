<?php
 define("MYSQL_HOST","127.0.0.1");
 define("MYSQL_USER","College");
 define("MYSQL_PASSWORD","12345678");
 define("MYSQL_DB","Insta_attendance");

 $options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES => false
 ];
 $conn = new PDO("mysql:host=" . MYSQL_HOST . ";dbname="
         . MYSQL_DB,MYSQL_USER,MYSQL_PASSWORD,$options);
?>
