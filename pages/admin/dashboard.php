<?php
session_start();
include "../includes/config.php"; // Đã sửa đường dẫn include cho đúng cấp thư mục

// 1. Lấy tổng số lượng sách
$sql_books = "SELECT COUNT(*) as total FROM products";
$res_books = mysqli_query($conn, $sql_books);
$row_books = mysqli_fetch_assoc($res_books);

// 2. Lấy số đơn hàng hôm nay
$sql_orders = "SELECT COUNT(*) as total FROM orders WHERE DATE(created_at) = CURDATE()";
$res_orders = mysqli_query($conn, $sql_orders);
$row_orders = mysqli_fetch_assoc($res_orders);

// 3. Tính tổng doanh thu
$sql_revenue = "SELECT SUM(total_price) as total FROM orders WHERE status IN ('Thành công', 'Đã hoàn thành')";
$res_revenue = mysqli_query($conn, $sql_revenue);
$row_revenue = mysqli_fetch_assoc($res_revenue);

// 4. Lấy 5 cuốn sách mới nhất
$sql_latest_books = "SELECT * FROM products ORDER BY product_id DESC LIMIT 5";
$latest_books = mysqli_query($conn, $sql_latest_books);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng điều khiển - Sagewood Admin</title>
    
    <link rel="stylesheet" href="../../css/postProduct.css">
    <link rel="stylesheet" href="../../css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body { margin: 0; padding: 0; background-color: #f9f6f2; }
        .admin-layout { display: flex; min-height: 100vh; }
        
        .sidebar { 
            width: 260px; background: #fff; border-right: 1px solid #e0e0e0; 
            flex-shrink: 0; position: sticky; top: 0; height: 100vh;
        }
        .sidebar a { 
            display: flex; align-items: center; padding: 15px 25px; 
            color: #555; text-decoration: none; gap: 10px; 
        }
        .sidebar a.active { background: #7A8F7A; color: #fff; }

        .content-area { flex-grow: 1; padding: 30px; }

        /* Style cho Card thống kê giống postProduct stats */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card-new {
            background: #fff; padding: 20px; border-radius: 15px;
            display: flex; align-items: center; gap: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .stat-card-new .icon-box {
            width: 50px; height: 50px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center; font-size: 20px;
        }

        .table-container {
            background: #fff; padding: 25px; border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .book-img {
            width: 40px; height: 55px; object-fit: cover; border-radius: 4px;
        }
        .badge-id {
            background: #f0f0f0; color: #666; padding: 3px 8px; 
            border-radius: 5px; font-size: 12px;
        }
    </style>
</head>
<body>

    <?php include "../includes/header.php"; ?>

    <div class="admin-layout">
        <aside class="sidebar">
            <nav>
                <a href="dashboard.php" class="active"><i class="fa-solid fa-chart-line"></i> Tổng quan</a>
                <a href="postProduct.php"><i class="fa-solid fa-book-open"></i> Quản lý sách</a>
                <a href="managePost.php"><i class="fa-solid fa-pen-to-square"></i> Quản lý bài viết</a>
                <a href="order.php"><i class="fa-solid fa-box"></i> Quản lý đơn hàng</a>
                <a href="statistics.php"><i class="fa-solid fa-chart-pie"></i> Thống kê</a>
            </nav>
        </aside>

        <main class="content-area">
            <div style="margin-bottom: 30px;">
                <h1>Dashboard</h1>
                <p style="color: #888;">Chào mừng Admin quay trở lại. Đây là tình hình kinh doanh của Sagewood Books.</p>
            </div>

            <div class="stat-grid">
                <div class="stat-card-new">
                    <div class="icon-box" style="background: #e3f2fd; color: #1565c0;"><i class="fa-solid fa-book"></i></div>
                    <div>
                        <p style="margin:0; color: #777; font-size: 14px;">Tổng số đầu sách</p>
                        <h3 style="margin:5px 0 0; font-size: 24px;"><?php echo number_format($row_books['total']); ?></h3>
                    </div>
                </div>

                <div class="stat-card-new">
                    <div class="icon-box" style="background: #fff3e0; color: #ef6c00;"><i class="fa-solid fa-cart-shopping"></i></div>
                    <div>
                        <p style="margin:0; color: #777; font-size: 14px;">Đơn hàng hôm nay</p>
                        <h3 style="margin:5px 0 0; font-size: 24px;"><?php echo number_format($row_orders['total']); ?></h3>
                    </div>
                </div>

                <div class="stat-card-new">
                    <div class="icon-box" style="background: #e8f5e9; color: #2e7d32;"><i class="fa-solid fa-money-bill-wave"></i></div>
                    <div>
                        <p style="margin:0; color: #777; font-size: 14px;">Doanh thu thực tế</p>
                        <h3 style="margin:5px 0 0; font-size: 24px;"><?php echo number_format($row_revenue['total'] ?? 0); ?>đ</h3>
                    </div>
                </div>
            </div>

            <div class="table-container">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="font-size: 18px; margin: 0;">Sách vừa cập nhật</h2>
                    <a href="postProduct.php" style="color: #7A8F7A; text-decoration: none; font-weight: 600; font-size: 14px;">Xem tất cả →</a>
                </div>

                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 2px solid #f9f6f2;">
                            <th style="padding: 12px 0;">Hình ảnh</th>
                            <th>Tên sách</th>
                            <th>Giá bán</th>
                            <th>Mã sách</th>
                            <th style="text-align: center;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($book = mysqli_fetch_assoc($latest_books)): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 15px 0;">
                                <img src="../../img/<?php echo $book['image']; ?>" class="book-img" onerror="this.src='../../img/default_book.jpg'">
                            </td>
                            <td><strong><?php echo $book['name']; ?></strong></td>
                            <td><b style="color: #2c3e50;"><?php echo number_format($book['price']); ?>đ</b></td>
                            <td><span class="badge-id">#<?php echo $book['product_id']; ?></span></td>
                            <td style="text-align: center;">
                                <a href="editProduct.php?id=<?php echo $book['product_id']; ?>" style="color: #7A8F7A;"><i class="fa-solid fa-pen"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

</body>
</html>