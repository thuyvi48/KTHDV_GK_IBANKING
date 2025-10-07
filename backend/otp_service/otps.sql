/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     25/09/2025 9:31:16 PM                        */
/*==============================================================*/

DROP TABLE IF EXISTS OTPS;

/*==============================================================*/
/* Table: OTPS                                                  */
/*==============================================================*/
CREATE TABLE OTPS (
   OTP_ID       VARCHAR(10) NOT NULL,
   USER_ID      VARCHAR(10),
   PAYMENT_ID   VARCHAR(50),
   CODE         VARCHAR(10),
   CREATED_AT   DATETIME,
   EXPIRES_AT   DATETIME,
   IS_USED      BOOL,
   STATUS       TEXT,
   PRIMARY KEY (OTP_ID)
);
