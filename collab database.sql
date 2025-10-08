--- Drop existing database if exists
CREATE DATABASE lms_collab;
USE lms_collab;



-- Create Tables
CREATE TABLE Roles (
    role_id INT PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(50) UNIQUE NOT NULL
);

CREATE TABLE Users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) UNIQUE NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    role_id INT NOT NULL,
    last_active TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Used for online status
    
    FOREIGN KEY (role_id) REFERENCES Roles(role_id)
);

CREATE TABLE Collaborators (
    collaboration_id INT PRIMARY KEY AUTO_INCREMENT,
    user_a_id INT NOT NULL,
    user_b_id INT NOT NULL,
    relationship_type VARCHAR(50) DEFAULT 'Co-Teacher',
    
    FOREIGN KEY (user_a_id) REFERENCES Users(user_id),
    FOREIGN KEY (user_b_id) REFERENCES Users(user_id),
    
    UNIQUE KEY unique_collaboration (user_a_id, user_b_id)
);

CREATE TABLE Student_Teacher_Map (
    map_id INT PRIMARY KEY AUTO_INCREMENT,
    teacher_id INT NOT NULL,
    student_id INT NOT NULL,
    class_name VARCHAR(100), -- e.g., 'BSIT 2-5'
    
    FOREIGN KEY (teacher_id) REFERENCES Users(user_id),
    FOREIGN KEY (student_id) REFERENCES Users(user_id),
    
    UNIQUE KEY unique_assignment (teacher_id, student_id)
);

-- Insert Sample Data
INSERT INTO Roles (role_id, role_name) VALUES 
(1, 'Teacher'),
(2, 'Student');

INSERT INTO Users (user_id, username, first_name, last_name, email, role_id, last_active) VALUES
(101, 'jferrarullo', 'Jake Israel', 'Ferrarullo', 'jake.f@school.edu', 1, NOW()),
(102, 'mllerena', 'Ma Polouise', 'Llerena', 'ma.p@school.edu', 1, NOW() - INTERVAL 15 MINUTE),
(201, 'student_a', 'FirstnameA', 'LastnameA', 'stda@school.edu', 2, NOW()),
(202, 'student_b', 'FirstnameB', 'LastnameB', 'stdb@school.edu', 2, NOW() - INTERVAL 5 MINUTE),
(203, 'student_c', 'FirstnameC', 'LastnameC', 'stdc@school.edu', 2, NOW());

INSERT INTO Collaborators (user_a_id, user_b_id, relationship_type) VALUES
(101, 102, 'Co-Teacher');

INSERT INTO Student_Teacher_Map (teacher_id, student_id, class_name) VALUES
(101, 201, 'BSIT 2-5'), 
(101, 202, 'BSIT 2-5'),
(101, 203, 'BSIT 2-5'),
(102, 201, 'BSIT 2-5'),
(102, 202, 'BSIT 2-5');