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


$sql = sprintf("INSERT INTO `gym`( `gym_name`, `gym_address`, `begin_time`,`end_time`, `gym_description`, `district_id`,`gym_photo`,`created_at`)
VALUES (?,?,?,?,?,?,?,now())");
$gym_name = $_POST['gym_name'] ?? null;
$gym_address = $_POST['gym_address'] ?? null;
$begin_time = $_POST['begin_time'] ?? null;
$end_time = $_POST['end_time'] ?? null;
$gym_description = $_POST['gym_description'] ?? null;
$district_id = $_POST['district_id'] ?? null;
$gym_photo = $_POST['gym_photo'] ?? null;

$stmt = $pdo->prepare($sql);
$stmt->execute(
  [
    $gym_name,
    $gym_address,
    $begin_time,
    $end_time,
    $gym_description,
    $district_id,
    $gym_photo
  ]
);
$output['success'] = boolval($stmt->rowCount());
echo json_encode($output);
