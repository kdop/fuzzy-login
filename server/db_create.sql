DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
	`user_id` INT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(32) NOT NULL,
	PRIMARY KEY(`user_id`),
	UNIQUE KEY(`name`)
) ENGINE=InnoDB;
/*INSERT INTO user (`name`) VALUES ('lisa'), ('panos'), ('kostas');*/

DROP TABLE IF EXISTS `password`;
CREATE TABLE `password` (
	`password_id` INT NOT NULL AUTO_INCREMENT,
	`user_id` INT NOT NULL,
	`password` VARCHAR(32) NOT NULL,
	PRIMARY KEY(`password_id`),
	FOREIGN KEY (`user_id`) REFERENCES `user`(`user_id`)
       ON DELETE CASCADE
       ON UPDATE CASCADE
) ENGINE=InnoDB;

/*DROP TABLE IF EXISTS `user_password`;
CREATE TABLE `user_password` (
	`user_id` INT NOT NULL,
	`password_id` INT NOT NULL,
	PRIMARY KEY(`password_id`, `user_id`)
) ENGINE=InnoDB;*/

/*
	lisa: 	this!5C00L
	panos:	1234qwer
	kostas:	M@m2m|a123
*/

DROP TABLE IF EXISTS `login`;
CREATE TABLE `login` (
	`login_id` INT NOT NULL AUTO_INCREMENT,
	`password_id` INT NOT NULL,
	`tstamp` TIMESTAMP NOT NULL,
	PRIMARY KEY(`login_id`),
	FOREIGN KEY (`password_id`) REFERENCES `password`(`password_id`)
       ON DELETE CASCADE
       ON UPDATE CASCADE
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `keystroke`;
CREATE TABLE `keystroke` (
	`keystroke_id` INT NOT NULL AUTO_INCREMENT,
	`login_id` INT NOT NULL,
	`keystroke_index` INT NOT NULL,
	`key` INT NOT NULL,
	`action` CHAR(1) NOT NULL,
	`tstamp` INT NOT NULL,
	PRIMARY KEY(`keystroke_id`),
	FOREIGN KEY (`login_id`) REFERENCES `login`(`login_id`)
       ON DELETE CASCADE
       ON UPDATE CASCADE
) ENGINE=InnoDB;