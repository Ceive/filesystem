<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.filesystem
 */

namespace Ceive\Filesystem\OperationMode;
use Ceive\Filesystem\AdapterInterface;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class OperationMode
 * @package Ceive\Filesystem\OperationMode
 */
class OperationMode{
	
	/**
	 * @param $adapterOrigin
	 * @param $pathOrigin
	 * @param $adapterDestination
	 * @param $pathDestination
	 */
	public function resolve(
		AdapterInterface $adapterOrigin, $pathOrigin,
		AdapterInterface $adapterDestination, $pathDestination
	){
		
		
		if($adapterDestination->file_exists($pathDestination)){
			
		}
		
	}
	
	public function resolveExisting(AdapterInterface $adapterDestination, $pathDestination){
		$adapterDestination->unlink($pathDestination);
		return true;
	}
	
	
}


