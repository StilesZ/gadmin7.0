<?php
/**
 * Rbac
 */
return [
    'user_auth_on' => true, // 是否开启认证
    'user_auth_type' => 2, // 默认认证类型 1 - 登录认证 | 2 - 实时认证
    'admin_auth_key' => 'is_admin',
    'not_auth_controller' => ['index', 'login', 'common', 'wf','msg','source'],   // 无需认证的控制器
    'not_auth_action' => ['deskconfig','help','help_view','print_view','sfdpList','sapi','getmsg','linkdata'],          // 无需认证的方法
];