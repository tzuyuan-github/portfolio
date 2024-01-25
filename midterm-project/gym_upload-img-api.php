<?php
$dir = __DIR__ . '/./uploads/';

# 用關聯式陣列做檔案類型的篩選
$exts = [
    'image/jpeg' => '.jpg',
    'image/png' => '.png',
    'image/webp' => '.webp',
];

header("Content-Type: application/json");
$output = [
    'success'=>false,
    'file'=>''
];



if(!empty($_FILES) and !empty($_FILES['gym_photosss']) and $_FILES['gym_photosss']['error']==0){

    if(!empty($exts[$_FILES['gym_photosss']['type']])){
        $ext=$exts[$_FILES['gym_photosss']['type']];// 副檔名
        # 隨機的主檔名
        $f =sha1($_FILES['gym_photosss']['name']. uniqid()); //uniqid 唯一
        if(
            move_uploaded_file(
            $_FILES['gym_photosss']['tmp_name'], // 臨時路徑
            $dir . $f. $ext // 目標路徑，新的文件名
        )
        ){
            $output['success']=true;
            $output['file']=$f. $ext;
        }
    }
}

echo json_encode($output);
/*
"gym_photosss": {
  "name": "截圖 2023-10-22 下午1.55.35.png",
  "full_path": "截圖 2023-10-22 下午1.55.35.png",
  "type": "image/png",
  "tmp_name": "/Applications/XAMPP/xamppfiles/temp/phpi2BG8X",
  "error": 0,
  "size": 294870
}
*/