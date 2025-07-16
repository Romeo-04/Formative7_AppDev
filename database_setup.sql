CREATE DATABASE IF NOT EXISTS user_management;
USE user_management;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    userlevel ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    status ENUM('active', 'inactive', 'pending') NOT NULL DEFAULT 'active',
    image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert 10 sample records
INSERT INTO users (email, username, password, userlevel, status, image) VALUES
('admin@example.com', 'admin1', MD5('admin123'), 'admin', 'active', 'images/admin1.jpg'),
('john.doe@email.com', 'johndoe', MD5('password123'), 'user', 'active', 'images/john.jpg'),
('jane.smith@email.com', 'janesmith', MD5('jane456'), 'user', 'active', 'images/jane.jpg'),
('mike.wilson@email.com', 'mikewilson', MD5('mike789'), 'user', 'inactive', 'images/mike.jpg'),
('sarah.brown@email.com', 'sarahb', MD5('sarah321'), 'admin', 'active', 'images/sarah.jpg'),
('david.lee@email.com', 'davidlee', MD5('david654'), 'user', 'pending', 'images/david.jpg'),
('lisa.garcia@email.com', 'lisag', MD5('lisa987'), 'user', 'active', 'images/lisa.jpg'),
('robert.taylor@email.com', 'robertt', MD5('robert147'), 'user', 'active', 'images/robert.jpg'),
('emily.davis@email.com', 'emilyd', MD5('emily258'), 'admin', 'active', 'images/emily.jpg'),
('chris.martinez@email.com', 'chrism', MD5('chris369'), 'user', 'inactive', 'images/chris.jpg');
