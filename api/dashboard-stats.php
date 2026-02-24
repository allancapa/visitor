<?php
require_once 'config.php';
requireAuth();

try {
    $role = getUserRole();
    $userId = getUserId();

    if ($role === 'admin') {
        // Admin sees all stats
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM visitor_tbl");
        $totalVisitors = $stmt->fetch()['total'];

        $stmt = $pdo->query("SELECT COUNT(*) as total FROM visitor_tbl WHERE visit_date = CURDATE()");
        $todayVisitors = $stmt->fetch()['total'];

        $stmt = $pdo->query("SELECT COUNT(*) as total FROM user_tbl WHERE role = 'staff'");
        $totalStaff = $stmt->fetch()['total'];

        $stmt = $pdo->query("SELECT COUNT(*) as total FROM user_tbl WHERE role = 'staff' AND status = 'active'");
        $activeStaff = $stmt->fetch()['total'];

        // Recent visitors (last 10)
        $stmt = $pdo->query("SELECT v.*, u.fullname AS added_by FROM visitor_tbl v LEFT JOIN user_tbl u ON v.user_id = u.user_id ORDER BY v.date_created DESC LIMIT 10");
        $recentVisitors = $stmt->fetchAll();

    } else {
        // Staff sees only their own today stats
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM visitor_tbl WHERE user_id = :uid AND visit_date = CURDATE()");
        $stmt->execute(['uid' => $userId]);
        $totalVisitors = $stmt->fetch()['total'];

        $todayVisitors = $totalVisitors; // Same for staff
        $totalStaff = 0;
        $activeStaff = 0;

        // Staff's recent visitors (today only)
        $stmt = $pdo->prepare("SELECT v.*, u.fullname AS added_by FROM visitor_tbl v LEFT JOIN user_tbl u ON v.user_id = u.user_id WHERE v.user_id = :uid AND v.visit_date = CURDATE() ORDER BY v.visit_time DESC LIMIT 10");
        $stmt->execute(['uid' => $userId]);
        $recentVisitors = $stmt->fetchAll();
    }

    jsonResponse([
        'success' => true,
        'stats' => [
            'total_visitors' => (int)$totalVisitors,
            'today_visitors' => (int)$todayVisitors,
            'total_staff' => (int)$totalStaff,
            'active_staff' => (int)$activeStaff
        ],
        'recent_visitors' => $recentVisitors
    ]);

} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Failed to fetch dashboard stats.'], 500);
}
?>
