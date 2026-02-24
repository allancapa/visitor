<?php
require_once 'config.php';
requireAuth();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    jsonResponse(['success' => false, 'message' => 'Invalid visitor ID.'], 400);
}

try {
    $role = getUserRole();
    $userId = getUserId();

    $sql = "SELECT v.*, u.fullname AS added_by FROM visitor_tbl v LEFT JOIN user_tbl u ON v.user_id = u.user_id WHERE v.id = :id";
    $params = ['id' => $id];

    // Staff can only view their own today's visitors
    if ($role === 'staff') {
        $sql .= " AND v.user_id = :user_id AND v.visit_date = CURDATE()";
        $params['user_id'] = $userId;
    }

    $sql .= " LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $visitor = $stmt->fetch();

    if (!$visitor) {
        jsonResponse(['success' => false, 'message' => 'Visitor not found or access denied.'], 404);
    }

    jsonResponse(['success' => true, 'visitor' => $visitor]);

} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Failed to fetch visitor.'], 500);
}
?>
