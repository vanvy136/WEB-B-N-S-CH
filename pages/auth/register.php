<?php
include "../includes/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password_raw = $_POST['password'];

        // VALIDATE EMAIL + bắt buộc gmail
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9._%+-]+@gmail\.com$/', $email)) {
                $error = "Email phải đúng định dạng và là @gmail.com!";
        }

        // VALIDATE PASSWORD (có chữ + số, không khoảng trắng, >=6 ký tự)
        elseif (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/', $password_raw)) {
                $error = "Mật khẩu phải >=6 ký tự, gồm chữ và số, không chứa khoảng trắng!";
        } else {
                $password = password_hash($password_raw, PASSWORD_DEFAULT);

                // check email tồn tại
                $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

                if (mysqli_num_rows($check) > 0) {
                        $error = "Email đã tồn tại!";
                } else {
                        $sql = "INSERT INTO users(name,email,password,role)
                    VALUES('$name','$email','$password','user')";

                        mysqli_query($conn, $sql);

                        header("Location: login.php");
                        exit();
                }
        }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
        <meta charset="UTF-8">
        <title>Đăng ký</title>
        <link rel="stylesheet" href="../../css/login.css">
</head>

<body>

        <div class="auth-container">
                <form method="POST" class="auth-box">
                        <h2>Đăng ký</h2>

                        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

                        <input type="text" name="name" placeholder="Họ tên" required>
                        <input type="email" name="email" placeholder="Email" required>
                        <input type="password" name="password" placeholder="Mật khẩu" required>

                        <button type="submit">Đăng ký</button>

                        <p>Đã có tài khoản?
                                <a href="login.php">Đăng nhập</a>
                        </p>
                </form>
        </div>

</body>

</html>