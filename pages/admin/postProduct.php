<?php
include "../includes/config.php"; 

if (!$conn) die("Kết nối DB thất bại.");

// Truy vấn lấy danh sách sản phẩm
$result = mysqli_query($conn, "SELECT * FROM products ORDER BY product_id DESC");
$total_books = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sách - Sagewood Admin</title>
    
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

        /* Main Content */
        .content-area { flex-grow: 1; padding: 40px; }
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px; }

        /* Table Design */
        .table-container { background: #fff; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .book-table { width: 100%; border-collapse: collapse; }
        .book-table th { text-align: left; padding: 15px; border-bottom: 2px solid var(--bg); color: #888; font-size: 13px; text-transform: uppercase; }
        .book-table td { padding: 15px; border-bottom: 1px solid #eee; vertical-align: middle; }
        
        .book-img { width: 45px; height: 60px; object-fit: cover; border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .badge-id { background: #f0f0f0; color: #777; padding: 4px 8px; border-radius: 6px; font-size: 12px; }
        .badge-cat { background: #e8ede8; color: var(--sage); padding: 4px 10px; border-radius: 10px; font-size: 12px; font-weight: bold; }
        
        /* Button Add */
        .btn-add { 
            display: inline-flex; align-items: center; justify-content: center; gap: 10px;
            background: var(--sage); color: #fff !important; text-decoration: none;
            padding: 12px 30px; min-width: 180px; border-radius: 10px; 
            font-weight: 600; white-space: nowrap; transition: 0.3s;
        }
        .btn-add:hover { background: var(--sage-dark); transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }

        .action-btns a { margin: 0 8px; font-size: 16px; transition: 0.2s; }
        .edit { color: #3498db; } .delete { color: #e74c3c; }
    </style>
</head>
<body>
    <?php include "../includes/header.php"; ?>
    <div class="admin-layout">
        <aside class="sidebar">
            <nav>
                <a href="dashboard.php"><i class="fa-solid fa-chart-line"></i> Tổng quan</a>
                <a href="postProduct.php" class="active"><i class="fa-solid fa-book-open"></i> Quản lý sách</a>
                <a href="managePost.php"><i class="fa-solid fa-pen-to-square"></i> Quản lý bài viết</a>
                <a href="order.php"><i class="fa-solid fa-box"></i> Quản lý đơn hàng</a>
                <a href="statistics.php"><i class="fa-solid fa-chart-pie"></i> Thống kê</a>
            </nav>
        </aside>

        <main class="content-area">
            <div class="header-flex">
                <div>
                    <h1 style="margin: 0; font-size: 28px; color: #333;">Quản lý kho sách</h1>
                    <p style="color: #888; margin-top: 5px;">Hệ thống đang quản lý <strong><?= $total_books ?></strong> đầu sách thực tế.</p>
                </div>
                <a href="add_book.php" class="btn-add"><i class="fa-solid fa-plus"></i> Thêm sách mới</a>
            </div>

            <div class="table-container">
                <table class="book-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Tác giả</th>
                            <th>Danh mục</th>
                            <th>Giá bán</th>
                            <th style="text-align: center;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total_books > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><span class="badge-id">#<?= $row['product_id'] ?></span></td>
                                <td><img src="../../img/<?= $row['image'] ?>" class="book-img" onerror="this.src='../../img/default_book.jpg'"></td>
                                <td><strong style="color: #333;"><?= $row['name'] ?></strong></td>
                                <td style="color: #666;"><?= $row['author'] ?></td>
                                <td><span class="badge-cat">ID: <?= $row['category_id'] ?></span></td>
                                <td><b><?= number_format($row['price'], 0, ',', '.') ?>đ</b></td>
                                <td class="action-btns" style="text-align: center;">
                                    <a href="delete_book.php?id=<?= $row['product_id'] ?>" class="delete" onclick="return confirm('Xóa cuốn sách này?')"><i class="fa-solid fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7" style="text-align: center; padding: 50px; color: #999;">Chưa có dữ liệu sách.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

</body>
</html>