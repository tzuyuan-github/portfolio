<?php
require './parts/connect_db.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    // 解析傳入的JSON數據
    $postData = json_decode(file_get_contents("php://input"), true);

    if (isset($postData['items']) && is_array($postData['items'])) {
        // 將每個選到的選項刪除
        foreach ($postData['items'] as $itemId) {
            $sql = "DELETE FROM `product_list` WHERE sid = :sid";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['sid' => $itemId]);
        }

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => '無效的數據']);
    }
} else {
    echo json_encode(['success' => false, 'message' => '無效的請求']);
}

