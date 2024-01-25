<?php
require './parts/connect_db.php';

$fdentry_id = isset($_GET['fdentry_id']) ? intval($_GET['fdentry_id']) : 0;
if(! empty($fdentry_id)){
  $sql = "DELETE FROM sports_diary WHERE fdentry_id={$fdentry_id}";
  $pdo->query($sql);
}

$come_from = 'list.php';
if(! empty($_SERVER['HTTP_REFERER'])){
    //如果不是空的 資料
  $come_from = $_SERVER['HTTP_REFERER'];
}

header("Location: $come_from");