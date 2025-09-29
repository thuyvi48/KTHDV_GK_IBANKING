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

