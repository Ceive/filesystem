<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: ceive.filesystem
 */

namespace Ceive\Filesystem;

const DS = DIRECTORY_SEPARATOR;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class FS
 * @package Ceive\Filesystem
 * todo Lazy normalizing by passed path
 */
class FS{
	
	const DS = DIRECTORY_SEPARATOR;
	
	const DEFAULT_STRATEGY      = [ self::S_REJECT, self::S_MERGE ];
	
	const DEFAULT_FILE_STRATEGY = self::S_REJECT;
	const DEFAULT_DIR_STRATEGY  = self::S_MERGE;
	
	const S_REJECT  = 'reject';
	const S_KEEP    = 'keep';
	const S_MERGE   = 'merge';
	const S_REPLACE = 'replace';
	
	const STRATEGIES = [
		self::S_REJECT,
		self::S_KEEP,
		self::S_MERGE,
		self::S_REPLACE
	];
	
	protected static $normalize_paths           = true;
	protected static $normalize_paths_snapshot  = [];
	protected static $normalize_paths_results   = [];
	
	protected static $paths_cache = [];
	
	public static function enableNormalizePaths($normalize_paths = true){
		self::$normalize_paths_snapshot[] = self::$normalize_paths;
		self::$normalize_paths = $normalize_paths;
	}
	
	/**
	 *
	 */
	public static function restoreNormalizePaths(){
		if(self::$normalize_paths_snapshot){
			if(self::$normalize_paths_snapshot && is_bool($v = array_pop(self::$normalize_paths_snapshot))){
				self::$normalize_paths = array_pop($v);
			}
		}else{
			self::$normalize_paths = true;
		}
	}
	
	/**
	 * @param $strategy
	 * @return array
	 */
	public static function getStrategy($strategy){
		if($strategy === false){
			$strategy = [self::S_REJECT, null];
		}elseif($strategy === true){
			$strategy = [self::S_REPLACE, null];
		}elseif(is_array($strategy)){
			if(!isset($strategy[0])){
				$strategy = array_replace(['file'=>null,'dir'=> null,],(array)$strategy);
				$strategy = [$strategy['file'], $strategy['dir']];
			}
		}else{
			if(is_string($strategy)){
				$strategy = explode(':',$strategy,2);
			}else{
				$strategy = [$strategy, null];
			}
		}
		
		foreach([ self::DEFAULT_FILE_STRATEGY, self::DEFAULT_DIR_STRATEGY, ] as $k => $v){
			if(empty($strategy[$k])){
				$strategy[$k] = $v;
			}
		}
		return $strategy;
	}
	
	/**
	 * @param $source_path
	 * @param $destination_path
	 * @param bool $strategy
	 * @param null $filePermissions
	 * @param null $dirPermissions
	 * @return bool
	 * @throws Exception
	 * @throws Exception\ExceptionAlreadyExists
	 * @throws Exception\ExeptionNotExists
	 */
	public static function copyRecursive($source_path, $destination_path, $strategy = false, $filePermissions = null, $dirPermissions = null){
		list($strategyFile,$strategyDir) = $strategy = self::getStrategy($strategy);
		
		$destination_path = ltrim($destination_path,'/\\');
		$source_path = ltrim($source_path,'/\\');
		
		if(file_exists($source_path)){
			try{
				$oldPermissions = self::freeForRead($source_path, 'copyRecursive-source');
				if(!is_dir($source_path)){
					if(file_exists($destination_path)){
						if($strategyFile === self::S_KEEP) return true;
						if(in_array($strategyFile,[self::S_REPLACE, self::S_MERGE],true)){
							$oldPermissions = self::freeForWrite($destination_path);
							try{
								self::unlink($destination_path);
							}catch(Exception $e){
								self::chmod($destination_path, $oldPermissions);
								throw Exception::error('Copy error: Use overwrite strategy to destination "' . $destination_path . '" deleteRecursive; error: ' . $e->getMessage(),0,$e);
							}
						}else{
							throw Exception::alreadyExists($destination_path, 'Copy error: destination "' . $destination_path . '" already exists, use overvrite strategy or deleteRecursive file');
						}
					}
					self::copy($source_path, $destination_path);
					if($filePermissions!==null){
						self::chmod($destination_path, $filePermissions);
					}
				}else{
					if(is_dir($destination_path)){
						if($strategyDir === self::S_REPLACE){
							self::clean($destination_path,true);
						}elseif($strategyDir === self::S_KEEP){
							return true;
						}elseif($strategyDir === self::S_REJECT){
							throw Exception::alreadyExists($destination_path, 'Copy error: destination dir "' . $destination_path . '" already exists, use overvrite strategy or deleteRecursive directory');
						}
					}
					if(!is_dir($destination_path)){
						self::mkdir($destination_path, 0777, true);
					}
					try{
						$dirResource = self::opendir($source_path);
					}catch(Exception $e){
						throw Exception::error('Copy error: '.$e->getMessage(),0,$e);
					}
					while( ($path = self::readdir($dirResource))!==false){
						if(in_array($path, ['.','..'], true)){
							continue;
						}
						// readdir Использует отностительные пути от директории.
						$src = $source_path . DS . $path;
						$path_destination = $destination_path . DS . basename($path);
						self::copyRecursive($src,$path_destination, $strategy, $filePermissions, $dirPermissions);
					}
					if($dirPermissions!==null){
						self::chmod($destination_path, $dirPermissions);
					}else{
						self::chmod($destination_path, $dirPermissions?:0544);
					}
				}
			}finally{
				if(isset($dirResource)){
					closedir($dirResource);
				}
				self::restorePermissions($source_path, $oldPermissions);
			}
		}else{
			throw Exception::notExists($source_path, 'Source file absolute path "'.$source_path.'" not exists');
		}
		return true;
	}
	
	
	/**
	 * @param $source_path
	 * @param $destination_path
	 * @param $strategy
	 * @return bool
	 * @throws Exception
	 */
	public static function moveRecursive($source_path, $destination_path, $strategy = false){
		
		list($strategyFile,$strategyDir) = self::getStrategy($strategy);
		
		$destination_path = ltrim($destination_path,'/\\');
		$source_path = ltrim($source_path,'/\\');
		
		if(file_exists($source_path)){
			try{
				$oldPermissions = self::freeForRead($source_path, 'copyRecursive-source');
				if(!is_dir($source_path)){
					
					if(file_exists($destination_path)){
						if($strategyFile === self::S_KEEP) return true;
						if(in_array($strategyFile,[self::S_REPLACE, self::S_MERGE],true)){
							$oldPermissions = self::freeForWrite($destination_path);
							try{
								self::deleteRecursive($destination_path);
							}catch(Exception $e){
								self::chmod($destination_path, $oldPermissions);
								throw Exception::error('Move error: Use overwrite strategy to destination "'.$destination_path.'" deleteRecursive; error: '.$e->getMessage(),0,$e);
							}
						}else{
							throw Exception::alreadyExists($destination_path, 'Move error: destination "'.$destination_path.'" already exists, use replace or merge strategy or deleteRecursive file');
						}
					}
					
					self::rename($source_path, $destination_path);
					
				}else{
					
					if(is_dir($destination_path)){
						if($strategyDir === self::S_REPLACE){
							self::deleteRecursive($destination_path, true);
						}elseif($strategyDir === self::S_KEEP){
							return true;
						}elseif($strategyDir === self::S_REJECT){
							throw Exception::alreadyExists($destination_path, 'Move error: Destination dir "'.$destination_path.'" already exists, use replace or merge strategy or deleteRecursive directory');
						}
					}
					
					if(!is_dir($destination_path)){
						self::mkdir($destination_path, 0777, true);
					}
					
					$dirResource = self::opendir($source_path);
					while( ($path = self::readdir($dirResource))!==false){
						if(in_array($path, ['.','..'], true)){
							continue;
						}
						// readdir Использует отностительные пути от директории.
						$src_path = $source_path . DS . $path;
						$path_destination = $destination_path . DS . basename($path);
						self::moveRecursive($src_path,$path_destination, $strategy);
					}
				}
			}finally{
				if(isset($dirResource)){
					closedir($dirResource);
				}
				self::restorePermissions($source_path,$oldPermissions);
			}
		}else{
			throw Exception::notExists($source_path, 'Source file absolute path "'.$source_path.'" not exists');
		}
		return true;
	}
	
	/**
	 * @param $path
	 * @param bool $force
	 * @return bool
	 */
	public static function deleteRecursive($path, $force = false){
		if(file_exists($path)){
			$oldPermissions = null;
			try{
				if($force){
					$oldPermissions = self::freeForWrite($path);
				}
				if(!is_dir($path)){
					self::unlink($path);
				}else{
					$dirResource = self::opendir($path);
					while( ($p = self::readdir($dirResource))!==false){
						if(in_array($p, ['.','..'], true)){
							continue;
						}
						// readdir Использует отностительные пути от директории.
						$p = $path . DS . $p;
						self::deleteRecursive($p);
					}
					closedir($dirResource);
					$dirResource  = null;
					self::rmdir($path);
				}
			}finally{
				if(file_exists($path)){
					if(isset($dirResource)){
						closedir($dirResource);
					}
					self::restorePermissions($path, $oldPermissions);
				}
			}
		}
		return true;
	}
	
	/**
	 * @param $path
	 * @param $mode
	 * @param $useIncludePath
	 * @return resource
	 * @throws Exception
	 */
	public static function fopen($path, $mode, $useIncludePath=null){
		if(!$resource = @fopen($path,$mode, $useIncludePath)){
			$e = error_get_last();
			if($e){
				throw Exception::error($e['message'], $e['type']);
			}
		}
		return $resource;
	}
	
	/**
	 * @return resource
	 * @throws Exception
	 */
	public static function fclose($resource){
		if(!@fclose($resource)){
			$e = error_get_last();
			if($e){
				throw Exception::error($e['message'], $e['type']);
			}
		}
		return $resource;
	}
	
	/**
	 * @param $filename
	 * @param $data
	 * @param $flags
	 * @return bool
	 * @throws Exception
	 */
	public static function file_put_contents($filename,$data, $flags = null){
		if(!@file_put_contents($filename,$data, $flags)){
			$e = error_get_last();
			if($e){
				throw Exception::error($e['message'], $e['type']);
			}
		}
		return true;
	}
	
	/**
	 * @param $filename
	 * @param null $flags
	 * @param null $offset
	 * @param null $maxLen
	 * @return string
	 * @throws Exception
	 */
	public static function file_get_contents($filename, $flags = null,$offset=null,$maxLen = null){
		if(($content = @file_get_contents($filename,$flags,null,$offset,$maxLen)) === false){
			$e = error_get_last();
			if($e){
				throw Exception::error($e['message'], $e['type']);
			}
		}
		return $content;
	}
	
	/**
	 * @param $file_path
	 * @param bool $overwrite
	 * @return bool
	 * @throws Exception
	 */
	public static function blank($file_path, $overwrite = false){
		if(!file_exists($file_path)){
			self::file_put_contents($file_path,'');
		}else{
			if($overwrite){
				self::file_put_contents($file_path,'');
				return true;
			}elseif(filesize($file_path) < 1024 * 10 && @self::file_get_contents($file_path)===''){
				return true;
			}
		}
		return false;
	}
	
	/**
	 * @param $path
	 * @param bool $force
	 * @return bool
	 * @throws Exception
	 */
	public static function clean($path, $force = false){
		if(file_exists($path)){
			$oldPermissions = null;
			try{
				if($force){
					$oldPermissions = self::freeForWrite($path);
				}
				if(!is_dir($path)){
					self::blank($path);
				}else{
					$dirResource = self::opendir($path);
					while( ($p = self::readdir($dirResource))!==false){
						if(in_array($p, ['.','..'], true)){
							continue;
						}
						// readdir Использует отностительные пути от директории.
						$p = $path . DS . $p;
						self::deleteRecursive($p,$force);
					}
					closedir($dirResource);
					$dirResource  = null;
				}
			}finally{
				if(file_exists($path)){
					if(isset($dirResource)){
						closedir($dirResource);
					}
					self::restorePermissions($path, $oldPermissions);
				}
			}
		}
		return true;
	}
	
	/**
	 * @param $source_path
	 * @param $destination_path
	 * @return bool
	 * @throws Exception
	 */
	public static function copy($source_path, $destination_path){
		if(!@copy($source_path, $destination_path)){
			if($e = error_get_last()){
				throw new Exception($e['message']);
			}
		}
		return true;
	}
	
	
	/**
	 * @param $nodepath
	 * @param null $filePermissions
	 * @param null $dirPermissions
	 * @return bool
	 * @throws \Exception
	 */
	public static function chmodRecursive($nodepath, $filePermissions=null, $dirPermissions=null){
		if(!is_dir($nodepath)){
			if($filePermissions !== null){
				self::chmod($nodepath, $filePermissions);
			}
		}else{
			$oldPermissions = fileperms($nodepath);
			self::chmod($nodepath, 0777);
			try{
				$dirResource = self::opendir($nodepath);
				while( ($path = self::readdir($dirResource))!==false){
					if(in_array($path, ['.','..'], true)){
						continue;
					}
					// readdir Использует отностительные пути от директории.
					$path = $nodepath . self::DS . $path;
					self::chmodRecursive($path, $filePermissions, $dirPermissions);
				}
				
			}finally{
				if(isset($dirResource)){
					closedir($dirResource);
				}
				if($dirPermissions!==null){
					self::chmod($nodepath, $dirPermissions);
				}else{
					self::restorePermissions($nodepath,$oldPermissions);
				}
			}
		}
		return true;
	}
	
	/**
	 * @param $dirname
	 * @param $chmod
	 * @param $recursively
	 * @return bool
	 * @throws Exception
	 */
	public static function mkdir($dirname, $chmod = 0777, $recursively = false){
		if(!@mkdir($dirname, $chmod, $recursively)){
			$e = error_get_last();
			if($e){
				throw Exception::error($e['message'],$e['type']);
			}
		}
		return true;
	}
	
	/**
	 * @param $nodepath
	 * @param $new_nodepath
	 * @return bool
	 * @throws Exception
	 */
	public static function rename($nodepath, $new_nodepath){
		if(!@rename($nodepath, $new_nodepath)){
			$e = error_get_last();
			if($e){
				throw Exception::error($e['message'],$e['type']);
			}
		}
		return true;
	}
	
	/**
	 * @param $pattern
	 * @param null $flags
	 * @return array
	 */
	public static function glob($pattern,$flags = null){
		return glob($pattern, $flags);
	}
	
	/**
	 * @param $directory
	 * @param $sorting_order
	 * @return array
	 */
	public static function scandir($directory, $sorting_order = null){
		return scandir($directory, $sorting_order);
	}
	
	/**
	 * @param $resource
	 * @return mixed|string
	 */
	public static function readdir($resource){
		return readdir($resource);
	}
	
	
	/**
	 * @param $path
	 * @return mixed
	 *
	 * Before use system predefined functions for filesystem, paths be exports to filesystem compatible represent of path
	 *
	 */
	public static function exportPathEncoding($path){
		if(!isset(self::$paths_cache[$path])){
			$p = $path;
			self::$paths_cache[$path] = $p;
		}
		return self::$paths_cache[$path];
	}
	
	/**
	 * @param $path
	 * @return mixed
	 *
	 * After fetch by glob etc.. get paths directed to import path to system compatible encoding
	 *
	 */
	public static function importPathEncoding($path){
		if( ($systemPathname = array_search($path, self::$paths_cache, true)) !== false){
			return $systemPathname;
		}
		return $path;
	}
	
	
	/**
	 * @param $path
	 * @return bool
	 */
	public static function isEmpty($path){
		if(file_exists($path)){
			if(is_dir($path)){
				return empty(glob($path . self::DS . '*'));
			}else{
				return (filesize($path) < (1024 * 5)) && !file_get_contents($path);
			}
		}
		return true;
	}
	
	/**
	 * @param $filename
	 * @return bool
	 * @throws Exception
	 */
	public static function unlink($filename){
		if(!@unlink($filename)){
			$e = error_get_last();
			if($e){
				throw Exception::error($e['message'],$e['type']);
			}
		}
		return true;
	}
	
	/**
	 * @param $dirname
	 * @return bool
	 * @throws Exception
	 */
	public static function rmdir($dirname){
		if(!@rmdir($dirname)){
			$e = error_get_last();
			if($e){
				throw Exception::error($e['message'],$e['type']);
			}
		}
		return true;
	}
	
	/**
	 * @param $dirname
	 * @return resource
	 * @throws Exception
	 */
	public static function opendir($dirname){
		if(!($dirResource = @opendir($dirname))){
			$e = error_get_last();
			if($e){
				throw Exception::error($e['message'],$e['type']);
			}
		}
		return $dirResource;
	}
	
	
	/**
	 * @param $nodepath
	 * @param int $permissions
	 * @return bool
	 * @throws Exception
	 */
	public static function chmod($nodepath, $permissions = 0777){
		if(!@chmod($nodepath, $permissions)){
			$e = error_get_last();
			if($e){
				throw Exception::error($e['message'],$e['type']);
			}
		}
		return true;
	}
	
	/**
	 * @param $path
	 * @param $oldPermissions
	 */
	public static function restorePermissions($path, $oldPermissions){
		if(isset($oldPermissions)){
			self::chmod($path, $oldPermissions);
		}
	}
	
	/**
	 * @param $path
	 * @param string $operationKey
	 * @return int|null
	 * @throws \Exception
	 */
	public static function freeForWrite($path, $operationKey = null){
		$oldPermissions = null;
		if(!is_writable($path)){
			$oldPermissions = fileperms($path);
			self::chmod($path,0777);
			if(!is_writable($path)){
				throw Exception::notSetFreeWritable($path, $operationKey);
			}
		}
		return $oldPermissions;
	}
	
	/**
	 * @param $path
	 * @param string $operationKey
	 * @return int|null
	 * @throws \Exception
	 */
	public static function freeForRead($path, $operationKey = 'other'){
		$oldPermissions = null;
		if(!is_readable($path)){
			$oldPermissions = fileperms($path);
			self::chmod($path,0777);
			if(!is_readable($path)){
				throw Exception::notSetFreeReadable($path, $operationKey);
			}
		}
		return $oldPermissions;
	}
	
	/**
	 * @param $path
	 * @param null $separator
	 * @param bool $keepDoubleSlash
	 * @return mixed
	 */
	public static function normalizeSeparators($path, $separator = null, $keepDoubleSlash = false){
		$separator = $separator?:DS;
		// Replace backslashes with forward slashes
		$path = str_replace(['\\','/'], $separator, $path);
		// todo URL vs FS separator scheme vs drive letter
		
		//keep double slash in URL
		$atStart = '';
		if(preg_match('@^(\w+\:)('.preg_quote($separator).'+)@',$path, $m)){
			$atStart = $m[1] . (strlen($m[2]) > 1 && $keepDoubleSlash? ($separator . $separator)  : $separator );
			$path = substr_replace($path,'',0,strlen($m[0]));
		}
		
		
		// Combine multiple slashes into a single slash
		$path = preg_replace('/'.preg_quote($separator,'/').'+/', $separator, $path);
		return $atStart.$path;
	}
	
	/**
	 * @param $string
	 * @param null|string $border - DS  as default
	 * @param null|string $value - '.'  as default
	 * @param int $vMinRepeat
	 * @param int $vMaxRepeat
	 * @return boolean
	 */
	public static function hasBorderedString($string, $border, $value, $vMinRepeat = 1, $vMaxRepeat = 2){
		return preg_match('@(?:^|'.preg_quote($border) . ')(?:' . preg_quote($value,'@') . '){' . intval($vMinRepeat) . ',' . intval($vMaxRepeat) . '}(?:$|' . preg_quote($border) . ')@', $string) > 0;
	}
	
	/**
	 * @param $path
	 * @param null $separator
	 * @param null $dot
	 * @return boolean
	 */
	public static function hasDotSegments($path, $separator=null, $dot=null){
		$separator  = $separator?:DS;
		$dot = $dot?:'.';
		return self::hasBorderedString($path, $separator, $dot, 1, 2);
	}
	
	/**
	 * TODO for filesystem one slash C:/
	 * TODO for urls double slash http://
	 * todo class Path for urls and fs
	 * @param $path
	 * @param $separator
	 * @param bool $keepDoubleSlash(Slashes, for urls)
	 * @return string
	 */
	public static function normalizePath($path, $separator = null, $keepDoubleSlash = false){
		$separator = $separator?:DS;
		// Array to build a new path from the good parts
		$parts = [];
		$path = self::normalizeSeparators($path, $separator, $keepDoubleSlash);
		$segments   = array_filter(explode($separator, $path));
		foreach($segments as $segment){
			if($segment != '.'){
				$test = array_pop($parts);
				if(is_null($test)){
					$parts[] = $segment;
				}else{
					if($segment == '..'){
						if($test == '..'){
							$parts[] = $test;
						}
						if($test == '..' || $test == ''){
							$parts[] = $segment;
						}
					}else{
						$parts[] = $test;
						$parts[] = $segment;
					}
				}
			}
		}
		return implode($separator, $parts);
	}
	
	/**
	 * @param null $separator
	 * @param array ...$segments
	 * @return string
	 */
	public static function path($separator = null, ...$segments){
		$separator = $separator?:DS;
		if(!in_array($separator,['/','\\'], true)){
			array_unshift($segments, $separator);
			$separator = DS;
		}
		return self::normalizePath( self::_path($separator,...$segments) ,$separator);
	}
	
	/**
	 * @param $separator
	 * @param array ...$segments
	 * @return string
	 */
	protected static function _path($separator, ...$segments){
		$a = [];
		//todo array decompose line
		foreach($segments as $argument){
			if(is_array($argument)){
				$a[] = self::_path($separator, ...$argument);
			}else{
				$a[] = $argument;
			}
		}
		return implode($separator, $a);
	}
	
	/**
	 * @param $container_path
	 * @param $inner_path
	 * @return bool
	 */
	public static function isContains($container_path, $inner_path){
		return $container_path !== $inner_path
		       && ($cl = strlen($container_path)) < strlen($inner_path)
		       && substr($inner_path, 0, $cl) === $container_path;
		
	}
	
	public static function chown($path, $owner){
		if(!@chown($path, $owner)){
			$e = error_get_last();
			if($e){
				throw Exception::error($e['message'],$e['type']);
			}
		}
		return true;
	}
	
	public static function chgrp($path, $group){
		if(!@chown($path, $group)){
			$e = error_get_last();
			if($e){
				throw Exception::error($e['message'],$e['type']);
			}
		}
		return true;
	}
	
	public static function touch($path, $modifyTime=null, $accessTime=null){
		if(!@touch($path, $modifyTime, $accessTime)){
			$e = error_get_last();
			if($e){
				throw Exception::error($e['message'],$e['type']);
			}
		}
		return true;
	}
	
	public static function fileatime($path){
		if(($v = @fileatime($path)) === false){
			$e = error_get_last();
			if($e){
				throw Exception::error($e['message'],$e['type']);
			}
		}
		return $v;
	}
	
	public static function filemtime($path){
		if(($v = @filemtime($path)) === false){
			$e = error_get_last();
			if($e){
				throw Exception::error($e['message'],$e['type']);
			}
		}
		return $v;
	}
	
	public static function filectime($path){
		if(($v = @filectime($path)) === false){
			$e = error_get_last();
			if($e){
				throw Exception::error($e['message'],$e['type']);
			}
		}
		return $v;
	}
	
	public static function fileperms($path){
		if(($v = @fileperms($path)) === false){
			$e = error_get_last();
			if($e){
				throw Exception::error($e['message'],$e['type']);
			}
		}
		return $v;
	}
	
	public static function fileowner($path){
		if(($v = @fileowner($path)) === false){
			$e = error_get_last();
			if($e){
				throw Exception::error($e['message'],$e['type']);
			}
		}
		return $v;
	}
	
	
}


