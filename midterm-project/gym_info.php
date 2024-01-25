<?php
require './parts/connect_db.php';

if (!isset($_SESSION['admin'])) {
  header('Location: login.php');
  exit();
}

$gym_id = isset($_GET['gym_id']) ? intval($_GET['gym_id']) : 0;

if (empty($gym_id)) {
  header('Location: gym_list.php');
  exit;
} else {
  $sql = "SELECT * FROM gym JOIN district on gym.district_id = district.district_id WHERE gym_id={$gym_id}";
  $row = $pdo->query($sql)->fetch();
  if (empty($row)) {
    header('Location: gym_list.php');
    exit;
  }
}
;
?>
<?php include './parts/html-head.php' ?>

<?php include './parts/navbar.php' ?>
<div class="container">
  <h1 class="border-bottom border-5 p-2">
    <?= htmlentities($row['gym_name']) ?>
  </h1>

  <div style="width: 500px" class="mb-3">
    <img src=<?= "/main-dev/uploads/" . htmlentities($row['gym_photo']) ?> alt="" id="gym_photo_img" width="100%" />
  </div>
  <h3>介紹</h3>
  <div class="mb-3">
    <?= htmlentities($row['gym_description']) ?>
  </div>
  <h3>地址</h3>
  <div class="mb-3">
    <?= htmlentities($row['district_name']).htmlentities($row['gym_address']) ?>
  </div>
  <h3>營業時間</h3>
  <div class="mb-3">
  <?= substr($row['begin_time'], 0, -3) . '~' . substr($row['end_time'], 0, -3) ?>
  </div>
</div>

<?php include './parts/scripts.php' ?>

<?php include './parts/html-foot.php' ?>