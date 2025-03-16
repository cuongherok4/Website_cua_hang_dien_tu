<?php
include '../web/admin/autoload/Database.php';

// Kiểm tra session
// Kiểm tra nếu người dùng đã đăng nhập
$MaND = isset($_SESSION['MaND']) ? $_SESSION['MaND'] : 0;

// Kiểm tra kết nối MySQL
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

// Truy vấn danh mục sản phẩm
$sql_loaisanpham = "SELECT * FROM loaisanpham";
$result_loaisanpham = $conn->query($sql_loaisanpham);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="/HMC/web/public/client/css/header_footer.css">
</head>
<style>
    .menu {
        display: flex;
        gap: 15px;
        position: relative;
        z-index: 1000;
    }

    .search-box {
            width: 730px;
            display: flex;
            align-items: center;
            flex: 1;
            position: relative;
            margin: 0 15px;
        }

        .search-box input {
            width: 100%;
            padding: 10px 15px;
            border-radius: 25px;
            border: none;
            font-size: 16px;
        }

        .search-box button {
            position: absolute;
            right: 10px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            color: gray;
        }
   
</style>
<body>
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
        <form action="sanpham.php" method="GET">
            <div class="search-box">

                <input type="text" name="search" placeholder="Tìm kiếm sản phẩm..." required>
                <button type="submit"><i class="fas fa-search"></i></button>
            </div>
        </form>



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
</body>

</html>