<?php
session_start();
include "../includes/config.php";

if(isset($_POST['submit'])){
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $status = (int)$_POST['status'];

    // XỬ LÝ ẢNH
    $newImage = "default.jpg"; 
    if(!empty($_FILES['image']['name'])){
        $image = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];
        
        // Tạo tên file duy nhất để không bị trùng
        $newImage = time() . "_" . preg_replace("/\s+/", "_", $image);
        
        // ĐƯỜNG DẪN: Đảm bảo đường dẫn này đúng với cấu trúc thư mục của bạn
        $uploadPath = "../admin/uploads/"; 
        
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        if(move_uploaded_file($tmp, $uploadPath . $newImage)){
            // Upload thành công
        } else {
            // Nếu lỗi upload thì dùng ảnh mặc định
            $newImage = "default.jpg";
        }
    }

    $stmt = $conn->prepare("INSERT INTO posts (title, image, content, description, category, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssssi", $title, $newImage, $content, $description, $category, $status);

    if($stmt->execute()){
        echo "<script>alert('Đăng bài thành công!'); window.location.href='managePost.php';</script>";
        exit();
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm bài viết - Sagewood Admin</title>
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

        /* MỞ RỘNG VÙNG NỘI DUNG */
        .content-area { 
            flex-grow: 1; 
            padding: 40px 60px; /* Tăng padding hai bên để thoáng hơn */
            width: 100%;
        }

        /* FORM FULL WIDTH */
        .panel-form {
            background: #fff; 
            padding: 40px; 
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
            width: 100%; /* Chiếm hết chiều ngang vùng content */
            box-sizing: border-box;
        }

        .form-grid { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 30px; /* Tăng khoảng cách giữa các ô input */
        }
        .full-width { grid-column: span 2; }

        .form-group label {
            display: block; font-weight: 600; margin-bottom: 12px; color: #333; font-size: 15px;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%; padding: 14px; border: 1px solid #e0e0e0;
            border-radius: 10px; font-size: 15px; outline: none; transition: 0.3s;
            background-color: #fafafa;
        }
        .form-group input:focus, .form-group textarea:focus { 
            border-color: #7A8F7A; background-color: #fff; box-shadow: 0 0 0 4px rgba(122, 143, 122, 0.1);
        }

        .btn-submit {
            background: #7A8F7A; color: white; border: none;
            padding: 16px 40px; border-radius: 12px; font-weight: bold; font-size: 16px;
            cursor: pointer; transition: 0.3s; display: inline-flex; align-items: center; gap: 10px;
        }
        .btn-submit:hover { background: #5F745F; transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        
        textarea { resize: vertical; min-height: 100px; }
    </style>
</head>
<body>
    <?php include "../includes/header.php"; ?>
    <div class="admin-layout">
        <aside class="sidebar">
            <nav>
                <a href="dashboard.php"><i class="fa-solid fa-chart-line"></i> Tổng quan</a>
                <a href="postProduct.php"><i class="fa-solid fa-book-open"></i> Quản lý sách</a>
                <a href="managePost.php" class="active"><i class="fa-solid fa-pen-to-square"></i> Quản lý bài viết</a>
                <a href="order.php"><i class="fa-solid fa-box"></i> Quản lý đơn hàng</a>
                <a href="statistics.php"><i class="fa-solid fa-chart-pie"></i> Thống kê</a>
            </nav>
        </aside>

        <main class="content-area">
            <div style="margin-bottom: 40px;">
                <h1 style="margin: 0; font-size: 32px; color: #2c3e50;">Viết bài mới</h1>
                <p style="color: #7f8c8d; margin-top: 8px; font-size: 16px;">Chia sẻ tin tức hoặc review sách mới đến cộng đồng Sagewood.</p>
            </div>

            <div class="panel-form">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label>Tiêu đề bài viết</label>
                            <input type="text" name="title" required placeholder="Nhập tiêu đề hấp dẫn cho bài viết của bạn...">
                        </div>

                        <div class="form-group">
                            <label>Danh mục bài viết</label>
                            <select name="category">
                                <option value="Tin tức">Tin tức</option>
                                <option value="Review sách">Review sách</option>
                                <option value="Sách hay">Sách hay nên đọc</option>
                                <option value="Kinh nghiệm">Kinh nghiệm đọc sách</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Trạng thái xuất bản</label>
                            <select name="status">
                                <option value="1">Công khai (Hiển thị ngay trên Blog)</option>
                                <option value="0">Bản nháp (Chỉ Admin thấy)</option>
                            </select>
                        </div>

                        <div class="form-group full-width">
                            <label>Hình ảnh bìa (Thumbnail - Kích thước lớn)</label>
                            <div style="border: 2px dashed #ddd; padding: 20px; border-radius: 10px; background: #fff;">
                                <input type="file" name="image" accept="image/*" style="border: none; background: transparent;">
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label>Mô tả ngắn (Sapo)</label>
                            <textarea name="description" rows="3" placeholder="Viết một đoạn ngắn dẫn dắt người đọc (khoảng 2-3 câu)..."></textarea>
                        </div>

                        <div class="form-group full-width">
                            <label>Nội dung bài viết chi tiết</label>
                            <textarea name="content" rows="15" required placeholder="Nội dung chính của bài viết..."></textarea>
                        </div>
                    </div>

                    <div style="text-align: right; margin-top: 30px; border-top: 1px solid #eee; padding-top: 30px;">
                        <button type="submit" name="submit" class="btn-submit">
                            <i class="fa-solid fa-paper-plane"></i> Xuất bản bài viết ngay
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

</body>
</html>