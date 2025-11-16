<?php
require_once __DIR__ . '/config.php';
require_auth();

// Simple router stub: action=[create|update|delete] & type=[club|event]
$action = $_GET['action'] ?? '';
$type = $_GET['type'] ?? '';
$payload = json_input();

try {
  if ($type === 'club') {
    if ($action === 'create') {
      $stmt = db()->prepare('INSERT INTO clubs (club_name, description, establishment_date, email, faculty_advisor_name, faculty_advisor_email) VALUES (?, ?, ?, ?, ?, ?)');
      $stmt->execute([
        $payload['club_name'] ?? 'New Club',
        $payload['description'] ?? null,
        $payload['establishment_date'] ?? null,
        $payload['email'] ?? null,
        $payload['faculty_advisor_name'] ?? null,
        $payload['faculty_advisor_email'] ?? null,
      ]);
      respond([ 'ok' => true, 'id' => (int)db()->lastInsertId() ]);
    }
    if ($action === 'update') {
      $stmt = db()->prepare('UPDATE clubs SET club_name=?, description=?, establishment_date=?, email=?, faculty_advisor_name=?, faculty_advisor_email=? WHERE club_id=?');
      $stmt->execute([
        $payload['club_name'] ?? '',
        $payload['description'] ?? null,
        $payload['establishment_date'] ?? null,
        $payload['email'] ?? null,
        $payload['faculty_advisor_name'] ?? null,
        $payload['faculty_advisor_email'] ?? null,
        (int)($payload['club_id'] ?? 0),
      ]);
      respond([ 'ok' => true ]);
    }
    if ($action === 'delete') {
      $stmt = db()->prepare('DELETE FROM clubs WHERE club_id=?');
      $stmt->execute([ (int)($_GET['club_id'] ?? 0) ]);
      respond([ 'ok' => true ]);
    }
  }

  if ($type === 'event') {
    if ($action === 'create') {
      $stmt = db()->prepare('INSERT INTO events (event_name, event_date, organiser_club_id, venue, description) VALUES (?, ?, ?, ?, ?)');
      $stmt->execute([
        $payload['event_name'] ?? 'New Event',
        $payload['event_date'] ?? date('Y-m-d'),
        (int)($payload['organiser_club_id'] ?? 0),
        $payload['venue'] ?? null,
        $payload['description'] ?? null,
      ]);
      respond([ 'ok' => true, 'id' => (int)db()->lastInsertId() ]);
    }
    if ($action === 'update') {
      $stmt = db()->prepare('UPDATE events SET event_name=?, event_date=?, organiser_club_id=?, venue=?, description=? WHERE event_id=?');
      $stmt->execute([
        $payload['event_name'] ?? '',
        $payload['event_date'] ?? date('Y-m-d'),
        (int)($payload['organiser_club_id'] ?? 0),
        $payload['venue'] ?? null,
        $payload['description'] ?? null,
        (int)($payload['event_id'] ?? 0),
      ]);
      respond([ 'ok' => true ]);
    }
    if ($action === 'delete') {
      $stmt = db()->prepare('DELETE FROM events WHERE event_id=?');
      $stmt->execute([ (int)($_GET['event_id'] ?? 0) ]);
      respond([ 'ok' => true ]);
    }
  }

  respond([ 'error' => 'Unsupported action' ], 400);
} catch (Throwable $e) {
  respond([ 'error' => 'Server error', 'detail' => $e->getMessage() ], 500);
}
?>


