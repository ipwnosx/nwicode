<?php
/**
 *
 * Schema definition for 'wallet_payment_systems_yandex'
 *
 * Last update: 2016-04-28
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['wallet_payment_systems_yandex'] = [
    'wallet_payment_systems_yandex_id' => [
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
            'name' => 'wallet_yandex_11',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],		
    ],	
    'title' => [
        'type' => 'varchar(150)',
    ],		
    'shop_id' => [
        'type' => 'varchar(150)',
    ],
    'secret_key' => [
        'type' => 'varchar(150)',
    ],
    'model' => [
        'type' => 'varchar(150)',
    ],	
    'enabled' => [
        'type' => 'tinyint(1)',
		'default'=>'0',
    ],

    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ],
];	