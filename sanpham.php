<?php
session_start();
include '../web/admin/autoload/Database.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra nếu người dùng đã đăng nhập
$MaND = isset($_SESSION['MaND']) ? $_SESSION['MaND'] : 0;

// Kiểm tra nếu kết nối còn hoạt động
if (!isset($conn) || !$conn instanceof mysqli) {
    error_log("Lỗi: Kết nối MySQL không hợp lệ hoặc đã bị đóng.");
    exit;
}

// Đếm số sản phẩm trong giỏ hàng
$sqlCount = "SELECT COUNT(*) AS item_count FROM giohang WHERE MaND = ? AND (trangthai IS NULL OR trangthai = '')";
$stmtCount = $conn->prepare($sqlCount);
$itemCount = 0;

if ($stmtCount) {
    $stmtCount->bind_param("i", $MaND);
    $stmtCount->execute();
    $resultCount = $stmtCount->get_result();
    if ($resultCount->num_rows > 0) {
        $itemCount = $resultCount->fetch_assoc()['item_count'];
    }
    $stmtCount->close();
}

// Cấu hình phân trang
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Xử lý sắp xếp sản phẩm
$sort_option = isset($_GET['sort']) ? $_GET['sort'] : '';
$order_by = match ($sort_option) {
    'phobien' => "SoDanhGia DESC",
    'moinhat' => "MaSP DESC",
    'banchay' => "SoLuong DESC",
    'cao' => "GiaSauKM DESC",
    'thap' => "GiaSauKM ASC",
    default => "MaSP DESC"
};

// Xử lý lọc sản phẩm theo danh mục và tên
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$search_name = isset($_GET['search']) ? trim($_GET['search']) : '';

$where_clause = " WHERE (TrangThai IS NULL OR TrangThai = '')";
$params = [];
$types = "";

// Thêm điều kiện danh mục
if ($category_id > 0) {
    $where_clause .= " AND MaLoaiSP = ?";
    $params[] = $category_id;
    $types .= "i";
}

// Thêm điều kiện tìm kiếm theo tên
if (!empty($search_name)) {
    $where_clause .= " AND TenSP LIKE ?";
    $params[] = "%$search_name%";
    $types .= "s";
}

// Đếm tổng số sản phẩm
$sql_count = "SELECT COUNT(*) AS total FROM sanpham $where_clause";
$stmt_count = $conn->prepare($sql_count);
if ($stmt_count) {
    if (!empty($params)) {
        $stmt_count->bind_param($types, ...$params);
    }
    $stmt_count->execute();
    $result_count = $stmt_count->get_result();
    $total_products = $result_count->fetch_assoc()['total'];
    $stmt_count->close();
} else {
    $total_products = 0;
}

$total_pages = ceil($total_products / $limit);

// Truy vấn sản phẩm theo bộ lọc
$sql_sanpham = "SELECT * FROM sanpham $where_clause ORDER BY $order_by LIMIT ? OFFSET ?";
$stmt_sanpham = $conn->prepare($sql_sanpham);

if ($stmt_sanpham) {
    $params[] = $limit;
    $params[] = $offset;
    $types .= "ii";
    $stmt_sanpham->bind_param($types, ...$params);
    $stmt_sanpham->execute();
    $result_sanpham = $stmt_sanpham->get_result();
    $stmt_sanpham->close();
} else {
    $result_sanpham = false;
}

// Truy vấn danh mục sản phẩm
$sql_loaisanpham = "SELECT * FROM loaisanpham";
$result_loaisanpham = $conn->query($sql_loaisanpham);
?>



<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-commerce UI</title>
    <link rel="stylesheet" href="/HMC/web/public/client/css/sanpham.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="/HMC/web/public/client/css/header_footer.css">
    
</head>

<style>
    .product-img img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        /* Đảm bảo ảnh không bị vỡ */
    }

    .pagination {
        margin-left: auto;
        /* Đẩy về bên phải */
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .pagination span {
        font-weight: bold;
    }

    .pagination a,
    .pagination span.disabled {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border: 1px solid #ccc;
        text-decoration: none;
        color: #333;
        border-radius: 5px;
        font-size: 18px;
    }

    .pagination a:hover {
        background-color: #ddd;
    }

    .pagination span.disabled {
        color: #aaa;
        border-color: #ddd;
        cursor: not-allowed;
    }
</style>

<body>
    <!-- header_menu -->
    <?php include 'header.php'; ?>


    <!-- main -->
    <main>
        <div>
            <aside class="sidebar" id="sidebar">

                <ul>
                    <?php
                    // Lấy danh mục từ bảng loaisanpham
                    $sql_loaisanpham = "SELECT * FROM loaisanpham";
                    $result_loaisanpham = $conn->query($sql_loaisanpham);
                    echo "<ul>";
                    echo "<li><a href='?category=0'> ☰ Tất cả danh mục</a></li>";
                    if ($result_loaisanpham->num_rows > 0) {
                        while ($row = $result_loaisanpham->fetch_assoc()) {
                            $active_class = ($category_id == $row['MaLoaiSP']) ? "style='font-weight:bold; color:red;'" : "";
                            echo "<li><a href='?category=" . $row['MaLoaiSP'] . "' $active_class>" . $row['TenLoaiSP'] . "</a></li>";
                        }
                    }
                    echo "</ul>";
                    ?>
                </ul>
            </aside>
        </div>
        <section class="product-section" id="productSection">
            <div class="sort-bar">
                <span>Sắp xếp theo:</span>
                <button onclick="sortProducts('phobien')">Phổ biến</button>
                <button onclick="sortProducts('moinhat')">Mới nhất</button>
                <button onclick="sortProducts('banchay')">Bán chạy</button>
                <select onchange="sortProducts(this.value)">
                    <option value="">Giá</option>
                    <option value="cao">Từ cao - thấp</option>
                    <option value="thap">Từ thấp - cao</option>
                </select>

                <div class="pagination">
                    <span><?= $page ?> / <?= $total_pages ?></span>
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>&sort=<?= $sort_option ?>" class="prev">‹</a>
                    <?php else: ?>
                        <span class="disabled">‹</span>
                    <?php endif; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?= $page + 1 ?>&sort=<?= $sort_option ?>" class="next">›</a>
                    <?php else: ?>
                        <span class="disabled">›</span>
                    <?php endif; ?>
                </div>
            </div>
            <script>
                function sortProducts(option) {
                    window.location.href = "?sort=" + option;
                }
            </script>
            <div class="product-grid">
                <?php
                // Hiển thị sản phẩm từ cơ sở dữ liệu
                if ($result_sanpham->num_rows > 0) {
                    while ($row = $result_sanpham->fetch_assoc()) {
                        // Hiển thị từng sản phẩm
                        echo "<div class='product'>";
                        echo "<a href='chitietsanpham.php?id=" . $row['MaSP'] . "' style='text-decoration: none; color: inherit;'>";
                        echo "<div class='product-img'><img src='" . $row['HinhAnh'] . "' alt='" . $row['TenSP'] . "'></div>";
                        echo "<div class='product-info'>";
                        echo "<h3>" . $row['TenSP'] . "</h3>";
                        echo "<p class='discount-price'>" . number_format($row['DonGia'], 0, ',', '.') . " VND</p>";
                        // Hiển thị phần trăm giảm giá màu đỏ
                        echo "<p class='discount-percent' style='color: red;'>Giảm giá: " . $row['KM'] . "%</p>";
                        echo "<p class='price'>" . number_format($row['GiaSauKM'], 0, ',', '.') . " VND</p>";

                        echo "<div class='clearfix'></div>";
                        // echo "<p class='quantity'>Số lượng: " . $row['SoLuong'] . "</p>";
                        // echo "<p class='rating'>⭐ " . $row['SoSao'] . " (" . $row['SoDanhGia'] . " đánh giá)</p>";
                        echo "<p class='rating'>SL: " . $row['SoLuong'] . "   " . "⭐ " . $row['SoSao'] . " (" . $row['SoDanhGia'] . " đánh giá)</p>";
                        echo "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>Không có sản phẩm nào.</p>";
                }
                ?>
            </div>
        </section>
    </main>
    <script>
        function sortProducts(option) {
            const urlParams = new URLSearchParams(window.location.search);
            const category = urlParams.get('category') || '0';
            window.location.href = "?sort=" + option + "&category=" + category;
        }
    </script>


</body>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/HMC/web/footer.php'); ?>
</footer>

</html>