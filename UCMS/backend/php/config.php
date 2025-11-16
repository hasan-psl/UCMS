<?php
// Basic config and helpers
session_start();

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

// Allow same-origin; adjust for development if needed
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
  header('Access-Control-Allow-Headers: Content-Type');
  http_response_code(204);
  exit;
}
header('Access-Control-Allow-Origin: *');

$DB_HOST = '127.0.0.1';
$DB_NAME = 'ucms';
$DB_USER = 'root';
$DB_PASS = '';

function db() {
  static $pdo = null;
  global $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS;
  if ($pdo === null) {
    $dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4";
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
  }
  return $pdo;
}

function json_input() {
  $raw = file_get_contents('php://input');
  return $raw ? json_decode($raw, true) : [];
}

function respond($data, $code = 200) {
  http_response_code($code);
  echo json_encode($data);
  exit;
}

function require_auth() {
  if (!isset($_SESSION['user_id'])) {
    respond([ 'error' => 'Unauthorized' ], 401);
  }
}

?>


