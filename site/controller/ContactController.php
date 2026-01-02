<?php



class ContactController
{
    // Trang liên hệ
    function form()
    {
        require 'view/contact/form.php';
    }

    // Gởi mail đến chủ cửa hàng
    function sendEmail()
    {
        $name = $_POST['fullname'];
        $mobile = $_POST['mobile'];
        $email = $_POST['email'];
        $message = $_POST['content'];

        $emailService = new EmailService();
        $to = SHOP_OWNER;
        $website = get_domain();

        $subject = 'The Bloom Studio - Liên hệ';
        $content = "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>The Bloom Studio - Liên hệ</title>
            <style>
                span {
                    color:red;
                    background-color:yellow;
                }
            </style>
        </head>
        <body>
            <span>Chào chủ cửa hàng</span>,<br>
            Dưới đây là thông tin khách hàng liên hệ:<br>
            Tên: $name <br>
            Sdt: $mobile <br>
            Email: $email <br>
            Nội dung: $message<br>
            ---------------------<br>
            Được gởi từ trang web: $website
        </body>
        </html>
        
        

        ";
        $emailService->send($to, $subject, $content);
        echo 'Đã gởi mail thành công';
        // Tạm thời ba xạo cái
        //Create an instance; passing `true` enables exceptions

        // gởi mail thật
    }
}
