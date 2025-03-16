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
$sql = "SELECT * FROM tintuc";
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
                    <h1 class="mt-4">Tin tức</h1>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Bảng dữ liệu
                        </div>
                        <div class="card-body">
                            <table id="datatablesSimple" class="table table-hover table-bordered text-center align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Mã tin tức</th>
                                        <th>Mã người dùng</th>
                                        <th>Tiêu đề</th> 
                                        <th>Đường dẫn tiêu đề</th> 
                                        <th>Ảnh tin tức</th>
                                        <th>Nội dung</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?= $row['MaTinTuc'] ?></td>
                                            <td><?= $row['MaND'] ?></td>
                                            <td><?= $row['TieuDe'] ?></td>
                                            <td><?= $row['Linklk'] ?></td>
                                            <td><img src="<?= $row['AnhTinTuc'] ?>" alt="Hình ảnh sản phẩm" width="80" height="80"></td>
                                            <td><?= $row['NoiDung'] ?></td>
                                            <td>
                                                <button class="btn btn-warning btn-sm edit-btn"
                                                    data-matt="<?= $row['MaTinTuc'] ?>"
                                                    data-mand="<?= $row['MaND'] ?>"
                                                    data-tieude="<?= $row['TieuDe'] ?>"
                                                    data-link="<?= $row['Linklk'] ?>"
                                                    data-anhtt="<?= $row['AnhTinTuc'] ?>"
                                                    data-noidung="<?= $row['NoiDung'] ?>"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#userModal">
                                                    ✏️ Sửa
                                                </button>
                                                <a href="autoload/autoload_tintuc.php?id=<?= $row['MaTinTuc'] ?>" class="btn btn-danger btn-sm"
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
    <!-- Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">

                    <h5 class="modal-title" id="modalLabel">Người Dùng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="autoload/autoload_tintuc.php" method="POST">
                        <input type="hidden" id="matt" name="matt">
                        <div class="mb-3">
                            <label for="tensp" class="form-label">Mã người dùng</label>
                            <input type="text" class="form-control" id="mand" name="mand" required>
                        </div>
                        <div class="mb-3">
                            <label for="hinhanh" class="form-label">Tiêu đề</label>
                            <input type="text" class="form-control" id="tieude" name="tieude" required>
                        </div>
                        <div class="mb-3">
                            <label for="dongia" class="form-label">Link liên kết</label>
                            <input type="text" class="form-control" id="link" name="link" required>
                        </div>
                        <div class="mb-3">
                            <label for="soluong" class="form-label">Ảnh tin tức</label>
                            <input type="text" class="form-control" id="anhtt" name="anhtt">
                        </div>
                        <div class="mb-3">
                            <label for="soluong" class="form-label">Nội dung</label>
                            <input type="text" class="form-control" id="noidung" name="noidung">
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
                $('#matt').val($(this).data('matt'));
                $('#mand').val($(this).data('mand'));
                $('#tieude').val($(this).data('tieude'));
                $('#link').val($(this).data('link'));
                $('#anhtt').val($(this).data('anhtt'));
                $('#noidung').val($(this).data('noidung'));
                $('#modalLabel').text('Chỉnh sửa người dùng');
            });

            $('#btnAddNew').click(function() {
                $('#matt').val('');
                $('#mand').val('');
                $('#tieude').val('');
                $('#link').val('');
                $('#anhtt').val('');
                $('#noidung').val('');
                $('#modalLabel').text('Thêm người dùng mới');
            });
        });
    </script>
</body>

</html>