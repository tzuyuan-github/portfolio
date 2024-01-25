<?php
require './parts/connect_db.php';

$member_id = isset($_GET['member_id']) ? intval($_GET['member_id']) : 0;

$t_sql = "SELECT COUNT(*) FROM member WHERE member_id BETWEEN 1 AND $member_id";
$t_row = $pdo->query($t_sql)->fetch();
$perPage = 10;

if (!empty($member_id)) {
  $sql = "DELETE FROM member WHERE member_id={$member_id}";
  $pdo->query($sql);
}

// header('Location: member.php?page=$t_row['COUNT(*)] / $perPage + 1');
header("Location: member.php?page=" . floor($t_row['COUNT(*)'] / $perPage) + 1);
// Q: Redirect to the page where entry was deleted?