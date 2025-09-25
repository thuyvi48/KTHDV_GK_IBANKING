/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     25/09/2025 9:31:16 PM                        */
/*==============================================================*/


drop table if exists OTPS;

/*==============================================================*/
/* Table: OTPS                                                  */
/*==============================================================*/
create table OTPS
(
   OTP_ID               varchar(10) not null  comment '',
   TRANSACTION_ID       varchar(10)  comment '',
   USER_ID              varchar(10)  comment '',
   CODE                 varchar(10)  comment '',
   STATUS               text  comment '',
   CREATED_AT           datetime  comment '',
   EXPIRES_AT           datetime  comment '',
   ATTEMPTS             int  comment '',
   primary key (OTP_ID)
);

INSERT INTO OTPS (
  OTP_ID, TRANSACTION_ID, USER_ID, CODE, STATUS, CREATED_AT, EXPIRES_AT, ATTEMPTS
) VALUES
('O001', 'T001', 'U001', '458921', 'ACTIVE',  NOW(), DATE_ADD(NOW(), INTERVAL 5 MINUTE), 0),
('O002', 'T002', 'U002', '782345', 'USED',    NOW(), DATE_ADD(NOW(), INTERVAL 5 MINUTE), 1),
('O003', 'T003', 'U001', '129876', 'EXPIRED', NOW(), DATE_ADD(NOW(), INTERVAL -1 MINUTE), 3);
