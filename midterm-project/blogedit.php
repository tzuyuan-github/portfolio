<?php

require './parts/connect_db.php';


if (isset($_SESSION['admin'])) {
  $loggedInUser = $_SESSION['admin'];
  # 获取用户的 ID、昵称等信息

  $nickname = $loggedInUser['nickname'];
  $userId = $loggedInUser['member_id'];
} else {
}


// 取得資料的 PK
$BlogArticle_ID = isset($_GET['BlogArticle_ID']) ? intval($_GET['BlogArticle_ID']) : 0;

if (empty($BlogArticle_ID)) {
  header('Location: bloglist.php');
  exit; // 結束程式
}

#撈前面資料
$sql = "SELECT * FROM bloglist WHERE BlogArticle_ID={$BlogArticle_ID}";
$row = $pdo->query($sql)->fetch();
if (empty($row)) {
  header('Location: bloglist.php');
  exit; // 結束程式
}


#給blogclass用的
$sqlclass = "SELECT * FROM blogclass";
$rowsclass = $pdo->query($sqlclass)->fetchAll();


#echo json_encode($row, JSON_UNESCAPED_UNICODE);
$title = '編輯資料';

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
          <h5 class="card-title">編輯資料</h5>

          <form name="form1" onsubmit="sendData(event)">
            <input type="hidden" name="BlogArticle_ID" value="<?= $row['BlogArticle_ID'] ?>">
            <div class="mb-3">
              <label for="Member_ID" class="form-label">會員ID</label>
              <input type="text" class="form-control" id="Member_ID" name="Member_ID" value="<?= htmlentities($row['Member_ID']) ?>">
              <div class="form-text"></div>
            </div>
            <div class="mb-3">
              <label for="BlogClass_ID" class="form-label">文章分類</label>
              <select class="form-select" name="BlogClass_ID" id="BlogClass_ID" onchange="generateCate2List()">
                <?php foreach ($rowsclass as $r) :
                  // if ($r['parent_sid'] == '0') : 
                ?>
                  <option value="<?= $r['BlogClass_ID'] ?>" <?= $r['BlogClass_ID'] == $row['BlogClass_ID'] ? 'selected' : "" ?>><?= $r['BlogClass_content'] ?></option>
                <?php
                // endif;
                endforeach

                ?>

              </select>

              <div class="form-text"></div>
              <!-- <button class="btn btn-danger" type="button">btn 預設的 type 為 submit</button> -->
            </div>
            <div class="mb-3">
              <label for="BlogArticle_Title" class="form-label">標題</label>
              <input type="text" class="form-control" id="BlogArticle_Title" name="BlogArticle_Title" value="<?= htmlentities($row['BlogArticle_Title']) ?>">
              <div class="form-text"></div>
            </div>
            <div class="mb-3">
              <label for="BlogArticle_photo" class="form-label">請上傳文章首圖</label><br>
              <input id="BlogArticle_photo" name="BlogArticle_photo" style="width: 200px;"  hidden
              value="<?= htmlentities($row['BlogArticle_photo']) ?>">

              <span style="cursor: pointer;"  onclick="triggerUpload('BlogArticle_photo')">
                <i class="fa-solid fa-circle-plus" style="color: #580dc9;"></i>
              </span>
              <br><br>
              <div style="width: 300px">
              
                <img src="/main-dev/uploads/<?=htmlentities($row['BlogArticle_photo']) ?> " alt="" id="abcimg" width="100%"  />
                <img src=" " alt="" id="BlogArticle_photo_img" width="100%" />
              
              </div>
            </div>
            <div class="mb-3">
              <label for="BlogArticle_content" class="form-label">文章內容</label>
              <textarea class="form-control" name="BlogArticle_content" id="BlogArticle_content" cols="30" rows="3"><?= htmlentities($row['BlogArticle_content']) ?></textarea>
              <div class="form-text"></div>
            </div>

            <button type="submit" class="btn btn-primary">修改</button>
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



  function sendData(e) {
    e.preventDefault(); // 不要讓表單以傳統的方式送出

    BlogArticle_Title_in.style.border = '1px solid #CCCCCC';
    BlogArticle_Title_in.nextElementSibling.innerHTML = '';

    BlogArticle_content_in.style.border = '1px solid #CCCCCC';
    BlogArticle_content_in.nextElementSibling.innerHTML = '';

    BlogArticle_photo_in.style.border = '1px solid #CCCCCC';
    BlogArticle_photo_in.nextElementSibling.innerHTML = '';

    // TODO: 資料在送出之前, 要檢查格式
    let isPass = true; // 有沒有通過檢查

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

    if (!isPass) {
      return; // 沒有通過就不要發送資料
    }
    // 建立只有資料的表單
    const fd = new FormData(document.form1);

    fetch('blogedit-api.php', {
        method: 'POST',
        body: fd, // 送出的格式會自動是 multipart/form-data
      }).then(r => r.json())
      .then(data => {
        console.log({
          data
        });
        if (data.success) {
          alert('資料編輯成功');
          location.href = "./bloglist.php"
        } else {
          alert('資料未填寫');
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
            const abcimg =document.querySelector(`#abcimg`);
            abcimg.style.display = 'none';
            document.form1[uploadFieldId].value = data.file
            document.querySelector(`#${uploadFieldId}_img`).src = "/main-dev/uploads/" + data.file;
          }



        }
        uploadFieldId = null;
      });
  }
</script>