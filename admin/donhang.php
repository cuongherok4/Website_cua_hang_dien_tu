<?php
session_start();
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
<!-- Bootstrap Icons (Cần thêm vào trang) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<?php
include 'autoload/Database.php';
$sql = "SELECT * FROM donhang";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Tables - SB Admin</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="/HMC/web/public/admin/css/styles.css" rel="stylesheet" />
    <link href="/HMC/web/public/admin/css/btn.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="index.php">SERVER</a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
        <!-- Add new-->
        <!-- Add new-->
        <!-- Add new and Navbar Search in the same row -->
        <div class="d-flex align-items-center ms-auto me-0 me-md-3 my-2 my-md-0">
            <!-- Add New -->
            <button class="btn btnchung btn-success" id="btnAddNew" type="button" data-bs-toggle="modal" data-bs-target="#userModal">
                <i class="fas fa-plus"></i> Thêm Mới
            </button>
            <!-- Navbar Search -->
            <button class="btn btnchung btn-primary" id="btnReload" type="button" data-bs-toggle="modal" data-bs-target="#userModal" onclick="location.reload();">
                <i class="fas fa-sync-alt"></i> Làm mới
            </button>
            <button class="btn btnchung btn-warning" id="btnExportExcel" type="button">
                <i class="bi bi-file-earmark-arrow-down me-2"></i> Xuất Excel
            </button>
        </div>

        <!-- Add New Modal -->
        <!-- Navbar-->
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="#!">Settings</a></li>
                    <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li><a class="dropdown-item" href="../login/logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Home</div>
                        <a class="nav-link" href="index.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                            Trang chủ
                        </a>
                        <div class="sb-sidenav-menu-heading">Dữ liệu</div>
                        <a class="nav-link" href="sanpham.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-box"></i></div>
                            Sản phẩm
                        </a>
                        <a class="nav-link" href="donhang.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-shopping-cart"></i></div>
                            Đơn hàng
                        </a>
                        <a class="nav-link" href="taikhoan.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                            Tài khoản
                        </a>
                        <a class="nav-link" href="thongtin.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-address-book"></i></div>
                            Thông tin liên hệ
                        </a>
                        <a class="nav-link" href="tintuc.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-newspaper"></i></div>
                            Tin tức
                        </a>
                        <a class="nav-link" href="loaisp.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tags"></i></div>
                            Loại sản phẩm
                        </a>
                        <div class="sb-sidenav-menu-heading">Data</div>
                        <a class="nav-link" href="thongke.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-chart-bar"></i></div>
                            Thống kê
                        </a>

                    </div>
                </div>

            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Đơn hàng</h1>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Bảng dữ liệu
                        </div>
                        <div class="card-body">
                            <table id="datatablesSimple" class="table table-hover table-bordered text-center align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Mã ĐH</th>
                                        <th>Mã ND</th>
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
                                            <td><?= $row['MaDH'] ?></td>
                                            <td><?= $row['MaND'] ?></td>
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
                                                    ✏️ Sửa
                                                </button>

                                                <a href="autoload/autoload_donhang.php?id=<?= $row['MaDH'] ?>" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Bạn có chắc muốn xóa?');">
                                                    🗑️ Xóa
                                                </a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>

                            </table>
                        </div>

                    </div>
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
            </footer>
        </div>
    </div>

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


    <!-- Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Đơn hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="autoload/autoload_donhang.php" method="POST">
                        <input type="hidden" id="madh" name="madh">

                        <select class="form-control" id="mand" name="mand" required>
                            <option value="">-- Chọn mã người dùng --</option>
                            <?php
                            include 'db_connect.php'; // Kết nối CSDL
                            $query = "SELECT MaND, HoTen FROM nguoidung";
                            $result = mysqli_query($conn, $query);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<option value="' . $row['MaND'] . '">' . $row['HoTen'] . '</option>';
                            }
                            ?>
                        </select>

                        <div class="mb-3">
                            <label for="hinhanh" class="form-label">Ngày lập</label>
                            <input type="text" class="form-control" id="ngaylap" name="ngaylap" required>
                        </div>
                        <div class="mb-3">
                            <label for="dongia" class="form-label">Người nhận</label>
                            <input type="text" class="form-control" id="nguoinhan" name="nguoinhan" required>
                        </div>
                        <div class="mb-3">
                            <label for="soluong" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" id="sdt" name="sdt">
                        </div>
                        <div class="mb-3">
                            <label for="sosao" class="form-label">Địa chỉ</label>
                            <input type="text" class="form-control" id="diachi" name="diachi" required>
                        </div>

                        <div class="mb-3">
                            <label for="trangthai" class="form-label">Phương thức tt</label>
                            <select class="form-control" id="pttt" name="pttt" required>
                                <option value="Thanh toán qua zalophlay">Thanh toán qua zaloplay</option>
                                <option value="Thanh toán khi nhận">Thanh toán khi nhận</option>

                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="khuyenmai" class="form-label">Tổng tiền</label>
                            <input type="text" class="form-control" id="tongtien" name="tongtien" required>
                        </div>

                        <div class="mb-3">
                            <label for="trangthai" class="form-label">Trạng thái</label>
                            <select class="form-control" id="trangthai" name="trangthai" required>
                                <option value="Vận chuyển">Chờ xác nhận</option>
                                <option value="Vận chuyển">Vận chuyển</option>
                                <option value="Đã giao">Đã giao</option>
                                <option value="Đã hủy">Đã hủy</option>
                                <option value="Hoàn tiền">Hoàn tiền</option>
                            </select>
                        </div>


                        <button type="submit" class="btn btn-primary">Lưu</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="/HMC/web/public/admin/js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="/HMC/web/public/admin/js/datatables-simple-demo.js"></script>
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <script>
        $(document).ready(function() {
            $('.edit-btn').click(function() {
                $('#madh').val($(this).data('madh'));
                $('#mand').val($(this).data('mand'));
                $('#ngaylap').val($(this).data('ngaylap'));
                $('#nguoinhan').val($(this).data('nguoinhan'));
                $('#sdt').val($(this).data('sdt'));
                $('#diachi').val($(this).data('diachi'));
                $('#pttt').val($(this).data('pttt'));
                $('#tongtien').val($(this).data('tongtien'));
                $('#trangthai').val($(this).data('trangthai'));
                $('#modalLabel').text('Chỉnh sửa người dùng');
            });

            $('#btnAddNew').click(function() {
                $('#madh').val('');
                $('#mand').val('');
                $('#ngaylap').val('');
                $('#nguoinhan').val('');
                $('#sdt').val('');
                $('#diachi').val('');
                $('#pttt').val('');

                $('#tongtien').val('');
                $('#trangthai').val('');
                $('#modalLabel').text('Thêm người dùng mới');
            });
        });
    </script>


    <script>
        document.getElementById('btnExportExcel').addEventListener('click', function() {
            let table = document.getElementById('datatablesSimple');
            let clonedTable = table.cloneNode(true);

            let rows = clonedTable.rows;
            let data = [];

            for (let i = 0; i < rows.length; i++) {
                let rowData = [];
                let cells = rows[i].cells;

                for (let j = 0; j < cells.length - 1; j++) {
                    let cell = cells[j];

                    let img = cell.querySelector('img');
                    if (img) {
                        rowData.push(img.src); // Lưu URL của hình ảnh
                    } else {
                        rowData.push(cell.innerText || cell.textContent);
                    }
                }
                data.push(rowData);
            }

            let ws = XLSX.utils.aoa_to_sheet(data);
            let wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "DonHang");

            XLSX.writeFile(wb, 'donhang.xlsx');
        });
    </script>





</body>

</html>