<?php
/**
 *+------------------
 * Gadmin 5.0 企业级开发平台
 *+------------------
 */
return [
    // Session初始化
    \think\middleware\SessionInit::class,
    // 加载自定义日志数据
    \app\gadmin\middleware\Log::class,
];
