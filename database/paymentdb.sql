
CREATE DATABASE paymentdb;
USE paymentdb;

CREATE TABLE transactions (
  transaction_id   varchar(10) PRIMARY KEY,
  mssv             varchar(10),   -- tham chiếu logic sang studentdb
  user_id          varchar(10),   -- tham chiếu logic sang userdb
  amount           decimal,
  transaction_date datetime,
  status           text,
  description      text
);

INSERT INTO transactions (transaction_id, mssv, user_id, amount, transaction_date, status, description) VALUES
('T001', 'S001', 'U001', 15000000, NOW(), 'SUCCESS', 'Thanh toán học phí học kỳ 1'),
('T002', 'S002', 'U002',  6000000, NOW(), 'PENDING', 'Thanh toán một phần học phí'),
('T003', 'S003', 'U001', 18000000, NOW(), 'FAILED',  'Giao dịch thất bại do số dư không đủ');
