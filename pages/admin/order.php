<?php
session_start();
include "../includes/config.php";

// Xử lý Xóa đơn hàng
if (isset($_GET['delete_id'])) {
    $del_id = $_GET['delete_id'];
    mysqli_query($conn, "DELETE FROM order_details WHERE order_id = '$del_id'");
    if (mysqli_query($conn, "DELETE FROM orders WHERE order_id = '$del_id'")) {
        header("Location: order.php?msg=deleted");
        exit();
    }
}

$sql = "SELECT * FROM orders ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Quản lý đơn hàng - Sagewood Admin</title>
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

        /* Table Style Mới */
        .panel-table { background: #fff; padding: 30px; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { text-align: left; padding: 15px; border-bottom: 2px solid #f0f0f0; color: #777; font-size: 14px; }
        td { padding: 20px 15px; border-bottom: 1px solid #f4f4f4; vertical-align: middle; }
        
        .status-badge { padding: 6px 14px; border-radius: 30px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; }
        .st-pending { background: #fff3e0; color: #ef6c00; }
        .st-shipping { background: #e3f2fd; color: #1565c0; }
        .st-success { background: #e8f5e9; color: #2e7d32; }
        .st-cancel { background: #ffebee; color: #c62828; }

        .btn-action { width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; text-decoration: none; transition: 0.3s; }
        .btn-view { background: #f0f4f0; color: #7A8F7A; }
        .btn-view:hover { background: #7A8F7A; color: #fff; }
        .btn-delete { background: #fff1f1; color: #d9534f; margin-left: 5px; }
        .btn-delete:hover { background: #d9534f; color: #fff; }

        .alert-msg { background: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: 10px; margin-bottom: 25px; border-left: 5px solid #2e7d32; }
    </style>
</head>
<body>
    <?php include "../includes/header.php"; ?>
    <div class="admin-layout">
        <aside class="sidebar">
            <nav>
                <a href="dashboard.php"><i class="fa-solid fa-chart-line"></i> Tổng quan</a>
                <a href="postProduct.php"><i class="fa-solid fa-book-open"></i> Quản lý sách</a>
                <a href="managePost.php"><i class="fa-solid fa-pen-to-square"></i> Quản lý bài viết</a>
                <a href="order.php" class="active"><i class="fa-solid fa-box"></i> Quản lý đơn hàng</a>
                <a href="statistics.php"><i class="fa-solid fa-chart-pie"></i> Thống kê</a>
            </nav>
        </aside>

        <main class="content-area">
            <div style="margin-bottom: 35px;">
                <h1 style="margin: 0; font-size: 32px;">Danh sách đơn hàng</h1>
                <p style="color: #888; margin-top: 5px;">Quản lý quy trình vận chuyển và trạng thái thanh toán</p>
            </div>

            <?php if(isset($_GET['msg'])): ?>
                <div class="alert-msg"><i class="fa-solid fa-check-circle"></i> Đã xóa dữ liệu đơn hàng thành công!</div>
            <?php endif; ?>

            <div class="panel-table">
                <table>
                    <thead>
                        <tr>
                            <th>MÃ ĐƠN</th>
                            <th>KHÁCH HÀNG</th>
                            <th>TỔNG TIỀN</th>
                            <th>TRẠNG THÁI</th>
                            <th>NGÀY ĐẶT</th>
                            <th style="text-align: right;">HÀNH ĐỘNG</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)): 
                            $st_class = 'st-pending';
                            if($row['status'] == 'Đang giao') $st_class = 'st-shipping';
                            if(in_array($row['status'], ['Thành công', 'Đã hoàn thành'])) $st_class = 'st-success';
                            if($row['status'] == 'Đã hủy') $st_class = 'st-cancel';
                        ?>
                        <tr>
                            <td><b style="color: #7A8F7A;">#SW<?php echo $row['order_id']; ?></b></td>
                            <td>
                                <div style="font-weight: 600;"><?php echo $row['customer_name'] ?: "Khách lẻ"; ?></div>
                                <div style="font-size: 12px; color: #999;"><i class="fa-solid fa-phone"></i> <?php echo $row['phone']; ?></div>
                            </td>
                            <td><b style="font-size: 16px;"><?php echo number_format($row['total_price']); ?>đ</b></td>
                            <td>
                                <span class="status-badge <?php echo $st_class; ?>">
                                    <i class="fa-solid fa-circle" style="font-size: 6px;"></i> <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td style="color: #666; font-size: 14px;"><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                            <td style="text-align: right;">
                                <a href="orderdetail.php?id=<?php echo $row['order_id']; ?>" class="btn-action btn-view"><i class="fa-solid fa-eye"></i></a>
                                <a href="order.php?delete_id=<?php echo $row['order_id']; ?>" class="btn-action btn-delete" onclick="return confirm('Xóa đơn hàng này?')"><i class="fa-solid fa-trash"></i></a>
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