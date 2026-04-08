
<?php
session_start();
include "../includes/config.php";

// ================= FILTER =================
$filter_month = $_GET['month'] ?? '';
$filter_year = $_GET['year'] ?? date('Y');

$where_filter = "YEAR(created_at) = '$filter_year'";
if (!empty($filter_month)) {
    $where_filter .= " AND MONTH(created_at) = '$filter_month'";
}

// ================= THỐNG KÊ =================

// 1. Tổng doanh thu
$sql_total_revenue = "SELECT SUM(total_price) as total 
FROM orders 
WHERE status IN ('Thành công', 'Đã hoàn thành') 
AND $where_filter";

$res_revenue = mysqli_query($conn, $sql_total_revenue);
$total_revenue = mysqli_fetch_assoc($res_revenue)['total'] ?? 0;

// 2. Tổng đơn
$sql_total_orders = "SELECT COUNT(*) as total FROM orders WHERE $where_filter";
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, $sql_total_orders))['total'];

// 3. Đơn hủy
$sql_cancel_orders = "SELECT COUNT(*) as total 
FROM orders 
WHERE status = 'Đã hủy' AND $where_filter";

$cancel_orders = mysqli_fetch_assoc(mysqli_query($conn, $sql_cancel_orders))['total'];

$chart_labels = [];
$chart_data = [];

for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $display_date = date('d/m', strtotime($date));
    
    $sql_day = "SELECT SUM(total_price) as daily_total 
                FROM orders 
                WHERE DATE(created_at) = '$date' 
                AND status NOT IN ('Đã hủy')
                AND $where_filter";

    $res_day = mysqli_query($conn, $sql_day);
    $val = mysqli_fetch_assoc($res_day)['daily_total'] ?? 0;

    $chart_labels[] = $display_date;
    $chart_data[] = (int)$val;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thống kê báo cáo - Sagewood Admin</title>
    <link rel="stylesheet" href="../../css/postProduct.css">
    <link rel="stylesheet" href="../../css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { margin: 0; padding: 0; background-color: #f9f6f2; }
        .admin-layout { display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: #fff; border-right: 1px solid #e0e0e0; }
        .sidebar a { display: flex; padding: 15px 25px; color: #555; text-decoration: none; }
        .sidebar a.active { background: #7A8F7A; color: #fff; }
        .content-area { flex-grow: 1; padding: 30px; }

        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 25px; margin-bottom: 40px; }
        .report-card { background: #fff; padding: 25px; border-radius: 15px; border-bottom: 4px solid #7A8F7A; }

        .chart-box { background: #fff; padding: 40px; border-radius: 20px; }

        .filter-box {
            margin-bottom: 25px;
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .filter-box select {
            padding: 8px 12px;
            border-radius: 6px;
        }

        .btn-filter {
            background: #2c3e50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<?php include "../includes/header.php"; ?>

<div class="admin-layout">

    <aside class="sidebar">
        <a href="dashboard.php"><i class="fa-solid fa-chart-line"></i> Tổng quan</a>
        <a href="postProduct.php"><i class="fa-solid fa-book-open"></i> Quản lý sách</a>
        <a href="managePost.php"><i class="fa-solid fa-pen-to-square"></i> Quản lý bài viết</a>
        <a href="order.php"><i class="fa-solid fa-box"></i> Quản lý đơn hàng</a>
        <a href="statistics.php" class="active"><i class="fa-solid fa-chart-pie"></i> Thống kê</a>
    </aside>

    <main class="content-area">

        <h1>Báo cáo doanh thu</h1>

        <!-- FILTER -->
        <form method="GET" class="filter-box">
            <select name="month">
                <option value="">-- Tháng --</option>
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?php echo $m; ?>" <?php if ($filter_month == $m) echo 'selected'; ?>>
                        Tháng <?php echo $m; ?>
                    </option>
                <?php endfor; ?>
            </select>

            <select name="year">
                <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
                    <option value="<?php echo $y; ?>" <?php if ($filter_year == $y) echo 'selected'; ?>>
                        <?php echo $y; ?>
                    </option>
                <?php endfor; ?>
            </select>

            <button type="submit" class="btn-filter">Lọc</button>
        </form>

        <!-- STATS -->
        <div class="stats-grid">
            <div class="report-card">
                <div>Doanh thu</div>
                <strong><?php echo number_format($total_revenue); ?>đ</strong>
            </div>
            <div class="report-card">
                <div>Tổng đơn</div>
                <strong><?php echo $total_orders; ?></strong>
            </div>
            <div class="report-card">
                <div>Đơn hủy</div>
                <strong><?php echo $cancel_orders; ?></strong>
            </div>
            <div class="report-card">
                <div>TB / đơn</div>
                <strong><?php echo ($total_orders > 0) ? number_format($total_revenue / $total_orders) : 0; ?>đ</strong>
            </div>
        </div>

        <!-- CHART -->
        <div class="chart-box">
            <canvas id="chart"></canvas>
        </div>

    </main>
</div>

<script>
const ctx = document.getElementById('chart').getContext('2d');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($chart_labels); ?>,
        datasets: [{
            label: 'Doanh thu',
            data: <?php echo json_encode($chart_data); ?>,
            borderWidth: 2,
            fill: true
        }]
    }
});
</script>

</body>
</html>
