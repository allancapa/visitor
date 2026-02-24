<?php
require_once 'config.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);

$visitor_name = trim($input['visitor_name'] ?? '');
$contact_number = trim($input['contact_number'] ?? '');
$purpose = trim($input['purpose'] ?? '');
$address = trim($input['address'] ?? '');
$visit_date = trim($input['visit_date'] ?? '');
$visit_time = trim($input['visit_time'] ?? '');

$role = getUserRole();
$userId = getUserId();

// Validation
if (empty($visitor_name) || empty($contact_number) || empty($purpose) || empty($address)) {
    jsonResponse(['success' => false, 'message' => 'All fields are required.'], 400);
}

// Staff can only add visitors for today
if ($role === 'staff') {
    $visit_date = date('Y-m-d'); // Force today's date
    if (empty($visit_time)) {
        $visit_time = date('H:i:s');
    }
} else {
    // Admin can set any date
    if (empty($visit_date)) {
        $visit_date = date('Y-m-d');
    }
    if (empty($visit_time)) {
        $visit_time = date('H:i:s');
    }
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO visitor_tbl (visitor_name, contact_number, purpose, address, visit_date, visit_time, user_id) 
        VALUES (:visitor_name, :contact_number, :purpose, :address, :visit_date, :visit_time, :user_id)
    ");
    $stmt->execute([
        'visitor_name' => $visitor_name,
        'contact_number' => $contact_number,
        'purpose' => $purpose,
        'address' => $address,
        'visit_date' => $visit_date,
        'visit_time' => $visit_time,
        'user_id' => $userId
    ]);

    $newId = $pdo->lastInsertId();

    jsonResponse(['success' => true, 'message' => 'Visitor added successfully.', 'id' => $newId], 201);

} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Failed to add visitor.'], 500);
}
?>
