

<?php
include '../web/admin/autoload/Database.php';
// Lấy danh sách tin tức từ cơ sở dữ liệu
$sql = "SELECT MaTinTuc, TieuDe, AnhTinTuc, NoiDung, Linklk FROM tintuc ORDER BY MaTinTuc DESC";
$result = $conn->query($sql);




?>


<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh tìm kiếm</title>
    <link rel="stylesheet" href="/HMC/web/public/client/css/header_footer.css">
    <link rel="stylesheet" href="/HMC/web/public/client/css/tintuc.css">
    
</head>

<body>
      <!-- header_menu -->
 <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/HMC/web/header.php'); ?>

    <!--  -->
    <section class="content">
    <?php while ($row = $result->fetch_assoc()) { ?>
        <div class="box">
            <h2>
                <a href="<?= htmlspecialchars($row['Linklk']) ?>" target="_blank">
                    <?= htmlspecialchars($row['TieuDe']) ?>
                </a>
            </h2>
            <div class="text_img">
                <img src="<?= htmlspecialchars($row['AnhTinTuc']) ?>" alt="Ảnh tin tức">
                <p><?= nl2br(htmlspecialchars($row['NoiDung'])) ?></p>
            </div>
        </div>
    <?php } ?>
</section>

</body>

<footer>
    <!-- footer -->
<?php include_once($_SERVER['DOCUMENT_ROOT'] . '/HMC/web/footer.php'); ?>

</footer>

</html>
