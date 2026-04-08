<?php
session_start();
include "../includes/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // VALIDATE EMAIL
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email không hợp lệ!";
    }

    // VALIDATE PASSWORD (chữ + số, không khoảng trắng, >=6 ký tự)
    elseif (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/', $password)) {
        $error = "Mật khẩu phải >=6 ký tự, gồm chữ và số, không chứa khoảng trắng!";
    } else {
        // PREPARED STATEMENT (CHỐNG SQL INJECTION)
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {

                $_SESSION['user'] = $user;

                if ($user['role'] == 'admin') {
                    header("Location: ../admin/dashboard.php");
                } else {
                    header("Location: ../products/products.php");
                }
                exit();
            } else {
                $error = "Sai mật khẩu!";
            }
        } else {
            $error = "Email không tồn tại!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="../../css/login.css">
</head>

<body>

    <div class="auth-container">
        <form method="POST" class="auth-box">

            <h2>Đăng nhập</h2>

            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

            <!-- EMAIL -->
            <input type="email" name="email" placeholder="Email"
                value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>

            <!-- PASSWORD -->
            <input type="password" name="password" placeholder="Mật khẩu"
                pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$"
                title="Ít nhất 6 ký tự, gồm chữ và số, không khoảng trắng" required>

            <button type="submit">Đăng nhập</button>

            <p>Chưa có tài khoản?
                <a href="register.php">Đăng ký</a>
            </p>

        </form>
    </div>

</body>

</html>