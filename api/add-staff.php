<?php
require_once 'config.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);

$fullname = trim($input['fullname'] ?? '');
$username = trim($input['username'] ?? '');
$password = $input['password'] ?? '';
$role = trim($input['role'] ?? 'staff');
$status = trim($input['status'] ?? 'active');

// Validation
if (empty($fullname) || empty($username) || empty($password)) {
    jsonResponse(['success' => false, 'message' => 'Full name, username, and password are required.'], 400);
}

if (!in_array($role, ['admin', 'staff'])) {
    $role = 'staff';
}

if (!in_array($status, ['active', 'inactive'])) {
    $status = 'active';
}

if (strlen($password) < 6) {
    jsonResponse(['success' => false, 'message' => 'Password must be at least 6 characters.'], 400);
}

try {
    // Check if username already exists
    $check = $pdo->prepare("SELECT user_id FROM user_tbl WHERE username = :username LIMIT 1");
    $check->execute(['username' => $username]);
    if ($check->fetch()) {
        jsonResponse(['success' => false, 'message' => 'Username already exists.'], 409);
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO user_tbl (fullname, username, password, role, status) VALUES (:fullname, :username, :password, :role, :status)");
    $stmt->execute([
        'fullname' => $fullname,
        'username' => $username,
        'password' => $hashedPassword,
        'role' => $role,
        'status' => $status
    ]);

    $newId = $pdo->lastInsertId();

    jsonResponse(['success' => true, 'message' => 'Staff member added successfully.', 'user_id' => $newId], 201);

} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Failed to add staff member.'], 500);
}
?>
