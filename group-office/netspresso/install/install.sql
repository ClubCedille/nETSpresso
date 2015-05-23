
DROP VIEW IF EXISTS `netspresso_events`;
CREATE VIEW `netspresso_events` AS 
SELECT `cal_events`.id AS id, `cal_events`.id AS `event_id`, `cal_calendars`.name AS `calendar_name`, `cal_events`.`name` AS `event_name`,`cal_events`.start_time, `cal_events`.end_time, `cal_events`.status, `cal_events`.is_organizer 
FROM `groupoffice4`.`cf_cal_events` AS `cf_cal_events`, 
	 `groupoffice4`.`cal_events` AS `cal_events`,
	 `groupoffice4`.`cal_calendars` AS `cal_calendars`,
	 `groupoffice4`.`go_users` AS `go_users` 
WHERE `cf_cal_events`.`model_id` = `cal_events`.`id` AND 
	  `cal_events`.`calendar_id` = `cal_calendars`.`id` AND 
	  `go_users`.`id` = `cal_calendars`.`user_id` AND 
	  `cf_cal_events`.`col_8` = 1;

DROP TABLE IF EXISTS `netspresso_config`;
CREATE TABLE IF NOT EXISTS `netspresso_config` (
  `id` int(11) NOT NULL,
  `ready_before` int(11) NOT NULL,
  `stdby_after` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL,
  PRIMARY KEY `netspresso_config_id` (`id`),
  KEY `id` (`id`)
);

INSERT INTO `netspresso_config` (`id`, `ready_before`, `stdby_after`, `resource_id`) 
VALUES ('1', '300', '1200', '1');


DROP TABLE IF EXISTS `go_links_netspresso_events`;
CREATE TABLE IF NOT EXISTS `go_links_netspresso_events` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `model_id` int(11) NOT NULL,
  `model_type_id` int(11) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY `model_id` (`id`,`model_id`,`model_type_id`),
  KEY `id` (`id`,`folder_id`),
  KEY `ctime` (`ctime`)
);

