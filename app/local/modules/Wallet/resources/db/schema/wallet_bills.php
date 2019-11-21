<?php
/**
 *
 * Schema definition for 'wallet_bills'
 *
 * Last update: 2016-04-28
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['wallet_bills'] = [
    'wallet_bill_id' => [
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
            'name' => 'wallet_bill_1',
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
            'name' => 'wallet_bill_2',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],			
    ),	
    'transaction_id' => [
        'type' => 'int(11) unsigned',
        'index' => [
            'key_name' => 'wallet_transaction1',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => false,
        ],
    ],
    'title' => array(
        'type' => 'varchar(250)',
        'is_null' => true,
    ),
    'bill_source' => array(
        'type' => 'varchar(250)',
        'is_null' => true,
    ),
    'description' => array(
        'type' => 'text',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ),
    'cancel_text' => array(
        'type' => 'text',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ),
    'complete_text' => array(
        'type' => 'text',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ),	
    'command_complete' => array(
        'type' => 'text',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ),
    'command_cancel' => array(
        'type' => 'text',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ),		
    'summ' => [
        'type' => 'decimal(12,2)',
    ],
    'status' => array(
        'type' => 'enum(\'pending\',\'complete\',\'decline\',\'cancel\')',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'default' => 'pending',
    ),
    'operation_at' => [
        'type' => 'datetime',
    ],	
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ],
];	