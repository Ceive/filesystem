<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: ceive.filesystem
 */

namespace Ceive\Filesystem\File;


use Ceive\Filesystem\File;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class LocalFile
 * @package Ceive\Filesystem\StorageLinear\File
 */
class LocalFile extends File{
	
	/**
	 * LocalFile constructor.
	 * @param $path
	 */
	public function __construct($path){
		$this->path = $path;
	}
	
	/**
	 * @param array $path
	 */
	public function init($path){
		$this->path = $path;
	}
}


