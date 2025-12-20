CREATE DATABASE IF NOT EXISTS `1383838`;
USE `1383838`;

CREATE TABLE IF NOT EXISTS uploads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    file_pin VARCHAR(4) NOT NULL UNIQUE,
    file_name VARCHAR(255) NOT NULL,
    file_content LONGBLOB,  -- Kept for backward compatibility if needed, though init_db uses storage_path
    storage_path VARCHAR(255) NULL,
    encryption_iv VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS text_shares (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pin VARCHAR(4) NOT NULL UNIQUE,
    content LONGTEXT NOT NULL,
    iv VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_key VARCHAR(10) NOT NULL UNIQUE,
    owner_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    is_public BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS room_participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    user_id INT NOT NULL,
    role ENUM('owner', 'editor', 'viewer') DEFAULT 'viewer',
    status ENUM('active', 'banned') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_participation (room_id, user_id),
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS signaling_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_key VARCHAR(50) NOT NULL,
    sender_id INT NOT NULL,
    message JSON NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (room_key),
    INDEX (created_at)
);
