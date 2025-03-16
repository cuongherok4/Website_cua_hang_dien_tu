<?php
session_start();
include '../web/admin/autoload/Database.php'; // Kết nối CSDL

// Kiểm tra session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra nếu người dùng đã đăng nhập
$MaND = isset($_SESSION['MaND']) ? $_SESSION['MaND'] : 0;

// Kiểm tra nếu kết nối còn hoạt động
if (isset($conn) && $conn instanceof mysqli) {
    // Truy vấn để đếm số lượng sản phẩm trong giỏ hàng khi trạng thái rỗng
    $sqlCount = "SELECT COUNT(*) AS item_count FROM giohang WHERE MaND = ? AND (trangthai IS NULL OR trangthai = '')";
    $stmtCount = $conn->prepare($sqlCount);

    if ($stmtCount) {
        $stmtCount->bind_param("i", $MaND);
        $stmtCount->execute();
        $resultCount = $stmtCount->get_result();
        $itemCount = 0;
        if ($resultCount->num_rows > 0) {
            $itemCount = $resultCount->fetch_assoc()['item_count'];
        }
        $stmtCount->close();
    } else {
        error_log("Lỗi prepare: " . $conn->error);
    }
} else {
    error_log("Lỗi: Kết nối MySQL không hợp lệ hoặc đã bị đóng.");
}
// Kiểm tra kết nối cơ sở dữ liệu
if (!$conn) {
    die("Lỗi kết nối: " . mysqli_connect_error());
}

// Kiểm tra tham số id
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Không tìm thấy sản phẩm.");
}
$maSP = $_GET['id'];

// Lấy thông tin sản phẩm
$sql = "SELECT * FROM sanpham WHERE MaSP = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $maSP);
$stmt->execute();
$result = $stmt->get_result();

// Kiểm tra nếu không có sản phẩm nào được trả về
if ($result->num_rows == 0) {
    die("Sản phẩm với mã '$maSP' không tồn tại.");
}
$row = $result->fetch_assoc();

// Lấy tên loại sản phẩm từ bảng loaisanpham
$tenLoaiSP = "Không xác định"; // Giá trị mặc định nếu không tìm thấy
$sql_loai = "SELECT TenLoaiSP FROM loaisanpham WHERE MaLoaiSP = ?";
$stmt_loai = $conn->prepare($sql_loai);
$stmt_loai->bind_param("s", $row['MaLoaiSP']);
$stmt_loai->execute();
$result_loai = $stmt_loai->get_result();

if ($result_loai->num_rows > 0) {
    $row_loai = $result_loai->fetch_assoc();
    $tenLoaiSP = $row_loai['TenLoaiSP'];
}

// Lấy danh sách đánh giá cho sản phẩm
$sql_danhgia = "SELECT dg.SoSao, dg.BinhLuan, dg.NgayLap, nd.   HoTen 
                FROM danhgia dg 
                JOIN nguoidung nd ON dg.MaND = nd.MaND 
                WHERE dg.MaSP = ? 
                ORDER BY dg.NgayLap DESC";

$stmt_danhgia = $conn->prepare($sql_danhgia);
$stmt_danhgia->bind_param("i", $maSP);
$stmt_danhgia->execute();
$result_danhgia = $stmt_danhgia->get_result();

// 
// Lấy số sao mặc định của sản phẩm từ bảng sanpham
$sql_default = "SELECT SoSao FROM sanpham WHERE MaSP = ?";
$stmt_default = $conn->prepare($sql_default);
$stmt_default->bind_param("i", $maSP);
$stmt_default->execute();
$result_default = $stmt_default->get_result();
$row_default = $result_default->fetch_assoc();
$soSaoMacDinh = $row_default['SoSao']; // Lưu số sao gốc của sản phẩm

// Tính toán số sao trung bình và số đánh giá từ bảng danhgia
$sql_avg = "SELECT AVG(SoSao) AS TrungBinhSao, COUNT(*) AS SoDanhGia FROM danhgia WHERE MaSP = ?";
$stmt_avg = $conn->prepare($sql_avg);
$stmt_avg->bind_param("i", $maSP);
$stmt_avg->execute();
$result_avg = $stmt_avg->get_result();
$row_avg = $result_avg->fetch_assoc();

$soDanhGia = $row_avg['SoDanhGia'];

// Nếu không có đánh giá, giữ nguyên số sao mặc định của sản phẩm
if ($soDanhGia == 0) {
    $trungBinhSao = $soSaoMacDinh;
} else {
    $trungBinhSao = round($row_avg['TrungBinhSao'], 1); // Làm tròn 1 chữ số thập phân
}

// Cập nhật số sao trung bình và số đánh giá vào bảng sản phẩm
$sql_update = "UPDATE sanpham SET SoSao = ?, SoDanhGia = ? WHERE MaSP = ?";
$stmt_update = $conn->prepare($sql_update);
$stmt_update->bind_param("dii", $trungBinhSao, $soDanhGia, $maSP);
$stmt_update->execute();

// Giải phóng tài nguyên
$stmt_default->close();
$stmt_avg->close();
$stmt_update->close();

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Sản Phẩm</title>
    <link rel="stylesheet" href="/HMC/web/public/client/css/header_footer.css">
    <link rel="stylesheet" href="/HMC/web/public/client/css/ctsp.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<style>
    .review-section {
    width: 100%;
    /* max-width: 700px; */
    margin: 20px auto;
    padding: 20px;
    background: #f9f9f9;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.review-form {
    padding: 15px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
}

.review-form h3 {
    text-align: center;
    color: #333;
    font-size: 20px;
}

.review-form label {
    font-weight: bold;
    display: block;
    margin-top: 10px;
    color: #555;
}

.review-form select, 
.review-form textarea {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 14px;
}

.review-form button {
    width: 100%;
    margin-top: 15px;
    padding: 10px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    transition: 0.3s;
}

.review-form button:hover {
    background: #0056b3;
}

.review-section p {
    text-align: center;
    font-size: 14px;
    margin-top: 10px;
}

.review-section a {
    color: #007bff;
    font-weight: bold;
    text-decoration: none;
}

.review-section a:hover {
    text-decoration: underline;
}


.review-section2 {
    width: 100%;
    margin: 30px auto;
    padding: 20px;
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.review-section2 h3 {
    text-align: center;
    font-size: 22px;
    color: #333;
    margin-bottom: 15px;
    border-bottom: 2px solid #007bff;
    display: inline-block;
    padding-bottom: 5px;
}

.customer-reviews {
    margin-top: 10px;
    max-height: 400px;
    overflow-y: auto;
    padding-right: 10px;
}

.review {
    background: #f9f9f9;
    padding: 15px;
    margin-bottom: 10px;
    border-radius: 8px;
    box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
}

.review p {
    margin: 5px 0;
    font-size: 14px;
    color: #555;
}

.review strong {
    color: #007bff;
}

.review-section2 p {
    text-align: left;
    font-size: 14px;
    color: #777;
}

.review-section2 p a {
    color: #007bff;
    font-weight: bold;
    text-decoration: none;
}

.review-section2 p a:hover {
    text-decoration: underline;
}

/* Thanh cuộn đẹp hơn cho phần đánh giá */
.customer-reviews::-webkit-scrollbar {
    width: 5px;
}

.customer-reviews::-webkit-scrollbar-thumb {
    background-color: #007bff;
    border-radius: 5px;
}

</style>
<body>
    <script src="/HMC/web/public/client/js/ctsp_soluongsao.js"></script>
    <script src="/HMC/web/public/client/js/them_giohang.js"></script>

    <div class="navbar">
        <a href="index.php" class="logo" style="text-decoration: none;">LOGO</a>


        <select class="category">
            <option>Tất cả</option>
            <?php
            // Hiển thị các danh mục từ bảng loaisanpham
            
            if ($result_loaisanpham->num_rows > 0) {
                while ($row = $result_loaisanpham->fetch_assoc()) {
                    echo "<option value='" . $row['MaLoaiSP'] . "'>" . $row['TenLoaiSP'] . "</option>";
                }
            } else {
                echo "<option>Không có danh mục nào</option>";
            }
            ?>
        </select>
        <div class="search-box">
            <form action="sanpham.php" method="GET">
                <input type="text" name="search" placeholder="Tìm kiếm sản phẩm..." required>
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>


        <div class="menu">
            <div class="icon">
                <i class="fas fa-shopping-cart"></i><a href="./giohang.php">Giỏ hàng</a>
                <span class="badge" id="cartBadge"><?= $itemCount ?></span>
            </div>
            <div class="icon">
                <i class="fas fa-bell"></i><a href="#">Thông báo</a>
                <span class="badge">0</span>
            </div>
            <div class="icon account">
                <i class="fas fa-user"></i><a href="#">Tài khoản</a>
                <div class="dropdown-menu">
                    <a href="../web/thongtincanhan.php">Thông tin cá nhân</a>
                    <a href="../web/login/login.php">Đăng nhập</a>
                    <a href="../web/login/logout.php">Đăng xuất</a>

                </div>
            </div>
        </div>
    </div>

    <!-- Tab Menu -->
    <div class="tab-menu">
        <a href="index.php">Trang chủ</a>
        <a href="./sanpham.php">Sản phẩm</a>
        <a href="./tintuc.php">Tin tức</a>
        <a href="./lienhe.php">Liên hệ</a>
        <a href="./donmua.php">Đơn mua</a>
    </div>

    <!-- Chi tiết sản phẩm -->
    <div class="product-container">
        <div class="product-detail">
            <div class="product-image">
                <img src="<?php echo htmlspecialchars($row['HinhAnh']); ?>"
                    alt="<?php echo htmlspecialchars($row['TenSP']); ?>">
            </div>
            <div class="product-info">
                <h2><?php echo htmlspecialchars($row['TenSP']); ?></h2>
                <p><strong>Loại:</strong> <?php echo htmlspecialchars($tenLoaiSP); ?></p>
                <p class="price"><strong>Đơn giá:</strong> <?php echo number_format($row['DonGia'], 0, ',', '.'); ?> VND
                </p>
                <p><strong>Số lượng:</strong></p>
                <div class="quantity">
                    <button class="minus">-</button>
                    <input type="number" value="1" min="1">
                    <button class="plus">+</button>
                </div>
                <p><strong>Số lượng trong kho:</strong> <?php echo htmlspecialchars($row['SoLuong']); ?></p>
                <p><strong>Số sao:</strong>
                    <?php
                    for ($i = 1; $i <= floor($row['SoSao']); $i++) {
                        echo "⭐";
                    }
                    if ($row['SoSao'] - floor($row['SoSao']) >= 0.5) {
                        echo "⭐"; // Nửa sao
                    }
                    ?>
                    (<?php echo $row['SoSao']; ?>)
                </p>
                <p><strong>Số đánh giá:</strong> <?php echo htmlspecialchars($row['SoDanhGia']); ?></p>
                <p><strong>Khuyến mãi:</strong> Giảm <?php echo htmlspecialchars($row['KM']); ?>%</p>
                <p><strong>Giá sau khuyến mãi:</strong> <?php echo number_format($row['GiaSauKM'], 0, ',', '.'); ?> VND
                </p>
                <button class="btn" id="addToCartBtn">Thêm vào giỏ hàng</button>
                <button class="btn buy-now">Mua Ngay</button>
            </div>

            <!-- Mô tả sản phẩm -->
            <div class="product-description_mota">
                <h3>Mô tả sản phẩm</h3>
                <p>
                    <?php echo !empty($row['MoTa']) ? nl2br(htmlspecialchars($row['MoTa'])) : "Chưa có mô tả cho sản phẩm này."; ?>
                </p>
            </div>
        </div>

        <!-- Phần đánh giá -->
        <div class="review-section">
            <?php if ($MaND != 0) { ?>
                <div class="review-form">
                    <h3>Gửi đánh giá của bạn</h3>
                    <form action="them_danhgia.php" method="POST">
                        <input type="hidden" name="MaSP" value="<?= htmlspecialchars($maSP) ?>">
                        <input type="hidden" name="MaND" value="<?= htmlspecialchars($MaND) ?>">
                        <label for="SoSao">Số sao:</label>
                        <select name="SoSao" required>
                            <option value="1">1 sao</option>
                            <option value="2">2 sao</option>
                            <option value="3">3 sao</option>
                            <option value="4">4 sao</option>
                            <option value="5">5 sao</option>
                        </select>
                        <br>
                        <label for="BinhLuan">Nhận xét:</label>
                        <textarea name="BinhLuan" required></textarea>
                        <br>
                        <button type="submit">Gửi đánh giá</button>
                    </form>
                </div>
            <?php } else { ?>
                <p>Vui lòng <a href="../web/login/login.php">đăng nhập</a> để đánh giá sản phẩm.</p>
            <?php } ?>
        </div>

        <div class="review-section2">
            <h3>Đánh giá từ khách hàng</h3>
            <?php if ($result_danhgia->num_rows > 0) { ?>
                <div class="customer-reviews">
                    <?php while ($row_dg = $result_danhgia->fetch_assoc()) { ?>
                        <div class="review">
                            <p><strong><?php echo htmlspecialchars($row_dg['HoTen']); ?></strong> -
                                <?php echo date("d/m/Y H:i", strtotime($row_dg['NgayLap'])); ?>
                            </p>
                            <p>- ⭐ <?php echo $row_dg['SoSao']; ?>/5: <?php echo htmlspecialchars($row_dg['BinhLuan']); ?></p>
                        </div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <p>Chưa có đánh giá nào cho sản phẩm này.</p>
            <?php } ?>
        </div>
    </div>

</body>

<!-- Footer -->
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/HMC/web/footer.php'); ?>
</footer>

</html>

<?php
// Đóng statement và kết nối
$stmt->close();
$stmt_loai->close();
$conn->close();
?>