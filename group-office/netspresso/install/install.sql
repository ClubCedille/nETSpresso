
DROP VIEW IF EXISTS `netspresso_events`;
CREATE VIEW `netspresso_events` AS 
SELECT r.id AS id, e.id AS `event_id`, c.name AS `calendar_name`, r.start_time, r.end_time, r.status, e.is_organizer 
FROM cal_events e, cal_events r, cal_calendars c 
WHERE r.resource_event_id > 0
AND r.resource_event_id = e.id
AND e.calendar_id = c.id;

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
VALUES ('1', '300', '1200', 'XXX');


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

