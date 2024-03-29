<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
//    'DB_TYPE'   =>  'mysql',     // 数据库类型
//    'DB_HOST'   =>  'localhost', // 服务器地址
//    'DB_NAME'   =>  'sjjdpt',          // 数据库名
//    'DB_USER'   =>  'root',      // 用户名
//    'DB_PWD'    =>  'root',          // 密码2135987
//    'DB_PORT'   =>  '3306',        // 端口
//    'DB_PREFIX' =>  'sjj_',    // 数据库表前缀
//    'DB_CHARSET'=>  'utf8',      // 数据库编码


    // 数据库类型
    'type' => 'mysql',
    // 服务器地址
    'hostname' => '127.0.0.1',
    // 数据库名
    'database' => 'sjjdpt',
    // 用户名
    'username' => 'root',
    // 密码
    'password' => 'root',
    // 端口
    'hostport' => '3306',
    // 连接dsn
    'dsn' => '',
    // 数据库连接参数
    'params' => [],
    // 数据库编码默认采用utf8
    'charset' => 'utf8',
    // 数据库表前缀
    'prefix' => 'sjj_',
    // 数据库调试模式
    'debug' => true,
    // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'deploy' => 0,
    // 数据库读写是否分离 主从式有效
    'rw_separate' => false,
    // 读写分离后 主服务器数量
    'master_num' => 1,
    // 指定从服务器序号
    'slave_no' => '',
    // 自动读取主库数据
    'read_master' => false,
    // 是否严格检查字段是否存在
    'fields_strict' => true,
    // 数据集返回类型
    'resultset_type' => 'array',
    // 自动写入时间戳字段
    'auto_timestamp' => false,
    // 时间字段取出后的默认时间格式
    'datetime_format' => 'Y-m-d H:i:s',
    // 是否需要进行SQL性能分析
    'sql_explain' => false,
    // Builder类
    'builder' => '',
    // Query类
    'query' => '\\think\\db\\Query',
    // 是否需要断线重连
    'break_reconnect' => false,
    // 断线标识字符串
    'break_match_str' => [],

//    连接其他数据库，配置1(矿区审计局)
    'db_config1' => [
        // 数据库类型
        'type' => 'mysql',
        // 服务器地址
        'hostname' => '192.168.199.235',
        // 数据库名
        'database' => 'sjjdpt',
        // 数据库用户名
        'username' => 'root',
        // 数据库密码
        'password' => ' ',
        // 数据库编码默认采用utf8
        'charset' => 'utf8',
        // 数据库表前缀
        'prefix' => 'sjj_',
    ],
//    连接其他数据库，配置2（暂时无需求！！）
    'DB_CONFIG2' => 'mysql://root: @192.168.199.235:3306/sjjdpt#utf8',
];
