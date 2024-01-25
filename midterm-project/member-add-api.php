<?php
require './parts/connect_db.php';

$output = [
  'postData' => $_POST,
  'success' => false,
  'errors' => [],
];

header('Content-Type: application/json');

$name = $_POST['name'];
$gender = $_POST['gender'];
$bday = $_POST['bday'];
$email = $_POST['email'];
$mobile = $_POST['mobile'];
$username = $_POST['username'];
$password = $_POST['password'];
$nickname = $_POST['nickname'];
$city = $_POST['cat1'];
$district = $_POST['cat2'];
$address = $_POST['address'];

$sql = "INSERT INTO `member`(
  `member_name`, `member_gender`, `member_bday`, `member_email`, `member_mobile`, `member_username`, `member_password`, `member_nickname`, `city`, `district`, `address`
  ) VALUES (
    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
  )";

$stmt = $pdo->prepare($sql);

$stmt->execute([
  $name,
  $gender,
  $bday,
  $email,
  $mobile,
  $username,
  $password,
  $nickname,
  $city,
  $district,
  $address,
]);

$output['success'] = boolval($stmt->rowCount());
echo json_encode($output);
