
<?php require './parts/Scripts.php'; ?>

<?php


require './parts/connect_db.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

$pageName = 'bloglist';
$title = '列表';

$where = ' WHERE 1 ';
$params = [];
//搜尋用
$text = isset($_GET['text']) ? trim($_GET['text']) : ''; //宣告他在做搜尋
if (!empty($text)) {
    $text_esc = $pdo->quote('%' . $text . '%');

    $where .= " AND (bl.`BlogArticle_Title` LIKE $text_esc 
    OR bc.`BlogClass_content` LIKE $text_esc 
    OR bl.`BlogArticle_content` LIKE $text_esc) ";

    $params["text"] = $text;
}

//類別用

$selCate = isset($_GET['selectclassSid']) ? intval($_GET['selectclassSid']) : 0;
if (!empty($selCate)) {
    $where .= " AND bc.`BlogClass_id`=$selCate ";
    $params["selectclassSid"] = $selCate;
}

$perPage = 10; # 一頁最多有幾筆

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) {
    header('Location: ?page=1'); # 頁面轉向
    exit; # 直接結束這支 php
}


# 總筆數
$t_sql = "SELECT COUNT(1) FROM `bloglist` bl JOIN `BlogClass` bc ON bl.`BlogClass_id` = bc.`BlogClass_id` $where ";


# 總筆數
$totalRows = $pdo->query($t_sql)->fetch(PDO::FETCH_NUM)[0];
if ($totalRows > 0) {
    # 總頁數=無條件進位法的(總筆數/每頁幾筆)  2頁=23筆/20=1.XXXX
    $totalPages = ceil($totalRows / $perPage);
    if ($page > $totalPages) {
        header('Location: ?page=' . $totalPages); # 頁面轉向最後一頁
        exit; # 直接結束這支 php
    }
}

# 預設值 預設=0
#總頁數

$rows = [];



// 有資料時 => 總筆數totalRows不是0
// 如果有資料時





//分類選單用
//$selectclassSid = isset($_GET['selectclassSid']) ? intval($_GET['selectclassSid']) : 0;
// echo $where; exit;
$sql = sprintf(
    "SELECT * FROM `bloglist` bl JOIN `BlogClass` bc ON bl.`BlogClass_id` = bc.`BlogClass_id` %s 
ORDER BY bl.BlogArticle_ID  desc LIMIT %s, %s",
    $where,
    ($page - 1) * $perPage,
    $perPage
);


$rows = $pdo->query($sql)->fetchAll();


?>
<?php
#給blogclass用的
$sqlclass = "SELECT * FROM blogclass";
$rowsclass = $pdo->query($sqlclass)->fetchAll();
?>


<?php include './parts/html-head.php' ?>
<?php include './parts/navbar.php'; ?>


<style>
    .scroll {
        max-width: none;
        overflow-x: auto;
    }

    .listtitlepage {
        display: flex;
        justify-content: space-between;

    }

    .table_tit {
        max-width: 300px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .trbackcolor {
        background-color: lightblue;
        color: black
    }
</style>

<!-- Page Wrapper -->
<div id="wrapper ">

    <!-- Begin Page Content -->
    <div class="container-fluid ">
        <!-- 跨頁按鈕 -->
        <div class="row">
            <div class="col">
                <nav aria-label="Page navigation example">
                    <ul class="pagination">
                        <!-- 左按鈕 -->
                        <li class="page-item <?= $page == 1 ? 'disabled' : '' ?>">
                            <!-- if page=第一頁 則功能消失 -->
                            <a class="page-link" href="?page=1">
                                <i class="fa-solid fa-angles-left">
                                </i>
                            </a>
                        </li>


                        <!-- <li class="page-item"><a class="page-link" href="#">Previous</a></li> -->


                        <!-- 中間的 -->

                        <!--  for($i=1; $i<= $totalPages; $i++):   -->
                        <!-- 這邊就是往前按鈕  一次減五頁 或一次加五頁 -->
                        <?php for ($i = $page - 3; $i <= $page + 3; $i++) :
                            // 如果i已經=1或是最後一頁 就不顯示
                            if ($i >= 1 and $i <= $totalPages) : ?>

                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <!-- 加上active  點了會反白 -->

                                    <a class="page-link" href=" ?page= <?= $i ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endif ?>
                        <?php endfor ?>

                        <!-- <li class="page-item"><a class="page-link" href="#">Next</a></li> -->

                        <!-- 右按鈕 -->
                        <li class="page-item <?= $page == $totalPages ? 'disabled' : '' ?>">
                            <!-- if page=最後一頁 則功能消失 -->
                            <a class="page-link" href="?page=<?= $totalPages ?>">
                                <i class="fa-solid fa-angles-right">
                                </i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- 跨行 -->
        <br>
        <!-- 列表總表格 -->
        <div class="card shadow mb-4 ">



            <div class="card-header py-3  listtitlepage">
                <div>
                    <h6 class="m-0 font-weight-bold ">文章列表</h6>
                </div>

                <!-- 總筆數/總頁數 -->
                <div>總筆數: <?= "$totalRows" ?> <br>
                    總頁數: <?= " $totalPages" ?>
                </div>
            </div>
            <!-- 刪除 + 搜尋按鈕 -->
            <div class="DeleSerbtn d-flex justify-content-between">
                <!-- 刪除表格按鈕 -->
                <div class="allbtn">
                    <button class="btn btn-danger" style="width: 100px;" onclick="deleteMultiple(event)">刪除</button>
                </div>

                <!-- 由大到小 -->
                <!-- <label for="BlogClass_ID" class="form-label">文章分類</label> -->
                <!-- <select id="selmd" name="selmd" class="allbtn sbtn d-flex  align-items-center form-select form-select-lg mb-3" aria-label="Large select example">
                    <option selected>--會員ID順序--</option>
                    <option name='selmd' style="width: 200px;" type="option" value="BMD">由大到小</option>
                    <option name='selmd' style="width: 200px;" type="option" value="SMD">由小到大</option>
                </select> -->




                <!-- 篩選類別 -->
                <!-- <label for="BlogClass_ID" class="form-label">文章分類</label> -->
                <select id="selectclass" name="selectclass" class="allbtn sbtn d-flex  align-items-center form-select form-select-lg mb-3" aria-label="Large select example">
                    <option selected>--文章分類選擇--</option>
                    <option><a href="./bloglist.php"></a>全部</option>
                    <?php foreach ($rowsclass as $r) :
                    ?>
                        <option name='selectclass' style="width: 200px;" type="option" value="<?= $r['BlogClass_ID'] ?>"><?= $r['BlogClass_content'] ?></option>
                    <?php
                    // endif;
                    endforeach ?>
                </select>

                <!-- 搜尋按鈕 -->
                <div class="blog_search d-flex allbtn ">
                    <div>
                        <input type="search" style="width: 200px;" class="form-control" id="keyword" placeholder="關鍵字查詢" value="<?= isset($_GET['text']) ? $_GET['text'] : "" ?>">
                    </div>
                    <div>
                        <button type="submit" class=" btn " id="search"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </div>
                </div>
            </div>





            <!-- LIST表單 -->
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="scrollingcold   card shadow mb-4 scroll  ">
                        <div class="inner ">
                            <table id="dataTable" class=" table-bordered scroll text-nowrap" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="trbackcolor">
                                        <!-- <th scope="col">編號</th> -->
                                        <th scope="col" style="border: none;">
                                            <i class="fa-solid fa-bars"></i>
                                        </th>
                                        <th class="trbackcolor" scope="col">編號</th>
                                        <td scope="col">會員ID</td>
                                        <td scope="col">文章分類</td>
                                        <td scope="col">標題</td>
                                        <td scope="col">詳情</td>
                                        <td scope="col">照片</td>
                                        <td scope="col">文章內容</td>
                                        <td scope="col">創建時間</td>
                                        <td scope="col">最後更新時間</td>
                                        <td><i class="fa-solid fa-file-pen"></i></td>
                                        <td><i class="fa-solid fa-trash-can"></i></td>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rows as $r) : ?>

                                        <tr>

                                            <th class="delselect" style="border: none;"><input type="checkbox" name="selectedItems[]" value="<?= $r['BlogArticle_ID'] ?>"></th>
                                            <th><?= $r['BlogArticle_ID'] ?></th>
                                            <td><?= $r['Member_ID'] ?></td>
                                            <td><?= $r['BlogClass_content'] ?></td>
                                            <td><?= $r['BlogArticle_Title'] ?></td>

                                            <td><a href="blogdetail.php?BlogArticle_ID=<?= $r['BlogArticle_ID'] ?>">
                                                    <i class="fa-solid fa-circle-info" style="color: darkgreen"></i>

                                                </a></td>

                                            <td>
                                                <div style=" width: 20px">
                                                    <img src="/main-dev/uploads/<?= $r['BlogArticle_photo'] ?> " class="IMGBlogArticle_photo" alt="" id="BlogArticle_photo" width="100%" />
                                                </div>
                                            </td>
                                            <td class="table_tit"><?= $r['BlogArticle_content'] ?></td>

                                            <td><?= date('Y-m-d H:i:s', strtotime($r['BlogArticle_Create'])) ?></td>
                                            <td><?= date('Y-m-d H:i:s', strtotime($r['BlogArticle_Time'])) ?></td>

                                            <td><a href="blogedit.php?BlogArticle_ID=<?= $r['BlogArticle_ID'] ?>">
                                                    <i class="fa-solid fa-file-pen" style="color: darkblue" ;></i>
                                                </a></td>
                                            <td><a href="javascript: deleteItem(<?= $r['BlogArticle_ID'] ?>)">
                                                    <i class="fa-solid fa-trash-can" style="color: red;"></i>
                                                </a></td>
                                        </tr>

                                    <?php endforeach ?>
                                </tbody>





                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include './parts/scripts.php' ?>
    <script>
        function deleteItem(BlogArticle_ID) {
            if (confirm(`確定要刪除編號為 ${BlogArticle_ID} 的資料嗎?`)) {
                location.href = 'bloglistdelete.php?BlogArticle_ID=' + BlogArticle_ID;
            }
        }
    </script>
    <?php include './parts/html-foot.php' ?>


    <!-- 下面是for圖片用的  不要動 -->

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="Page.css">
    <script src="TestField.js" type="text/javascript"></script>
    <style>
        .scrollingcold td,
        th {
            vertical-align: top;
            padding: 10px;
            min-width: 50px;
        }



        .outer {
            position: relative
        }

        .inner {
            overflow-x: auto;
            overflow-y: visible;
            margin-left: 100px;
        }

        .trbackcolor {
            font-weight: 900
        }

        .allbtn {
            margin: 10px;

        }

        /* 刪除+搜尋用 */
        .DeleSerbtn {
            flex-direction: flex;
        }

        .sbtn {
            border-radius: 10px;
            padding: 8px;
        }

        .sbtn:hover {
            box-shadow: 3px 3px 7px rgba(0, 0, 0, 0.5);
        }

        /* 搜尋用 */
        .blog_search {
            flex-direction: flex;
            align-items: center;

        }
    </style>
    <script>
        $(document).ready(function() {
            $('#dataTable thead tr').each(function() {
                $(this).find('th').each(function(index) {
                    var positionPX = 50;
                    if (index == 0) {
                        $(this).css({
                            position: 'absolute',
                            left: 0,
                            width: '50px'
                        });
                    } else {
                        $(this).css({
                            position: 'absolute',
                            left: positionPX * index + 'px',
                            width: '50px'
                        });
                    }
                });
            });

            $('#dataTable tbody tr').each(function() {
                $(this).find('th').each(function(index) {
                    var positionPX = 50;
                    if (index == 0) {
                        $(this).css({
                            position: 'absolute',
                            left: 0,
                            width: '50px'
                        });
                    } else {
                        $(this).css({
                            position: 'absolute',
                            left: positionPX * index + 'px',
                            width: '50px'
                        });
                    }
                });
            });

        });
    </script>

    <!-- 刪除用的 -->
    <script>
        function deleteMultiple(event) {

            const selectedItems = document.querySelectorAll('input[name="selectedItems[]"]:checked');
            //console.log("已选中的项目数量：", selectedItems.length);
            if (selectedItems.length === 0) {
                alert("請至少選擇一個項目進行刪除。");
                return;
            }

            const selectedIds = Array.from(selectedItems).map(item => item.getAttribute("value"));
            //console.log("已选中的项目的值：", selectedIds);
            if (confirm(`確定要刪除編號為 ${selectedIds.join(', ')}的資料嗎 ? `)) {
                location.href = 'blogdeletemultiple.php?BlogArticle_IDs=' + selectedIds.join(',');
            }

        }
    </script>


    <!-- 關鍵字搜尋 -->
    <script>
        //關鍵字搜尋
        let keyword = document.querySelector("#keyword");
        let search = document.querySelector("#search");
        search.addEventListener('click', function() {
            let keywordVal = keyword.value;
            location.href = 'bloglist.php?text=' + keywordVal;
        })

        //類別功能篩選

        const selectclassSelect = document.getElementById('selectclass');
        console.log(selectclassSelect)

        selectclassSelect.addEventListener('change', function() {
            const selectclassSid = selectclassSelect.value;
            location.href = 'bloglist.php?selectclassSid=' + selectclassSid;
        });


        //會員ID順序大小

        const selmd = document.getElementById('selmd');
        console.log(selmd)

        selmd.addEventListener('change', function() {
            const selmdid = selmd.value;
            location.href = 'bloglist.php?selmdid=' + selmdid;
        });
    </script>



    <!-- 浮動視窗 -->

    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Understood</button>
                </div>
            </div>
        </div>
    </div>



    <?php
    //搜尋用

    /*
if (isset($_GET['text']) && $_GET['text'] !== "") {
    $sql = "SELECT * FROM `bloglist` bl JOIN `BlogClass` bc ON bl.`BlogClass_id` = bc.`BlogClass_id` 
        WHERE bl.`BlogArticle_Title` LIKE '%$text%' 
        OR bc.`BlogClass_content` LIKE '%$text%' 
        Or bl.`BlogArticle_content` LIKE '%$text%' 
        ORDER BY bl.`BlogArticle_ID` ASC ";
} elseif (isset($_GET['selectclassSid']) && $_GET['selectclassSid'] !== "") {
    $sql = "SELECT * FROM `bloglist` bl JOIN `BlogClass` bc ON bl.`BlogClass_id` = bc.`BlogClass_id` 
            WHERE bc.`BlogClass_id` =$selectclassSid ;
            ORDER BY bl.`BlogArticle_ID` ASC ";
} else {
    if ($totalRows > 0) {
        # 總頁數=無條件進位法的(總筆數/每頁幾筆)  2頁=23筆/20=1.XXXX
        $totalPages = ceil($totalRows / $perPage);
        if ($page > $totalPages) {
            header('Location: ?page=' . $totalPages); # 頁面轉向最後一頁
            exit; # 直接結束這支 php
        }

        $sql = sprintf(
            "SELECT * FROM bloglist,BlogClass 
        where bloglist.BlogClass_id=BlogClass.BlogClass_ID
        ORDER BY BlogArticle_ID  desc LIMIT %s, %s",
            ($page - 1) * $perPage,
            $perPage

        );
        $rows = $pdo->query($sql)->fetchAll();
    }
}
*/
    #echo $sql; exit;
    ?>