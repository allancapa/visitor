<?php
require_once 'config.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);

$user_id = intval($input['user_id'] ?? 0);

if ($user_id <= 0) {
    jsonResponse(['success' => false, 'message' => 'Invalid user ID.'], 400);
}

// Cannot delete yourself
if ($user_id == getUserId()) {
    jsonResponse(['success' => false, 'message' => 'You cannot delete your own account.'], 403);
}

try {
    // Set visitors' user_id to NULL before deleting user
    $stmt = $pdo->prepare("UPDATE visitor_tbl SET user_id = NULL WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);

    $stmt = $pdo->prepare("DELETE FROM user_tbl WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);

    if ($stmt->rowCount() === 0) {
        jsonResponse(['success' => false, 'message' => 'Staff not found.'], 404);
    }

    jsonResponse(['success' => true, 'message' => 'Staff member deleted successfully.']);

} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Failed to delete staff member.'], 500);
}
?>
