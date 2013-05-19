create table users (
userid INTEGER PRIMARY KEY AUTOINCREMENT,
joined DATETIME NOT NULL,
name VARCHAR(50) NOT NULL,
address VARCHAR(50) NOT NULL,
city VARCHAR(20) NOT NULL,
state VARCHAR(10) NOT NULL,
postcode VARCHAR(8) NOT NULL,
country VARCHAR(50) NOT NULL,
phone VARHCAR(30) NOT NULL,
email VARCHAR(50) NOT NULL,
password VARCHAR(50) NOT NULL,
monitoring INT(0) NOT NULL,
supplier VARCHAR(20) NOT NULL,
decision VARCHAR(255) NOT NULL,
installer INT(8) NOT NULL DEFAULT '0',
level INT(1) NOT NULL DEFAULT '1');

create table inverters (
serial VARCHAR(15) NOT NULL,
custid INT(8) NOT NULL);

create table solarinstalls (
installid INTEGER PRIMARY KEY AUTOINCREMENT,
solarbrand VARCHAR(50) NOT NULL,
solarmodel VARCHAR(50) NOT NULL,
solarpanels INT(3) NOT NULL);
