<?php
session_start();
include 'Database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Nhận dữ liệu từ form
    $matintuc = $_POST['matt'] ?? null;
    $mand = $_POST['mand'] ?? '';
    $tieude = $_POST['tieude'] ?? '';
    $linklk = $_POST['link'] ?? ''; // Thêm trường Linklk
    $anhtintuc = $_POST['anhtt'] ?? ''; // Thêm trường ảnh tin tức
    $noidung = $_POST['noidung'] ?? '';

    

    // Xử lý cập nhật hoặc thêm mới
    if (!empty($matintuc)) {
        // Cập nhật tin tức
        $sql = "UPDATE tintuc 
                SET MaND=?, TieuDe=?, Linklk=?, AnhTinTuc=?, NoiDung=? 
                WHERE MaTinTuc=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssi", $mand, $tieude, $linklk, $anhtintuc, $noidung, $matintuc);
    } else {
        // Thêm mới tin tức
        $sql = "INSERT INTO tintuc (MaND, TieuDe, Linklk, AnhTinTuc, NoiDung) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issss", $mand, $tieude, $linklk, $anhtintuc, $noidung);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = "Lưu tin tức thành công!";
    } else {
        $_SESSION['error'] = "Lỗi: " . $conn->error;
    }

    // Chuyển hướng về trang quản lý tin tức
    header("Location: /HMC/web/admin/tintuc.php");
    exit;
}

// Xử lý xóa tin tức
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $matintuc = $_GET['id'];

    // Kiểm tra xem tin tức có tồn tại không
    $check_sql = "SELECT * FROM tintuc WHERE MaTinTuc = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("i", $matintuc);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Nếu tồn tại, thực hiện xóa
        $delete_sql = "DELETE FROM tintuc WHERE MaTinTuc = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $matintuc);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Xóa tin tức thành công!";
        } else {
            $_SESSION['error'] = "Lỗi khi xóa!";
        }
    } else {
        $_SESSION['error'] = "Tin tức không tồn tại!";
    }

    header("Location: /HMC/web/admin/tintuc.php");
    exit;
}

$conn->close();
?>
