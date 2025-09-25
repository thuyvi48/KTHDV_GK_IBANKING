/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     25/09/2025 9:36:34 PM                        */
/*==============================================================*/


drop table if exists USERS;

/*==============================================================*/
/* Table: USERS                                                 */
/*==============================================================*/
create table USERS
(
   USER_ID              varchar(10) not null  comment '',
   FULLNAME             text  comment '',
   PHONE                text  comment '',
   EMAIL                text  comment '',
   AVAILABLE__BALANCE   decimal  comment '',
   HOLD_BALANCE         decimal  comment '',
   VERSION              decimal  comment '',
   CREATED_AT           datetime  comment '',
   UPDATED_AT           datetime  comment '',
   primary key (USER_ID)
);

INSERT INTO USERS (USER_ID, FULLNAME, PHONE, EMAIL, AVAILABLE__BALANCE, HOLD_BALANCE, VERSION, CREATED_AT, UPDATED_AT)
VALUES 
('U001', 'John Doe', '0901234567', 'john@example.com', 5000000, 0, 1, NOW(), NOW()),
('U002', 'Jane Smith', '0902345678', 'jane@example.com', 3000000, 0, 1, NOW(), NOW());
