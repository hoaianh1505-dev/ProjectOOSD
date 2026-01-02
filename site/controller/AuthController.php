<?php
// Auth là authentication (xác thực)
// Authorization (phân quyền)
class AuthController
{
    function login()
    {
        $email = $_POST['email'];
        $password = $_POST['password'];
        //1. Kiểm tra tài khoản này có tồn tại trong hệ thông không?
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        if (!$customer) {
            // nếu hok có thì báo lỗi
            $_SESSION['error'] = "Email $email này không tồn tại trong hệ thống";
            // điều hướng về trang chủ
            header('location: /');
            exit;
        }
        // 2. Kiểm tra đúng mật khẩu hok?
        if (!password_verify($password, $customer->getPassword())) {
            // nếu sai mật khẩu
            $_SESSION['error'] = "Sai mật khẩu";
            // điều hướng về trang chủ
            header('location: /');
            exit;
        }

        // 3. Kiểm tra xem tài khoản đã được kích hoạt chưa?
        if (!$customer->getIsActive()) {
            // nếu chưa được kích hoạt tài khoản
            $_SESSION['error'] = "Tài khoản chưa được kích hoạt, vui lòng vào email để kích hoạt tài khoản";
            // điều hướng về trang chủ
            header('location: /');
            exit;
        }
        // Login thành công
        // Lưu email và tên người dùng vào session
        $_SESSION['email'] = $email;
        $_SESSION['name'] = $customer->getName();
        // Điều hướng về trang thông tin tài khoản
        header('location: ?c=customer&a=show');
    }

    function logout()
    {
        // hủy tất cả các session hay nói cách khác $_SESSION trở thành array rỗng không có phần tử nào
        session_destroy();
        header('location: /');
    }

    function loginGoogle()
    {
        try {
            $clientID = GOOGLE_CLIENT_ID;
            $clientSecret = GOOGLE_CLIENT_SECRET;
            $redirectUri = get_domain() . $_SERVER['PHP_SELF'] . "?c=auth&a=loginGoogle";

            // create Client Request to access Google API
            $client = new Google_Client();
            $client->setClientId($clientID);
            $client->setClientSecret($clientSecret);
            $client->setRedirectUri($redirectUri);
            $client->addScope("email");
            $client->addScope("profile");

            if (isset($_GET['code'])) {
                $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

                $client->setAccessToken($token['access_token']);

                // get profile info
                $google_oauth = new Google_Service_Oauth2($client);
                $google_account_info = $google_oauth->userinfo->get();
                $email =  $google_account_info->email;
                $name =  $google_account_info->name;
                $this->createCustomerBySocial($email, $name, "google");
                $this->setupLoginEnv($email, $name);
                header("location: index.php");
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    function createCustomerBySocial($email, $name, $type)
    {
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);

        if (empty($customer)) {
            //create new customer
            $data = array(
                "name" => $name,
                "mobile" => "",
                "password" => "",
                "email" => $email,
                "shipping_name" => $name,
                "shipping_mobile" => "",
                "ward_id" => null,
                "housenumber_street" => null,
                "login_by" => $type,
                "is_active" => 1
            );
            $customerRepository->save($data);
        }
    }

    function setupLoginEnv($email, $name, $remember_me = null)
    {
        $_SESSION["email"] = $email;
        $_SESSION["name"] = $name;
    }
}
