<?php
// 1. Kết nối cơ sở dữ liệu
include "../includes/config.php";

// 2. Kiểm tra xem có nhận được ID sách cần xóa không
if (isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];

    // 3. (Tùy chọn) Lấy tên file ảnh để xóa khỏi thư mục img cho sạch bộ nhớ
    $sql_img = "SELECT image FROM products WHERE product_id = $product_id";
    $res_img = mysqli_query($conn, $sql_img);
    if ($row = mysqli_fetch_assoc($res_img)) {
        $image_path = "../../img/" . $row['image'];
        if (file_exists($image_path) && $row['image'] != 'default_book.jpg') {
            unlink($image_path); // Xóa file ảnh vật lý
        }
    }

    // 4. Thực hiện lệnh xóa trong database
    $sql_delete = "DELETE FROM products WHERE product_id = $product_id";

    if (mysqli_query($conn, $sql_delete)) {
        // Xóa thành công, quay về trang danh sách với thông báo
        echo "<script>
                alert('Đã xóa sách thành công!');
                window.location.href = 'postProduct.php';
              </script>";
    } else {
        // Lỗi nếu không xóa được
        echo "Lỗi khi xóa: " . mysqli_error($conn);
    }
} else {
    // Nếu truy cập file trực tiếp mà không có ID
    header("Location: postProduct.php");
    exit();
}
?>