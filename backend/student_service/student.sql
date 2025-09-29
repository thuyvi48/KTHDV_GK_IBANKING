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


