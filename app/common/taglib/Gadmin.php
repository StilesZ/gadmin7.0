<?php
/**
 *+------------------
 * 标签
 *+------------------
 */
namespace app\common\taglib;

use think\template\TagLib;

class Gadmin extends TagLib {

    protected $tags = array(
        'list'      => ['attr' => 'table,where,d_id,limit,order','close' => 1]
    );

    /**
     * 列表标签
     * @param $tag
     * @param $content
     * @return string
     */
    public function tagList($tag, $content)
    {
        $name     = $tag['name'];
        $order    = $tag['order']  ?? 'id DESC';
        $d_id     = $tag['d_id']    ?? ''; // 查询条件
        $limit    = $tag['limit']  ?? '9999';
        $where    = $tag['where'] ?? ''; // 查询条件
        $parse  = '<?php ';
        $parse .= '
            $__LIST__ =  think\facade\Db::name("' . $name . '")
                ->where("' . $where . '")
                ->where("d_id",'.$d_id.')
                ->order(\'' . $order . '\')
                ->limit(\'' . $limit . '\')
                ->select();
            ';
        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="k"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }
}