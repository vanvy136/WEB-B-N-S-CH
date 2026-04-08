<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sagewood Books - Trang chủ</title>

  <!-- CSS -->
  <link rel="stylesheet" href="../css/index.css">
  <link rel="stylesheet" href="../css/login.css">

  <!-- ICON -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

  <!-- HEADER -->
  <?php include "includes/header.php"; ?>

  <!-- HERO -->
  <section class="hero">
    <div class="container">
      <div class="hero-grid">

        <div>
          <h1>
            Sách hay cho<br>
            tâm hồn & tri thức
          </h1>

          <p>
            Mua sách dễ dàng, giao nhanh, giá tốt.
            Tận hưởng không gian đọc sách nhẹ nhàng.
          </p>

          <div class="hero-actions">
            <a href="#sanpham">
              <button class="btn btn-primary">
                <i class="fa-solid fa-cart-shopping"></i> Mua sắm ngay
              </button>
            </a>

            <button class="btn btn-outline">
              <i class="fa-regular fa-circle-play"></i> Xem giới thiệu
            </button>
          </div>

          <div class="stats">
            <div class="stat">
              <strong>1.500+</strong>
              <span>Đầu sách đa dạng</span>
            </div>
            <div class="stat">
              <strong>1.000+</strong>
              <span>Đơn hàng đã giao</span>
            </div>
            <div class="stat">
              <strong>20+</strong>
              <span>Đối tác xuất bản</span>
            </div>
          </div>
        </div>

        <!-- BANNER -->
        <div class="hero-img banner-slider">

          <div class="banner-wrap">
            <button class="banner-btn prev" onclick="prevBanner()">
              <i class="fa-solid fa-chevron-left"></i>
            </button>

            <div class="banner-slides">
              <div class="banner-slide active">
                <img src="../img/banner1" alt="Banner 1">
              </div>

              <div class="banner-slide">
                <img src="../img/banner2" alt="Banner 2">
              </div>
            </div>

            <button class="banner-btn next" onclick="nextBanner()">
              <i class="fa-solid fa-chevron-right"></i>
            </button>
          </div>

          <div class="banner-dots">
            <span class="banner-dot active" onclick="goBanner(0)"></span>
            <span class="banner-dot" onclick="goBanner(1)"></span>
          </div>

        </div>

      </div>
    </div>
  </section>

  <!-- MAIN -->
  <main>
    <div class="container">

      <!-- DANH MỤC -->
      <div class="section-title">
        <h2>Danh mục nổi bật</h2>
        <a href="products/products.php">Xem tất cả →</a>
      </div>

      <div class="cats">
        <a href="products/products.php?category=1" class="cat">
          <i class="fa-solid fa-brain"></i><span>Kỹ năng</span>
        </a>

        <a href="products/products.php?category=2" class="cat">
          <i class="fa-solid fa-briefcase"></i><span>Kinh tế</span>
        </a>

        <a href="products/products.php?category=3" class="cat">
          <i class="fa-solid fa-heart"></i><span>Tiểu thuyết</span>
        </a>

        <a href="products/products.php?category=4" class="cat">
          <i class="fa-solid fa-graduation-cap"></i><span>Học tập</span>
        </a>

        <a href="products/products.php?category=5" class="cat">
          <i class="fa-solid fa-child"></i><span>Thiếu nhi</span>
        </a>

        <a href="products/products.php?category=6" class="cat">
          <i class="fa-solid fa-mask"></i><span>Truyện tranh</span>
        </a>
      </div>

      <!-- SẢN PHẨM -->
      <div class="section-title" style="margin-top:22px;">
        <h2 id="sanpham">Sách bán chạy</h2>
      </div>

      <div class="products">

        <a class="card" href="products/productDetails.php?id=1">
          <div class="thumb">
            <span class="sale">-20%</span>
            <img src="../img/DacNhanTam.jpg" alt="">
          </div>
          <div class="content">
            <div class="title">Đắc Nhân Tâm</div>
            <div class="author">Dale Carnegie</div>
            <div class="row">
              <div class="price">99.000đ</div>
              <button class="btn-add"><i class="fa-solid fa-cart-plus"></i></button>
            </div>
          </div>
        </a>

        <a class="card" href="products/productDetails.php?id=11">
          <div class="thumb">
            <span class="sale">-15%</span>
            <img src="../img/NhaGiaKim.jpg" alt="">
          </div>
          <div class="content">
            <div class="title">Nhà Giả Kim</div>
            <div class="author">Paulo Coelho</div>
            <div class="row">
              <div class="price">79.000đ</div>
              <button class="btn-add"><i class="fa-solid fa-cart-plus"></i></button>
            </div>
          </div>
        </a>

        <a class="card" href="products/productDetails.php?id=2">
          <div class="thumb">
            <span class="sale">-30%</span>
            <img src="../img/7ThoiQuenHieuQua.jpg" alt="">
          </div>
          <div class="content">
            <div class="title">7 Thói Quen Hiệu Quả</div>
            <div class="author">Stephen Covey</div>
            <div class="row">
              <div class="price">149.000đ</div>
              <button class="btn-add"><i class="fa-solid fa-cart-plus"></i></button>
            </div>
          </div>
        </a>

        <a class="card" href="products/productDetails.php?id=4">
          <div class="thumb">
            <span class="sale">-10%</span>
            <img src="../img/TuoiTreDangGiaBaoNhieu.jpg" alt="">
          </div>
          <div class="content">
            <div class="title">Tuổi Trẻ Đáng Giá Bao Nhiêu</div>
            <div class="author">Rosie Nguyễn</div>
            <div class="row">
              <div class="price">89.000đ</div>
              <button class="btn-add"><i class="fa-solid fa-cart-plus"></i></button>
            </div>
          </div>
        </a>

      </div>

    </div>
  </main>

  <!-- FOOTER -->
  <?php include "includes/footer.php"; ?>

  <!-- SCRIPT -->
  <script>
    let bannerIndex = 0;

    function showBanner(i) {
      const banners = document.querySelectorAll(".banner-slide");
      const dots = document.querySelectorAll(".banner-dot");

      if (i < 0) i = banners.length - 1;
      if (i >= banners.length) i = 0;

      banners.forEach(s => s.classList.remove("active"));
      dots.forEach(d => d.classList.remove("active"));

      banners[i].classList.add("active");
      dots[i].classList.add("active");

      bannerIndex = i;
    }

    function nextBanner() {
      showBanner(bannerIndex + 1);
    }

    function prevBanner() {
      showBanner(bannerIndex - 1);
    }

    function goBanner(i) {
      showBanner(i);
    }

    document.addEventListener("DOMContentLoaded", () => showBanner(0));
  </script>

</body>

</html>