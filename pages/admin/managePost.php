<?php
session_start();
include "../includes/config.php";

// 1. XỬ LÝ XÓA BÀI VIẾT (Đã cập nhật dùng post_id theo Database của bạn)
if (isset($_GET['delete_id'])) {
    $del_id = (int)$_GET['delete_id'];
    
    // Truy vấn lấy tên file ảnh dựa trên post_id
    $res = mysqli_query($conn, "SELECT image FROM posts WHERE post_id = $del_id");
    $post = mysqli_fetch_assoc($res);
    
    if ($post) {
        // Xóa file ảnh vật lý nếu không phải ảnh mặc định
        if ($post['image'] != "default.jpg" && file_exists("../../img/uploads/" . $post['image'])) {
            unlink("../../img/uploads/" . $post['image']);
        }
        
        // Xóa bản ghi trong Database
        mysqli_query($conn, "DELETE FROM posts WHERE post_id = $del_id");
        header("Location: managePost.php?msg=deleted");
        exit();
    }
}

// 2. LẤY DANH SÁCH BÀI VIẾT (Sắp xếp mới nhất lên đầu)
$sql = "SELECT * FROM posts ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý bài viết - Sagewood Admin</title>
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

        /* Sidebar cố định */
        .sidebar { 
            width: 260px; background: #fff; border-right: 1px solid #e0e0e0; 
            flex-shrink: 0; position: sticky; top: 0; height: 100vh; 
        }
        .sidebar .logo { padding: 30px 25px; font-size: 22px; font-weight: bold; color: var(--sage); border-bottom: 1px solid #f5f5f5; }
        .sidebar nav { margin-top: 10px; }
        .sidebar a { display: flex; align-items: center; padding: 15px 25px; color: #555; text-decoration: none; gap: 12px; transition: 0.2s; }
        .sidebar a:hover { background: #f8f9f8; color: var(--sage); }
        .sidebar a.active { background: var(--sage); color: #fff; }

        /* Vùng nội dung tràn màn hình */
        .content-area { 
            flex-grow: 1; 
            padding: 40px; 
            width: calc(100% - 260px); /* Tự động tính chiều rộng còn lại */
            box-sizing: border-box; 
        }
        
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px; }
        
        .panel-table { 
            background: #fff; padding: 0; border-radius: 15px; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.04); overflow: hidden;
            width: 100%;
        }
        
        table { width: 100%; border-collapse: collapse; }
        th { background: #fcfcfc; text-align: left; padding: 18px 20px; border-bottom: 2px solid #f0f0f0; color: #888; font-size: 13px; text-transform: uppercase; }
        td { padding: 18px 20px; border-bottom: 1px solid #f4f4f4; vertical-align: middle; }
        
        .post-img { width: 90px; height: 55px; object-fit: cover; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .badge { padding: 6px 14px; border-radius: 20px; font-size: 11px; font-weight: 700; }
        .bg-success { background: #e8f5e9; color: #2e7d32; }
        .bg-secondary { background: #f5f5f5; color: #757575; }

        .btn-action { width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; text-decoration: none; transition: 0.3s; }
        .btn-edit { background: #f0f4f0; color: var(--sage); }
        .btn-delete { background: #fff1f1; color: #d9534f; }
        .btn-edit:hover { background: var(--sage); color: #fff; }
        .btn-delete:hover { background: #d9534f; color: #fff; }

        .btn-add-new { 
            background: var(--sage); color: #fff; padding: 12px 25px; 
            border-radius: 10px; text-decoration: none; font-weight: 600; 
            display: inline-flex; align-items: center; gap: 8px; transition: 0.3s;
        }
        .btn-add-new:hover { background: #5F745F; transform: translateY(-2px); }
        
        .alert-success { background: #e8f5e9; color: #2e7d32; padding: 15px 25px; border-radius: 10px; margin-bottom: 25px; border-left: 5px solid #2e7d32; }
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
            <div class="header-flex">
                <div>
                    <h1 style="margin: 0; font-size: 32px; color: #333;">Tất cả bài viết</h1>
                    <p style="color: #888; margin-top: 5px;">Xem, sửa hoặc xóa các nội dung Blog và Review sách.</p>
                </div>
                <a href="addPost.php" class="btn-add-new"><i class="fa-solid fa-plus"></i> Viết bài mới</a>
            </div>

            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
                <div class="alert-success"><i class="fa-solid fa-check"></i> Đã xóa bài viết và hình ảnh liên quan thành công!</div>
            <?php endif; ?>

            <div class="panel-table">
                <table>
                    <thead>
                        <tr>
                            <th width="120">Hình ảnh</th>
                            <th>Tiêu đề & Nội dung ngắn</th>
                            <th>Danh mục</th>
                            <th>Trạng thái</th>
                            <th>Ngày đăng</th>
                            <th style="text-align: right;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>
                                <img src="../admin/uploads/<?php echo $row['image']; ?>" class="post-img" onerror="this.src='../admin/uploads/default.jpg'">
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #333; margin-bottom: 4px; font-size: 15px;"><?php echo $row['title']; ?></div>
                                <div style="color: #999; font-size: 12px; line-height: 1.4; max-width: 450px;">
                                    <?php echo mb_substr(strip_tags($row['description']), 0, 80, 'UTF-8') . '...'; ?>
                                </div>
                            </td>
                            <td><span style="color: var(--sage); font-weight: 600; font-size: 13px;"><?php echo $row['category']; ?></span></td>
                            <td>
                                <?php if($row['status'] == 1): ?>
                                    <span class="badge bg-success">Công khai</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Bản nháp</span>
                                <?php endif; ?>
                            </td>
                            <td style="color: #888; font-size: 13px;">
                                <i class="fa-regular fa-calendar-days" style="margin-right: 5px;"></i>
                                <?php echo date('d/m/Y', strtotime($row['created_at'])); ?>
                            </td>
                            <td style="text-align: right;">
                                <a href="editPost.php?id=<?php echo $row['post_id']; ?>" class="btn-action btn-edit" title="Chỉnh sửa"><i class="fa-solid fa-pen"></i></a>
                                <a href="managePost.php?delete_id=<?php echo $row['post_id']; ?>" 
                                   class="btn-action btn-delete" 
                                   title="Xóa vĩnh viễn" 
                                   onclick="return confirm('Cảnh báo: Bạn có chắc chắn muốn xóa bài viết này? Ảnh liên quan cũng sẽ bị xóa khỏi máy chủ!')">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
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