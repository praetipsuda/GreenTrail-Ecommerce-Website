<?php
require 'db.php';
require 'header.php';

// ดึงปีปัจจุบันเพื่อตั้งค่าเริ่มต้น
$selected_year = intval($_GET['year'] ?? date('Y'));

// ดึงข้อมูลสรุปรายเดือนในปีที่เลือก
$monthly_sql = "
    SELECT 
        MONTH(created_at) AS month, 
        COUNT(order_id) AS total_orders, 
        COALESCE(SUM(total_price), 0) AS total_revenue 
    FROM orders 
    WHERE YEAR(created_at) = {$selected_year}
    GROUP BY MONTH(created_at) 
    ORDER BY month ASC
";
$monthly_result = $conn->query($monthly_sql);

// เตรียมอาร์เรย์เก็บข้อมูล 12 เดือน (ตั้งค่าเริ่มต้นเป็น 0)
$sales_data = array_fill(1, 12, 0);
$orders_data = array_fill(1, 12, 0);
$grand_total_revenue = 0;
$grand_total_orders = 0;

if ($monthly_result && $monthly_result->num_rows > 0) {
    while ($row = $monthly_result->fetch_assoc()) {
        $m = intval($row['month']);
        $sales_data[$m] = floatval($row['total_revenue']);
        $orders_data[$m] = intval($row['total_orders']);
        $grand_total_revenue += floatval($row['total_revenue']);
        $grand_total_orders += intval($row['total_orders']);
    }
}

$thai_months = [
    1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
    5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
    9 => 'กันยายน', 10 => 'ตารางคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
];
?>

<!-- Chart.js สำหรับแสดงกราฟ -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container py-4">

    <!-- การ์ดหัวข้อรายได้และยอดขายรายเดือน (ตรงตามรูปภาพ) -->
    <div class="row justify-content-center mb-4">
        <div class="col-12 col-sm-8 col-md-5 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4 bg-white">
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light p-3" style="width: 70px; height: 70px;">
                        <i class="fa-solid fa-chart-line text-success fs-1"></i>
                    </div>
                </div>
                <h5 class="fw-bold text-dark mb-1">รายได้และยอดขายรายเดือน</h5>
                <p class="text-muted small mb-0">ดูรายได้ ยอดขาย และแนวโน้มประจำเดือน</p>
            </div>
        </div>
    </div>

    <!-- ตัวเลือกปี และ สรุปภาพรวม -->
    <div class="row g-3 mb-4 align-items-center">
        <div class="col-md-4">
            <form method="get" action="monthly_report.php" class="d-flex align-items-center gap-2">
                <label class="fw-bold text-dark text-nowrap">เลือกปี:</label>
                <select name="year" class="form-select rounded-pill border-success" onchange="this.form.submit()">
                    <?php 
                    $current_y = intval(date('Y'));
                    for ($y = $current_y; $y >= $current_y - 4; $y--): 
                    ?>
                        <option value="<?= $y ?>" <?= $selected_year === $y ? 'selected' : '' ?>>
                            พ.ศ. <?= $y + 543 ?> (<?= $y ?>)
                        </option>
                    <?php endfor; ?>
                </select>
            </form>
        </div>
        <div class="col-md-8 text-md-end">
            <span class="badge bg-success-subtle text-success fs-6 px-3 py-2 me-2 rounded-pill">
                <i class="fa-solid fa-wallet me-1"></i> รายได้รวมปีนี้: <strong><?= number_format($grand_total_revenue, 2) ?> ฿</strong>
            </span>
            <span class="badge bg-primary-subtle text-primary fs-6 px-3 py-2 rounded-pill">
                <i class="fa-solid fa-box me-1"></i> คำสั่งซื้อรวม: <strong><?= number_format($grand_total_orders) ?> รายการ</strong>
            </span>
        </div>
    </div>

    <!-- กราฟแนวโน้มยอดขาย -->
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
        <h6 class="fw-bold text-success mb-3"><i class="fa-solid fa-chart-area me-2"></i>แนวโน้มยอดขายประจำปี <?= $selected_year + 543 ?></h6>
        <div style="height: 300px;">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <!-- ตารางรายละเอียดรายเดือน -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <h6 class="fw-bold text-success mb-0"><i class="fa-solid fa-list text-success me-2"></i>รายละเอียดรายได้แยกตามเดือน</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">เดือน</th>
                            <th class="text-center">จำนวนคำสั่งซื้อ</th>
                            <th class="text-end pe-4">ยอดขายรวม (บาท)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($thai_months as $m_num => $m_name): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-dark"><?= $m_name ?></td>
                                <td class="text-center text-muted"><?= number_format($orders_data[$m_num]) ?> รายการ</td>
                                <td class="text-end pe-4 fw-bold text-success">
                                    <?= number_format($sales_data[$m_num], 2) ?> ฿
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_values($thai_months)) ?>,
            datasets: [{
                label: 'ยอดขาย (บาท)',
                data: <?= json_encode(array_values($sales_data)) ?>,
                borderColor: '#43a047',
                backgroundColor: 'rgba(67, 160, 71, 0.1)',
                fill: true,
                tension: 0.3,
                borderWidth: 3,
                pointBackgroundColor: '#2e7d32',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' ฿';
                        }
                    }
                }
            }
        }
    });
</script>

</body>
</html>