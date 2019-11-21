<?php
/**
 *
 * Schema definition for 'wallet'
 *
 * Last update: 2016-04-28
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['wallet'] = [
    'wallet_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'value_id' => [
        'type' => 'int(11) unsigned',
        'index' => [
            'key_name' => 'value_id',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => false,
        ],
        'foreign_key' => [
            'table' => 'application_option_value',
            'column' => 'value_id',
            'name' => 'wallet_ibfk_1',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],		
    ],
    'sign_up_bonus' => [
        'type' => 'decimal(12,2)',
		'default' => '0',
    ],
	'c2c_commission' => [
        'type' => 'decimal(12,2)',
		'default' => '0',
    ],
	'upload_commission' => [
        'type' => 'decimal(12,2)',
		'default' => '0',
    ], 	
    'can_transfer' => [
        'type' => 'tinyint(1)',
        'default' => '1',
    ],
    'can_request' => [
        'type' => 'tinyint(1)',
        'default' => '1',
    ],
    'can_upload' => [
        'type' => 'tinyint(1)',
        'default' => '1',
    ],	
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ],
];