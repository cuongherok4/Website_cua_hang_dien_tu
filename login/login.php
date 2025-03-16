<?php
session_start();
include '../admin/autoload/Database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == "login") {
        // Lấy dữ liệu từ form
        $taikhoan = trim($_POST['TaiKhoan']);
        $matkhau = trim($_POST['MatKhau']);
        $matkhau_hashed = md5($matkhau);// Mã hóa mật khẩu

        // Truy vấn kiểm tra tài khoản
        $sql = "SELECT MaND, MatKhau, Quyen FROM nguoidung WHERE TaiKhoan = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $taikhoan);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Kiểm tra mật khẩu
        if ($user && $matkhau_hashed === $user['MatKhau']) {
            $_SESSION['MaND'] = $user['MaND'];
            $_SESSION['Quyen'] = $user['Quyen'];
            $_SESSION['success'] = "Đăng nhập thành công!";

            // Kiểm tra quyền, nếu là Admin thì chuyển sang admin.php
            if ($user['Quyen'] == 'Admin') {
                header("Location: ../admin/index.php");
            } else {
                header("Location: ../index.php");
            }
            exit();
        } else {
            $_SESSION['error'] = "Sai tài khoản hoặc mật khẩu!";
        }
    }

    if (isset($_POST['action']) && $_POST['action'] == "register") {
        // Xử lý đăng ký
        $taikhoan = trim($_POST['TaiKhoan']);
        // $matkhau = password_hash($_POST['MatKhau'], PASSWORD_DEFAULT);
        $matkhau = trim($_POST['MatKhau']);
        $matkhau_hashed = md5($matkhau);// Mã hóa mật khẩu
        $hoten = trim($_POST['HoTen']);
        $sdt = trim($_POST['SDT']);
        $email = trim($_POST['Email']);

        // Kiểm tra xem tài khoản đã tồn tại chưa
        $check_sql = "SELECT TaiKhoan FROM nguoidung WHERE TaiKhoan = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $taikhoan);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $_SESSION['error'] = "Tài khoản đã tồn tại!";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $sql = "INSERT INTO nguoidung (HoTen, SDT, Email, TaiKhoan, MatKhau, Quyen) VALUES (?, ?, ?, ?, ?, 'Khách Hàng')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $hoten, $sdt, $email, $taikhoan, $matkhau_hashed);

            if ($stmt->execute()) {
                $_SESSION['MaND'] = $stmt->insert_id;
                $_SESSION['success'] = "Đăng ký thành công!";
                header("Location: ../index.php");
                exit();
            } else {
                $_SESSION['error'] = "Lỗi khi đăng ký!";
            }
            $stmt->close();
        }
        $check_stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Đăng Nhập & Đăng Ký</title>
</head>
<style>
    *{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Montserrat', sans-serif;
}

body{
    background-color: #ffe6e6;
    background: linear-gradient(to right, #ffd6d6, #ff9999);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    height: 100vh;
}

.container{
    background-color: #fff;
    border-radius: 30px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.35);
    position: relative;
    overflow: hidden;
    width: 768px;
    max-width: 100%;
    min-height: 480px;
}

.container p{
    font-size: 14px;
    line-height: 20px;
    letter-spacing: 0.3px;
    margin: 20px 0;
}

.container span{
    font-size: 12px;
}

.container a{
    color: #d32f2f;
    font-size: 13px;
    text-decoration: none;
    margin: 15px 0 10px;
}

.container button{
    background-color: #d32f2f;
    color: #fff;
    font-size: 12px;
    padding: 10px 45px;
    border: 1px solid transparent;
    border-radius: 8px;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    margin-top: 10px;
    cursor: pointer;
}

.container button.hidden{
    background-color: transparent;
    border: 1px solid #fff;
    color: #fff;
    background-color: #ff9999;
}
.container button.hidden:hover{
    background-color: #ff5252;
}

.container form{
    background-color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    padding: 0 40px;
    height: 100%;
}

.container input{
    background-color: #f8d7da;
    border: none;
    margin: 8px 0;
    padding: 10px 15px;
    font-size: 13px;
    border-radius: 8px;
    width: 100%;
    outline: none;
}

.form-container{
    position: absolute;
    top: 0;
    height: 100%;
    transition: all 0.6s ease-in-out;
}

.sign-in{
    left: 0;
    width: 50%;
    z-index: 2;
}

.container.active .sign-in{
    transform: translateX(100%);
}

.sign-up{
    left: 0;
    width: 50%;
    opacity: 0;
    z-index: 1;
}

.container.active .sign-up{
    transform: translateX(100%);
    opacity: 1;
    z-index: 5;
    animation: move 0.6s;
}

@keyframes move{
    0%, 49.99%{
        opacity: 0;
        z-index: 1;
    }
    50%, 100%{
        opacity: 1;
        z-index: 5;
    }
}

.toggle-container{
    position: absolute;
    top: 0;
    left: 50%;
    width: 50%;
    height: 100%;
    overflow: hidden;
    transition: all 0.6s ease-in-out;
    border-radius: 150px 0 0 100px;
    z-index: 1000;
}

.container.active .toggle-container{
    transform: translateX(-100%);
    border-radius: 0 150px 100px 0;
}

.toggle{
    background-color: #d32f2f;
    height: 100%;
    background: linear-gradient(to right, #ff5252, #d32f2f);
    color: #fff;
    position: relative;
    left: -100%;
    height: 100%;
    width: 200%;
    transform: translateX(0);
    transition: all 0.6s ease-in-out;
}

.container.active .toggle{
    transform: translateX(50%);
}

.toggle-panel{
    position: absolute;
    width: 50%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    padding: 0 30px;
    text-align: center;
    top: 0;
    transform: translateX(0);
    transition: all 0.6s ease-in-out;
}

.toggle-left{
    transform: translateX(-200%);
}

.container.active .toggle-left{
    transform: translateX(0);
}

.toggle-right{
    right: 0;
    transform: translateX(0);
}

.container.active .toggle-right{
    transform: translateX(200%);
}

</style>
<body>

    <div class="container" id="container">
        <!-- Form đăng ký -->
        <div class="form-container sign-up">
            <form method="POST">
                <h1>Đăng Ký</h1>
                <span>Nhập thông tin của bạn để đăng ký</span>
                <input type="hidden" name="action" value="register">
                <input type="text" name="TaiKhoan" placeholder="Tài khoản" required>
                <input type="password" name="MatKhau" placeholder="Mật khẩu" required>
                <input type="text" name="HoTen" placeholder="Họ và tên" required>
                <input type="text" name="SDT" placeholder="Số điện thoại" required>
                <input type="email" name="Email" placeholder="Email" required>
                
                <!-- Thông báo lỗi cho đăng ký -->
                <?php if (isset($_SESSION['error'])): ?>
                    <p class='error' style='color:red;'><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
                <?php endif; ?>
                
                <!-- Thông báo thành công cho đăng ký -->
                <?php if (isset($_SESSION['success'])): ?>
                    <p class='success' style='color:green;'><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
                <?php endif; ?>

                <button type="submit">Đăng ký</button>
            </form>
        </div>

        <!-- Form đăng nhập -->
        <div class="form-container sign-in">
            <form method="POST">
                <h1>Đăng Nhập</h1>
                <span>Nhập tài khoản và mật khẩu của bạn</span>
                <input type="hidden" name="action" value="login">
                <input type="text" name="TaiKhoan" placeholder="Tài khoản" required>
                <input type="password" name="MatKhau" placeholder="Mật khẩu" required>

                <!-- Thông báo lỗi cho đăng nhập -->
                <?php if (isset($_SESSION['error'])): ?>
                    <p class='error' style='color:red;'><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
                <?php endif; ?>

                <!-- Thông báo thành công cho đăng nhập -->
                <?php if (isset($_SESSION['success'])): ?>
                    <p class='success' style='color:green;'><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
                <?php endif; ?>

                <a href="../quenmk.php">Quên mật khẩu?</a>
                <button type="submit">Đăng nhập</button>
            </form>
        </div>

        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Chào mừng trở lại!</h1>
                    <p>Nhập thông tin đăng nhập để tiếp tục</p>
                    <button class="hidden" id="login">Đăng nhập</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Xin chào!</h1>
                    <p>Đăng ký để tham gia cùng chúng tôi</p>
                    <button class="hidden" id="register">Đăng ký</button>
                </div>
            </div>
        </div>
    </div>

    <script src="login.js"></script>
</body>

</html>
