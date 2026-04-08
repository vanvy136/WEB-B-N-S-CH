<?php
session_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/config.php';

// =========================
// 1. CHECK DATA
// =========================
if (!isset($_SESSION['pending_order']) || empty($_SESSION['cart'])) {
    die("Không có dữ liệu đơn hàng");
}

$data = $_SESSION['pending_order'];

$user_id = $data['user_id'];
$customer_name = $data['customer_name'];
$phone = $data['phone'];
$address = $data['address'];
$note = $data['note'];
$total = $data['total'];
$discount = $data['discount'];
$shipping = $data['shipping'];
$voucher = $data['voucher'];

$status = 'Chờ thanh toán';

// =========================
// 2. INSERT ORDER
// =========================
$sql = "INSERT INTO orders 
    (user_id, customer_name, phone, address, total_price, discount_amount, shipping_fee, voucher_code, status, note, created_at) 
    VALUES 
    ('$user_id', '$customer_name', '$phone', '$address', '$total', '$discount', '$shipping', '$voucher', '$status', '$note', NOW())";

if (!mysqli_query($conn, $sql)) {
    die("Lỗi tạo đơn hàng: " . mysqli_error($conn));
}

$order_id = mysqli_insert_id($conn);

// =========================
// 3. INSERT ORDER DETAILS
// =========================
foreach ($_SESSION['cart'] as $p_id => $item) {
    $price = $item['price'];
    $qty = $item['quantity'];

    mysqli_query($conn, "INSERT INTO order_details (order_id, product_id, quantity, price) 
                        VALUES ('$order_id', '$p_id', '$qty', '$price')");
}

// =========================
// 4. TẠO LINK VNPAY
// =========================
$vnp_TxnRef = $order_id;
$vnp_Amount = $total * 100;

$startTime = date("YmdHis");
$expire = date('YmdHis', strtotime('+15 minutes'));

$inputData = array(
    "vnp_Version" => "2.1.0",
    "vnp_TmnCode" => $vnp_TmnCode,
    "vnp_Amount" => $vnp_Amount,
    "vnp_Command" => "pay",
    "vnp_CreateDate" => $startTime,
    "vnp_CurrCode" => "VND",
    "vnp_IpAddr" => $_SERVER['REMOTE_ADDR'],
    "vnp_Locale" => "vn",
    "vnp_OrderInfo" => "Thanh toán đơn hàng #" . $order_id,
    "vnp_OrderType" => "billpayment",
    "vnp_ReturnUrl" => $vnp_Returnurl,
    "vnp_TxnRef" => $vnp_TxnRef,
    "vnp_ExpireDate" => $expire
);

// =========================
// 5. HASH
// =========================
ksort($inputData);

$query = "";
$hashdata = "";

foreach ($inputData as $key => $value) {
    $hashdata .= urlencode($key) . "=" . urlencode($value) . '&';
    $query .= urlencode($key) . "=" . urlencode($value) . '&';
}

$hashdata = rtrim($hashdata, '&');

$vnp_SecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);

$vnp_Url = $vnp_Url . "?" . $query . "vnp_SecureHash=" . $vnp_SecureHash;

// lưu order_id để xử lý return
$_SESSION['vnpay_order_id'] = $order_id;

header("Location: " . $vnp_Url);
exit;
?>