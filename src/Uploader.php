<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: ceive.filesystem
 */

namespace Ceive\Filesystem;

use Ceive\Filesystem\File\UploadedFile;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class Uploader
 * @package Ceive\Filesystem
 */
class Uploader{
	
	/**
	 * @param array $_files
	 * @param bool $top
	 * @return array
	 */
	public static function multiple(array $_files, $top = true){
		$files = [];
		foreach($_files as $name=>$file){
			if($top) $sub_name = $file['name'];
			else    $sub_name = $name;
			
			if(is_array($sub_name)){
				foreach(array_keys($sub_name) as $key){
					if(!isset($files[$name])){
						$files[$name] = [];
					}
					$files[$name][$key] = self::uploadedFile(
						$file['name'][$key],
						$file['type'][$key],
						$file['tmp_name'][$key],
						$file['error'][$key],
						$file['size'][$key]
					);
					$files[$name] = self::multiple($files[$name], false);
				}
			}else{
				$files[$name] = $file;
			}
		}
		return $files;
	}
	
	/**
	 * @param $name
	 * @param $type
	 * @param $tmp_name
	 * @param $size
	 * @param $error
	 * @return UploadedFile
	 */
	public static function uploadedFile($name, $type, $tmp_name, $size, $error){
		return new UploadedFile($name, $type, $tmp_name, $size, $error);
	}
	
}


