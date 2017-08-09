<?php

if (!defined('IN_FINECMS'))
    exit('No permission resources');

return array(
    "DROP TABLE IF EXISTS `{prefix}category_jump`;",
    "CREATE TABLE IF NOT EXISTS `{prefix}category_jump` (
	  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	  `subject` char(255) NOT NULL,
	  `cat_id` int(11) NOT NULL DEFAULT '0',
	  `start` int(5) unsigned NOT NULL DEFAULT '0',
	  `end` int(5) unsigned NOT NULL DEFAULT '0',
	  `description` text NOT NULL,
	  `status` tinyint(1) NOT NULL,
          `addtime` int(10) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8;"
);
