-- DATABASE CREATION FOR DEPLOYMENT
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

-- ADMIN ACCOUNT
INSERT INTO users (fullname, phone_number, work, email, password, role)
VALUES ('Admin', '9999999', 'Administrator', 'hidalgo-apartment@admin.com', '$2y$10$AdR93qoCLMmPv6jhcPrhYeIwOfDaCPWGKzUNROxDT3DrXCf4i1f12', 'admin');

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
    residents INT(11),
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


-- TRANSACTION
CREATE TABLE transaction_info (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    unit VARCHAR(50) NOT NULL,
    monthly_rent_status ENUM('Paid', 'Not Paid', 'No Bill Yet') DEFAULT 'No Bill Yet',
    electricity_status ENUM('Paid', 'Not Paid', 'No Bill Yet') DEFAULT 'No Bill Yet',
    water_status ENUM('Paid', 'Not Paid', 'No Bill Yet') DEFAULT 'No Bill Yet',
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenant(tenant_id)
    ON DELETE CASCADE ON UPDATE CASCADE
);


-- MAINTENANCE 
CREATE TABLE maintenance_request (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT,
    unit VARCHAR(50),
    description TEXT NOT NULL,
    status ENUM('Pending', 'In Progress', 'Resolved') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenant(tenant_id) ON DELETE CASCADE
);

-- MESSAGING FUNCTIONALITY
CREATE TABLE messages (
    message_id INT(11) NOT NULL AUTO_INCREMENT,
    sender_id INT(11) NOT NULL,
    receiver_id INT(11) NOT NULL,
    message_text TEXT COLLATE utf8mb4_general_ci NOT NULL,
    timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (message_id)
);

CREATE VIEW tenant_users AS
SELECT 
    t.tenant_id,
    t.fullname,
    t.phone_number,
    t.units,
    u.id as user_id,
    u.email,
    u.role
FROM tenant t
JOIN users u ON t.fullname = u.fullname AND t.phone_number = u.phone_number;


-- APPOINTMENTS
CREATE TABLE appointments (
    appointment_id INT PRIMARY KEY AUTO_INCREMENT,
    tenant_id INT NOT NULL,
    units VARCHAR(255) NOT NULL,
    appointment_date DATETIME NOT NULL,
    valid_id_path VARCHAR(255) NOT NULL,
    appointment_status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenant(tenant_id),
    INDEX idx_appointment_date (appointment_date),
    INDEX idx_status (appointment_status),
    INDEX idx_tenant (tenant_id)
);