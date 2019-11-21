<?php
/**
 *
 * Schema definition for 'followus'
 *
 * Last update: 2016-04-28
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['followus'] = [
    'followus_id' => [
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
    ],
    'title' => [
        'type' => 'varchar(150)',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'color' => [
        'type' => 'varchar(20)',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'shape_color' => [
        'type' => 'varchar(20)',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
		'default' => 'transparent',
    ],
    'text_color' => [
        'type' => 'varchar(20)',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
		'default' => '#000000',
    ],		
    'icon_size' => [
        'type' => 'varchar(50)',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
		'default' => '4x',
    ],
    'shape' => [
        'type' => 'varchar(50)',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
		'default' => 'none',
    ],		
    'description' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'library_id' => array(
        'type' => 'int(11) unsigned',
        'index' => array(
            'key_name' => 'library_id',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => false,
        ),
    ),
    'designt_type' => [
        'type' => 'tinyint(1)',
        'default' => '0',
    ],
    'use_item_custom' => [
        'type' => 'tinyint(1)',
        'default' => '1',
    ],	
    'columns' => [
        'type' => 'tinyint(1)',
        'default' => '3',
    ],		
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ],
];