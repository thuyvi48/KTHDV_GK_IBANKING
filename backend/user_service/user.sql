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
   FULL_NAME            text  comment '',
   EMAIL                text  comment '',
   PHONE                text  comment '',
   BALANCE              decimal  comment '',
   CREATED_AT           datetime  comment '',
   UPDATED_AT           datetime  comment '',
   primary key (USER_ID)
);
