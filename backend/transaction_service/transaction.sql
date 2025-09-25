/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     25/09/2025 10:10:03 PM                       */
/*==============================================================*/


drop table if exists TRANSACTIONS;

/*==============================================================*/
/* Table: TRANSACTIONS                                          */
/*==============================================================*/
create table TRANSACTIONS
(
   TRANSACTION_ID       varchar(10) not null  comment '',
   PAYMENT_ID           varchar(10)  comment '',
   USER_ID              varchar(10)  comment '',
   CHANGE_AMOUNT        decimal  comment '',
   BALANCE_AFTER        decimal  comment '',
   TYPE                 text  comment '',
   DESCRIPTION          text  comment '',
   CREATED_AT           datetime  comment '',
   primary key (TRANSACTION_ID)
);

INSERT INTO TRANSACTIONS 
(TRANSACTION_ID, PAYMENT_ID, USER_ID, CHANGE_AMOUNT, BALANCE_AFTER, TYPE, DESCRIPTION, CREATED_AT) 
VALUES
('T001', 'P001', 'U001', -5000000,  2000000, 'DEBIT', 'Thanh toán học phí cho MSSV 522H0001', NOW()),
('T002', 'P002', 'U002', -3000000,     0, 'DEBIT', 'Thanh toán học phí cho MSSV 522H0002', NOW()),
('T003', NULL,  'U001',  1000000,  3000000, 'CREDIT', 'Nạp tiền vào tài khoản', NOW());
