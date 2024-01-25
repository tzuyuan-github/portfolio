<?php
require './parts/connect_db.php';

$member_id = isset($_GET['member_id']) ? intval($_GET['member_id']) : 0;

$t_sql = "SELECT COUNT(*) FROM member WHERE member_id BETWEEN 1 AND $member_id";
$t_row = $pdo->query($t_sql)->fetch();
$perPage = 10;

if (isset($_GET['member_id'])) {
  $member_id = explode(',', $_GET['member_id']);
  $placeholders = implode(',', array_fill(0, count($member_id), '?'));

  $delete_sql = "DELETE FROM member WHERE member_id IN ($placeholders)";
  $stmt = $pdo->prepare($delete_sql);
  $stmt->execute($member_id);

}

/*
$come_from = 'member.php';
if(!empty($_SERVER['HTTP_REFERER'])){
  $come_from = $_SERVER['HTTP_REFERER'];
}
*/

header("Location: member.php?page=" . floor($t_row['COUNT(*)'] / $perPage) + 1);