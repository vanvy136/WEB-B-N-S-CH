<?php
include "../includes/config.php";
if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $author = mysqli_real_escape_string($conn, trim($_POST['author']));
    
    // Lấy category_id (kiểu số) từ form
    $category_id = (int)$_POST['category_id']; 
    
    $price = (int)$_POST['price'];
    $stock = (int)$_POST['stock']; // Đảm bảo bảng có cột stock, nếu không hãy bỏ qua
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Xử lý ảnh
    $image = $_FILES['image']['name'];
    $newImage = "default_book.jpg";
    if ($image != "") {
        $newImage = time() . "_" . $image;
        move_uploaded_file($_FILES['image']['tmp_name'], "../../img/" . $newImage);
    }

    // SỬA TÊN CỘT Ở ĐÂY: thay 'category' thành 'category_id'
    // Lưu ý: Kiểm tra lại bảng của bạn có cột 'stock' không, trong ảnh mình không thấy cột stock.
    // Nếu không có stock, hãy xóa nó khỏi câu lệnh dưới đây.
    $stmt = $conn->prepare("INSERT INTO products (name, author, category_id, price, image, description, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    
    // "ssiiss" -> string, string, integer (ID), integer (Price), string (Image), string (Desc)
    $stmt->bind_param("ssiiss", $name, $author, $category_id, $price, $newImage, $description);

    if ($stmt->execute()) {
        echo "<script>alert('Thêm sách thành công!'); window.location.href='postProduct.php';</script>";
        exit();
    } else {
        echo "Lỗi SQL: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm sách mới - Sagewood Admin</title>
    <link rel="stylesheet" href="../../css/postProduct.css">
        <link rel="stylesheet" href="../../css/index.css">
    <link rel="stylesheet" href="../../css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* CSS đồng bộ giao diện sát lề */
        body { margin: 0; background: #f9f6f2; }
        .admin-wrapper { display: flex; min-height: 100vh; }
        
        .sidebar { 
            width: 260px; background: #fff; border-right: 1px solid #eee; flex-shrink: 0;
            position: sticky; top: 0; height: 100vh;
        }
        .sidebar a { 
            display: flex; align-items: center; padding: 15px 25px; 
            color: #555; text-decoration: none; gap: 10px; 
        }
        .sidebar a.active { background: #7A8F7A; color: #fff; border-radius: 0 25px 25px 0; margin-right: 15px; }

        .content-main { flex-grow: 1; padding: 40px; }
        
        .form-container {
            background: #fff; padding: 30px; border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05); max-width: 900px;
        }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .full-width { grid-column: span 2; }
        
        .form-group label { display: block; font-weight: 600; margin-bottom: 8px; color: #444; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;
        }
        
        .btn-submit {
            background: #7A8F7A; color: white; border: none; padding: 12px 30px;
            border-radius: 8px; font-weight: bold; cursor: pointer; transition: 0.3s;
        }
        .btn-submit:hover { background: #5F745F; transform: translateY(-2px); }
    </style>
</head>
<body>

    <?php include "../includes/header.php"; ?>

    <div class="admin-wrapper">
        <aside class="sidebar">
            <div style="padding-top: 20px;">
                <a href="dashboard.php"><i class="fa-solid fa-chart-line"></i> Tổng quan</a>
                <a class="active" href="postProduct.php"><i class="fa-solid fa-book-open"></i> Quản lý sách</a>
                <a href="managePost.php"><i class="fa-solid fa-pen-to-square"></i> Quản lý bài viết</a>
                <a href="order.php"><i class="fa-solid fa-box"></i> Quản lý đơn hàng</a>
                <a href="statistics.php"><i class="fa-solid fa-chart-pie"></i> Thống kê</a>
            </div>
        </aside>

        <section class="content-main">
            <div style="margin-bottom: 25px;">
                <a href="postProduct.php" style="text-decoration: none; color: #7A8F7A;">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
                </a>
                <h1 style="margin-top: 15px;">Thêm sách mới</h1>
            </div>

            <div class="form-container">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label>Tên sách</label>
                            <input type="text" name="name" placeholder="Nhập tên sách..." required>
                        </div>

                        <div class="form-group">
                            <label>Tác giả</label>
                            <input type="text" name="author" placeholder="Tên tác giả..." required>
                        </div>

                        <div class="form-group">
                            <label>Danh mục</label>
                            <select name="category_id">
                                <option value="1">Kĩ năng</option>
                                <option value="2">Kinh tế</option>
                                <option value="3">Tiểu thuyết</option>
                                <option value="4">Học tập</option>
                                <option value="5">Thiếu nhi</option>
                                <option value="6">Truyện tranh</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Giá bán (VNĐ)</label>
                            <input type="number" name="price" required>
                        </div>

                        <div class="form-group">
                            <label>Số lượng tồn kho</label>
                            <input type="number" name="stock" required>
                        </div>

                        <div class="form-group full-width">
                            <label>Ảnh bìa sách</label>
                            <input type="file" name="image" accept="image/*" required>
                        </div>

                        <div class="form-group full-width">
                            <label>Mô tả sách</label>
                            <textarea name="description" rows="5" placeholder="Viết mô tả tóm tắt về nội dung sách..."></textarea>
                        </div>
                    </div>

                    <div style="text-align: right; margin-top: 20px;">
                        <button type="submit" name="submit" class="btn-submit">
                            <i class="fa-solid fa-check"></i> Lưu sản phẩm
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>

</body>
</html>