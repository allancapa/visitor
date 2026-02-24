<?php
/**
 * Setup Script - Run this ONCE from your browser to create the default admin user.
 * URL: http://localhost/visitor-management/api/setup.php
 * 
 * After running successfully, DELETE this file for security.
 */

require_once 'config.php';

try {
    // Check if admin user already exists
    $stmt = $pdo->prepare("SELECT user_id FROM user_tbl WHERE username = 'admin' LIMIT 1");
    $stmt->execute();
    $existing = $stmt->fetch();

    if ($existing) {
        jsonResponse(['success' => false, 'message' => 'Admin user already exists. Delete it first if you want to reset.']);
    }

    // Create admin user with hashed password
    $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO user_tbl (fullname, username, password, role, status) VALUES (:fullname, :username, :password, :role, :status)");
    $stmt->execute([
        'fullname' => 'System Administrator',
        'username' => 'admin',
        'password' => $hashedPassword,
        'role' => 'admin',
        'status' => 'active'
    ]);

    jsonResponse([
        'success' => true, 
        'message' => 'Admin user created successfully! Username: admin, Password: admin123. Please DELETE this setup.php file now for security.'
    ]);

} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Setup failed: ' . $e->getMessage()], 500);
}
?>
