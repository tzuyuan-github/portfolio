<?php
require './parts/connect_db.php';

$sql = "SELECT * FROM districts";
$rows = $pdo->query($sql)->fetchAll();
// echo json_encode($rows, JSON_UNESCAPED_UNICODE);

?>

<?php include './parts/html-head.php' ?>

<?php include './parts/navbar.php' ?>

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
          <h5 class="card-title">新增會員</h5>

          <form name="form1" onsubmit="sendData(event)">
            <div class="mb-3">
              <label for="name" class="form-label">姓名</label>
              <input type="text" class="form-control" id="name" name="name">
              <div class="form-text"></div>
            </div>

            <div class="mb-3">
              <label for="gender" class="form-label">性別</label>
              <select class="form-control" name="gender" id="gender">
                <option value="N">--請選擇--</option>
                <option value="M">男</option>
                <option value="F">女</option>
                <option value="N/A">其他</option>
              </select>
              <div class="form-text"></div>
            </div>

            <div class="mb-3">
              <label for="bday" class="form-label">生日</label>
              <input type="date" class="form-control" id="bday" name="bday">
              <div class="form-text"></div>
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">信箱</label>
              <input type="text" class="form-control" id="email" name="email">
              <div class="form-text"></div>
            </div>

            <div class="mb-3">
              <label for="mobile" class="form-label">手機</label>
              <input type="tel" class="form-control" id="mobile" name="mobile">
              <div class="form-text"></div>
            </div>

            <div class="mb-3">
              <label for="username" class="form-label">帳號</label>
              <input type="text" class="form-control" name="username" id="username">
              <div class="form-text"></div>
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">密碼</label>
              <input type="password" class="form-control" name="password" id="password">
              <div class="form-text"></div>
            </div>


            <div class="mb-3">
              <label for="nickname" class="form-label">暱稱</label>
              <input type="text" class="form-control" name="nickname" id="nickname">
              <div class="form-text"></div>
            </div>

            <div class="mb-3">
              <label for="cat1" class="form-label">縣市</label>
              <select class="form-control" name="cat1" id="cat1" onchange="generateCat2List()">
              
                <?php foreach ($rows as $r) :
                  if ($r['parent_sid'] == 0) : ?>
                    <option value="<?= $r['sid'] ?>"><?= $r['district'] ?></option>
                <?php endif;
                endforeach; ?>
              </select>
              <div class="form-text"></div>
            </div>

            <div class="mb-3">
              <label for="cat2" class="form-label">鄉鎮市區</label>
              <select class="form-control" name="cat2" id="cat2">

                <?php foreach ($rows as $r) :
                  if ($r['parent_id'] == $rows['sid']) : ?>
                    <option value="<?= $r['sid'] ?>"><?= $r['district'] ?></option>
                <?php endif;
                endforeach; ?>

              </select>
              <div class="form-text"></div>
            </div>

            <div class="mb-3">
              <label for="address" class="form-label">地址</label>
              <input type="text" class="form-control" name="address" id="address">
              <div class="form-text"></div>
            </div>

            <button type="submit" class="btn-primary">送出</button>
          </form>

          <form name="form2">
            <!-- Q: sendData(event)? -->
            <div class="mb-3">
              <label for="avatar" class="form-label">大頭貼</label>
              <input type="file" class="form-control" name="avatar" id="avatar" accept="image/jpeg,image/png" onchange="f(event)" />
              <div class="form-text"></div>
            </div>
            <!-- Q: Onchange? Change to trigger by button? -->

            <div style="width: 300px;">
              <img id="myimg" src="" alt="" width="100%" />
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>

<?php include './parts/scripts.php' ?>

<script>
  const name_in = document.form1.name;
  const gender_in = document.form1.gender;
  const bday_in = document.form1.bday;
  const email_in = document.form1.email;
  const mobile_in = document.form1.mobile;
  const fields = [name_in, gender_in, bday_in, email_in, mobile_in];

  const cats = <?= json_encode($rows, JSON_UNESCAPED_UNICODE) ?>;
  // Q: What for?

  const cat1 = document.querySelector('#cat1')
  const cat2 = document.querySelector('#cat2')

  function generateCat2List() {
    const cat1Val = cat1.value;
    let str = "";
    for (let item of cats) {
      if (+item.parent_sid === +cat1Val) {
        str += `<option value="${item.sid}">${item.district}</option>`;
      }
    }
    cat2.innerHTML = str;
  }
  generateCat2List(); // 一進來就呼叫

  function validateEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
  }

  function validateMobile(mobile) {
    const re = /^09\d{2}-?\d{3}-?\d{3}$/;
    return re.test(mobile);
  }

  function sendData(e) {
    e.preventDefault();

    fields.forEach(field => {
      field.style.border = '1px solid #d1d3e2';
      field.nextElementSibling.innerHTML = '';
    })

    let isPass = true;
    if (name_in.value.length < 2) {
      isPass = false;
      name_in.style.border = '2px solid red';
      name_in.nextElementSibling.innerHTML = '請填寫正確的姓名';
    }

    if (gender_in.value === "N") {
      isPass = false;
      gender_in.style.border = '2px solid red';
      gender_in.nextElementSibling.innerHTML = '此欄為必填欄位';
    }

    if (!(bday_in.value)) {
      isPass = false;
      bday_in.style.border = '2px solid red';
      bday_in.nextElementSibling.innerHTML = '此欄為必填欄位';
    }

    if (!validateEmail(email_in.value)) {
      isPass = false;
      email_in.style.border = '2px solid red';
      email_in.nextElementSibling.innerHTML = '請填寫正確的 Email';
    }

    if (mobile_in.value && !validateMobile(mobile_in.value)) {
      isPass = false;
      mobile_in.style.border = '2px solid red';
      mobile_in.nextElementSibling.innerHTML = '請填寫正確的手機號碼';
    }

    if (!isPass) {
      return;
    }

    const fd = new FormData(document.form1);
    fetch('member-add-api.php', {
        method: 'POST',
        body: fd,
      })
      .then(r => r.json())
      .then(data => {
        console.log({
          data
        });
        if (data.success) {
          // alert('資料新增成功');
          // location.href = "member.php"
          sendData2();
        } else {
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
  };

  function sendData2() {
    const fd2 = new FormData(document.form2);
    fetch('member-add-img-api.php', {
        method: 'POST',
        body: fd2,
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          alert('資料新增成功');
          location.href = "member.php"
        }
      })
      .catch(ex => console.log(ex))
  }

  const f = (e) => {
    const el = e.currentTarget;
    const reader = new FileReader();

    // myimg.src = URL.createObjectURL(el.files[0]);
    // console.log(el.files);

    reader.onload = function(event) {
      myimg.src = reader.result;
    }
    reader.readAsDataURL(el.files[0]);
    // Q: use URLobject instead of this?

    // el.setAttribute("data-target", reader.result);
    // console.log(el.getAttribute("data-target"));

  };
</script>

<?php include './parts/html-foot.php' ?>