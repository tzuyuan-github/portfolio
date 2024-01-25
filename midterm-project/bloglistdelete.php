<?php
require './parts/connect_db.php';

$BlogArticle_ID = isset($_GET['BlogArticle_ID']) ? intval($_GET['BlogArticle_ID']) : 0;
if(! empty($BlogArticle_ID)){
  $sql = "DELETE FROM bloglist WHERE BlogArticle_ID={$BlogArticle_ID}";
  $pdo->query($sql);
}

$come_from = 'bloglist.php';
if(! empty($_SERVER['HTTP_REFERER'])){
    //如果不是空的 資料
  $come_from = $_SERVER['HTTP_REFERER'];
}

header("Location: $come_from");