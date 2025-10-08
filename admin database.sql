- ==============================================
-- SQL SCHEMA FOR SCHOOL ADMIN PANEL (MySQL)
-- Defines tables for Users, Roles, Subjects, Classes, Logs, and Events.
-- ==============================================

-- 1. Table for defining user roles (Admin, Teacher, Student)
CREATE TABLE roles (
    role_id INT PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(50) UNIQUE NOT NULL -- e.g., 'Admin', 'Teacher', 'Student'
);

-- 2. Main Users Table (Stores login credentials and basic info for all users)
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL, -- Always store hashed passwords!
    role_id INT NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (role_id) REFERENCES roles(role_id)
);

-- Initial population of the roles table
INSERT INTO roles (role_name) VALUES ('Admin'), ('Teacher'), ('Student');

-- 3. Table for Subjects
CREATE TABLE subjects (
    subject_id INT PRIMARY KEY AUTO_INCREMENT,
    subject_name VARCHAR(100) UNIQUE NOT NULL,
    subject_code VARCHAR(10) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. Table for Classes/Sections
CREATE TABLE classes (
    class_id INT PRIMARY KEY AUTO_INCREMENT,
    class_name VARCHAR(100) UNIQUE NOT NULL, -- e.g., 'Grade 10 - Section A'
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 5. Link Teachers to Classes/Subjects (Class Schedule)
CREATE TABLE class_schedule (
    schedule_id INT PRIMARY KEY AUTO_INCREMENT,
    teacher_user_id INT NOT NULL,
    class_id INT NOT NULL,
    subject_id INT NOT NULL,
    FOREIGN KEY (teacher_user_id) REFERENCES users(user_id),
    FOREIGN KEY (class_id) REFERENCES classes(class_id),
    FOREIGN KEY (subject_id) REFERENCES subjects(subject_id),
    UNIQUE (teacher_user_id, class_id, subject_id) -- Ensures a unique assignment
);

-- 6. Table for Uploaded Assignments (Used by the 'Uploaded Assignments' menu)
CREATE TABLE assignments (
    assignment_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    file_path VARCHAR(255) NOT NULL,
    uploaded_by_user_id INT NOT NULL, -- The Teacher who uploaded it
    class_id INT,
    subject_id INT,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by_user_id) REFERENCES users(user_id),
    FOREIGN KEY (class_id) REFERENCES classes(class_id),
    FOREIGN KEY (subject_id) REFERENCES subjects(subject_id)
);

-- 7. Table for User Logs (Used by the 'User Log' menu)
CREATE TABLE user_logs (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    action_type ENUM('LOGIN', 'LOGOUT', 'PASSWORD_RESET') NOT NULL,
    log_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- 8. Table for System Activity Logs (Used by the 'Activity Log' menu)
CREATE TABLE activity_logs (
    activity_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT, -- The admin/user performing the action
    action_description VARCHAR(255) NOT NULL, -- e.g., 'Created new Teacher (ID: 101)', 'Updated Subject: Math'
    table_affected VARCHAR(100),
    record_id INT,
    activity_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- 9. Table for Calendar of Events (Used by the 'Calendar of Events' menu)
CREATE TABLE events (
    event_id INT PRIMARY KEY AUTO_INCREMENT,
    event_title VARCHAR(255) NOT NULL,
    event_description TEXT,
    start_date DATETIME NOT NULL,
    end_date DATETIME,
    created_by_user_id INT,
    FOREIGN KEY (created_by_user_id) REFERENCES users(user_id)
);
