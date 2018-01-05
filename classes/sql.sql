CREATE SCHEMA IF NOT EXISTS `kasicare`;

CREATE TABLE IF NOT EXISTS `kasicare`.`userlist` (
	`id` integer NOT NULL,
	`name` VARCHAR(30),
	`surname` VARCHAR(30),
	`email` VARCHAR(70),
	`phone` VARCHAR(15) NOT NULL,
	`unique_id` VARCHAR(20),
	`gender` INTEGER NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `kasicare`.`institution` (
	`id` integer NOT NULL,
	`name` varchar(40) NOT NULL,
	`longitute` float,
	`latitude` float,
	`address` varchar(100),
	`province` varchar(2),
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `kasicare`.`access_log` (
	`id` integer NOT NULL,
	`client_id` interger NOT NULL,
	`platform` integer NOT NULL,
	`when` datetime NOT NULL,
	`med_prof_id` integer NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `kasicare`.`platforms` (
	`id` integer NOT NULL,
	`name` varchar(50) NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `kasicare`.`activity_log` (
	`id` integer NOT NULL,
	`data` varchar(200) NOT NULL,
	`when` datetime NOT NULL,
	`userlist_id` integer NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `kasicare`.`location_description` (
);

CREATE TABLE IF NOT EXISTS `kasicare`.`user_details` (
	`date_of_birth` date,
	`id_number` VARCHAR(15),
	`occupation` varchar(15),
	`address` varchar(80),
	`work` varchar(15),
	`ulist_id` integer NOT NULL,
	`id` integer NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `kasicare`.`user_med_history` (
	`institute` integer NOT NULL,
	`id` integer NOT NULL,
	`date_of_visit` date NOT NULL,
	`description` varchar(200),
	`title` varchar(25) NOT NULL,
	`med_professional_sign` integer,
	`ulist_id` integer NOT NULL,
	`diagnosis` varchar(300),
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `kasicare`.`user_signatures` (
	`id` integer NOT NULL,
	`ulist_id` integer,
	`key_file` varchar(100),
	`nat_id_number` varchar(15),
	`nationality_key` varchar(4) NOT NULL,
	`user_passcode` varchar(100),
	`salt_version` integer NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `kasicare`.`medical_professional_list` (
	`id` integer NOT NULL,
	`licence_number` varchar(30) NOT NULL,
	`specialisation_key` varchar(3) NOT NULL,
	`reg_id` integer NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `kasicare`.`medical_specialisation` (
	`id` integer NOT NULL,
	`key` varchar(3) NOT NULL,
	`title` varchar(10) NOT NULL,
	`description` varchar(150) NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `kasicare`.`medical_signatures` (
	`id` integer NOT NULL,
	`ulist_id` integer NOT NULL,
	`key_file` integer NOT NULL,
	`expiration` date NOT NULL,
	`validation` date NOT NULL,
	`nationality_key` varchar(4) NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `kasicare`.`relations` (
	`id` integer NOT NULL,
	`user_1` integer NOT NULL,
	`user_2` integer NOT NULL,
	`relation_description` varchar(100),
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `kasicare`.`user_treatment` (
	`id` integer NOT NULL,
	`ulist_id` integer NOT NULL,
	`umhistory_id` integer,
	`med_description` varchar(100),
	`frequency` numeric,
	`begin` datetime,
	`end` datetime,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `kasicare`.`bookings` (
	`id` integer NOT NULL,
	`booking` date NOT NULL,
	`confirmed` char,
	`type_` char,
	`desc` varchar(100),
	`location_id` id NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `kasicare`.`survey` (
	`id` integer NOT NULL,
	`title` VARCHAR(40) NOT NULL,
	`description` VARCHAR(100),
	`ulist_id` INTEGER NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `kasicare`.`survey_question` (
	`id` INTEGER NOT NULL,
	`question` VARCHAR(250),
	`type_` INTEGER,
	`surv_id` INTEGER NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `kasicare`.`survey_answers` (
	`id` INTEGER NOT NULL,
	`surv_id` INTEGER NOT NULL,
	`response` VARCHAR(500),
	`ulist_id` INTEGER NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `kasicare`.`survey_question_options` (
	`id` INTEGER NOT NULL,
	`surv_question_id` INTEGER NOT NULL,
	`option` VARCHAR(100) NOT NULL,
	PRIMARY KEY (`id`)
);
