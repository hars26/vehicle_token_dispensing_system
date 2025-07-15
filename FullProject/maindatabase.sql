create database parkinglot;
use parkinglot;


CREATE TABLE CustomerRecords (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    vehicle_number VARCHAR(15) NOT NULL,
    name VARCHAR(50) NOT NULL,
    phone_number VARCHAR(15) NOT NULL,
    email VARCHAR(50) NOT NULL,
    Vehicle_type VARCHAR(15),
    is_parked TINYINT DEFAULT 0
);

CREATE TABLE ParkingRecords (
    vehicle_number VARCHAR(13),
    vehicle_amt FLOAT,
    reference_id CHAR(6),
    token_number INT,
    time_out TIMESTAMP,
    time_in TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PType VARCHAR(9),
    duration INT
);

CREATE TABLE Carriage(
    id INT AUTO_INCREMENT PRIMARY KEY,
    carriage_number INT NOT NULL,
    space_number INT NOT NULL,
    vehicle_number VARCHAR(20) NOT NULL,
    UNIQUE(carriage_number, space_number)
);

CREATE TABLE CarriageRecords(
	id INT AUTO_INCREMENT PRIMARY KEY,
    carriage_number INT NOT NULL,
    space_number INT NOT NULL,
    vehicle_number VARCHAR(20) NOT NULL,
    time_in TIMESTAMP,
    UNIQUE(carriage_number, space_number)
);

CREATE TABLE Valet (
    v_id INT AUTO_INCREMENT PRIMARY KEY,
    reference_id CHAR(6),
    vehicle_number VARCHAR(20) NOT NULL,
    vehicle_type VARCHAR(50) NOT NULL,
    Slot_number int,
    parked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE ValetRecords (
    v_id INT AUTO_INCREMENT PRIMARY KEY,
    reference_id CHAR(6),
    vehicle_number VARCHAR(20) NOT NULL,
    vehicle_type VARCHAR(50) NOT NULL,
    slot_number int,
    parked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


desc CustomerRecords;
desc ParkingRecords;
desc Carriage;
desc Valet;

select * from CustomerRecords;
select * from ParkingRecords;
select * from Carriage;
select * from CarriageRecords;
select * from Valet;
select * from ValetRecords;

delete from CustomerRecords;
delete from ParkingRecords;
delete from Carriage;
delete from CarriageRecords;
delete from Valet;
delete from ValetRecords;

drop table CustomerRecords;
drop table ParkingRecords;
drop table Carriage;
drop table CarriageRecords;
drop table Valet;
drop table ValetRecords;

