CREATE TABLE IF NOT EXISTS `AppUsers` (
    `id` mediumint(20) NOT NULL AUTO_INCREMENT,
  `fbid` varchar(255) NULL DEFAULT '',
  `name` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;