<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>  ��
// +----------------------------------------------------------------------

return [
    // ��庱�
    'type'            => 'mysql',
    // ��
    'hostname'        => '127.0.0.1',
    // ������阿达
    'database'        => 'www_jpcryptoex_v',
    // ��
    'username'        => 'www_jpcryptoex_v',
    // ��
    'password'        => 'w71Ts5Rni8imZbm9',
    // ��
    'hostport'        => '3306',
    // ��dsn
    'dsn'             => '',
    // ��庥�����
    'params'          => [],
    // ����认�tf8
    'charset'         => 'utf8',
    // ��庡�
    'prefix'          => 'wp_',
    // ����诨���
    'debug'           => true,
    // ����署���:0 ����(),1 ��(主�)��
    'deploy'          => 0,
    // ������ 主���
    'rw_separate'     => false,
    // 读� 主�����
    'master_num'      => 1,
    // 仡���
    'slave_no'        => '',
    // 严�棥�段�
    'fields_strict'   => true,
    // �汻
    'resultset_type'  => 'array',
    // ������
    'auto_timestamp'  => false,
    // ����黶���
    'datetime_format' => 'Y-m-d H:i:s',
    // ����QL��
    'sql_explain'     => false,
    // Builder��
    'builder'         => '',
    // Query��
    'query'           => '\\think\\db\\Query',
];
