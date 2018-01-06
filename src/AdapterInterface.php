<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: ceive.filesystem
 */

namespace Ceive\Filesystem;

/**
 * @Author: Alexey Kutuzov <lexus.1995@mail.ru>
 * Interface AdapterInterface
 * @package Ceive\Filesystem
 */
interface AdapterInterface{
	
	/**
	 * @return mixed
	 */
	public function getEncoding();
	
	/**
	 * @return string|null
	 */
	public function getDriveLetter();
	
	
	public function deleteRecursive($path, $force = false);
	
	public function moveRecursive($path, $destination, $strategy = false);
	
	public function copyRecursive($path, $destination, $strategy = false);
	
	public function chmodRecursive($path, $permissions = 0777);
	
	public function getSize($path);
	
	/**
	 * @param $path
	 * @return int
	 */
	public function filesize($path);
	
	/**
	 * @param $path
	 * @return float
	 */
	public function disk_free_space($path);
	
	/**
	 * @param $path
	 * @return float
	 */
	public function disk_total_space($path);
	
	/**
	 * @param $path
	 * @param null $modifyTime
	 * @param null $accessTime
	 * @return mixed
	 */
	public function touch($path, $modifyTime = null, $accessTime = null);
	
	/**
	 * @param $path
	 * @return mixed
	 */
	public function getAccessTime($path);
	
	/**
	 * @param $path
	 * @return mixed
	 */
	public function getModifyTime($path);
	
	/**
	 * @param $path
	 * @return mixed
	 */
	public function getCreateTime($path);
	
	/**
	 * @param string $path
	 * @return bool
	 */
	public function is_link($path);
	
	/**
	 * @param string $path
	 * @return bool
	 */
	public function is_dir($path);
	
	/**
	 * @param string $path
	 * @return bool
	 */
	public function is_file($path);
	
	/**
	 * @param string $path
	 * @return bool
	 */
	public function is_readable($path);
	
	/**
	 * @param string $path
	 * @return bool
	 */
	public function is_writable($path);
	
	/**
	 * @param string $path
	 * @return bool
	 */
	public function is_executable($path);
	
	/**
	 * @param string $path
	 * @return int
	 */
	public function fileperms($path);
	
	/**
	 * @param string $path
	 * @return int
	 */
	public function fileowner($path);
	
	/**
	 * @param string $path
	 * @return bool
	 */
	public function file_exists($path);
	
	/**
	 * @param string $path
	 * @return bool
	 */
	public function unlink($path);
	
	/**
	 * @param string $path
	 * @param int $mod
	 * @param bool $recursive
	 * @return bool
	 */
	public function mkdir($path, $mod = 0777, $recursive = false);
	
	/**
	 * @param $path
	 * @return bool
	 * @throws \LogicException
	 */
	public function mkfile($path);
	
	/**
	 * @param string $path
	 * @return bool
	 */
	public function rmdir($path);
	
	/**
	 * @param string $path
	 * @param int $owner
	 * @return bool
	 */
	public function changeOwner($path, $owner);
	
	/**
	 * @param string $path
	 * @param int $mod
	 * @return bool
	 */
	public function changePermissions($path, $mod);
	
	/**
	 * @param $path
	 * @param $group
	 * @return mixed
	 */
	public function changeGroup($path, $group);
	
	/**
	 * @param string $path
	 * @param string $newPath
	 * @return bool
	 */
	public function rename($path, $newPath);
	
	/**
	 * @param string $path
	 * @param string $destination
	 * @return bool
	 */
	public function copy($path, $destination);
	
	/**
	 * @param $path
	 * @return array
	 */
	public function nodeList($path);
	
	/**
	 * @param $pattern
	 * @return array
	 */
	public function nodeListMatch($pattern);
	
	/**
	 * @param string $filePath
	 * @return string
	 */
	public function fileGetContents($filePath);
	
	/**
	 * @param string $filePath
	 * @param string $content
	 * @return mixed
	 */
	public function filePutContents($filePath, $content);
	
}

