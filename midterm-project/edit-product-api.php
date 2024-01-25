<?php
require './parts/connect_db.php';

// 告訴用戶端格式為JSON
header('Content-Type: application/json');

// 宣告變數 避免使用者點開此 api 會出現 warning，通常不要直接看api檔案
$output = [
  'postData' => $_POST,
  'success' => false,
  'errors' => [],
];

$sid = isset($_POST['sid']) ? intval($_POST['sid']) : 0;

if (empty($sid)) {
  $output['errors']['sid'] = '沒有PK';
  echo json_encode($output);
  exit;
}


// 資料寫入前要檢查: 除更多圖片其他欄位必填
if (empty($_POST['name']) or empty($_POST['price']) or empty($_POST['cate1']) or empty($_POST['cate2']) or empty($_POST['descriptions'])) {
  $output['errors']['form'] = "缺少欄位資料";
  echo json_encode($output, JSON_UNESCAPED_UNICODE);
  exit;
};

// 避免錯誤訊息：避免沒有值時 warning 訊息跑到 html 標籤中
$name = $_POST['name'] ?? '';
$price = $_POST['price'] ?? '';
$cate1 = $_POST['cate1'] ?? '';
$cate2 = $_POST['cate2'] ?? '';
$descriptions = $_POST['descriptions'] ?? '';
$inventory = $_POST['inventory'] ?? '';
$launch = $_POST['launch'] ?? '';

# 後端檢查
$isPass = true;

//如果庫存量為0,
if (intval($inventory) === 0 && $launch === 1) {
  $isPass = false;
  $output['errors']['launch'] = '您設定的庫存量為0,商品將無法上架';
}

// 如果沒有通過檢查
if (!$isPass) {
  echo json_encode($output, JSON_UNESCAPED_UNICODE);
  exit;
}

# 與資料庫串接 修改資料

$sql = "UPDATE `product_list` SET 
`name`=?,
`price`=?,
`main_category`=?,
`category`=?,
`descriptions`=?,
`inventory`=?,
`launch`=?
WHERE `sid`=? ";


$stmt = $pdo->prepare($sql);

$stmt->execute([
  $name,
  $price,
  $cate1,
  $cate2,
  $descriptions,
  $inventory,
  $launch,
  $sid
]);

$output['success'] = boolval($stmt->rowCount());
echo json_encode($output, JSON_UNESCAPED_UNICODE);