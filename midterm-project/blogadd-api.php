<?php
session_start(); # 启用 session 的功能


# 检查是否已登录
$loggedInUser = $_SESSION['admin'];

$nickname = $loggedInUser['nickname']; //記得前面login要有資料
// $userId = $loggedInUser['member_id'];  //記得前面login要有資料

?>

<?php
require './parts/connect_db.php';



$output = [
  'postData' => $_POST,
  'success' => false,
  // 'error' => '',
  'errors' => [],
];


# 告訴用戶端, 資料格式為 JSON
header('Content-Type: application/json');

//判斷是否缺少欄位值  有缺少就顯示缺少  並echo json格式的output欄位
if (empty($_POST['BlogArticle_Title'])) {
  $output['errors']['form'] = '缺少欄位資料';
  echo json_encode($output);
  exit;
}


//後端檢查

$Member_ID = $_POST['Member_ID'];
$BlogClass_ID = $_POST['BlogClass_ID'];
$BlogArticle_Title = $_POST['BlogArticle_Title'] ?? '';
$BlogArticle_photo = $_POST['BlogArticle_photo'] ?? '';
$BlogArticle_content = $_POST['BlogArticle_content'] ?? '';


// TODO: 資料在寫入之前, 要檢查格式

$isPass = true;

// if(! filter_var($BlogArticle_Title, FILTER_VALIDATE_EMAIL)){
//   $isPass = false;
//   $output['errors']['BlogArticle_Title'] = 'BlogArticle_Title 格式錯誤';
// }


# 如果沒有通過檢查
if (!$isPass) {
  echo json_encode($output);
  exit;
}


$sql = "INSERT INTO `bloglist`(
  `Member_ID`, `BlogClass_ID`, `BlogArticle_Title`, `BlogArticle_photo`, `BlogArticle_content`, `BlogArticle_Create`, `BlogArticle_Time`
  ) VALUES (
    ?, ?, ?, ?, ?, NOW(), NOW()
  )";

$stmt = $pdo->prepare($sql);

// $stmt->execute([
//   $_POST['name'],
//   $_POST['email'],
//   $_POST['mobile'],
//   $_POST['birthday'],
//   $_POST['address'],
// ]);

$stmt->execute([
  $Member_ID,
  $BlogClass_ID,
  $BlogArticle_Title,
  $BlogArticle_photo,
  $BlogArticle_content
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
