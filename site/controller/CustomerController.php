<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class CustomerController
{
    function checkLogin()
    {
        if (empty($_SESSION['email'])) {
            //chưa login 
            $_SESSION['error'] = 'Bạn phải login mới có quyền truy cập';
            header('location: /');
            exit;
        }
    }


    // Hiển thị thông tin tài khoản
    function show()
    {
        $this->checkLogin();

        $email = $_SESSION['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        require 'view/customer/show.php';
    }

    // Trang địa chỉ giao hàng mặc định
    function shippingDefault()
    {
        $this->checkLogin();

        $email = $_SESSION['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);

        require 'layout/variable.php';
        require 'view/customer/shippingDefault.php';
    }
    function updateShippingDefault()
    {
        $this->checkLogin();

        $email = $_SESSION['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        //Cập nhật vào customer
        $customer->setShippingName($_POST['fullname']);
        $customer->setShippingMobile($_POST['mobile']);
        $customer->setWardId($_POST['ward']);
        $customer->setHousenumberStreet($_POST['address']);

        // Lưu xuống database
        if (!$customerRepository->update($customer)) {
            // lưu thất bại
            $_SESSION['error'] = $customerRepository->getError();
            header('location: ?c=customer&a=show');
            exit;
        }

        // lưu thành công
        $_SESSION['success'] = 'Đã cập nhật thông tin tài khoản thành công';
        header('location: ?c=customer&a=shippingDefault');
    }

    // Trang danh sách đơn hàng
    function orders()
    {
        $this->checkLogin();

        // Lấy danh sách đơn hàng của người đăng nhập
        $email = $_SESSION['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);

        $orderRepository = new OrderRepository();
        $orders = $orderRepository->getByCustomerId($customer->getId());

        require 'view/customer/orders.php';
    }

    // Trang chi tiết đơn hàng
    function orderDetail()
    {
        $this->checkLogin();

        // Lấy thông tin của 1 đơn hàng cụ thể dựa vào mã đơn hàng
        $id = $_GET['id'];
        $orderRepository = new OrderRepository();
        $order = $orderRepository->find($id);
        require 'view/customer/orderDetail.php';
    }

    function updateAccount()
    {
        $this->checkLogin();

        $email = $_SESSION['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);

        //update vào đối tượng customer
        $name = $_POST['fullname'];
        $mobile = $_POST['mobile'];
        $customer->setName($name);
        $customer->setMobile($mobile);

        // Kiểm ra xem người dùng có nhu cầu đổi mật khẩu không?
        $current_password = $_POST['current_password'];
        $password = $_POST['password'];
        // Nếu người dùng nhập mật khẩu hiện và mật khẩu mới, điều đó có nghĩa là họ có nhu cầu đổi mật khẩu
        if ($current_password && $password) {
            // verify password: kiểm tra password hiện tại đúng không?
            if (!password_verify($current_password, $customer->getPassword())) {
                // báo lỗi nếu sai mật khẩu
                $_SESSION['error'] = 'Sai mật khẩu hiện tại';
                header('location: ?c=customer&a=show');
                exit;
            }
            // ok, mật khẩu hiện tại đúng.
            // mã hóa mật khẩu, vd: chuyển 12345aA@1 -> $2y$10$lgMSHSprLJv1jtR2DG5iFOyJTsepT6TR4pKBjSJkw92eQ48/eZauG
            // mã hóa mật khẩu 1 chiều, nghĩa là không giải mã được
            // PASSWORD_BCRYPT là giải thuật mã hóa (tiến sĩ làm, mình hok có quan tâm nhiều!!!)
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            // cập nhập new passowrd này vào đối tượng customer 
            $customer->setPassword($password_hash);
        }

        // Lưu xuống database
        if (!$customerRepository->update($customer)) {
            // lưu thất bại
            $_SESSION['error'] = $customerRepository->getError();
            header('location: ?c=customer&a=show');
            exit;
        }

        // lưu thành công
        $_SESSION['name'] = $name; //update session cho đúng với database
        $_SESSION['success'] = 'Đã cập nhật thông tin tài khoản thành công';
        header('location: ?c=customer&a=show');
    }

    function notExistingEmail()
    {
        $email = $_GET['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        if (empty($customer)) {
            echo 'true';
            return;
        }
        echo 'false';
    }
    function register()
    {
        $secret = GOOGLE_RECAPTCHA_SECRET;
        $recaptcha = new \ReCaptcha\ReCaptcha($secret);
        $hostname = get_host_name();
        $remoteIp = '127.0.0.1';
        $gRecaptchaResponse = $_POST['g-recaptcha-response'];
        $resp = $recaptcha->setExpectedHostname($hostname)
            ->verify($gRecaptchaResponse, $remoteIp);
        if (!$resp->isSuccess()) {
            $errors = $resp->getErrorCodes();
            //Chuyển array 2 phần tử thành chuỗi để hiển thị trên website 
            $error = implode($errors, '<br>');
            $_SESSION['error'] = $error;
            header('location: /');
            exit;
        }
        // var_dump($_POST);
        $email = $_POST['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        if ($customer) {
            $_SESSION['error'] = 'Email đã tồn tại, vui lòng nhập email khác';
            header('location: /');
            exit;
        }

        // Tạo account mới và lưu xuống database
        // var_dump($_POST);
        $data = [];
        $data["name"] = $_POST['fullname'];
        // PASSWORD_BCRYPT là giải thuật mã hóa (tiến sĩ làm, mình hok có quan tâm nhiều!!!)
        $data["password"] = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $data["mobile"] = $_POST['mobile'];
        $data["email"] = $_POST['email'];
        $data["login_by"] = 'form';
        $data["shipping_name"] = $_POST['fullname'];
        $data["shipping_mobile"] = $_POST['mobile'];
        $data["ward_id"] = NULL;
        $data["is_active"] = 0; // măc định là chưa kích hoạt tài khoản
        $data["housenumber_street"] = '';

        if (!$customerRepository->save($data)) {
            // lưu thất bại
            $_SESSION['error'] = $customerRepository->getError();
            header('location: /');
            exit;
        }
        // Tạo account thành công
        // Gửi email kích hoạt tài khoản (chưa làm)
        $emailService = new EmailService();
        $to = $data['email'];
        $subject = 'The Bloom Studio - Active Account';
        $name = $data['name'];

        $website = get_domain();
        $key = JWT_KEY;
        // data
        $payload = [
            'email' => $to,
        ];
        $jwt = JWT::encode($payload, $key, 'HS256');

        $link = get_domain_site() . '?c=customer&a=activeAccount&jwt=' . $jwt;
        $activeLink = "<a href='$link'>Active Account</a>";
        $content = "
        Dear $name, <br>
        Vui lòng click vào link bên dưới để active tài khoản của bạn <br>
        $activeLink <br>
        ------------------------- <br>
        Được gởi từ trang web $website
        ";

        $emailService->send($to, $subject, $content);

        $_SESSION['success'] = "Đã tạo tài khoản thành công, vui lòng kiểm tra email $email   để kích hoạt tài khoản";
        header('location: /');
    }
    function test1()
    {
        // mã hoá
        $key = 'Con rùa bò 4 chân';
        // data
        $payload = [
            'email' => 'abc@gmail.com',
        ];
        $jwt = JWT::encode($payload, $key, 'HS256');
        echo $jwt;
    }

    function test2()
    {
        // giải mã
        $jwt = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6ImFiY0BnbWFpbC5jb20ifQ.QSENN_XKGq8j91hhfYcfwLkREc9mx09xGYzUGY9DLLA';
        $key = 'Con rùa bò 4 chân';
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        print_r($decoded);
    }
    function activeAccount()
    {
        //  Dựa vào token jwt, giải mã để tìm ra email
        // Sau đó tìm ra account tương ứng với email đó
        $jwt = $_GET['jwt'];
        $key = JWT_KEY;
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        $email = $decoded->email;
        // Sau đó active account tương ứng với email đó 
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);

        // $customer xuống database
        $customer->setIsActive(1); // kích hoạt tài khoản

        // Lưu xuống database
        if (!$customerRepository->update($customer)) {
            // lưu thất bại
            $_SESSION['error'] = $customerRepository->getError();
            header('location: /');
            exit;
        }

        // lưu thành công
        $_SESSION['success'] = 'Đã kích hoạt tài khoản thành công, vui lòng đăng nhập để sử dụng';
        header('location: /');
    }

    function forgotPassword()
    {
        $email = $_POST['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        if (empty($customer)) {
            $_SESSION['error'] = "Email $email không tồn tại.";
            header('location: /');
            exit;
        }
        $emailService = new EmailService();
        $to = $email;
        $subject = 'Godashop - Reset Password';
        $name = $customer->getName();

        $website = get_domain();
        $key = JWT_KEY;
        // data
        $payload = [
            'email' => $to,
        ];
        $jwt = JWT::encode($payload, $key, 'HS256');

        $link = get_domain_site() . '?c=customer&a=resetPassword&jwt=' . $jwt;
        $resetPasswordLink = "<a href='$link'>Reset Password</a>";
        $content = "
        Dear $name, <br>
        Vui lòng click vào link bên dưới để reset password của bạn <br>
        $resetPasswordLink <br>
        ------------------------- <br>
        Được gởi từ trang web $website
        ";

        $emailService->send($to, $subject, $content);

        $_SESSION['success'] = "Đã gởi link reset password, vui lòng vào email $email để reset password";
        header('location: /');
    }

    function resetPassword()
    {
        $jwt = $_GET['jwt'];
        require 'view/customer/resetPassword.php';
    }

    function updatePassword()
    {
        $jwt = $_POST['jwt'];
        $password = $_POST['password'];

        $key = JWT_KEY;
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        $email = $decoded->email;
        // Sau đó active account tương ứng với email đó 
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        // cập nhập new passowrd này vào đối tượng customer 
        $customer->setPassword($password_hash);

        // Lưu xuống database
        if (!$customerRepository->update($customer)) {
            // lưu thất bại
            $_SESSION['error'] = $customerRepository->getError();
            header('location: /');
            exit;
        }

        // lưu thành công
        $_SESSION['success'] = 'Đã reset password thành công, vui lòng đăng nhập để sử dụng';
        header('location: /');
    }
}
