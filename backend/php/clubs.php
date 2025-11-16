<?php
require_once __DIR__ . '/config.php';

$action = $_GET['action'] ?? '';

if ($action === 'get') {
  $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
  if ($id <= 0) { respond([ 'error' => 'Invalid id' ], 400); }
  try {
    $stmt = db()->prepare('SELECT club_id, club_name, description, establishment_date, email, faculty_advisor_name, faculty_advisor_email, created_at FROM clubs WHERE club_id = ? LIMIT 1');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) { respond([ 'error' => 'Not found' ], 404); }
    respond([
      'club_id' => (int)$row['club_id'],
      'club_name' => $row['club_name'],
      'description' => $row['description'],
      'establishment_date' => $row['establishment_date'],
      'email' => $row['email'],
      'faculty_advisor_name' => $row['faculty_advisor_name'],
      'faculty_advisor_email' => $row['faculty_advisor_email'],
      'created_at' => $row['created_at'],
    ]);
  } catch (Throwable $e) {
    respond([ 'error' => 'Failed to load club' ], 500);
  }
  exit;
}

// List clubs based on actual schema
try {
  $rows = db()->query('SELECT club_id, club_name, description, establishment_date, email, faculty_advisor_name, faculty_advisor_email, created_at FROM clubs ORDER BY club_name ASC')->fetchAll();
  $clubs = array_map(function($c) {
    // Assign appropriate Unsplash images based on club name
    $name = strtolower($c['club_name']);
    $img = (function($n) {
      if (strpos($n, 'tech') !== false || strpos($n, 'innovator') !== false) {
        return 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1200&q=60';
      }
      if (strpos($n, 'robo') !== false || strpos($n, 'automation') !== false) {
        return 'https://images.unsplash.com/photo-1558137623-ce933996c730?auto=format&fit=crop&w=1200&q=60';
      }
      if (strpos($n, 'debate') !== false || strpos($n, 'oratory') !== false) {
        return 'https://images.unsplash.com/photo-1565689157206-0fddef7589a2?auto=format&fit=crop&w=1200&q=60';
      }
      if (strpos($n, 'sport') !== false || strpos($n, 'fitness') !== false) {
        return 'https://images.unsplash.com/photo-1553778263-73a83bab9b0c?auto=format&fit=crop&w=1200&q=60';
      }
      if (strpos($n, 'cultural') !== false || strpos($n, 'arts') !== false || strpos($n, 'art') !== false) {
        return 'https://images.unsplash.com/photo-1603629242133-adaaa856147c?auto=format&fit=crop&w=1200&q=60';
      }
      return 'https://images.unsplash.com/photo-1523580846011-d3a5bc25702b?auto=format&fit=crop&w=1200&q=60';
    })($name);
    return [
      'id' => (int)$c['club_id'],
      'name' => $c['club_name'],
      'description' => $c['description'],
      'establishment_date' => $c['establishment_date'],
      'email' => $c['email'],
      'faculty_advisor_name' => $c['faculty_advisor_name'],
      'faculty_advisor_email' => $c['faculty_advisor_email'],
      'image_url' => $img,
    ];
  }, $rows);
  respond([ 'clubs' => $clubs ]);
} catch (Throwable $e) {
  respond([ 'error' => 'Failed to load clubs' ], 500);
}
?>


