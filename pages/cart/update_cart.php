<?php
session_start();
if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];

    if (isset($_SESSION['cart'][$id])) {
        if ($action == 'plus') {
            $_SESSION['cart'][$id]['quantity'] += 1;
        } elseif ($action == 'minus') {
            if ($_SESSION['cart'][$id]['quantity'] > 1) {
                $_SESSION['cart'][$id]['quantity'] -= 1;
            } else {
                unset($_SESSION['cart'][$id]);
            }
        } elseif ($action == 'delete') {
            unset($_SESSION['cart'][$id]);
        }
    }
}
// Sau khi xử lý xong, quay lại trang giỏ hàng
header("Location: cart.php");
exit();