<?php
require '../core/config.php';

header('Content-Type: application/json');

if (!isset($_GET['date'])) {
    echo json_encode(['success' => false, 'message' => 'Date required']);
    exit;
}

$date = $_GET['date'];
$time = $_GET['time'] ?? null;


if ($time) {
    $checkRes = $conn->prepare("SELECT id FROM reservations WHERE reservation_date = ? AND reservation_time = ? AND status != 'cancelled'");
    $checkRes->bind_param("ss", $date, $time);
    $checkRes->execute();
    $resResult = $checkRes->get_result();

    $checkLock = $conn->prepare("SELECT id FROM locked_slots WHERE date = ? AND time = ?");
    $checkLock->bind_param("ss", $date, $time);
    $checkLock->execute();
    $lockResult = $checkLock->get_result();

    $isAvailable = ($resResult->num_rows == 0 && $lockResult->num_rows == 0);

    echo json_encode([
        'success' => true,
        'available' => $isAvailable,
        'message' => $isAvailable ? 'Slot tersedia' : 'Slot sudah direservasi'
    ]);
} else {
    
    $unavailableTimes = [];

    
    $checkRes = $conn->prepare("SELECT reservation_time FROM reservations WHERE reservation_date = ? AND status != 'cancelled'");
    $checkRes->bind_param("s", $date);
    $checkRes->execute();
    $resResult = $checkRes->get_result();
    while ($row = $resResult->fetch_assoc()) {
        $unavailableTimes[] = $row['reservation_time'];
    }

    
    $checkLock = $conn->prepare("SELECT time FROM locked_slots WHERE date = ?");
    $checkLock->bind_param("s", $date);
    $checkLock->execute();
    $lockResult = $checkLock->get_result();
    while ($row = $lockResult->fetch_assoc()) {
        $unavailableTimes[] = $row['time'];
    }

    
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i');
    
    
    $allSlots = [];
    $slotCheck = $conn->query("SHOW TABLES LIKE 'reservation_slots'");
    if ($slotCheck && $slotCheck->num_rows > 0) {
        $slotRes = $conn->query("SELECT slot_time FROM reservation_slots WHERE is_active = 1");
        while ($s = $slotRes->fetch_assoc()) {
            $allSlots[] = date('H:i', strtotime($s['slot_time'])) . " WIB";
        }
    }
    
    
    if (empty($allSlots)) {
        $allSlots = ["09:00 WIB", "11:00 WIB", "13:00 WIB", "15:00 WIB", "17:00 WIB", "19:00 WIB"];
    }

    if ($date < $currentDate) {
        
        
        $unavailableTimes = array_merge($unavailableTimes, $allSlots);
    } elseif ($date == $currentDate) {
        
        foreach ($allSlots as $slot) {
            $slotTime = explode(' ', $slot)[0]; 
            if ($slotTime <= $currentTime) {
                $unavailableTimes[] = $slot;
            }
        }
    }

    echo json_encode([
        'success' => true,
        'unavailable_times' => array_unique($unavailableTimes),
        'is_past_date' => ($date < $currentDate)
    ]);
}
?>
