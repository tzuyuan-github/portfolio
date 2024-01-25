<?php
require './parts/connect_db.php';

if (isset($_POST['search-field'])) {
    $searchStr = $_POST['search-field'];
    $sql = "SELECT * FROM member WHERE member_name LIKE '%$searchStr%'";
    $result = $pdo->query($sql)->fetchAll();

    if (count($result) > 0) {
        echo json_encode($result);
    } else {
        echo json_encode(array('message' => 'No results found.'));
    }
}
