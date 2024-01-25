<?php
require './member-parts/connect_db.php';

/*
$sql = "SELECT * FROM member ORDER BY member_bday DESC";
$result = $pdo->query($sql)->fetchAll();
*/


$data = json_decode(file_get_contents('php://input'), true);
$sortFlag = $data['sort'];
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$perPage = 10;

if ($sortFlag === 'sort_by_birthday') {
  $sql = sprintf(
    "SELECT * FROM member ORDER BY member_bday DESC LIMIT %s, %s",
    ($page - 1) * $perPage,
    $perPage
  );
} else {
  $sql = sprintf(
    "SELECT * FROM member ORDER BY member_id ASC LIMIT %s, %s",
    ($page - 1) * $perPage,
    $perPage
  );
}

$result = $pdo->query($sql)->fetchAll();


if (count($result) > 0) {
  echo json_encode($result);
} else {
  echo json_encode(array('message' => 'No results found.'));
}
