<?php require 'layout/header.php' ?>
<main id="maincontent" class="page-main">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <ol class="breadcrumb">
                    <li><a href="/" target="_self">Giỏ hàng</a></li>
                    <li><span>/</span></li>
                    <li class="active"><span>Thông tin giao hàng</span></li>
                </ol>
            </div>
        </div>
        <div class="row">
            <aside class="col-md-6 cart-checkout">
                <?php foreach ($cart->getItems() as $item): ?>
                    <div class="row">
                        <div class="col-xs-2">
                            <img class="img-responsive" src="../upload/<?= $item['img'] ?>" alt="<?= $item['name'] ?>">
                        </div>
                        <div class="col-xs-7">
                            <a class="product-name"
                                href="?c=product&=detail&id=<?= $item['product_id'] ?>"><?= $item['name'] ?></a>
                            <br>
                            <span><?= $item['qty'] ?></span> x <span><?= formatMoney($item['unit_price']) ?>₫</span>
                        </div>
                        <div class="col-xs-3 text-right">
                            <span><?= formatMoney($item['total_price']) ?>₫</span>
                        </div>
                    </div>
                    <hr>
                <?php endforeach; ?>
                <div class="row">
                    <div class="col-xs-6">
                        Tạm tính
                    </div>
                    <div class="col-xs-6 text-right">
                        <?= formatMoney($cart->getTotalPrice()) ?>₫
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        Phí vận chuyển
                    </div>
                    <div class="col-xs-6 text-right">
                        <?php $shipping_fee = 50000 //later 
                        ?>
                        <span class="shipping-fee" data=""><?= formatMoney($shipping_fee) ?>₫</span>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-xs-6">
                        Tổng cộng
                    </div>
                    <div class="col-xs-6 text-right">
                        <span class="payment-total"
                            data="<?= $cart->getTotalPrice() ?>"><?= formatMoney($cart->getTotalPrice() + $shipping_fee) ?>₫</span>
                    </div>
                </div>
            </aside>
            <div class="ship-checkout col-md-6">
                <h4>Thông tin giao hàng</h4>

                <form action="?c=payment&a=order" method="POST" class="form-checkout">
                    <?php require 'layout/address.php' ?>
                    <h4>Phương thức thanh toán</h4>
                    <div class="form-group">
                        <label> <input type="radio" name="payment_method" checked="" value="0"> Thanh toán khi giao hàng
                            (COD) </label>
                        <div></div>
                    </div>
                    <div class="form-group">
                        <label class="fake-modal-qr"> <input type="radio" name="payment_method" value="1"> Chuyển khoản
                            qua ngân hàng </label>
                        <!-- Modal -->
                        <!-- Button trigger modal -->
                        <button type="button" id="btn-modal" class="btn btn-border-primary"
                            style="background-color:rgb(from blu 34 5 90)" data-toggle="modal"
                            data-target="#staticBackdrop">
                            Mã qr thanh toán
                        </button>
                        <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false"
                            tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog ">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="staticBackdropLabel"></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- <img class="img-responsive " src="../upload/qr1.png
                                    " alt="" style="width: 400px; height :auto" /> -->
                                        <img class="image_bank"
                                            src="https://img.vietqr.io/image/vpbank-0965337849-compact2.jpg?amount=<?= $cart->getTotalPrice() + $shipping_fee ?>&addInfo=thanh%20toan%20don%20v%20hang&accountName=CTK:%20NGUYEN%20DUC%20THANH%20LONG."
                                            alt="" style="width: auto; height: 500px;">


                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Close</button>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



            </div>
            <div>
                <button type="submit" class="btn btn-sm btn-primary pull-right">Hoàn tất đơn hàng</button>
            </div>
            </form>
        </div>
    </div>
    </div>
</main>
<?php require 'layout/footer.php' ?>