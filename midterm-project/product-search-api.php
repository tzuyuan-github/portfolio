<?php

require './parts/connect_db.php';

$searchStr = $_POST['searchStr'];
$searchType = $_POST['search-type'];

if ($searchType === 'name') {
    // Search by product name
    $query = "SELECT * FROM product_list WHERE name LIKE '%$searchStr%'";
} elseif ($searchType === 'id') {
    // Search by product ID
    $query = "SELECT * FROM product_list WHERE product_id LIKE '%$searchStr%'";
}

$result = $pdo->query($query)->fetchAll();
if (count($result) > 0) {
    echo json_encode($result);
} else {
    echo json_encode(array('message' => 'No results found.'));
}

# Search:only product name
// require './index-parts/connect_db.php';

// if (isset($_POST['search-field'])) {
//     $searchStr = $_POST['search-field'];
//     $sql = "SELECT * FROM product_list WHERE name LIKE '%$searchStr%'";
//     $result = $pdo->query($sql)->fetchAll();

//     if (count($result) > 0) {
//         echo json_encode($result);
//     } else {
//         echo json_encode(array('message' => 'No results found.'));
//     }
// }