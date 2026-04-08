<?php
session_start();
include "../includes/config.php";

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: ../products/products.php");
    exit();
}

// 1. Tính toán giá trị giỏ hàng
$total_temp = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_temp += $item['price'] * $item['quantity'];
}

// 2. Xử lý giảm giá
$discount = 0;
$coupon_code = "";
if (isset($_SESSION['coupon'])) {
    $coupon_code = $_SESSION['coupon']['code'];
    $discount = ($_SESSION['coupon']['type'] == 'percent') 
                ? ($total_temp * $_SESSION['coupon']['value'] / 100) 
                : $_SESSION['coupon']['value'];
}

// 3. Phí vận chuyển và Tổng cộng
$shipping_fee = ($total_temp > 500000) ? 0 : 20000;
$grand_total = $total_temp - $discount + $shipping_fee;
if ($grand_total < 0) $grand_total = 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán - Sagewood Books</title>
    <link rel="stylesheet" href="../../css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --sage-dark: #2d3e40;
            --sage: #5f7d73;
            --border: #e5e7eb;
            --be: #f8f7f2;
        }
        body { background: var(--be); font-family: 'Inter', sans-serif; }
        .nav { display: flex; justify-content: space-between; align-items: center; padding: 20px 0; }
        .logo { font-size: 24px; font-weight: 900; color: var(--sage-dark); text-decoration: none; }
        .logo span { color: var(--sage); }
        
        /* Grid Layout */
        .checkout-container {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
            margin-bottom: 50px;
        }

        .summary-box { 
            background: white; 
            border-radius: 24px; 
            padding: 30px; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            border: 1px solid var(--border);
        }

        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; font-weight: 700; color: var(--sage-dark); margin-bottom: 8px; font-size: 14px; }
        .input-group input, .input-group textarea, .input-group select {
            width: 100%; padding: 14px; border: 1px solid var(--border); border-radius: 12px; outline: none; transition: 0.3s;
        }
        .input-group input:focus { border-color: var(--sage); box-shadow: 0 0 0 4px rgba(95, 125, 115, 0.1); }

        /* Payment Styles */
        .payment-item { 
            border: 1px solid var(--border); border-radius: 16px; padding: 18px; 
            display: flex; align-items: center; gap: 15px; cursor: pointer; margin-bottom: 12px; transition: 0.3s;
        }
        .payment-item:has(input:checked) { border-color: var(--sage); background: #f9faf9; border-width: 2px; }
        
        /* Sidebar Styles */
        .mini-item { display: flex; gap: 15px; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #f3f4f6; }
        .mini-item img { width: 45px; height: 65px; object-fit: cover; border-radius: 6px; }
        .sum-line { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 15px; color: #6b7280; }
        .sum-line strong { color: var(--sage-dark); }
        .grand-total-line { border-top: 2px solid #f3f4f6; margin-top: 15px; padding-top: 20px; }

        @media (max-width: 992px) {
            .checkout-container { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<header style="background: white; border-bottom: 1px solid var(--border);">
    <div class="container">
        <div class="nav">
            <a class="logo" href="../index.php"><span>Sagewood</span> Books</a>
            <a href="cart.php" style="color: var(--sage-dark); font-weight: 700; text-decoration: none; font-size: 14px;">
                <i class="fa-solid fa-arrow-left"></i> Quay lại giỏ hàng
            </a>
        </div>
    </div>
</header>

<main class="container" style="padding-top: 40px;">
    <h2 style="font-weight: 900; color: var(--sage-dark); margin-bottom: 30px;">Thanh toán đơn hàng</h2>

    <form action="save_order.php" method="POST" class="checkout-container">
        
        <input type="hidden" name="total_price" value="<?php echo $grand_total; ?>">
        <input type="hidden" name="discount_amount" value="<?php echo $discount; ?>">
        <input type="hidden" name="shipping_fee" value="<?php echo $shipping_fee; ?>">
        <input type="hidden" name="voucher_code" value="<?php echo $coupon_code; ?>">

        <div class="checkout-left">
            <div class="summary-box">
                <h3 style="margin-bottom: 25px; font-weight: 900; display: flex; align-items: center; gap: 10px; color: var(--sage-dark);">
                    <i class="fa-solid fa-truck-fast"></i> Thông tin giao hàng
                </h3>
                
                <div class="input-group">
                    <label>Họ và tên</label>
                    <input type="text" name="customer_name" placeholder="Nguyễn Văn A" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="input-group">
                        <label>Số điện thoại</label>
                        <input type="tel" name="phone" placeholder="090 123 4567" required>
                    </div>
                    <div class="input-group">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="email@vi-du.com">
                    </div>
                </div>

                <div class="input-group">
                    <label>Địa chỉ nhận hàng</label>
                    <textarea name="address" rows="3" placeholder="Số nhà, tên đường, Phường/Xã..." required></textarea>
                </div>

                <div class="input-group">
                    <label>Ghi chú (tùy chọn)</label>
                    <textarea name="note" rows="2" placeholder="Ví dụ: Giao giờ hành chính..."></textarea>
                </div>
            </div>

            <div class="summary-box" style="margin-top: 30px;">
                <h3 style="margin-bottom: 25px; font-weight: 900; color: var(--sage-dark);"><i class="fa-solid fa-credit-card"></i> Phương thức thanh toán</h3>
                
                <label class="payment-item">
                    <input type="radio" name="payment_method" value="COD" checked onclick="toggleBank(false)">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                    <div>
                        <strong style="display:block; color: var(--sage-dark);">Thanh toán khi nhận hàng (COD)</strong>
                        <small style="color: #6b7280;">Kiểm tra hàng trước khi trả tiền</small>
                    </div>
                </label>

                <label class="payment-item">
                    <input type="radio" name="payment_method" value="BANK" onclick="toggleBank(true)">
                    <i class="fa-solid fa-university"></i>
                    <div>
                        <strong style="display:block; color: var(--sage-dark);">Chuyển khoản / Ví điện tử</strong>
                        <small style="color: #6b7280;">VNpay</small>
                    </div>
                </label>
            </div>
        </div>

        <aside class="checkout-right">
            <div class="summary-box" style="position: sticky; top: 20px;">
                <h3 style="font-weight: 900; margin-bottom: 20px; font-size: 18px; color: var(--sage-dark);">Đơn hàng của bạn</h3>
                
                <div style="max-height: 300px; overflow-y: auto; margin-bottom: 20px;">
                    <?php foreach($_SESSION['cart'] as $item): ?>
                    <div class="mini-item">
                        <img src="../../img/<?php echo $item['image']; ?>" alt="">
                        <div style="flex:1;">
                            <div style="font-weight: 700; font-size: 13px; color: var(--sage-dark);"><?php echo $item['name']; ?></div>
                            <small>SL: <?php echo $item['quantity']; ?></small>
                        </div>
                        <span style="font-weight: 700; font-size: 14px;"><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>đ</span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="sum-line">
                    <span>Tạm tính</span>
                    <strong><?php echo number_format($total_temp, 0, ',', '.'); ?>đ</strong>
                </div>

                <?php if($discount > 0): ?>
                <div class="sum-line" style="color: #16a34a;">
                    <span>Giảm giá (<?php echo $coupon_code; ?>)</span>
                    <strong>-<?php echo number_format($discount, 0, ',', '.'); ?>đ</strong>
                </div>
                <?php endif; ?>

                <div class="sum-line">
                    <span>Phí vận chuyển</span>
                    <strong><?php echo ($shipping_fee == 0) ? "Miễn phí" : number_format($shipping_fee, 0, ',', '.')."đ"; ?></strong>
                </div>

                <div class="sum-line grand-total-line">
                    <span style="font-weight: 800; color: var(--sage-dark);">TỔNG THANH TOÁN</span>
                    <strong style="font-size: 22px; color: var(--sage);"><?php echo number_format($grand_total, 0, ',', '.'); ?>đ</strong>
                </div>

                <button type="submit" style="width: 100%; background: var(--sage-dark); color: white; border: none; padding: 18px; border-radius: 16px; font-weight: 800; font-size: 16px; cursor: pointer; margin-top: 25px; box-shadow: 0 10px 20px rgba(45, 62, 64, 0.2);">
                    XÁC NHẬN ĐẶT HÀNG
                </button>
                <p style="text-align: center; font-size: 11px; color: #9ca3af; margin-top: 15px;">Thông tin của bạn được bảo mật tuyệt đối.</p>
            </div>
        </aside>
    </form>
</main>

</body>
</html>