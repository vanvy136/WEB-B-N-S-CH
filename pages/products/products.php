<?php
session_start();
// Kết nối database với đường dẫn chính xác theo cấu trúc thư mục của bạn
include "../includes/config.php"; 

// Cấu hình phân trang
$limit = 12;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Lọc theo danh mục
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Truy vấn tính tổng số sản phẩm để phân trang
if ($category > 0) {
    $totalQuery = "SELECT COUNT(*) as total FROM products WHERE category_id = $category";
} else {
    $totalQuery = "SELECT COUNT(*) as total FROM products";
}
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalProducts = $totalRow['total'];
$totalPages = ceil($totalProducts / $limit);

// Truy vấn lấy danh sách sản phẩm thực tế
if ($category > 0) {
    $sql = "SELECT * FROM products WHERE category_id = $category LIMIT $limit OFFSET $offset";
} else {
    $sql = "SELECT * FROM products LIMIT $limit OFFSET $offset";
}
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sagewood Books - Sản phẩm</title>
    <link rel="stylesheet" href="../../css/products.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <?php include "../includes/header.php"; ?>
    <div class="hero products-hero">
    <div class="container">
      <div class="products-hero-grid">
        <div>
          <h1>Kho sách chọn lọc</h1>
          <p>
            Khám phá những tựa sách truyền cảm hứng, tri thức và giải trí.
            Mỗi cuốn sách là một hành trình đang chờ bạn mở ra 📖✨
          </p>

          <div class="hero-actions">
            <button class="btn btn-primary">
              <i class="fa-solid fa-fire"></i> Sách hot hôm nay
            </button>
            <button class="btn btn-outline">
              <i class="fa-solid fa-filter"></i> Lọc theo nhu cầu
            </button>
          </div>
        </div>
        <div class="hero-mini-box">
          <div class="mini-item">
            <i class="fa-solid fa-truck-fast"></i>
            <div>
              <strong>Giao nhanh</strong>
              <span>Nhận sách trong 24h</span>
            </div>
          </div>

          <div class="mini-item">
            <i class="fa-solid fa-tags"></i>
            <div>
              <strong>Ưu đãi</strong>
              <span>Giảm giá mỗi ngày</span>
            </div>
          </div>

          <div class="mini-item">
            <i class="fa-solid fa-shield-heart"></i>
            <div>
              <strong>An tâm</strong>
              <span>Đổi trả linh hoạt</span>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
    <main>
        <div class="container">
            <div class="cats">
        <a href="?category=1"
          class="cat <?php if (isset($_GET['category']) && $_GET['category'] == 1) echo 'active'; ?>">
          <i class="fa-solid fa-brain"></i><span>Kỹ năng</span>
        </a>

        <a href="?category=2"
          class="cat <?php if (isset($_GET['category']) && $_GET['category'] == 2) echo 'active'; ?>">
          <i class="fa-solid fa-briefcase"></i><span>Kinh tế</span>
        </a>

        <a href="?category=3"
          class="cat <?php if (isset($_GET['category']) && $_GET['category'] == 3) echo 'active'; ?>">
          <i class="fa-solid fa-heart"></i><span>Tiểu thuyết</span>
        </a>

        <a href="?category=4"
          class="cat <?php if (isset($_GET['category']) && $_GET['category'] == 4) echo 'active'; ?>">
          <i class="fa-solid fa-graduation-cap"></i><span>Học tập</span>
        </a>

        <a href="?category=5"
          class="cat <?php if (isset($_GET['category']) && $_GET['category'] == 5) echo 'active'; ?>">
          <i class="fa-solid fa-child"></i><span>Thiếu nhi</span>
        </a>

        <a href="?category=6"
          class="cat <?php if (isset($_GET['category']) && $_GET['category'] == 6) echo 'active'; ?>">
          <i class="fa-solid fa-mask"></i><span>Truyện tranh</span>
        </a>

      </div>
<h2 class="title-page">
        <a href="products.php" <?php if (!isset($_GET['category'])) echo 'active'; ?>>Danh sách sản phẩm</a>
      </h2>
            
            <div class="products">
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <div class="card">
                        <div class="thumb">
                            <img src="../../img/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
                        </div>
                        <div class="content">
                            <div class="title"><?php echo $row['name']; ?></div>
                            <div class="author"><?php echo $row['author']; ?></div>
                            <div class="row">
                                <div class="price"><?php echo number_format($row['price']); ?>đ</div>
                            </div>
                            <div class="actions">
                                <a href="../cart/addcart.php?id=<?php echo $row['product_id']; ?>" class="btn-cart">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                </a>
                                <a href="productDetails.php?id=<?php echo $row['product_id']; ?>" class="btn-buy">
                                    Chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a class="page-btn" href="?page=<?php echo $page - 1; ?><?php echo ($category > 0) ? '&category='.$category : ''; ?>">‹</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a class="page-btn <?php echo ($i == $page) ? 'active' : ''; ?>" 
                       href="?page=<?php echo $i; ?><?php echo ($category > 0) ? '&category='.$category : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a class="page-btn" href="?page=<?php echo $page + 1; ?><?php echo ($category > 0) ? '&category='.$category : ''; ?>">›</a>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <?php include "../includes/footer.php"; ?>
</body>
</html>