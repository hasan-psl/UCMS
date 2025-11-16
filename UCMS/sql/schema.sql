-- UCMS MySQL schema and seed data

CREATE DATABASE IF NOT EXISTS ucms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ucms;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS clubs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(160) NOT NULL,
  description TEXT,
  image_url VARCHAR(512),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  start_time DATETIME NOT NULL,
  location VARCHAR(200),
  image_url VARCHAR(512),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS memberships (
  user_id INT NOT NULL,
  club_id INT NOT NULL,
  role ENUM('member','officer','admin') DEFAULT 'member',
  PRIMARY KEY (user_id, club_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE CASCADE
);

-- Seed: default admin user (password: password)
INSERT INTO users (name, email, password_hash) VALUES
('Admin User', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Seed clubs
INSERT INTO clubs (name, description, image_url) VALUES
('Robotics Club', 'Build, program, and compete.', 'https://images.unsplash.com/photo-1581091870622-7c74b1ad7f36?auto=format&fit=crop&w=1200&q=60'),
('Art Society', 'Creativity and exhibitions.', 'https://images.unsplash.com/photo-1526318472351-c75fcf070305?auto=format&fit=crop&w=1200&q=60'),
('Debate Team', 'Sharpen public speaking.', 'https://images.unsplash.com/photo-1523580846011-23f28a2a8b2b?auto=format&fit=crop&w=1200&q=60');

-- Seed events
INSERT INTO events (title, start_time, location, image_url) VALUES
('Robotics Kickoff', DATE_ADD(NOW(), INTERVAL 3 DAY), 'Engineering Hall', 'https://images.unsplash.com/photo-1551836022-d5d88e9218df?auto=format&fit=crop&w=1200&q=60'),
('Art Exhibition', DATE_ADD(NOW(), INTERVAL 7 DAY), 'Gallery', 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?auto=format&fit=crop&w=1200&q=60'),
('Debate Tournament', DATE_ADD(NOW(), INTERVAL 10 DAY), 'Main Auditorium', 'https://images.unsplash.com/photo-1519455953755-af066f52f1ea?auto=format&fit=crop&w=1200&q=60');


