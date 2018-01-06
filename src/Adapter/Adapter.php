<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 03.02.2016
 * Time: 15:42
 */
namespace Ceive\Filesystem\Adapter {
	
	use Ceive\Filesystem\AdapterInterface;
	use Ceive\Filesystem\Exception;
	use Ceive\Filesystem\FS;
	use Ceive\Filesystem\LocationPoint;
	
	/**
	 * Class Adapter
	 * @package Jungle\FileSystem\Model\Manager
	 */
	abstract class Adapter implements AdapterInterface, LocationPoint{

		/** @var string */
		protected $root;

		/** @var bool  */
		protected $relative_enabled = true;


		public $default_encoding = 'utf-8';

		public $encoding = 'utf-8';
		
		/**
		 * @return mixed
		 */
		public function getEncoding(){
			return $this->encoding;
		}
		
		/**
		 * @return string|null
		 */
		public function getDriveLetter(){
			$letter = substr($this->root,0,2);
			if(substr($letter,1,1) === ':'){
				return strtoupper($letter{0});
			}
			return null;
		}
		
		/**
		 * @deprecated
		 * @param null $enabled
		 * @return bool
		 */
		public function relativeEnabled($enabled = null){
			if(is_bool($enabled)){
				$this->relative_enabled = $enabled;
			}
			return $this->relative_enabled;
		}
		
		/**
		 * @param Adapter $origin
		 * @param $originPath
		 * @param Adapter $destination
		 * @param $destinationPath
		 */
		public function transfer(
			Adapter $origin,        $originPath,
			Adapter $destination,   $destinationPath
		){
			$content = $origin->fileGetContents($originPath);
			$destination->filePutContents($destinationPath,$content);
		}

		/**
		 * @param null $root
		 * @param bool $auto_create
		 * @param null $fs_charset
		 * @throws Exception
		 */
		public function __construct($root = null, $auto_create = false, $fs_charset = null){
			if($fs_charset){
				$this->encoding = $fs_charset;
			}
			$this->setRoot($root,$auto_create);
		}
		
		/**
		 * @param $path
		 * @param bool $auto_create
		 * @throws Exception
		 * @throws \Exception
		 */
		public function setRoot($path,$auto_create = false){
			if($this->root !== null){
				throw new Exception("Root absolute already isset");
			}elseif($path){
				try{
					$oldRoot = $this->root;
					$this->root = '';
					if(!$this->is_dir($path)){
						if($auto_create){
							$this->mkdir($path,0755,true);
						}else{
							throw new Exception("Could not set root absolute to not exists directory");
						}
					}
					$this->root = $path;
				}catch(\Exception $e){
					$this->root = $oldRoot;
					throw $e;
				}
				
			}else{
				$this->root = false;
			}
		}

		/**
		 * @return string
		 */
		public function getRoot(){
			return $this->root;
		}

		/**
		 * @return string
		 */
		public function ds(){
			return DIRECTORY_SEPARATOR;
		}
		
		
		/**
		 * @param $path
		 * @return string
		 */
		public function importFilenameEncoding($path){
			if(!$this->encoding || strcasecmp($this->encoding,$this->default_encoding)===0) return $path;
			return iconv($this->default_encoding, $this->encoding . '//TRANSLIT',$path);
		}
		
		/**
		 * @param $path
		 * @return string
		 */
		public function exportFilenameEncoding($path){
			if(!$this->encoding || strcasecmp($this->encoding,$this->default_encoding)===0) return $path;
			return iconv($this->encoding . '//TRANSLIT', $this->default_encoding ,$path);
		}

		/**
		 * @param $path
		 * @param bool $for_fs @deprecated
		 * @return string
		 */
		public function absolute($path, $for_fs = false){
			if($this->root){
				return FS::path($this->ds(), $this->root, $path);
			}else{
				return $path;
			}
		}

		/**
		 * @param $path
		 * @return false|string
		 */
		public function relative($path){
			if($this->root){
				if(strpos($path,$this->root) === 0){
					return substr($path,strlen($this->root));
				}else{
					return false;
				}
			}else{
				return $path;
			}
		}
		
		public function re($path){
			return $this->relative($path);
		}
		
		public function abs($path){
			return $this->absolute($path);
		}
		
		/**
		 * @param $path
		 * @return string
		 */
		public function internal($path){
			$path = $this->absolute($path);
			$path = $this->importFilenameEncoding($path);
			return $path;
		}
		
		/**
		 * @param $path
		 * @param bool $pathIsRelative
		 * @return null|string
		 */
		public function external($path, $pathIsRelative = false){
			if(!$pathIsRelative)$path = $this->relative($path);
			$path = $this->exportFilenameEncoding($path);
			return $path;
		}
		
		
	}
}

