<?php

require './parts/connect_db.php';
require './parts/scripts.php';


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
$sql = "SELECT * FROM bloglist as a,blogclass as b where a.BlogClass_ID=b.BlogClass_ID 
and BlogArticle_ID={$BlogArticle_ID}";
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
    *{
        text-decoration: none;
    }
    form .form-text {
        color: red;
    }

    .timedet {
        font-size: smaller;
    }

    .detailph {
        /* margin-top: 5vh;
        margin-left: 20vh; */
        width: 50vh;
        margin: auto;
        padding-top: 20px;
    }

    .tittde {
        width: 90vh;
        margin: auto;
    }

    .titlestyle {
        color: royalblue;
        ;
        font-weight: bolder;
    }

    .contentstyle {
        color: darkblue;

    }

    .classde {
        margin-top: 5px;
        margin-left: 5px;
        color: brown;
    }

    .btnstyle {
        padding-top: 25px;
        display: flex;
        justify-content: space-around;
    }

    .buttons {
        display: inline-block;
        padding: 10px 20px;
        background-image: linear-gradient(to left bottom, #c9153c, #d64259, #e26275, #eb7f91, #f29bac);
        color: white;
        text-decoration: none;
        border-radius: 5px;
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
    }

    .buttons:hover {
        box-shadow: 3px 3px 7px rgba(0, 0, 0, 0.5);
        color: white;
        font-weight: 900;
    }


    .buttonsa {
        display: inline-block;
        padding: 10px 20px;
        background-image: linear-gradient(to left bottom, #1dc915, #3aaf2c, #479538, #4c7c40, #4c6345);
        color: white;
        text-decoration: none;
        border-radius: 5px;
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
    }

    .buttonsa:hover {
        box-shadow: 3px 3px 7px rgba(0, 0, 0, 0.5);
        color: white;
        font-weight: 900;
    }
</style>



<!-- 詳細文章 -->
<div class="container">

    <div class="card">
        <div class="classde  d-flex  justify-content-end timedet">
            分類:<?= htmlentities($row['BlogClass_content']) ?>
        </div>
        <div class="detailtime d-flex  justify-content-end timedet">
            創建時間 : <?= date('Y-m-d H:i:s', strtotime(htmlentities($row['BlogArticle_Create']))) ?>
        </div>
        <div class="detailtime d-flex  justify-content-end timedet">
            最後更新時間 : <?= date('Y-m-d H:i:s', strtotime(htmlentities($row['BlogArticle_Time']))) ?>
        </div>
        <div class="detailph">
            <img src="/main-dev/uploads/<?= htmlentities($row['BlogArticle_photo']) ?> " alt="" id="abcimg" width="100%" />
        </div>



        <div class="card-body tittde">
            <h1 class="card-title titlestyle"><?= htmlentities($row['BlogArticle_Title']) ?>
            </h1>
            <p class="card-text contentstyle"><?= htmlentities($row['BlogArticle_content']) ?></p>

            <div class="btnstyle ">
                <!-- <a >返回列表</a> -->
                <a href="blogedit.php?BlogArticle_ID=<?= htmlentities($row['BlogArticle_ID']) ?>" class="btn buttonsa">修改資料</a> 
                <a href="./bloglist.php" class="btn buttons">返回列表</a> 
            </div>

        </div>
    </div>
</div>