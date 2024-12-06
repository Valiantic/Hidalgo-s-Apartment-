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
    workplace VARCHAR(255),
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    reset_token VARCHAR(255),  -- Column to store the reset token
    token_expiry DATETIME      -- Column to store the token expiration time
);


