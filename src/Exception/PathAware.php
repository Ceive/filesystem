<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: ceive.filesystem
 */

namespace Ceive\Filesystem\Exception;

use Ceive\Filesystem\Exception;


/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class PathAware
 * @package Ceive\Filesystem\Exception
 */
class PathAware extends Exception{
	
	public $path;
	
	/**
	 * PathAware constructor.
	 * @param string $path
	 * @param string $message
	 * @param int $code
	 * @param Exception|null $previous
	 */
	public function __construct($path, $message = "", $code = 0, Exception $previous = null){
		$this->path = $path;
		parent::__construct(
			$message,
			$code,
			$previous
		);
	}
	
	
	public function getPath(){
		return $this->path;
	}
	
}


