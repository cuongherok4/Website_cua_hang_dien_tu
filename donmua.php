<?php
session_start();
include '../web/admin/autoload/Database.php';

if (!isset($_SESSION['MaND'])) {
    echo "<script>alert('Bạn chưa đăng nhập! Vui lòng đăng nhập!'); window.history.back();;</script>";
    exit();
}

$MaND = $_SESSION['MaND'];

// Lấy trạng thái từ URL
$trangthai = isset($_GET['trangthai']) ? $_GET['trangthai'] : 'all';

// Truy vấn danh sách đơn hàng dựa vào trạng thái
if ($trangthai == 'all') {
    $sql = "SELECT * FROM donhang WHERE MaND = ?";
} else {
    $sql = "SELECT * FROM donhang WHERE MaND = ? AND TrangThai = ?";
}


$stmt = $conn->prepare($sql);

if ($trangthai == 'all') {
    $stmt->bind_param("i", $MaND);
} else {
    $stmt->bind_param("is", $MaND, $trangthai);
}


$stmt->execute();
$result = $stmt->get_result();


?>






<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $(".view-btn").click(function(e) {
            e.preventDefault(); // Ngăn chặn load trang
            var maDH = $(this).data("madh"); // Lấy mã đơn hàng từ data-attribute

            $.ajax({
                url: "get_chitietdonhang.php",
                type: "GET",
                data: {
                    MaDH: maDH
                },
                success: function(response) {
                    $("#modalContent").html(response); // Đổ dữ liệu vào modal
                    $("#viewModal").modal("show"); // Hiển thị modal
                }
            });
        });
    });
</script>


<!-- Hiển thị thông báo -->
<div id="alert-container" class="position-fixed start-50 translate-middle-x mt-3" style="top: 10px; z-index: 1050;">
    <?php if (isset($_SESSION['success'])) : ?>
        <div class="alert alert-success alert-dismissible fade show text-center d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <!-- Icon tích -->
            <?= $_SESSION['success']; ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])) : ?>
        <div class="alert alert-danger alert-dismissible fade show text-center d-flex align-items-center" role="alert">
            <i class="bi bi-x-circle-fill me-2"></i> <!-- Icon X -->
            <?= $_SESSION['error']; ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
</div>

<!-- JavaScript để đóng thông báo sau 5 giây -->
<script src="/HMC/web/public/admin/js/thongbaosave.js"></script>
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">


<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Đơn Hàng</title>
    <link rel="stylesheet" href="/HMC/web/public/client/css/donmua.css">

</head>
<style>
    .modal.fade .modal-dialog {
        transform: translateY(-100%);
        opacity: 0;
        transition: transform 0.5s ease-out, opacity 0.5s ease-out;
    }

    .modal.show .modal-dialog {
        transform: translateY(0);
        opacity: 1;
    }


    .cot {
        list-style: none;
        padding: 10px;
        margin: 10px auto;
        display: flex;
        justify-content: center;
        /* Căn giữa danh sách */
        gap: 10px;
        /* Giảm khoảng cách giữa các mục */
        background: #f8f9fa;
        /* Màu nền nhẹ */
        border-radius: 8px;
        padding: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .cot .li1 {
        display: inline-block;
    }

    .cot .li1 .a1 {
        text-decoration: none;
        padding: 8px 12px;
        /* Giảm kích thước padding */
        font-size: 14px;
        /* Giảm cỡ chữ */
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
        font-weight: bold;
        border-radius: 6px;
        border: 1px solid transparent;
        transition: all 0.3s ease-in-out;
        display: block;
    }

    .cot .li1 .a1:hover {
        background: white;
        color: #007bff;
        border: 1px solid #007bff;
        box-shadow: 0 2px 6px rgba(0, 123, 255, 0.3);
        transform: translateY(-2px);
    }
</style>

<body>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/HMC/web/header.php'); ?>

    <h2>Danh sách Đơn Hàng</h2>
    <ul class="cot">
        <li class="li1"><a class="a1" href="?trangthai=all">Tất cả</a></li>
        <li class="li1"><a class="a1" href="?trangthai=Chờ xác nhận">Chờ xác nhận</a></li>
        <li class="li1"><a class="a1" href="?trangthai=Vận chuyển">Vận chuyển</a></li>
        <li class="li1"><a class="a1" href="?trangthai=Đã giao">Đã giao</a></li>
        <li class="li1"><a class="a1" href="?trangthai=Đã hủy">Đã hủy</a></li>
        <li class="li1"><a class="a1" href="?trangthai=Hoàn tiền">Trả hàng\hoàn tiền</a></li>
    </ul>
    <table id="datatablesSimple" class="table table-hover table-bordered text-center align-middle">
        <thead class="table-dark">
            <tr>

                <th>Ngày lập</th>
                <th>Người nhận</th>
                <th>Số điện thoại</th>
                <th>Địa chỉ</th>
                <th>Phương thức tt</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>

                    <td><?= $row['NgayLap'] ?></td>
                    <td><?= $row['NguoiNhan'] ?></td>
                    <td><?= $row['SDT'] ?></td>
                    <td><?= $row['DiaChi'] ?></td>
                    <td><?= $row['PhuongThucTT'] ?></td>
                    <td><?= number_format($row['TongTien'], 0, ',', '.') ?> đ</td>
                    <td><?= $row['TrangThai'] ?></td>
                    <td>
                        <a href="#" class="btn btn-info btn-sm view-btn"
                            data-madh="<?= $row['MaDH'] ?>">
                            👀 Xem
                        </a>

                        <?php if ($row['TrangThai'] == 'Chờ xác nhận') : ?>
                            <a href="huy_donhang.php?madh=<?= $row['MaDH'] ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?')">
                                ❌ Hủy
                            </a>
                        <?php endif; ?>
                        <button class="btn btn-warning btn-sm edit-btn"
                            data-madh="<?= $row['MaDH'] ?>"
                            data-mand="<?= $row['MaND'] ?>"
                            data-ngaylap="<?= $row['NgayLap'] ?>"
                            data-nguoinhan="<?= $row['NguoiNhan'] ?>"
                            data-sdt="<?= $row['SDT'] ?>"
                            data-diachi="<?= $row['DiaChi'] ?>"
                            data-pttt="<?= $row['PhuongThucTT'] ?>"
                            data-tongtien="<?= $row['TongTien'] ?>"
                            data-trangthai="<?= $row['TrangThai'] ?>"
                            data-bs-toggle="modal"
                            data-bs-target="#userModal">
                        </button>


                    </td>
                </tr>
            <?php } ?>
        </tbody>

    </table>

    <!-- Modal hiển thị chi tiết đơn hàng -->
    <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewModalLabel">Chi Tiết Đơn Hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalContent">
                    <!-- Nội dung chi tiết đơn hàng sẽ được tải vào đây -->
                </div>
            </div>
        </div>
    </div>

</body>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/HMC/web/footer.php'); ?>
</footer>

</html>