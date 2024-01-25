<?php
require './parts/connect_db.php';
$pageName = 'add';
$title = '新增商品';

$sql_category = "SELECT * FROM product_categories";
$rows_category = $pdo->query($sql_category)->fetchAll();


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
          <h3 class="card-title text-gray-800 text-center">新增商品資料</h3>
          <hr>
          <form name="form1" onsubmit="sendData(event)">
            <!-- 獲得最新的sid : issue 得不到sid 因為送出是同步的 要想別的選單 value=""-->
            <div class="mb-3">
              <label for="product_id" class="form-label">商品編號</label>
              <span class="form-control bg-secondary text-light" id="product_id">FYT-yyyymmdd-xxxx(新增商品後會自動生成)</span>
              <div class="form-text"></div>
            </div>
            <div class="mb-3">
              <label for="name" class="form-label">商品名稱</label>
              <input type="text" class="form-control" id="name" name="name">
              <div class="form-text"></div>
            </div>
            <div class="mb-3">
              <label for="price" class="form-label">商品價格</label>
              <input type="number" class="form-control" id="price" name="price">
              <div class="form-text"></div>
            </div>
            <!-- 商品分類 下拉式選單 -->
            <div class="input-group mb-3">
              <span class="input-group-text">主分類</span>
              <select class="form-select" name="cate1" id="cate1" onchange="generateCate2List()">
                <?php foreach ($rows_category as $r) :
                  if ($r['parent_sid'] == 0) : ?>
                    <option value="<?= $r['sid'] ?>"><?= $r['name'] ?></option>
                <?php endif;
                endforeach; ?>
              </select>
            </div>
            <div class="input-group mb-3">
              <span class="input-group-text">次分類</span>
              <select class="form-select" name="cate2" id="cate2">
              </select>
            </div>

            <!-- 商品描述 -->
            <div class="mb-3">
              <label for="descriptions" class="form-label">商品描述</label>
              <textarea class="form-control" name="descriptions" id="descriptions" cols="30" rows="3"></textarea>
              <div class="form-text"></div>
            </div>
            <!-- 庫存數量 -->
            <div class="mb-3">
              <label for="inventory" class="form-label">庫存數量</label>
              <input type="number" class="form-control" id="inventory" name="inventory">
              <div class="form-text"></div>
            </div>
            <!-- 是否上架 -->
            <div class="mb-3">
              <label for="launch" class="form-label">上架狀態</label><br/>
              <input type="radio" id="on" name="launch" value="1">
              <label for="launch">上架</label>&nbsp;
              <input type="radio" id="off" name="launch" value="0">
              <label for="launch">下架</label>
              <span class="form-text" id="launchVerify"></span>
            </div>
            <!-- 主要商品圖片 -->
            <div class="mb-3">
              <label for="mainImg" class="form-label">主要商品圖片(建議圖片大小 600 x 600px)</label>
              <br />
              <div class="btn btn-secondary uploadButton" style="cursor: pointer" onclick="document.mainImgForm.mainImg.click()">點擊上傳主要圖片</div>
              <div class="form-text"></div>
              <div class="showMainImg" style="width: 100px">
                <img src="./img/default_img.jpg" alt="" id="mainImg" name="mainImg" width="100%" />
              </div>
            </div>
            <div id="info"></div>
            <!-- 新增商品／取消新增商品 按鈕 -->
            <div class="d-flex justify-content-center mb-3">
              <button type="submit" class="btn btn-warning rounded-pill">新增商品</button> &nbsp;
              <button type="button" onclick="cancelSend(event)" class="btn btn-secondary rounded-pill">取消新增</button>
            </div>
        </div>
        </form>
        <!-- 單一圖片上傳的表單(hidden) -->
        <form name="mainImgForm" hidden>
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
  // const selectedCategory = document.form1.category;
  const descriptions = document.form1.descriptions;
  const mainImg = document.form1.mainImg;
  const inventory = document.form1.inventory;
  const launch = document.form1.launch.value;
  const fields = [name_in, price_in, inventory, descriptions];
  const showMainImg = document.querySelector('.showMainImg');
  const uploadButton = document.querySelector(".uploadButton");
  const mainImgElement = document.querySelector("#mainImg");
  const onRadioButton = document.querySelector('#on');
  const offRadioButton = document.querySelector('#off');
  const launchVerify = document.querySelector('#launchVerify');


  // 下拉選單的設定
  const initVals = {
    cate1: 1,
    cate2: 5
  };
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

  cate1.value = initVals.cate1; // 設定第一層的初始值
  generateCate2List(); // 一進來就呼叫 / 生第二層
  cate2.value = initVals.cate2; // 設定第二層的初始持


  // 預覽圖片 createObjectURL
  const previewImg = (event) => {
    const el = event.target;
    mainImg.src = URL.createObjectURL(el.files[0]);
    // console.log(el.files); // 會拿到FileList
  };



  // ---- 按下送出按鈕要執行以下驗證及AJAX -----
  function sendData(event) {
    event.preventDefault();

    // 外觀要回復原來的狀態
    fields.forEach(field => {
      field.style.border = '1px solid #CCCCCC';
      if (field.nextElementSibling) {
        field.nextElementSibling.innerHTML = '';
      }
    })

    launchVerify.innerHTML = '';

    uploadButton.style.border = '1px solid #CCCCCC';
    uploadButton.nextElementSibling.innerHTML = '';

    // 先假設表單都是正確資訊，後續判斷如果有誤就把它變成false
    let isPass = true;

    // 2.判斷商品名稱需大於兩個字:如果長度小於二就是資訊有誤
    if (name_in.value.length < 2) {
      $isPass = false;
      name_in.style.border = '2px solid red';
      name_in.nextElementSibling.innerHTML = '請填寫正確的商品名稱';
    }

    //3.price 如果價格<1 就不是正確值
    if (price_in.value <= 0) {
      isPass = false;
      price_in.style.border = '2px solid red';
      price_in.nextElementSibling.innerHTML = '請填寫正確的商品價格';
    }

    // 4.判斷商品描述 需大於10字
    if (descriptions.value.length < 10) {
      $isPass = false;
      descriptions.style.border = '2px solid red';
      descriptions.nextElementSibling.innerHTML = '請填寫商品描述(需滿10字)';
    }

    // 5.判斷是否填寫上架狀態
    if (onRadioButton.checked) {
      const launchStatus = onRadioButton.value;
    } else if (offRadioButton.checked) {
      const launchStatus = offRadioButton.value;
    } else {
      $isPass = false;
      launchVerify.innerHTML = '請選擇是否要上架';
    }

    // 6.判斷上架狀態：預設為1,如果inventory填寫0自動將launch設為0
    const inventoryValue = parseInt(inventory.value, 10); 
    const launchStatus = onRadioButton.checked ? 1 : 0;
    if (inventoryValue === 0 && launchStatus === 1){
      onRadioButton.checked = false;
      offRadioButton.checked = true;
    }

    // 7.mainImg 檢查圖片是否有上傳
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

    fetch("add-product-api.php", {
        method: 'POST',
        body: fd, // 送出資料格式會自動是mutipart/form-data
      }).then(r => r.json())
      .then(data => {
        console.log({
          data
        })
        console.log(data.success);
        if (data.success) {
          // alert('商品資料新增成功');
          info.innerHTML = `<div class="alert alert-success" role="alert">
          商品資料新增成功
          </div>`
          sendData2(); // 呼叫先去做圖片上傳
          pauseForOneSecond();
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
  }

  // 上傳資料完呼叫這支去上傳圖片 要做到資料和圖片同時上傳
  function sendData2() {
    const fd_mainImg = new FormData(document.mainImgForm);
    fetch('add-product-img-api.php', {
        method: 'POST',
        body: fd_mainImg,
      })
      .then(r => r.json())
      .then(data => {
        console.log({
          data
        });
      })
      .catch(ex => console.log(ex))
  }

  // 等待一秒再跳轉畫面
  function pauseForOneSecond() {
    setTimeout(function() {
      location.href = "product_list.php";
    }, 1000);
  }

  //---- 取消新增
  function cancelSend() {
    if (confirm(`確定要取消新增資料嗎？`)) {
      document.form1.reset();
      location.href = "product_list.php";
    }
  }
</script>
<?php include './parts/html-foot.php' ?>