<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: ceive.filesystem
 */

namespace Ceive\Filesystem\Exception;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class ExceptionPermissionsDenied
 * @package Ceive\Filesystem\Exception
 */
class ExceptionPermissionsDenied extends PathAware{
	
	
	
	public function __construct($path, $message = "", $code = 0, $previous = null){
		
		parent::__construct(
			$path,
			$message,
			$code,
			$previous
		);
	}
	
	
}

