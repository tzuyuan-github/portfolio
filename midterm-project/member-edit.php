<?php

require './parts/connect_db.php';

$member_id = isset($_GET['member_id']) ? intval($_GET['member_id']) : 0;

if (empty($member_id)) {
    header('Location: member.php');
    exit;
}
// Q: Necessary?

$sql = "SELECT * FROM member WHERE member_id={$member_id}";
$row = $pdo->query($sql)->fetch();

$t_sql = "SELECT COUNT(*) FROM member";
$t_row = $pdo->query($t_sql)->fetch();
$perPage = 10;

$sql_dis = "SELECT * FROM districts";
$rows_dis = $pdo->query($sql_dis)->fetchAll();

if (empty($row)) {
    header('Location: member.php');
    exit;
}
// Q: Necessary?
// Q: echo json_encode($row, JSON_UNESCAPED_UNICODE);
?>

<?php include './parts/html-head.php' ?>
<?php include './parts/navbar.php' ?>

<style>
    form .form-text {
        color: red;
    }
</style>
<!-- Q: Move? -->

<div class="container">
    <div class="row">
        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">更新會員</h5>

                    <form name="form1" onsubmit="sendData(event)">

                        <input type="hidden" name="member_id" value="<?= htmlentities($row['member_id']) ?>">

                        <div class="mb-3">
                            <label for="name" class="form-label">姓名</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= htmlentities($row['member_name']) ?>">
                            <div class="form-text"></div>
                        </div>

                        <div class="mb-3">
                            <label for="gender" class="form-label">性別</label>
                            <select class="form-control" name="gender" id="gender">
                                <option value="M">男</option>
                                <option value="F">女</option>
                                <option value="N/A">其他</option>
                            </select>
                            <div class="form-text"></div>
                        </div>
                        <!-- Q: Complete default select for gender -->

                        <div class="mb-3">
                            <label for="bday" class="form-label">生日</label>
                            <input type="date" class="form-control" id="bday" name="bday" value="<?= htmlentities($row['member_bday']) ?>">
                            <div class="form-text"></div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">信箱</label>
                            <input type="text" class="form-control" id="email" name="email" value="<?= htmlentities($row['member_email']) ?>">
                            <div class="form-text"></div>
                        </div>

                        <div class="mb-3">
                            <label for="mobile" class="form-label">手機</label>
                            <input type="tel" class="form-control" id="mobile" name="mobile" value="<?= htmlentities($row['member_mobile']) ?>">
                            <div class="form-text"></div>
                        </div>

                        <div class="mb-3">
                            <label for="username" class="form-label">帳號</label>
                            <input type="text" class="form-control" name="username" id="username" value="<?= htmlentities($row['member_username']) ?>">
                            <div class="form-text"></div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">密碼</label>
                            <input type="password" class="form-control" name="password" id="password" value="<?= htmlentities($row['member_password']) ?>">
                            <div class="form-text"></div>
                        </div>


                        <div class="mb-3">
                            <label for="nickname" class="form-label">暱稱</label>
                            <input type="text" class="form-control" name="nickname" id="nickname" value="<?= htmlentities($row['member_nickname']) ?>">
                            <div class="form-text"></div>
                        </div>

                        <div class="mb-3">
                            <label for="cat1" class="form-label">縣市</label>
                            <select class="form-control" name="cat1" id="cat1" onchange="generateCat2List()">

                                <?php foreach ($rows_dis as $r) :
                                    if ($r['parent_sid'] == 0) : ?>
                                        <option value="<?= $r['sid'] ?>"
                                        
                                        <?php if ($row['city'] == $r['sid']) echo 'selected' ?> ><?= $r['district'] ?>
                                    
                                    </option>
                                <?php endif;
                                endforeach; ?>

                            </select>
                            <div class="form-text"></div>
                        </div>

                        <div class="mb-3">
                            <label for="cat2" class="form-label">鄉鎮市區</label>
                            <select class="form-control" name="cat2" id="cat2">

                                <?php foreach ($rows_dis as $r) :
                                    if ($row['city'] == $r['parent_sid']) : ?>
                                        <option value="<?= $r['sid'] ?>" <?php if ($row['district'] == $r['sid']) echo 'selected' ?>><?= $r['district'] ?></option>
                                <?php endif;
                                endforeach; ?>
                            </select>
                            <div class="form-text"></div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">地址</label>
                            <input type="text" class="form-control" name="address" id="address" value="<?= htmlentities($row['address']) ?>">
                            <div class="form-text"></div>
                        </div>

                        <button type="submit" class="btn-primary">送出</button>

                        <button class="btn-primary" onclick="deleteItem(<?= $member_id ?>)">刪除</button>
                    </form>

                    <form name="form2">
                        <input type="hidden" name="member_id2" value="<?= htmlentities($row['member_id']) ?>">

                        <div class="mb-3">
                            <label for="avatar" class="form-label">大頭貼</label>
                            <input type="file" class="form-control" name="avatar" id="avatar" accept="image/jpeg,image/png" onchange="f(event)" />
                            <div class="form-text"></div>
                        </div>

                        <div style="width: 300px;">
                            <img id="myimg" src="./uploads/<?= $row['profile_pic'] ?>" alt="" width="100%" />
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include './parts/scripts.php' ?>

<script>
    const cats = <?= json_encode($rows_dis, JSON_UNESCAPED_UNICODE) ?>;
    // Q: What for?

    const cat1 = document.querySelector('#cat1')
    const cat2 = document.querySelector('#cat2')

    function generateCat2List() {
        console.log("yay")
        const cat1Val = cat1.value;
        let str = "";
        for (let item of cats) {
            if (+item.parent_sid === +cat1Val) {
                str += `<option value="${item.sid}">${item.district}</option>`;
            }
        }
        cat2.innerHTML = str;
    }
    // generateCat2List();
    // N: 不可以一進來就呼叫

    function sendData(e) {
        e.preventDefault();
        const fd = new FormData(document.form1);
        const fd2 = new FormData(document.form2);

        fetch('member-edit-api.php', {
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
                    sendData2();
                } else {
                    for (let n in data.errors) {
                        console.log(`n: ${n}`);
                        location.href = "member.php"
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
        fetch('member-edit-img-api.php', {
                method: 'POST',
                body: fd2,
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert('資料新增成功');
                    location.href = `member.php?page=<?= ($t_row['COUNT(*)'] / $perPage) + 1 ?>`
                    
                }
            })
    }

    const f = (e) => {
        const el = e.currentTarget;
        const reader = new FileReader();

        reader.onload = function(event) {
            myimg.src = reader.result;
        }

        reader.readAsDataURL(el.files[0]);

    };

    function deleteItem(member_id) {
    if (confirm(`確定要刪除會員編號為 ${member_id} 的資料嗎?`)) {
      location.href = 'member-delete.php?member_id=' + member_id;
    }
  }
</script>

<?php include './parts/html-foot.php' ?>