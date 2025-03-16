<?php
session_start();
session_unset(); // Xóa tất cả biến session
session_destroy(); // Hủy toàn bộ session
header("Location: ../login/login.php"); // Chuyển hướng về trang đăng nhập
exit();
?>
