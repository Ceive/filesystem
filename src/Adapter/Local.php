<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 03.02.2016
 * Time: 15:43
 */
namespace Ceive\Filesystem\Adapter {
	
	use Ceive\Filesystem\FS;
	
	/**
	 * Class Local
	 * @package Jungle\FileSystem\Adapter
	 */
	class Local extends Adapter{

		/**
		 * @param $path
		 * @return int
		 */
		public function filesize($path){
			return filesize($this->internal($path));
		}

		/**
		 * @param $path
		 * @return float
		 */
		public function disk_total_space($path){
			return disk_total_space($this->internal($path));
		}

		/**
		 * @param $path
		 * @return float
		 */
		public function disk_free_space($path){
			return disk_free_space($this->internal($path));
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function is_link($path){
			return is_link($this->internal($path));
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function is_dir($path){
			return is_dir($this->internal($path));
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function is_file($path){
			return is_file($this->internal($path));
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function is_readable($path){
			return is_readable($this->internal($path));
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function is_writable($path){
			return is_writable($this->internal($path));
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function is_executable($path){
			return is_executable($this->internal($path));
		}

		/**
		 * @param string $path
		 * @return int
		 */
		public function fileperms($path){
			return FS::fileperms($this->internal($path));
		}

		/**
		 * @param string $path
		 * @return int
		 */
		public function fileowner($path){
			return FS::fileowner($this->internal($path));
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function file_exists($path){
			return file_exists($this->internal($path));
		}

		/**
		 * @param $path
		 * @return bool
		 */
		public function mkfile($path){
			$this->filePutContents($path,'');
			return false;
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function unlink($path){
			return FS::unlink($this->internal($path));
		}

		/**
		 * @param string $path
		 * @param int $mod
		 * @param bool $recursive
		 * @return bool
		 */
		public function mkdir($path, $mod = 0777, $recursive = false){
			return FS::mkdir($this->internal($path),$mod,$recursive);
		}

		/**
		 * @param string $path
		 * @return bool
		 */
		public function rmdir($path){
			return FS::rmdir($this->internal($path));
		}

		/**
		 * @param string $path
		 * @param int $owner
		 * @return bool
		 */
		public function changeOwner($path, $owner){
			return FS::chown($this->internal($path),$owner);
		}

		/**
		 * @param string $path
		 * @param int $mod
		 * @return bool
		 */
		public function changePermissions($path, $mod){
			return FS::chmod($this->internal($path),$mod);
		}

		/**
		 * @param $path
		 * @param $group
		 * @return mixed
		 */
		public function changeGroup($path, $group){
			return FS::chgrp($this->internal($path), $group);
		}

		/**
		 * @param string $path
		 * @param string $newPath
		 * @return bool
		 */
		public function rename($path, $newPath){
			return FS::rename($this->internal($path), $this->internal($newPath));
		}

		/**
		 * @param string $path
		 * @param string $destination
		 * @return bool
		 */
		public function copy($path, $destination){
			return FS::copy($this->internal($path), $this->internal($destination));
		}

		/**
		 * @param $path
		 * @return array
		 */
		public function nodeList($path){
			$a = [];
			foreach(FS::scandir($this->internal($path)) as $sub){
				if(!in_array($sub,['.','..'],true))$a[] = $this->external(FS::path($this->ds(), $path,$sub), true);
			}
			return $a;
		}

		/**
		 * @param $pattern
		 * @return array
		 */
		public function nodeListMatch($pattern){
			$a = [];
			foreach(FS::glob($this->internal($pattern)) as $path){
				if(!in_array($path,['.','..'],true)){
					$a[] = $this->external($path);
				}
			}
			return $a;
		}

		/**
		 * @param $path
		 * @param null $modifyTime
		 * @param null $accessTime
		 * @return mixed
		 */
		public function touch($path, $modifyTime = null, $accessTime = null){
			return FS::touch($this->internal($path),$modifyTime,$accessTime);
		}

		/**
		 * @param $path
		 * @return mixed
		 */
		public function getAccessTime($path){
			return FS::fileatime($this->internal($path));
		}

		/**
		 * @param $path
		 * @return mixed
		 */
		public function getModifyTime($path){
			return FS::filemtime($this->internal($path));
		}

		/**
		 * @param $path
		 * @return mixed
		 */
		public function getCreateTime($path){
			return FS::filectime($this->internal($path));
		}

		/**
		 * @param string $filePath
		 * @return string
		 */
		public function fileGetContents($filePath){
			return FS::file_get_contents($this->internal($filePath));
		}

		/**
		 * @param string $filePath
		 * @param string $content
		 * @return mixed
		 */
		public function filePutContents($filePath, $content){
			return FS::file_put_contents($this->internal($filePath), $content);
		}
		
		public function deleteRecursive($path, $force = false){
			return FS::deleteRecursive($path,$force);
		}
		
		public function moveRecursive($path, $destination, $strategy = false){
			return FS::moveRecursive($path,$destination,$strategy);
		}
		
		public function copyRecursive($path, $destination, $strategy = false, $filePermissions = null, $directoryPermissions = null){
			return FS::copyRecursive($path,$destination,$strategy,$filePermissions,$directoryPermissions);
		}
		
		public function chmodRecursive($path, $filePermissions = null, $directoryPermissions = null){
			return FS::chmodRecursive($path,$filePermissions, $directoryPermissions);
		}
		
		/**
		 * @param $path
		 * @return int
		 */
		public function getSize($path){
			$size = 0;
			if($this->is_dir($path)){
				foreach($this->nodeList($path) as $p){
					$child = FS::path($this->ds(), $path, $p);
					$size+= $this->getSize($child);
				}
			}else{
				$size+=$this->filesize($path);
			}
			return $size;
		}
	}
}

