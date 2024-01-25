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
$bdentry_id = isset($_POST['bdentry_id']) ? intval($_POST['bdentry_id']) : 0;
// 上述代码主要用于获取从 $_POST 数组中传递的名为 bdentry_id 的值，并将其转换为整数。它使用了条件（三元）运算符来执行以下操作：
// isset($_POST['bdentry_id'])：这部分检查 $_POST 数组中是否存在名为 bdentry_id 的键。如果存在，表达式将返回 true；否则返回 false。
// intval($_POST['bdentry_id'])：如果 bdentry_id 存在，它将获取该值并使用 intval() 函数将其转换为整数。如果 bdentry_id 不存在，则这一部分不会执行。
// 0：这是条件运算符的第三部分，即如果 bdentry_id 不存在（返回 false），则将为变量 $bdentry_id 分配默认值 0。
// 综合起来，这行代码的目的是获取 bdentry_id 的整数值，如果 bdentry_id 不存在或无效，则将其设置为 0。这可以用于后续对 bdentry_id 进行处理，确保始终有一个整数值可供使用。

if (empty($bdentry_id)) {
    $output['errors']['bdentry_id'] = "沒有 PK";
    $output['success'] = false;
    echo json_encode($output);
    exit; // 結束程式
}


$bdentry_id = $_POST['bdentry_id'] ?? '';
$member_id = $_POST['member_id'] ?? '';
$height_update = $_POST['height_update'] ?? '';
$weight_update = $_POST['weight_update'] ?? '';
$waistline_update = $_POST['waistline_update'] ?? '';
$bodyfat_update = $_POST['bodyfat_update'] ?? '';
$bmi_update = $_POST['bmi_update'] ?? '';
$bmr_update = $_POST['bmr_update'] ?? '';


// TODO: 資料在寫入之前, 要檢查格式

// trim(): 去除頭尾的空白
// strlen(): 查看字串的長度
// mb_strlen(): 查看中文字串的長度

$isPass = true;
// if (empty($name)) {
//     $isPass = false;
//     $output['errors']['name'] = '請填寫正確的姓名';
// }

// if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
//     $isPass = false;
//     $output['errors']['email'] = 'email 格式錯誤';
// }

# 如果沒有通過檢查
if (!$isPass) {
    echo json_encode($output);
    exit;
}

$sql = "UPDATE `body_tracking` SET 
  `member_id`=?,
  `height_update`=?,
  `weight_update`=?,
  `waistline_update`=?,
  `bodyfat_update`=?,
  `bmi_update`=?,
  `bmr_update`=?
WHERE `bdentry_id`=? ";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    $member_id,
    $height_update,
    $weight_update,
    $waistline_update,
    $bodyfat_update,
    $bmi_update,
    $bmr_update,
    $bdentry_id
]);

$output['rowCount'] = $stmt->rowCount();
$output['success'] = boolval($stmt->rowCount());
echo json_encode($output);
