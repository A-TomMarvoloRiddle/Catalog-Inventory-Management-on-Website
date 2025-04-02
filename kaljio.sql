CREATE DATABASE kaljio;
USE kaljio;

CREATE TABLE product (
    pno INT(3) PRIMARY KEY,
    pname VARCHAR(50) NOT NULL,
    compnam VARCHAR(50) NOT NULL,
    featr VARCHAR(50),
    price DECIMAL(8,2)
);

INSERT INTO product VALUES
(1,'Bluetooth Speaker', 'JBL', 'High Bass & Best Sound Quality',999),
(2,'Wireless Earpods', 'Ptron','Water Resistant Touch Sensor',789),
(3,'Headphone', 'JBL', 'High Bass with Extended Battery Life',2600),
(4,'Graphic Tablet', 'XP-pen', '12 inches  with Customizable Express Keys',2700),
(5,'Alexa', 'Amazon', 'High Sound Quality with Various Functions', 4499),
(6,'Home Mini','Google','Fully automated and Perfect for Domestic Purpose',499),
(7,'Wireless Earphones', 'Ptron', 'High Sound Quality with Extended Battery Life', 1189),
(8,'Power Bank', 'Zebronics', '16W, 20000 mAh,Led Percentage & Time Indicator', 1599);

CREATE TABLE login (
    username VARCHAR(50) PRIMARY KEY,
    password VARCHAR(50) NOT NULL
);

INSERT INTO login VALUES
('apaar','12p3'),
('mathur','top');

SELECT * FROM product ORDER BY pno;
select * from login;

#insert into product value(2,'Wireless Earpods', 'Ptron','Water Resistant Touch Sensor',789);
#insert into product value(7,'Wireless Earphones', 'Ptron', 'High Sound Quality with Extended Battery Life', 1189);
