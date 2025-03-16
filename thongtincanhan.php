<?php
session_start();
include '../web/admin/autoload/Database.php';

if (!isset($_SESSION['MaND'])) {
    echo "<script>alert('Bạn chưa đăng nhập! Vui lòng đăng nhập!'); window.history.back();;</script>";
    exit();
}

$user_id = $_SESSION['MaND']; // Lấy ID từ session

$sql = "SELECT MaND, HoTen, SDT, Email, TaiKhoan, MatKhau FROM nguoidung WHERE MaND = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Xử lý cập nhật thông tin
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $HoTen = trim($_POST['HoTen']);
    $SDT = trim($_POST['SDT']);
    $Email = trim($_POST['Email']);
    $TaiKhoan = trim($_POST['TaiKhoan']);
    $MatKhau = trim($_POST['MatKhau']);

    if (empty($HoTen) || empty($SDT) || empty($Email) || empty($TaiKhoan)) {
        echo "<script>alert('Vui lòng nhập đầy đủ thông tin!'); window.history.back();</script>";
        exit;
    }

    // Nếu nhập mật khẩu mới -> cập nhật, nếu không -> giữ nguyên
    if (!empty($MatKhau)) {
        $hashedPassword = password_hash($MatKhau, PASSWORD_BCRYPT);
        $sql_update = "UPDATE nguoidung SET HoTen=?, SDT=?, Email=?, TaiKhoan=?, MatKhau=? WHERE MaND=?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssssi", $HoTen, $SDT, $Email, $TaiKhoan, $hashedPassword, $user_id);
    } else {
        $sql_update = "UPDATE nguoidung SET HoTen=?, SDT=?, Email=?, TaiKhoan=? WHERE MaND=?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssssi", $HoTen, $SDT, $Email, $TaiKhoan, $user_id);
    }

    if ($stmt_update->execute()) {
        echo "<script>alert('Cập nhật thành công!'); window.location.href='thongtincanhan.php';</script>";
    } else {
        echo "<script>alert('Lỗi khi cập nhật!'); window.history.back();</script>";
    }

    $stmt_update->close();
}

?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh tìm kiếm</title>
    <link rel="stylesheet" href="/web/public/client/css/header_footer.css">
    <link rel="stylesheet" href="/web/public/client/css/thongtincanhan.css">
   
</head>
<style>
    .giua {
    display: flex;
    justify-content: center;
    align-items: center;
    /* height: 60vh; Nếu muốn căn giữa theo cả chiều dọc */
}

.card {
    width: 400px; /* Điều chỉnh kích thước thẻ card */
    padding: 20px;
    background: white;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    margin: 20px 0;
    text-align: center;
}
.card h2 {
    margin-bottom: 20px;
    color: #333;
}
.input-group {
    margin-bottom: 12px;
    text-align: left;
}
.input-group label {
    font-weight: bold;
    display: block;
    margin-bottom: 5px;
}
.input-group input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 16px;
    transition: 0.3s;
}
.input-group input:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
}
.btn-save {
    background: #007bff;
    color: white;
    padding: 10px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
    width: 100%;
    transition: 0.3s;
}
.btn-save:hover {
    background: #0056b3;
}
.card h2 {
    margin-bottom: 20px;
    color: #333;
}
.input-group {
    margin-bottom: 12px;
    text-align: left;
}
.input-group label {
    font-weight: bold;
    display: block;
    margin-bottom: 5px;
}
.input-group input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 16px;
    transition: 0.3s;
}
.input-group input:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
}
.btn-save {
    background: #007bff;
    color: white;
    padding: 10px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
    width: 100%;
    transition: 0.3s;
}
.btn-save:hover {
    background: #0056b3;
}
</style>
<body>
    
  <!-- header_menu -->
 <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/HMC/web/header.php'); ?>

    <!--  -->
<div class="giua">
    <div class="card">
    <h2>Thông tin cá nhân</h2>
    <form method="POST">
        <div class="input-group">
            <label>Họ và tên:</label>
            <input type="text" name="HoTen" value="<?= htmlspecialchars($user['HoTen']) ?>" required>
        </div>

        <div class="input-group">
            <label>Số điện thoại:</label>
            <input type="text" name="SDT" value="<?= htmlspecialchars($user['SDT']) ?>" required>
        </div>

        <div class="input-group">
            <label>Email:</label>
            <input type="email" name="Email" value="<?= htmlspecialchars($user['Email']) ?>" required>
        </div>

        <div class="input-group">
            <label>Tài khoản:</label>
            <input type="text" name="TaiKhoan" value="<?= htmlspecialchars($user['TaiKhoan']) ?>" required>
        </div>

        <div class="input-group">
            <label>Mật khẩu (để trống nếu không muốn đổi):</label>
            <input type="password" name="MatKhau" placeholder="Nhập mật khẩu mới">
        </div>

        <input type="hidden" name="MaND" value="<?= $user['MaND'] ?>">

        <button type="submit" name="update" class="btn-save">Lưu</button>
    </form>
    </div>
</div>
</body>

<footer>
<?php include_once($_SERVER['DOCUMENT_ROOT'] . '/HMC/web/footer.php'); ?>
</footer>

</html>
