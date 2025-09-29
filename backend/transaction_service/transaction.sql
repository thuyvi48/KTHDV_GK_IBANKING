/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     25/09/2025 10:10:03 PM                       */
/*==============================================================*/


drop table if exists TRANSACTIONS;

/*==============================================================*/
/* Table: TRANSACTIONS                                          */
/*==============================================================*/
create table TRANSACTIONS
(
   TRANSACTION_ID       varchar(10) not null  comment '',
   PAYMENT_ID           varchar(10)  comment '',
   USER_ID              varchar(10)  comment '',
   CHANGE_AMOUNT        decimal  comment '',
   BALANCE_AFTER        decimal  comment '',
   TYPE                 text  comment '',
   DESCRIPTION          text  comment '',
   CREATED_AT           datetime  comment '',
   primary key (TRANSACTION_ID)
);


