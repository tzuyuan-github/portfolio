<?php include './parts/html-head.php' ?>
<?php include './parts/navbar.php' ?>

<?php
require './parts/connect_db.php';

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$perPage = 10;

$t_sql = "SELECT COUNT(*) FROM member";
$totalRows = $pdo->query($t_sql)->fetch(PDO::FETCH_NUM)[0];
$totalPages = ceil($totalRows / $perPage);

/*
$sql = sprintf(
  "SELECT * FROM member ORDER BY member_id ASC LIMIT %s, %s",
  ($page - 1) * $perPage,
  $perPage
);


$rows = $pdo->query($sql)->fetchAll();
*/

/*
$sql = sprintf(
  "SELECT member.*, districts.district FROM member
  JOIN districts ON member.city = districts.sid
  JOIN districts ON member.district = districts.sid
  ORDER BY member.member_id ASC
  LIMIT %s, %s",
  ($page - 1) * $perPage,
  $perPage
);

$rows = $pdo->query($sql)->fetchAll();
*/


$sql = sprintf(
  "SELECT member.*, mcity.district AS city_district, mdistrict.district AS member_district
  FROM member
  LEFT JOIN districts AS mcity ON member.city = mcity.sid
  LEFT JOIN districts AS mdistrict ON member.district = mdistrict.sid
  ORDER BY member.member_bday DESC
  LIMIT %s, %s",
  ($page - 1) * $perPage,
  $perPage
);

$rows = $pdo->query($sql)->fetchAll();
?>

<div class="container-fluid">
  <div class="row">
    <div class="col table-responsive scroll">

      <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" id="search-form" name="search-form">
        <div class="input-group">
          <input type="text" id="search-field" name="search-field" class="form-control bg-light border-0 small" placeholder="搜尋會員" aria-label="Search" aria-describedby="basic-addon2" />
          <div class="input-group-append">
            <button class="btn btn-primary" type="submit">
              <i class="fas fa-search fa-sm"></i>
            </button>
          </div>
        </div>
      </form>

      <nav aria-label="Page navigation example" id="pageIndex">
        <ul class="pagination">
          <li class="page-item <?= $page === 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=1">
              <i class="fa-solid fa-angles-left"></i></a>
          </li>
          <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
              <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
            </li>
          <?php endfor; ?>
          <li class="page-item <?= $page === $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $totalPages ?>">
              <i class="fa-solid fa-angles-right"></i></a>
          </li>
        </ul>
      </nav>

      <a href="member-add.php">新增</a>
      <div class="btn btn-danger" onclick="deleteMultiple(event)"><i class="fa-solid fa-trash-can text-white"></i>
        刪除勾選的<span id="selectedCount"></span>筆資料</div>
      <div><?= "$totalRows / $totalPages" ?></div>
      <table class="table table-bordered table-striped">
        <thead>
          <tr>

            <th>
              <input type="checkbox" id="selectAllCheckbox" ?>
            </th>
            <th scope="col">
              <i class="fa-solid fa-trash-can"></i>
            </th>
            <th scope="col">編號</th>
            <th scope="col">身分證字號</th>
            <th scope="col">姓名</th>
            <th scope="col">性別</th>
            <th scope="col">生日<a href="javascript:void(0)" onclick="sortData()"><i class="fa-solid fa-caret-down"></i></a></th>
            <th scope="col">信箱</th>
            <th scope="col">手機</th>
            <th scope="col">帳號</th>
            <th scope="col">地址</th>
            <th scope="col">
              <i class="fa-solid fa-file-pen"></i>
            </th>
          </tr>
        </thead>

        <tbody id="original-table">

          <?php foreach ($rows as $r) : ?>
            <tr>
              <td>
                <input type="checkbox" name="selectedItems[]" value="<?= $r['member_id'] ?>">
              </td>

              <td>
                <a href="javascript: deleteItem(<?= $r['member_id'] ?>)">
                  <i class="fa-solid fa-trash-can"></i>
                </a>
              </td>

              <td><?= $r['member_id'] ?></td>
              <td><?= $r['member_national_id'] ?></td>
              <td><?= $r['member_name'] ?></td>
              <td><?= $r['member_gender'] ?></td>
              <td><?= $r['member_bday'] ?></td>
              <td><?= $r['member_email'] ?></td>
              <td><?= $r['member_mobile'] ?></td>
              <td><?= $r['member_username'] ?></td>
              <td><?= $r['city_district'] . $r['member_district'] . $r['address'] ?></td>


              <td>
                <a href="member-edit.php?member_id=<?= $r['member_id'] ?>"><i class="fa-solid fa-file-pen"></i></a>
              </td>
            </tr>
          <?php endforeach ?>

        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include './parts/scripts.php' ?>

<script>
  const originalTable = document.querySelector("#original-table");

  function deleteItem(member_id) {
    if (confirm(`確定要刪除會員編號為 ${member_id} 的資料嗎?`)) {
      location.href = 'member-delete.php?member_id=' + member_id;
    }
  }

  function deleteMultiple(event) {
    const selectedItems = document.querySelectorAll('input[name="selectedItems[]"]:checked');
    // console.log("已选中的项目数量：", selectedItems.length);
    if (selectedItems.length === 0) {
      alert("請至少選擇一個項目進行刪除。");
      return;
    }

    const selectedIds = Array.from(selectedItems).map(item => item.getAttribute("value"));
    console.log("已选中的项目的值：", selectedIds);
    if (confirm(`確定要刪除編號為${selectedIds.join(', ')}的資料嗎?`)) {
      location.href = 'member-delete-multi.php?member_id=' + selectedIds.join(',');
    }
  }

  const selectAllCheckbox = document.querySelector('#selectAllCheckbox');
  const checkboxes = document.querySelectorAll('input[name="selectedItems[]"]');
  
  selectAllCheckbox.addEventListener('click', function() {
    checkboxes.forEach(function(checkbox) {
      checkbox.checked = selectAllCheckbox.checked;
  });
});

  const searchForm = document.querySelector("#search-form");

  searchForm.addEventListener("submit", function(e) {
    e.preventDefault();
    const searchStr = document.querySelector("#search-field").value;
    const fd = new FormData(searchForm);

    fetch('member-search-api.php', {
        method: 'POST',
        body: fd,
      })
      .then(r => r.json())
      .then(data => {
        originalTable.innerHTML = ``;

        if (data.length > 0) {
          data.forEach(item => {
            const row = document.createElement("tr");
            row.innerHTML = `
              <td>
                <a href="javascript: deleteItem(${item.member_id})">
                  <i class="fa-solid fa-trash-can"></i>
                </a>
              </td>

              <td>${item.member_id}</td>
              <td>${item.member_national_id}</td>
              <td>${item.member_name}</td>
              <td>${item.member_gender}</td>
              <td>${item.member_bday}</td>
              <td>${item.member_email}</td>
              <td>${item.member_mobile}</td>
              <td>${item.member_username}</td>
              <td>${item.city}</td>
              <td>${item.district}</td>

              <td>
                <a href="member-edit.php?member_id=${item.member_id}"><i class="fa-solid fa-file-pen"></i></a>
              </td>
            `;

            originalTable.appendChild(row);
          });
        } else {
          resultTable.innerHTML = '<tr><td colspan="13">No results found.</td></tr>';
        }
      })
  })

  function sortData() {
    let currentUrl = window.location.href;
    console.log(window.location.href);
    let number = currentUrl.split('=');
    console.log(currentUrl.split('='));
    console.log(number[1]);
    // newUrl = `member-sort-bday.php?page=${number[1]}`
    newUrl = `member-sort-bday.php`
    window.location.href = newUrl;




  }

  /*
  function sortData() {
    const urlParams = new URLSearchParams(window.location.search);
    const page = urlParams.get('page');
    let sortFlag = urlParams.get('sort');
  

    // Default sorting to member_id if not specified
    if (!sortFlag) {
      sortFlag = null;
    } else {
      sortFlag = 'sort_by_birthday';
    }

  
    if (!sortFlag || sortFlag !== 'sort_by_birthday') {
      sortFlag = 'sort_by_birthday';
    } else {
      sortFlag = null;
    }

    const newUrl = `?page=${page}&sort=${sortFlag || ''}`;
    history.replaceState({}, '', newUrl);

    fetch('member-sort-api.php?page=' + page + '&sort=' + sortFlag, {
        method: 'POST',
        body: JSON.stringify({
          page,
          sort: sortFlag,
        }),
        headers: {
          'Content-Type': 'application/json',
        },
      })
      .then(r => r.json())
      .then(data => {
        originalTable.innerHTML = ``;

        if (data.length > 0) {
          data.forEach(item => {
            const row = document.createElement("tr");
            row.innerHTML = `
              <td>
                <a href="javascript: deleteItem(${item.member_id})">
                  <i class="fa-solid fa-trash-can"></i>
                </a>
              </td>

              <td>${item.member_id}</td>
              <td>${item.member_category}</td>
              <td>${item.member_national_id}</td>
              <td>${item.member_name}</td>
              <td>${item.member_gender}</td>
              <td>${item.member_bday}</td>
              <td>${item.member_email}</td>
              <td>${item.member_mobile}</td>
              <td>${item.member_username}</td>
              <td>${item.member_password}</td>
              <td>${item.member_nickname}</td>
              <td>${item.member_security_q_id}</td>
              <td>${item.member_security_a}</td>

              <td>
                <a href="member-edit.php?member_id=${item.member_id}"><i class="fa-solid fa-file-pen"></i></a>
              </td>
            `;

            originalTable.appendChild(row);
          })
        }
      })

  }
  */


  /*
  window.addEventListener('load', function() {
    originalTable.innerHTML = ``;
    const birthdayHeader = document.querySelector('th[scope="col"] a');
    birthdayHeader.click();
  });
  */
</script>

<?php include './parts/html-foot.php' ?>