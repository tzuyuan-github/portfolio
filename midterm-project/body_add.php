<?php
require './parts/connect_db.php';

if (!isset($_SESSION['admin'])) {
  header('Location: login.php');
  exit();
}

$pageName = 'add';
$title = '新增';

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

<!-- 這是給  出現錯誤的時候才用的 -->
<style>
  form .form-text {
    color: red;
  }
</style>



<div class="container">
  <div class="row">
    <div class="col-6">
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
              <label for="height_update" class="form-label">更新身高</label>
              <input type="text" class="form-control" id="height_update" name="height_update">
              <div class="form-text"></div>
            </div>
            <div class="mb-3">
              <label for="weight_update" class="form-label">更新體重</label>
              <input type="text" class="form-control" id="weight_update" name="weight_update">
              <div class="form-text"></div>
            </div>
            <div class="mb-3">
              <label for="waistline_update" class="form-label">更新腰圍</label>
              <input type="text" class="form-control" id="waistline_update" name="waistline_update">
              <div class="form-text"></div>
            </div>
            <div class="mb-3">
              <label for="bodyfat_update" class="form-label">更新體脂</label>
              <input type="text" class="form-control" id="bodyfat_update" name="bodyfat_update">
              <div class="form-text"></div>
            </div>
            <div class="mb-3">
              <label for="bmi_update" class="form-label">目前BMI</label>
              <input type="text" class="form-control" id="bmi_update" name="bmi_update">
              <div class="form-text"></div>
            </div>
            <div class="mb-3">
              <label for="bmr_update" class="form-label">目前BMR</label>
              <input type="text" class="form-control" id="bmr_update" name="bmr_update">
              <div class="form-text"></div>
            </div>

            <button type="submit" class="btn btn-primary">送出</button>
          </form>

        </div>
      </div>
    </div>
  </div>


</div>
<?php include './parts/html-foot.php' ?>
<?php include './parts/scripts.php' ?>
<script>

  const member_id_in = document.form1.member_id;
  const height_update_in = document.form1.height_update;
  const weight_update_in = document.form1.weight_update;
  const waistline_update_in = document.form1.waistline_update;
  const bodyfat_update_in = document.form1.bodyfat_update;
  const bmi_update_in = document.form1.bmi_update;
  const bmr_update_in = document.form1.bmr_update;
  // const email_in = document.form1.email;
  // const mobile_in = document.form1.mobile;
  // const fields = [name_in, email_in, mobile_in];

  // email的格式
  // function validateEmail(email) {
  //   const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  //   return re.test(email);
  // }

  //手機的格式
  // function validateMobile(mobile) {
  //   const re = /^09\d{2}-?\d{3}-?\d{3}$/;
  //   return re.test(mobile);
  // }

  function sendData(event) {
    event.preventDefault(); // 不要讓表單以傳統的方式送出

    // 外觀要回復原來的狀態
    member_id_in.style.border = '1px solid #CCCCCC';
    member_id_in.nextElementSibling.innerHTML = '';
    height_update_in.style.border = '1px solid #CCCCCC';
    height_update_in.nextElementSibling.innerHTML = '';
    weight_update_in.style.border = '1px solid #CCCCCC';
    weight_update_in.nextElementSibling.innerHTML = '';
    waistline_update_in.style.border = '1px solid #CCCCCC';
    waistline_update_in.nextElementSibling.innerHTML = '';
    bodyfat_update_in.style.border = '1px solid #CCCCCC';
    bodyfat_update_in.nextElementSibling.innerHTML = '';
    bmi_update_in.style.border = '1px solid #CCCCCC';
    bmi_update_in.nextElementSibling.innerHTML = '';
    bmr_update_in.style.border = '1px solid #CCCCCC';
    bmr_update_in.nextElementSibling.innerHTML = '';

    // fields.forEach(field => {
    //   field.style.border = '1px solid #CCCCCC';
    //   field.nextElementSibling.innerHTML = '';
    // })    

    // TODO: 資料在送出之前, 要檢查格式
    let isPass = true; // 有沒有通過檢查

    //判斷name的格式 以及給出提醒
  

    /*
    if (name_in.value.length < 2) {
      isPass = false;
      name_in.style.border = '2px solid red';
      name_in.nextElementSibling.innerHTML = '請填寫正確的姓名';
      //属性返回指定元素之后的下一个兄弟元素（相同节点树层中的下一个元素节点）
    }  

    //判斷email 如果不是對的內容
    if (!validateEmail(email_in.value)) {
      isPass = false;
      email_in.style.border = '2px solid red';
      email_in.nextElementSibling.innerHTML = '請填寫正確的 Email';
    }
    */

    // 非必填  如果有寫內容且內容錯誤 才要跳出錯誤訊息
    // if (mobile_in.value && !validateMobile(mobile_in.value)) {
    //   isPass = false;
    //   mobile_in.style.border = '2px solid red';
    //   mobile_in.nextElementSibling.innerHTML = '請填寫正確的手機號碼';
    // }

    if (!isPass) {
      return; // 沒有通過就不要發送資料
    }

    // 建立只有資料的表單
    const fd = new FormData(document.form1);

    fetch('body_add-api.php', {
        method: 'POST',
        body: fd, // 送出的格式會自動是 multipart/form-data
      }).then(r => r.json())
      .then(data => {
        console.log({
          data
        });
        if (data.success) {
          alert('資料新增成功');
          location.href = "./body_list.php"
        }else {
          //alert('發生問題');
          for(let n in data.errors){
            console.log(`n: ${n}`);
            if(document.form1[n]){
              const input = document.form1[n];
              input.style.border = '2px solid red';
              input.nextElementSibling.innerHTML = data.errors[n];
            }
          }
        }


      })
      .catch(ex => console.log(ex))
  }
</script>
<?php include './parts/html-foot.php' ?>