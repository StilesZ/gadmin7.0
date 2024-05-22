<?php
/**
 *+------------------
 * Gadmin 3.0 企业级开发平台
 *+------------------
 * Copyright (c) 2021~2025 https://gadmin8.com All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
namespace app\common\server;

use think\facade\Db;
use think\facade\Filesystem;
use think\file\UploadedFile;

class Upload
{
	public function up($log = 1){
		try {
			// 接收文件信息
			$file = request()->file('file');
			if (!$file || empty($file)) {
				return false;
			}
		} catch (\Throwable $th) {
			return false;
		}
		// 文件信息过滤器
		if (!$this->filefilter($file)) {
			return false;
		}
		$info = Filesystem::disk('public')->putFile('uploads', $file);
        $post =[
            'size' => $file->getSize(),
            'type' => $file->getOriginalExtension(),
            'name' => $file->getOriginalName()
        ];
		if($log == 1){
			$post['info'] =$info;
			return $this->addLog($post);
		}else{
            $up = [];
            $up['src'] = str_replace("\\",'/',$info);
            $up['title'] = $file->getOriginalName();
            $up['ext'] = $file->getOriginalExtension();
			return $up;
		}
	}
	/**
	 * 添加到数据库
	 * @param $post
	 */
	private function addLog($post){
		$insert = [
			'name' => $post['info'],
			'original' => $post['name'],
			'ip' => request()->ip(),
			'type' => $post['type'],
			'size' => $post['size'],
			'mtime' => time(),
			'uptime' => date('Y-m-d h:i:s')
		];
		return Db::name('soft_file')->insertGetId($insert);
	}
	/**
	 * 验证文件是否包含木马
	 * @param $file object
	 * @return bool
	 */
	private function filefilter($file)
	{
		// 验证一句话木马 /*如果是加密的无法判断*/
		$tempFile = $file->getPathname();
		$content = @file_get_contents($tempFile);
		if (false == $content
			|| preg_match('#<\?php#i', $content)
			|| $file->getMime() == 'text/x-php' )  {
			return false;
		}
		// 未找到类型或验证文件失败
		return true;
	}
	
	
}