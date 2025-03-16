<?php
session_start();
?>

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
$sql = "SELECT * FROM sanpham WHERE TrangThai IS NULL OR TrangThai = ''";
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
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
            <button class="btn btnchung btn-success" id="btnImportExcel" type="button">
                <i class="bi bi-file-earmark-arrow-up me-2"></i> Nhập Excel
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
                    <h1 class="mt-4">Sản phẩm</h1>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Bảng dữ liệu
                        </div>
                        <div class="card-body">
                            <table id="datatablesSimple" class="table table-hover table-bordered text-center align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Mã SP</th>
                                        <th>Tên SP</th>
                                        <th>Mã Loại SP</th>
                                        <th>Hình ảnh</th>
                                        <th>Đơn giá</th>
                                        <th>Số lượng</th>
                                        <th>Số sao</th>
                                        <th>Số đánh giá</th>
                                        <th>Khuyến mãi</th>
                                        <th>Giá sau khuyến mãi</th>
                                        <th>Mô tả</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?= $row['MaSP'] ?></td>
                                            <td><?= $row['TenSP'] ?></td>
                                            <td><?= $row['MaLoaiSP'] ?></td>
                                            <td>
                                                <img src="<?= $row['HinhAnh'] ?>" alt="Hình ảnh sản phẩm" width="80" height="80">
                                            </td>
                                            <td><?= number_format($row['DonGia'], 0, ',', '.') ?> đ</td>
                                            <td><?= $row['SoLuong'] ?></td>
                                            <td><?= $row['SoSao'] ?></td>
                                            <td><?= $row['SoDanhGia'] ?></td>
                                            <td><?= $row['KM'] ?>%</td>
                                            <td><?= number_format($row['GiaSauKM'], 0, ',', '.') ?> đ</td>
                                            <td><?= $row['MoTa'] ?></td>
                                            <td>
                                                <button class="btn btn-warning btn-sm edit-btn"
                                                    data-masp="<?= $row['MaSP'] ?>"
                                                    data-tensp="<?= $row['TenSP'] ?>"
                                                    data-maloaisp="<?= $row['MaLoaiSP'] ?>"
                                                    data-hinhanh="<?= $row['HinhAnh'] ?>"
                                                    data-dongia="<?= $row['DonGia'] ?>"
                                                    data-soluong="<?= $row['SoLuong'] ?>"
                                                    data-sosao="<?= $row['SoSao'] ?>"
                                                    data-sodanhgia="<?= $row['SoDanhGia'] ?>"
                                                    data-km="<?= $row['KM'] ?>"
                                                    data-giasaukm="<?= $row['GiaSauKM'] ?>"
                                                    data-mota="<?= $row['MoTa'] ?>"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#userModal">
                                                    ✏️ Sửa
                                                </button>
                                                <a href="autoload/autoload_sanpham.php?id=<?= $row['MaSP'] ?>" class="btn btn-danger btn-sm"
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



    <!--                               Modal Nhập Excel                -->
    <div class="modal fade" id="importExcelModal" tabindex="-1" aria-labelledby="importExcelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importExcelModalLabel">Nhập Dữ Liệu Từ Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="autoload/autoload_sanpham.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="excelFile" class="form-label">Chọn tệp Excel</label>
                            <input type="file" class="form-control" id="excelFile" name="excelFile" accept=".xlsx, .xls" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Nhập Dữ Liệu</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Người Dùng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="autoload/autoload_sanpham.php" method="POST">
                        <input type="hidden" id="masp" name="masp">
                        <div class="mb-3">
                            <label for="tensp" class="form-label">Tên sản phẩm</label>
                            <input type="text" class="form-control" id="tensp" name="tensp" required>
                        </div>
                        <select class="form-control" id="maloaisp" name="maloaisp" required>
                            <option value="">-- Chọn loại sản phẩm --</option>
                            <?php
                            include 'db_connect.php'; // Kết nối CSDL
                            $query = "SELECT MaLoaiSP, TenLoaiSP FROM loaisanpham";
                            $result = mysqli_query($conn, $query);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<option value="' . $row['MaLoaiSP'] . '">' . $row['TenLoaiSP'] . '</option>';
                            }
                            ?>
                        </select>

                        <div class="mb-3">
                            <label for="hinhanh" class="form-label">Hình ảnh</label>
                            <input type="text" class="form-control" id="hinhanh" name="hinhanh" required>
                        </div>
                        <div class="mb-3">
                            <label for="dongia" class="form-label">Đơn giá</label>
                            <input type="text" class="form-control" id="dongia" name="dongia" required>
                        </div>
                        <div class="mb-3">
                            <label for="soluong" class="form-label">Số lượng</label>
                            <input type="text" class="form-control" id="soluong" name="soluong">
                        </div>
                        <div class="mb-3">
                            <label for="sosao" class="form-label">Số sao</label>
                            <input type="text" class="form-control" id="sosao" name="sosao" required>
                        </div>
                        <div class="mb-3">
                            <label for="sodanhgia" class="form-label">Số đánh giá</label>
                            <input type="text" class="form-control" id="sodanhgia" name="sodanhgia" required>
                        </div>
                        <div class="mb-3">
                            <label for="khuyenmai" class="form-label">Khuyến mãi >=(100%)</label>
                            <input type="text" class="form-control" id="khuyenmai" name="khuyenmai" required>
                        </div>
                        <div class="mb-3">
                            <label for="giasaukhuyemmai" class="form-label">Giá sau khuyến mãi</label>
                            <input type="text" class="form-control" id="giasaukhuyemmai" name="giasaukhuyemmai" required>
                        </div>
                        <div class="mb-3">
                            <label for="giasaukhuyemmai" class="form-label">Mô tả</label>
                            <input type="text" class="form-control" id="mota" name="mota" required>
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
        document.addEventListener("DOMContentLoaded", function() {
            let dongiaInput = document.getElementById("dongia");
            let khuyenmaiInput = document.getElementById("khuyenmai");
            let giasaukmInput = document.getElementById("giasaukhuyemmai");

            function updateGiaSauKM() {
                let dongia = parseFloat(dongiaInput.value) || 0;
                let khuyenmai = parseFloat(khuyenmaiInput.value) || 0;

                if (khuyenmai >= 0 && khuyenmai <= 100) {
                    let giasaukm = dongia * (1 - khuyenmai / 100);
                    giasaukmInput.value = giasaukm.toFixed(3); // Làm tròn 2 chữ số sau dấu thập phân
                } else {
                    giasaukmInput.value = "Lỗi KM";
                }
            }
            dongiaInput.addEventListener("input", updateGiaSauKM);
            khuyenmaiInput.addEventListener("input", updateGiaSauKM);
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.edit-btn').click(function() {
                $('#masp').val($(this).data('masp'));
                $('#tensp').val($(this).data('tensp'));
                $('#maloaisp').val($(this).data('maloaisp'));
                $('#hinhanh').val($(this).data('hinhanh'));
                $('#dongia').val($(this).data('dongia'));
                $('#soluong').val($(this).data('soluong'));
                $('#sosao').val($(this).data('sosao'));
                $('#sodanhgia').val($(this).data('sodanhgia'));
                $('#khuyenmai').val($(this).data('km'));
                $('#giasaukhuyemmai').val($(this).data('giasaukm'));
                $('#mota').val($(this).data('mota'));
                $('#modalLabel').text('Chỉnh sửa người dùng');
            });

            $('#btnAddNew').click(function() {
                $('#masp').val('');
                $('#tensp').val('');
                $('#maloaisp').val('');
                $('#hinhanh').val('');
                $('#dongia').val('');
                $('#soluong').val('');
                $('#sosao').val('User');
                $('#sodanhgia').val('');
                $('#khuyenmai').val('');
                $('#giasaukhuyemmai').val('');
                $('#mota').val('');
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

                for (let j = 0; j < cells.length-1; j++) {
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
            XLSX.utils.book_append_sheet(wb, ws, "SanPham");

            XLSX.writeFile(wb, 'SanPham.xlsx');
        });
    </script>

    <script>
        document.getElementById('btnImportExcel').addEventListener('click', function() { // Khi nhấn vào nút có id 'btnImportExcel'
            var importExcelModal = new bootstrap.Modal(document.getElementById('importExcelModal')); // Tạo đối tượng Modal từ Bootstrap cho modal có id 'importExcelModal'
            importExcelModal.show(); // Hiển thị modal nhập Excel
        });
    </script>
</body>

</html>