<?php

   //htmlspecialcharsをfunction化
   function h($value) {
    return htmlspecialchars($value, ENT_QUOTES);
   }

   //db接続
   function dbconnect() {
      // $db = new mysqli("localhost:8889", "root", "root", "boardsql");
      // $db = new PDO('mysql:dbname=boardsql;host=localhost:8889;charset=utf8', "root", "root");
      $db = new PDO('mysql:dbname=heroku_eedb825a6a04d25;host=us-cdbr-east-05.cleardb.net;charset=utf8', "b6a407877c4b52", "e7051d68");

      //データベースに接続できなかったら
      if (!$db) {
         die($db->error);
     }

     return $db;

   }
?>