<?php
require './parts/connect_db.php';

# 检查是否已登录
// if (isset($_SESSION['admin'])) {
//   $loggedInUser = $_SESSION['admin'];

//   # 获取用户的 ID、昵称等信息
//   $member_id = $loggedInUser['member_id'];
//   $nickname = $loggedInUser['nickname'];
// }

// ini_set('display_errors', 1);
// error_reporting(E_ALL);


$output = [
  'postData' => $_POST,
  'success' => false,
  // 'error' => '',
  'errors' => [],
];


# 告訴用戶端, 資料格式為 JSON
header('Content-Type: application/json');

//判斷是否缺少欄位值  有缺少就顯示缺少  並echo json格式的output欄位

/*
if(empty($_POST['name']) or empty($_POST['email'])){
    $output['errors']['form'] = '缺少欄位資料';
    echo json_encode($output);
    exit;
}
*/



//後端檢查
$member_id = $_POST['member_id'] ?? '';
$height_update = $_POST['height_update'] ?? '';
$weight_update = $_POST['weight_update'] ?? '';
$waistline_update = $_POST['waistline_update'] ?? '';
$bodyfat_update = $_POST['bodyfat_update'] ?? '';
$bmi_update = $_POST['bmi_update'] ?? '';
$bmr_update = $_POST['bmr_update'] ?? '';

// 在后端檢查之前，添加条件检查
if (empty($member_id) || empty($height_update) || empty($weight_update) || empty($waistline_update) || empty($bodyfat_update) || empty($bmi_update) || empty($bmr_update)) {
  $output['errors']['form'] = '缺少必填欄位資料'; // 指示缺少必填字段
  echo json_encode($output);
  exit; // 如果缺少必填字段，停止继续执行
}



// TODO: 資料在寫入之前, 要檢查格式

// trim(): 去除頭尾的空白
// strlen(): 查看字串的長度
// mb_strlen(): 查看中文字串的長度

$isPass = true;

// if (empty($name)) {
//   $isPass = false;
//   $output['errors']['name'] = '請填寫正確的姓名';
// }

// if(! filter_var($email, FILTER_VALIDATE_EMAIL)){
//   $isPass = false;
//   $output['errors']['email'] = 'email 格式錯誤';
// }


# 如果沒有通過檢查
if(! $isPass){
  echo json_encode($output);
  exit;
}


$sql = "INSERT INTO `body_tracking`(
  `member_id`, `height_update`, `weight_update`, `waistline_update`, `bodyfat_update`, `bmi_update`, `bmr_update` ) VALUES (
    ?, ?, ?, ?, ?, ?, ?)";

$stmt = $pdo->prepare($sql);

// $stmt->execute([
//   $_POST['name'],
//   $_POST['email'],
//   $_POST['mobile'],
//   $_POST['birthday'],
//   $_POST['address'],
// ]);

// 下面這些值要灌進資料庫
$stmt->execute([
  $member_id,
  $height_update,
  $weight_update,
  $waistline_update,
  $bodyfat_update,
  $bmi_update,
  $bmr_update
]);


/*
#錯誤作法：

會有 SQL injection

   $sql = sprintf("INSERT INTO `address_book`(
     `name`, `email`, `mobile`, `birthday`, `address`, `created_at`
     ) VALUES (
       '%s', '%s', '%s', '%s', '%s', NOW()

//值如果包含單引號就會出錯

     )", 
       $_POST['name'],
       $_POST['email'],
       $_POST['mobile'],
       $_POST['birthday'],
       $_POST['address']
   );

   $stmt = $pdo->query($sql);
*/




/*
#一開始的傳統做法

echo json_encode([
  'postData' => $_POST,
  'rowCount' => $stmt->rowCount(),
]);
*/

$output['lastInsertId'] = $pdo->lastInsertId(); # 取得最新資料的 primary key
$output['success'] = boolval($stmt->rowCount());
echo json_encode($output, JSON_UNESCAPED_UNICODE);


