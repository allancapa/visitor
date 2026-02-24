<?php
session_start();

// Database configuration - UPDATE THESE VALUES for your environment
define('DB_HOST', 'localhost');
define('DB_NAME', 'visitor_management');
define('DB_USER', 'root');
define('DB_PASS', '');

// Set headers for JSON API responses
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Create PDO database connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

// Helper function to send JSON response
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}

// Helper function to check if user is authenticated
function requireAuth() {
    if (!isset($_SESSION['user_id'])) {
        jsonResponse(['success' => false, 'message' => 'Unauthorized. Please log in.'], 401);
    }
}
?>
