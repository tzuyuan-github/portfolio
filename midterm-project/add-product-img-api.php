<?php
require './parts/connect_db.php';

$dir = __DIR__ . '/product-imgs/';

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

$mainImg = $_POST['mainImg'] ?? '';
// echo json_encode($output);

//先去一個空白欄位 為了得到 sid 後面會刪掉
$sql = "INSERT INTO `product_list`(`img`) VALUES ('')";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$last_id = $pdo->lastInsertId();
$prev_id = $last_id - 1;



if (!empty($_FILES) and !empty($_FILES['mainImg']) and $_FILES['mainImg']['error'] == 0) {

  if (!empty($exts[$_FILES['mainImg']['type']])) {
    $ext = $exts[$_FILES['mainImg']['type']]; // 副檔名

    # 隨機的主檔名
    $f = sha1($_FILES['mainImg']['name'] . uniqid());

    # 將檔案直接存到資料夾
    if (
      move_uploaded_file(
        $_FILES['mainImg']['tmp_name'],
        $dir . $f . $ext
      )
    ) {
      $output['success'] = true;
      $output['file'] = $f . $ext;
    }

    $sql = "UPDATE `product_list` SET `img`=? WHERE `sid`= ? ";
    $stmt2 = $pdo->prepare($sql);

    $stmt2->execute([
      $f . $ext, $prev_id
    ]);
  }
}



$sql = "DELETE FROM product_list WHERE sid = (SELECT MAX(sid) FROM product_list)";
$stmt3 = $pdo->prepare($sql);
$stmt3->execute();


$output['success'] = boolval($stmt->rowCount());

echo json_encode($output, JSON_UNESCAPED_UNICODE);
