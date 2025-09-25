/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     25/09/2025 9:35:11 PM                        */
/*==============================================================*/


drop table if exists USERS_AUTH;

/*==============================================================*/
/* Table: USERS_AUTH                                            */
/*==============================================================*/
create table USERS_AUTH
(
   AUTH_ID              varchar(10) not null  comment '',
   USERNAME             text  comment '',
   PASSWORD             text  comment '',
   USER_ID              varchar(10)  comment '',
   CREATED_AT           datetime  comment '',
   UPDATED_AT           datetime  comment '',
   primary key (AUTH_ID)
);

INSERT INTO USERS_AUTH (
  AUTH_ID, USERNAME, PASSWORD, USER_ID, CREATED_AT, UPDATED_AT
) VALUES
('A001', 'john_doe',   '123456',  'U001', NOW(), NOW()),
('A002', 'jane_smith', 'abcdef',  'U002', NOW(), NOW()),
('A003', 'admin',      'admin123','U003', NOW(), NOW());
