<?php
/**
 *
 * Schema definition for 'wallet_payout_methods'
 *
 * Last update: 2016-04-28
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['wallet_payout_methods'] = [
    'wallet_payout_methods_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'wallet_id' => [
        'type' => 'int(11) unsigned',
        'index' => [
            'key_name' => 'wallet_id',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => false,
        ],
        'foreign_key' => [
            'table' => 'wallet',
            'column' => 'wallet_id',
            'name' => 'wallet_pm_1',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],			
    ],
    'title' => [
        'type' => 'varchar(50)',
    ],		
    'minimum' => [
        'type' => 'decimal(12,0)',
		'default' => '0'
    ],
    'active' => [
        'type' => 'tinyint(1)',
        'default' => '1',
    ],
    'description' => array(
        'type' => 'text',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ),	
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ],
];