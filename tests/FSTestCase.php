<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: ceive.filesystem
 */

namespace Ceive\Filesystem\Tests;

use Ceive\Filesystem\FS;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class FSTestCase
 * @package Ceive\Filesystem\Tests
 */
class FSTestCase extends \PHPUnit_Framework_TestCase{
	
	//todo: предварительная проверка рекурсивных операций без осуществения самого действия дабы не нарушать целостность структуры папки в момент его осуществления
	//todo: FS:canCopy
	//todo: FS:canMove
	//todo: FS:canRemove
	
	public $dirname;
	public $source_dirname;
	
	
	public function setUp(){
		$this->source_dirname   = __DIR__ . DIRECTORY_SEPARATOR . 'my_filesystem';
		$this->dirname          = __DIR__ . DIRECTORY_SEPARATOR . 'my_tests';
		
	}
	
	protected function tearDown(){
		
	}
	
	
	public function testCase(){
		if(!is_dir($this->dirname)){
			FS::mkdir($this->dirname, 0777, true);
		}
		
		$source_path = $this->source_dirname . FS::DS . 'pro100';
		$destination_path = $this->dirname . FS::DS . 'my';
		FS::copyRecursive($source_path, $destination_path, FS::STRATEGY_OVERWRITE);
		
		FS::clean($destination_path);
		
		FS::deleteRecursive($destination_path);
		
	}
	
	
	public function testRealpath(){
		
		$path = '/./../my/path//////////////////////////////';
		$dir = __DIR__;
		$path1 = FS::path(null, __DIR__, $path);
		$path2 = FS::path('/', 'http://google.com/foreground/at', $path);
		echo $path1;
	}
	
}


