<?php
require_once 'config.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);

$user_id = intval($input['user_id'] ?? 0);
$fullname = trim($input['fullname'] ?? '');
$username = trim($input['username'] ?? '');
$password = $input['password'] ?? '';
$role = trim($input['role'] ?? 'staff');
$status = trim($input['status'] ?? 'active');

if ($user_id <= 0) {
    jsonResponse(['success' => false, 'message' => 'Invalid user ID.'], 400);
}

if (empty($fullname) || empty($username)) {
    jsonResponse(['success' => false, 'message' => 'Full name and username are required.'], 400);
}

if (!in_array($role, ['admin', 'staff'])) {
    $role = 'staff';
}

if (!in_array($status, ['active', 'inactive'])) {
    $status = 'active';
}

try {
    // Check if username taken by another user
    $check = $pdo->prepare("SELECT user_id FROM user_tbl WHERE username = :username AND user_id != :user_id LIMIT 1");
    $check->execute(['username' => $username, 'user_id' => $user_id]);
    if ($check->fetch()) {
        jsonResponse(['success' => false, 'message' => 'Username already taken by another user.'], 409);
    }

    // Update with or without password
    if (!empty($password)) {
        if (strlen($password) < 6) {
            jsonResponse(['success' => false, 'message' => 'Password must be at least 6 characters.'], 400);
        }
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE user_tbl SET fullname = :fullname, username = :username, password = :password, role = :role, status = :status WHERE user_id = :user_id");
        $stmt->execute([
            'fullname' => $fullname,
            'username' => $username,
            'password' => $hashedPassword,
            'role' => $role,
            'status' => $status,
            'user_id' => $user_id
        ]);
    } else {
        $stmt = $pdo->prepare("UPDATE user_tbl SET fullname = :fullname, username = :username, role = :role, status = :status WHERE user_id = :user_id");
        $stmt->execute([
            'fullname' => $fullname,
            'username' => $username,
            'role' => $role,
            'status' => $status,
            'user_id' => $user_id
        ]);
    }

    if ($stmt->rowCount() === 0) {
        jsonResponse(['success' => false, 'message' => 'Staff not found or no changes made.'], 404);
    }

    jsonResponse(['success' => true, 'message' => 'Staff member updated successfully.']);

} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Failed to update staff member.'], 500);
}
?>
