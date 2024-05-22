<?php
// Gadmin事件定义器
return [
    'bind'      => [
    ],
    'listen'    => [
		'CurdEvent'    =>    ['app\event\Curd'],
        'UserEvent'    =>    ['app\event\User']
    ],
    'subscribe' => [
    ],
];
