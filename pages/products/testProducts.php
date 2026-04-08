<?php
include "../includes/config.php";

$sql = "SELECT * FROM products";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Lỗi SQL: " . mysqli_error($conn));
}
echo "<h1>Danh sách sản phẩm</h1>";
while ($row = mysqli_fetch_assoc($result)) {
?>
    <div style="border:1px solid #ccc; margin:10px; padding:10px; width:300px;">
        <h3><?php echo $row['name']; ?></h3>
        <p>Tác giả: <?php echo $row['author']; ?></p>
        <p>Giá: <?php echo $row['price']; ?> VNĐ</p>
        <img src="../../img/<?php echo $row['image']; ?>" width="150">

    </div>

<?php
}
?>