-- Create database if not exists
CREATE DATABASE IF NOT EXISTS prayer_tracker;
USE prayer_tracker;

-- Create prayer_tracking table (legacy)
CREATE TABLE IF NOT EXISTS prayer_tracking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(50) NOT NULL,
    prayer_name VARCHAR(20) NOT NULL,
    status ENUM('pending', 'completed', 'missed') NOT NULL DEFAULT 'pending',
    prayer_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_prayer (user_id, prayer_name, prayer_date)
);

-- Create prayers table (new version)
CREATE TABLE IF NOT EXISTS prayers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(50) NOT NULL,
    prayer_id VARCHAR(20) NOT NULL,
    status ENUM('pending', 'completed') NOT NULL DEFAULT 'pending',
    date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_prayer (user_id, prayer_id, date)
); 