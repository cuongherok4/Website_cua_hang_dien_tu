<?php
include '../web/admin/autoload/Database.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $new_password = trim($_POST["new_password"]);
    $matkhau_hashed = md5($new_password);// Mã hóa mật khẩu


    if (empty($username) || empty($email) || empty($matkhau_hashed)) {
        $message = "Vui lòng nhập đầy đủ thông tin!";
    } else {
        $conn = new mysqli("localhost", "root", "", "php_webchdt"); // Cập nhật thông tin kết nối

        if ($conn->connect_error) {
            die("Lỗi kết nối database: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM nguoidung WHERE TaiKhoan = ? AND Email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
            
                $update_sql = "UPDATE nguoidung SET MatKhau = ? WHERE TaiKhoan = ? AND Email = ?";

                if ($update_stmt = $conn->prepare($update_sql)) {
                    $update_stmt->bind_param("sss", $matkhau_hashed, $username, $email);
                    if ($update_stmt->execute()) {
                        $message = "Mật khẩu đã được cập nhật thành công!";
                    } else {
                        $message = "Có lỗi xảy ra, vui lòng thử lại!";
                    }
                }
            } else {
                $message = "Tên tài khoản hoặc email không chính xác!";
            }
        }
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên Mật Khẩu</title>
    <style>
body {
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background: linear-gradient(to right, #ffd6d6, #ff9999);
    margin: 0;
}

.container {
    width: 400px;
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    text-align: center;
}

h2 {
    font-size: 24px;
    color: #d32f2f;
    margin-bottom: 20px;
}

.message {
    color: red;
    font-size: 14px;
    margin-bottom: 15px;
}

input {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 25px;
    font-size: 16px;
    outline: none;
    transition: 0.3s;
}

input:focus {
    border-color: #d32f2f;
    box-shadow: 0 0 5px rgba(211, 47, 47, 0.5);
}

button {
    width: 100%;
    padding: 12px;
    background: #d32f2f;
    color: white;
    border: none;
    border-radius: 25px;
    font-size: 16px;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background: #b71c1c;
}

.links {
    margin-top: 15px;
}

.links a {
    text-decoration: none;
    color: #d32f2f;
    font-size: 16px;
    padding: 8px 16px;
    border: 1px solid #d32f2f;
    border-radius: 25px;
    transition: 0.3s;
}

.links a:hover {
    background: #d32f2f;
    color: white;
}


    </style>
</head>
<body>
    <div class="container">
        <h2>Quên Mật Khẩu</h2>
        <?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>
        <form action="" method="post">
            <input type="text" name="username" placeholder="Tên tài khoản" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="new_password" placeholder="Mật khẩu mới" required>
            <button type="submit">Gửi</button>
        </form>
        <div class="links">
            <a href="../web/login/login.php">Đăng nhập</a>
        </div>
    </div>
</body>
</html>
