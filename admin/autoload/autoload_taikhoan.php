<?php
session_start();  // Bắt đầu phiên làm việc (session) để lưu trữ thông tin người dùng và trạng thái.
include 'Database.php';  // Bao gồm tệp kết nối cơ sở dữ liệu để sử dụng trong mã.

if ($_SERVER["REQUEST_METHOD"] == "POST") {  // Kiểm tra nếu yêu cầu là một POST request (ví dụ: từ form).
    // Nhận dữ liệu từ form gửi đến (sử dụng toán tử null coalescing để tránh lỗi khi dữ liệu không tồn tại).
    $user_id = $_POST['user_id'] ?? null;  
    $hoten = $_POST['hoten'] ?? '';  // Tên người dùng
    $sdt = $_POST['sdt'] ?? '';  // Số điện thoại
    $email = $_POST['email'] ?? '';  // Email người dùng
    $taikhoan = $_POST['taikhoan'] ?? '';  // Tên tài khoản
    $matkhau = $_POST['matkhau'] ?? '';  // Mật khẩu
    $quyen = $_POST['quyen'] ?? '';  // Quyền của người dùng

    // Kiểm tra xem email đã tồn tại trong cơ sở dữ liệu chưa
    $sql_check_email = "SELECT * FROM nguoidung WHERE Email = ?";  
    if (!empty($user_id)) {  // Nếu có user_id, tức là đang cập nhật người dùng
        $sql_check_email .= " AND MaND != ?";  // Loại trừ người dùng hiện tại khỏi kết quả kiểm tra
    }
    $stmt = $conn->prepare($sql_check_email);  // Chuẩn bị câu lệnh SQL
    if (!empty($user_id)) {  // Nếu có user_id thì sử dụng thêm tham số
        $stmt->bind_param("si", $email, $user_id);  
    } else {
        $stmt->bind_param("s", $email);  // Nếu không có user_id, chỉ kiểm tra email
    }
    $stmt->execute();  // Thực thi câu lệnh SQL
    $result_email = $stmt->get_result();  // Lấy kết quả truy vấn

    // Kiểm tra nếu email đã tồn tại
    if ($result_email->num_rows > 0) {  
        $_SESSION['error'] = "Email không hợp lệ! Email này đã được sử dụng.";  // Gửi thông báo lỗi vào session
        header("Location: /HMC/web/admin/taikhoan.php");  // Điều hướng lại trang quản lý tài khoản
        exit;  // Dừng thực thi tiếp theo
    }

    // Kiểm tra xem tài khoản đã tồn tại chưa
    $sql_check_taikhoan = "SELECT * FROM nguoidung WHERE TaiKhoan = ?";  
    if (!empty($user_id)) {
        $sql_check_taikhoan .= " AND MaND != ?";  // Loại trừ người dùng hiện tại
    }
    $stmt = $conn->prepare($sql_check_taikhoan);  // Chuẩn bị câu lệnh SQL
    if (!empty($user_id)) {
        $stmt->bind_param("si", $taikhoan, $user_id);  // Bind tham số với giá trị tài khoản và user_id
    } else {
        $stmt->bind_param("s", $taikhoan);  // Nếu không có user_id, chỉ kiểm tra tài khoản
    }
    $stmt->execute();  // Thực thi câu lệnh SQL
    $result_taikhoan = $stmt->get_result();  // Lấy kết quả

    // Kiểm tra nếu tài khoản đã tồn tại
    if ($result_taikhoan->num_rows > 0) {  
        $_SESSION['error'] = "Tài khoản đã tồn tại! Vui lòng chọn tên tài khoản khác.";  // Gửi thông báo lỗi vào session
        header("Location: /HMC/web/admin/taikhoan.php");  // Điều hướng lại trang quản lý tài khoản
        exit;  // Dừng thực thi tiếp theo
    }

    // Xử lý cập nhật người dùng hoặc thêm mới
    if (!empty($user_id)) {  // Nếu có user_id, tức là đang cập nhật thông tin người dùng
        if (!empty($matkhau)) {  // Nếu mật khẩu không rỗng, cần mã hóa mật khẩu trước khi lưu
            $matkhau_hashed = md5($matkhau);
            $sql = "UPDATE nguoidung SET HoTen=?, SDT=?, Email=?, TaiKhoan=?, MatKhau=?, Quyen=? WHERE MaND=?";  // Câu lệnh SQL để cập nhật
            $stmt = $conn->prepare($sql);  // Chuẩn bị câu lệnh SQL
            $stmt->bind_param("ssssssi", $hoten, $sdt, $email, $taikhoan, $matkhau_hashed, $quyen, $user_id);  // Bind tham số
        } else {  // Nếu không thay đổi mật khẩu
            $sql = "UPDATE nguoidung SET HoTen=?, SDT=?, Email=?, TaiKhoan=?, Quyen=? WHERE MaND=?";  // Câu lệnh SQL để cập nhật
            $stmt = $conn->prepare($sql);  // Chuẩn bị câu lệnh SQL
            $stmt->bind_param("sssssi", $hoten, $sdt, $email, $taikhoan, $quyen, $user_id);  // Bind tham số
        }
    } else {  // Nếu không có user_id, tức là thêm mới người dùng
        $matkhau_hashed = md5($matkhau); // Mã hóa mật khẩu
        $sql = "INSERT INTO nguoidung (HoTen, SDT, Email, TaiKhoan, MatKhau, Quyen) VALUES (?, ?, ?, ?, ?, ?)";  // Câu lệnh SQL để thêm mới người dùng
        $stmt = $conn->prepare($sql);  // Chuẩn bị câu lệnh SQL
        $stmt->bind_param("ssssss", $hoten, $sdt, $email, $taikhoan, $matkhau_hashed, $quyen);  // Bind tham số
    }

    // Thực thi câu lệnh SQL
    if ($stmt->execute()) {  
        $_SESSION['success'] = "Lưu thành công!";  // Gửi thông báo thành công vào session
    } else {
        $_SESSION['error'] = "Lỗi: " . $conn->error;  // Gửi thông báo lỗi nếu có
    }

    header("Location: /HMC/web/admin/taikhoan.php");  // Điều hướng lại trang quản lý tài khoản
    exit;  // Dừng thực thi tiếp theo
}

// Xử lý xóa người dùng
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {  // Nếu yêu cầu là GET và có tham số id
    $user_id = $_GET['id'];  // Lấy id người dùng cần xóa

    // Kiểm tra xem MaND có tồn tại không
    $check_sql = "SELECT * FROM nguoidung WHERE MaND = ?";  
    $stmt = $conn->prepare($check_sql);  // Chuẩn bị câu lệnh SQL
    $stmt->bind_param("i", $user_id);  // Bind tham số id
    $stmt->execute();  // Thực thi câu lệnh SQL
    $result = $stmt->get_result();  // Lấy kết quả

    if ($result->num_rows > 0) {  // Nếu tìm thấy người dùng
        // Nếu tồn tại, thực hiện xóa
        $delete_sql = "DELETE FROM nguoidung WHERE MaND = ?";  // Câu lệnh SQL xóa người dùng
        $stmt = $conn->prepare($delete_sql);  // Chuẩn bị câu lệnh SQL
        $stmt->bind_param("i", $user_id);  // Bind tham số id
        if ($stmt->execute()) {  
            $_SESSION['success'] = "Xóa thành công!";  // Thông báo xóa thành công
        } else {
            $_SESSION['error'] = "Lỗi khi xóa!";  // Thông báo lỗi khi xóa
        }
    } else {
        $_SESSION['error'] = "Người dùng không tồn tại!";  // Thông báo không tìm thấy người dùng
    }

    header("Location: /HMC/web/admin/taikhoan.php");  // Điều hướng lại trang quản lý tài khoản
    exit;  // Dừng thực thi tiếp theo
}

$conn->close();  // Đóng kết nối cơ sở dữ liệu
?>
