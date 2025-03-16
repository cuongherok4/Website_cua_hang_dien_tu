// Lắng nghe sự kiện khi toàn bộ nội dung trang web được tải xong
window.addEventListener('DOMContentLoaded', event => {
    // Sử dụng thư viện Simple-DataTables
    // Tài liệu: https://github.com/fiduswriter/Simple-DataTables/wiki

    // Lấy phần tử có id 'datatablesSimple' trong trang
    const datatablesSimple = document.getElementById('datatablesSimple');

    // Kiểm tra xem phần tử này có tồn tại không
    if (datatablesSimple) {
        // Khởi tạo bảng dữ liệu bằng thư viện Simple-DataTables
        new simpleDatatables.DataTable(datatablesSimple);
    }
});
