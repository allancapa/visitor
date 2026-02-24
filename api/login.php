<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);

$username = trim($input['username'] ?? '');
$password = $input['password'] ?? '';

if (empty($username) || empty($password)) {
    jsonResponse(['success' => false, 'message' => 'Username and password are required.'], 400);
}

try {
    $stmt = $pdo->prepare("SELECT user_id, username, password, fullname, role, status FROM user_tbl WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        jsonResponse(['success' => false, 'message' => 'Invalid username or password.'], 401);
    }

    if ($user['status'] !== 'active') {
        jsonResponse(['success' => false, 'message' => 'Your account has been deactivated. Contact admin.'], 403);
    }

    // Set session
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['fullname'] = $user['fullname'];
    $_SESSION['role'] = $user['role'];

    jsonResponse([
        'success' => true,
        'message' => 'Login successful.',
        'user' => [
            'user_id' => $user['user_id'],
            'username' => $user['username'],
            'fullname' => $user['fullname'],
            'role' => $user['role']
        ]
    ]);

} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Server error.'], 500);
}
?>
