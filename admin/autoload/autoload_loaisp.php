<?php
session_start();  // Khởi động phiên làm việc (session) để lưu trữ dữ liệu tạm thời

// THƯ VIỆN HỖ TRỢ NHẬP EXCEL
require 'vendor/autoload.php';  // Tải thư viện hỗ trợ xử lý file Excel
use PhpOffice\PhpSpreadsheet\IOFactory;  // Sử dụng lớp IOFactory từ PhpSpreadsheet để đọc và ghi file Excel


include 'Database.php';  // Kết nối cơ sở dữ liệu


// NHẬP EXCEL
if (isset($_FILES['excelFile']) && $_FILES['excelFile']['error'] == 0) {  // Kiểm tra xem file Excel có được gửi lên không và không có lỗi
    $file = $_FILES['excelFile']['tmp_name'];  // Lấy tên tạm thời của file Excel

    // Kiểm tra định dạng file
    $fileType = pathinfo($_FILES['excelFile']['name'], PATHINFO_EXTENSION);  // Lấy phần mở rộng của file
    if ($fileType !== 'xlsx' && $fileType !== 'xls') {  // Kiểm tra nếu file không phải dạng Excel (.xlsx hoặc .xls)
        $_SESSION['error'] = 'Chỉ chấp nhận file Excel (.xlsx hoặc .xls)';  // Lưu thông báo lỗi vào session
        header('Location: /HMC/web/admin/loaisp.php');  // Điều hướng về trang hiện tại
        exit();  // Dừng thực thi mã tiếp theo
    }

    try {
        // Đọc tệp Excel
        $spreadsheet = IOFactory::load($file);  // Đọc file Excel
        $sheet = $spreadsheet->getActiveSheet();  // Lấy sheet đang hoạt động trong file Excel

        // Lấy dữ liệu từ Excel
        $data = [];  // Khởi tạo mảng chứa dữ liệu từ file Excel
        foreach ($sheet->getRowIterator() as $row) {  // Lặp qua từng dòng trong sheet
            $cellIterator = $row->getCellIterator();  // Lấy iterator cho các ô trong dòng
            $cellIterator->setIterateOnlyExistingCells(false);  // Đảm bảo lặp qua tất cả các ô, không chỉ ô có dữ liệu

            $rowData = [];  // Mảng chứa dữ liệu của một dòng
            foreach ($cellIterator as $cell) {  // Lặp qua các ô trong dòng
                $rowData[] = $cell->getValue();  // Lưu giá trị của mỗi ô vào mảng rowData
            }
            $data[] = $rowData;  // Thêm dòng dữ liệu vào mảng dữ liệu tổng
        }

        // Kiểm tra dữ liệu có tồn tại
        if (empty($data)) {  // Nếu không có dữ liệu trong file
            $_SESSION['error'] = 'File Excel không có dữ liệu!';  // Lưu thông báo lỗi
            header('Location: /HMC/web/admin/loaisp.php');  // Điều hướng về trang hiện tại
            exit();  // Dừng thực thi mã tiếp theo
        }

        array_shift($data);  // Bỏ dòng tiêu đề (dòng đầu tiên)

        // Nhập dữ liệu vào cơ sở dữ liệu
        foreach ($data as $row) {  // Lặp qua từng dòng dữ liệu
            $maLoaiSP = $row[1];  // Lấy mã loại sản phẩm từ cột THỨ2
            $tenLoaiSP = $row[1]; // Lấy tên loại sản phẩm từ cột thứ hai

            // Thực hiện câu lệnh SQL để thêm dữ liệu vào bảng loaisanpham
            $sql = "INSERT INTO loaisanpham (MaLoaiSP, TenLoaiSP) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);  // Chuẩn bị câu lệnh SQL
            $stmt->bind_param("ss", $maLoaiSP, $tenLoaiSP);  // Liên kết tham số với câu lệnh SQL

            if ($stmt->execute()) {  // Thực thi câu lệnh SQL
                $_SESSION['success'] = 'Dữ liệu đã được nhập thành công từ Excel!';  // Nếu thành công, lưu thông báo thành công
            } else {
                $_SESSION['error'] = 'Lỗi khi nhập dữ liệu: ' . $stmt->error;  // Nếu thất bại, lưu thông báo lỗi
            }
        }
    } catch (Exception $e) {  // Xử lý lỗi nếu có
        $_SESSION['error'] = 'Đã xảy ra lỗi khi nhập dữ liệu: ' . $e->getMessage();  // Lưu thông báo lỗi vào session
    }
}




// Kiểm tra nếu có yêu cầu POST từ form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Nhận dữ liệu từ form
    $loai_id = $_POST['user_id'] ?? null;  // Lấy mã loại sản phẩm từ form, nếu không có thì null
    $tenloai = $_POST['tenloai'] ?? '';  // Lấy tên loại sản phẩm từ form

    // Kiểm tra trùng Tên Loại Sản Phẩm
    $sql_check_ten = "SELECT * FROM loaisanpham WHERE TenLoaiSP = ?";  // Câu lệnh kiểm tra tên loại sản phẩm đã tồn tại
    if (!empty($loai_id)) {
        $sql_check_ten .= " AND MaLoaiSP != ?";  // Nếu có mã loại sản phẩm, kiểm tra trùng tên loại trừ chính bản ghi đó
    }
    $stmt = $conn->prepare($sql_check_ten);  // Chuẩn bị câu lệnh SQL
    if (!empty($loai_id)) {
        $stmt->bind_param("si", $tenloai, $loai_id);  // Gắn giá trị tham số cho câu lệnh SQL
    } else {
        $stmt->bind_param("s", $tenloai);  // Chỉ cần tên loại sản phẩm
    }
    $stmt->execute();  // Thực thi câu lệnh SQL
    $result_ten = $stmt->get_result();  // Lấy kết quả trả về

    if ($result_ten->num_rows > 0) {  // Nếu có kết quả trả về (tên loại sản phẩm đã tồn tại)
        $_SESSION['error'] = "Tên loại sản phẩm đã tồn tại!";  // Lưu thông báo lỗi
        header("Location: /HMC/web/admin/loaisp.php");  // Điều hướng về trang hiện tại
        exit;  // Dừng thực thi mã tiếp theo
    }

    // Xử lý cập nhật hoặc thêm mới
    if (!empty($loai_id)) {  // Nếu có mã loại sản phẩm (cập nhật)
        $sql = "UPDATE loaisanpham SET TenLoaiSP=? WHERE MaLoaiSP=?";
        $stmt = $conn->prepare($sql);  // Chuẩn bị câu lệnh SQL
        $stmt->bind_param("si", $tenloai, $loai_id);  // Gắn giá trị tham số cho câu lệnh SQL
    } else {  // Nếu không có mã loại sản phẩm (thêm mới)
        $sql = "INSERT INTO loaisanpham (TenLoaiSP) VALUES (?)";
        $stmt = $conn->prepare($sql);  // Chuẩn bị câu lệnh SQL
        $stmt->bind_param("s", $tenloai);  // Gắn giá trị tham số cho câu lệnh SQL
    }

    if ($stmt->execute()) {  // Thực thi câu lệnh SQL
        $_SESSION['success'] = "Lưu thành công!";  // Nếu thành công, lưu thông báo thành công
    } else {
        $_SESSION['error'] = "Lỗi: " . $conn->error;  // Nếu thất bại, lưu thông báo lỗi
    }

    header("Location: /HMC/web/admin/loaisp.php");  // Điều hướng về trang hiện tại
    exit;
}

// Xử lý xóa loại sản phẩm
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {  // Kiểm tra nếu có yêu cầu GET và có tham số id
    $loai_id = $_GET['id'];  // Lấy mã loại sản phẩm từ tham số GET

    // Kiểm tra xem MaLoaiSP có tồn tại không
    $check_sql = "SELECT * FROM loaisanpham WHERE MaLoaiSP = ?";  // Câu lệnh kiểm tra sự tồn tại của loại sản phẩm
    $stmt = $conn->prepare($check_sql);  // Chuẩn bị câu lệnh SQL
    $stmt->bind_param("i", $loai_id);  // Gắn giá trị tham số cho câu lệnh SQL
    $stmt->execute();  // Thực thi câu lệnh SQL
    $result = $stmt->get_result();  // Lấy kết quả trả về

    if ($result->num_rows > 0) {  // Nếu tồn tại loại sản phẩm
        // Nếu tồn tại, thực hiện xóa
        $delete_sql = "DELETE FROM loaisanpham WHERE MaLoaiSP = ?";  // Câu lệnh xóa loại sản phẩm
        $stmt = $conn->prepare($delete_sql);  // Chuẩn bị câu lệnh SQL
        $stmt->bind_param("i", $loai_id);  // Gắn giá trị tham số cho câu lệnh SQL
        if ($stmt->execute()) {  // Thực thi câu lệnh SQL
            $_SESSION['success'] = "Xóa thành công!";  // Nếu thành công, lưu thông báo thành công
        } else {
            $_SESSION['error'] = "Lỗi khi xóa!";  // Nếu thất bại, lưu thông báo lỗi
        }
    } else {
        $_SESSION['error'] = "Loại sản phẩm không tồn tại!";  // Nếu loại sản phẩm không tồn tại, lưu thông báo lỗi
    }

    header("Location: /HMC/web/admin/loaisp.php");  // Điều hướng về trang hiện tại
    exit;
}

$conn->close();  // Đóng kết nối cơ sở dữ liệu

header('Location: loaisp.php'); // Chuyển hướng lại trang sau khi nhập
exit;
?>
