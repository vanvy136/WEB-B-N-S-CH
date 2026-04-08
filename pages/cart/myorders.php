<?php
session_start();
include "../includes/config.php";

// Giả sử user_id = 1 nếu chưa có hệ thống đăng nhập để test
// Khi có đăng nhập, bạn dùng: $user_id = $_SESSION['user_id'];
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

// Lấy danh sách đơn hàng của người dùng này
$sql_orders = "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY created_at DESC";
$res_orders = mysqli_query($conn, $sql_orders);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn hàng của tôi - Sagewood Books</title>
    <link rel="stylesheet" href="../../css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .orders-container { max-width: 1000px; margin: 50px auto; padding: 0 20px; }
        .order-card { 
            background: #fff; 
            border-radius: 20px; 
            padding: 25px; 
            margin-bottom: 25px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border: 1px solid var(--border);
        }
        .order-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            border-bottom: 1px solid #eee; 
            padding-bottom: 15px; 
            margin-bottom: 15px; 
        }
        .status-badge {
            padding: 6px 15px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
        }
        /* Màu sắc cho từng trạng thái */
        .status-pending { background: #fff7ed; color: #ea580c; } /* Chờ xử lý */
        .status-transfer { background: #f0f9ff; color: #0284c7; } /* Chờ chuyển khoản */
        .status-completed { background: #f0fdf4; color: #16a34a; } /* Đã giao xong */
        
        .product-item { display: flex; gap: 15px; margin-bottom: 10px; align-items: center; }
        .product-item img { width: 45px; height: 60px; object-fit: cover; border-radius: 5px; }
        
        .order-footer { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-top: 15px; 
            padding-top: 15px; 
            border-top: 1px dashed #eee; 
        }
        .empty-state { text-align: center; padding: 100px 0; }
        .empty-state i { font-size: 60px; color: var(--sage); margin-bottom: 20px; }
    </style>
</head>
<?php 
    include "../includes/header.php";
 ?>
<body style="background: var(--be);">

<div class="container">
    <div class="orders-container">
        <h2 style="color: var(--sage-dark); font-weight: 900; margin-bottom: 30px;">
            <i class="fa-solid fa-box-open"></i> Đơn hàng của bạn
        </h2>

        <?php if (mysqli_num_rows($res_orders) > 0): ?>
            <?php while($order = mysqli_fetch_assoc($res_orders)): ?>
                <?php 
                    $order_id = $order['order_id'];
                    // Lấy chi tiết sản phẩm cho mỗi đơn hàng
                    $sql_items = "SELECT od.*, p.name, p.image 
                                  FROM order_details od 
                                  JOIN products p ON od.product_id = p.product_id 
                                  WHERE od.order_id = '$order_id'";
                    $res_items = mysqli_query($conn, $sql_items);
                    
                    // Xác định class cho badge trạng thái
                    $status_class = '';
                    if($order['status'] == 'Chờ xử lý') $status_class = 'status-pending';
                    elseif($order['status'] == 'Chờ chuyển khoản') $status_class = 'status-transfer';
                    else $status_class = 'status-completed';
                ?>

                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <span style="font-weight: 800; color: var(--sage-dark);">Mã đơn: #SW-<?php echo $order_id; ?></span>
                            <br>
                            <small style="color: #9CA3AF;"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></small>
                        </div>
                        <span class="status-badge <?php echo $status_class; ?>">
                            <?php echo $order['status']; ?>
                        </span>
                    </div>

                    <div class="order-body">
                        <?php while($item = mysqli_fetch_assoc($res_items)): ?>
                            <div class="product-item">
                                <img src="../../img/<?php echo $item['image']; ?>" alt="">
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; font-size: 14px;"><?php echo $item['name']; ?></div>
                                    <small>x<?php echo $item['quantity']; ?></small>
                                </div>
                                <span style="font-weight: 700;"><?php echo number_format($item['price'] * $item['quantity']); ?>đ</span>
                            </div>
                        <?php endwhile; ?>
                    </div>

                    <div class="order-footer">
                        <div style="color: #6B7280; font-size: 13px;">
                            <i class="fa-solid fa-circle-info"></i> <?php echo substr($order['note'], 0, 50) . '...'; ?>
                        </div>
                        <div>
                            <span style="margin-right: 10px;">Thành tiền:</span>
                            <strong style="color: var(--sage); font-size: 20px;"><?php echo number_format($order['total_price']); ?>đ</strong>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fa-solid fa-ghost"></i>
                <h3>Bạn chưa có đơn hàng nào.</h3>
                <p>Hãy bắt đầu khám phá những cuốn sách tuyệt vời của Sagewood nhé!</p>
                <a href="../products/products.php" class="btn-checkout" style="display: inline-block; margin-top: 20px; text-decoration: none;">Mua sắm ngay</a>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php 
    include "../includes/footer.php";
 ?>
</body>
</html>