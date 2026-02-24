<?php
require_once 'config.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);

$id = intval($input['id'] ?? 0);
$name = trim($input['name'] ?? '');
$email = trim($input['email'] ?? '');
$phone = trim($input['phone'] ?? '');
$purpose = trim($input['purpose'] ?? '');
$person_to_visit = trim($input['person_to_visit'] ?? '');

if ($id <= 0) {
    jsonResponse(['success' => false, 'message' => 'Invalid visitor ID.'], 400);
}

if (empty($name) || empty($purpose) || empty($person_to_visit)) {
    jsonResponse(['success' => false, 'message' => 'Name, purpose, and person to visit are required.'], 400);
}

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['success' => false, 'message' => 'Invalid email address.'], 400);
}

try {
    $stmt = $pdo->prepare("
        UPDATE visitors 
        SET name = :name, email = :email, phone = :phone, purpose = :purpose, person_to_visit = :person_to_visit
        WHERE id = :id
    ");
    $stmt->execute([
        'name' => $name,
        'email' => $email ?: null,
        'phone' => $phone ?: null,
        'purpose' => $purpose,
        'person_to_visit' => $person_to_visit,
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
