
CREATE DATABASE userdb;
USE userdb;

CREATE TABLE users (
  user_id   varchar(10) PRIMARY KEY,
  username  text UNIQUE NOT NULL,
  password  text NOT NULL,
  full_name text,
  phone     text,
  email     text,
  balance   decimal
);

INSERT INTO users (user_id, username, password, full_name, phone, email, balance) VALUES
('U001', 'john_doe', '123456', 'John Doe', '0901234567', 'john@example.com', 5000000),
('U002', 'jane_smith', 'abcdef', 'Jane Smith', '0902345678', 'jane@example.com', 3000000),
('U003', 'admin', 'admin123', 'Admin User', '0903456789', 'admin@example.com', 10000000);
