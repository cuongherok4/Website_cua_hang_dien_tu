<?php
session_start();
include '../web/admin/autoload/Database.php';

if (!isset($_SESSION['MaND'])) {
    header('Location: ../login/login.php');
    exit();
}

if (isset($_GET['madh'])) {
    $MaDH = $_GET['madh'];

    // Kiểm tra trạng thái hiện tại của đơn hàng
    $sql_check = "SELECT TrangThai FROM donhang WHERE MaDH = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $MaDH);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $row = $result_check->fetch_assoc();
        if ($row['TrangThai'] == 'Chờ xác nhận') {
            // Cập nhật trạng thái đơn hàng thành 'Đã hủy'
            $sql_update = "UPDATE donhang SET TrangThai = 'Đã hủy' WHERE MaDH = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("i", $MaDH);

            if ($stmt_update->execute()) {
                $_SESSION['success'] = "Đơn hàng đã được hủy thành công.";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra khi hủy đơn hàng.";
            }
        } else {
            $_SESSION['error'] = "Chỉ có thể hủy đơn hàng đang ở trạng thái 'Vận chuyển'.";
        }
    } else {
        $_SESSION['error'] = "Không tìm thấy đơn hàng.";
    }
} else {
    $_SESSION['error'] = "Mã đơn hàng không hợp lệ.";
}

header("Location: donmua.php");
exit();
