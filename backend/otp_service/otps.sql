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
   USER_ID              varchar(10)  comment '',
   CODE                 varchar(10)  comment '',
   CREATED_AT           datetime  comment '',
   EXPIRES_AT           datetime  comment '',
   IS_USED              bool  comment '',
   STATUS               text  comment '',
   primary key (OTP_ID)
);