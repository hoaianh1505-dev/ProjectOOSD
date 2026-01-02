<?php
class CartController
{
    function add()
    {
        $product_id = $_GET['product_id'];
        $qty = $_GET['qty'];
        // lần đầu tiên mua hàng, hoặc giỏ hàng hết hạn
        if (empty($_COOKIE['cart'])) {
            // giỏ hàng mới hoàn toàn, hok có gì trong đó
            $cart = new Cart();
        } else {
            // Đã mua trước đó, giỏ hàng cũ nằm trong $_COOKIE.
            // Dựa vào $_COOKIE, tái tạo lại giỏ hàng
            $currentCookieCart = $_COOKIE['cart'];
            // chuyển ngược từ json sang array
            $arr = json_decode($currentCookieCart, true);
            // Tạo lại giỏ hàng
            $items = $arr['items'];
            $total_price = $arr['total_price'];
            $total_product_number = $arr['total_product_number'];
            $cart = new Cart($items, $total_price, $total_product_number);
        }

        $cart->addProduct($product_id, $qty);
        // Tạo cookie và gởi cookie này về trình duyệt, để lưu ở trình duyệt

        // tham số thứ 3 là thời gian hết hạn, hay là thời gian sống (đơn vị là giây)
        // cookie cart bên dưới sống trong vòng 1 ngày
        $strCart = json_encode($cart);
        setcookie('cart', $strCart, time() + 24 * 60 * 60 * 1);
    }

    function update()
    {
        $product_id = $_GET['product_id'];
        $qty = $_GET['qty'];
        // Đã mua trước đó, giỏ hàng cũ nằm trong $_COOKIE.
        // Dựa vào $_COOKIE, tái tạo lại giỏ hàng
        $currentCookieCart = $_COOKIE['cart'];
        // chuyển ngược từ json sang array
        $arr = json_decode($currentCookieCart, true);
        // Tạo lại giỏ hàng
        $items = $arr['items'];
        $total_price = $arr['total_price'];
        $total_product_number = $arr['total_product_number'];
        $cart = new Cart($items, $total_price, $total_product_number);
        $cart->deleteProduct($product_id);
        $cart->addProduct($product_id, $qty);
        // Tạo cookie và gởi cookie này về trình duyệt, để lưu ở trình duyệt

        // tham số thứ 3 là thời gian hết hạn, hay là thời gian sống (đơn vị là giây)
        // cookie cart bên dưới sống trong vòng 1 ngày
        $strCart = json_encode($cart);
        setcookie('cart', $strCart, time() + 24 * 60 * 60 * 1);
    }

    function delete()
    {
        $product_id = $_GET['product_id'];
        // Đã mua trước đó, giỏ hàng cũ nằm trong $_COOKIE.
        // Dựa vào $_COOKIE, tái tạo lại giỏ hàng
        $currentCookieCart = $_COOKIE['cart'];
        // chuyển ngược từ json sang array
        $arr = json_decode($currentCookieCart, true);
        // Tạo lại giỏ hàng
        $items = $arr['items'];
        $total_price = $arr['total_price'];
        $total_product_number = $arr['total_product_number'];
        $cart = new Cart($items, $total_price, $total_product_number);
        $cart->deleteProduct($product_id);

        // Tạo cookie và gởi cookie này về trình duyệt, để lưu ở trình duyệt

        // tham số thứ 3 là thời gian hết hạn, hay là thời gian sống (đơn vị là giây)
        // cookie cart bên dưới sống trong vòng 1 ngày
        $strCart = json_encode($cart);
        setcookie('cart', $strCart, time() + 24 * 60 * 60 * 1);
    }
}
