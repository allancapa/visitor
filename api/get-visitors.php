<?php
require_once 'config.php';
requireAuth();

try {
    $role = getUserRole();
    $userId = getUserId();
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $date = isset($_GET['date']) ? trim($_GET['date']) : '';

    $where = [];
    $params = [];

    // Staff can only see today's visitors that they created
    if ($role === 'staff') {
        $where[] = "v.visit_date = CURDATE()";
        $where[] = "v.user_id = :user_id";
        $params['user_id'] = $userId;
    }

    // Admin can filter by date
    if ($role === 'admin' && !empty($date)) {
        $where[] = "v.visit_date = :visit_date";
        $params['visit_date'] = $date;
    }

    // Search filter
    if (!empty($search)) {
        $where[] = "(v.visitor_name LIKE :search OR v.contact_number LIKE :search2 OR v.purpose LIKE :search3 OR v.address LIKE :search4)";
        $searchTerm = '%' . $search . '%';
        $params['search'] = $searchTerm;
        $params['search2'] = $searchTerm;
        $params['search3'] = $searchTerm;
        $params['search4'] = $searchTerm;
    }

    $sql = "SELECT v.*, u.fullname AS added_by FROM visitor_tbl v LEFT JOIN user_tbl u ON v.user_id = u.user_id";
    
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    
    $sql .= " ORDER BY v.visit_date DESC, v.visit_time DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $visitors = $stmt->fetchAll();

    jsonResponse(['success' => true, 'visitors' => $visitors]);

} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Failed to fetch visitors.'], 500);
}
?>
