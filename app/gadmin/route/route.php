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
use think\facade\Route;

//Tpflow 5.0工作流引擎路由配置
Route::group('wf', function () {
    Route::rule('designapi', '\tpflow\Api@designapi');//工作流设计统一接口API
    Route::rule('wfapi', '\tpflow\Api@wfapi');//工作流前端管理统一接口
    Route::rule('wfdo', '\tpflow\Api@wfdo');//工作流审批统一接口
});
//超级表单接口
Route::group('sfdp', function () {
    Route::rule('sfdpApi', '\sfdp\Api@sfdpApi');//业务流程设计API接口
    Route::rule('fApi', '\sfdp\Api@fApi');//业务脚本请求接口
});
/*方块报表接口*/
Route::group('fk', function () {
    Route::rule('api', '\Fkreport\Api@Api');//方块报表入库API接口
    Route::rule('fun', '\Fkreport\Api@fun');//方块报表统一函数接口
    Route::rule('data', '\Fkreport\Api@data');//方块报表 资源统一接口
});
//元数据Api接口
Route::rule('sapi','Source/api');
Route::rule('smsg','Api/getMsg');
Route::rule('tApi','Api/tApi');
//Route::rule('upfiles','common/uploads');
