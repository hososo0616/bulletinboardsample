<?php

   //htmlspecialcharsをfunction化
   function h($value) {
    return htmlspecialchars($value, ENT_QUOTES);
   }

   //db接続
   function dbconnect() {
      // $db = new mysqli("localhost:8889", "root", "root", "boardsql");
      // $db = new PDO('mysql:dbname=boardsql;host=localhost:8889;charset=utf8', "root", "root");
      $db = new PDO('mysql:dbname=heroku_6de2d7937688b8b;host=us-cdbr-east-05.cleardb.net;charset=utf8', "b8d554ea577e0c", "6dd97f31");

      //データベースに接続できなかったら
      if (!$db) {
         die($db->error);
     }

     return $db;

   }
?>