<?php

require './parts/connect_db.php';

if (!isset($_SESSION['admin'])) {
  header('Location: login.php');
  exit();
}

$pageName = 'list';
$title = '列表';

$perPage = 20; # 一頁最多有幾筆

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) {
  header('Location: ?page=1'); # 頁面轉向
  exit; # 直接結束這支 php
}

$searchStr = isset($_POST['search-field']) ? $_POST['search-field'] : '';

$t_sql = "SELECT COUNT(1) FROM sports_diary";
if (!empty($searchStr)) {
  // 如果有搜索条件，添加搜索条件到 SQL 查询中
  $t_sql .= " WHERE member_id LIKE '%$searchStr%'";
}





# 總筆數
$totalRows = $pdo->query($t_sql)->fetch(PDO::FETCH_NUM)[0];



# 預設值 預設=0
$totalPages = 0;
$rows = [];


// 有資料時 => 總筆數totalRows不是0
if ($totalRows > 0) {
  # 總頁數
  $totalPages = ceil($totalRows / $perPage);
  if ($page > $totalPages) {
    header('Location: ?page=' . $totalPages); # 頁面轉向最後一頁
    exit; # 直接結束這支 php
  }

  // JOIN用法(將兩張表相關聯，解釋：選擇sp表單的所有欄位、和選擇wc表單的exercise_name欄位。一個JOIN配一個ON)
  // 選擇sports_diary的全部,和workout_category的exercise_name,從sports_diary和workout_category,當運動日記的e2_sid=workout_category的exercise_sid
  $sql = "SELECT sd.*, wc_main.exercise_name AS category1, wc_sub.exercise_name AS category2  
  FROM sports_diary AS sd 
  LEFT JOIN workout_category AS wc_main ON sd.e1_sid = wc_main.exercise_sid
  LEFT JOIN workout_category AS wc_sub ON sd.e2_sid = wc_sub.exercise_sid";

  //   $sql = "SELECT sd.*, wc.exercise_name  FROM sports_diary AS sd 
// JOIN workout_category AS wc ON sd.e1_sid=wc.exercise_sid";

  //   $sql2 = "SELECT sd.*, wc.exercise_name  FROM sports_diary AS sd 
//   JOIN workout_category AS wc ON sd.e2_sid=wc.exercise_sid";
  if (!empty($searchStr)) {
    // 如果有搜索条件，添加搜索条件到 SQL 查询中
    $sql .= " WHERE member_id LIKE '%$searchStr%'";
  }
  $sql .= " ORDER BY fdentry_id DESC LIMIT " . ($page - 1) * $perPage . ", $perPage";
  $rows = $pdo->query($sql)->fetchAll();
}

?>
<?php include './parts/html-head.php' ?>
<?php include './parts/navbar.php' ?>
<style>
  nav.navbar ul.navbar-nav .nav-link.active {
    background-color: blue;
    color: white;
    border-radius: 6px;
    font-weight: 600;
  }
</style>
<div class="container">
  <nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
      <a class="navbar-brand" href="./">運動日記</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link <?= $pageName == 'list' ? 'active' : '' ?>" href="health_list.php">運動紀錄</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $pageName == 'add' ? 'active' : '' ?>" href="health_add.php">新增紀錄</a>
          </li>
        </ul>
        <ul class="navbar-nav mb-2 mb-lg-0">
          <?php if (isset($_SESSION['admin'])): ?>
            <li class="nav-item">
              <a class="nav-link">
                <?= $_SESSION['admin']['nickname'] ?>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= $pageName == 'login' ? 'active' : '' ?>" href="logout.php">登出</a>
            </li>
          <?php else: ?>
            <li class="nav-item">
              <a class="nav-link <?= $pageName == 'login' ? 'active' : '' ?>" href="login.php">登入</a>
            </li>
          <?php endif ?>

        </ul>
      </div>
    </div>
  </nav>
</div>
<style>
  .avatarsize {
    max-width: 100px;
  }
</style>



<div class="container">
  <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" id="search-form"
    name="search-form" method="POST">
    <div class="input-group">
      <input type="text" id="search-field" name="search-field" class="form-control bg-light border-0 small"
        placeholder="搜尋會員編號" aria-label="Search" aria-describedby="basic-addon2"
        value="<?= htmlspecialchars($searchStr) ?>" />
      <div class="input-group-append">
        <button class="btn btn-primary" type="submit">
          <i class="fas fa-search fa-sm"></i>
        </button>
      </div>
    </div>



  </form>
  <div class="row">
    <div class="col">
      <nav aria-label="Page navigation example">
        <ul class="pagination">
          <!-- 左按鈕 -->
          <li class="page-item <?= $page == 1 ? 'disabled' : '' ?>">
            <!-- if page=第一頁 則功能消失 -->
            <a class="page-link" href="?page=1">
              <i class="fa-solid fa-angles-left">
              </i>
            </a>
          </li>


          <!-- <li class="page-item"><a class="page-link" href="#">Previous</a></li> -->


          <!--  for($i=1; $i<= $totalPages; $i++):   -->
          <?php for ($i = $page - 5; $i <= $page + 5; $i++):

            if ($i >= 1 and $i <= $totalPages): ?>

              <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <!-- 加上active  點了會反白 -->

                <a class="page-link" href=" ?page= <?= $i ?>">
                  <?= $i ?>
                </a>
              </li>
            <?php endif ?>
          <?php endfor ?>

          <!-- <li class="page-item"><a class="page-link" href="#">Next</a></li> -->

          <!-- 右按鈕 -->
          <li class="page-item <?= $page == $totalPages ? 'disabled' : '' ?>">
            <!-- if page=最後一頁 則功能消失 -->
            <a class="page-link" href="?page=<?= $totalPages ?>">
              <i class="fa-solid fa-angles-right">
              </i>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </div>

  <!-- 總筆數/總頁數 -->
  <div>
    <?= "$totalRows / $totalPages" ?>
  </div>


  <div class="row">
    <div class="col">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <!-- 表頭 -->
            <th scope="col">運動日記 - 紀錄編號</th>
            <th scope="col">會員編號</th>
            <th scope="col">重訓類別</th>
            <th scope="col">重訓名稱</th>
            <th scope="col">運動日記 - 紀錄日期時間</th>
            <th scope="col">重訓重量</th>
            <th scope="col">重訓次數</th>
            <th scope="col">重訓組數</th>
            <th scope="col">健身成果</th>
            <th><i class="fa-solid fa-file-pen"></i></th>
            <th><i class="fa-solid fa-trash-can"></i></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              </a></th>
              <td>
                <?= $r['fdentry_id'] ?>
              </td>
              <td>
                <?= $r['member_id'] ?>
              </td>
              <td>
                <?= $r['category1'] ?>
              </td>
              <td>
                <?= $r['category2'] ?>
              </td>
              <td>
                <?= $r['fdentry_datetime'] ?>
              </td>
              <td>
                <?= $r['wtraining_weight'] ?>
              </td>
              <td>
                <?= $r['wtraining_reps'] ?>
              </td>
              <td>
                <?= $r['wtraining_set'] ?>
              </td>
              <td>
                <?php if (!empty($r['avatar'])): ?>
                  <div>
                    <img src="./uploads/<?= $r['avatar'] ?>" alt="" class=" avatarsize">
                  </div>
                <?php endif ?>
              </td>

              <th><a href="health_edit.php?fdentry_id=<?= $r['fdentry_id'] ?>">
                  <i class="fa-solid fa-file-pen"></i>
              <th><a href="javascript: deleteItem(<?= $r['fdentry_id'] ?>)">
                  <i class="fa-solid fa-trash-can"></i>
                </a></th>
            </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include './parts/scripts.php' ?>
<script>
  function deleteItem(fdentry_id) {
    if (confirm(`確定要刪除編號為 ${fdentry_id} 的資料嗎?`)) {
      location.href = 'health_delete.php?fdentry_id=' + fdentry_id;
    }
  }



</script>
<?php include './parts/html-foot.php' ?>