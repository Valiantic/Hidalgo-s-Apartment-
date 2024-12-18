-- DATABASE CREATION
CREATE DATABASE db_apartment;

--FOR CONTACT US
CREATE TABLE contact_us (
  email varchar(100) NOT NULL,
  full_name varchar(100) NOT NULL,
  message varchar(100) NOT NULL,
  created_at timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- USER ACCOUNTS
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255) NOT NULL,
    phone_number VARCHAR(15) NOT NULL,
    work VARCHAR(255),
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    reset_token VARCHAR(255),  -- Column to store the reset token
    token_expiry DATETIME      -- Column to store the token expiration time
);

-- TENANTS
CREATE TABLE tenant (
    tenant_id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255) NOT NULL,
    phone_number VARCHAR(15) NOT NULL,
    work VARCHAR(255),
    downpayment DECIMAL NOT NULL,
    advance DECIMAL(10, 2) NOT NULL,
    electricity DECIMAL(10, 2) NOT NULL,
    water DECIMAL(10, 2) NOT NULL,
    units VARCHAR(255),
    move_in_date DATE
);


-- TENANT HISTORY
CREATE TABLE tenant_history (
    history_id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    fullname VARCHAR(255) NOT NULL,
    phone_number VARCHAR(15) NOT NULL,
    work VARCHAR(255),
    downpayment DECIMAL(10, 2) NOT NULL,
    advance DECIMAL(10, 2) NOT NULL,
    electricity DECIMAL(10, 2) NOT NULL,
    water DECIMAL(10, 2) NOT NULL,
    units VARCHAR(50) NOT NULL,
    move_in_date DATE NOT NULL,
    move_out_date DATE NOT NULL,
    deleted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- FOREIGN KEY CONSTRAINT
ALTER TABLE tenant ADD COLUMN user_id INT;

UPDATE tenant
SET user_id = (
    SELECT id
    FROM users
    WHERE users.fullname = tenant.fullname
    LIMIT 1
);

UPDATE tenant SET user_id = 1 WHERE user_id IS NULL;

ALTER TABLE tenant
ADD CONSTRAINT fk_user_id FOREIGN KEY (user_id) REFERENCES users(id)
ON DELETE CASCADE ON UPDATE CASCADE;

