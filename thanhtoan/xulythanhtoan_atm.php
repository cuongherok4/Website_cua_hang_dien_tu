<?php
// Kiểm tra xem có dữ liệu từ form gửi lên hay không
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy các giá trị từ form
    $totalAmount = $_POST['TotalAmount']; // Tổng số tiền
    $nguoiNhan = $_POST['NguoiNhan']; // Người nhận
    $diaChi = $_POST['DiaChi']; // Địa chỉ
    $sdt = $_POST['SDT']; // Số điện thoại
    $phuongThucTT = $_POST['PhuongThucTT']; // Phương thức thanh toán

    // Lấy thông tin sản phẩm
    $products = [];
    if (isset($_POST['products']) && is_array($_POST['products'])) {
        foreach ($_POST['products'] as $product) {
            $products[] = [
                'MaGH' => $product['MaGH'],
                'TenSP' => $product['TenSP'],
                'HinhAnh' => $product['HinhAnh'],
                'SoLuong' => $product['SoLuong'],
                'DonGia' => $product['DonGia'],
                'TongTien' => $product['TongTien']
            ];
        }
    }

    // Bây giờ bạn có thể xử lý các dữ liệu trên, ví dụ: lưu vào cơ sở dữ liệu, gửi email, v.v.

    // Thiết lập kiểu nội dung của trang là HTML với bộ ký tự UTF-8
    header('Content-type: text/html; charset=utf-8');

    // Hàm thực hiện gửi yêu cầu HTTP POST
    function execPostRequest($url, $data)
    {
        // Khởi tạo cURL
        $ch = curl_init($url);

        // Thiết lập phương thức HTTP là POST
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

        // Thiết lập dữ liệu gửi đi
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        // Yêu cầu trả về kết quả thay vì hiển thị trực tiếp
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Thiết lập tiêu đề HTTP, bao gồm kiểu dữ liệu JSON và độ dài dữ liệu
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            )
        );

        // Thiết lập thời gian chờ tối đa là 5 giây
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        // Thực thi yêu cầu HTTP POST
        $result = curl_exec($ch);

        // Đóng kết nối cURL
        curl_close($ch);

        // Trả về kết quả từ API
        return $result;
    }

    // URL API của MoMo để tạo yêu cầu thanh toán
    $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";

    // Các thông tin xác thực do MoMo cung cấp
    $partnerCode = 'MOMOBKUN20180529';
    $accessKey = 'klm05TvNBzhg7h7j';
    $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';

    // Thông tin đơn hàng
    $orderInfo = "Thanh toán qua MoMo - ATM";
    $amount = $totalAmount; // Sử dụng tổng số tiền từ form
    $orderId = time() . ""; // Mã đơn hàng (sử dụng timestamp để tạo mã duy nhất)
    $redirectUrl = "http://localhost/HMC/web/donmua.php"; // URL chuyển hướng sau khi thanh toán
    $ipnUrl = "http://localhost/HMC/web/donmua.php"; // URL nhận thông báo kết quả thanh toán từ MoMo
    $extraData = ""; // Dữ liệu bổ sung nếu có

    // Tạo requestId (mã yêu cầu) duy nhất dựa trên timestamp
    $requestId = time() . "";

    // Loại yêu cầu thanh toán (ở đây là thanh toán qua ATM)
    $requestType = "payWithATM";

    // Lấy dữ liệu extraData từ form (nếu có)
    $extraData = ($_POST["extraData"] ? $_POST["extraData"] : "");

    // Chuỗi dữ liệu trước khi mã hóa HMAC SHA256
    $rawHash = "accessKey=" . $accessKey .
        "&amount=" . $amount .
        "&extraData=" . $extraData .
        "&ipnUrl=" . $ipnUrl .
        "&orderId=" . $orderId .
        "&orderInfo=" . $orderInfo .
        "&partnerCode=" . $partnerCode .
        "&redirectUrl=" . $redirectUrl .
        "&requestId=" . $requestId .
        "&requestType=" . $requestType;

    // Tạo chữ ký số bằng thuật toán HMAC SHA256
    $signature = hash_hmac("sha256", $rawHash, $secretKey);

    // Tạo mảng dữ liệu gửi đi
    $data = array(
        'partnerCode' => $partnerCode,
        'partnerName' => "Test",
        "storeId" => "MomoTestStore",
        'requestId' => $requestId,
        'amount' => $amount,
        'orderId' => $orderId,
        'orderInfo' => $orderInfo,
        'redirectUrl' => $redirectUrl,
        'ipnUrl' => $ipnUrl,
        'lang' => 'vi', // Ngôn ngữ hiển thị trên giao diện thanh toán MoMo
        'extraData' => $extraData,
        'requestType' => $requestType,
        'signature' => $signature
    );

    // Gửi yêu cầu đến API MoMo và nhận phản hồi
    $result = execPostRequest($endpoint, json_encode($data));

    // Giải mã chuỗi JSON trả về thành mảng PHP
    $jsonResult = json_decode($result, true);

    // Chuyển hướng người dùng đến URL thanh toán MoMo
    header('Location: ' . $jsonResult['payUrl']);
    
}
?>
