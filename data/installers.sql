create table installers (
instid INTEGER PRIMARY KEY AUTOINCREMENT,
instdate DATETIME NOT NULL,
instname VARCHAR(30) NOT NULL,
instabn VARCHAR(20) NOT NULL,
instaddress VARCHAR(50) NOT NULL,
instcity VARCHAR(50) NOT NULL,
inststate VARCHAR(20) NOT NULL,
instpcode VARCHAR(10) NOT NULL,
instcountry VARCHAR(3) NOT NULL,
instphone VARCHAR(50) NOT NULL,
instemail VARCHAR(50) NOT NULL,
instperson VARCHAR(50) NOT NULL);
