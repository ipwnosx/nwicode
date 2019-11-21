<?php
/**
 *
 * Schema definition for 'wallet_payment_systems'
 *
 * Last update: 2016-04-28
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['wallet_payment_systems'] = [
    'wallet_payment_systems_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'title' => [
        'type' => 'varchar(50)',
    ],		
    'code' => [
        'type' => 'varchar(20)',
    ],
    'folder' => [
        'type' => 'varchar(255)',
    ],
    'type' => [
        'type' => 'varchar(50)',
    ],
	'state_name' => [
        'type' => 'varchar(50)',
    ],
    'model' => [
        'type' => 'varchar(250)',
    ],	   
	'url' => [
        'type' => 'varchar(250)',
    ],	
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ],
];	