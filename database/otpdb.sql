
CREATE DATABASE otpdb;
USE otpdb;

CREATE TABLE otps (
  otp_id         varchar(10) PRIMARY KEY,
  transaction_id varchar(10), -- mapping logic sang paymentdb
  code           varchar(10),
  created_at     datetime,
  expires_at     datetime,
  is_used        boolean
);

INSERT INTO otps (otp_id, transaction_id, code, created_at, expires_at, is_used) VALUES
('O001', 'T001', '123456', NOW(), DATE_ADD(NOW(), INTERVAL 5 MINUTE), TRUE),
('O002', 'T002', '654321', NOW(), DATE_ADD(NOW(), INTERVAL 5 MINUTE), FALSE);
