<?php

require './parts/connect_db.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}
 ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>首頁 - FreeFyt</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        
        .indexwelcome{
            margin: auto;
            height: 60vh;
            display: flex;
            justify-content: center;
        }
    </style>
</head>

<body id="page-top">

    

 
<?php include './parts/navbar.php'; ?>
<?php include './parts/scripts.php'; ?>

    <div class="container indexwelcome">
        <img src="./parts/uploads/welcome-02.png"
        style="height: 100%;" 
        alt="" srcset="">
    </div>
</body>

</html>
