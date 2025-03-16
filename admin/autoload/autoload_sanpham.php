<?php
session_start(); // Bắt đầu session để lưu trạng thái

// THƯ VIỆN HỖ TRỢ NHẬP EXCEL
require 'vendor/autoload.php';  
use PhpOffice\PhpSpreadsheet\IOFactory;  

include 'Database.php';  // Kết nối cơ sở dữ liệu

// KIỂM TRA FILE UPLOAD
if (isset($_FILES['excelFile']) && $_FILES['excelFile']['error'] == 0) {
    $file = $_FILES['excelFile']['tmp_name'];

    // Kiểm tra định dạng file
    $fileType = pathinfo($_FILES['excelFile']['name'], PATHINFO_EXTENSION);
    if (!in_array($fileType, ['xlsx', 'xls'])) {
        $_SESSION['error'] = 'Chỉ chấp nhận file Excel (.xlsx hoặc .xls)';
        header('Location: /HMC/web/admin/sanpham.php');
        exit();
    }

    try {// Đọc tệp Excel
        // Đọc tệp Excel
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();

        $data = [];
        foreach ($sheet->getRowIterator() as $index => $row) {
            if ($index == 1) continue; // Bỏ qua cột đầu tiên (tiêu đề)

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $rowData = [];
            foreach ($cellIterator as $cell) {
                $rowData[] = $cell->getValue();
            }

            if (!empty($rowData[1])) { // Kiểm tra nếu tên sản phẩm không rỗng
                $data[] = $rowData;
            }
        }

        // Kiểm tra nếu không có dữ liệu
        if (empty($data)) {
            $_SESSION['error'] = 'File Excel không có dữ liệu!';
            header('Location: /HMC/web/admin/sanpham.php');
            exit();
        }

        // Nhập dữ liệu vào database
        foreach ($data as $row) {
            // Bỏ cột đầu tiên, lấy từ cột thứ 2 trở đi
            $tenSP = $row[1];
            $maLoaiSP = $row[2];
            $hinhAnh = $row[3];
            $donGia = $row[4];
            $soLuong = $row[5];
            $soSao = $row[6];
            $soDanhGia = $row[7];
            $km = $row[8];
            $giaSauKM = $row[9];
            $moTa = $row[10];
           
            $sql = "INSERT INTO sanpham (TenSP, MaLoaiSP, HinhAnh, DonGia, SoLuong, SoSao, SoDanhGia, KM, GiaSauKM, MoTa) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssdiiddds", $tenSP, $maLoaiSP, $hinhAnh, $donGia, $soLuong, $soSao, $soDanhGia, $km, $giaSauKM, $moTa);

            if (!$stmt->execute()) {
                $_SESSION['error'] = 'Lỗi khi nhập dữ liệu: ' . $stmt->error;
                header('Location: /HMC/web/admin/sanpham.php');
                exit();
            }
        }

        $_SESSION['success'] = 'Nhập dữ liệu thành công!';
        header('Location: /HMC/web/admin/sanpham.php');
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        header('Location: /HMC/web/admin/sanpham.php');
        exit();
    }
}



if ($_SERVER["REQUEST_METHOD"] == "POST") { // Kiểm tra xem yêu cầu HTTP là phương thức POST không (gửi dữ liệu từ form)
    // Nhận dữ liệu từ form gửi qua POST, nếu không có thì giá trị mặc định là null hoặc 0
    $masp = $_POST['masp'] ?? null;
    $tensp = $_POST['tensp'] ?? '';
    $maloaisp = $_POST['maloaisp'] ?? '';
    $hinhanh = $_POST['hinhanh'] ?? '';
    $dongia = $_POST['dongia'] ?? 0;
    $soluong = $_POST['soluong'] ?? 0;
    $sosao = $_POST['sosao'] ?? 0;
    $sodanhgia = $_POST['sodanhgia'] ?? 0;
    $km = $_POST['khuyenmai'] ?? 0;
    $giasaukm = $_POST['giasaukhuyemmai'] ?? 0;
    $mota = $_POST['mota'] ?? 0;

    // Kiểm tra trùng tên sản phẩm (nếu cần thiết) - Đây là nơi bạn có thể thêm logic kiểm tra trùng tên sản phẩm

    // Kiểm tra nếu mã sản phẩm có tồn tại (cập nhật sản phẩm) hay không (thêm mới sản phẩm)
    
    if (!empty($masp)) {
        // Nếu mã sản phẩm đã tồn tại, thực hiện câu lệnh UPDATE để sửa thông tin sản phẩm
        $sql = "UPDATE sanpham 
                SET TenSP=?, MaLoaiSP=?, HinhAnh=?, DonGia=?, SoLuong=?, SoSao=?, SoDanhGia=?, KM=?, GiaSauKM=?, MoTa=? 
                WHERE MaSP=?";
        $stmt = $conn->prepare($sql); // Chuẩn bị câu lệnh SQL để thực thi
        $stmt->bind_param("sisdiiiiisi", $tensp, $maloaisp, $hinhanh, $dongia, $soluong, $sosao, $sodanhgia, $km, $giasaukm, $mota, $masp); // Gán giá trị cho các tham số trong câu lệnh SQL

    } else {
        // Nếu không có mã sản phẩm (thêm mới), thực hiện câu lệnh INSERT
        $sql = "INSERT INTO sanpham (TenSP, MaLoaiSP, HinhAnh, DonGia, SoLuong, SoSao, SoDanhGia, KM, GiaSauKM,MoTa) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sisdiiiiis", $tensp, $maloaisp, $hinhanh, $dongia, $soluong, $sosao, $sodanhgia, $km, $giasaukm, $mota);
    }

    if ($stmt->execute()) { // Nếu câu lệnh SQL thực thi thành công
        $_SESSION['success'] = "Lưu sản phẩm thành công!"; // Thông báo thành công
    } else {
        $_SESSION['error'] = "Lỗi: " . $conn->error; // Nếu có lỗi, hiển thị thông báo lỗi
    }

    // Sau khi lưu dữ liệu, chuyển hướng về trang quản lý sản phẩm
    header("Location: /HMC/web/admin/sanpham.php");
    exit;
}

// Xử lý xóa sản phẩm khi yêu cầu là GET và có tham số 'id'
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $masp = $_GET['id']; // Lấy mã sản phẩm từ URL

    // Kiểm tra xem sản phẩm có tồn tại trong cơ sở dữ liệu không
    $check_sql = "SELECT * FROM sanpham WHERE MaSP = ?"; 
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("i", $masp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) { // Nếu tìm thấy sản phẩm, thực hiện thao tác xóa
        // Cập nhật trạng thái sản phẩm thành 'đã xóa' thay vì xóa hoàn toàn
        $update_sql = "UPDATE sanpham SET TrangThai = 1 WHERE MaSP = ?"; 
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("i", $masp);
        
        if ($stmt->execute()) { // Nếu câu lệnh UPDATE thành công
            $_SESSION['success'] = "Xóa sản phẩm thành công!"; // Thông báo thành công
        } else {
            $_SESSION['error'] = "Lỗi khi xóa!"; // Thông báo lỗi khi xóa thất bại
        }
    } else {
        $_SESSION['error'] = "Sản phẩm không tồn tại!"; // Thông báo sản phẩm không tồn tại
    }

    // Sau khi xử lý xóa hoặc cập nhật, chuyển hướng về trang quản lý sản phẩm
    header("Location: /HMC/web/admin/sanpham.php");
    exit;
}

$conn->close(); // Đóng kết nối với cơ sở dữ liệu
?>
