<?php
/**
 *
 * Schema definition for 'wallet_customer'
 *
 * Last update: 2016-04-28
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['wallet_customer'] = [
    'wallet_customer_id' => [
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
            'name' => 'wallet_customer_1',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],			
    ],
 
    'customer_id' => array(
        'type' => 'int(11) unsigned',
        'index' => array(
            'key_name' => 'customer_id',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => false,
        ),
        'foreign_key' => [
            'table' => 'customer',
            'column' => 'customer_id',
            'name' => 'wallet_customer_2',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],			
    ),
    'score' => [
        'type' => 'decimal(12,2)',
		'default' => '0',
    ],
    'is_blocked' => [
        'type' => 'tinyint(1)',
        'default' => '0',
    ],
    'email' => [
        'type' => 'varchar(50)',
		'is_null' => false,
    ],	
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ],
];