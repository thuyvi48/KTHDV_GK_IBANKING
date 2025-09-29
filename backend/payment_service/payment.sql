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


