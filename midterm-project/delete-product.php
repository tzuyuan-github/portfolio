<?php
require './parts/connect_db.php';

// 因為 product_list.php 裡面 script location 有設定網址會加上sid，所以用get去取得丟到變數裡
$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;

// 判斷有沒有拿到值 // 如果有值就執行以下程式刪除欄位資料，如果沒有值就不執行以下程式(刪除)會直接跳下方程式碼倒回原頁面
if (! empty($sid)) {
  $sql = "DELETE FROM product_list WHERE sid={$sid}";
  $pdo->query($sql);
}

// 若沒有值因為不符合if判斷 就直接執行以下倒回原頁面

$come_from = 'product_list.php';
if (! empty($_SERVER['HTTP_REFERER'])) {
  $come_from = $_SERVER['HTTP_REFERER'];
}
header("Location: $come_from");