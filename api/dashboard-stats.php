<?php
require_once 'config.php';
requireAuth();

try {
    // Total visitors
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM visitors");
    $totalVisitors = $stmt->fetch()['total'];

    // Checked in today
    $stmt = $pdo->query("
        SELECT COUNT(*) as total FROM visitors 
        WHERE status = 'checked_in' AND DATE(check_in) = CURDATE()
    ");
    $checkedInToday = $stmt->fetch()['total'];

    // Checked out today
    $stmt = $pdo->query("
        SELECT COUNT(*) as total FROM visitors 
        WHERE status = 'checked_out' AND DATE(check_out) = CURDATE()
    ");
    $checkedOutToday = $stmt->fetch()['total'];

    // Currently checked in (all time, still in building)
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM visitors WHERE status = 'checked_in'");
    $currentlyIn = $stmt->fetch()['total'];

    // Recent visitors (last 5)
    $stmt = $pdo->query("SELECT * FROM visitors ORDER BY created_at DESC LIMIT 5");
    $recentVisitors = $stmt->fetchAll();

    jsonResponse([
        'success' => true,
        'stats' => [
            'total_visitors' => (int)$totalVisitors,
            'checked_in_today' => (int)$checkedInToday,
            'checked_out_today' => (int)$checkedOutToday,
            'currently_in' => (int)$currentlyIn
        ],
        'recent_visitors' => $recentVisitors
    ]);

} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Failed to fetch dashboard stats.'], 500);
}
?>
