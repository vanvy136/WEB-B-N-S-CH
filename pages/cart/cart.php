<?php
session_start();
include "../includes/config.php";

$total_temp = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total_temp += $item['price'] * $item['quantity'];
    }
}

// Logic giảm giá
$discount = 0;
$coupon_code = "";
if (isset($_SESSION['coupon'])) {
    $coupon_code = $_SESSION['coupon']['code'];
    $discount = ($_SESSION['coupon']['type'] == 'percent') 
                ? ($total_temp * $_SESSION['coupon']['value'] / 100) 
                : $_SESSION['coupon']['value'];
}

$shipping_fee = ($total_temp > 500000 || $total_temp == 0) ? 0 : 20000;
$grand_total = $total_temp - $discount + $shipping_fee;
if ($grand_total < 0) $grand_total = 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Giỏ hàng - Sagewood Books</title>
    <link rel="stylesheet" href="../../css/index.css">
    <link rel="stylesheet" href="../../css/cart.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <?php include "../includes/header.php"; ?>

    <main class="cart-page">
        <div class="container">
            <div class="section-title">
                <h2>Giỏ hàng của bạn</h2>
                <a href="../products/products.php"><i class="fa-solid fa-arrow-left"></i> Tiếp tục mua sắm</a>
            </div>

            <?php if (!empty($_SESSION['cart'])): ?>
            <div class="hero-grid" style="align-items: start; grid-template-columns: 1.3fr 0.7fr;">
                
                <div class="cart-list">
                    <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                    <div class="card" style="display: flex; flex-direction: row; margin-bottom: 16px; padding: 15px; align-items: center; gap: 20px;">
                        <div class="thumb" style="width: 100px; height: 130px; flex-shrink: 0; border-radius: 12px;">
                            <img src="../../img/<?php echo $item['image']; ?>" alt="book" style="width: 80px;">
                        </div>
                        
                        <div class="content" style="flex-grow: 1; padding: 0;">
                            <div class="title"><?php echo $item['name']; ?></div>
                            <div class="price" style="margin-top: 5px;"><?php echo number_format($item['price'], 0, ',', '.'); ?>đ</div>
                            
                            <div class="row" style="justify-content: flex-start; gap: 20px; margin-top: 15px;">
                                <div class="qty-control" style="display: flex; align-items: center; background: var(--be); border-radius: 999px; padding: 4px 12px; border: 1px solid var(--border);">
                                    <a href="update_cart.php?id=<?php echo $id; ?>&action=minus" style="color: var(--sage-dark); font-weight: bold;">-</a>
                                    <span style="margin: 0 15px; font-weight: 800;"><?php echo $item['quantity']; ?></span>
                                    <a href="update_cart.php?id=<?php echo $id; ?>&action=plus" style="color: var(--sage-dark); font-weight: bold;">+</a>
                                </div>
                                <a href="update_cart.php?id=<?php echo $id; ?>&action=delete" style="color: #ef4444; font-size: 14px; font-weight: 600;">
                                    <i class="fa-solid fa-trash-can"></i> Xóa
                                </a>
                            </div>
                        </div>

                        <div class="subtotal" style="font-weight: 900; color: var(--sage-dark); font-size: 18px; min-width: 100px; text-align: right;">
                            <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>đ
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

            <aside class="cart-sidebar">
                <div class="stat" style="background: white; border: 1px solid var(--border); padding: 25px; min-width: 100%; border-radius: 24px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);">
                    <h3 style="color: var(--sage-dark); margin-bottom: 22px; font-weight: 900; font-size: 20px; letter-spacing: -0.5px;">Tóm tắt đơn hàng</h3>
                    
                    <form action="apply_coupon.php" method="POST" style="display: flex; gap: 8px; margin-bottom: 20px;">
                        <input type="text" name="coupon_code" placeholder="Mã giảm giá..." value="<?php echo $coupon_code; ?>" 
                            style="flex-grow: 1; padding: 12px 15px; border-radius: 12px; border: 1px solid var(--border); outline: none; font-size: 14px; color: var(--sage-dark);">
                        <button type="submit" class="btn btn-primary" style="padding: 10px 18px; font-size: 12px; font-weight: 700; background: var(--sage); color: white; border-radius: 12px; border: none; cursor: pointer;">ÁP DỤNG</button>
                    </form>

                    <div style="display: flex; justify-content: space-between; margin-bottom: 14px;">
                        <span style="color: #6B7280; font-weight: 500;">Tạm tính:</span>
                        <strong style="color: var(--sage-dark); font-weight: 700;"><?php echo number_format($total_temp, 0, ',', '.'); ?>đ</strong>
                    </div>
                    
                    <?php if($discount > 0): ?>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 14px; 
                                background-color: #f0fdf4; padding: 10px; border-radius: 10px; border: 1px dashed #16a34a;">
                        <span style="color: #16a34a; font-weight: 600;">
                            <i class="fa-solid fa-tags"></i> Giảm giá (<?php echo $coupon_code; ?>):
                        </span>
                        <strong style="color: #16a34a; font-weight: 800;">
                            -<?php echo number_format($discount, 0, ',', '.'); ?>đ
                        </strong>
                    </div>
                    <?php endif; ?>

                    <div style="display: flex; justify-content: space-between; margin-bottom: 14px;">
                        <span style="color: #6B7280; font-weight: 500;">Phí vận chuyển:</span>
                        <strong style="color: var(--sage-dark); font-weight: 700;">
                            <?php echo ($shipping_fee == 0) ? "Miễn phí" : number_format($shipping_fee, 0, ',', '.')."đ"; ?>
                        </strong>
                    </div>

                    <hr style="border: none; border-top: 1px solid var(--border); margin: 18px 0;">

                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px;">
                        <span style="font-size: 16px; font-weight: 800; color: var(--sage-dark); text-transform: uppercase;">Tổng cộng:</span>
                        <span style="font-size: 26px; font-weight: 900; color: var(--sage-dark); letter-spacing: -1px;">
                            <?php echo number_format($grand_total, 0, ',', '.'); ?>đ
                        </span>
                    </div>

                    <a href="checkout.php" class="btn btn-primary" style="display: block; text-align: center; width: 100%; background: var(--sage-dark); color: white; padding: 16px; border-radius: 14px; font-weight: 700; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                        THANH TOÁN NGAY
                    </a>
                </div>
            </aside>

            </div>
            <?php else: ?>
                <div style="text-align: center; padding: 80px 0;">
                    <i class="fa-solid fa-box-open" style="font-size: 60px; color: var(--sage); margin-bottom: 20px; opacity: 0.5;"></i>
                    <h3 style="color: var(--sage-dark);">Giỏ hàng đang trống</h3>
                    <p style="margin-bottom: 25px; color: #6B7280;">Hãy lấp đầy kệ sách của bạn với những câu chuyện tuyệt vời!</p>
                    <a href="../products/products.php" class="btn btn-primary" style="background: var(--sage); color: white;">QUAY LẠI CỬA HÀNG</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <?php include "../includes/footer.php"; ?>
</body>
</html>