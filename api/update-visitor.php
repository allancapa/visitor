<?php
require_once 'config.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);

$id = intval($input['id'] ?? 0);
$visitor_name = trim($input['visitor_name'] ?? '');
$contact_number = trim($input['contact_number'] ?? '');
$purpose = trim($input['purpose'] ?? '');
$address = trim($input['address'] ?? '');
$visit_date = trim($input['visit_date'] ?? '');
$visit_time = trim($input['visit_time'] ?? '');

$role = getUserRole();
$userId = getUserId();

if ($id <= 0) {
    jsonResponse(['success' => false, 'message' => 'Invalid visitor ID.'], 400);
}

if (empty($visitor_name) || empty($contact_number) || empty($purpose) || empty($address)) {
    jsonResponse(['success' => false, 'message' => 'All fields are required.'], 400);
}

try {
    // Check ownership for staff
    if ($role === 'staff') {
        $check = $pdo->prepare("SELECT id FROM visitor_tbl WHERE id = :id AND user_id = :user_id AND visit_date = CURDATE()");
        $check->execute(['id' => $id, 'user_id' => $userId]);
        if (!$check->fetch()) {
            jsonResponse(['success' => false, 'message' => 'Access denied. You can only edit your own visitors from today.'], 403);
        }
        // Staff cannot change the date
        $visit_date = date('Y-m-d');
    }

    if (empty($visit_date)) {
        $visit_date = date('Y-m-d');
    }
    if (empty($visit_time)) {
        $visit_time = date('H:i:s');
    }

    $stmt = $pdo->prepare("
        UPDATE visitor_tbl 
        SET visitor_name = :visitor_name, contact_number = :contact_number, purpose = :purpose, 
            address = :address, visit_date = :visit_date, visit_time = :visit_time
        WHERE id = :id
    ");
    $stmt->execute([
        'visitor_name' => $visitor_name,
        'contact_number' => $contact_number,
        'purpose' => $purpose,
        'address' => $address,
        'visit_date' => $visit_date,
        'visit_time' => $visit_time,
        'id' => $id
    ]);

    if ($stmt->rowCount() === 0) {
        jsonResponse(['success' => false, 'message' => 'Visitor not found or no changes made.'], 404);
    }

    jsonResponse(['success' => true, 'message' => 'Visitor updated successfully.']);

} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Failed to update visitor.'], 500);
}
?>
