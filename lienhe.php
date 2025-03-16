<?php
session_start();
include '../web/admin/autoload/Database.php';

// Lấy thông tin liên hệ mới nhất
$sql = "SELECT * FROM thongtinlienhe ORDER BY MaLH DESC LIMIT 1";
$result = $conn->query($sql);

$hotline = $email = $diachi = "";
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $hotline = $row["SoDienThoai"];
    $email = $row["Email"];
    $diachi = $row["DiaChi"];
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin liên hệ</title>
    <link rel="stylesheet" href="/HMC/web/public/client/css/lienhe.css">
    
</head>
<body>
     <!-- header_menu -->
     <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/HMC/web/header.php'); ?>


<div class="container">
    <!-- Phần bên trái: Thông tin liên hệ + Form -->
    <div class="left">
        <h2>Thông tin liên hệ</h2>
        <div class="info">
            <p><strong>Hotline:</strong> <?php echo $hotline; ?></p>
            <p><strong>Email:</strong> <?php echo $email; ?></p>
            <p><strong>Địa chỉ:</strong> <?php echo $diachi; ?></p>
        </div>

        <h2>Đặt câu hỏi nếu có thắc mắc?</h2>

        <form method="post">
            <label for="name">Họ và tên</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="message">Nội dung</label>
            <textarea id="message" name="message" required></textarea>

            <button type="submit">Gửi liên hệ</button>
        </form>
    </div>

    <!-- Phần bên phải: Google Map -->
    <div class="right">
        <h2>Bản đồ</h2>
        <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.637064541317!2d106.67584897593205!3d10.762663459309488!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f38c74b4fbd%3A0x79b7129a47d3b19a!2zMTIzIMSQLiBBQkMsIFF14bqtbiAxLCBUUC5IQ00!5e0!3m2!1sen!2s!4v1700000000000!5m2!1sen!2s" 
                width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy">
            </iframe>
    </div>
</div>

<script>
function initMap() {
    var address = "<?php echo $diachi; ?>";
    var geocoder = new google.maps.Geocoder();
    var map = new google.maps.Map(document.getElementById("map"), {
        zoom: 15,
        center: { lat: 21.036237, lng: 105.782742 } // Giá trị mặc định
    });

    geocoder.geocode({ 'address': address }, function(results, status) {
        if (status === 'OK') {
            map.setCenter(results[0].geometry.location);
            new google.maps.Marker({
                map: map,
                position: results[0].geometry.location
            });
        } else {
            console.error('Lỗi tìm địa chỉ: ' + status);
            alert('Không tìm thấy địa chỉ, vui lòng kiểm tra lại.');
        }
    });
}
</script>


<script async defer src="https://maps.googleapis.com/maps/api/js?key=YOUR_REAL_API_KEY&callback=initMap&v=weekly"></script>


</body>
<footer>
<?php include_once($_SERVER['DOCUMENT_ROOT'] . '/HMC/web/footer.php'); ?>
</footer>
</html>
