create database parking;
USE parking;
create table vehicle (vehicle_no varchar(50), vehicle_type varchar(50), parking_type varchar(50),in_days varchar(30),vehi_amt float);
insert into vehicle value('TN 38 CF 7519', 'FOUR WHEELER', 'VALET','3 DAYS',50);
insert into vehicle value('TN 99 F 2510', 'FOUR WHEELER', 'CARRIAGE','2 DAYS',50);
insert into vehicle value('TN 66 BC 0597', 'FOUR WHEELER', 'VALET','1 DAY', 50);
insert into vehicle value('TN 37 AB 6055', 'FOUR WHEELER', 'VALET','4 DAYS',50);
insert into vehicle value('TN 39 MK 8764', 'FOUR WHEELER', 'CARRIAGE','2 DAYS',50);
insert into vehicle value('TN 38 CF 7519', 'FOUR WHEELER', 'VALET','3 DAYS',50);
insert into vehicle value('TN 99 F 2510', 'FOUR WHEELER', 'CARRIAGE','2 DAYS',50);
insert into vehicle value('TN 38 CF 7519', 'FOUR WHEELER', 'VALET','3 DAYS',50);
insert into vehicle (vehicle_type, vehi_amt) values ('TWO WHEELER', 30.00), ('FOUR WHEELER', 50.00);