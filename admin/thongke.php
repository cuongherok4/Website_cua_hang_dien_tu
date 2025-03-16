<?php
include 'autoload/Database.php';

// Truy vấn số lượng đơn hàng theo trạng thái
$sql = "SELECT TrangThai, COUNT(*) AS SoLuong FROM donhang GROUP BY TrangThai";
$result = $conn->query($sql);

$labels_count = [];
$data_count = [];

while ($row = $result->fetch_assoc()) {
    $labels_count[] = $row['TrangThai'];
    $data_count[] = $row['SoLuong'];
}

// Chuyển mảng PHP sang JSON để sử dụng trong JavaScript
$labels_count_json = json_encode($labels_count);
$data_count_json = json_encode($data_count);

// Truy vấn tổng tiền đơn hàng theo trạng thái
// Truy vấn tổng tiền đơn hàng theo trạng thái
$sql = "SELECT TrangThai, SUM(TongTien) AS TongTien FROM donhang GROUP BY TrangThai";
$result = $conn->query($sql);

$labels_tongtien = [];
$data_tongtien = [];

while ($row = $result->fetch_assoc()) {
    $labels_tongtien[] = $row['TrangThai'];
    // Định dạng số tiền theo kiểu VND
    $data_tongtien[] = number_format($row['TongTien'], 0, ',', '.') . ' VND';
}

// Chuyển đổi sang JSON
$labels_tongtien_json = json_encode($labels_tongtien);
$data_tongtien_json = json_encode($data_tongtien);


// Truy vấn tổng số lượng sản phẩm còn trong kho
$sql_tonkho = "SELECT SUM(SoLuong) AS TongTonKho FROM sanpham";
$result_tonkho = $conn->query($sql_tonkho);
$row_tonkho = $result_tonkho->fetch_assoc();
$tong_ton_kho = $row_tonkho['TongTonKho'];

// Truy vấn tổng số lượng sản phẩm đã bán
$sql_daban = "SELECT SUM(SoLuong) AS TongBan FROM chitietdonhang";
$result_daban = $conn->query($sql_daban);
$row_daban = $result_daban->fetch_assoc();
$tong_da_ban = $row_daban['TongBan'];

// Chuyển dữ liệu thành JSON
$labels_sanpham = json_encode(["Tồn kho", "Đã bán"]);
$data_sanpham = json_encode([$tong_ton_kho, $tong_da_ban]);


?>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var ctx = document.getElementById("sanphamPieChart").getContext("2d");
        var sanphamPieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: <?php echo $labels_sanpham; ?>,
                datasets: [{
                    data: <?php echo $data_sanpham; ?>,
                    backgroundColor: ['#36A2EB', '#FF6384'],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                }
            }
        });
    });
</script>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Charts - SB Admin</title>
    <link href="/HMC/web/public/admin/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="index.html">SERVER</a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
        <!-- Navbar Search-->
        <!-- Navbar Search (Combobox + Xuất Excel) -->
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <div class="input-group">
                <button class="btn btn-success" id="btnExportExcel" type="button">
                    <i class="fas fa-file-excel"></i> Xuất Excel
                </button>
                <select class="form-control" id="statisticSelect">
                    <option value="tongtien">Tổng tiền theo trạng thái</option>
                    <option value="soluongsanpham">Tổng số sản phẩm theo trạng thái</option>
                    <option value="sanphamtonban">Tổng số sản phẩm tồn và bán</option>
                </select>

            </div>
        </form>


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
                    <h1 class="mt-4">Thống kê</h1>


                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-chart-bar me-1"></i>
                                    Tổng tiền theo trạng thái đơn hàng
                                </div>
                                <div class="card-body"><canvas id="myBarChart" width="100%" height="50"></canvas></div>
                                <div class="card-footer small text-muted">Updated yesterday at 11:59 PM</div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-chart-pie me-1"></i>
                                    Dữ liệu đơn hàng
                                </div>
                                <div class="card-body"><canvas id="myPieChart" width="100%" height="50"></canvas></div>
                                <div class="card-footer small text-muted">Updated yesterday at 11:59 PM</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-chart-pie me-1"></i>
                                Thống kê số sản phẩm (Tồn kho & Đã bán)
                            </div>
                            <div class="card-body"><canvas id="sanphamPieChart" width="100%" height="50"></canvas></div>
                            <div class="card-footer small text-muted">Cập nhật mới nhất</div>
                        </div>
                    </div>
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Your Website 2023</div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="/HMC/web/public/admin/js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="/HMC/web/public/admin/assets/demo/chart-area-demo.js"></script>
    <script>
        var ctx = document.getElementById("myBarChart").getContext('2d');
        var myBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo $labels_tongtien_json; ?>,
                datasets: [{
                    data: <?php echo json_encode(array_map('intval', $data_tongtien)); ?>,
                    backgroundColor: ['#007bff', '#dc3545', '#ffc107', '#28a745', '#6c757d'],
                }],
            },
            options: {
                legend: {
                    display: false // Ẩn hoàn toàn phần chú thích
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            var index = tooltipItem.index;
                            return data.labels[index] + ": " + <?php echo json_encode($data_tongtien); ?>[index];
                        }
                    }
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            callback: function(value) {
                                return (value * 1000000).toLocaleString('vi-VN') + ' VND';
                            }
                        }
                    }]
                }

            }
        });
    </script>

    <script>
        var ctx = document.getElementById("myPieChart").getContext('2d');
        var myPieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: <?php echo $labels_count_json; ?>,
                datasets: [{
                    data: <?php echo $data_count_json; ?>,
                    backgroundColor: ['#007bff', '#dc3545', '#ffc107', '#28a745', '#6c757d'],
                }],
            },
        });
    </script>
<script>
document.getElementById("btnExportExcel").addEventListener("click", function () {
    var selectedOption = document.getElementById("statisticSelect").value;
    var form = document.createElement("form");
    form.method = "POST";
    form.action = "export_excel.php";
    
    var input = document.createElement("input");
    input.type = "hidden";
    input.name = "chartType";
    input.value = selectedOption;
    
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
});
</script>

</body>

</html>