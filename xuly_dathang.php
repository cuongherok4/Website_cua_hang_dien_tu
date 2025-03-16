<?php
session_start();
include '../web/admin/autoload/Database.php';

if (!isset($_SESSION['MaND'])) {
    header('Location: ../web/login/login.php');
    exit();
}

$MaND = $_SESSION['MaND'];

if (!isset($_SESSION['donhang'])) {
    echo "Không có sản phẩm trong đơn hàng!";
    exit();
}

$donhang = $_SESSION['donhang'];
$products = $donhang['products'];
$TotalAmount = $donhang['TotalAmount'];
//  $NguoiNhan = $donhang['NguoiNhan'];
// $DiaChi = $donhang['DiaChi'];
//  $SDT = $donhang['SDT'];
// $PhuongThucTT = $donhang['PhuongThucTT'];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy thông tin từ form
    $NguoiNhan = $_POST['NguoiNhan'];
    $DiaChi = $_POST['DiaChi'];
    $SDT = $_POST['SDT'];
    $PhuongThucTT = $_POST['PhuongThucTT'];
} else {
    echo "Dữ liệu không hợp lệ!";
    exit();
}


$conn->begin_transaction();
try {
    // 1️⃣ Lưu thông tin đơn hàng vào bảng DonHang
    $sqlDonHang = "INSERT INTO DonHang (MaND, NgayLap, NguoiNhan, SDT, DiaChi, PhuongThucTT, TongTien, TrangThai) 
               VALUES (?, NOW(), ?, ?, ?, ?, ?, 'Chờ xác nhận')";
    $stmt = $conn->prepare($sqlDonHang);
    $stmt->bind_param("issssd", $MaND, $NguoiNhan, $SDT, $DiaChi, $PhuongThucTT, $TotalAmount);

    // Thực thi câu lệnh và kiểm tra lỗi
    if (!$stmt->execute()) {
        die("Lỗi khi thêm đơn hàng: " . $stmt->error);
    }

    // Lấy ID đơn hàng vừa tạo
    $MaDH = $conn->insert_id;


    // 2️⃣ Lưu thông tin sản phẩm vào bảng ChiTietDonHang
    $sqlChiTiet = "INSERT INTO ChiTietDonHang (MaDH, MaSP, SoLuong, DonGia) VALUES (?, ?, ?, ?)";
    $stmtChiTiet = $conn->prepare($sqlChiTiet);

    $sqlLayMaSP = "SELECT MaSP FROM GioHang WHERE MaND = ? AND MaGH = ?";
    $stmtLayMaSP = $conn->prepare($sqlLayMaSP);

    foreach ($products as $product) {
        $MaGH = $product["MaGH"]; // Lấy MaGH từ giỏ hàng
        $SoLuong = $product["SoLuong"];
        $DonGia = $product["DonGia"];

        // Truy vấn để lấy MaSP từ bảng GioHang
        $stmtLayMaSP->bind_param("ii", $MaND, $MaGH);
        $stmtLayMaSP->execute();
        $result = $stmtLayMaSP->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            $MaSP = $row["MaSP"];
            $stmtChiTiet->bind_param("iiid", $MaDH, $MaSP, $SoLuong, $DonGia);
            $stmtChiTiet->execute();
        }
    }


    // 3️⃣ Chỉ xóa sản phẩm đã đặt trong giỏ hàng
    $sqlXoaSanPham = "DELETE FROM GioHang WHERE MaND = ? AND MaGH = ?";
    $stmtXoaSanPham = $conn->prepare($sqlXoaSanPham);

    foreach ($products as $product) {
        $MaSP = $product["MaGH"]; // MaGH là ID sản phẩm trong giỏ hàng
        $stmtXoaSanPham->bind_param("ii", $MaND, $MaSP);
        $stmtXoaSanPham->execute();
    }


    // Hoàn tất giao dịch
    $conn->commit();

    // Xóa session đơn hàng sau khi lưu xong
    unset($_SESSION['donhang']);

    // if($PhuongThucTT=="Thanh toán bằng Momo"){
    //     header("/HMC/web/thanhtoan/xulythanhtoan_momo.php");
    //     exit();
    // }else if($PhuongThucTT=="Thanh toán bằng ATM"){
    //     header("/HMC/web/thanhtoan/xulythanhtoan_atm.php");
    //     exit();
    // }else{
    //     header("Location:donmua.php?MaDH=" . $MaDH);
    //     exit();
    // }
    // Chuyển hướng đến trang xác nhận đơn hàng
    header("Location:donmua.php?MaDH=" . $MaDH);
        exit();
} catch (Exception $e) {
    $conn->rollback();
    echo "Lỗi đặt hàng: " . $e->getMessage();
}
?>