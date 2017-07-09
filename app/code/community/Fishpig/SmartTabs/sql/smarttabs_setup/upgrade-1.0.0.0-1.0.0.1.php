<?php
/**
 * @category    Fishpig
 * @package    Fishpig_SmartTabs
 * @license      http://fishpig.co.uk/license.txt
 * @author       Ben Tideswell <ben@fishpig.co.uk>
 */

	$this->startSetup();
	
	$this->getConnection()->addColumn($this->getTable('smarttabs_tab'), 'sort_order', " int(3) unsigned NOT NULL default 0 AFTER content");
	$this->getConnection()->addColumn($this->getTable('smarttabs_tab'), 'group_id', " int(5) unsigned NOT NULL default 1 AFTER tab_id");

	$this->endSetup();
	