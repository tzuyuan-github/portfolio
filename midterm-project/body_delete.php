<?php
require './parts/connect_db.php';

$bdentry_id = isset($_GET['bdentry_id']) ? intval($_GET['bdentry_id']) : 0;
if(! empty($bdentry_id)){
  $sql = "DELETE FROM body_tracking WHERE bdentry_id={$bdentry_id}";
  $pdo->query($sql);
}

$come_from = 'body_list.php';
if(! empty($_SERVER['HTTP_REFERER'])){
    //如果不是空的 資料
  $come_from = $_SERVER['HTTP_REFERER'];
}

header("Location: $come_from");