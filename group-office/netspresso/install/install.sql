
DROP VIEW IF EXISTS `netspresso_events`;

CREATE VIEW `netspresso_events AS 
SELECT r.id, e.id AS `event_id`, c.name AS `calendar_name`, r.start_time, r.end_time, r.status, e.is_organizer 
FROM cal_events e, cal_events r, cal_calendars c 
WHERE r.resource_event_id > 0
AND r.resource_event_id = e.id
AND e.calendar_id = c.id;

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
