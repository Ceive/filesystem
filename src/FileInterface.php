<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: ceive.filesystem
 */

namespace Ceive\Filesystem;

/**
 * @Author: Alexey Kutuzov <lexus.1995@mail.ru>
 * Interface FileInterface
 * @package Ceive\Filesystem\StorageLinear
 */
interface FileInterface{
	
	
	public function getPath();
	
	public function getSize();
	
	public function getBasename();
	
	public function getExtension();
	
	public function moveTo($path);
	
	public function copyTo($path);
	
	public function getMimeType();
	
}

