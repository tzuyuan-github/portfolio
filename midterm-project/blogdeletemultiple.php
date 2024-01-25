<?php
require './parts/connect_db.php';
if (isset($_GET['BlogArticle_IDs'])) {
  $gymIds = explode(',', $_GET['BlogArticle_IDs']);
  $placeholders = implode(',', array_fill(0, count($gymIds), '?'));

  $deleteSql = "DELETE FROM bloglist WHERE BlogArticle_ID IN ($placeholders)";
  $stmt = $pdo->prepare($deleteSql);
  $stmt->execute($gymIds);

//   echo "<script>alert('已刪除成功'); </script>";

}


$come_from = 'bloglist.php';
if(! empty($_SERVER['HTTP_REFERER'])){
  $come_from = $_SERVER['HTTP_REFERER'];


}
header("Location: $come_from");
?>

