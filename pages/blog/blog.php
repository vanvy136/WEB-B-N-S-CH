<?php
include "../includes/config.php";

// 1. PHÂN TRANG
$limit = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// 2. ĐẾM TỔNG (status = 1 là bài viết đã xuất bản)
$totalQuery = "SELECT COUNT(*) as total FROM posts WHERE status = 1";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalPages = ceil($totalRow['total'] / $limit);

// 3. LẤY DỮ LIỆU
$sql = "SELECT * FROM posts WHERE status = 1 ORDER BY post_id DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - Sagewood Books</title>
    
    <link rel="stylesheet" href="../../css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        :root { --sage: #7A8F7A; --bg: #f9f6f2; --text: #333; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: var(--bg); color: var(--text); margin: 0; }
        
        /* ĐỒNG BỘ HERO VỚI INDEX */
        .hero { background: var(--sage); color: white; padding: 60px 0; text-align: center; }
        .hero h1 { font-size: 3rem; margin: 0; font-weight: 700; }
        .hero p { opacity: 0.9; font-size: 1.1rem; margin-top: 10px; }

        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }

        /* BLOG GRID */
        .blog-list { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); 
            gap: 30px; 
            margin: 50px 0;
        }

        /* CARD ĐỒNG BỘ VỚI CARD SẢN PHẨM */
        .card { 
            background: #fff; 
            border-radius: 15px; 
            overflow: hidden; 
            transition: 0.3s; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            text-decoration: none;
            color: inherit;
        }
        .card:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        
        .thumb { position: relative; height: 230px; overflow: hidden; }
        .thumb img { width: 100%; height: 100%; object-fit: cover; }
        
        .card-body { padding: 25px; flex-grow: 1; display: flex; flex-direction: column; }
        .date { color: var(--sage); font-size: 0.85rem; font-weight: 600; margin-bottom: 10px; display: flex; align-items: center; gap: 5px; }
        
        .card h3 { margin: 0 0 12px 0; font-size: 1.3rem; line-height: 1.4; color: #2c3e50; transition: 0.2s; }
        .card:hover h3 { color: var(--sage); }
        
        .desc { color: #666; font-size: 0.95rem; line-height: 1.6; margin-bottom: 20px; flex-grow: 1; }

        /* NÚT ĐỌC TIẾP */
        .btn-read { 
            color: var(--sage); 
            text-decoration: none; 
            font-weight: 700; 
            font-size: 0.9rem;
            display: inline-flex; 
            align-items: center; 
            gap: 8px; 
            transition: 0.2s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .btn-read:hover { gap: 12px; }

        /* PHÂN TRANG ĐỒNG BỘ */
        .pagination { text-align: center; margin-bottom: 60px; }
        .pagination a { 
            display: inline-block; 
            padding: 12px 20px; 
            margin: 0 5px; 
            background: #fff; 
            color: var(--text); 
            text-decoration: none; 
            border-radius: 10px; 
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            transition: 0.3s;
        }
        .pagination a.active { background: var(--sage); color: #fff; }
        .pagination a:hover:not(.active) { background: #eee; }

        footer { background: #fff; padding: 40px 0; border-top: 1px solid #eee; }
    </style>
</head>

<body>

<?php include "../includes/header.php"; ?>

<header>
    <div class="hero">
        <div class="container">
            <h1>Blog Sagewood</h1>
        </div>
    </div>
</header>

<main class="container">
    <div class="blog-list">
        <?php 
        if(mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) { 
                // Cắt mô tả ngắn
                $content = strip_tags($row['content']);
                $preview = mb_substr($content, 0, 100, "UTF-8");
                
                // Đường dẫn ảnh từ thư mục admin
                $imagePath = "../admin/uploads/" . $row['image'];
        ?>
            <a href="blogDetails.php?id=<?php echo $row['post_id']; ?>" class="card">
                <div class="thumb">
                    <img src="<?php echo $imagePath; ?>" onerror="this.src='../admin/uploads/default.jpg'" alt="<?php echo $row['title']; ?>">
                </div>
                <div class="card-body">
                    <span class="date">
                        <i class="fa-regular fa-calendar-check"></i> 
                        <?php echo date("d/m/Y", strtotime($row['created_at'])); ?>
                    </span>
                    <h3><?php echo $row['title']; ?></h3>
                    <p class="desc"><?php echo $preview; ?>...</p>
                    <span class="btn-read">
                        Xem chi tiết <i class="fa-solid fa-arrow-right-long"></i>
                    </span>
                </div>
            </a>
        <?php 
            } 
        } else {
            echo "<div style='grid-column: 1/-1; text-align: center; padding: 100px 0;'>
                    <i class='fa-regular fa-folder-open' style='font-size: 3rem; color: #ccc; margin-bottom: 20px; display: block;'></i>
                    <p style='color: #888;'>Hiện tại chưa có bài viết nào trong mục này.</p>
                  </div>";
        }
        ?>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
            <a href="?page=<?php echo $i; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php } ?>
    </div>
    <?php endif; ?>
</main>

<?php include "../includes/footer.php"; ?>

</body>
</html>