<?php
require_once 'config.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);

$id = intval($input['id'] ?? 0);

if ($id <= 0) {
    jsonResponse(['success' => false, 'message' => 'Invalid visitor ID.'], 400);
}

try {
    $stmt = $pdo->prepare("
        UPDATE visitors 
        SET status = 'checked_out', check_out = NOW() 
        WHERE id = :id AND status = 'checked_in'
    ");
    $stmt->execute(['id' => $id]);

    if ($stmt->rowCount() === 0) {
        jsonResponse(['success' => false, 'message' => 'Visitor not found or already checked out.'], 404);
    }

    jsonResponse(['success' => true, 'message' => 'Visitor checked out successfully.']);

} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Failed to check out visitor.'], 500);
}
?>
