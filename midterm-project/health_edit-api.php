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
$fdentry_id = isset($_POST['fdentry_id']) ? intval($_POST['fdentry_id']) : 0;
// 上述代码主要用于获取从 $_POST 数组中传递的名为 fdentry_id 的值，并将其转换为整数。它使用了条件（三元）运算符来执行以下操作：
// isset($_POST['fdentry_id'])：这部分检查 $_POST 数组中是否存在名为 fdentry_id 的键。如果存在，表达式将返回 true；否则返回 false。
// intval($_POST['fdentry_id'])：如果 fdentry_id 存在，它将获取该值并使用 intval() 函数将其转换为整数。如果 fdentry_id 不存在，则这一部分不会执行。
// 0：这是条件运算符的第三部分，即如果 fdentry_id 不存在（返回 false），则将为变量 $fdentry_id 分配默认值 0。
// 综合起来，这行代码的目的是获取 fdentry_id 的整数值，如果 fdentry_id 不存在或无效，则将其设置为 0。这可以用于后续对 fdentry_id 进行处理，确保始终有一个整数值可供使用。

if (empty($fdentry_id)) {
    $output['errors']['fdentry_id'] = "沒有 PK";
    echo json_encode($output);
    exit; // 結束程式
}


// $fdentry_id = $_POST['fdentry_id'] ?? '';
$member_id = $_POST['member_id'] ?? '';
$e1_sid = $_POST['e1_sid'] ?? '';
$e2_sid = $_POST['e2_sid'] ?? '';
// $fdentry_datetime = $_POST['fdentry_datetime'] ?? '';
$wtraining_weight = $_POST['wtraining_weight'] ?? '';
$wtraining_reps = $_POST['wtraining_reps'] ?? '';
$wtraining_set = $_POST['wtraining_set'] ?? '';

$isPass = true;

# 如果沒有通過檢查
if (!$isPass) {
    echo json_encode($output);
    exit;
}

$sql = "UPDATE `sports_diary` SET 
  `member_id`=?,
  `e1_sid`=?,
  `e2_sid`=?,
  `wtraining_weight`=?,
  `wtraining_reps`=?,
  `wtraining_set`=?

WHERE `fdentry_id`=? ";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    $member_id,
    $e1_sid,
    $e2_sid,
    $wtraining_weight,
    $wtraining_reps,
    $wtraining_set,
    $fdentry_id
]);

$output['rowCount'] = $stmt->rowCount();
$output['success'] = boolval($stmt->rowCount());
echo json_encode($output, JSON_UNESCAPED_UNICODE);
