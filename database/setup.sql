CREATE DATABASE IF NOT EXISTS sms_tanzania
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE sms_tanzania;

CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registration_number VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),
    last_name VARCHAR(50) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('Male', 'Female') NOT NULL,
    level ENUM('Primary', 'Secondary') NOT NULL,
    academic_year VARCHAR(9) NOT NULL,
    address TEXT,
    phone VARCHAR(15),
    guardian_name VARCHAR(100) NOT NULL,
    guardian_phone VARCHAR(15) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_registration_number ON students(registration_number);
CREATE INDEX idx_level ON students(level);
CREATE INDEX idx_academic_year ON students(academic_year);
CREATE INDEX idx_created_at ON students(created_at);
