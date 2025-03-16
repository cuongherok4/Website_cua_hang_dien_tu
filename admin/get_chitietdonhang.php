<?php
include 'autoload/Database.php';

if (isset($_GET['MaDH'])) {
    $maDH = $_GET['MaDH'];

    // Truy vấn lấy chi tiết đơn hàng + tên sản phẩm + hình ảnh
    $sql = "SELECT ct.MaSP, sp.TenSP, sp.HinhAnh, ct.SoLuong, ct.DonGia
            FROM chitietdonhang ct
            JOIN SanPham sp ON ct.MaSP = sp.MaSP
            WHERE ct.MaDH = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $maDH);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<table class='table table-bordered'>";
        echo "<tr>
                <th>Hình Ảnh</th>
                <th>Tên Sản Phẩm</th>
                <th>Số Lượng</th>
                <th>Đơn Giá</th>
                <th>Thành Tiền</th>
              </tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td><img src='" . $row['HinhAnh'] . "' alt='" . $row['TenSP'] . "' style='width: 80px; height: 80px; object-fit: cover;'></td>";
            echo "<td>" . $row['TenSP'] . "</td>";
            echo "<td>" . $row['SoLuong'] . "</td>";
            echo "<td>" . number_format($row['DonGia'], 0, ',', '.') . " đ</td>";
            echo "<td>" . number_format($row['SoLuong'] * $row['DonGia'], 0, ',', '.') . " đ</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Không có chi tiết đơn hàng nào.</p>";
    }
}
?>
