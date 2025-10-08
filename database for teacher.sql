-- 1. DATABASE MANAGEMENT
-- Drop the database if it exists to ensure a clean slate
DROP DATABASE IF EXISTS lms_collab;
-- Create the new database
CREATE DATABASE lms_collab;
-- Use the newly created database for subsequent commands
USE lms_collab;

-- 2. ROLES TABLE
-- Defines the types of users (e.g., Teacher, Student).
CREATE TABLE Roles (
    role_id INT PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(50) UNIQUE NOT NULL -- e.g., 'Teacher', 'Student', 'Admin'
);

-- 3. USERS TABLE
-- Stores basic user information, linked to a Role.
CREATE TABLE Users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) UNIQUE NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    role_id INT NOT NULL,
    password_hash VARCHAR(255) NOT NULL, -- Added for realistic login simulation
    last_active TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Used for online status
    
    FOREIGN KEY (role_id) REFERENCES Roles(role_id)
);

-- 4. COLLABORATORS TABLE
-- Defines the collaboration relationships, primarily between Teachers.
-- This is used for the 'Online Collaborators' panel.
CREATE TABLE Collaborators (
    collaboration_id INT PRIMARY KEY AUTO_INCREMENT,
    user_a_id INT NOT NULL,
    user_b_id INT NOT NULL,
    relationship_type VARCHAR(50) DEFAULT 'Co-Teacher', -- e.g., 'Co-Teacher', 'PLC Member'
    
    FOREIGN KEY (user_a_id) REFERENCES Users(user_id),
    FOREIGN KEY (user_b_id) REFERENCES Users(user_id),
    
    -- Ensures a unique pair regardless of order (A, B) or (B, A)
    UNIQUE KEY unique_collaboration (user_a_id, user_b_id)
);

-- 5. STUDENT_TEACHER_MAP TABLE
-- Defines which students are taught by which teacher.
CREATE TABLE Student_Teacher_Map (
    map_id INT PRIMARY KEY AUTO_INCREMENT,
    teacher_id INT NOT NULL,
    student_id INT NOT NULL,
    class_name VARCHAR(100), -- e.g., 'BSIT 2-5'
    
    FOREIGN KEY (teacher_id) REFERENCES Users(user_id),
    FOREIGN KEY (student_id) REFERENCES Users(user_id),
    
    UNIQUE KEY unique_assignment (teacher_id, student_id)
);

-- 6. INSERT INITIAL DATA

-- Insert Roles
INSERT INTO Roles (role_id, role_name) VALUES 
(1, 'Teacher'),
(2, 'Student');

-- Insert Users (Teachers and Students)
-- NOTE: 'password' is used as a placeholder password_hash for simulation
INSERT INTO Users (user_id, username, first_name, last_name, email, role_id, password_hash, last_active) VALUES
-- Teacher: Angela Cunanan (The logged-in user in your screenshot)
(100, 'acunanan', 'Angela', 'Cunanan', 'angela.c@school.edu', 1, 'password', NOW()), 
-- Teacher: Jake Israel Ferrarullo (Collaborator)
(101, 'jferrarullo', 'Jake Israel', 'Ferrarullo', 'jake.f@school.edu', 1, 'password', NOW()),
-- Teacher: Ma Polouise Llerena (Collaborator)
(102, 'mllerena', 'Ma Polouise', 'Llerena', 'ma.p@school.edu', 1, 'password', NOW() - INTERVAL 15 MINUTE),
-- Students
(201, 'student_a', 'FirstnameA', 'LastnameA', 'stda@school.edu', 2, 'password', NOW()),
(202, 'student_b', 'FirstnameB', 'LastnameB', 'stdb@school.edu', 2, 'password', NOW() - INTERVAL 5 MINUTE);


-- Insert Collaborators
-- Angela Cunanan (100) collaborates with Jake (101) and Ma Polouise (102)
INSERT INTO Collaborators (user_a_id, user_b_id, relationship_type) VALUES
(100, 101, 'Co-Teacher'),
(100, 102, 'PLC Member'); 


-- Insert Student-Teacher Mapping
-- Angela (100) teaches all these students
INSERT INTO Student_Teacher_Map (teacher_id, student_id, class_name) VALUES
(100, 201, 'BSIT 2-5'), 
(100, 202, 'BSIT 2-5'),
(101, 201, 'BSIT 2-5'); -- Jake also teaches student_a