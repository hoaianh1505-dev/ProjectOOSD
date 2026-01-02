<?php
class PaymentController
{
    function checkout()
    {
        // Nếu chưa login thì không được đặt hàng 
        if (empty($_SESSION['email'])) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để đặt hàng';
            // điều hướng về trang chủ
            header('location: /');
            exit;
        }

        // Lần đầu tiên vào web, chưa kịp chọn sản phẩm nào hết.
        if (empty($_COOKIE['cart'])) {
            $_SESSION['error'] = 'Giỏ hàng rỗng';
            // điều hướng về trang mua sắm
            header('location: ?c=product');
            exit;
        }
        $currentCookieCart = $_COOKIE['cart'];
        // chuyển ngược từ json sang array
        $arr = json_decode($currentCookieCart, true);
        // Tạo lại giỏ hàng
        $items = $arr['items'];
        $total_price = $arr['total_price'];
        $total_product_number = $arr['total_product_number'];

        // Đã chọn sản phẩm trong giỏ hàng, nhưng sau đó xóa hết sản phẩm
        if ($total_product_number == 0) {
            $_SESSION['error'] = 'Giỏ hàng rỗng';
            // điều hướng về trang mua sắm
            header('location: ?c=product');
            exit;
        }

        // Build lại giỏ hàng 
        $cart = new Cart($items, $total_price, $total_product_number);
        // cart sẽ truyền qua view

        $email = $_SESSION['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);

        require 'layout/variable.php';
        require 'view/payment/checkout.php';
    }
    function order()
    {
        if (empty($_SESSION['email'])) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để đặt hàng';
            // điều hướng về trang chủ
            header('location: /');
            exit;
        }

        // Lần đầu tiên vào web, chưa kịp chọn sản phẩm nào hết.
        if (empty($_COOKIE['cart'])) {
            $_SESSION['error'] = 'Giỏ hàng rỗng';
            // điều hướng về trang mua sắm
            header('location: ?c=product');
            exit;
        }
        $currentCookieCart = $_COOKIE['cart'];
        // chuyển ngược từ json sang array
        $arr = json_decode($currentCookieCart, true);
        // Tạo lại giỏ hàng
        $items = $arr['items'];
        $total_price = $arr['total_price'];
        $total_product_number = $arr['total_product_number'];

        // Đã chọn sản phẩm trong giỏ hàng, nhưng sau đó xóa hết sản phẩm
        if ($total_product_number == 0) {
            $_SESSION['error'] = 'Giỏ hàng rỗng';
            // điều hướng về trang mua sắm
            header('location: ?c=product');
            exit;
        }

        // Build lại giỏ hàng 
        $cart = new Cart($items, $total_price, $total_product_number);
        // cart sẽ truyền qua view

        $email = $_SESSION['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);


        // Tìm phí giao hàng
        $provinceRepository = new ProvinceRepository();
        $province = $provinceRepository->find($_POST['province']);
        $shipping_fee = $province->getShippingFee();

        // Lưu order xuống database
        $data =  [];
        $data["created_date"] = date('Y-m-d H:i:s'); //2025-05-15 22:16:17
        $data["order_status_id"] = 1;
        $data["staff_id"] = null; // người chịu trách nhiệm trên đơn hàng
        $data["customer_id"] = $customer->getId();
        $data["shipping_fullname"] = $_POST['fullname'];
        $data["shipping_mobile"] = $_POST['mobile'];
        $data["payment_method"] = $_POST['payment_method']; //0 là COD, 1 là bank
        $data["shipping_ward_id"] = $_POST['ward'];
        $data["shipping_housenumber_street"] = $_POST['address'];
        $data["shipping_fee"] = $shipping_fee;
        $data["delivered_date"] = date('Y-m-d', strtotime('+3 days')); //ngày dự kiến giao hàng, là 3 ngày sau

        // Lưu tổng quan về 1 đơn hàng 
        $orderRepository = new OrderRepository();
        $order_id = $orderRepository->save($data);

        $orderItemRepository = new OrderItemRepository();
        foreach ($items as $item) {
            // Lưu chi tiết 1 đơn hàng, mỗi dòng là thông tin một sản phẩm khách hàng mua
            $dataItem = [
                'product_id' => $item['product_id'],
                'order_id' =>  $order_id,
                'qty' => $item['qty'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['total_price']
            ];
            $orderItemRepository->save($dataItem);
        }
        $_SESSION['success'] = 'Đơn hàng đã đặt thành công';
        // Xoá giỏ đơn hàng đi
        //time() là thời gian hiện tại 
        // Lùi về quá khứ để cho trình duyệt web xoá cookie(cookie bị hết hạn nên trình duyệt sẽ xoá)
        setcookie('cart', '', time() - 24 * 60 * 60);

        // Điều hướng về tran danh sách đơn hàng
        header('location: ?c=customer&a=orders');
    }
}
