<?php
require_once 'config.php';
requireAdmin();

try {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($id > 0) {
        // Get single staff member
        $stmt = $pdo->prepare("SELECT user_id, fullname, username, role, status, date_created FROM user_tbl WHERE user_id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $staff = $stmt->fetch();

        if (!$staff) {
            jsonResponse(['success' => false, 'message' => 'Staff not found.'], 404);
        }

        jsonResponse(['success' => true, 'staff' => $staff]);
    } else {
        // Get all staff
        $stmt = $pdo->query("SELECT user_id, fullname, username, role, status, date_created FROM user_tbl ORDER BY date_created DESC");
        $staffList = $stmt->fetchAll();

        jsonResponse(['success' => true, 'staff' => $staffList]);
    }

} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Failed to fetch staff.'], 500);
}
?>
