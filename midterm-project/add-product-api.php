<?php
require './parts/connect_db.php';

// 告訴用戶端格式為JSON
header('Content-Type: application/json');

// 宣告變數 避免使用者點開此 api 會出現 warning，通常不要直接看api檔案
$output = [
  'postData' => $_POST,
  'success' => false,
  'errors' => [],
];
// echo json_encode($output);
// exit;


// 資料寫入前要檢查: 
if (empty($_POST['name']) or empty($_POST['price']) or empty($_POST['cate1']) or empty($_POST['cate2']) or empty($_POST['descriptions'])) {
  $output['errors']['form'] = "缺少欄位資料";
  echo json_encode($output, JSON_UNESCAPED_UNICODE);
  exit;
};

// 避免錯誤訊息：避免沒有值時 warning 訊息跑到 html 標籤中
$name = $_POST['name'] ?? '';
$price = $_POST['price'] ?? '';
$cate1 = $_POST['cate1'] ?? '';
$cate2 = $_POST['cate2'] ?? '';
$descriptions = $_POST['descriptions'] ?? '';
$inventory = $_POST['inventory'] ?? '';
$launch = $_POST['launch'] ?? '';

# 後端檢查
$isPass = true;

//如果庫存量為0,launch設定為0
// if (intval($inventory) === 0) {
//   $launch = 0;
// }

// 如果沒有通過檢查
if (!$isPass) {
  echo json_encode($output, JSON_UNESCAPED_UNICODE);
  exit;
}

# --- 自動給商品編號 日期＋有序編號 FYT-20231018-00001
$currentDate = date('Ymd');

// 查找資料庫最大的序號
$maxNumber = getMaxProductNumber($pdo, $currentDate);

function getMaxProductNumber($pdo, $currentDate)
{
  // 查當前日期匹配的最大序號
  $sql = "SELECT MAX(SUBSTRING_INDEX(product_id, '-', -1)) AS max_number 
          FROM product_list
          WHERE product_id LIKE :date_prefix";

  $stmt = $pdo->prepare($sql);

  $datePrefix = 'FYT-' . $currentDate . '-%';
  $stmt->bindParam(':date_prefix', $datePrefix, PDO::PARAM_STR);
  $stmt->execute();

  $maxNumberResult = $stmt->fetch(PDO::FETCH_ASSOC);

  // 提取最大序號碼
  $maxNumber = (int)$maxNumberResult['max_number'];

  return $maxNumber;
}

$newNumber = $maxNumber + 1;

// 保持固定五位數 不夠補0
$numberFormatted = sprintf('%04d', $newNumber); // 例如：0001

// 生成商品編號
$pid = 'FYT-' . $currentDate . '-' . $numberFormatted;


# 與資料庫串接
// 新增功能： ?用來佔位
$sql = "INSERT INTO `product_list`(
    `product_id`, 
    `name`, `price`, `main_category`, `category`, `descriptions`, `inventory`, `purchase_qty`, `launch`, `create_date`, `img`
  ) VALUES (
    ?, ?, ?, ?, ?, ?, ?, 0, ?, NOW(), ''
  )";


// pdo 先準備：並沒有真的執行，會先拿到pdo statement 的物件
$stmt = $pdo->prepare($sql);

// pdo stmt 執行：把表單拿到的值丟到上方的 ?
$stmt->execute([
  $pid,
  $name,
  $price,
  $cate1,
  $cate2,
  $descriptions,
  $inventory,
  $launch,
]);

// 如果stmt有新增欄位成功(rowcount=1,布林值為ture),output sucess 就呈現 true, echo 輸出結果
$output['success'] = boolval($stmt->rowCount());
echo json_encode($output, JSON_UNESCAPED_UNICODE);


// $latest_sid = $pdo->lastInsertId(); //取得 PK


// # 上傳圖片檔名到 product_detail ------------------------------ 圖片上傳寫到另一支api
// $dir = __DIR__ . '/product-imgs/';

// # 檔案類型的篩選
// $exts = [
//   'image/jpeg' => '.jpg',
//   'image/png' => '.png',
//   'image/webp' => '.webp',
// ];


// $output_img = [
//   'success' => false,
//   'file' => ''
// ];

// $mainImg = $_POST['mainImg'] ?? '';
// // echo json_encode($output);
// $f = ''; // 初始化 $f 為空字串
// $ext = '';

// if (!empty($_FILES) and !empty($_FILES['mainImg']) and $_FILES['mainImg']['error'] == 0) {

//   if (!empty($exts[$_FILES['mainImg']['type']])) {
//     $ext = $exts[$_FILES['mainImg']['type']]; // 副檔名

//     # 隨機的主檔名
//     $f = sha1($_FILES['mainImg']['name'] . uniqid());

//     # 將檔案直接存到資料夾
//     if (
//       move_uploaded_file(
//         $_FILES['mainImg']['tmp_name'],
//         $dir . $f . $ext
//       )
//     ) {
//       $output_img['success'] = true;
//       $output_img['file'] = $f . $ext;
//     }
//     $imgurl = $f . $ext; // 只存檔名就好

//     $sql_img = "INSERT INTO `product_detail`(`product_sid`, `img`) VALUES (?, ?)";


//     $stmt_img = $pdo->prepare($sql_img);

//     $stmt_img->execute([
//       $pid,
//       $imgurl
//     ]);

//     $output_img['success'] = boolval($stmt_img->rowCount());
//     echo json_encode($output_img, JSON_UNESCAPED_UNICODE);
//   }
// }
