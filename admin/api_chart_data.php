<?php
require '../core/config.php';

// Check admin session
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
$year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
$days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

$query = "SELECT 
            DAY(reservation_date) as day, 
            SUM(total_price) as revenue, 
            COUNT(id) as bookings 
          FROM reservations 
          WHERE status != 'cancelled' 
            AND MONTH(reservation_date) = $month 
            AND YEAR(reservation_date) = $year
          GROUP BY DAY(reservation_date)";
$result = $conn->query($query);

$data = ['revenue' => [], 'bookings' => []];
while($row = $result->fetch_assoc()) {
    $data['revenue'][$row['day']] = $row['revenue'];
    $data['bookings'][$row['day']] = $row['bookings'];
}

// Fill zeros
$labels = range(1, $days_in_month);
$final_revenue = [];
$final_bookings = [];

foreach ($labels as $day) {
    $final_revenue[] = isset($data['revenue'][$day]) ? (int)$data['revenue'][$day] : 0;
    $final_bookings[] = isset($data['bookings'][$day]) ? (int)$data['bookings'][$day] : 0;
}

header('Content-Type: application/json');
echo json_encode([
    'labels' => $labels,
    'revenue' => $final_revenue,
    'bookings' => $final_bookings,
    'label_title' => date("F Y", mktime(0, 0, 0, $month, 10, $year))
]);
?>
