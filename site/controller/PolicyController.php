<?php 
class PolicyController {
    // chính sách đổi trả
    function return() {
        require 'view/policy/return.php';
    }

    // chính sách thanh toán
    function payment() {
        require 'view/policy/payment.php';
    }

    // chính sách giao hàng
    function delivery() {
        require 'view/policy/delivery.php';
    }
}
?>