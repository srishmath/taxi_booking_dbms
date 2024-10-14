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
    Rating FLOAT CHECK (Rating >= 0 AND Rating <= 5),  -- Rating between 0 and 5
    D_Number VARCHAR(15) UNIQUE NOT NULL,
    Status VARCHAR(50) DEFAULT 'Available',  -- Driver availability status
    Password VARCHAR(255) NOT NULL  -- Hashed password for authentication
);

-- Car Table
CREATE TABLE Car (
    Car_id INT AUTO_INCREMENT PRIMARY KEY,
    Reg VARCHAR(50) UNIQUE NOT NULL,  -- Registration number
    CarKe_id INT,                     -- Foreign key reference if needed
    Car_D VARCHAR(50),                -- Car model or type
    Type VARCHAR(50) NOT NULL         -- Car type (e.g., Sedan, SUV)
);

-- Booking Table
CREATE TABLE Booking (
    B_id INT AUTO_INCREMENT PRIMARY KEY,
    C_id INT,                         -- Foreign key to Customer
    D_id INT,                         -- Foreign key to Driver (if assigned)
    Pick_loc VARCHAR(255) NOT NULL,
    Drop_loc VARCHAR(255) NOT NULL,
    Status VARCHAR(50) DEFAULT 'Pending',  -- Booking status
    Time DATETIME NOT NULL,
    Distance FLOAT,                   -- Distance of the booking
    FOREIGN KEY (C_id) REFERENCES Customer(C_id),
    FOREIGN KEY (D_id) REFERENCES Driver(D_id)
);

-- Payment Table
CREATE TABLE Payment (
    P_id INT AUTO_INCREMENT PRIMARY KEY,
    B_id INT,                         -- Foreign key to Booking
    Mode VARCHAR(50) NOT NULL,        -- Payment method (e.g., Cash, Card)
    Distance FLOAT,                   -- Distance related to payment
    CP_id INT,                       -- Reference to another payment entity, if needed
    Cost DECIMAL(10, 2) NOT NULL,     -- Cost of the ride
    FOREIGN KEY (B_id) REFERENCES Booking(B_id)
);

-- Feedback Table
CREATE TABLE Feedback (
    F_id INT AUTO_INCREMENT PRIMARY KEY,
    C_id INT,                         -- Foreign key to Customer
    Rating INT CHECK (Rating >= 1 AND Rating <= 5),  -- Rating between 1 and 5
    Comments TEXT,                    -- Optional comments
    FOREIGN KEY (C_id) REFERENCES Customer(C_id)
);


