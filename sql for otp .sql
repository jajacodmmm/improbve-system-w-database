-- SQL DDL (Data Definition Language) to create the required table
-- Run this in your database tool (e.g., phpMyAdmin) under the database named 'otp_system' (or the one configured in your PHP file).

CREATE TABLE otp_codes (
    -- Unique primary key for internal database use
    id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Stores the user's email or phone number. Must be unique for active OTPs.
    contact_identifier VARCHAR(255) NOT NULL UNIQUE, 
    
    -- Stores the 6-digit OTP code
    otp_code VARCHAR(6) NOT NULL,
    
    -- Timestamp when the OTP was generated
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- The time when the OTP becomes invalid (5 minutes after creation)
    expires_at DATETIME NOT NULL,
    
    -- Indexes for fast lookups during verification and cleanup
    INDEX idx_contact (contact_identifier),
    INDEX idx_expiry (expires_at)
);

-- Note: Make sure the database credentials ($db_host, $db_user, $db_pass, $db_name) 
-- in your 'otp_app_with_db.php' file match your XAMPP/MySQL settings.
