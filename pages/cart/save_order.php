<?php
session_start();
include "../includes/config.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_SESSION['cart'])) {

    // =========================
    // 1. LẤY DATA
    // =========================
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $note = mysqli_real_escape_string($conn, $_POST['note']);

    $total = $_POST['total_price'] ?? 0;
    $discount = $_POST['discount_amount'] ?? 0;
    $shipping = $_POST['shipping_fee'] ?? 0;
    $voucher = $_POST['voucher_code'] ?? '';

    if (!isset($_SESSION['user']['user_id'])) {
        die("Bạn cần đăng nhập");
    }

    $user_id = $_SESSION['user']['user_id'];
    $payment_method = $_POST['payment_method'];

    // =========================
    // 2. CASE VNPAY
    // =========================
    if ($payment_method == 'BANK') {

        $_SESSION['pending_order'] = [
            'user_id' => $user_id,
            'customer_name' => $customer_name,
            'phone' => $phone,
            'address' => $address,
            'note' => $note,
            'total' => $total,
            'discount' => $discount,
            'shipping' => $shipping,
            'voucher' => $voucher
        ];

        header("Location: ../vnpay_php/vnpay_create_payment.php");
        exit();
    }

    // =========================
    // 3. CASE COD
    // =========================
    $status = 'Chờ xử lý';

    $sql_order = "INSERT INTO orders 
        (user_id, customer_name, phone, address, total_price, discount_amount, shipping_fee, voucher_code, status, note, created_at) 
        VALUES 
        ('$user_id', '$customer_name', '$phone', '$address', '$total', '$discount', '$shipping', '$voucher', '$status', '$note', NOW())";

    if (!mysqli_query($conn, $sql_order)) {
        die("Lỗi khi lưu đơn hàng: " . mysqli_error($conn));
    }

    $order_id = mysqli_insert_id($conn);

    // insert order_details
    foreach ($_SESSION['cart'] as $p_id => $item) {
        $price = $item['price'];
        $qty = $item['quantity'];

        mysqli_query($conn, "INSERT INTO order_details (order_id, product_id, quantity, price) 
                            VALUES ('$order_id', '$p_id', '$qty', '$price')");
    }

    unset($_SESSION['cart']);
    unset($_SESSION['coupon']);

    header("Location: order_success.php?id=$order_id");
    exit();
}
?>