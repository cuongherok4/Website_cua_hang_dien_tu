<?php
// Kết nối cơ sở dữ liệu
include 'autoload/Database.php';

// Lấy 10 sản phẩm có số lượng nhiều nhất trong kho và có trạng thái NULL hoặc rỗng
$sql = "SELECT * FROM sanpham WHERE TrangThai IS NULL OR TrangThai = '' ORDER BY SoLuong DESC LIMIT 10";
$result = $conn->query($sql);

// Lấy 10 sản phẩm có số lượng ít nhất trong kho và có trạng thái NULL hoặc rỗng
$sql_low_stock = "SELECT * FROM sanpham WHERE TrangThai IS NULL OR TrangThai = '' ORDER BY SoLuong ASC LIMIT 10";
$result_low_stock = $conn->query($sql_low_stock);

// Truy vấn doanh thu theo từng tháng
$sql_revenue = "SELECT DATE_FORMAT(dh.NgayLap, '%Y-%m') AS Thang, 
                       SUM(ctdh.SoLuong * ctdh.DonGia) AS DoanhThu 
                FROM donhang dh
                JOIN chitietdonhang ctdh ON dh.MaDH = ctdh.MaDH
                GROUP BY Thang
                ORDER BY Thang DESC";

$result_revenue = $conn->query($sql_revenue);
$data_labels = []; // Mảng chứa các tháng
$data_values = []; // Mảng chứa doanh thu tương ứng

// Lưu dữ liệu doanh thu vào mảng để hiển thị biểu đồ
while ($row = $result_revenue->fetch_assoc()) {
    $data_labels[] = $row['Thang'];
    $data_values[] = $row['DoanhThu'];
}

// Truy vấn 10 sản phẩm bán chạy nhất theo tổng số lượng đã bán
$sql_bestsellers = "
    SELECT sp.TenSP, SUM(ctdh.SoLuong) AS TongBan
    FROM chitietdonhang ctdh
    JOIN sanpham sp ON ctdh.MaSP = sp.MaSP
    GROUP BY sp.TenSP
    ORDER BY TongBan DESC
    LIMIT 10
";
$result_bestsellers = $conn->query($sql_bestsellers);

$labels = []; // Mảng chứa tên sản phẩm bán chạy
$data = [];   // Mảng chứa số lượng bán tương ứng

// Lưu dữ liệu sản phẩm bán chạy vào mảng để hiển thị biểu đồ
while ($row = $result_bestsellers->fetch_assoc()) {
    $labels[] = $row['TenSP'];
    $data[] = $row['TongBan'];
}

// Truy vấn tổng doanh thu của tất cả đơn hàng
$sql_revenue = "SELECT SUM(ctdh.SoLuong * ctdh.DonGia) AS DoanhThu FROM chitietdonhang ctdh";
$result_revenue = $conn->query($sql_revenue);
$row_revenue = $result_revenue->fetch_assoc();
$doanhThu = $row_revenue['DoanhThu'] ?? 0; // Nếu NULL thì gán giá trị 0

// Tính tổng giá trị tồn kho (giá trị sản phẩm còn trong kho)
$sql_stock_value = "SELECT SUM(SoLuong * DonGia) AS GiaTriKho FROM sanpham";
$result_stock = $conn->query($sql_stock_value);
$row_stock = $result_stock->fetch_assoc();
$giaTriKho = $row_stock['GiaTriKho'] ?? 0; // Nếu NULL thì gán giá trị 0

?>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var ctx = document.getElementById('pieChart').getContext('2d');
        var myPieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Doanh thu bán ra', 'Giá trị kho hàng'],
                datasets: [{
                    data: [<?php echo $doanhThu; ?>, <?php echo $giaTriKho; ?>],
                    backgroundColor: ['#36A2EB', '#FF6384'],
                    hoverBackgroundColor: ['#2F86C1', '#E64565']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                let value = tooltipItem.raw;
                                return ' ' + new Intl.NumberFormat('vi-VN', {
                                    style: 'currency',
                                    currency: 'VND'
                                }).format(value);
                            }
                        }
                    }
                }
            }
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var ctx = document.getElementById('myBarChart').getContext('2d');
        var myBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($data_labels); ?>,
                datasets: [{
                    label: 'Doanh thu (VND)',
                    data: <?php echo json_encode($data_values); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                let value = tooltipItem.raw; // Giá trị doanh thu
                                return ' ' + new Intl.NumberFormat('vi-VN', {
                                    style: 'currency',
                                    currency: 'VND'
                                }).format(value);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN', {
                                    style: 'currency',
                                    currency: 'VND'
                                }).format(value);
                            }
                        }
                    }
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
    <title>Dashboard - SB Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="/HMC/web/public/admin/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="index.php">SERVER</a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
        <!-- Navbar Search-->
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
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
                    <h1 class="mt-4">Trang chủ</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Trang chủ</li>
                    </ol>

                    <div class="row">
                        <div class="col-xl-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-chart-area me-1"></i>
                                    Doanh thu theo tháng
                                </div>
                                <div class="card-body">
                                    <canvas id="myBarChart" width="100%" height="40"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-chart-bar me-1"></i>
                                    Doanh thu và tồn kho
                                </div>
                                <div class="card-body">
                                    <canvas id="pieChart" width=255" height="255"></canvas>

                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Bảng các sản phẩm có số lượng tồn kho cao nhất
                        </div>
                        <div class="card-body">
                            <table id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th>Mã SP</th>
                                        <th>Tên SP</th>
                                        <th>Số lượng</th>
                                        <th>Đơn giá</th>
                                        <th>Giá sau KM</th>
                                        <th>Đánh giá</th>
                                        <th>Hình ảnh</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?php echo $row['MaSP']; ?></td>
                                            <td><?php echo $row['TenSP']; ?></td>
                                            <td><?php echo $row['SoLuong']; ?></td>
                                            <td><?php echo rtrim(rtrim(number_format($row['DonGia'], 3, '.', ','), '0'), '.'); ?> VND</td>
                                            <td><?php echo rtrim(rtrim(number_format($row['GiaSauKM'], 3, '.', ','), '0'), '.'); ?> VND</td>

                                            <td><?php echo $row['SoSao']; ?> ★</td>
                                            <td><img src="<?php echo $row['HinhAnh']; ?>" alt="Hình ảnh" width="50"></td>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="/HMC/web/public/admin/js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="/HMC/web/public/admin/js/datatables-simple-demo.js"></script>
    <!-- Code injected by live-server -->
    <script>
        // <![CDATA[  <-- For SVG support
        if ('WebSocket' in window) {
            (function() {
                function refreshCSS() {
                    var sheets = [].slice.call(document.getElementsByTagName("link"));
                    var head = document.getElementsByTagName("head")[0];
                    for (var i = 0; i < sheets.length; ++i) {
                        var elem = sheets[i];
                        var parent = elem.parentElement || head;
                        parent.removeChild(elem);
                        var rel = elem.rel;
                        if (elem.href && typeof rel != "string" || rel.length == 0 || rel.toLowerCase() == "stylesheet") {
                            var url = elem.href.replace(/(&|\?)_cacheOverride=\d+/, '');
                            elem.href = url + (url.indexOf('?') >= 0 ? '&' : '?') + '_cacheOverride=' + (new Date().valueOf());
                        }
                        parent.appendChild(elem);
                    }
                }
                var protocol = window.location.protocol === 'http:' ? 'ws://' : 'wss://';
                var address = protocol + window.location.host + window.location.pathname + '/ws';
                var socket = new WebSocket(address);
                socket.onmessage = function(msg) {
                    if (msg.data == 'reload') window.location.reload();
                    else if (msg.data == 'refreshcss') refreshCSS();
                };
                if (sessionStorage && !sessionStorage.getItem('IsThisFirstTime_Log_From_LiveServer')) {
                    console.log('Live reload enabled.');
                    sessionStorage.setItem('IsThisFirstTime_Log_From_LiveServer', true);
                }
            })();
        } else {
            console.error('Upgrade your browser. This Browser is NOT supported WebSocket for Live-Reloading.');
        }
        // ]]>
    </script>
</body>

</html>