<?php
require_once 'config.php';
requireAdmin(); // Only admin can delete visitors

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);

$id = intval($input['id'] ?? 0);

if ($id <= 0) {
    jsonResponse(['success' => false, 'message' => 'Invalid visitor ID.'], 400);
}

try {
    $stmt = $pdo->prepare("DELETE FROM visitor_tbl WHERE id = :id");
    $stmt->execute(['id' => $id]);

    if ($stmt->rowCount() === 0) {
        jsonResponse(['success' => false, 'message' => 'Visitor not found.'], 404);
    }

    jsonResponse(['success' => true, 'message' => 'Visitor deleted successfully.']);

} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Failed to delete visitor.'], 500);
}
?>
