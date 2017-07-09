<?php
/**
 * @category    Fishpig
 * @package    Fishpig_SmartTabs
 * @license      http://fishpig.co.uk/license.txt
 * @author       Ben Tideswell <ben@fishpig.co.uk>
 */

	$this->startSetup();
	
	$tabTable = $this->getTable('smarttabs_tab');
	$tabStoreTable = $this->getTable('smarttabs_tab_store');
	
	$this->run("

		DROP TABLE IF EXISTS {$tabTable};
		DROP TABLE IF EXISTS {$tabStoreTable};
		
		CREATE TABLE IF NOT EXISTS {$tabTable} (
			`tab_id` int(11) unsigned NOT NULL auto_increment,
			`alias` varchar(32) NOT NULL default '',
			`title` varchar(128) NOT NULL default '',
			`description` TEXT NOT NULL default '',
			`filters` TEXT NOT NULL default '',
			`content` TEXT NOT NULL default '',
			`status` int(1) unsigned NOT NULL default 1,
			`created_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
			`updated_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY (`tab_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='SmartTabs: Tab';
		
		CREATE TABLE IF NOT EXISTS {$tabStoreTable} (
			`tab_id` int(11) unsigned NOT NULL auto_increment,
			`store_id` smallint(5) unsigned NOT NULL default 0,
			PRIMARY KEY(`tab_id`, `store_id`),
			KEY `FK_SMARTTABS_TAB_ID_SMARTTABS_TAB` (`tab_id`),
			CONSTRAINT `FK_SMARTTABS_TAB_ID_SMARTTABS_TAB` FOREIGN KEY (`tab_id`) REFERENCES `{$tabTable}` (`tab_id`) ON DELETE CASCADE ON UPDATE CASCADE,
			KEY `FK_SMARTTABS_STORE_ID_CORE_STORE` (`store_id`),
			CONSTRAINT `FK_SMARTTABS_STORE_ID_CORE_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
		)  ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='SmartTabs: Tab Store Links';

	");

	$this->endSetup();
	