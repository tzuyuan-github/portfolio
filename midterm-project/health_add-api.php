<?php
require './parts/connect_db.php';

# 检查是否已登录
if (isset($_SESSION['admin'])) {
  $loggedInUser = $_SESSION['admin'];

  # 获取用户的 ID、昵称等信息
  $Member_ID = $loggedInUser['id'];
  $nickname = $loggedInUser['nickname'];
}

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


//後端檢查
$member_id = $_POST['member_id'] ?? '';
$e1_sid = $_POST['e1_sid'] ?? '';
$e2_sid = $_POST['e2_sid'] ?? '';
// $exercise_id = $_POST['exercise_id'] ?? '';
// $fdentry_datetime = $_POST['fdentry_datetime'] ?? '';
$wtraining_weight = $_POST['wtraining_weight'] ?? '';
$wtraining_reps = $_POST['wtraining_reps'] ?? '';
$wtraining_set = $_POST['wtraining_set'] ?? '';
$avatar = $_POST['avatar'] ?? '';


/*
if(empty($_POST['name']) or empty($_POST['email'])){
    $output['errors']['form'] = '缺少欄位資料';
    echo json_encode($output);
    exit;
}
*/
// 在后端檢查之前，添加条件检查,判斷是否缺少欄位值  有缺少就顯示缺少  並echo json格式的output欄位
if (empty($member_id) || empty($e1_sid) || empty($e2_sid) || empty($wtraining_weight) || empty($wtraining_reps) || empty($wtraining_set)) {
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
if (!$isPass) {
  echo json_encode($output);
  exit;
}


$sql = "INSERT INTO `sports_diary`(
  `member_id`, `e1_sid`, `e2_sid`, `fdentry_datetime`, `wtraining_weight`, `wtraining_reps`, `wtraining_set`, `avatar`
  ) VALUES (
    ?, ?, ?, NOW(), ?, ?, ?, ?
  )";

$stmt = $pdo->prepare($sql);


// 下面這些值要灌進資料庫
$stmt->execute([
  $member_id,
  $e1_sid,
  $e2_sid,
  // $exercise_id,
  // $fdentry_datetime,
  $wtraining_weight,
  $wtraining_reps,
  $wtraining_set,
  $avatar,
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
echo json_encode($output);


