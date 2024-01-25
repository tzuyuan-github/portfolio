<?php
require './parts/connect_db.php';

$dir = __DIR__ . './uploads/';

$exts = [
  'image/jpeg' => '.jpg',
  'image/png' => '.png',
  'image/webp' => '.webp',
];

header('Content-Type: application/json');

$result = [
  'success' => false,
  'file' => '',
];

$sql = "INSERT INTO member (profile_pic) VALUES ('')";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$last_id = $pdo->lastInsertId();
$prev_id = $last_id - 1;


$sql = "SELECT LAST_INSERT_ID() AS last_id";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$last_result = $stmt->fetch();
$last_id = $last_result['last_id'];

if (!empty($_FILES) and !empty($_FILES['avatar'])) {

  if (!empty($exts[$_FILES['avatar']['type']])) {
    $ext = $exts[$_FILES['avatar']['type']]; // 副檔名
    # 隨機的主檔名
    $f = sha1($_FILES['avatar']['name'] . uniqid());
    if (
      move_uploaded_file(
        $_FILES['avatar']['tmp_name'],
        $dir . $f . $ext
      )
    ) {
      $result['success'] = true;
      $result['file'] = $_FILES['avatar']['name'];
    }
  }
}

$sql = "UPDATE `member` SET `profile_pic`=? WHERE `member_id`=? ";
$stmt2 = $pdo->prepare($sql);

$stmt2->execute([
  $f . $ext, $prev_id
]);

$sql = "DELETE FROM member WHERE member_id = (SELECT MAX(member_id) FROM member)";
$stmt3 = $pdo->prepare($sql);
$stmt3->execute();

echo json_encode($result);
