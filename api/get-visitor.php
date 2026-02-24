<?php
require_once 'config.php';
requireAuth();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    jsonResponse(['success' => false, 'message' => 'Invalid visitor ID.'], 400);
}

try {
    $stmt = $pdo->prepare("SELECT * FROM visitors WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $visitor = $stmt->fetch();

    if (!$visitor) {
        jsonResponse(['success' => false, 'message' => 'Visitor not found.'], 404);
    }

    jsonResponse(['success' => true, 'visitor' => $visitor]);

} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Failed to fetch visitor.'], 500);
}
?>
