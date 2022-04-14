<?php

   //htmlspecialcharsをfunction化
   function h($value) {
    return htmlspecialchars($value, ENT_QUOTES);
   }

   //db接続
   function dbconnect() {
      // $db = new mysqli("localhost:8889", "root", "root", "boardsql");
      $db = new PDO('mysql:dbname=boardsql;host=localhost:8889;charset=utf8', "root", "root");

      //データベースに接続できなかったら
      if (!$db) {
         die($db->error);
     }

     return $db;

   }
?>