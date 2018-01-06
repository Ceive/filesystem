<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: ceive.filesystem
 */

namespace Ceive\Filesystem;

/**
 * @Author: Alexey Kutuzov <lexus.1995@mail.ru>
 * Interface LocationPoint
 * @package Ceive\Filesystem
 */
interface LocationPoint{
	
	/**
	 * @Relative path
	 * @param $path - pass absolute
	 * @return string
	 * @see StorageInterface::relative
	 */
	public function re($path);
	
	/**
	 * @Relative path
	 * @param $path - pass absolute
	 * @return string
	 * @see StorageInterface::re
	 */
	public function relative($path);
	
	/**
	 * @Absolute path
	 * @param $path - pass relative
	 * @return string
	 * @see StorageInterface::absolute
	 */
	public function abs($path);
	/**
	 * @Absolute path
	 * @param $path - pass relative
	 * @return string
	 * @see StorageInterface::abs
	 */
	public function absolute($path);
	
}

