<?php
session_start(); // Bắt đầu phiên làm việc của PHP, cho phép sử dụng session.
include 'Database.php'; // Kết nối đến tệp Database.php để sử dụng các chức năng kết nối cơ sở dữ liệu.


if ($_SERVER["REQUEST_METHOD"] == "POST") { // Kiểm tra xem yêu cầu HTTP có phải là POST không.
    // Nhận dữ liệu từ form
    $donhang_id = $_POST['madh'] ?? null; // Lấy giá trị từ trường 'madh' trong form, nếu không có thì gán null.
    $user_id = $_POST['mand'] ?? ''; // Lấy giá trị từ trường 'mand' trong form, nếu không có thì gán rỗng.
    $ngaylap = $_POST['ngaylap'] ?? ''; // Lấy giá trị từ trường 'ngaylap' trong form, nếu không có thì gán rỗng.
    $nguoimua = $_POST['nguoinhan'] ?? ''; // Lấy giá trị từ trường 'nguoinhan' trong form, nếu không có thì gán rỗng.
    $sdt = $_POST['sdt'] ?? ''; // Lấy giá trị từ trường 'sdt' trong form, nếu không có thì gán rỗng.
    $diachi = $_POST['diachi'] ?? ''; // Lấy giá trị từ trường 'diachi' trong form, nếu không có thì gán rỗng.
    $phuongthuctt = $_POST['pttt'] ?? ''; // Lấy giá trị từ trường 'pttt' trong form, nếu không có thì gán rỗng.
    $tongtien = $_POST['tongtien'] ?? ''; // Lấy giá trị từ trường 'tongtien' trong form, nếu không có thì gán rỗng.
    $trangthai = $_POST['trangthai'] ?? ''; // Lấy giá trị từ trường 'trangthai' trong form, nếu không có thì gán rỗng.

    // Kiểm tra kết nối CSDL
    if (!$conn) { // Nếu không có kết nối đến cơ sở dữ liệu.
        $_SESSION['error'] = "Lỗi kết nối CSDL!"; // Lưu thông báo lỗi vào session.
        header("Location: /HMC/web/admin/donhang.php"); // Chuyển hướng về trang quản lý đơn hàng.
        exit;
    }

    // Kiểm tra dữ liệu đầu vào
    if (empty($user_id) || empty($ngaylap) || empty($nguoimua) || empty($sdt) || empty($diachi) || empty($phuongthuctt) || empty($tongtien) || empty($trangthai)) {
        $_SESSION['error'] = "Vui lòng nhập đầy đủ thông tin!"; // Nếu thiếu thông tin, lưu thông báo lỗi vào session.
        header("Location: /HMC/web/admin/donhang.php"); // Chuyển hướng lại trang quản lý đơn hàng.
        exit;
    }

    // Kiểm tra định dạng số tiền
    if (!is_numeric($tongtien)) { // Kiểm tra nếu tổng tiền không phải là số.
        $_SESSION['error'] = "Tổng tiền không hợp lệ!"; // Lưu thông báo lỗi vào session.
        header("Location: /HMC/web/admin/donhang.php"); // Chuyển hướng về trang quản lý đơn hàng.
        exit;
    }

    // Xử lý cập nhật hoặc thêm mới
    if (!empty($donhang_id)) { // Nếu có mã đơn hàng (cập nhật).
        // Cập nhật đơn hàng
        $sql = "UPDATE donhang SET MaND=?, NgayLap=?, NguoiNhan=?, SDT=?, DiaChi=?, PhuongThucTT=?, TongTien=?, TrangThai=? WHERE MaDH=?"; // Câu lệnh SQL cập nhật.
        $stmt = $conn->prepare($sql); // Chuẩn bị câu lệnh SQL.
        $stmt->bind_param("isssssssi", $user_id, $ngaylap, $nguoimua, $sdt, $diachi, $phuongthuctt, $tongtien, $trangthai, $donhang_id); // Liên kết tham số với câu lệnh SQL.
    } else {
        // Thêm mới đơn hàng
        $sql = "INSERT INTO donhang (MaND, NgayLap, NguoiNhan, SDT, DiaChi, PhuongThucTT, TongTien, TrangThai) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"; // Câu lệnh SQL thêm mới.
        $stmt = $conn->prepare($sql); // Chuẩn bị câu lệnh SQL.
        $stmt->bind_param("isssssss", $user_id, $ngaylap, $nguoimua, $sdt, $diachi, $phuongthuctt, $tongtien, $trangthai); // Liên kết tham số với câu lệnh SQL.
    }

    // Thực thi truy vấn
    if ($stmt->execute()) { // Thực hiện câu lệnh SQL.
        $_SESSION['success'] = "Lưu thành công!"; // Nếu thành công, lưu thông báo thành công vào session.
    } else {
        $_SESSION['error'] = "Lỗi: " . $stmt->error; // Nếu lỗi, lưu thông báo lỗi vào session.
    }

    // Đóng statement và kết nối
    $stmt->close(); // Đóng statement.
    $conn->close(); // Đóng kết nối cơ sở dữ liệu.

    // Chuyển hướng
    header("Location: /HMC/web/admin/donhang.php"); // Chuyển hướng về trang quản lý đơn hàng.
    exit;
}
// Xử lý xóa đơn hàng
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) { // Kiểm tra nếu là yêu cầu GET và có tham số 'id'.
    $donhang_id = $_GET['id']; // Lấy mã đơn hàng từ URL.

    // Kiểm tra kết nối CSDL
    if (!$conn) { // Nếu không có kết nối đến cơ sở dữ liệu.
        $_SESSION['error'] = "Lỗi kết nối CSDL!"; // Lưu thông báo lỗi vào session.
        header("Location: /HMC/web/admin/donhang.php"); // Chuyển hướng về trang quản lý đơn hàng.
        exit;
    }
    // Kiểm tra xem MaDH có tồn tại không
    $check_sql = "SELECT * FROM donhang WHERE MaDH = ?"; // Kiểm tra mã đơn hàng có tồn tại trong cơ sở dữ liệu.
    $stmt = $conn->prepare($check_sql); // Chuẩn bị câu lệnh SQL.
    $stmt->bind_param("i", $donhang_id); // Liên kết tham số với câu lệnh SQL.
    $stmt->execute(); // Thực hiện câu lệnh SQL.
    $result = $stmt->get_result(); // Lấy kết quả truy vấn.

    if ($result->num_rows > 0) { // Nếu tìm thấy đơn hàng.
        // Nếu tồn tại, thực hiện xóa
        $delete_sql = "DELETE FROM donhang WHERE MaDH = ?"; // Câu lệnh SQL xóa.
        $stmt = $conn->prepare($delete_sql); // Chuẩn bị câu lệnh SQL.
        $stmt->bind_param("i", $donhang_id); // Liên kết tham số với câu lệnh SQL.
        if ($stmt->execute()) { // Thực hiện câu lệnh SQL.
            $_SESSION['success'] = "Xóa thành công!"; // Nếu thành công, lưu thông báo thành công vào session.
        } else {
            $_SESSION['error'] = "Lỗi khi xóa!"; // Nếu có lỗi, lưu thông báo lỗi vào session.
        }
    } else {
        $_SESSION['error'] = "Đơn hàng không tồn tại!"; // Nếu không tìm thấy đơn hàng, lưu thông báo lỗi vào session.
    }
    // Đóng statement và kết nối
    $stmt->close(); // Đóng statement.
    $conn->close(); // Đóng kết nối cơ sở dữ liệu.
    // Chuyển hướng
    header("Location: /HMC/web/admin/donhang.php"); // Chuyển hướng về trang quản lý đơn hàng.
    exit;
}
?>
