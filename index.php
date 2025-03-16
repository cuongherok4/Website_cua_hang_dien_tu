<?php
session_start();
include '../web/admin/autoload/Database.php';

// Số sản phẩm hiển thị trên mỗi trang
$so_san_pham_moi_trang = 12;

// Xác định trang hiện tại (mặc định là trang 1 nếu không có `page` trên URL)
$trang_hien_tai = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$bat_dau = ($trang_hien_tai - 1) * $so_san_pham_moi_trang;

// Lấy tổng số sản phẩm để tính số trang
$sql_count = "SELECT COUNT(*) AS total FROM sanpham WHERE TrangThai IS NULL OR TrangThai = ''";
$result_count = $conn->query($sql_count);
$row_count = $result_count->fetch_assoc();
$tong_san_pham = $row_count['total'];

// Tính tổng số trang
$tong_trang = ceil($tong_san_pham / $so_san_pham_moi_trang);

// Truy vấn sản phẩm theo trang
$sql_sanpham = "SELECT * FROM sanpham WHERE TrangThai IS NULL OR TrangThai = '' ORDER BY MaSP DESC LIMIT $bat_dau, $so_san_pham_moi_trang";
$result_sanpham = $conn->query($sql_sanpham);



?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh tìm kiếm</title>

    <!-- Liên kết tới file CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Liên kết tới file CSS -->
    <link rel="stylesheet" href="/HMC/web/public/client/css/imgsanpham.css">
    <link rel="stylesheet" href="/HMC/web/public/client/css/trangchu.css">
    <link rel="stylesheet" href="/HMC/web/public/client/css/header_footer.css">

</head>
<style>
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 20px 0;
    }

    .pagination a,
    .pagination span {
        text-decoration: none;
        padding: 10px 15px;
        margin: 0 5px;
        border: 1px solid #ddd;
        color: #333;
        font-weight: bold;
        border-radius: 5px;
        transition: background 0.3s, color 0.3s;
    }

    .pagination a:hover {
        background: #f8b400;
        color: white;
    }

    .pagination a.active {
        background: #ff6600;
        color: white;
        border: 1px solid #ff6600;
    }

    .pagination span {
        padding: 10px 15px;
        color: #999;
        font-weight: bold;
    }
</style>

<body>
    <!-- header_menu -->
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/HMC/web/header.php'); ?>

    <!-- Image Slider -->
    <div class="slider">
        <div class="slides">
            <img src="https://cdn2.fptshop.com.vn/unsafe/1920x0/filters:quality(100)/H2_614x212_8211be63a9.png" alt="Slide1">
            <img src="https://cdn2.fptshop.com.vn/unsafe/1920x0/filters:quality(100)/H2_614x212_d0d453bfe4.png" alt="Slide2">
            <img src="https://cdn2.fptshop.com.vn/unsafe/1920x0/filters:quality(100)/H2_614x212_5108a00a96.png" alt="Slide3">
            <img src="https://cdn2.fptshop.com.vn/unsafe/1920x0/filters:quality(100)/H2_614x212_5d676788c1.png" alt="Slide4">
            <img src="https://cdn2.fptshop.com.vn/unsafe/1920x0/filters:quality(100)/H2_614x212_01412c8785.png" alt="Slide5">
            <img src="https://cdn2.fptshop.com.vn/unsafe/1920x0/filters:quality(100)/H2_614x212_08fde2e1c9.png" alt="Slide6">

        </div>
        <button class="prev" onclick="prevSlide()">&#10094;</button>
        <button class="next" onclick="nextSlide()">&#10095;</button>
    </div>

    <!-- Sản phẩm nổi bật  -->
    <div class="featured-title">
        <h1>Sản phẩm nổi bật</h1>
    </div>
    


    <!-- Danh sách sản phẩm 12 sản phẩm  -->
    <div class="product-grid">
        <?php
        if ($result_sanpham->num_rows > 0) {
            while ($row = $result_sanpham->fetch_assoc()) {
                echo "<div class='product'>";
                echo "<a href='chitietsanpham.php?id=" . $row['MaSP'] . "' style='text-decoration: none; color: inherit;'>";
                echo "<div class='product-img'><img src='" . $row['HinhAnh'] . "' alt='" . $row['TenSP'] . "'></div>";
                echo "<div class='product-info'>";
                echo "<h3>" . $row['TenSP'] . "</h3>";
                echo "<p class='discount-price'>" . number_format($row['DonGia'], 0, ',', '.') . " VND</p>";
                echo "<p class='discount-percent' style='color: red;'>Giảm giá: " . $row['KM'] . "%</p>";
                echo "<p class='price'>" . number_format($row['GiaSauKM'], 0, ',', '.') . " VND</p>";
                echo "<p class='quantity'>Số lượng: " . $row['SoLuong'] . "</p>";
                echo "<p class='rating'>⭐ " . $row['SoSao'] . " (" . $row['SoDanhGia'] . " đánh giá)</p>";
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<p>Không có sản phẩm nào.</p>";
        }
        ?>
    </div>
    
   <div class="pagination">
    <!-- Nút "Trước" -->
    <?php if ($trang_hien_tai > 1) : ?>
        <a href="?page=<?= $trang_hien_tai - 1 ?>">&#10094; Trước</a>
    <?php endif; ?>

    <!-- Hiển thị số trang -->
    <?php 
    for ($i = 1; $i <= $tong_trang; $i++) : 
        if ($i == 1 || $i == $tong_trang || abs($i - $trang_hien_tai) <= 2) : ?>
            <a href="?page=<?= $i ?>" class="<?= ($i == $trang_hien_tai) ? 'active' : '' ?>"><?= $i ?></a>
        <?php elseif (($i == 2 && $trang_hien_tai > 4) || ($i == $tong_trang - 1 && $trang_hien_tai < $tong_trang - 3)) : ?>
            <span>...</span>
        <?php endif; ?>
    <?php endfor; ?>

    <!-- Nút "Tiếp" -->
    <?php if ($trang_hien_tai < $tong_trang) : ?>
        <a href="?page=<?= $trang_hien_tai + 1 ?>">Tiếp  &#10095;</a>
    <?php endif; ?>
</div>



    <script src="/HMC/web/public/client/js/chuyenanh.js"></script>
</body>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/HMC/web/footer.php'); ?>
</footer>

</html>