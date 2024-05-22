<?php
/**
 *+------------------
 * 文件安全检测器
 *+------------------
 */

class Security
{
    /**
     * 获取全部节点控制器
     */
    public function node(){
        $ROOT_PATH = app()->getRootPath();
        $ctrl = [];
        foreach (self::listFile($ROOT_PATH . 'app/gadmin/controller/') as $file) {
            if(count(explode(".php",$file['filename']))>1){
                $name = strtolower(str_replace(".php","",$file['filename']));
                if(!in_array($name,['index','api','base','common','login','upgrade'])){
                    $ctrl[] = $name;
                }
            }
        }
       return $ctrl;
    }
    /**
     * 获取写入安全MD5
     */
    public function int(){
        $ROOT_PATH = app()->getRootPath();
        $security_dir = $ROOT_PATH . 'extend/SecurityData/';
        if (!file_exists($security_dir)) {
            @mkdir($security_dir);
        }
        $filename = $security_dir . 'Security_' . date('YmdHi') . '.sfile';
        $obj        = fopen($filename, 'w');
        fwrite($obj, "NVER: GADMIN V" . GadminVer . "\n");
        fwrite($obj, "TIME: " . date('Y-m-d H:i:s') . "\n");
        foreach (['app', 'config', 'extend', 'public', 'vendor'] as $dir) {
            foreach ($this->FileGenerate($ROOT_PATH . $dir . '/', $dir . '/') as $file_md5) {
                fwrite($obj, $file_md5[1] . '|' . $file_md5[0] . "\n");
            }
        }
        fclose($obj);
    }
    /**
     * 执行检查
     */
    public function check(){
        $errs = [];
        $ROOT_PATH = app()->getRootPath();
        $dir = $ROOT_PATH . 'extend/SecurityData/';
        /*遍历安全文件，取出最新的安全文件*/
        $files = [];
        foreach (self::listFile($dir) as $file) {
            $files[$file['atime']] = $file['pathname'];
        }
        $key_ay=array_keys($files);
        $last_key = end($key_ay);
        $security_file = $files[$last_key] ?? '';
        if (!file_exists($security_file)) {
            return ['msg'=>['安全文件不存在，请生成安全文件！'],'e'=>0,'d'=>0,'r'=>0];
        }
        $allFile = explode("\n", file_get_contents($security_file));
        if (count($allFile) < 3) {
            return ['msg'=>['安全文件错误，请检查程序完整性！'],'e'=>0,'d'=>0,'r'=>0];
        }
        unset($allFile[0], $allFile[1]);
        $edit = 0; $del = 0; $err = 0;
        foreach ($allFile as $v) {
            $v = trim($v);
            if ($v) {
                $l = explode('|', $v);
                if (count($l) == 2) {
                    $filename = $ROOT_PATH . trim($l[1]);
                    if (self::listFile($filename)) {
                        if (trim($l[0]) != md5_file($filename)) {
                            $errs[] = 'Edit: ' . trim($l[1]);
                            $edit = $edit+1;
                        }
                    }else{
                        $errs[] = 'Del : ' . trim($l[1]);
                        $del = $del+1;
                    }
                } else {
                    $errs[] = 'Err : ' . $v;
                    $err = $err+1;
                }
            }
        }
        return ['msg'=>$errs,'e'=>$edit,'d'=>$del,'r'=>$err];
    }
    /**
     * 文件循环生成
     */
    private function FileGenerate($dir = '', $prefix = ''){
        $file_ext = ['php', 'js','css', 'html'];
        $file_arr = [];
        foreach (self::listFile($dir) as $file) {
            if ($file['isDir']) {
                $file_arr = array_merge($file_arr, $this->FileGenerate($file['pathname'] . '/', $prefix . $file['filename'] . '/'));
            } else if ($file['isFile']) {
                if (in_array($file['ext'],$file_ext)) {
                    $file_saved  = $prefix . str_replace('\\', '/', $file['filename']);
                    $file_arr[] = [
                        $file_saved,
                        md5_file($file['pathname']),
                    ];
                }
            }
        }
        return $file_arr;
    }
    /**
     * 文件列表信息
     */
    static function listFile($pathname, $pattern = '*')
    {
        if (strpos($pattern, '|') !== false) {
            $patterns = explode('|', $pattern);
        } else {
            $patterns[0] = $pattern;
        }
        $i   = 0;
        $dir = [];
        foreach ($patterns as $pattern) {
            $list = glob($pathname . $pattern);
            if ($list !== false) {
                foreach ($list as $file) {
                    $dir[$i]['filename'] = preg_replace('/^.+[\\\\\\/]/', '', $file);
                    $dir[$i]['pathname'] = realpath($file);
                    $dir[$i]['owner']    = fileowner($file);
                    $dir[$i]['path']     = dirname($file);
                    $dir[$i]['atime']    = fileatime($file);
                    $dir[$i]['ctime']    = filectime($file);
                    $dir[$i]['size']     = filesize($file);
                    $dir[$i]['type']     = filetype($file);
                    $dir[$i]['ext']      = is_file($file) ? strtolower(substr(strrchr(basename($file), '.'), 1)) : '';
                    $dir[$i]['isDir']    = is_dir($file);
                    $dir[$i]['isFile']   = is_file($file);
                    $i++;
                }
            }
        }
        usort($dir, function ($a, $b) {
            if (($a["isDir"] && $b["isDir"]) || (!$a["isDir"] && !$b["isDir"])) {
                return $a["filename"] > $b["filename"] ? 1 : -1;
            } else {
                if ($a["isDir"]) {
                    return -1;
                } elseif ($b["isDir"]) {
                    return 1;
                }
                if ($a["filename"] == $b["filename"]) {
                    return 0;
                }
                return $a["filename"] > $b["filename"] ? -1 : 1;
            }
        });
        return $dir;
    }
}