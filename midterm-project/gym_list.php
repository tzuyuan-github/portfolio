<?php
require './parts/connect_db.php';

if (!isset($_SESSION['admin'])) {
  header('Location: login.php');
  exit();
}

$pageName = 'gym_list';
$title = '健身房管理列表';
$sql_d = 'SELECT * FROM district';
$option_d = $pdo->query($sql_d)->fetchAll();
/*
//搜尋用
$text = isset($_GET['text']) ? $_GET['text'] : ''; //宣告他在做搜尋

if (isset($_GET['text']) && $_GET['text'] !== "") {
  $sql = "SELECT * FROM gym JOIN district on gym.district_id = district.district_id
  where district_name='$text'
  ORDER BY gym_id";
} else {
  if ($totalRows > 0) {
      # 總頁數=無條件進位法的(總筆數/每頁幾筆)  2頁=23筆/20=1.XXXX
      $totalPages = ceil($totalRows / $perPage);
      if ($page > $totalPages) {
          header('Location: ?page=' . $totalPages); # 頁面轉向最後一頁
          exit; # 直接結束這支 php
      }

      $sql = sprintf('SELECT * FROM gym JOIN district on gym.district_id = district.district_id
      ORDER BY gym_id desc
      limit %s,%s',
       ($page - 1) * $perPage,
       $perPage

      );
      $rows = $pdo->query($sql)->fetchAll();
  }
}
*/


$perPage = 10;

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) {
  header('Location: ?page=1'); #頁面轉向
  exit; # 直接結束這支 php
}

$t_sql = "SELECT COUNT(1) FROM gym";
# 總筆數
$totalRows = $pdo->query($t_sql)->fetch(PDO::FETCH_NUM)[0];

# 預設值
$totalPages = 0;
$rows = [];

// 有資料時
if ($totalRows > 0) {
  # 總頁數
  $totalPages = ceil($totalRows / $perPage); # 無條件進位，不足20筆也算一頁
  if ($page > $totalPages) {
    header('Location: ?page=' . $totalPages); #頁面轉向最後一頁
    exit;
  }
}

$sql = sprintf('SELECT * FROM gym JOIN district on gym.district_id = district.district_id
 ORDER BY gym_id desc
 limit %s,%s',
  ($page - 1) * $perPage,
  $perPage
);
/*
$sql = sprintf("SELECT *
FROM gym
JOIN district ON gym.district_id = district.district_id
JOIN admin_area ON admin_area.district_id = gym.district_id
ORDER BY gym.gym_id DESC
LIMIT %s, %s",
($page - 1) * $perPage,
  $perPage
);*/
$rows = $pdo->query($sql)->fetchAll();
?>

<?php include './parts/html-head.php' ?>
<?php include './parts/navbar.php' ?>



<style>
  .selected-row {
    background-color: #ffaab4;
    /* 更改为您希望的背景颜色 */
  }
</style>

<!-- Begin Page Content -->
<div class="container-fluid">

  <div class="row">
    <div class="col">
      <div class="d-flex">


        <nav aria-label="Page navigation example">
          <ul class="pagination">
            <li class="page-item <?= $page == 1 ? 'disabled' : '' ?>">
              <a class="page-link" href="?page=1">
                <i class="fa-solid fa-angles-left"></i>
              </a>
            </li>
            <?php for ($i = $page - 3; $i <= $page + 3; $i++):
              if ($i >= 1 and $i <= $totalPages): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                  <a class="page-link" href="?page=<?= $i ?>">
                    <?= $i ?>
                  </a>
                </li>
                <?php
              endif;
            endfor; ?>
            <li class="page-item <?= $page == $totalPages ? 'disabled' : '' ?>">
              <a class="page-link" href="?page=<?= $totalPages ?>">
                <i class="fa-solid fa-angles-right"></i></a>
            </li>
          </ul>
        </nav>
        <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search"
          id="search-form" name="search-form">
          <div class="input-group">
            <input type="text" id="search-field" name="search-field" class="form-control bg-light border-0 small"
              placeholder="搜尋健身房" aria-label="Search" aria-describedby="basic-addon2" />
            <div class="input-group-append">
              <button class="btn btn-primary" type="submit">
                <i class="fas fa-search fa-sm"></i>
              </button>
            </div>
          </div>
        </form>

        <!-- <div class="blog_search d-flex allbtn ">
          <div class="input-group mb-2">
            <select class="form-select form-control" id="district_name" name="district_name" style="width:140px">
              <option>--請選擇縣市搜尋--</option>
              <?php foreach ($option_d as $o): ?>
                <option value="<?= isset($_GET['text']) ? $_GET['text'] : "" ?>">
                  <?= $o['district_name'] ?>
                </option>
              <?php endforeach ?>
            </select>
            <input type="search" style="width: 200px;" class="form-control" id="keyword" placeholder="關鍵字查詢"
              value="<?= isset($_GET['text']) ? $_GET['text'] : "" ?>">
          </div>
          <div>
            <button type="submit" id="search"><i class="fa-solid fa-magnifying-glass"></i></button>
          </div>
        </div> -->




      </div>
      <a href="gym_list.php" class="btn btn-secondary">顯示所有資料</a>
      <div class="btn btn-danger" onclick="deleteMultiple(event)"><i class="fa-solid fa-trash-can text-white"></i>
        刪除勾選的資料<span id="selectedCount"></span>筆</div>
      <a href="gym_add.php" class="btn btn-primary">新增健身房資料</a>
      <div>
        <?= "總共 $totalRows 筆/總共 $totalPages 頁" ?>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col">
      <table class="table table-bordered ">
        <thead>
          <tr class="sticky-top">
            <th scope="col"></th>
            <th scope="col">
              <i class="fa-solid fa-trash-can"></i>
            </th>

            <th scope="col">#</th>
            <th scope="col">健身房名稱</th>
            <th scope="col">圖片</th>
            <th scope="col">介紹</th>
            <th scope="col">營業時間</th>
            <!-- <th scope="col">結束營業時間</th> -->
            <th scope="col">地址</th>
            <th scope="col">預覽</th>
            <th scope="col">資料建立時間</th>
            <th scope="col">
              <i class="fa-solid fa-file-pen"></i>
            </th>
          </tr>
        </thead>
        <tbody id="originalTable">
          <?php foreach ($rows as $r): ?>
            <tr>

              <td>
                <input type="checkbox" name="selectedItems[]" value="<?= $r['gym_id'] ?>">
              </td>

              <td><a href="javascript: deleteItem(<?= $r['gym_id'] ?>)">
                  <i class="fa-solid fa-trash-can text-danger"></i>
                </a></td>
              <td>
                <?= $r['gym_id'] ?>
              </td>
              <td>
                <?= htmlentities($r['gym_name']) ?>
              </td>
              <td>
                <div style="width:100px">
                  <img src="<?= "/main-dev/uploads/" . $r['gym_photo'] ?>" alt="" width='100%'>
                </div>
              </td>
              <td class="text-truncate" style="max-width:200px">
                <?= htmlentities($r['gym_description']) ?>
              </td>
              <td>
                <?= substr($r['begin_time'], 0, -3) . '~' . substr($r['end_time'], 0, -3) ?>
              </td>
              <td>
                <?= $r['district_name'] . htmlentities($r['gym_address']) ?>
              </td>
              <td>
                <a href="gym_info.php?gym_id=<?= $r['gym_id'] ?>">
                  <i class="fa-solid fa-circle-info text-info"></i>
                </a>
              </td>
              <td>
                <?= substr($r['created_at'], 0, -3); ?>
              </td>
              <td><a href="gym_edit.php?gym_id=<?= $r['gym_id'] ?>">
                  <i class="fa-solid fa-file-pen"></i>
                </a></td>
            </tr>
          <?php endforeach ?>
        </tbody>
      </table>
      <table id="resultTable"></table>
    </div>
  </div>

</div>
<!-- /.container-fluid -->



</div>
<!-- End of Main Content -->

<!-- Footer -->
<footer class="sticky-footer bg-white">
  <div class="container my-auto">
    <div class="copyright text-center my-auto">
      <span>Copyright &copy; Your Website 2020</span>
    </div>
  </div>
</footer>
<!-- End of Footer -->

</div>
<!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<?php include './parts/scripts.php' ?>

<script>
  function updateSelectedCount() {
    const selectedItems = document.querySelectorAll('input[name="selectedItems[]"]:checked');
    const selectedCount = selectedItems.length;
    const selectedCountSpan = document.getElementById('selectedCount');
    selectedCountSpan.textContent = selectedCount;

    // 重置背景颜色
    $('.selected-row').removeClass('selected-row');

    // 遍历所有勾选的项目
    selectedItems.forEach(item => {
      // 找到包含勾选框的父元素，然后添加CSS类来更改背景颜色
      $(item).closest('tr').addClass('selected-row');
    });
  }

  // 调用updateSelectedCount以确保初始状态正确显示所选项目的数量
  updateSelectedCount();

  // 监听checkbox勾选状态的变化
  const checkboxes = document.querySelectorAll('input[name="selectedItems[]"]');
  checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', updateSelectedCount);
  });


  function deleteItem(gym_id) {
    if (confirm(`確定要刪除編號為 ${gym_id} 的資料嗎 ?`)) {
      location.href = 'gym_delete.php?gym_id=' + gym_id;
    }
  }

  function deleteMultiple(event) {

    const selectedItems = document.querySelectorAll('input[name="selectedItems[]"]:checked');
    //console.log("已选中的项目数量：", selectedItems.length);
    if (selectedItems.length === 0) {
      alert("請至少選擇一個項目進行刪除。");
      return;
    }

    const selectedIds = Array.from(selectedItems).map(item => item.getAttribute("value"));
    //console.log("已选中的项目的值：", selectedIds);
    if (confirm(`確定要刪除編號為 ${selectedIds.join(', ')} 的資料嗎?`)) {
      location.href = 'gym_deletemultiple.php?gym_ids=' + selectedIds.join(',');
    }
  }


  const searchForm = document.querySelector("#search-form");

  searchForm.addEventListener("submit", function (e) {
    e.preventDefault();
    const searchStr = document.querySelector("#search-field").value;
    const fd = new FormData(searchForm);

    fetch('gym-search-api.php', {
      method: 'POST',
      body: fd,
    })
      .then(r => r.json())
      .then(data => {
        originalTable.innerHTML = '';

        if (data.length > 0) {
          data.forEach(item => {
            const row = document.createElement("tr");
            row.innerHTML = `
            <td>
                <input type="checkbox" name="selectedItems[]" value=" ${item.gym_id} ">
              </td>

              <td><a href="javascript: deleteItem(${item.gym_id})">
                  <i class="fa-solid fa-trash-can text-danger"></i>
                </a></td>
              <td>
              ${item.gym_id}
              </td>
              <td>
              ${item.gym_name}
              </td>
              <td>
                <div style="width:100px">
                  <img src= "/main-dev/uploads/${item.gym_photo}" alt="" width='100%'>
                </div>
              </td>
              <td class="text-truncate " style="max-width:200px">
              ${item.gym_description}
              </td>

              <td>
              ${item.begin_time.slice(0, -3)} ~ ${item.end_time.slice(0, -3)}
              </td>

              <td>
              ${item.district_name} ${item.gym_address}
              </td>
              <td>
                <a href="gym_info.php?gym_id=${item.gym_id}">
                  <i class="fa-solid fa-circle-info text-info"></i>
                </a>
              </td>
              <td>
              ${item.created_at.slice(0, -3)}
              </td>
              <td>
                <a href="gym_edit.php?gym_id=${item.gym_id}">
                  <i class="fa-solid fa-file-pen"></i>
                </a>
              </td>
            `;

            originalTable.appendChild(row);
          });
        } else {
          resultTable.innerHTML = '<tr><td colspan="13">No results found.</td></tr>';
        }
      })
  })
  /*
  let district_name = document.querySelector("#district_name");
        let search = document.querySelector("#search");
        search.addEventListener('click', function() {
            let districtVal = district_name.value;
            location.href = 'gym_list.php?text=' + districtVal;
        })
    */
</script>
<?php include './parts/html-foot.php' ?>