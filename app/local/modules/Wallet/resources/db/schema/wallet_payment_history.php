<?php
/**
 *
 * Schema definition for 'wallet_payment_history'
 *
 * Last update: 2016-04-28
 *
 */
 $schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['wallet_payment_history'] = [
    'wallet_payment_history_id' => [
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
            'name' => 'wallet_payment_history_1',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],			
    ],
    'wallet_customer_id' => array(
        'type' => 'int(11) unsigned',
        'index' => array(
            'key_name' => 'customer_id',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => false,
        ),
        'foreign_key' => [
            'table' => 'wallet_customer',
            'column' => 'wallet_customer_id',
            'name' => 'wallet_payment_history_2',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],			
    ),
    'code' => [
        'type' => 'varchar(50)',
        'is_null' => false,
    ],		
    'summ' => [
        'type' => 'decimal(12,2)',
    ],
    'payment_url' => array(
        'type' => 'text',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ),	
    'complete' => [
        'type' => 'tinyint(1)',
        'default' => '1',
    ],
    'payment_id' => [
        'type' => 'varchar(250)',
        'is_null' => true,
    ],
    'payment_message' => array(
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