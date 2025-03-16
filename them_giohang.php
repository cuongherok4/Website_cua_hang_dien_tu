<?php
session_start();
include '../web/admin/autoload/Database.php';

if (!isset($_SESSION['MaND'])) {
    echo json_encode(["status" => "error", "message" => "Bạn cần đăng nhập để thêm vào giỏ hàng."]);
    exit();
}

$maSP = $_POST['maSP'];
$soLuong = $_POST['soLuong'];
$maND = $_SESSION['MaND']; // Lấy mã người dùng từ session

// Kiểm tra sản phẩm có tồn tại không
$sql_check = "SELECT DonGia FROM sanpham WHERE MaSP = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("i", $maSP);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows == 0) {
    echo json_encode(["status" => "error", "message" => "Sản phẩm không tồn tại."]);
    exit();
}

$row_check = $result_check->fetch_assoc();
$donGia = $row_check['DonGia'];

// Kiểm tra sản phẩm đã có trong giỏ hàng chưa
$sql_cart = "SELECT * FROM giohang WHERE MaSP = ? AND MaND = ?";
$stmt_cart = $conn->prepare($sql_cart);
$stmt_cart->bind_param("ii", $maSP, $maND);
$stmt_cart->execute();
$result_cart = $stmt_cart->get_result();

if ($result_cart->num_rows > 0) {
    // Nếu sản phẩm đã tồn tại, cập nhật số lượng
    $sql_update = "UPDATE giohang SET SoLuong = SoLuong + ? WHERE MaSP = ? AND MaND = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("iii", $soLuong, $maSP, $maND);
    $stmt_update->execute();
} else {
    // Nếu sản phẩm chưa có, thêm mới vào giỏ hàng
    $sql_insert = "INSERT INTO giohang (MaSP, MaND, SoLuong, DonGia) VALUES (?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("iiid", $maSP, $maND, $soLuong, $donGia);
    $stmt_insert->execute();
}

echo json_encode(["status" => "success", "message" => "Sản phẩm đã được thêm vào giỏ hàng."]);
?>
