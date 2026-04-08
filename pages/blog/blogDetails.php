<?php
include "../includes/config.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 1. QUERY CHI TIẾT BÀI VIẾT
$stmt = $conn->prepare("SELECT * FROM posts WHERE post_id = ? AND status = 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if (!$post) {
    die("<div style='text-align:center; padding:100px;'><h3>Bài viết không tồn tại.</h3><a href='blog.php'>Quay lại</a></div>");
}

// 2. QUERY SIDEBAR
$sidebarResult = $conn->query("SELECT * FROM posts WHERE status = 1 AND post_id != $id ORDER BY post_id DESC LIMIT 3");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($post['title']) ?> - Sagewood Blog</title>
    <link rel="stylesheet" href="../../css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root { --sage: #7A8F7A; --sage-light: #f4f6f4; --text-main: #1a2a3a; --text-sub: #6B7280; }
        body { background: #fdfaf7; font-family: 'Segoe UI', sans-serif; margin: 0; color: #333; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        
        /* Layout */
        .main-wrapper { display: grid; grid-template-columns: 1fr 350px; gap: 40px; margin: 40px auto; }
        .article-card { background: #fff; padding: 45px; border-radius: 20px; box-shadow: 0 4px 25px rgba(0,0,0,0.02); }
        
        /* Header Post */
        .post-header { margin-bottom: 30px; background: transparent !important; }
        .post-title { background: transparent !important; padding: 0 !important; font-size: 2.8rem; font-weight: 800; color: var(--text-main); margin: 10px 0; }
        .post-meta { display: flex; gap: 20px; color: var(--text-sub); font-size: 0.9rem; }
        .badge { background: var(--sage-light); color: var(--sage); padding: 5px 15px; border-radius: 50px; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; }
        
        /* Content */
        .post-thumbnail { width: 100%; border-radius: 15px; margin: 20px 0; }
        .post-sapo { font-size: 1.2rem; font-style: italic; border-left: 4px solid var(--sage); padding: 15px 25px; margin-bottom: 30px; background: #f9fbf9; }
        .post-body { font-size: 1.15rem; line-height: 1.9; }
        
        /* Sidebar */
        .sidebar-widget { background: #fff; padding: 25px; border-radius: 20px; margin-bottom: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.02); }
        .side-item { display: flex; gap: 15px; margin-bottom: 20px; text-decoration: none; color: inherit; align-items: center; }
        .side-item img { width: 70px; height: 70px; border-radius: 10px; object-fit: cover; }
        
        @media (max-width: 1024px) { .main-wrapper { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<?php include "../includes/header.php"; ?>

<main class="container">
    <div class="main-wrapper">
        <article class="article-card">
            <a href="blog.php" style="color:var(--sage); text-decoration:none; font-weight:700;">
                <i class="fa-solid fa-arrow-left"></i> Quay lại Blog
            </a>

            <header class="post-header">
                <span class="badge"><?= htmlspecialchars($post['category']) ?></span>
                <h1 class="post-title"><?= htmlspecialchars($post['title']) ?></h1>
                <div class="post-meta">
                    <span><i class="fa-regular fa-calendar"></i> <?= date("d/m/Y", strtotime($post['created_at'])) ?></span>
                    <span><i class="fa-regular fa-clock"></i> 5 phút đọc</span>
                </div>
            </header>

            <img src="../admin/uploads/<?= $post['image'] ?>" class="post-thumbnail" onerror="this.src='../admin/uploads/default.jpg'">

            <div class="post-sapo"><?= nl2br(htmlspecialchars($post['description'])) ?></div>
            <div class="post-body"><?= nl2br($post['content']) ?></div>

            <div style="margin-top:40px; padding-top:20px; border-top:1px solid #eee; display:flex; justify-content:space-between; align-items:center;">
                <span style="font-weight:700; color:var(--text-sub);">Chia sẻ:</span>
                <div style="display:flex; gap:10px;">
                    <a href="#" style="color:#1877F2; font-size:1.5rem;"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#" style="color:#000; font-size:1.5rem;"><i class="fa-brands fa-x-twitter"></i></a>
                </div>
            </div>
        </article>

        <aside class="sidebar">
            <div class="sidebar-widget">
                <h3 style="margin-top:0;">Mới nhất</h3>
                <?php while($s = $sidebarResult->fetch_assoc()): ?>
                <a href="blogDetails.php?id=<?= $s['post_id'] ?>" class="side-item">
                    <img src="../admin/uploads/<?= $s['image'] ?>" onerror="this.src='../admin/uploads/default.jpg'">
                    <h4 style="margin:0; font-size:0.95rem;"><?= mb_substr($s['title'], 0, 45, 'UTF-8') ?>...</h4>
                </a>
                <?php endwhile; ?>
            </div>
        </aside>
    </div>
</main>

<?php include "../includes/footer.php"; ?>

</body>
</html>