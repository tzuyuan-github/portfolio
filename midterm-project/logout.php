<?php
session_start();

# 清除所有 session 資料
unset($_SESSION['admin']);
session_destroy();

header('Location: login.php');