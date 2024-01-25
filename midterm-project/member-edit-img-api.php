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

$member_id = isset($_POST['member_id2']) ? intval($_POST['member_id2']) : 0;

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
$stmt = $pdo->prepare($sql);

$stmt->execute([
  $f . $ext, $member_id
]);

echo json_encode($result);
