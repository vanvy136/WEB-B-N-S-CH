<?php
include "../includes/config.php";

// Lấy product_id từ URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Query sản phẩm
$sql = "SELECT * FROM products WHERE product_id = $product_id";
$result = mysqli_query($conn, $sql);

// Kiểm tra
if (!$result || mysqli_num_rows($result) == 0) {
  echo "Không tìm thấy sản phẩm!";
  exit;
}

$product = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title><?php echo $product['name']; ?> - Sagewood Books</title>
  <link rel="stylesheet" href="../../css/productDetails.css">
  <link rel="stylesheet" href="../../css/index.css">
  <link rel="stylesheet" href="../../css/login.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <?php include "../includes/header.php"; ?>

<!-- MAIN -->
<main>
  <div class="container">

    <!-- Breadcrumb -->
    <div class="breadcrumb">
      <a href="../index.php">Trang chủ</a> / 
      <span><?php echo $product['name']; ?></span>
    </div>

    <!-- CHI TIẾT -->
    <div class="detail-grid">

      <!-- Ảnh -->
      <div class="gallery">
        <div class="big-img">
          <img src="../../img/<?php echo $product['image']; ?>" alt="">
        </div>
      </div>

      <!-- Thông tin -->
      <div class="info">
        <h1><?php echo $product['name']; ?></h1>

        <p class="author">
          Tác giả: <b><?php echo $product['author']; ?></b>
        </p>

        <div class="price-box">
          <div class="price">
            <?php echo number_format($product['price']); ?>đ
          </div>
        </div>

        <div class="desc">
          <?php echo $product['description']; ?>
        </div>

        <div class="actions">
          <a href="../cart/addcart.php?id=<?php echo $product['product_id']; ?>" class="btn btn-primary">
            <i class="fa-solid fa-cart-plus"></i> Thêm vào giỏ
          </a>

          <button class="btn btn-danger">
            <i class="fa-solid fa-bolt"></i> Mua ngay
          </button>
        </div>

        <div class="policy">
          <div><i class="fa-solid fa-truck-fast"></i> Giao nhanh 2-4h nội thành</div>
          <div><i class="fa-solid fa-book"></i> Sách chính hãng 100%</div>
          <div><i class="fa-solid fa-rotate-left"></i> Đổi trả trong 7 ngày</div>
        </div>

      </div>
    </div>

  </div>
</main>

    <?php include "../includes/footer.php"; ?>
</body>
</html>