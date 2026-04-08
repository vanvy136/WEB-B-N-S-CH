<?php
session_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/config.php';
$vnp_SecureHash = $_GET['vnp_SecureHash'] ?? '';

$inputData = [];
foreach ($_GET as $key => $value) {
    if (substr($key, 0, 4) == "vnp_") {
        $inputData[$key] = $value;
    }
}

unset($inputData['vnp_SecureHash']);
ksort($inputData);

$hashData = "";
foreach ($inputData as $key => $value) {
    $hashData .= urlencode($key) . "=" . urlencode($value) . '&';
}
$hashData = rtrim($hashData, '&');

$secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

$responseCode = $_GET['vnp_ResponseCode'];
$order_id = $_GET['vnp_TxnRef'] ?? '';
$amount = isset($_GET['vnp_Amount']) ? $_GET['vnp_Amount'] / 100 : 0;

if ($secureHash !== $vnp_SecureHash) {
    die("Sai chữ ký (hack detected)");
}

if ($responseCode == '00') {
    if(mysqli_query($conn, "
        UPDATE orders 
        SET status = 'Đã thanh toán' 
        WHERE order_id = '$order_id'
    ")){
        echo "đã update";
    }
    unset($_SESSION['cart']);
    unset($_SESSION['pending_order']);
    unset($_SESSION['vnpay_order_id']);
    header("Location: ../cart/order_success.php?id=$order_id");
    exit();
    

} else {
    mysqli_query($conn, "
        UPDATE orders 
        SET status = 'Thanh toán thất bại' 
        WHERE id = '$order_id'
    ");

    echo "<h3>Thanh toán thất bại!</h3>";
    echo "<a href='../cart.php'>Quay lại giỏ hàng</a>";
}