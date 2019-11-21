<?php
/**
 *
 * Schema definition for 'wallet_payout_request'
 *
 * Last update: 2016-04-28
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['wallet_payout_request'] = [
    'wallet_payout_request_id' => [
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
            'name' => 'wallet_payout_request_1',
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
            'name' => 'wallet_payout_request_2',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],			
    ),	
    'transaction_id' => [
        'type' => 'int(11) unsigned',
        'index' => [
            'key_name' => 'transaction_id1',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => false,
        ],
        'foreign_key' => [
            'table' => 'wallet_transactions',
            'column' => 'wallet_transaction_id',
            'name' => 'wallet_payout_request_3',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],	
    ],
    'customer_info' => array(
        'type' => 'text',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ),
    'admin_info' => array(
        'type' => 'text',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ),	
    'summ' => [
        'type' => 'decimal(12,2)',
    ],
    'payout_method' => [
        'type' => 'int(11) unsigned',
        'is_null' => false,
    ],
    'payout_method_title' => [
        'type' => 'varchar(50)',
        'is_null' => false,
    ],		
    'status' => array(
        'type' => 'enum(\'pending\',\'complete\',\'decline\',\'cancel\')',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'default' => 'pending',
    ),
    'approved_at' => [
        'type' => 'datetime',
    ],	
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ],
];	