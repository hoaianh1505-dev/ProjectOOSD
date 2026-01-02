<?php
class HomeController
{
    function index()
    {
        $conds = []; //không có condition
        $page = 1;
        $item_per_page = 4;
        $productRepository = new ProductRepository(); // xuống model

        // lấy 4 sản phẩm nổi bật
        $sorts = ['featured' => 'DESC'];
        $featuredProducts = $productRepository->getBy($conds, $sorts, $page, $item_per_page);
        // SELECT * FROM view_product ORDER BY featured DESC LIMIT 0,4;

        // lấy 4 sản phẩm mới nhất
        $sorts = ['created_date' => 'DESC'];
        $latestProducts = $productRepository->getBy($conds, $sorts, $page, $item_per_page);
        // SELECT * FROM view_product ORDER BY created_date DESC LIMIT 0,4;

        // Đổ cấu trúc dữ liệu phức tạp ra view
        // danh sách, mà mỗi phần tử bao gồm 2 phần:
        // + Tên danh mục	
        // + Danh sách sản phẩm theo danh mục
        $categoryProducts = [];
        // Lấy tất cả danh mục
        $categoryRepository = new CategoryRepository();
        $categories = $categoryRepository->getAll();
        // Loop để xết từng dang mục
        foreach ($categories as $category) {
            $categoryName = $category->getName();
            $category_id = $category->getId(); //3
            // Lấy danh sách sản phẩm theo danh mục tương ứng
            $conds = [
                'category_id' => [
                    'type' => '=',
                    'val' => $category_id
                ]
            ];
            // SELECT * FROM view_product WHERE category_id=3;
            $products = $productRepository->getBy($conds, $sorts, $page, $item_per_page);

            $categoryProducts[] = [
                'categoryName' => $categoryName,
                'products' => $products
            ];
        }
        require 'view/home/index.php';
    }
}
