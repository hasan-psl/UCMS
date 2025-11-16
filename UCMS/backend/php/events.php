<?php
require_once __DIR__ . '/config.php';

$action = $_GET['action'] ?? '';

if ($action === 'get') {
  $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
  if ($id <= 0) { respond([ 'error' => 'Invalid id' ], 400); }
  try {
    $stmt = db()->prepare('SELECT e.event_id, e.event_name, e.event_date, e.organiser_club_id, e.venue, e.description, c.club_name
      FROM events e
      LEFT JOIN clubs c ON c.club_id = e.organiser_club_id
      WHERE e.event_id = ?
      LIMIT 1');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) { respond([ 'error' => 'Not found' ], 404); }
    respond([
      'event_id' => (int)$row['event_id'],
      'event_name' => $row['event_name'],
      'event_date' => $row['event_date'],
      'venue' => $row['venue'],
      'organiser_club_name' => $row['club_name'],
      'description' => $row['description'],
    ]);
  } catch (Throwable $e) {
    respond([ 'error' => 'Failed to load event' ], 500);
  }
  exit;
}

try {
  $rows = db()->query('SELECT event_id, event_name, event_date, organiser_club_id, venue, description FROM events ORDER BY event_date ASC')->fetchAll();
  $events = array_map(function($e) {
    $title = (string)$e['event_name'];
    $img = (function($t) {
      $t = strtolower($t);
      if (strpos($t, 'hack') !== false) return 'https://images.unsplash.com/photo-1544890225-2f3faec4cd60?auto=format&fit=crop&w=1200&q=60';
      if (strpos($t, 'arts') !== false || strpos($t, 'art') !== false) return 'https://images.unsplash.com/photo-1603629242133-adaaa856147c?auto=format&fit=crop&w=1200&q=60';
      if (strpos($t, 'robo') !== false) return 'https://images.unsplash.com/photo-1558137623-ce933996c730?auto=format&fit=crop&w=1200&q=60';
      if (strpos($t, 'debate') !== false) return 'https://images.unsplash.com/photo-1565689157206-0fddef7589a2?auto=format&fit=crop&w=1200&q=60';
      if (strpos($t, 'sport') !== false) return 'https://images.unsplash.com/photo-1553778263-73a83bab9b0c?auto=format&fit=crop&w=1200&q=60';
      if (strpos($t, 'cultural') !== false | strpos($t, 'arts') !== false || strpos($t, 'art') !== false) return 'https://images.unsplash.com/photo-1761124739617-03f139e2f51d?auto=format&fit=crop&w=1200&q=60';
      if (strpos($t, 'tech') !== false) return 'https://images.unsplash.com/photo-1713345248737-2698000f143d?auto=format&fit=crop&w=1200&q=60';
      if (strpos($t, 'talk') !== false || strpos($t, 'seminar') !== false) return 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=1200&q=60';
      if (strpos($t, 'music') !== false) return 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=1200&q=60';
    })($title);
    return [
      'id' => (int)$e['event_id'],
      'title' => $title,
      'start_time' => date('c', strtotime($e['event_date'])),
      'location' => $e['venue'],
      'organiser_club_id' => (int)$e['organiser_club_id'],
      'description' => $e['description'],
      'image_url' => $img,
    ];
  }, $rows);
  respond([ 'events' => $events ]);
} catch (Throwable $e) {
  respond([ 'error' => 'Failed to load events' ], 500);
}
?>


