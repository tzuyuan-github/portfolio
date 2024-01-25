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
$BlogArticle_ID = isset($_POST['BlogArticle_ID']) ? intval($_POST['BlogArticle_ID']) : 0;

if (empty($BlogArticle_ID)) {
    $output['errors']['BlogArticle_ID'] = "沒有 PK";
    echo json_encode($output);
    exit; // 結束程式
}


$Member_ID = $_POST['Member_ID'] ?? '';
$BlogClass_ID = $_POST['BlogClass_ID'] ?? '';
$BlogArticle_Title = $_POST['BlogArticle_Title'] ?? '';
$BlogArticle_photo = $_POST['BlogArticle_photo'] ?? '';
$BlogArticle_content = $_POST['BlogArticle_content'] ?? '';



// TODO: 資料在寫入之前, 要檢查格式

// trim(): 去除頭尾的空白
// strlen(): 查看字串的長度
// mb_strlen(): 查看中文字串的長度

$isPass = true;
// if (empty($Member_ID)) {
//     $isPass = false;
//     $output['errors']['Member_ID'] = '請填寫ID';
// }else


// if (empty($BlogArticle_Title)) {
//     $isPass = false;
//     $output['errors']['BlogArticle_Title'] = '請填寫標題';
// }


# 如果沒有通過檢查
if (!$isPass) {
    echo json_encode($output);
    exit;
}

$sql = "UPDATE `bloglist` SET 
  `Member_ID`=?,
  `BlogClass_ID`=?,
  `BlogArticle_Title`=?,
  `BlogArticle_photo`=?,
  `BlogArticle_content`=?,
  `BlogArticle_Time`=now()
WHERE `BlogArticle_ID`=? ";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    $Member_ID,
    $BlogClass_ID ,
    $BlogArticle_Title,
    $BlogArticle_photo ,
    $BlogArticle_content,
    $BlogArticle_ID
]);

$output['rowCount'] = $stmt->rowCount();
$output['success'] = boolval($stmt->rowCount());
echo json_encode($output);
