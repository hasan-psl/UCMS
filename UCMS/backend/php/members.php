<?php
require_once __DIR__ . '/config.php';

require_auth();

try {
  $action = $_GET['action'] ?? '';
  if ($action === 'get') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) { respond([ 'error' => 'Invalid id' ], 400); }
    $stmt = db()->prepare('SELECT m.member_id, m.name, m.student_id, m.club_id, m.position, m.email, m.contact_no, m.join_date, c.club_name
      FROM members m
      LEFT JOIN clubs c ON c.club_id = m.club_id
      WHERE m.member_id = ?
      LIMIT 1');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) { respond([ 'error' => 'Not found' ], 404); }
    respond([
      'member_id' => (int)$row['member_id'],
      'name' => $row['name'],
      'student_id' => $row['student_id'],
      'club_name' => $row['club_name'],
      'position' => $row['position'],
      'email' => $row['email'],
      'contact_no' => $row['contact_no'],
      'join_date' => $row['join_date'],
    ]);
  }

  $rows = db()->query('SELECT * FROM members ORDER BY name ASC')->fetchAll();
  respond([ 'members' => $rows ]);
} catch (Throwable $e) {
  respond([ 'error' => 'Failed to load members' ], 500);
}
?>


