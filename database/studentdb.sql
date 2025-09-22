
CREATE DATABASE studentdb;
USE studentdb;

CREATE TABLE students (
  mssv       varchar(10) PRIMARY KEY,
  full_name  text,
  tuition    decimal
);

INSERT INTO students (mssv, full_name, tuition) VALUES
('S001', 'Nguyen Van A', 15000000),
('S002', 'Tran Thi B', 12000000),
('S003', 'Le Van C', 18000000);
