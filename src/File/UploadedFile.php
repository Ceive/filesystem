<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: ceive.filesystem
 */

namespace Ceive\Filesystem\File;

use Ceive\Filesystem\File;
use Ceive\Filesystem\FS;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class UploadedFile
 * @package Ceive\Filesystem\StorageLinear\File
 */
class UploadedFile extends File{
	
	public $name;
	public $type;
	public $size;
	public $error;
	
	/**
	 * UploadedFile constructor.
	 * @param $name
	 * @param $type
	 * @param $tmp_name
	 * @param $size
	 * @param $errors
	 */
	public function __construct($name, $type, $tmp_name, $size, $errors){
		$this->name = $name;
		$this->type = $type;
		$this->path = $tmp_name;
		$this->size = $size;
		$this->error = $errors;
	}
	
	/**
	 * @param $name
	 * @param $type
	 * @param $tmp_name
	 * @param $size
	 * @param $errors
	 */
	public function init($name, $type, $tmp_name, $size, $errors){
		$this->name = $name;
		$this->type = $type;
		$this->path = $tmp_name;
		$this->size = $size;
		$this->error = $errors;
	}
	
	
	/**
	 * После вызова данного метода, от этого объекта следует избавится
	 * @param $path
	 * @param bool $force
	 * @return $this
	 * @throws \Ceive\Filesystem\Exception
	 */
	public function moveTo($path, $force = false){
		if($force){
			if(file_exists($path)){
				FS::deleteRecursive($path, $force);
			}elseif(!is_dir(dirname($path))){
				FS::mkdir($path,0777, true);
			}
			
			
		}
		if(!@move_uploaded_file($this->path, $path)){
			$e  = error_get_last();
			if($e){
				throw \Ceive\Filesystem\Exception::error('Error move_uploaded_file ' . $e['message']);
			}else{
				throw \Ceive\Filesystem\Exception::error('Error move_uploaded_file ');
			}
		}
		return $this;
	}
	
	public function getMimeType(){
		return $this->type;
	}
	
	public function getSize(){
		return $this->size;
	}
	
	public function getBasename(){
		return $this->name;
	}
	
	public function getExtension(){
		return pathinfo($this->name, PATHINFO_EXTENSION);
	}
	
	public function isError(){
		return $this->error !== 0;
	}
	
	
	
}


