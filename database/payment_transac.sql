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
   STUDENT_ID           varchar(10)  comment '',
   USER_ID              varchar(10)  comment '',
   INVOICE_ID           varchar(10)  comment '',
   AMOUNT               decimal  comment '',
   IDEMPOTENCY          text  comment '',
   STATUS               text  comment '',
   CREATED_AT           datetime  comment '',
   CONFIRM_AT           datetime  comment '',
   primary key (PAYMENT_ID)
);

/*==============================================================*/
/* Table: TRANSACTIONS                                          */
/*==============================================================*/
create table TRANSACTIONS
(
   TRANSACTION_ID       varchar(10) not null  comment '',
   PAYMENT_ID           varchar(10)  comment '',
   USER_ID              varchar(10)  comment '',
   BALANCE_AFTER        decimal  comment '',
   TYPE                 text  comment '',
   CHANGE_AMOUNT        decimal  comment '',
   DESCRIPTION          text  comment '',
   CREATED_AT           datetime  comment '',
   STATUS               text  comment '',
   primary key (TRANSACTION_ID)
);

