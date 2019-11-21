<?php
/**
 *
 * Schema definition for 'followus_line'
 *
 * Last update: 2016-04-28
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['followus_line'] = [
    'followus_line_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'followus_id' => [
        'type' => 'int(11) unsigned',
        'foreign_key' => [
            'table' => 'followus',
            'column' => 'followus_id',
            'name' => 'followus_ibfk_1',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],
        'index' => [
            'key_name' => 'KEY_FOLLOWUS_ID',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => false,
        ],
    ],	
    'value_id' => [
        'type' => 'int(11) unsigned',
        'index' => [
            'key_name' => 'value_id',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => false,
        ],
    ],

    'icon' => [
        'type' => 'varchar(50)',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],	
	
    'url' => [
        'type' => 'varchar(250)',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'title' => [
        'type' => 'varchar(250)',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],	
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ],
];