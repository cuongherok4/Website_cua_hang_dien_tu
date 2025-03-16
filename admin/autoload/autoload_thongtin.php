<?php
session_start();  // Khởi tạo phiên làm việc (session) để lưu trữ thông tin tạm thời cho người dùng
include 'Database.php';  // Kết nối với cơ sở dữ liệu, bao gồm các tham số kết nối và các hàm truy vấn

// Kiểm tra nếu phương thức HTTP là POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Nhận dữ liệu từ form thông qua phương thức POST
    $malh = $_POST['malh'] ?? null;  // Mã liên hệ, nếu không có sẽ gán null
    $mand = $_POST['mand'] ?? '';  // Mã người dùng, nếu không có sẽ gán chuỗi rỗng
    $sodienthoai = $_POST['sdt'] ?? '';  // Số điện thoại
    $email = $_POST['email'] ?? '';  // Email
    $diachi = $_POST['diachi'] ?? '';  // Địa chỉ

    // Kiểm tra dữ liệu đầu vào, nếu có trường nào trống sẽ thông báo lỗi và dừng lại
    if (empty($mand) || empty($sodienthoai) || empty($email) || empty($diachi)) {
        $_SESSION['error'] = "Vui lòng nhập đầy đủ thông tin!";  // Lưu thông báo lỗi vào session
        header("Location: /HMC/web/admin/thongtin.php");  // Chuyển hướng người dùng về trang quản lý thông tin
        exit;  // Dừng mã thực thi
    }

    // Kiểm tra nếu có mã liên hệ (malh) tức là đang cập nhật thông tin liên hệ
    if (!empty($malh)) {
        // Cập nhật thông tin liên hệ trong cơ sở dữ liệu
        $sql = "UPDATE thongtinlienhe 
                SET MaND=?, SoDienThoai=?, Email=?, DiaChi=? 
                WHERE MaLH=?";  // SQL query để cập nhật thông tin
        $stmt = $conn->prepare($sql);  // Chuẩn bị câu lệnh SQL
        $stmt->bind_param("isssi", $mand, $sodienthoai, $email, $diachi, $malh);  // Liên kết các tham số với câu lệnh SQL
    } else {
        // Nếu không có mã liên hệ, tức là thêm mới thông tin liên hệ
        $sql = "INSERT INTO thongtinlienhe (MaND, SoDienThoai, Email, DiaChi) 
                VALUES (?, ?, ?, ?)";  // SQL query để thêm mới thông tin
        $stmt = $conn->prepare($sql);  // Chuẩn bị câu lệnh SQL
        $stmt->bind_param("isss", $mand, $sodienthoai, $email, $diachi);  // Liên kết các tham số với câu lệnh SQL
    }

    // Thực thi câu lệnh SQL
    if ($stmt->execute()) {
        $_SESSION['success'] = "Lưu thông tin liên hệ thành công!";  // Nếu thực thi thành công, lưu thông báo thành công vào session
    } else {
        $_SESSION['error'] = "Lỗi: " . $conn->error;  // Nếu có lỗi, lưu thông báo lỗi vào session
    }

    // Chuyển hướng về trang quản lý thông tin liên hệ
    header("Location: /HMC/web/admin/thongtin.php");
    exit;  // Dừng mã thực thi
}

// Kiểm tra nếu phương thức HTTP là GET và có tham số 'id' trong URL (xử lý xóa thông tin liên hệ)
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $malh = $_GET['id'];  // Lấy mã liên hệ từ URL

    // Kiểm tra xem mã liên hệ có tồn tại trong cơ sở dữ liệu không
    $check_sql = "SELECT * FROM thongtinlienhe WHERE MaLH = ?";
    $stmt = $conn->prepare($check_sql);  // Chuẩn bị câu lệnh SQL
    $stmt->bind_param("i", $malh);  // Liên kết tham số mã liên hệ
    $stmt->execute();  // Thực thi câu lệnh SQL
    $result = $stmt->get_result();  // Lấy kết quả

    // Nếu mã liên hệ tồn tại trong cơ sở dữ liệu
    if ($result->num_rows > 0) {
        // Xóa thông tin liên hệ
        $delete_sql = "DELETE FROM thongtinlienhe WHERE MaLH = ?";  // SQL query để xóa thông tin
        $stmt = $conn->prepare($delete_sql);  // Chuẩn bị câu lệnh SQL
        $stmt->bind_param("i", $malh);  // Liên kết tham số mã liên hệ
        if ($stmt->execute()) {
            $_SESSION['success'] = "Xóa thông tin liên hệ thành công!";  // Thông báo thành công
        } else {
            $_SESSION['error'] = "Lỗi khi xóa!";  // Thông báo lỗi nếu xóa không thành công
        }
    } else {
        $_SESSION['error'] = "Thông tin liên hệ không tồn tại!";  // Thông báo lỗi nếu mã liên hệ không tồn tại
    }

    // Chuyển hướng về trang quản lý thông tin liên hệ
    header("Location: /HMC/web/admin/thongtin.php");
    exit;  // Dừng mã thực thi
}

$conn->close();  // Đóng kết nối với cơ sở dữ liệu
?>
