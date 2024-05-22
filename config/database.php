<?php

return [
    // 默认使用的数据库连接配置
    'default'         => 'mysql',
    'time_query_rule' => [],
    'auto_timestamp'  => true,
    'datetime_format' => 'Y-m-d H:i:s',
    // 数据库连接配置信息
    'connections'     => [
        'mysql' => [
            'type'            => 'mysql',
            'hostname'        => '127.0.0.1',
            'database'        => 'gadmin7',
            'username'        => 'root',
            'password'        => 'root',
            'hostport'        => '3306',
            'params'          => [],
            'charset'         => 'utf8',
            'prefix'          => 'g_',
            'deploy'          => 0,
            'rw_separate'     => false,
            'master_num'      => 1,
            'slave_no'        => '',
            'fields_strict'   => true,
            'break_reconnect' => false,
            'trigger_sql'     => true,
            'fields_cache'    => false,
        ],
        'PG' => [
            'type'        => 'pgsql',
            'hostname'    => '127.0.0.1',
            'database'    => 'gadmin',
            'username'    => 'postgres',
            'password'    => 'root',
            'charset'     => 'utf8'
        ],
	'db_log' => [
            'type'            => 'mysql',
            'hostname'        => '127.0.0.1',
            'database'        => 'log',
            'username'        => 'root',
            'password'        => 'root',
            'hostport'        => '3306',
            'params'          => [],
            'charset'         => 'utf8',
            'prefix'          => 'g_',
            'deploy'          => 0,
            'rw_separate'     => false,
            'master_num'      => 1,
            'slave_no'        => '',
            'fields_strict'   => true,
            'break_reconnect' => false,
            'trigger_sql'     => true,
            'fields_cache'    => false,
        ]
    ],
];
