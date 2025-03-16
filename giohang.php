<?php
session_start();
include '../web/admin/autoload/Database.php';

if (!isset($_SESSION['MaND'])) {
    echo "<script>alert('Bạn chưa đăng nhập! Vui lòng đăng nhập!'); window.history.back();;</script>";
    exit();
}

$MaND = $_SESSION['MaND'];

$sql = "SELECT g.MaGH, g.MaSP, g.MaND, g.SoLuong, g.DonGia, s.TenSP, s.HinhAnh
        FROM giohang g 
        JOIN sanpham s ON g.MaSP = s.MaSP
        WHERE g.MaND = ? AND (g.TrangThai IS NULL OR g.TrangThai = '')";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['action'] == "update") {
        $MaGH = $_POST['MaGH'];
        $SoLuong = $_POST['SoLuong'];
        $updateQuery = "UPDATE giohang SET SoLuong = ? WHERE MaGH = ? AND MaND = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("iii", $SoLuong, $MaGH, $MaND);
        $stmt->execute();
        echo "success";
        exit();
    }

    if ($_POST['action'] == "delete") {
        $MaGH = $_POST['MaGH'];
        $deleteQuery = "DELETE FROM giohang WHERE MaGH = ? AND MaND = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("ii", $MaGH, $MaND);
        $stmt->execute();
        echo "deleted";
        exit();
    }
}
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $MaND);
$stmt->execute();
$result = $stmt->get_result();


$sql_loaisanpham = "SELECT * FROM loaisanpham";
$result_loaisanpham = $conn->query($sql_loaisanpham);


?>


<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng</title>
    <link rel="stylesheet" href="/HMC/web/public/client/css/header_footer.css">
    <link rel="stylesheet" href="/HMC/web/public/client/css/giohang.css">


</head>

<body>
    <!-- header_menu -->
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/HMC/web/header.php'); ?>

    <!-- giỏ hànghàng -->
    <h1>Giỏ hàng của bạn</h1>
    <table>
        <thead>
            <tr>
                <th><input type="checkbox" id="selectAll"></th>
                <th>Tên sản phẩm</th>
                <th>Hình ảnh</th>
                <th>Đơn giá</th>
                <th>Số lượng</th>
                <th>Số tiền</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php
                    $MaGH = $row["MaGH"];
                    $TenSP = $row["TenSP"];
                    $HinhAnh = $row["HinhAnh"];
                    $SoLuong = $row["SoLuong"];
                    $DonGia = $row["DonGia"];
                    $TongTien = $SoLuong * $DonGia;
                    ?>
                    <tr data-id="<?= $MaGH ?>">
                        <td><input type="checkbox" class="product-checkbox" data-price="<?= $TongTien ?>"></td>
                        <td><?= $TenSP ?></td>
                        <td>
                            <img src="<?= $row['HinhAnh'] ?>" alt="Hình ảnh sản phẩm" width="80" height="80">
                        </td>
                        <td><?= number_format($DonGia, 0, ',', '.') ?>đ</td>
                        <td class="quantity-container">
                            <button class="minus" data-id="<?= $MaGH ?>">-</button>
                            <span class="quantity" data-id="<?= $MaGH ?>" data-price="<?= $DonGia ?>"><?= $SoLuong ?></span>
                            <button class="plus" data-id="<?= $MaGH ?>">+</button>
                        </td>
                        <td class="total-price"><?= number_format($TongTien, 0, ',', '.') ?>đ</td>
                        <td><a href="#" class="delete" data-id="<?= $MaGH ?>">Xóa</a></td>
                    </tr>
                <?php endwhile; ?>
                <tr>
                    <td colspan="6" class="total">
                        <span>Tổng tiền: <span id="totalAmount">0</span>đ</span>
                    </td>
                    <td><button id="pay">Thanh toán</button></td>
                </tr>
            <?php else: ?>
                <tr>
                    <td colspan="6">Giỏ hàng trống</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const selectAllCheckbox = document.getElementById("selectAll");
            const checkboxes = document.querySelectorAll(".product-checkbox");
            const totalSpan = document.getElementById("totalAmount");
            const payButton = document.getElementById("pay");

            function updateTotal() {
                let total = 0;
                let hasChecked = false;

                checkboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        hasChecked = true;
                        let row = checkbox.closest("tr");
                        let quantity = parseInt(row.querySelector(".quantity").textContent);
                        let price = parseInt(row.querySelector(".quantity").getAttribute("data-price"));
                        total += quantity * price;
                    }
                });

                totalSpan.textContent = total.toLocaleString("vi-VN");
                payButton.disabled = !hasChecked;
            }

            selectAllCheckbox.addEventListener("change", function () {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
                updateTotal();
            });

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener("change", function () {
                    selectAllCheckbox.checked = [...checkboxes].every(cb => cb.checked);
                    updateTotal();
                });
            });

            document.querySelectorAll(".plus").forEach(button => {
                button.addEventListener("click", function () {
                    let row = this.closest("tr");
                    let quantitySpan = row.querySelector(".quantity");
                    let quantity = parseInt(quantitySpan.textContent);
                    let price = parseInt(quantitySpan.getAttribute("data-price"));
                    quantity++;
                    quantitySpan.textContent = quantity;
                    row.querySelector(".total-price").textContent = (quantity * price).toLocaleString("vi-VN") + "đ";
                    updateDatabase(this.getAttribute("data-id"), quantity);
                    updateTotal(); // Cập nhật lại tổng tiền sau khi thay đổi số lượng
                });
            });

            document.querySelectorAll(".minus").forEach(button => {
                button.addEventListener("click", function () {
                    let row = this.closest("tr");
                    let quantitySpan = row.querySelector(".quantity");
                    let quantity = parseInt(quantitySpan.textContent);
                    let price = parseInt(quantitySpan.getAttribute("data-price"));
                    if (quantity > 1) {
                        quantity--;
                        quantitySpan.textContent = quantity;
                        row.querySelector(".total-price").textContent = (quantity * price).toLocaleString("vi-VN") + "đ";
                        updateDatabase(this.getAttribute("data-id"), quantity);
                        updateTotal(); // Cập nhật lại tổng tiền sau khi thay đổi số lượng
                    }
                });
            });

            document.querySelectorAll(".delete").forEach(link => {
                link.addEventListener("click", function (e) {
                    e.preventDefault();
                    if (confirm("Bạn có chắc chắn muốn xóa sản phẩm này?")) {
                        let MaGH = this.getAttribute("data-id");
                        fetch("giohang.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded"
                            },
                            body: `action=delete&MaGH=${MaGH}`
                        })
                            .then(response => response.text())
                            .then(data => {
                                if (data.trim() === "deleted") {
                                    this.closest("tr").remove();
                                    updateTotal();
                                }
                            });
                    }
                });
            });

            function updateDatabase(MaGH, SoLuong) {
                fetch("giohang.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `action=update&MaGH=${MaGH}&SoLuong=${SoLuong}`
                });
            }
        });

        document.getElementById("pay").addEventListener("click", function () {
    let selectedItems = [];
    let totalAmount = 0;

    document.querySelectorAll(".product-checkbox:checked").forEach(checkbox => {
        let row = checkbox.closest("tr");
        let MaGH = row.getAttribute("data-id");
        let quantity = row.querySelector(".quantity").textContent;
        let price = parseInt(row.querySelector(".quantity").getAttribute("data-price"));
        let total = quantity * price;
        let image = row.querySelector("img").getAttribute("src"); // Lấy đường dẫn ảnh
        let name = row.querySelector("td:nth-child(2)").textContent.trim(); // Lấy tên sản phẩm từ cột thứ 2

        totalAmount += total;
        selectedItems.push({
            MaGH,
            quantity,
            price,
            total,
            image,
            name
        });
    });

    if (selectedItems.length === 0) {
        alert("Vui lòng chọn ít nhất một sản phẩm để thanh toán!");
        return;
    }

    // Gửi dữ liệu bằng FormData thay vì URL
    let formData = new FormData();
    selectedItems.forEach((item, index) => {
        formData.append(`MaGH[${index}]`, item.MaGH);
        formData.append(`SoLuong[${index}]`, item.quantity);
        formData.append(`DonGia[${index}]`, item.price);
        formData.append(`TongTien[${index}]`, item.total);
        formData.append(`HinhAnh[${index}]`, item.image);
        formData.append(`TenSP[${index}]`, item.name);
    });

    formData.append("TotalAmount", totalAmount);

    fetch("donhang.php", {
        method: "POST",
        body: formData
    })
        .then(response => response.text())
        .then(data => {
            window.location.href = "donhang.php";
        });
});

    </script>

</body>

<footer>
    <!-- footer -->
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/HMC/web/footer.php'); ?>
</footer>

</html>