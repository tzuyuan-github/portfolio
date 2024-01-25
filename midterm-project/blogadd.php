<?php


require './parts/connect_db.php';

$pageName = 'blogadd';
$title = '新增';

?>
<?php include './parts/html-head.php' ?>
<?php include './parts/navbar.php' ?>

<?php
# 检查是否已登录
if (isset($_SESSION['admin'])) {
  $loggedInUser = $_SESSION['admin'];
  # 获取用户的 ID、昵称等信息
  // $userId = $loggedInUser['member_id'];
  $nickname = $loggedInUser['nickname'];
}
?>


<?php
# 检查是否已登录
if (!isset($_SESSION['admin'])) {
  echo "<script>alert('警告：請先會員登入'); location.href = ' login.php';</script>";
  // header('Location: login.php'); 
}
?>

<?php
#給blogclass用的
$sqlclass = "SELECT * FROM blogclass";
$rowsclass = $pdo->query($sqlclass)->fetchAll();
?>


<!-- 這是給  出現錯誤的時候才用的 -->
<style>
  form .form-text {
    color: red;員ID順序
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
              <label for="Member_ID" class="form-label">會員ID</label>
              <input type="text" class="form-control" id="Member_ID" name="Member_ID">
            </div>
        
        <div class="mb-3">
          <label for="BlogClass_ID" class="form-label">文章分類</label>
          <select class="form-select" name="BlogClass_ID" id="BlogClass_ID" onchange="generateCate2List()">
            <?php foreach ($rowsclass as $r) :
              // if ($r['parent_sid'] == '0') : 
            ?>
              <option value="<?= $r['BlogClass_ID'] ?>"><?= $r['BlogClass_content'] ?></option>
            <?php
            // endif;
            endforeach ?>
          </select>
          <div class="form-text"></div>
        </div>
        <div class="mb-3">
          <label for="BlogArticle_Title" class="form-label">標題</label>
          <br>
          <input type="text" class="form-control" id="BlogArticle_Title" name="BlogArticle_Title">
          <div class="form-text"></div>
        </div>
        <div class="mb-3">
          <label for="BlogArticle_photo" class="form-label">請上傳文章首圖</label><br>
          <input id="BlogArticle_photo" name="BlogArticle_photo" style="width: 200px;" hidden>
          <div class="form-text"></div>

          <span style="cursor: pointer;" onclick="triggerUpload('BlogArticle_photo')">
            <i class="fa-solid fa-circle-plus" style="color: #580dc9;"></i>
          </span>
          <div style="width: 300px">
            <img src="" alt="" id="BlogArticle_photo_img" width="100%" />
          </div>
          <!-- 
              <input id="pic2" name="pic2" style="width: 600px;" >
              <div style="cursor: pointer;" onclick="triggerUpload('pic2')">點選上傳第2張圖</div>
              <div style="width: 300px">
                <img src="" alt="" id="pic2_img" width="100%" />
              </div>


              <input id="pic3" name="pic3" style="width: 600px;" >
              <div style="cursor: pointer;" onclick="triggerUpload('pic3')">點選上傳第3張圖</div>
              <div style="width: 300px">
                <img src="" alt="" id="pic3_img" width="100%" />
              </div> -->
        </div>
        <div class="mb-3">
          <label for="BlogArticle_content" class="form-label">內容</label>
          <textarea class="form-control" name="BlogArticle_content" id="BlogArticle_content" cols="30" rows="3"></textarea>
          <div class="form-text"></div>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
        </form>

      </div>
    </div>
  </div>
</div>


</div>

<?php include './parts/scripts.php' ?>
<script>
  const BlogArticle_Title_in = document.form1.BlogArticle_Title; //標題
  const BlogArticle_content_in = document.form1.BlogArticle_content; //內文
  const BlogArticle_photo_in = document.form1.BlogArticle_photo; //內文
  // const email_in = document.form1.email;
  // const mobile_in = document.form1.mobile;
  // const fields = [BlogArticle_Title_in, email_in, mobile_in,BlogArticle_content];

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

  function sendData(e) {
    e.preventDefault(); // 不要讓表單以傳統的方式送出

    // 外觀要回復原來的狀態
    BlogArticle_Title_in.style.border = '1px solid #CCCCCC';
    BlogArticle_Title_in.nextElementSibling.innerHTML = '';

    BlogArticle_content_in.style.border = '1px solid #CCCCCC';
    BlogArticle_content_in.nextElementSibling.innerHTML = '';

    BlogArticle_photo_in.style.border = '1px solid #CCCCCC';
    BlogArticle_photo_in.nextElementSibling.innerHTML = '';
    // fields.forEach(field => {
    //   field.style.border = '1px solid #CCCCCC';
    //   field.nextElementSibling.innerHTML = '';
    // })    

    // TODO: 資料在送出之前, 要檢查格式
    let isPass = true; // 有沒有通過檢查

    //判斷name的格式 以及給出提醒



    if (BlogArticle_Title_in.value.length < 1) {
      isPass = false;
      BlogArticle_Title_in.style.border = '2px solid red';
      BlogArticle_Title_in.nextElementSibling.innerHTML = '請填寫標題';
      //属性返回指定元素之后的下一个兄弟元素（相同节点树层中的下一个元素节点）
    }


    if (BlogArticle_content_in.value.length < 1) {
      isPass = false;
      BlogArticle_content_in.style.border = '2px solid red';
      BlogArticle_content_in.nextElementSibling.innerHTML = '請填寫內文';
      //属性返回指定元素之后的下一个兄弟元素（相同节点树层中的下一个元素节点）
    }

    if (BlogArticle_photo_in.value.length < 1) {
      isPass = false;
      BlogArticle_photo_in.style.border = '2px solid red';
      BlogArticle_photo_in.nextElementSibling.innerHTML = '請上傳圖片';
      //属性返回指定元素之后的下一个兄弟元素（相同节点树层中的下一个元素节点）
    }
    //判斷email 如果不是對的內容
    /*
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

    fetch('blogadd-api.php', {
        method: 'POST',
        body: fd, // 送出的格式會自動是 multipart/form-data
      }).then(r => r.json())
      .then(data => {
        console.log({
          data
        });
        if (data.success) {
          alert('資料新增成功');
          location.href = "./bloglist.php"
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
</script>
<?php include './parts/html-foot.php' ?>

<form name="formuploadphoto" hidden>
  <input type="file" name="avatar" onchange="uploadFile()" />
</form>


<script>
  let uploadFieldId; // 欄位 Id

  function triggerUpload(fid) {
    uploadFieldId = fid;
    document.formuploadphoto.avatar.click();
  }

  function uploadFile() {
    const fd = new FormData(document.formuploadphoto);

    fetch("upload-img-api圖片上傳_存資料夾.php", {
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