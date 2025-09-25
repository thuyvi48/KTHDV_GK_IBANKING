/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     25/09/2025 9:29:57 PM                        */
/*==============================================================*/


drop table if exists STUDENTS;

drop table if exists TUITION_VOICES;

/*==============================================================*/
/* Table: STUDENTS                                              */
/*==============================================================*/
create table STUDENTS
(
   ID                   varchar(10) not null  comment '',
   MSSV                 varchar(10)  comment '',
   FULLNAME             text  comment '',
   EMAIL                text  comment '',
   CREATED_AT           datetime  comment '',
   primary key (ID)
);

/*==============================================================*/
/* Table: TUITION_VOICES                                        */
/*==============================================================*/
create table TUITION_VOICES
(
   ID                   varchar(10) not null  comment '',
   STUDENT_ID           varchar(10)  comment '',
   SEMESTER             int  comment '',
   AMOUNT_DUE           decimal  comment '',
   AMOUNT_PAID          decimal  comment '',
   STATUS               text  comment '',
   CREATED_AT           datetime  comment '',
   UPDATED_AT           datetime  comment '',
   primary key (ID)
);

-- Thêm sinh viên
INSERT INTO STUDENTS (ID, MSSV, FULLNAME, EMAIL, CREATED_AT) VALUES
('S001', '522H0001', 'Nguyen Van A', 'vana@tdtu.edu.vn', NOW()),
('S002', '522H0002', 'Tran Thi B', 'thib@tdtu.edu.vn', NOW()),
('S003', '522H0003', 'Le Van C', 'vanc@tdtu.edu.vn', NOW());

-- Thêm hóa đơn học phí
INSERT INTO TUITION_VOICES (ID, STUDENT_ID, SEMESTER, AMOUNT_DUE, AMOUNT_PAID, STATUS, CREATED_AT, UPDATED_AT) VALUES
('INV001', 'S001', 20241, 5000000, 0, 'UNPAID', NOW(), NOW()),
('INV002', 'S002', 20241, 3000000, 3000000, 'PAID', NOW(), NOW()),
('INV003', 'S003', 20241, 4000000, 2000000, 'PARTIAL', NOW(), NOW());
