<?php

require './parts/connect_db.php';

if (!isset($_SESSION['admin'])) {
  header('Location: login.php');
  exit();
}

$pageName = 'list';
$title = '列表';

$perPage = 20; # 一頁最多有幾筆

$page=isset($_GET['page'])?intval($_GET['page']):1;
if($page<1){
    header('Location: ?page=1'); # 頁面轉向
    exit; # 直接結束這支 php
}


$t_sql = "SELECT COUNT(1) FROM body_tracking";

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


    $sql = sprintf(
        "SELECT * FROM body_tracking ORDER BY bdentry_id DESC LIMIT %s, %s",
        ($page - 1) * $perPage,
        $perPage

    );
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
      <a class="navbar-brand" href="./">體態追蹤</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link <?= $pageName == 'list' ? 'active' : '' ?>" href="body_list.php">體態紀錄</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $pageName == 'add' ? 'active' : '' ?>" href="body_add.php">新增紀錄</a>
          </li>
        </ul>
        <ul class="navbar-nav mb-2 mb-lg-0">
          <?php if (isset($_SESSION['admin'])) : ?>
            <li class="nav-item">
              <a class="nav-link"><?= $_SESSION['admin']['nickname'] ?></a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= $pageName == 'login' ? 'active' : '' ?>" href="logout.php">登出</a>
            </li>
          <?php else : ?>
            <li class="nav-item">
              <a class="nav-link <?= $pageName == 'login' ? 'active' : '' ?>" href="login.php">登入</a>
            </li>
          <?php endif ?>

        </ul>
      </div>
    </div>
  </nav>
</div>


<div class="container">
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
            <?php for($i = $page-5; $i <= $page+5; $i++):  

            if($i>=1 and $i<=$totalPages):?>

                <li class="page-item <?= $i==$page ? 'active' : '' ?>">
                <!-- 加上active  點了會反白 -->

                    <a class="page-link" href=" ?page= <?= $i ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endif?>
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
  <div><?= "$totalRows / $totalPages" ?></div>


  <div class="row">
    <div class="col">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th scope="col">體態追蹤 - 紀錄編號</th>
            <th scope="col">會員編號</th>
            <th scope="col">更新身高</th>
            <th scope="col">更新體重</th>
            <th scope="col">更新腰圍</th>
            <th scope="col">更新體脂</th>
            <th scope="col">目前BMI</th>
            <th scope="col">目前BMR</th>
            <th ><i class="fa-solid fa-file-pen"></i></th>            
            <th ><i class="fa-solid fa-trash-can"></i></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($rows as $r): ?>
          <tr>
                </a></th>
            <td><?= $r['bdentry_id'] ?></td>
            <td><?= $r['member_id'] ?></td>
            <td><?= $r['height_update'] ?></td>
            <td><?= $r['weight_update'] ?></td>
            <td><?= $r['waistline_update'] ?></td>
            <td><?= $r['bodyfat_update'] ?></td>
            <td><?= $r['bmi_update'] ?></td>
            <td><?= $r['bmr_update'] ?></td>
            <th ><a href="body_edit.php?bdentry_id=<?= $r['bdentry_id'] ?>">
                  <i class="fa-solid fa-file-pen"></i>
                  <th ><a href="javascript: deleteItem(<?= $r['bdentry_id'] ?>)">
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
  function deleteItem(bdentry_id) {
    if (confirm(`確定要刪除編號為 ${bdentry_id} 的資料嗎?`)) {
      location.href = 'body_delete.php?bdentry_id=' + bdentry_id;
    }
  }
</script>
<?php include './parts/html-foot.php' ?>