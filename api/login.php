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
    $stmt = $pdo->prepare("SELECT id, username, password, full_name FROM users WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        jsonResponse(['success' => false, 'message' => 'Invalid username or password.'], 401);
    }

    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['full_name'] = $user['full_name'];

    jsonResponse([
        'success' => true,
        'message' => 'Login successful.',
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'full_name' => $user['full_name']
        ]
    ]);

} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Server error.'], 500);
}
?>
