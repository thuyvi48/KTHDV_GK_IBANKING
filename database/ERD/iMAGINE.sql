/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     03/10/2025 10:29:29 PM                       */
/*==============================================================*/


alter table OTPS 
   drop foreign key FK_OTPS_USERS_OTP_USERS;

alter table PAYMENTS 
   drop foreign key FK_PAYMENTS_STUDENTS__STUDENTS;

alter table PAYMENTS 
   drop foreign key FK_PAYMENTS_TUITION_P_TUITION_;

alter table PAYMENTS 
   drop foreign key FK_PAYMENTS_USERS_PAY_USERS;

alter table TRANSACTIONS 
   drop foreign key FK_TRANSACT_TRANSACTI_PAYMENTS;

alter table TRANSACTIONS 
   drop foreign key FK_TRANSACT_USERS_TRA_USERS;

alter table TUITION_INVOICES 
   drop foreign key FK_TUITION__STUDENTS__STUDENTS;

alter table USERS_AUTH 
   drop foreign key FK_USERS_AU_USERS_USE_USERS;


alter table OTPS 
   drop foreign key FK_OTPS_USERS_OTP_USERS;

drop table if exists OTPS;


alter table PAYMENTS 
   drop foreign key FK_PAYMENTS_USERS_PAY_USERS;

alter table PAYMENTS 
   drop foreign key FK_PAYMENTS_STUDENTS__STUDENTS;

alter table PAYMENTS 
   drop foreign key FK_PAYMENTS_TUITION_P_TUITION_;

drop table if exists PAYMENTS;

drop table if exists STUDENTS;


alter table TRANSACTIONS 
   drop foreign key FK_TRANSACT_TRANSACTI_PAYMENTS;

alter table TRANSACTIONS 
   drop foreign key FK_TRANSACT_USERS_TRA_USERS;

drop table if exists TRANSACTIONS;


alter table TUITION_INVOICES 
   drop foreign key FK_TUITION__STUDENTS__STUDENTS;

drop table if exists TUITION_INVOICES;

drop table if exists USERS;


alter table USERS_AUTH 
   drop foreign key FK_USERS_AU_USERS_USE_USERS;

drop table if exists USERS_AUTH;

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
/* Table: STUDENTS                                              */
/*==============================================================*/
create table STUDENTS
(
   STUDENT_ID           varchar(10) not null  comment '',
   MSSV                 decimal  comment '',
   FULL_NAME            text  comment '',
   EMAIL                text  comment '',
   CREATED_AT           datetime  comment '',
   primary key (STUDENT_ID)
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

/*==============================================================*/
/* Table: TUITION_INVOICES                                      */
/*==============================================================*/
create table TUITION_INVOICES
(
   INVOICE_ID           varchar(10) not null  comment '',
   STUDENT_ID           varchar(10)  comment '',
   SEMESTER             int  comment '',
   AMOUNT_DUE           decimal  comment '',
   AMOUNT_PAID          decimal  comment '',
   STATUS               text  comment '',
   CREATED_AT           datetime  comment '',
   UPDATED_AT           datetime  comment '',
   primary key (INVOICE_ID)
);

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

/*==============================================================*/
/* Table: USERS_AUTH                                            */
/*==============================================================*/
create table USERS_AUTH
(
   AUTH_ID              varchar(10) not null  comment '',
   USER_ID              varchar(10)  comment '',
   USERNAME             text  comment '',
   PASSWORD             text  comment '',
   CREATED_AT           datetime  comment '',
   UPDATED_AT           datetime  comment '',
   primary key (AUTH_ID)
);

alter table OTPS add constraint FK_OTPS_USERS_OTP_USERS foreign key (USER_ID)
      references USERS (USER_ID) on delete restrict on update restrict;

alter table PAYMENTS add constraint FK_PAYMENTS_STUDENTS__STUDENTS foreign key (STUDENT_ID)
      references STUDENTS (STUDENT_ID) on delete restrict on update restrict;

alter table PAYMENTS add constraint FK_PAYMENTS_TUITION_P_TUITION_ foreign key (INVOICE_ID)
      references TUITION_INVOICES (INVOICE_ID) on delete restrict on update restrict;

alter table PAYMENTS add constraint FK_PAYMENTS_USERS_PAY_USERS foreign key (USER_ID)
      references USERS (USER_ID) on delete restrict on update restrict;

alter table TRANSACTIONS add constraint FK_TRANSACT_TRANSACTI_PAYMENTS foreign key (PAYMENT_ID)
      references PAYMENTS (PAYMENT_ID) on delete restrict on update restrict;

alter table TRANSACTIONS add constraint FK_TRANSACT_USERS_TRA_USERS foreign key (USER_ID)
      references USERS (USER_ID) on delete restrict on update restrict;

alter table TUITION_INVOICES add constraint FK_TUITION__STUDENTS__STUDENTS foreign key (STUDENT_ID)
      references STUDENTS (STUDENT_ID) on delete restrict on update restrict;

alter table USERS_AUTH add constraint FK_USERS_AU_USERS_USE_USERS foreign key (USER_ID)
      references USERS (USER_ID) on delete restrict on update restrict;

