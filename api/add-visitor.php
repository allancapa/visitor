<?php
require_once 'config.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);

$name = trim($input['name'] ?? '');
$email = trim($input['email'] ?? '');
$phone = trim($input['phone'] ?? '');
$purpose = trim($input['purpose'] ?? '');
$person_to_visit = trim($input['person_to_visit'] ?? '');

// Validation
if (empty($name) || empty($purpose) || empty($person_to_visit)) {
    jsonResponse(['success' => false, 'message' => 'Name, purpose, and person to visit are required.'], 400);
}

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['success' => false, 'message' => 'Invalid email address.'], 400);
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO visitors (name, email, phone, purpose, person_to_visit, check_in, status) 
        VALUES (:name, :email, :phone, :purpose, :person_to_visit, NOW(), 'checked_in')
    ");
    $stmt->execute([
        'name' => $name,
        'email' => $email ?: null,
        'phone' => $phone ?: null,
        'purpose' => $purpose,
        'person_to_visit' => $person_to_visit
    ]);

    $newId = $pdo->lastInsertId();

    jsonResponse(['success' => true, 'message' => 'Visitor added successfully.', 'id' => $newId], 201);

} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Failed to add visitor.'], 500);
}
?>
