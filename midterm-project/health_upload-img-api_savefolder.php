<?php
require './parts/connect_db.php';

$dir = __DIR__ . '/uploads/';


# 檔案類型的篩選
$exts = [
  'image/jpeg' => '.jpg',
  'image/png' => '.png',
  'image/webp' => '.webp',
];



header('Content-Type: application/json');
$output = [
  'success' => false,
  'file' => ''
];

if (!empty($_FILES) and !empty($_FILES['avatar']) and $_FILES['avatar']['error']==0) {
  
  if (!empty( $exts[$_FILES['avatar']['type']] )) {
    $ext = $exts[$_FILES['avatar']['type']]; // 副檔名

    # 隨機的主檔名
    $f = sha1($_FILES['avatar']['name']. uniqid());

    if (
      move_uploaded_file(
        $_FILES['avatar']['tmp_name'],
        $dir . $f. $ext
      )
    ) {
      $output['success'] = true;
      $output['file'] = $f. $ext;
    }
  }
}

$fdentry_id = $_POST['fdentry_id'] ?? '';
$avatar = $f. $ext;


$sql = "UPDATE `sports_diary` SET 
  `avatar`=?
WHERE `fdentry_id`=? ";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    $avatar,
    $fdentry_id
]);

// $output['success'] = boolval($stmt->rowCount());
echo json_encode($output);
