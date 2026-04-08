<?php
session_start();
include "../includes/config.php";

if (!isset($_GET['id'])) {
    header("Location: ../index.php");
    exit();
}

$order_id = mysqli_real_escape_string($conn, $_GET['id']);

// 1. Lấy thông tin đơn hàng (Lấy các cột đã được lưu từ save_order.php)
$sql_order = "SELECT * FROM orders WHERE order_id = '$order_id'";
$res_order = mysqli_query($conn, $sql_order);
$order = mysqli_fetch_assoc($res_order);

if (!$order) {
    die("Đơn hàng không tồn tại.");
}

// 2. Lấy chi tiết sản phẩm
$sql_detail = "SELECT od.*, p.name, p.image 
                FROM order_details od 
                JOIN products p ON od.product_id = p.product_id 
                WHERE od.order_id = '$order_id'";
$res_detail = mysqli_query($conn, $sql_detail);

// 3. ĐỒNG BỘ DỮ LIỆU TIỀN TỆ TỪ DATABASE
$grand_total = (int)$order['total_price'];
$discount = (int)$order['discount_amount'];
$shipping = (int)$order['shipping_fee'];
$subtotal = $grand_total + $discount - $shipping; // Tính ngược lại để ra tạm tính
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đơn hàng #<?php echo $order_id; ?></title>
    <link rel="stylesheet" href="../../css/index.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --sage-dark: #2d3e40;
            --sage: #5f7d73;
            --be: #f8f7f2;
            --accent: #c2410c;
        }
        .success-wrapper { padding: 40px 0; background: var(--be); min-height: 80vh; }
        .success-card { background: #fff; padding: 40px; border-radius: 24px; box-shadow: 0 10px 40px rgba(0,0,0,0.05); max-width: 900px; margin: 0 auto; text-align: center; }
        .success-icon { font-size: 4rem; color: #16a34a; margin-bottom: 20px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; text-align: left; margin: 30px 0; }
        .info-box { background: #f9fafb; padding: 20px; border-radius: 16px; border: 1px solid #f1f5f9; }
        .info-box h4 { color: var(--sage-dark); margin-bottom: 12px; font-weight: 800; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; }
        .order-table { width: 100%; border-collapse: collapse; margin: 25px 0; text-align: left; }
        .order-table th { padding: 12px; border-bottom: 2px solid #f1f5f9; color: #64748b; font-size: 13px; }
        .order-table td { padding: 15px 12px; border-bottom: 1px solid #f1f5f9; }
        .order-summary-block { margin-top: 30px; padding: 25px; background: #fafaf9; border-radius: 16px; width: 100%; max-width: 400px; margin-left: auto; border: 1px solid #eee; }
        .summary-line { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 15px; }
        .summary-line.grand-total { margin-top: 15px; padding-top: 15px; border-top: 2px solid #e5e7eb; }
        .total-amount { font-size: 26px; font-weight: 900; color: var(--sage); }
        .voucher-tag { font-size: 11px; background: #ffedd5; color: var(--accent); padding: 2px 8px; border-radius: 4px; border: 1px dashed var(--accent); margin-left: 5px; }
        .btn-group { display: flex; gap: 15px; justify-content: center; margin-top: 35px; }
        .btn-primary { background: var(--sage-dark); color: white; padding: 15px 30px; border-radius: 12px; text-decoration: none; font-weight: 700; }
        .btn-outline { border: 2px solid var(--sage-dark); color: var(--sage-dark); padding: 13px 30px; border-radius: 12px; text-decoration: none; font-weight: 700; }
    </style>
</head>
<body>
    <main class="success-wrapper">
        <div class="container">
            <div class="success-card">
                <i class="fa-solid fa-circle-check success-icon"></i>
                <h1 style="color: var(--sage-dark); font-weight: 900;">Đặt hàng thành công!</h1>
                <p>Mã đơn hàng: <b>#SW-<?php echo $order_id; ?></b></p>

                <div class="info-grid">
                    <div class="info-box">
                        <h4><i class="fa-solid fa-truck"></i> Thông tin nhận hàng</h4>
                        <p><strong>Người nhận:</strong> <?php echo htmlspecialchars($order['customer_name'] ?? 'Khách hàng'); ?></p>
                        <p><strong>Điện thoại:</strong> <?php echo htmlspecialchars($order['phone'] ?? 'N/A'); ?></p>
                        <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order['address'] ?? 'N/A'); ?></p>
                    </div>

                    <div class="info-box">
                        <h4><i class="fa-solid fa-credit-card"></i> Thanh toán</h4>
                        <p><strong>Trạng thái:</strong> <span style="color: #ea580c; font-weight: 700;"><?php echo $order['status']; ?></span></p>
                        <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                        <p><strong>Phương thức:</strong> <?php echo (strpos($order['note'], 'Transfer') !== false) ? 'Chuyển khoản' : 'COD'; ?></p>
                    </div>
                </div>

                <table class="order-table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th style="text-align: center;">SL</th>
                            <th style="text-align: right;">Đơn giá</th>
                            <th style="text-align: right;">Tổng cộng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($item = mysqli_fetch_assoc($res_detail)): ?>
                        <tr>
                            <td style="display: flex; align-items: center; gap: 10px;">
                                <img src="../../img/<?php echo $item['image']; ?>" width="40" style="border-radius: 4px;">
                                <span><?php echo $item['name']; ?></span>
                            </td>
                            <td style="text-align: center;">x<?php echo $item['quantity']; ?></td>
                            <td style="text-align: right;"><?php echo number_format($item['price']); ?>đ</td>
                            <td style="text-align: right; font-weight: 700;"><?php echo number_format($item['price'] * $item['quantity']); ?>đ</td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <div class="order-summary-block">
    <div class="summary-line">
        <span>Tạm tính</span>
        <strong><?php echo number_format($subtotal); ?>đ</strong>
    </div>

    <?php if ($discount > 0): ?>
    <div class="summary-line" style="color: var(--accent);">
        <span>Giảm giá (<?php echo $order['voucher_code']; ?>)</span>
        <strong>-<?php echo number_format($discount); ?>đ</strong>
    </div>
    <?php endif; ?>

    <div class="summary-line">
        <span>Phí vận chuyển</span>
        <strong><?php echo ($shipping == 0) ? "Miễn phí" : number_format($shipping)."đ"; ?></strong>
    </div>

    <div class="summary-line grand-total">
        <span>TỔNG THANH TOÁN</span>
        <span class="total-amount"><?php echo number_format($grand_total); ?>đ</span>
    </div>
</div>

                <div class="btn-group">
                    <a href="../products/products.php" class="btn-outline">Tiếp tục mua sắm</a>
                    <a href="myorders.php" class="btn-primary">Quản lý đơn hàng</a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>