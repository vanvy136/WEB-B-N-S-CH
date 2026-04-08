<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<header>
    <div class="container">
        <div class="nav">

            <!-- LOGO -->
            <a class="logo" href="/sagewood/pages/index.php">
                <span>Sagewood</span> Books
            </a>

            <!-- MENU -->
            <nav class="menu">
                <a href="/sagewood/pages/index.php">Trang chủ</a>
                <a href="/sagewood/pages/products/products.php">Sản phẩm</a>
                <a href="/sagewood/pages/blog/blog.php">Blog</a>
            </nav>

            <!-- RIGHT -->
            <div class="right">

                <!-- SEARCH -->
                <div class="search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" placeholder="Tìm kiếm sách...">
                </div>

                <!-- USER -->
                <?php if (isset($_SESSION['user'])) { ?>

                    <a href="/sagewood/pages/profile.php">
                        👋 <?php echo $_SESSION['user']['name']; ?>
                    </a>

                    <!-- ADMIN -->
                    <?php if ($_SESSION['user']['role'] == 'admin') { ?>
                        <a href="/sagewood/pages/admin/dashboard.php" class="admin-btn">
                            ⚙️
                        </a>
                    <?php } ?>

                    <a href="/sagewood/pages/auth/logout.php">Đăng xuất</a>

                <?php } else { ?>

                    <a href="/sagewood/pages/auth/login.php">
                        <i class="fa-regular fa-user"></i>
                    </a>

                <?php } ?>

                <a class="icon-btn" href="../cart/cart.php">
                        <i class="fa-solid fa-bag-shopping"></i>
                        <span class="badge">
                            <?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>
                        </span>
                </a>

            </div>

        </div>
    </div>
</header>