<?php
/**
 *+------------------
 * Gadmin 5.0 企业级开发平台
 *+------------------
 */
 
function returnMsg($msg = '',$code = 0,$data = [])
{	
		http_response_code(200);    //设置返回头部
        $return['code'] = (int)$code;
        $return['message'] = $msg;
        $return['msg'] = $msg;
        $return['data'] = is_array($data) ? $data : ['info'=>$data];
        exit(json_encode($return,JSON_UNESCAPED_UNICODE));
}

/**
* 生成签名
*/
function makeSign($data = [])
{
    unset($data['version'],$data['sign']);
    ksort($data);
    $params['key'] = '4Fz0r0r1pf3yGm1MdD5UGxiJ0pHDP';
    return strtolower(md5(urldecode(http_build_query($params))));
}