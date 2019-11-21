<?php
/**
 *
 * Schema definition for 'wallet_transactions'
 *
 * Last update: 2016-04-28
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['wallet_transactions'] = [
    'wallet_transaction_id' => [
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
            'name' => 'wallet_transaction_1',
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
            'name' => 'wallet_transaction_2',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],			
    ),
    'type' => array(
        'type' => 'enum(\'in\',\'out\')',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'default' => 'in',
    ),
    'summ' => [
        'type' => 'decimal(12,2)',
    ],
    'operation_summ' => [
        'type' => 'decimal(12,2)',
    ],	
    'summ_after' => [
        'type' => 'decimal(12,2)',
    ],
    'comission_summ' => [
        'type' => 'decimal(12,2)',
		'default' => '0',
    ],		
    'secret' => [
        'type' => 'varchar(5)',
    ],
    'complete' => [
        'type' => 'tinyint(1)',
        'default' => '1',
    ],
    'description' => array(
        'type' => 'text',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ),
    'from_customer_id' => array(
        'type' => 'int(11) unsigned',
		'default' => '0',
	),
    'to_customer_id' => array(
        'type' => 'int(11) unsigned',
		'default' => '0',
	),
    'os_transaction_id' => [
        'type' => 'int(11) unsigned',
		'default' => '0',
    ],
    'transaction_note' => array(
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