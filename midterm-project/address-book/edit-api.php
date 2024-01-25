<?php
require './parts/connect_db.php';

# 告訴用戶端, 資料格式為 JSON
header('Content-Type: application/json');
#echo json_encode($_POST);
#exit; // 結束程式


$output = [
  'postData' => $_POST,
  'success' => false,
  // 'error' => '',
  'errors' => [],
];


// 取得資料的 PK
$sid = isset($_POST['sid']) ? intval($_POST['sid']) : 0;

if (empty($sid)) {
  $output['errors']['sid'] = "沒有 PK";
  echo json_encode($output);
  exit; // 結束程式
}


$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$mobile = $_POST['mobile'] ?? '';
$birthday = $_POST['birthday'] ?? '';
$address = $_POST['address'] ?? '';



// TODO: 資料在寫入之前, 要檢查格式

// trim(): 去除頭尾的空白
// strlen(): 查看字串的長度
// mb_strlen(): 查看中文字串的長度

$isPass = true;
if (empty($name)) {
  $isPass = false;
  $output['errors']['name'] = '請填寫正確的姓名';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  $isPass = false;
  $output['errors']['email'] = 'email 格式錯誤';
}

# 如果沒有通過檢查
if (!$isPass) {
  echo json_encode($output);
  exit;
}

$sql = "UPDATE `address_book` SET 
  `name`=?,
  `email`=?,
  `mobile`=?,
  `birthday`=?,
  `address`=?
WHERE `sid`=? ";

$stmt = $pdo->prepare($sql);

$stmt->execute([
  $name,
  $email,
  $mobile,
  $birthday,
  $address,
  $sid
]);

$output['rowCount'] = $stmt->rowCount();
$output['success'] = boolval($stmt->rowCount());
echo json_encode($output);
