<?php
session_start();
include '../web/admin/autoload/Database.php'; // Kết nối CSDL

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $MaSP = $_POST['MaSP'];
    $MaND = $_POST['MaND'];
    $SoSao = $_POST['SoSao'];
    $BinhLuan = trim($_POST['BinhLuan']);
    $NgayLap = date("Y-m-d H:i:s");

    if (!empty($MaSP) && !empty($MaND) && !empty($SoSao) && !empty($BinhLuan)) {
        // Kiểm tra xem người dùng đã mua sản phẩm này chưa & đơn hàng đã giao hay chưa
        $sql_check_purchase = "SELECT COUNT(*) as total FROM chitietdonhang ct 
                               JOIN donhang dh ON ct.MaDH = dh.MaDH 
                               WHERE dh.MaND = ? AND ct.MaSP = ? AND dh.TrangThai = 'Đã giao'";
        $stmt_check = $conn->prepare($sql_check_purchase);
        $stmt_check->bind_param("ii", $MaND, $MaSP);
        $stmt_check->execute();
        $result = $stmt_check->get_result();
        $row = $result->fetch_assoc();
        
        if ($row['total'] > 0) { // Nếu người dùng đã mua và nhận hàng
            // Thêm đánh giá vào CSDL
            $sql_insert = "INSERT INTO danhgia (MaSP, MaND, SoSao, BinhLuan, NgayLap) VALUES (?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("iiiss", $MaSP, $MaND, $SoSao, $BinhLuan, $NgayLap);
            
            if ($stmt_insert->execute()) {
                echo "<script>
                    alert('Đánh giá của bạn đã được gửi!');
                    window.history.back();
                </script>";
            } else {
                echo "<script>
                    alert('Lỗi: " . $conn->error . "');
                    window.history.back();
                </script>";
            }
            $stmt_insert->close();
        } else {
            echo "<script>
                alert('Bạn chỉ có thể đánh giá sản phẩm sau khi đã nhận hàng!');
                window.history.back();
            </script>";
        }
        $stmt_check->close();
    } else {
        echo "<script>
            alert('Vui lòng điền đầy đủ thông tin!');
            window.history.back();
        </script>";
    }
} else {
    echo "<script>
        alert('Yêu cầu không hợp lệ!');
        window.history.back();
    </script>";
}
?>
