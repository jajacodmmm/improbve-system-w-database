-- Assuming your database is named 'student_dashboard' (you must create this first in phpMyAdmin)
-- USE student_dashboard; 

-- 1. Users Table (Core information, including Profile Status details)
CREATE TABLE `users` (
    `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `student_id_number` VARCHAR(20) UNIQUE NOT NULL, -- The unique ID (e.g., 06-24XX-004XXX)
    `user_name` VARCHAR(100) NOT NULL,
    `password` VARCHAR(255) NOT NULL,              -- Store hashed passwords (e.g., using PHP's password_hash())
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `course_id` INT(11) DEFAULT NULL,               -- Foreign Key to the 'courses' table
    `year_level` VARCHAR(20) DEFAULT NULL,
    `profile_photo` VARCHAR(255) DEFAULT NULL,      -- Path to the uploaded photo file name
    `is_online` TINYINT(1) DEFAULT 0,               -- For the 'Online Collaborators' section (0=Offline, 1=Online)
    `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Courses Table (To link users to their course details)
CREATE TABLE `courses` (
    `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `course_code` VARCHAR(10) UNIQUE NOT NULL,       -- E.g., ITE 083
    `course_name` VARCHAR(100) NOT NULL
);

-- 3. Tasks Table (For the 'Today', 'Later', 'Missing', 'Done' sections)
CREATE TABLE `tasks` (
    `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT(11) NOT NULL,                     -- Foreign Key to 'users' (who the task belongs to)
    `course_id` INT(11) DEFAULT NULL,                 -- Foreign Key to 'courses' (which course the task is for)
    `title` VARCHAR(255) NOT NULL,                  -- E.g., ENTITY RELATIONSHIP DIAGRAM
    `due_date` DATETIME NOT NULL,
    `status` ENUM('today', 'later', 'missing', 'done') NOT NULL DEFAULT 'today',
    `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE SET NULL
);

-- 4. Events Table (For the 'Campus Events' and 'Academic Timeline' section)
CREATE TABLE `events` (
    `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `event_date` DATE NOT NULL,
    `event_time` TIME DEFAULT NULL,
    `type` ENUM('campus', 'academic', 'holiday') NOT NULL DEFAULT 'academic'
);

-- 5. Collaborators Table (To track relationships, assuming simple grouping is needed)
-- NOTE: For simple status like in your image, the `is_online` field in the `users` table might be sufficient.
-- This table is for defining actual team/group memberships if required.
CREATE TABLE `collaborators` (
    `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT(11) NOT NULL,                       -- The user whose team this record belongs to
    `collaborator_user_id` INT(11) NOT NULL,          -- The actual collaborator user ID
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`collaborator_user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_collaboration` (`user_id`, `collaborator_user_id`)
);

-- 6. Example Data Insertion (For initial testing)

-- Insert a Course
INSERT INTO `courses` (`course_code`, `course_name`) VALUES 
('ITE 083', 'Database Management System');

-- Insert a Test User (Replace 'your_hashed_password' with a password hash)
INSERT INTO `users` 
    (`student_id_number`, `user_name`, `password`, `email`, `course_id`, `year_level`, `profile_photo`, `is_online`) 
VALUES 
    ('06-24XX-004XXX', 'Angela Cunanan', 'your_hashed_password', 'angela.cunanan@university.edu', 1, '4th Year', 'angela.jpg', 1);

-- Insert Sample Tasks for the Test User (Angela)
INSERT INTO `tasks` 
    (`user_id`, `course_id`, `title`, `due_date`, `status`) 
VALUES 
    (1, 1, 'ENTITY RELATIONSHIP DIAGRAM', NOW()),
    (1, 1, 'TITLE PROPOSAL DOCUMENTATION', NOW());