<?php

   //htmlspecialcharsをfunction化
   function h($value) {
    return htmlspecialchars($value, ENT_QUOTES);
   }

   //db接続
   function dbconnect() {
      // $db = new mysqli("localhost:8889", "root", "root", "boardsql");
      // $db = new PDO('mysql:dbname=boardsql;host=localhost:8889;charset=utf8', "root", "root");
      $db = new PDO('mysql:dbname=heroku_45b92d6d958d659;host=us-cdbr-east-05.cleardb.net;charset=utf8', "b221d50c2d2141", "759121d3");

      //データベースに接続できなかったら
      if (!$db) {
         die($db->error);
     }

     return $db;

   }
?>