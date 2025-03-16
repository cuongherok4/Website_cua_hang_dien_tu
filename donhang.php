<?php
session_start();
include '../web/admin/autoload/Database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['MaND'])) {
        echo "<script>alert('Bạn chưa đăng nhập!'); window.location.href='../web/login/login.php';</script>";
        exit();
    }

    $MaND = $_SESSION['MaND'];
    $products = [];

    foreach ($_POST['MaGH'] as $key => $MaGH) {
        $SoLuong = $_POST['SoLuong'][$key];
        $DonGia = $_POST['DonGia'][$key];
        $TongTien = $_POST['TongTien'][$key];
        $TenSP = $_POST['TenSP'][$key];
        $HinhAnh = $_POST['HinhAnh'][$key];

        $products[] = [
            "MaGH" => $MaGH,
            "TenSP" => $TenSP,
            "HinhAnh" => $HinhAnh,
            "SoLuong" => $SoLuong,
            "DonGia" => $DonGia,
            "TongTien" => $TongTien
        ];
    }

    $TotalAmount = $_POST['TotalAmount'];

    $_SESSION['donhang'] = [
        "products" => $products,
        "TotalAmount" => $TotalAmount,
        // "NguoiNhan" => $_POST['NguoiNhan'] ?? "Không có thông tin",
        // "DiaChi" => $_POST['DiaChi'] ?? "0 địa chỉ",
        // "SDT" => $_POST['SDT'] ?? "Không có số điện thoại",
        // "PhuongThucTT" => $_POST['payment'] ?? "COD"
    ];
}

if (isset($_SESSION['donhang'])) {
    $donhang = $_SESSION['donhang'];
    $products = $donhang['products'];
    $TotalAmount = $donhang['TotalAmount'];
} else {
    echo "Không có sản phẩm nào!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn hàng</title>
    <link rel="stylesheet" href="/HMC/web/public/client/css/donhang.css">
</head>
<style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        font-family: Arial, sans-serif;
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: center;
    }

    th {
        background-color: #f8f9fa;
        font-weight: bold;
    }

    td img {
        border-radius: 5px;
    }

    tr:hover {
        background-color: #f1f1f1;
    }

    .total-row {
        background-color: #e9ecef;
        font-size: 18px;
        font-weight: bold;
    }

    .total-label {
        text-align: right;
        padding-right: 20px;
    }

    .payment-buttons {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }

    .payment-buttons form {
        flex: 1;
    }

    .payment-buttons input[type="submit"] {
        width: 100%;
        padding: 10px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
    }

    .payment-buttons input[name="momo"] {
        background-color: #d32f2f;
        color: white;
    }

    .payment-buttons input[name="atm"] {
        background-color: #007bff;
        color: white;
    }

    .payment-buttons input[type="submit"]:hover {
        opacity: 0.9;
    }
</style>

<body>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/HMC/web/header.php'); ?>
    <form method="post" action="xuly_dathang.php">
        <div class="container">
            <div class="left">
                <h3>Thông tin thanh toán</h3>

                <div class="form-group">
                    <label>Người nhận</label>
                    <input type="text" name="NguoiNhan" placeholder="Nhập tên người nhận" required>
                </div>
                <div class="form-group">
                    <label>Địa chỉ</label>
                    <input type="text" name="DiaChi" placeholder="Nhập địa chỉ nhận hàng" required>
                </div>
                <div class="form-group">
                    <label>Số điện thoại</label>
                    <input type="text" name="SDT" placeholder="Nhập số điện thoại" required>
                </div>

            </div>

            <div class="right">
                <h3>Đơn đặt hàng</h3>
                <div class="order-summary">


                    <table>
                        <tr>
                            <th>Ảnh</th>
                            <th>Sản phẩm</th>
                            <th>Đơn giá</th>
                            <th>Số lượng</th>
                            <th>Tổng tiền</th>
                        </tr>
                        <?php foreach ($products as $product) { ?>
                            <tr>
                                <td><img src="<?= htmlspecialchars($product["HinhAnh"]) ?>" width="50" height="50"></td>
                                <td><?= htmlspecialchars($product["TenSP"]) ?></td>
                                <td><?= number_format($product["DonGia"], 0, ',', '.') ?>đ</td>
                                <td><?= $product["SoLuong"] ?></td>
                                <td><?= number_format($product["TongTien"], 0, ',', '.') ?>đ</td>
                            </tr>
                        <?php } ?>
                        <tr class="total-row">
                            <td colspan="4" class="total-label">Tổng tiền thanh toán:</td>
                            <td>₫<?= number_format($TotalAmount, 0, ',', '.') ?></td>
                        </tr>
                    </table>

                    </table>
                </div>

                <div class="payment-methods">

                    <h4>Phương thức thanh toán</h4>
                    <input type="radio" name="PhuongThucTT" value="Thanh toán khi nhận" checked> Thanh toán khi nhận
                    hàng
                    <br>
                    <input type="radio" name="PhuongThucTT" value="Thanh toán bằng Zalopay"> Thanh toán bằng Zalopay

                </div>
                <button type="submit" class="submit-btn">Đặt hàng</button>
            </div>
        </div>
        </div>
    </form>

    <div class="payment-buttons">
        <!-- Thanh toán MOMO -->
        <form method="post" target="_blank" action="/HMC/web/thanhtoan/xulythanhtoan_momo.php">
            <input type="hidden" name="TotalAmount" value="<?= $TotalAmount ?>">
            <input type="hidden" name="NguoiNhan" value="<?= $_POST['NguoiNhan'] ?? '' ?>">
            <input type="hidden" name="DiaChi" value="<?= $_POST['DiaChi'] ?? '' ?>">
            <input type="hidden" name="SDT" value="<?= $_POST['SDT'] ?? '' ?>">
            <input type="hidden" name="PhuongThucTT" value="MOMO">

            <?php foreach ($products as $index => $product) { ?>
                <input type="hidden" name="products[<?= $index ?>][MaGH]" value="<?= $product["MaGH"] ?>">
                <input type="hidden" name="products[<?= $index ?>][TenSP]" value="<?= $product["TenSP"] ?>">
                <input type="hidden" name="products[<?= $index ?>][HinhAnh]" value="<?= $product["HinhAnh"] ?>">
                <input type="hidden" name="products[<?= $index ?>][SoLuong]" value="<?= $product["SoLuong"] ?>">
                <input type="hidden" name="products[<?= $index ?>][DonGia]" value="<?= $product["DonGia"] ?>">
                <input type="hidden" name="products[<?= $index ?>][TongTien]" value="<?= $product["TongTien"] ?>">
            <?php } ?>

            <input type="submit" name="momo" value="Thanh toán MOMO QRcode">
        </form>

        <!-- Thanh toán ATM -->
        <form method="post" target="_blank" action="/HMC/web/thanhtoan/xulythanhtoan_atm.php">
            <input type="hidden" name="TotalAmount" value="<?= $TotalAmount ?>">
            <input type="hidden" name="NguoiNhan" value="<?= $_POST['NguoiNhan'] ?? '' ?>">
            <input type="hidden" name="DiaChi" value="<?= $_POST['DiaChi'] ?? '' ?>">
            <input type="hidden" name="SDT" value="<?= $_POST['SDT'] ?? '' ?>">
            <input type="hidden" name="PhuongThucTT" value="ATM">
            <?php foreach ($products as $index => $product) { ?>
                <input type="hidden" name="products[<?= $index ?>][MaGH]" value="<?= $product["MaGH"] ?>">
                <input type="hidden" name="products[<?= $index ?>][TenSP]" value="<?= $product["TenSP"] ?>">
                <input type="hidden" name="products[<?= $index ?>][HinhAnh]" value="<?= $product["HinhAnh"] ?>">
                <input type="hidden" name="products[<?= $index ?>][SoLuong]" value="<?= $product["SoLuong"] ?>">
                <input type="hidden" name="products[<?= $index ?>][DonGia]" value="<?= $product["DonGia"] ?>">
                <input type="hidden" name="products[<?= $index ?>][TongTien]" value="<?= $product["TongTien"] ?>">
            <?php } ?>

            <input type="submit" name="atm" value="Thanh toán ATM">
        </form>
    </div>


</body>

<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/HMC/web/footer.php'); ?>
</footer>

</html>