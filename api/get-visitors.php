<?php
require_once 'config.php';
requireAuth();

try {
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';

    if (!empty($search)) {
        $stmt = $pdo->prepare("
            SELECT * FROM visitors 
            WHERE name LIKE :search 
               OR email LIKE :search2 
               OR phone LIKE :search3 
               OR purpose LIKE :search4 
               OR person_to_visit LIKE :search5
            ORDER BY created_at DESC
        ");
        $searchTerm = '%' . $search . '%';
        $stmt->execute([
            'search' => $searchTerm,
            'search2' => $searchTerm,
            'search3' => $searchTerm,
            'search4' => $searchTerm,
            'search5' => $searchTerm
        ]);
    } else {
        $stmt = $pdo->query("SELECT * FROM visitors ORDER BY created_at DESC");
    }

    $visitors = $stmt->fetchAll();

    jsonResponse(['success' => true, 'visitors' => $visitors]);

} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Failed to fetch visitors.'], 500);
}
?>
