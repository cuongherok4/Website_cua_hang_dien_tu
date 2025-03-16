<?php
require 'autoload/vendor/autoload.php';
include 'autoload/Database.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (isset($_POST['chartType'])) {
    $chartType = $_POST['chartType'];
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    if ($chartType == 'tongtien') {
        $sql = "SELECT TrangThai, SUM(TongTien) AS TongTien FROM donhang GROUP BY TrangThai";
        $sheet->setCellValue('A1', 'Trạng thái');
        $sheet->setCellValue('B1', 'Tổng tiền (VND)');
    } elseif ($chartType == 'soluongsanpham') {
        $sql = "SELECT TrangThai, COUNT(*) AS SoLuong FROM donhang GROUP BY TrangThai";
        $sheet->setCellValue('A1', 'Trạng thái');
        $sheet->setCellValue('B1', 'Số lượng đơn hàng');
    } elseif ($chartType == 'sanphamtonban') {
        $sql = "SELECT SUM(SoLuong) AS TongTonKho FROM sanpham";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $tong_ton_kho = $row['TongTonKho'];

        $sql = "SELECT SUM(SoLuong) AS TongBan FROM chitietdonhang";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $tong_da_ban = $row['TongBan'];

        $sheet->setCellValue('A1', 'Loại');
        $sheet->setCellValue('B1', 'Số lượng');
        $sheet->setCellValue('A2', 'Tồn kho');
        $sheet->setCellValue('B2', $tong_ton_kho);
        $sheet->setCellValue('A3', 'Đã bán');
        $sheet->setCellValue('B3', $tong_da_ban);
    } else {
        die("Lựa chọn không hợp lệ.");
    }

    if ($chartType != 'sanphamtonban') {
        $result = $conn->query($sql);
        $rowIndex = 2;
        while ($row = $result->fetch_assoc()) {
            $sheet->setCellValue("A$rowIndex", $row['TrangThai']);
            $sheet->setCellValue("B$rowIndex", $rowType == 'tongtien' ? number_format($row['TongTien'], 0, ',', '.') : $row['SoLuong']);
            $rowIndex++;
        }
    }

    $fileName = "ThongKe_".date("Y-m-d").".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$fileName\"");

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}
?>
