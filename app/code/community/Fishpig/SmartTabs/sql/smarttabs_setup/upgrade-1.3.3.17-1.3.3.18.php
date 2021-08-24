<?php
/**
 * @category  Fishpig
 * @package   Fishpig_SmartTabs
 * @license   http://fishpig.co.uk
 * @author    Ben Tideswell <ben@fishpig.com>
 */
$this->startSetup();

$this->getConnection()->changeColumn(
    $this->getTable('smarttabs_tab'), 
    'description', 
    'description', 
    " varchar(255) NOT NULL default '' AFTER title"
);

$this->endSetup();
