<?php
session_start();
include "../includes/config.php"; 
if (isset($_GET['id'])) {
    $product_id = (int)$_GET['id']; 
    
    // Truy vấn thông tin sản phẩm từ bảng products
    $sql = "SELECT product_id, name, price, image FROM products WHERE product_id = $product_id";
    $result = mysqli_query($conn, $sql);
    $product = mysqli_fetch_assoc($result);

    if ($product) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += 1;
        } else {
            $_SESSION['cart'][$product_id] = [
                'name' => $product['name'],
                'price' => (int)$product['price'], // Giá gốc từ database
                'image' => $product['image'],
                'quantity' => 1
            ];
        }
    }
}
// Sau khi xử lý xong
header("Location: cart.php"); 
exit();
exit();
?>