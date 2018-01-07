<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: ceive.filesystem
 */

namespace Ceive\Filesystem;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class File
 * @package Ceive\Filesystem\StorageLinear
 */
abstract class File implements FileInterface{
	
	protected $path;
	
	public function getPath(){
		return $this->path;
	}
	
	public function getMimeType(){
		return mime_content_type($this->path);
	}
	
	public function getSize(){
		return filesize($this->path);
	}
	
	public function getBasename(){
		return basename($this->path);
	}
	
	public function getExtension(){
		return pathinfo($this->path,PATHINFO_EXTENSION);
	}
	
	
	/**
	 * @param $path
	 * @param bool $force
	 * @return $this
	 */
	public function moveTo($path, $force = false){
		FS::moveRecursive($this->path, $path, $force?FS::S_REPLACE:FS::S_REJECT);
		$this->path = $path;
		return $this;
	}
	
	/**
	 * @param $path
	 * @param bool $force
	 * @return $this
	 */
	public function copyTo($path, $force = false){
		FS::copyRecursive($this->path, $path, $force?FS::S_REPLACE:FS::S_REJECT);
		return $this;
	}
}


