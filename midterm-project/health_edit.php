<?php

require './parts/connect_db.php';

if (!isset($_SESSION['admin'])) {
  header('Location: login.php');
  exit();
}

// 取得資料的 PK
$fdentry_id = isset($_GET['fdentry_id']) ? intval($_GET['fdentry_id']) : 0;

if (empty($fdentry_id)) {
  header('Location: health_list.php');
  exit; // 結束程式
}

$sql = "SELECT * FROM sports_diary WHERE fdentry_id={$fdentry_id}";
// row為總表
$row = $pdo->query($sql)->fetch();

$sql_workout_category = "SELECT * FROM workout_category";
// rows為workout_category內的all
$rows = $pdo->query($sql_workout_category)->fetchAll();


if (empty($row)) {
  header('Location: health_list.php');
  exit; // 結束程式
}

#echo json_encode($row, JSON_UNESCAPED_UNICODE);
$title = '編輯資料';

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
  form .form-text {
    color: red;
  }
</style>

<div class="container">
  <div class="row">
    <div class="col-10">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">編輯資料</h5>

          <form name="form1" onsubmit="sendData(event)">

            <input type="hidden" name="fdentry_id" value="<?= htmlentities($row['fdentry_id']) ?>">

            <div class="mb-3">
              <label for="member_id" class="form-label">會員編號</label>
              <input type="text" class="form-control" id="member_id" name="member_id"
                value="<?= htmlentities($row['member_id']) ?>">
              <div class="form-text"></div>
            </div>

            <div class="mb-3">
              <label for="fdentry_datetime" class="form-label">運動日記 - 紀錄日期時間</label>
              <input type="text" class="form-control" id="fdentry_datetime" name="fdentry_datetime"
                value="<?= htmlentities($row['fdentry_datetime']) ?>">
              <div class="form-text"></div>
            </div>

            <div class="mb-3">
              <label for="category1" class="form-label">重訓類別</label>
              <select class="form-control" name="e1_sid" id="category1" onchange="generateCate2List()">
                <!-- parent_sid=parent_exercise_sid
                sid=exercise_sid
                distinct=category1
                city=category2 -->
                <!-- (rows大表、r小表) -->
                <!-- 如果小表格的parent_exercise_sid=0,獲取下拉的小表格的exercise_sid -->
                <!-- 如果大表格的次分類＝小表格的exercise_sid,預設選取 -->
                <!-- 小表格的主分類 -->
                <?php foreach ($rows as $r):
                  if ($r['parent_exercise_sid'] == 0): ?>
                    <option value="<?= $r['exercise_sid'] ?>" <?php
                      echo ($row['e1_sid'] == $r['exercise_sid']) ?
                        'selected' : '' ?>>
                      <?= $r['exercise_name'] ?>

                    </option>
                  <?php endif;
                endforeach; ?>
              </select>
              <div class="form-text"></div>
            </div>

            <div class="mb-3">
              <label for="category2" class="form-label">重訓名稱</label>
              <select class="form-control" name="e2_sid" id="category2">
                <!-- (rows大表、r小表) -->
                <!-- 如果大表格的次分類=小表格的parent_exercise_sid,獲取下拉的小表格的exercise_sid -->
                <!-- 如果大表格的主分類＝小表格的exercise_sid,預設選取 -->
                <!-- 小表格的主分類 -->
                <?php foreach ($rows as $r):
                  if ($r['parent_exercise_sid'] == $row['e1_sid']): ?>
                    <option value="<?= $r['exercise_sid'] ?>" <?php echo ($r['exercise_sid'] == $row['e2_sid']) ? 'selected' : '' ?>>
                      <?= $r['exercise_name'] ?>
                    </option>
                  <?php endif;
                endforeach; ?>
              </select>
              <div class="form-text"></div>
            </div>

            <div class="mb-3">
              <label for="wtraining_weight" class="form-label">重訓重量</label>
              <input type="text" class="form-control" id="wtraining_weight" name="wtraining_weight"
                value="<?= htmlentities($row['wtraining_weight']) ?>">
              <div class="form-text"></div>
            </div>
            <div class="mb-3">
              <label for="wtraining_reps" class="form-label">重訓次數</label>
              <input type="text" class="form-control" id="wtraining_reps" name="wtraining_reps"
                value="<?= htmlentities($row['wtraining_reps']) ?>">
              <div class="form-text"></div>
            </div>
            <div class="mb-3">
              <label for="wtraining_set" class="form-label">重訓組數</label>
              <input type="text" class="form-control" id="wtraining_set" name="wtraining_set"
                value="<?= htmlentities($row['wtraining_set']) ?>">
              <div class="form-text"></div>
            </div>

            <button type="submit" class="btn btn-primary">修改</button>
          </form>

          <form name="form2">
            <input type="hidden" name="fdentry_id" value="<?= htmlentities($row['fdentry_id']) ?>">

            <!-- <div class="mb-3">
              <label for="avatar" class="form-label">健身成果</label><br>
              <input type="file" class="form-control" name="avatar" id="avatar" accept="image/jpeg,image/png"
                style="width: 200px;" />
              <div class="form-text"></div>
            </div> -->
            <div class="mb-3 btn-container">
              <label for="avatar" class="form-label">健身成果</label><br>
              <input type="hidden" id="avatar" name="avatar" style="width: 200px;">


              <button type="button" class="btn btn-primary" onclick="triggerUpload('avatar')">上傳圖片</button>

              <div style="width: 300px">
                <img src="" alt="" id="avatar" width="100%" />
              </div>
            </div>

            <div style="width: 300px;">
              <img id="myimg" src="/main-dev/uploads/<?= $row['avatar'] ?>" alt="" width="100%" />
              <img src="" alt="" id="avatar" width="100%" />
            </div>

          </form>
          <form name="formuploadphoto" hidden>
            <input type="hidden" name="fdentry_id" value="<?= htmlentities($row['fdentry_id']) ?>">
            <input type="file" name="avatar" accept="image/*" onchange="uploadFile()" />
          </form>


        </div>
      </div>
    </div>
  </div>
</div>

<?php include './parts/html-foot.php' ?>
<?php include './parts/scripts.php' ?>
<script>
  const cates = <?= json_encode($rows, JSON_UNESCAPED_UNICODE) ?>;

  const category1 = document.querySelector('#category1')
  const category2 = document.querySelector('#category2')
  console.log(category2);

  category1.addEventListener('change', function () {
    generateCate2List();
  })

  function generateCate2List() {
    console.log("yay");
    const cate1Val = category1.value;
    let str = "";
    for (let item of cates) {
      if (+item.parent_exercise_sid === +cate1Val) {
        str += `<option value="${item.exercise_sid}">${item.exercise_name}</option>`;
      }
    }
    category2.innerHTML = str;
  }


  // generateCate2List();
  // N: 不可以一進來就呼叫
  // const name_in = document.form1.name;
  // const email_in = document.form1.email;
  // const mobile_in = document.form1.mobile;
  // const fields = [name_in, email_in, mobile_in];

  // function validateEmail(email) {
  //     const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  //     return re.test(email);
  // }

  // function validateMobile(mobile) {
  //     const re = /^09\d{2}-?\d{3}-?\d{3}$/;
  //     return re.test(mobile);
  // }


  function sendData(event) {
    // console.log("document.form1.category2")
    event.preventDefault(); // 不要讓表單以傳統的方式送出

    let isPass = true; // 有沒有通過檢查


    if (!isPass) {
      return; // 沒有通過就不要發送資料
    }
    // 建立只有資料的表單
    const fd = new FormData(document.form1);
    const fd2 = new FormData(document.form2);
    console.log("document.form1.category2")

    fetch('health_edit-api.php', {
      method: 'POST',
      body: fd, // 送出的格式會自動是 multipart/form-data
    }).then(r => r.json())
      .then(data => {
        console.log({
          data
        });
        if (data.success) {
          alert('資料編輯成功');
          location.href = "./health_list.php"
          // sendData2();
        } else {
          // alert('資料沒有修改');
          for (let n in data.errors) {
            console.log(`n: ${n}`);
            // location.href = "health_list.php"
            if (document.form1[n]) {
              const input = document.form1[n];
              input.style.border = '2px solid red';
              input.nextElementSibling.innerHTML = data.errors[n];
            }
          }
        }
      })
      .catch(ex => console.log(ex))
  };

  function sendData2() {
    const fd2 = new FormData(document.form2);
    fetch('health_edit-img-api.php', {
      method: 'POST',
      body: fd2,
    })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          alert('資料編輯成功');
          location.href = `health_list.php`
          // location.href = `health_list.php?page=小於問號等於($t_row['COUNT(*)'] / $perPage) + 1 ?>`
        }
      })
  }

  // const f = (e) => {
  //   const el = e.currentTarget;
  //   const reader = new FileReader();

  //   reader.onload = function (event) {
  //     myimg.src = reader.result;
  //   }

  //   reader.readAsDataURL(el.files[0]);

  // };

  let uploadFieldId; // 欄位 Id

  function triggerUpload(fid) {
    uploadFieldId = fid;
    document.formuploadphoto.avatar.click();
  }

  function uploadFile() {
    const fd = new FormData(document.formuploadphoto);

    fetch("health_upload-img-api_savefolder.php", {
      method: "POST",
      body: fd, // enctype="multipart/form-data"
    })
      .then((r) => r.json())
      .then((data) => {
        if (data.success) {
          if (uploadFieldId) {
            const myimg =document.getElementById('myimg')
            myimg.src = '/main-dev/uploads/'+ data.file
            // document.querySelector(`#${uploadFieldId}_img`).src = "/main-dev/uploads/" + data.file;
          }



        }
        uploadFieldId = null;
      });
  }

</script>

<?php include './parts/html-foot.php' ?>