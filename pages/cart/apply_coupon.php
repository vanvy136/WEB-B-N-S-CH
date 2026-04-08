<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['coupon_code'])) {
    $code = strtoupper(trim($_POST['coupon_code']));

    // Danh sách mã giảm giá
    $coupons = [
        'SAGE10'  => ['type' => 'percent', 'value' => 10], 
        'GIAM50K' => ['type' => 'fixed',   'value' => 50000], 
        'HELLO'   => ['type' => 'percent', 'value' => 20]  
    ];

    if (array_key_exists($code, $coupons)) {
        $_SESSION['coupon'] = [
            'code' => $code,
            'type' => $coupons[$code]['type'],
            'value' => $coupons[$code]['value']
        ];
        $_SESSION['coupon_msg'] = "Áp dụng mã $code thành công!";
    } else {
        unset($_SESSION['coupon']);
        $_SESSION['coupon_msg'] = "Mã giảm giá không tồn tại.";
    }
}
header("Location: cart.php");
exit();
?>