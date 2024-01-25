<?php
require './parts/connect_db.php';

header('Content-Type: application/json');

$output = [
  'postData' => $_POST,
  'success' => false,
  'errors' => [],
];

$member_id = isset($_POST['member_id']) ? intval($_POST['member_id']) : 0;

if (empty($member_id)) {
  $output['errors']['member_id'] = "沒有 PK";
  echo json_encode($output);
  exit;
}

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

$sql = "UPDATE `member` SET
  `member_name`=?,
  `member_gender`=?,
  `member_bday`=?,
  `member_email`=?,
  `member_mobile`=?,
  `member_username`=?,
  `member_password`=?,
  `member_nickname`=?,
  `city`=?,
  `district`=?,
  `address`=?
WHERE `member_id`=? ";

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
  $member_id,
]);

$output['rowCount'] = $stmt->rowCount();
$output['success'] = boolval($stmt->rowCount());
echo json_encode($output);
