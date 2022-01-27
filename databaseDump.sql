-- Adminer 4.7.2 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `Users`;
CREATE TABLE `Users` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `userName` varchar(50) NOT NULL,
  `passwordHash` varchar(255) NOT NULL,
  `accessPrivileges` varchar(10) NOT NULL DEFAULT 'user',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `price` varchar(10) NOT NULL,
  `imageFile` varchar(100) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `products` (`Id`, `name`, `price`, `imageFile`) VALUES
(1,	'Ceramic Brake Pads (4-Pad Set)',	'$39.99',	'images/breakPads.jpg'),
(2,	'Break Rotor',	'$57.99',	'images/breakRotor.jpg'),
(3,	'Break Caliper w/Bracket',	'$70.99',	'images/breakCaliper.jpg'),
(4,	'Break Hose',	'$41.99',	'images/breakHose.jpg'),
(5,	'Oil Filter',	'$16.99',	'images/oilFilter.jpg'),
(6,	'Air Filter',	'$56.99',	'images/airFilter.jpg'),
(7,	'Cold Air Intake Kit',	'$229.99',	'images/coldAirIntake.jpg'),
(8,	'Fuel Filter',	'$21.99',	'images/fuelFilter.jpg'),
(9,	'Fuel Pump',	'$49.99',	'images/fuelPump.jpg'),
(10,	'Wiper Blade 20 inch',	'$13.29',	'images/wipers20in.jpg'),
(11,	'Wiper Blade 18 inch',	'$8.29',	'images/wipers18in.jpg'),
(12,	'Gold Battery, 51R, 500CCA',	'$179.99',	'images/goldBattery.jpg'),
(13,	'Silver Battery, 51R, 425CCA',	'$159.99',	'images/silverBattery.jpg'),
(14,	'Starter',	'$148.99',	'images/starter.jpg'),
(15,	'High Beam & Low Beam Headlight (2 pack)',	'$24.99',	'images/headlights.jpg'),
(16,	'Serpentine Belt',	'$11.71',	'images/serpentineBelt.jpg'),
(17,	'Rack and Pinion Assembly',	'$268.99',	'images/rackAndPinion.jpg'),
(18,	'Engine Mount - Right',	'$18.89',	'images/rightEngineMount.jpg'),
(19,	'Engine Mount - Left',	'$60.99',	'images/leftEngineMount.jpg'),
(20,	'Engine Mount - Rear',	'$57.99',	'images/rearEngineMount.jpg'),
(21,	'Clutch Set',	'$114.99',	'images/clutch.jpg');

DROP TABLE IF EXISTS `userCart`;
CREATE TABLE `userCart` (
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `userCart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`Id`),
  CONSTRAINT `userCart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
