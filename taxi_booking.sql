CREATE DATABASE IF NOT EXISTS Taxi_Booking;
USE Taxi_Booking;

-- Customer Table
CREATE TABLE Customer (
    C_id INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    Addr VARCHAR(255),
    Number VARCHAR(15) UNIQUE NOT NULL,
    Password VARCHAR(255) NOT NULL  -- Hashed password
);

-- Driver Table
CREATE TABLE Driver (
    D_id INT AUTO_INCREMENT PRIMARY KEY,
    D_Name VARCHAR(255) NOT NULL,
    Rating INT CHECK (Rating >= 0 AND Rating <= 5),  -- Rating as an integer between 0 and 5
    D_Number VARCHAR(15) UNIQUE NOT NULL,
    Status ENUM('Available', 'Not Available') DEFAULT 'Available',  -- Driver availability status
    Password VARCHAR(255) NOT NULL  -- Hashed password for authentication
);
-- Booking Table
CREATE TABLE Booking (
    B_id INT AUTO_INCREMENT PRIMARY KEY,
    C_id INT,                         -- Foreign key to Customer
    D_id INT,                         -- Foreign key to Driver (if assigned)
    Pick_loc VARCHAR(255) NOT NULL,
    Drop_loc VARCHAR(255) NOT NULL,
    Status ENUM('Pending', 'Booked') DEFAULT 'Pending',  -- Booking status
    Time DATETIME NOT NULL,
    Distance FLOAT,                   -- Distance of the booking
    FOREIGN KEY (C_id) REFERENCES Customer(C_id),
    FOREIGN KEY (D_id) REFERENCES Driver(D_id)
);
-- Car Table
CREATE TABLE Car (
    Car_id INT AUTO_INCREMENT PRIMARY KEY,
    Reg VARCHAR(50) UNIQUE NOT NULL,  -- Registration number
    CarKe_id INT,                     -- Foreign key reference if needed
    Car_D ENUM('Mini', 'Sedan', 'SUV') NOT NULL,  -- Car model/type with options
    Type VARCHAR(50) NOT NULL,        -- Car type (e.g., Sedan, SUV)
    B_id INT,                         -- Foreign key to Booking table
    FOREIGN KEY (B_id) REFERENCES Booking(B_id)
);



-- Payment Table
CREATE TABLE Payment (
    P_id INT AUTO_INCREMENT PRIMARY KEY,
    B_id INT,                         -- Foreign key to Booking
    Mode ENUM('Cash', 'UPI') DEFAULT 'Cash',  -- Payment method
    Distance FLOAT,                   -- Distance related to payment
    -- CP_id INT,                        -- Reference to another payment entity, if needed
    Cost DECIMAL(10, 2) NOT NULL,     -- Cost of the ride
    FOREIGN KEY (B_id) REFERENCES Booking(B_id)
);

-- Feedback Table
CREATE TABLE Feedback (
    F_id INT AUTO_INCREMENT PRIMARY KEY,
    C_id INT,                         -- Foreign key to Customer
    D_id INT,                         -- Foreign key to Driver
    Rating INT CHECK (Rating >= 1 AND Rating <= 5),  -- Rating between 1 and 5
    Comments TEXT,                    -- Optional comments
    FOREIGN KEY (C_id) REFERENCES Customer(C_id),
    FOREIGN KEY (D_id) REFERENCES Driver(D_id)
);

SHOW TABLES;

INSERT INTO Driver (D_Name, Rating, D_Number, Status, Password) VALUES
('Rajesh Kumar', 5, '9876543210', 'Available', 'hashedpassword1'),
('Priya Sharma', 4, '9876543211', 'Available', 'hashedpassword2'),
('Amit Singh', 3, '9876543212', 'Available', 'hashedpassword3'),
('Neha Gupta', 5, '9876543213', 'Available', 'hashedpassword4'),
('Vikram Patel', 2, '9876543214', 'Available', 'hashedpassword5'),
('Suman Reddy', 4, '9876543215', 'Available', 'hashedpassword6'),
('Ravi Iyer', 1, '9876543216', 'Available', 'hashedpassword7'),
('Anjali Verma', 3, '9876543217', 'Available', 'hashedpassword8'),
('Deepak Mehta', 5, '9876543218', 'Available', 'hashedpassword9'),
('Kavita Nair', 4, '9876543219', 'Available', 'hashedpassword10');


SELECT * FROM DRIVER;

INSERT INTO Customer (Name, Addr, Number, Password) VALUES
('Arjun Singh', 'AGW', '9988776650', 'HP1'),
('Meera Nair', 'BTF', '9988776651', 'HP2'),
('Karan Patel', 'CDX', '9988776652', 'HP3'),
('Sonal Gupta', 'DKY', '9988776653', 'HP4'),
('Rohit Sharma', 'ELP', '9988776654', 'HP5'),
('Anita Desai', 'FZS', '9988776655', 'HP6'),
('Vikas Reddy', 'GHT', '9988776656', 'HP7'),
('Pooja Chauhan', 'HJK', '9988776657', 'HP8'),
('Suresh Menon', 'IQR', '9988776658', 'HP9'),
('Shweta Tripathi', 'JMP', '9988776659', 'HP10'),
('Amit Deshmukh', 'KLV', '9988776660', 'HP11'),
('Ritika Bhatia', 'MNQ', '9988776661', 'HP12'),
('Nikhil Verma', 'OPR', '9988776662', 'HP13'),
('Divya Pandey', 'QRT', '9988776663', 'HP14'),
('Manish Tiwari', 'UVW', '9988776664', 'HP15');


SELECT * FROM CUSTOMER;



-- Fare Calculation Function
DELIMITER //

CREATE FUNCTION CalculateFare(distance FLOAT)
RETURNS DECIMAL(10, 2)
DETERMINISTIC
BEGIN
    DECLARE base_fare DECIMAL(10, 2) DEFAULT 100.00;
    DECLARE additional_cost DECIMAL(10, 2);

    IF distance > 0 THEN
        SET additional_cost = (distance - 1) * 10.00; -- 10 Rs for every km after the first
        RETURN base_fare + additional_cost;
    ELSE
        RETURN 0; -- Return 0 if distance is less than or equal to 0
    END IF;
END;

//

DELIMITER ;




-- TRIGGER 1-DRIVER STATUS UPDATE
DELIMITER //

CREATE TRIGGER after_booking_insert
AFTER INSERT ON Booking
FOR EACH ROW
BEGIN
    UPDATE Driver
    SET Status = 'Not Available'
    WHERE D_id = NEW.D_id;
END;

//

DELIMITER ;


-- TRIGGER 2-BOOKING STATUS UPDATE

DELIMITER //

CREATE TRIGGER after_payment_insert
AFTER INSERT ON Payment
FOR EACH ROW
BEGIN
    UPDATE Booking
    SET Status = 'Completed'
    WHERE B_id = NEW.B_id;
END;

//

DELIMITER ;


