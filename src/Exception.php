<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: ceive.filesystem
 */

namespace Ceive\Filesystem;

use Ceive\Filesystem\Exception\ExceptionAlreadyExists;
use Ceive\Filesystem\Exception\ExeptionNotExists;
use Ceive\Filesystem\Exception\ExceptionPermissionsDenied;

class Exception extends \Exception{
	
	
	/**
	 * @param $path
	 * @return Exception
	 */
	public static function changePermission($path){
		return new Exception('Error change permissions for "'.$path.'"');
	}
	
	public static function notReadable($path, $useAutoPermissionsUnlock = false){
		return new ExceptionPermissionsDenied($path, 'Access Denied for Read from "' . $path . '"; use auto permissions unlock: ' . ($useAutoPermissionsUnlock?'Yes':'No') . '');
	}
	
	public static function notWritable($path, $useAutoPermissionsUnlock = false){
		return new ExceptionPermissionsDenied($path, 'Access Denied for Write to "' . $path . '"; use auto permissions unlock: ' . ($useAutoPermissionsUnlock?'Yes':'No') . '');
	}
	
	public static function notDeletable($path, $useAutoPermissionsUnlock = false){
		return new ExceptionPermissionsDenied($path, 'Access Denied for Write(Delete) to "' . $path . '"; use auto permissions unlock: ' . ($useAutoPermissionsUnlock?'Yes':'No') . '');
	}
	
	public static function notSetFreeWritable($path, $operationKey=null){
		if(empty($operationKey))$operationKey = 'other';
		return new ExceptionPermissionsDenied($path, 'Error could not set Writable for [operation: ' . $operationKey . ']; to path: "' . $path . '"');
	}
	
	public static function notSetFreeReadable($path, $operationKey = null){
		if(empty($operationKey))$operationKey = 'other';
		return new ExceptionPermissionsDenied($path, 'Error could not set Readable for [operation: ' . $operationKey . ']; to path: "' . $path . '"');
	}
	
	public static function locked($path){
		return new Exception('Error locked; path: "'.$path.'"');
	}
	
	public static function lockedForRead($path){
		return new Exception('Error locked for read; path: "'.$path.'"');
	}
	
	public static function lockedForWrite($path){
		return new Exception('Error locked for write; path: "'.$path.'"');
	}
	
	public static function alreadyExists($path, $message = ''){
		return new ExceptionAlreadyExists($path, $message);
	}
	public static function permissionsDenied($path, $message = ''){
		return new ExceptionPermissionsDenied($path, $message);
	}
	public static function notExists($path, $message = ''){
		return new ExeptionNotExists($path, $message);
	}
	
	public static function error($message, $code = 0, $previous = null){
		return new Exception($message, $code, $previous);
	}
	
}


