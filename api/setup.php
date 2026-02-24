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
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'admin' LIMIT 1");
    $stmt->execute();
    $existing = $stmt->fetch();

    if ($existing) {
        jsonResponse(['success' => false, 'message' => 'Admin user already exists. Delete it first if you want to reset.']);
    }

    // Create admin user with hashed password
    $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name) VALUES (:username, :password, :full_name)");
    $stmt->execute([
        'username' => 'admin',
        'password' => $hashedPassword,
        'full_name' => 'System Administrator'
    ]);

    jsonResponse([
        'success' => true, 
        'message' => 'Admin user created successfully! Username: admin, Password: admin123. Please DELETE this setup.php file now for security.'
    ]);

} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Setup failed: ' . $e->getMessage()], 500);
}
?>
