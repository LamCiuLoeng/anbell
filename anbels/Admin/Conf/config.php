<?php
return array(
    'AUTH_CONFIG'=>array(
        'AUTH_ON' => true, //认证开关
        'AUTH_TYPE' => 1, // 认证方式，1为时时认证；2为登录认证。
        'AUTH_GROUP' => 'anbels_auth_group', //用户组数据表名
        'AUTH_GROUP_ACCESS' => 'anbels_auth_group_access', //用户组明细表
        'AUTH_RULE' => 'anbels_auth_rule', //权限规则表
        'AUTH_USER' => 'anbels_auth_user'//用户信息表
    )
);