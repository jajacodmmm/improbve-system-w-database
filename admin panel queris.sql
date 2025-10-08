
-- ---------------------------------
-- 2. 'ADMIN USERS' MANAGEMENT (CRUD Examples)
-- ---------------------------------

-- READ: Retrieve a list of all active Admin Users
SELECT user_id, first_name, last_name, email, registration_date
FROM users
WHERE role_id = 1 AND is_active = TRUE
ORDER BY last_name ASC;

-- CREATE: Insert a new Admin User (role_id = 1)
-- REMINDER: ALWAYS HASH THE PASSWORD in PHP before inserting into the DB.
INSERT INTO users (first_name, last_name, email, password_hash, role_id)
VALUES ('New', 'Admin', 'admin@example.com', 'hashed_secure_password', 1);

-- UPDATE: Deactivate an Admin User (Soft Delete is generally preferred over permanent DELETE)
UPDATE users
SET is_active = FALSE
WHERE user_id = [ID_OF_ADMIN_TO_DEACTIVATE] AND role_id = 1;


-- ---------------------------------
-- 3. 'USER LOG' Query
-- ---------------------------------

-- READ: Fetch recent user activity details (last 100 entries)
SELECT
    ul.log_time,
    u.first_name,
    u.last_name,
    r.role_name,
    ul.action_type,
    ul.ip_address
FROM user_logs ul
JOIN users u ON ul.user_id = u.user_id
JOIN roles r ON u.role_id = r.role_id
ORDER BY ul.log_time DESC
LIMIT 100;

-- ---------------------------------
-- 4. 'SUBJECTS' CRUD Examples
-- ---------------------------------

-- CREATE: Add a new subject
INSERT INTO subjects (subject_name, subject_code)
VALUES ('Computer Science', 'CS101');

-- READ: Get all subjects
SELECT subject_id, subject_name, subject_code FROM subjects ORDER BY subject_name;

-- UPDATE: Rename a subject
UPDATE subjects
SET subject_name = 'Advanced Mathematics'
WHERE subject_code = 'MATH101';