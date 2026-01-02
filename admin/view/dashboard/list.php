<?php include "layout/header.php"; ?>

<style>
   /* 1. Gradient Cards: Màu chuyển sắc sang trọng */
   .bg-gradient-primary {
      background: linear-gradient(45deg, #4e73df 0%, #224abe 100%);
   }

   .bg-gradient-success {
      background: linear-gradient(45deg, #1cc88a 0%, #13855c 100%);
   }

   .bg-gradient-info {
      background: linear-gradient(45deg, #36b9cc 0%, #258391 100%);
   }

   .bg-gradient-warning {
      background: linear-gradient(45deg, #f6c23e 0%, #dda20a 100%);
   }

   .bg-gradient-danger {
      background: linear-gradient(45deg, #e74a3b 0%, #be2617 100%);
   }

   /* 2. Card Design */
   .card-dashboard {
      border: none;
      border-radius: 12px;
      box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
      transition: all 0.3s ease;
      overflow: hidden;
      position: relative;
   }

   .card-dashboard:hover {
      transform: translateY(-5px);
      box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.25);
   }

   .card-dashboard .text-xs {
      font-size: .7rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .05rem;
      opacity: 0.8;
   }

   .card-dashboard .h5 {
      font-size: 1.25rem;
      font-weight: 700;
      margin-bottom: 0;
   }

   /* Icon chìm làm nền */
   .card-dashboard .icon-bg {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 4rem;
      opacity: 0.15;
      color: #fff;
      z-index: 0;
   }

   .card-content {
      position: relative;
      z-index: 1;
   }

   /* 3. Layout Bảng & Biểu đồ */
   .card-box {
      background: #fff;
      border: none;
      border-radius: 12px;
      box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
   }

   .card-header-pro {
      background: transparent;
      border-bottom: 1px solid #e3e6f0;
      padding: 1.2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-weight: bold;
      color: #4e73df;
   }

   /* Danh sách tóm tắt bên phải biểu đồ */
   .summary-item {
      padding: 15px 0;
      border-bottom: 1px dashed #e3e6f0;
      display: flex;
      justify-content: space-between;
      align-items: center;
   }

   .summary-item:last-child {
      border-bottom: none;
   }

   .summary-label {
      color: #858796;
      font-size: 0.9rem;
   }

   .summary-value {
      font-weight: bold;
      color: #5a5c69;
   }
</style>

<div id="content-wrapper">
   <div class="container-fluid">
      <div class="d-sm-flex align-items-center justify-content-between mb-4">
         <h1 class="h3 mb-0 text-gray-800">Tổng quan kinh doanh</h1>

         <?php $type = !empty($_GET["type"]) ? $_GET["type"] : "today"; ?>
         <div class="d-none d-sm-inline-block">
            <div class="btn-group shadow-sm" role="group">
               <a href="index.php?type=today&from_date=<?= date("Y-m-d") ?>&to_date=<?= date("Y-m-d") ?>"
                  class="btn btn-sm btn-white text-primary border <?= $type == "today" ? "active bg-primary text-white" : "" ?>">Hôm
                  nay</a>
               <a href="index.php?type=yesterday&from_date=<?= date("Y-m-d", strtotime("-1 days")) ?>&to_date=<?= date("Y-m-d", strtotime("-1 days")) ?>"
                  class="btn btn-sm btn-white text-primary border <?= $type == "yesterday" ? "active bg-primary text-white" : "" ?>">Hôm
                  qua</a>
               <a href="index.php?type=thisweek&from_date=<?= date("Y-m-d", strtotime("this week")) ?>&to_date=<?= date("Y-m-d") ?>"
                  class="btn btn-sm btn-white text-primary border <?= $type == "thisweek" ? "active bg-primary text-white" : "" ?>">Tuần
                  này</a>
               <a href="index.php?type=thismonth&from_date=<?= date("Y-m-d", strtotime("this month")) ?>&to_date=<?= date("Y-m-d") ?>"
                  class="btn btn-sm btn-white text-primary border <?= $type == "thismonth" ? "active bg-primary text-white" : "" ?>">Tháng
                  này</a>
            </div>
            <div class="dropdown d-inline-block ml-2">
               <button class="btn btn-sm btn-primary shadow-sm dropdown-toggle" type="button"
                  data-toggle="dropdown">
                  <i class="fas fa-calendar-alt fa-sm text-white-50"></i> Tùy chọn
               </button>
               <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in p-3" style="width:300px">
                  <form action="index.php">
                     <div class="form-group mb-2">
                        <small class="text-muted">Từ ngày:</small>
                        <input type="date" class="form-control form-control-sm" name="from_date" required
                           value="<?= $type == "custom" ? $_GET["from_date"] : "" ?>">
                     </div>
                     <div class="form-group mb-2">
                        <small class="text-muted">Đến ngày:</small>
                        <input type="date" class="form-control form-control-sm" name="to_date" required
                           value="<?= $type == "custom" ? $_GET["to_date"] : "" ?>">
                     </div>
                     <input type="hidden" value="custom" name="type">
                     <button type="submit" class="btn btn-primary btn-sm btn-block">Lọc dữ liệu</button>
                  </form>
               </div>
            </div>
         </div>
      </div>

      <?php
      // --- XỬ LÝ LOGIC PHP (Nâng cao) ---
      $revenue = 0;
      $cancel_number = 0;
      $shipping_total = 0; // Thêm: Tổng tiền ship
      $max_order_value = 0; // Thêm: Đơn hàng giá trị cao nhất
      $count_orders = 0;

      if (!empty($orders)) {
         $count_orders = count($orders);
         foreach ($orders as $order) {
            // 1. Check Status
            $status = method_exists($order, 'getStatusId') ? $order->getStatusId() : (isset($order->status_id) ? $order->status_id : 0);
            if ($status == 6) {
               $cancel_number++;
            }

            // 2. Tính tiền
            $orderTotal = 0;
            $items = method_exists($order, 'getOrderItems') ? $order->getOrderItems() : [];
            if (!empty($items)) {
               foreach ($items as $item) {
                  $orderTotal += method_exists($item, 'getTotalPrice') ? $item->getTotalPrice() : 0;
               }
            }

            $ship = method_exists($order, 'getShippingFee') ? $order->getShippingFee() : 0;
            $shipping_total += $ship;
            $orderTotal += $ship;

            $revenue += $orderTotal;

            // Tìm đơn hàng cao nhất
            if ($orderTotal > $max_order_value) {
               $max_order_value = $orderTotal;
            }
         }
      }

      // Tính trung bình đơn hàng (AOV)
      $success_orders = $count_orders - $cancel_number;
      $avg_order_value = ($success_orders > 0) ? ($revenue / $success_orders) : 0;
      ?>

      <div class="row">
         <div class="col-xl-3 col-md-6 mb-4">
            <div class="card-dashboard bg-gradient-primary text-white h-100 py-2">
               <div class="card-body">
                  <div class="card-content">
                     <div class="text-xs">Tổng Đơn Hàng</div>
                     <div class="h5"><?= number_format($count_orders) ?></div>
                  </div>
                  <i class="fas fa-clipboard-list icon-bg"></i>
               </div>
            </div>
         </div>

         <div class="col-xl-3 col-md-6 mb-4">
            <div class="card-dashboard bg-gradient-success text-white h-100 py-2">
               <div class="card-body">
                  <div class="card-content">
                     <div class="text-xs">Doanh thu</div>
                     <div class="h5"><?= number_format($revenue) ?> <small>đ</small></div>
                  </div>
                  <i class="fas fa-dollar-sign icon-bg"></i>
               </div>
            </div>
         </div>

         <div class="col-xl-3 col-md-6 mb-4">
            <div class="card-dashboard bg-gradient-info text-white h-100 py-2">
               <div class="card-body">
                  <div class="card-content">
                     <div class="text-xs">Giá trị trung bình/đơn</div>
                     <div class="h5"><?= number_format($avg_order_value) ?> <small>đ</small></div>
                  </div>
                  <i class="fas fa-chart-line icon-bg"></i>
               </div>
            </div>
         </div>

         <div class="col-xl-3 col-md-6 mb-4">
            <div class="card-dashboard bg-gradient-danger text-white h-100 py-2">
               <div class="card-body">
                  <div class="card-content">
                     <div class="text-xs">Đơn hàng bị hủy</div>
                     <div class="h5" style="color: #ffffff;"><?= $cancel_number ?></div>
                  </div>
                  <i class="fas fa-trash-alt icon-bg"></i>
               </div>
            </div>
         </div>
      </div>

      <div class="row">
         <div class="col-xl-8 col-lg-7">
            <div class="card card-box mb-4">
               <div class="card-header-pro">
                  <span><i class="fas fa-chart-bar mr-2"></i> Biểu đồ doanh số</span>
               </div>
               <div class="card-body">
                  <div class="chart-area">
                     <canvas id="myRevenueChart" style="height: 320px; width: 100%;"></canvas>
                  </div>
               </div>
            </div>
         </div>

         <div class="col-xl-4 col-lg-5">
            <div class="card card-box mb-4">
               <div class="card-header-pro">
                  <span><i class="fas fa-lightbulb mr-2"></i> Tiêu điểm</span>
               </div>
               <div class="card-body">
                  <div class="summary-item">
                     <span class="summary-label">Đơn cao nhất:</span>
                     <span class="summary-value text-success"><?= number_format($max_order_value) ?> đ</span>
                  </div>
                  <div class="summary-item">
                     <span class="summary-label">Tổng phí vận chuyển:</span>
                     <span class="summary-value"><?= number_format($shipping_total) ?> đ</span>
                  </div>
                  <div class="summary-item">
                     <span class="summary-label">Tỷ lệ thành công:</span>
                     <span class="summary-value">
                        <?php
                        $rate = ($count_orders > 0) ? round(($success_orders / $count_orders) * 100, 1) : 0;
                        echo $rate . "%";
                        ?>
                     </span>
                  </div>

                  <div class="mt-4 pt-3 border-top">
                     <div class="text-center small text-muted mb-2">Trạng thái hệ thống</div>
                     <div class="progress mb-1" style="height: 10px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= $rate ?>%">
                        </div>
                        <div class="progress-bar bg-danger" role="progressbar"
                           style="width: <?= 100 - $rate ?>%">
                        </div>
                     </div>
                     <div class="d-flex justify-content-between small">
                        <span class="text-success">Hoàn thành</span>
                        <span class="text-danger">Hủy</span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <div class="card card-box mb-4">
         <div class="card-header-pro">
            <span><i class="fas fa-table mr-2"></i> Chi tiết giao dịch</span>
         </div>
         <div class="card-body">
            <div class="table-responsive">
               <?php include "layout/orders.php" ?>
            </div>
         </div>
      </div>
   </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script>
   document.addEventListener("DOMContentLoaded", function() {
      // Lấy dữ liệu từ PHP
      var totalOrders = <?= $count_orders ?>;
      var totalCancel = <?= $cancel_number ?>;
      var successOrders = totalOrders - totalCancel;

      // Cấu hình Font chữ đẹp hơn
      Chart.defaults.global.defaultFontFamily =
         'Nunito, -apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
      Chart.defaults.global.defaultFontColor = '#858796';

      var ctx = document.getElementById("myRevenueChart");
      if (ctx) {
         var myChart = new Chart(ctx, {
            type: 'bar', // Có thể đổi thành 'doughnut' hoặc 'pie' nếu thích
            data: {
               labels: ["Tổng đơn hàng", "Thành công", "Đã hủy"],
               datasets: [{
                  label: "Số lượng",
                  backgroundColor: ["#4e73df", "#1cc88a", "#e74a3b"],
                  hoverBackgroundColor: ["#2e59d9", "#17a673", "#be2617"],
                  borderColor: "#ffffff",
                  data: [totalOrders, successOrders, totalCancel],
               }],
            },
            options: {
               maintainAspectRatio: false,
               layout: {
                  padding: {
                     left: 10,
                     right: 25,
                     top: 25,
                     bottom: 0
                  }
               },
               scales: {
                  xAxes: [{
                     gridLines: {
                        display: false,
                        drawBorder: false
                     },
                     barPercentage: 0.5
                  }],
                  yAxes: [{
                     ticks: {
                        beginAtZero: true,
                        stepSize: 1,
                        padding: 10
                     },
                     gridLines: {
                        color: "rgb(234, 236, 244)",
                        borderDash: [2],
                        drawBorder: false
                     }
                  }]
               },
               legend: {
                  display: false
               },
               tooltips: {
                  backgroundColor: "rgb(255,255,255)",
                  bodyFontColor: "#858796",
                  titleFontColor: '#6e707e',
                  borderColor: '#dddfeb',
                  borderWidth: 1,
                  xPadding: 15,
                  yPadding: 15,
                  displayColors: false,
                  intersect: false,
                  mode: 'index',
                  caretPadding: 10
               }
            }
         });
      }
   });
</script>
<?php include "layout/footer.php"; ?>