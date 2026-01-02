<?php
class ProductController
{
    // Hiển thị trang danh sách sản phẩm
    function index()
    {
        // Lấy 10 sản phẩm đổ ra view
        $conds = [];
        $sorts = [];
        $page = $_GET['page'] ?? 1;
        $item_per_page = 10;
        $productRepository = new ProductRepository();

        // Lấy sản phẩm theo category
        // category_id=3
        $category_id = $_GET['category_id'] ?? '';
        if ($category_id) {
            $conds = [
                'category_id' => [
                    'type' => '=',
                    'val' => $category_id //3
                ]
            ];
            // SELECT * FROM view_product WHERE category_id=3
        }

        // Lấy sản phẩm theo khoảng giá
        // price-range=200000-300000
        $price_range = $_GET['price-range'] ?? '';
        if ($price_range) {
            $temp = explode('-', $price_range);
            $start_price = $temp[0]; //200000
            $end_price = $temp[1]; //300000
            $conds = [
                'sale_price' => [
                    'type' => 'BETWEEN',
                    'val' => "$start_price AND $end_price" //200000 AND 300000
                ]
            ];
            // SELECT * FROM view_product WHERE sale_price BETWEEN 200000 AND 300000
            // Trường hợp 1000000-greater
            if ($end_price == 'greater') {
                $conds = [
                    'sale_price' => [
                        'type' => '>=',
                        'val' => $start_price //1000000
                    ]
                ];
                // SELECT * FROM view_product WHERE sale_price >= 100000
            }
        }

        // Tìm kiếm theo từ khóa
        // search=kem
        $search = $_GET['search'] ?? '';

        if ($search) {
            $conds = [
                'name' => [
                    'type' => 'LIKE',
                    'val' => "'%$search%'" //"'%kem%'"
                ]
            ];
            // SELECT * FROM view_product WHERE name LIKE '%kem%'
        }

        // sort=price-asc
        $sort = $_GET['sort'] ?? '';
        if ($sort) {
            $map = [
                // sau dấu => là cột trong view_product
                'price' => 'sale_price',
                'alpha' => 'name',
                'created' => 'created_date',
            ];
            $temp = explode('-', $sort);
            $first = $temp[0]; //price
            $second = $temp[1]; //asc

            $colName = $map[$first];

            $order = strtoupper($second); //asc -> ASC
            $sorts = [$colName => $order]; //[sale_price => ASC]
            // SELECT * FROM view_product ORDER BY sale_price ASC
        }

        $products =  $productRepository->getBy($conds, $sorts, $page, $item_per_page);

        $totalProductNumber =  $productRepository->getByNumber($conds, $sorts);

        // Lấy tất cả danh mục đổ ra view
        $categoryRepository = new CategoryRepository();
        $categories = $categoryRepository->getAll();

        $totalPage = ceil($totalProductNumber / $item_per_page);

        require 'view/product/index.php';
    }

    function detail()
    {
        $id = $_GET['id']; //3
        $productRepository = new ProductRepository();
        $product = $productRepository->find($id);
        $category_id = $product->getCategoryId(); //2
        // sản phẩm có liên quan
        $conds = [
            'category_id' => [
                'type' => '=',
                'val' => $category_id //2
            ],
            'id' => [
                'type' => '!=',
                'val' => $id //3
            ]
        ];
        // SELECT * FROM view_product WHERE category_id=2 AND id != 3;
        $relatedProducts = $productRepository->getBy($conds);

        // Lấy tất cả danh mục đổ ra view
        $categoryRepository = new CategoryRepository();
        $categories = $categoryRepository->getAll();

        require 'view/product/detail.php';
    }

    function storeComment()
    {
        $data = [
            'product_id' => $_POST['product_id'],
            'email' => $_POST['email'],
            'fullname' => $_POST['fullname'],
            'star' => $_POST['rating'],
            'created_date' => date('Y-m-d H:i:s'), //2025-04-20 20:59:17,
            'description' => $_POST['description'],
        ];
        $commentRepository = new CommentRepository();
        $commentRepository->save($data);

        $productRepository = new ProductRepository();
        $id = $_POST['product_id'];
        $product = $productRepository->find($id);

        require 'view/product/comments.php';
    }
}
