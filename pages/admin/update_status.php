<?php
session_start();
include "../includes/config.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id']) && isset($_POST['new_status'])) {
    $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['new_status']);

    // Cập nhật trạng thái trong bảng orders
    $sql = "UPDATE orders SET status = '$new_status' WHERE order_id = '$order_id'";

    if (mysqli_query($conn, $sql)) {
        // Chuyển hướng về trang danh sách kèm theo thông báo thành công (tùy chọn)
        header("Location: order.php?update=success");
        exit();
    } else {
        echo "Lỗi cập nhật: " . mysqli_error($conn);
    }
} else {
    // Nếu truy cập trực tiếp file này mà không qua form
    header("Location: order.php");
    exit();
}
?>