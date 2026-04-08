<?php
session_start();
include "../includes/config.php";

// Kiểm tra nếu có ID được truyền vào
if (isset($_GET['id'])) {
    $order_id = $_GET['id'];

    // 1. Xóa các sản phẩm liên quan trong bảng order_details trước
    $sql_details = "DELETE FROM order_details WHERE order_id = '$order_id'";
    
    if (mysqli_query($conn, $sql_details)) {
        // 2. Sau khi xóa chi tiết xong mới xóa đơn hàng ở bảng orders
        $sql_order = "DELETE FROM orders WHERE order_id = '$order_id'";
        
        if (mysqli_query($conn, $sql_order)) {
            // Xóa thành công, quay về trang quản lý đơn hàng
            header("Location: order.php?msg=deleted");
            exit();
        } else {
            echo "Lỗi khi xóa đơn hàng: " . mysqli_error($conn);
        }
    } else {
        echo "Lỗi khi xóa chi tiết đơn hàng: " . mysqli_error($conn);
    }
} else {
    header("Location: order.php");
    exit();
}
?>