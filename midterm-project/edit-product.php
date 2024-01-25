<?php
require './parts/connect_db.php';


// 取得資料的PK 設定給sid
$sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;

if (empty($sid)) {
  header("Location: product_list.php");
  exit;
}

$sql = "SELECT * FROM product_list WHERE sid = {$sid}";
$row = $pdo->query($sql)->fetch();
// echo json_encode($row);

$sql_category = "SELECT * FROM product_categories";
$rows_category = $pdo->query($sql_category)->fetchAll();

$pageName = 'edit';
$title = '編輯商品';

?>
<style>
  form .form-text {
    color: red;
  }
</style>

<?php include './parts/html-head.php' ?>
<?php include './parts/navbar.php' ?>
<div class="container mb-4">
  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-body">
          <h3 class="card-title text-gray-800 text-center">編輯商品資料</h3>
          <hr>
          <form name="form1" onsubmit="sendData(event)">
            <!-- 獲得最新的sid -->
            <input type="hidden" name="sid" value="<?= $sid ?>">
            <div class="mb-3">
              <label for="product_id" class="form-label">商品編號</label>
              <input type="text" class="form-control bg-secondary text-light" id="product_id" name="product_id" value="<?php echo isset($row["product_id"]) ? htmlentities($row["product_id"]) : ''; ?>" disabled>
              <div class="form-text"></div>
            </div>
            <div class="mb-3">
              <label for="name" class="form-label">商品名稱</label>
              <input type="text" class="form-control" id="name" name="name" value="<?= htmlentities($row['name']) ?>">
              <div class="form-text"></div>
            </div>
            <div class="mb-3">
              <label for="price" class="form-label">商品價格</label>
              <input type="number" class="form-control" id="price" name="price" value="<?= htmlentities($row['price']) ?>">
              <div class="form-text"></div>
            </div>
            <!-- 商品分類 下拉式選單 -->
            <div class="input-group mb-3">
              <span class="input-group-text">主分類</span>
              <select class="form-select" name="cate1" id="cate1" onchange="generateCate2List()">
                <?php foreach ($rows_category as $r) :
                  if ($r['parent_sid'] == 0) :
                ?>
                    <option value="<?= $r['sid'] ?>" <?php echo ($r['sid'] == $row['main_category']) ? 'selected' : '' ?>><?= $r['name'] ?></option>
                <?php
                  endif;
                endforeach; ?>
              </select>

            </div>
            <div class="input-group mb-3">
              <span class="input-group-text">次分類</span>
              <select class="form-select" name="cate2" id="cate2">
                <?php foreach ($rows_category as $r) :
                  if ($r['parent_sid'] == $row['main_category']) :
                ?>
                    <option value="<?= $r['sid'] ?>" <?php echo ($r['sid'] == $row['category']) ? 'selected' : '' ?>><?= $r['name'] ?></option>
                <?php
                  endif;
                endforeach; ?>
              </select>
            </div>

            <!-- 商品描述 -->
            <div class="mb-3">
              <label for="descriptions" class="form-label">商品描述</label>
              <textarea class="form-control" name="descriptions" id="descriptions" cols="30" rows="3"><?= htmlentities($row['descriptions']) ?></textarea>
              <div class="form-text"></div>
            </div>
            <!-- 庫存數量 -->
            <div class="mb-3">
              <label for="inventory" class="form-label">庫存數量</label>
              <input type="number" class="form-control" id="inventory" name="inventory" value="<?= htmlentities($row['inventory']) ?>">
              <div class="form-text"></div>
            </div>
            <!-- 是否上架 -->
            <div class="mb-3">
              <label for="launch" class="form-label">上架狀態</label>&nbsp;
              <!-- if判斷如果是1 就顯示上架 else顯示下架 -->
              <input type="radio" id="on" name="launch" value="1" <?php if ($row['launch'] === 1) echo 'checked'; ?>>
              <label for="launch">上架</label>&nbsp;
              <input type="radio" id="off" name="launch" value="0" <?php if ($row['launch'] === 0) echo 'checked'; ?>>
              <label for="launch">下架</label>
            </div>
            <!-- 主要商品圖片 -->
            <div class="mb-3">
              <label for="mainImg" class="form-label">主要商品圖片(建議圖片大小 600 x 600px)</label>
              <br />
              <div class="btn btn-secondary uploadButton" style="cursor: pointer" onclick="document.mainImgForm.mainImg.click()">點擊上傳主要圖片</div>
              <div class="form-text"></div>
              <div class="showMainImg" style="width: 100px">
                <?php
                $imgUrl = empty($row['img']) ? './img/default_img.jpg' : './product-imgs/' . $row['img'];
                ?>
                <img src="<?= $imgUrl ?>" alt="" id="mainImg" name="mainImg" width="100%" />
              </div>
            </div>
            <div id="info"></div>
            <!-- 新增商品／取消新增商品 按鈕 -->
            <div class="d-flex justify-content-center mb-3">
              <button type="submit" class="btn btn-warning rounded-pill">確認修改商品</button> &nbsp;
              <button type="button" onclick="cancelSend(event)" class="btn btn-secondary rounded-pill">取消編輯</button>
            </div>
        </div>
        </form>
        <!-- 單一圖片上傳的表單(hidden) -->
        <form name="mainImgForm" hidden>
          <input type="hidden" name="sid" value="<?= $sid ?>">
          <input type="file" name="mainImg" onchange="previewImg(event)">
        </form>
        <!-- 多張圖片上傳的表單(hidden)  -->
        <!-- <form name="moreImgForm" hidden>
          <input type="file" name="moreImg" onchange="uploadMoreImg()" multiple/>
        </form> -->
      </div>
    </div>
  </div>
</div>
</div>

<?php include './parts/scripts.php' ?>
<script>
  // 先拿到欄位參照，因為一開始是空的 沒有值
  const name_in = document.form1.name;
  const price_in = document.form1.price;
  const category = document.form1.category;
  // const selectedCategory = document.form1.category.value;
  const descriptions = document.form1.descriptions;
  const mainImg = document.form1.mainImg;
  // const moreImg = document.form1.moreImg;
  const inventory = document.form1.inventory;
  const launch = document.form1.launch.value;
  const fields = [name_in, price_in, inventory, descriptions];
  const showMainImg = document.querySelector('.showMainImg');
  const uploadButton = document.querySelector(".uploadButton");
  const mainImgElement = document.querySelector("#mainImg");


  // 下拉選單的設定
  // const initVals = {
  //   cate1: 1,
  //   cate2: 5
  // };
  const cates = <?= json_encode($rows_category, JSON_UNESCAPED_UNICODE) ?>;
  const cate1 = document.querySelector('#cate1');
  const cate2 = document.querySelector('#cate2');

  function generateCate2List() {
    const cate1Val = cate1.value; // 主分類的值
    let str = ""; // 要加進第二個選單的內容
    // 跑迴圈看有哪些符合
    for (let item of cates) {
      if (+item.parent_sid === +cate1Val) { // 轉換成數值, cateVal 是字串
        str += `<option value="${item.sid}">${item.name}</option>`;
      }
    }
    cate2.innerHTML = str;
  }

  // cate1.value = initVals.cate1; // 設定第一層的初始值
  // generateCate2List(); // 一進來就呼叫 / 生第二層
  // cate2.value = initVals.cate2; // 設定第二層的初始值


  // 預覽圖片 createObjectURL
  const previewImg = (event) => {
    const el = event.target;
    mainImg.src = URL.createObjectURL(el.files[0]);
    // console.log(el.files); // 會拿到FileList
  };



  // ---- 按下送出按鈕要執行以下
  function sendData(event) {
    event.preventDefault();

    // 外觀要回復原來的狀態
    fields.forEach(field => {
      field.style.border = '1px solid #CCCCCC';
      if (field.nextElementSibling) {
        field.nextElementSibling.innerHTML = '';
      }
    })

    uploadButton.style.border = '1px solid #CCCCCC';
    uploadButton.nextElementSibling.innerHTML = '';

    // // 先假設表單都是正確資訊，後續判斷如果有誤就把它變成false
    let isPass = true;

    // // 2.判斷商品名稱需大於兩個字:如果長度小於二就是資訊有誤
    if (name_in.value.length < 2) {
      $isPass = false;
      name_in.style.border = '2px solid red';
      name_in.nextElementSibling.innerHTML = '請填寫正確的商品名稱';
    }

    // //3.price 如果價格<1 就不是正確值
    if (price_in.value <= 0) {
      isPass = false;
      price_in.style.border = '2px solid red';
      price_in.nextElementSibling.innerHTML = '請填寫正確的商品價格';
    }

    // // 4.category 如果value沒有值，就代表沒選 (尚未釐清) // 設定進去後還是會有 名稱要改
    // // if (selectedCategory === '0') {
    // //   isPass = false;
    // //   category.style.border = '2px solid red';
    // //   category.nextElementSibling.innerHTML = '請選擇商品類別';
    // // }

    // // 5.判斷商品描述 需大於50字
    if (descriptions.value.length < 10) {
      $isPass = false;
      descriptions.style.border = '2px solid red';
      descriptions.nextElementSibling.innerHTML = '請填寫商品描述(需滿50字)';
    }

    // // 6.inventory 如果庫存沒有值 或 庫存<0代表資料有誤
    // if (!inventory.value || inventory.value < 0) {
    //   isPass = false;
    //   inventory.style.border = '2px solid red';
    //   inventory.nextElementSibling.innerHTML = '請填寫庫存';
    // }

    // 7.判斷上架狀態：預設為1,如果inventory填寫0就將launch設為0

    // 8.mainImg 圖片驗證
    let imgSrc = mainImgElement.getAttribute("src");
    if (imgSrc == "./img/default_img.jpg") {
      isPass = false;
      uploadButton.style.border = '2px solid red';
      uploadButton.nextElementSibling.innerHTML = '請上傳商品圖片';
    }


    uploadButton.addEventListener("click", function() {
      // 判定是否使用預設圖
      if (imgSrc === "./img/default_img.jpg") {
        isPass = false;
        uploadButton.style.border = '2px solid red';
        uploadButton.nextElementSibling.innerHTML = '請上傳商品圖片';
      }
    });




    // 沒有通過就不要發送資料
    if (!isPass) {
      return;
    }

    // 建立只有資料的表單 用formData類型去接
    const fd = new FormData(document.form1);
    const fd_mainImg = new FormData(document.mainImgForm);

    // 只要有資料傳送時或是想暫存資料就可以用 AJAX 方式去叫小弟做事
    fetch("edit-product-api.php", {
        method: 'POST',
        body: fd, // 送出資料格式會自動是mutipart/form-data
      }).then(r => r.json())
      .then(data => {
        console.log({
          data
        })
        // 無論商品資料是否有修改 去上傳圖片＋更新檔名
        fetch('edit-product-img-api.php', {
            method: 'POST',
            body: fd_mainImg,
          })
          .then(r => r.json())
          .then(img_data => {
            console.log({
              data
            })
          })
          .catch(ex => console.log(ex))
        if (data.success || img_data.success) {
          // alert('商品資料修改成功');
          let str = '';
          info.innerHTML = `<div class="alert alert-success" role="alert">
          商品資料修改成功
          </div>`;
          pauseForOneSecond();
          // location.href = "product_list.php";
        } else {
          for (let n in data.errors) {
            console.log(`n: ${n}`);
            // location.href = "product_list.php"
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

  function pauseForOneSecond() {
    setTimeout(function() {
      location.href = "product_list.php";
    }, 1000);
  }

  //---- 取消新增
  function cancelSend() {
    if (confirm(`確定要取消編輯資料嗎？`)) {
      document.form1.reset(); // 回覆預設？？
      location.href = "product_list.php";
    }
  }
</script>
<?php include './parts/html-foot.php' ?>