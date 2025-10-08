
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



