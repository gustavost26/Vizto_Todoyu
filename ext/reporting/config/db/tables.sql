--
-- Table structure for table `ext_reporting_report`
--

CREATE TABLE `ext_reporting_report` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`date_create` int(10) unsigned NOT NULL,
	`date_update` int(10) unsigned NOT NULL,
	`id_person_create` int(10) unsigned NOT NULL,
	`deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`reporttype` varchar(100) NOT NULL,
	`title` varchar(255) NOT NULL,
	`filtervalues` text NOT NULL,
	`sorting` tinyint(255) unsigned NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;