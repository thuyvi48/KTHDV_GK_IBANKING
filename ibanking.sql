
/*==============================================================*/
/* Table: OTP                                                   */
/*==============================================================*/
create table OTP
(
   OPT_ID               varchar(10) not null  comment '',
   TRANSACTION_ID       varchar(10)  comment '',
   CODE                 varchar(10)  comment '',
   CREATED_AT           datetime  comment '',
   EXPIRES_AT           datetime  comment '',
   IS_USED              bool  comment '',
   primary key (OPT_ID)
);

/*==============================================================*/
/* Table: STUDENTS                                              */
/*==============================================================*/
create table STUDENTS
(
   MSSV                 varchar(10) not null  comment '',
   FULL_NAME            text  comment '',
   TUITION              decimal  comment '',
   primary key (MSSV)
);

/*==============================================================*/
/* Table: TRANSACTIONS                                          */
/*==============================================================*/
create table TRANSACTIONS
(
   TRANSACTION_ID       varchar(10) not null  comment '',
   MSSV                 varchar(10)  comment '',
   OPT_ID               varchar(10)  comment '',
   USER_ID              varchar(10)  comment '',
   AMOUNT               decimal  comment '',
   TRANSACTION_DATE     datetime  comment '',
   STATUS               text  comment '',
   DESCRIPTION          text  comment '',
   primary key (TRANSACTION_ID)
);

/*==============================================================*/
/* Table: USERS                                                 */
/*==============================================================*/
create table USERS
(
   USER_ID              varchar(10) not null  comment '',
   USENAME              text  comment '',
   PASSWORD             text  comment '',
   FULL_NAME            text  comment '',
   PHONE                text  comment '',
   EMAIL                text  comment '',
   BALANCE              decimal  comment '',
   primary key (USER_ID)
);

