<?php
session_start();
//Load Composer's autoloader (created by composer, not included with PHPMailer)
require '../vendor/autoload.php';
// router (điều hướng)
// c và a là 2 tham số tự nghĩ ra, tự đặt để điều hướng đến hành động tướng ứng
// c là controller
// a là action 
$c = $_GET['c'] ?? 'home';
$a = $_GET['a'] ?? 'index';

// import config & connect db
require '../config.php';
require '../connectDB.php';

// ucfirst là uppercase (chữ hoa) ký tự đầu tiên của chuỗi
$strController = ucfirst($c) . 'Controller'; //StudentController

// import file chứa class controller tương ứng
// require "controller/StudentController.php";
require "controller/$strController.php";

// import model
require '../bootstrap.php';

$controller = new $strController(); //new StudentController();
// call hàm tương ứng với a
$controller->$a();//$controller->index();