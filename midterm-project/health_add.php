<?php
require './parts/connect_db.php';

if (!isset($_SESSION['admin'])) {
  header('Location: login.php');
  exit();
}

$pageName = 'add';
$title = '新增';
// 從表單拿資料
$sql = "SELECT * FROM workout_category";
$rows = $pdo->query($sql)->fetchAll();

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
      </div>
    </div>
  </nav>
</div>


<!-- 這是給  出現錯誤的時候才用的 -->
<style>
  form .form-text {
    color: red;
  }

  .btn-container {
    display: flex;
    flex-direction: column;
    align-items: left;
  }


  .btn {
    width: 150px;
    margin: 5px 0;
  }
</style>



<div class="container">
  <div class="row">
    <div class="col-10">
      <div class="card">

        <div class="card-body">
          <h5 class="card-title">新增資料</h5>

          <form name="form1" onsubmit="sendData(event)">
            <div class="mb-3">
              <label for="member_id" class="form-label">會員編號</label>
              <input type="text" class="form-control" id="member_id" name="member_id">
              <div class="form-text"></div>
            </div>
            <div class="mb-3">
              <label for="fdentry_datetime" class="form-label">運動日記 - 紀錄日期時間</label>
              <input type="time" class="form-control" id="fdentry_datetime" name="fdentry_datetime">
              <div class="form-text"></div>
            </div>

            <div class="mb-3">
              <label for="category1" class="form-label">重訓類別</label>
              <select class="form-control" name="e1_sid" id="category1" onchange="generateCate2List()">

                <?php foreach ($rows as $r):
                  if ($r['parent_exercise_sid'] == '0'): ?>
                    <option value="<?= $r['exercise_sid'] ?>">
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
                <!-- 第二個下拉選單的選項將通過 JavaScript 動態生成 -->
                <?php foreach ($rows as $r):
                  if ($r['parent_exercise_sid'] == $r['exercise_sid']): ?>
                    <option value="<?= $r['exercise_sid'] ?>">
                      <?= $r['exercise_name'] ?>
                    </option>
                  <?php endif;
                endforeach; ?>
              </select>
     
              <div class="form-text"></div>
            </div>



            <div class="mb-3">
              <label for="wtraining_weight" class="form-label">重訓重量</label>
              <input type="text" class="form-control" id="wtraining_weight" name="wtraining_weight">
              <div class="form-text"></div>
            </div>
            <div class="mb-3">
              <label for="wtraining_reps" class="form-label">重訓次數</label>
              <input type="text" class="form-control" id="wtraining_reps" name="wtraining_reps">
              <div class="form-text"></div>
            </div>
            <div class="mb-3">
              <label for="wtraining_set" class="form-label">重訓組數</label>
              <input type="text" class="form-control" id="wtraining_set" name="wtraining_set">
              <div class="form-text"></div>
            </div>

            <div class="mb-3 btn-container">
              <label for="avatar" class="form-label">健身成果</label><br>
              <input type="hidden" id="avatar" name="avatar" style="width: 200px;">
              <button type="button" class="btn btn-primary" onclick="triggerUpload('avatar')">上傳圖片</button>
              <div style="width: 300px">
                <img src="" alt="" id="avatar_img" width="100%" />
              </div>
            </div>

            <div class="mb-3 btn-container">
              <button type="submit" class="btn btn-primary">送出</button>
            </div>
          </form>


          <form name="formuploadphoto" hidden>
            <input type="file" name="avatar" accept="image/*" onchange="uploadFile()" />
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include './parts/scripts.php' ?>



<script>

  const member_id_in = document.form1.member_id;

  const exercise_name_in = document.form1.exercise_name;
  const fdentry_datetime_in = document.form1.fdentry_datetime;
  const wtraining_weight_in = document.form1.wtraining_weight;
  const wtraining_reps_in = document.form1.wtraining_reps;
  const wtraining_set_in = document.form1.wtraining_set;

  const cates = <?= json_encode($rows) ?>; // 将 PHP 数据转换为 JavaScript 对象

  const category1 = document.querySelector('#category1')
  const category2 = document.querySelector('#category2')




  function generateCate2List() {
    const cate1Val = category1.value;
    let str = "";
    for (let item of cates) {
      if (+item.parent_exercise_sid === +cate1Val) {
        str += `<option value="${item.exercise_sid}">${item.exercise_name}</option>`;
      }
    }
    category2.innerHTML = str;
  }
  generateCate2List(); // 一進來就呼叫



  function sendData(e) {
    e.preventDefault(); // 不要讓表單以傳統的方式送出

    // 外觀要回復原來的狀態
    member_id_in.style.border = '1px solid #CCCCCC';
    member_id_in.nextElementSibling.innerHTML = '';
  
    fdentry_datetime_in.style.border = '1px solid #CCCCCC';
    fdentry_datetime_in.nextElementSibling.innerHTML = '';
    wtraining_weight_in.style.border = '1px solid #CCCCCC';
    wtraining_weight_in.nextElementSibling.innerHTML = '';
    wtraining_reps_in.style.border = '1px solid #CCCCCC';
    wtraining_reps_in.nextElementSibling.innerHTML = '';
    wtraining_set_in.style.border = '1px solid #CCCCCC';
    wtraining_set_in.nextElementSibling.innerHTML = '';

    // 用来声明和初始化一个名为 isPass 的变量。这个变量通常用于控制条件语句，以确定某些操作是否可以继续执行。
    let isPass = true;

    //必填欄位
    if (!(member_id_in.value)) {
      isPass = false;
      member_id_in.style.border = '2px solid red';
      member_id_in.nextElementSibling.innerHTML = '此欄為必填欄位';
    }

    if (!(wtraining_weight_in.value)) {
      isPass = false;
      wtraining_weight_in.style.border = '2px solid red';
      wtraining_weight_in.nextElementSibling.innerHTML = '此欄為必填欄位';
    }
    if (!(wtraining_reps_in.value)) {
      isPass = false;
      wtraining_reps_in.style.border = '2px solid red';
      wtraining_reps_in.nextElementSibling.innerHTML = '此欄為必填欄位';
    }
    if (!(wtraining_set_in.value)) {
      isPass = false;
      wtraining_set_in.style.border = '2px solid red';
      wtraining_set_in.nextElementSibling.innerHTML = '此欄為必填欄位';
    }


    // 非必填  如果有寫內容且內容錯誤 才要跳出錯誤訊息
    // if (mobile_in.value && !validateMobile(mobile_in.value)) {
    //   isPass = false;
    //   mobile_in.style.border = '2px solid red';
    //   mobile_in.nextElementSibling.innerHTML = '請填寫正確的手機號碼';
    // }
    // 沒有通過就不要發送資料
    if (isPass) {
      // 建立只有資料的表單
      const fd = new FormData(document.form1);

      fetch('health_add-api.php', {
        method: 'POST',
        body: fd, // 送出的格式會自動是 multipart/form-data
      }).then(r => r.json())
        .then(data => {
          console.log({
            data
          });
          if (data.success) {
            alert('資料新增成功');
            location.href = "./health_list.php"
          } else {
            //alert('發生問題');
            for (let n in data.errors) {
              console.log(`n: ${n}`);
              if (document.form1[n]) {
                const input = document.form1[n];
                input.style.border = '2px solid red';
                input.nextElementSibling.innerHTML = data.errors[n];
              }
            }
          }
        })
        .catch(ex => console.log(ex))
    }
  }



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
            document.form1[uploadFieldId].value = data.file
            document.querySelector(`#${uploadFieldId}_img`).src = "/main-dev/uploads/" + data.file;
          }



        }
        uploadFieldId = null;
      });
  }


</script>
<?php include './parts/html-foot.php' ?>