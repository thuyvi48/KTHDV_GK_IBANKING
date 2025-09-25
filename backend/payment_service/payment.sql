/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     25/09/2025 9:53:55 PM                        */
/*==============================================================*/


drop table if exists PAYMENTS;

/*==============================================================*/
/* Table: PAYMENTS                                              */
/*==============================================================*/
create table PAYMENTS
(
   PAYMENT_ID           varchar(10) not null  comment '',
   USER_ID              varchar(10)  comment '',
   STUDENT_ID           varchar(10)  comment '',
   INVOICE_ID           varchar(10)  comment '',
   AMOUNT               decimal  comment '',
   STATUS               text  comment '',
   IDEPOTENCY           varchar(128)  comment '',
   CREATED_AT           datetime  comment '',
   CONFIRM_AT           datetime  comment '',
   primary key (PAYMENT_ID)
);

INSERT INTO PAYMENTS (
  PAYMENT_ID, USER_ID, STUDENT_ID, INVOICE_ID, AMOUNT, STATUS, IDEPOTENCY, CREATED_AT, CONFIRM_AT
) VALUES
('P001', 'U001', 'S001', 'INV001', 5000000, 'PENDING', 'key-abc-123', NOW(), NULL),
('P002', 'U002', 'S002', 'INV002', 3000000, 'CONFIRMED', 'key-def-456', NOW(), NOW()),
('P003', 'U001', 'S003', 'INV003', 2000000, 'FAILED', 'key-ghi-789', NOW(), NULL);
