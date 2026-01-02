<?php 
// session_destroy();
$message = '';//khởi tạo
$classType = '';
if (!empty($_SESSION['success'])) {
    $classType = 'alert-success';
    $message = $_SESSION['success'];
    // xóa phần tử có key là success ra khỏi array $_SESSION
    unset($_SESSION['success']);
} else if (!empty($_SESSION['error'])) {
    $classType = 'alert-danger';
    $message = $_SESSION['error'];
    // xóa phần tử có key là success ra khỏi array $_SESSION
    unset($_SESSION['error']);
}
if ($message):
?>
<!-- .alert.alert-success.text-center -->
<div class="alert <?=$classType?> text-center mt-3"><?=$message?></div>
<?php 
endif;
?>