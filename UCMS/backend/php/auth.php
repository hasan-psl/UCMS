<?php
require_once __DIR__ . '/config.php';

$action = $_GET['action'] ?? 'check';

if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $in = json_input();
  $identifier = trim($in['email'] ?? $in['username'] ?? ''); // allow email or username field from client
  $password = $in['password'] ?? '';

  if (!$identifier || !$password) {
    respond([ 'error' => 'Username/Email and password required' ], 400);
  }

  $stmt = db()->prepare('SELECT admin_id, username, full_name, email, password_hash FROM admin_login WHERE username = ? OR email = ? LIMIT 1');
  $stmt->execute([$identifier, $identifier]);
  $admin = $stmt->fetch();

  $valid = false;
  if ($admin) {
    // Some databases may currently store plain text in password_hash; support both.
    $hash = $admin['password_hash'] ?? '';
    $valid = password_verify($password, $hash) || hash_equals($hash, $password);
  }

  if (!$admin || !$valid) {
    respond([ 'error' => 'Invalid credentials' ], 401);
  }

  $_SESSION['user_id'] = (int)$admin['admin_id'];
  respond([ 'ok' => true, 'user' => [
    'id' => (int)$admin['admin_id'],
    'name' => $admin['full_name'] ?: $admin['username'],
    'email' => $admin['email'],
    'username' => $admin['username'],
  ]]);
}

if ($action === 'logout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
  session_destroy();
  respond([ 'ok' => true ]);
}

if ($action === 'check') {
  if (!isset($_SESSION['user_id'])) {
    respond([ 'user' => null ]);
  }
  $stmt = db()->prepare('SELECT admin_id, username, full_name, email FROM admin_login WHERE admin_id = ?');
  $stmt->execute([$_SESSION['user_id']]);
  $admin = $stmt->fetch();
  if (!$admin) { respond([ 'user' => null ]); }
  respond([ 'user' => [
    'id' => (int)$admin['admin_id'],
    'name' => $admin['full_name'] ?: $admin['username'],
    'email' => $admin['email'],
    'username' => $admin['username'],
  ]]);
}

respond([ 'error' => 'Not found' ], 404);
?>


