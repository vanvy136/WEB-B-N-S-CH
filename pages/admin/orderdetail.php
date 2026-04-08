<?php
session_start();
include "../includes/config.php";

if(isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $order_res = mysqli_query($conn, "SELECT * FROM orders WHERE order_id = $id");
    $order = mysqli_fetch_assoc($order_res);

    if (!$order) { exit("Đơn hàng không tồn tại!"); }

    $details = mysqli_query($conn, "SELECT od.*, p.name, p.image FROM order_details od 
                                    JOIN products p ON od.product_id = p.product_id WHERE od.order_id = $id");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết #SW<?php echo $id; ?> - Sagewood Admin</title>
    <link rel="stylesheet" href="../../css/postProduct.css">
    <link rel="stylesheet" href="../../css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { margin: 0; padding: 0; background-color: #f9f6f2; font-family: 'Segoe UI', sans-serif; }
        .admin-layout { display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: #fff; border-right: 1px solid #e0e0e0; flex-shrink: 0; }
        .content-area { flex-grow: 1; padding: 40px 60px; }

        .grid-info { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-bottom: 30px; }
        .card { background: #fff; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.04); }
        .card-title { font-size: 18px; font-weight: 700; color: #7A8F7A; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        
        .info-item { margin-bottom: 15px; display: flex; gap: 10px; font-size: 15px; }
        .info-item b { width: 130px; color: #555; }

        .product-table { width: 100%; border-collapse: collapse; }
        .product-table th { padding: 15px; background: #fafafa; text-align: left; color: #888; }
        .product-table td { padding: 15px; border-bottom: 1px solid #eee; }
        .p-img { width: 50px; height: 70px; object-fit: cover; border-radius: 5px; }

        .status-select { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 15px; outline: none; }
        .btn-update { width: 100%; background: #7A8F7A; color: #fff; border: none; padding: 12px; border-radius: 8px; font-weight: 700; cursor: pointer; transition: 0.3s; }
        .btn-update:hover { background: #5F745F; }
    </style>
</head>
<body>
    <?php include "../includes/header.php"; ?>
    <div class="admin-layout">
        <aside class="sidebar">
            <div style="padding: 20px 25px;"><h2>Sagewood</h2></div>
            <nav>
                <a href="order.php" style="display: flex; align-items: center; padding: 15px 25px; color: #7A8F7A; text-decoration: none; font-weight: bold;">
                    <i class="fa-solid fa-arrow-left"></i> &nbsp; Quay lại
                </a>
            </nav>
        </aside>

        <main class="content-area">
            <h1 style="margin-bottom: 30px;">Chi tiết hóa đơn #SW<?php echo $id; ?></h1>

            <div class="grid-info">
                <div class="left-col">
                    <div class="card" style="margin-bottom: 30px;">
                        <div class="card-title"><i class="fa-solid fa-address-card"></i> Thông tin giao hàng</div>
                        <div class="info-item"><b>Người nhận:</b> <span><?php echo $order['customer_name']; ?></span></div>
                        <div class="info-item"><b>Điện thoại:</b> <span><?php echo $order['phone']; ?></span></div>
                        <div class="info-item"><b>Địa chỉ:</b> <span><?php echo $order['address']; ?></span></div>
                        <div class="info-item"><b>Ghi chú:</b> <span style="font-style: italic; color: #888;"><?php echo $order['note'] ?: "Không có"; ?></span></div>
                    </div>

                    <div class="card">
                        <div class="card-title"><i class="fa-solid fa-box-open"></i> Danh sách sản phẩm</div>
                        <table class="product-table">
                            <thead>
                                <tr>
                                    <th>Sách</th>
                                    <th>Số lượng</th>
                                    <th>Đơn giá</th>
                                    <th style="text-align: right;">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($item = mysqli_fetch_assoc($details)): ?>
                                <tr>
                                    <td style="display: flex; align-items: center; gap: 15px;">
                                        <img src="../../img/<?php echo $item['image']; ?>" class="p-img">
                                        <b><?php echo $item['name']; ?></b>
                                    </td>
                                    <td>x<?php echo $item['quantity']; ?></td>
                                    <td><?php echo number_format($item['price']); ?>đ</td>
                                    <td style="text-align: right; font-weight: 700;"><?php echo number_format($item['price'] * $item['quantity']); ?>đ</td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        <div style="text-align: right; margin-top: 20px; font-size: 20px;">
                            Tổng thanh toán: <b style="color: #7A8F7A;"><?php echo number_format($order['total_price']); ?>đ</b>
                        </div>
                    </div>
                </div>

                <div class="right-col">
                    <div class="card">
                        <div class="card-title"><i class="fa-solid fa-arrows-rotate"></i> Trạng thái</div>
                        <form action="update_status.php" method="POST">
                            <input type="hidden" name="order_id" value="<?php echo $id; ?>">
                            <select name="new_status" class="status-select">
                                <option value="Chờ xử lý" <?php if($order['status']=='Chờ xử lý') echo 'selected';?>>🟡 Chờ xử lý</option>
                                <option value="Đang giao" <?php if($order['status']=='Đang giao') echo 'selected';?>>🔵 Đang giao</option>
                                <option value="Thành công" <?php if($order['status']=='Thành công') echo 'selected';?>>🟢 Thành công</option>
                                <option value="Đã hủy" <?php if($order['status']=='Đã hủy') echo 'selected';?>>🔴 Đã hủy</option>
                            </select>
                            <button type="submit" class="btn-update">CẬP NHẬT ĐƠN HÀNG</button>
                        </form>
                        <button onclick="window.print()" style="width: 100%; margin-top: 10px; padding: 10px; background: none; border: 1px solid #ddd; border-radius: 8px; cursor: pointer;">
                            <i class="fa-solid fa-print"></i> In hóa đơn
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>