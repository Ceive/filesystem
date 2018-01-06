<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 01.02.2016
 * Time: 22:36
 */
namespace Ceive\Filesystem {

	/**
	 * Class Permissions
	 * @package Jungle\FileSystem\Model
	 */
	class Permissions implements PermissionsInterface{

		/** @var int decimal file mod */
		protected $decimal = 0;
		
		/** @return bool */
		public function hasOwnerRead(){
			return boolval($this->decimal & self::PERMISSION_OWNER_READ);
		}

		/** @return bool */
		public function hasOwnerWrite(){
			return boolval($this->decimal & self::PERMISSION_OWNER_WRITE);
		}

		/** @return bool */
		public function hasOwnerExecute(){
			return boolval($this->decimal & self::PERMISSION_OWNER_EXECUTE);
		}

		/**
		 * @param bool|true $readable
		 * @return $this
		 */
		public function setOwnerRead($readable = true){
			$this->decimal = $readable?
				$this->decimal | self::PERMISSION_OWNER_READ :
				$this->decimal ^ self::PERMISSION_OWNER_READ ;
			return $this;
		}

		/**
		 * @param bool|true $writable
		 * @return $this
		 */
		public function setOwnerWrite($writable = true){
			$this->decimal = $writable ?
				$this->decimal | self::PERMISSION_OWNER_WRITE:
				$this->decimal ^ self::PERMISSION_OWNER_WRITE;
			return $this;
		}

		/**
		 * @param bool|true $executable
		 * @return $this
		 */
		public function setOwnerExecute($executable = true){
			$this->decimal = $executable ?
				$this->decimal | self::PERMISSION_OWNER_EXECUTE :
				$this->decimal ^ self::PERMISSION_OWNER_EXECUTE;
			return $this;
		}
		
		/** @return bool */
		public function hasGroupRead(){
			return boolval($this->decimal & self::PERMISSION_GROUP_READ);
		}

		/** @return bool */
		public function hasGroupWrite(){
			return boolval($this->decimal & self::PERMISSION_GROUP_WRITE);
		}

		/** @return bool */
		public function hasGroupExecute(){
			return boolval($this->decimal & self::PERMISSION_GROUP_EXECUTE);
		}

		/**
		 * @param bool|true $readable
		 * @return $this
		 */
		public function setGroupRead($readable = true){
			$this->decimal = $readable ?
				$this->decimal | self::PERMISSION_GROUP_READ :
				$this->decimal ^ self::PERMISSION_GROUP_READ;
			return $this;
		}

		/**
		 * @param bool|true $writable
		 * @return $this
		 */
		public function setGroupWrite($writable = true){
			$this->decimal = $writable ?
				$this->decimal | self::PERMISSION_GROUP_WRITE :
				$this->decimal ^ self::PERMISSION_GROUP_WRITE;
			return $this;
		}

		/**
		 * @param bool|true $executable
		 * @return $this
		 */
		public function setGroupExecute($executable = true){
			$this->decimal = $executable ?
				$this->decimal | self::PERMISSION_GROUP_EXECUTE :
				$this->decimal ^ self::PERMISSION_GROUP_EXECUTE;
			return $this;
		}





		/** @return bool */
		public function hasWorldRead(){
			return boolval($this->decimal & self::PERMISSION_WORLD_READ);
		}

		/** @return bool */
		public function hasWorldWrite(){
			return boolval($this->decimal & self::PERMISSION_WORLD_WRITE);
		}

		/** @return bool */
		public function hasWorldExecute(){
			return boolval($this->decimal & self::PERMISSION_WORLD_EXECUTE);
		}

		/**
		 * @param bool|true $readable
		 * @return $this
		 */
		public function setWorldRead($readable = true){
			$this->decimal = $readable ?
				$this->decimal | self::PERMISSION_WORLD_READ :
				$this->decimal ^ self::PERMISSION_WORLD_READ;
			return $this;
		}

		/**
		 * @param bool|true $writable
		 * @return $this
		 */
		public function setWorldWrite($writable = true){
			$this->decimal = $writable ?
				$this->decimal | self::PERMISSION_WORLD_WRITE :
				$this->decimal ^ self::PERMISSION_WORLD_WRITE;
			return $this;
		}

		/**
		 * @param bool|true $executable
		 * @return $this
		 */
		public function setWorldExecute($executable = true){
			$this->decimal = $executable ?
				$this->decimal | self::PERMISSION_WORLD_EXECUTE :
				$this->decimal ^ self::PERMISSION_WORLD_EXECUTE;
			return $this;
		}

		/**
		 * @param string|int|array|PermissionsInterface $permissions
		 * @return bool
		 */
		public function isEqual($permissions){
			return boolval( $this->decimal === $permissions);
		}

		/**
		 * @param $permissions
		 * @param bool $merge
		 * if merge === true each status to be checked if null,
		 * is null passed , status will be set from this object
		 * @return mixed
		 */
		public function setPermissions($permissions, $merge = false){
			$this->decimal = $this->_convertPermissionsFrom($permissions,true, $merge);
			return $this;
		}

		/**
		 * @param bool $octal
		 * @return string|int
		 */
		public function getPermissions($octal = true){
			$d = 0777 & $this->decimal;
			return $octal?decoct($d):$d;
		}

		/**
		 * @param int|null $permissions
		 * @return array [
		 *      'owner' => ['read' => true, 'write' => true, 'write' => true]
		 *      'group' => ['read' => true, 'write' => true, 'write' => true]
		 *      'world' => ['read' => true, 'write' => true, 'write' => true]
		 * ]
		 *
		 */
		public function toArray($permissions = null){
			if($permissions===null){
				$permissions = $this->decimal;
			}
			return [
				'owner' => [
					'read'      => boolval($permissions & self::PERMISSION_OWNER_READ),
					'write'     => boolval($permissions & self::PERMISSION_OWNER_WRITE),
					'execute'   => boolval($permissions & self::PERMISSION_OWNER_EXECUTE)
				],
				'group' => [
					'read'      => boolval($permissions & self::PERMISSION_GROUP_READ),
					'write'     => boolval($permissions & self::PERMISSION_GROUP_WRITE),
					'execute'   => boolval($permissions & self::PERMISSION_GROUP_EXECUTE)
				],
				'world' => [
					'read'      => boolval($permissions & self::PERMISSION_WORLD_READ),
					'write'     => boolval($permissions & self::PERMISSION_WORLD_WRITE),
					'execute'   => boolval($permissions & self::PERMISSION_WORLD_EXECUTE)
				]
			];
		}
		
		
		/**
		 * @param string|int $mod numeric
		 * @return $this
		 * @throws Exception
		 */
		public function setMod($mod){
			if(!is_numeric($mod)){
				throw new \InvalidArgumentException(__FUNCTION__ . ' $mod passed is not a numeric value');
			}
			$this->decimal = is_string($mod)?octdec($mod):$mod;
			return $this;
		}

		/**
		 * @param bool $decimal
		 * @return int|string
		 */
		public function getMod($decimal = true){
			return $decimal? $this->decimal : decoct($this->decimal);
		}

		/**
		 * @return int
		 */
		public function getDecimalMod(){
			return $this->decimal;
		}

		/**
		 * @return string
		 */
		public function getOctalMod(){
			return decoct($this->decimal);
		}




		/**
		 * @return number
		 */
		protected function _getModWithoutPermissionsBits(){
			$d = decoct($this->decimal);
			if(strlen($d) > 3){
				$d = substr(decoct($this->decimal),0,-3) . '000';
			}else{
				$d = '000';
			}
			return octdec($d);
		}

		/**
		 * @param $permissions
		 * @param bool $decimal
		 * @param bool $merge
		 * @return int|string if decimal === false
		 */
		protected function _convertPermissionsFrom($permissions, $decimal = true, $merge = false){
			if(is_string($permissions)){
				$permissions = $this->_permissionsFromString($permissions);
			}elseif(is_array($permissions)){
				$permissions = $this->_permissionsFromArray($permissions,($merge?$this->toArray():[]));
			}elseif($permissions instanceof PermissionsInterface){
				$permissions = octdec($permissions->getPermissions());
			}

			if(!is_int($permissions)){
				$permissions = 0;
			}else{
				return $permissions;
			}
			$permissions = decoct($permissions);
			$permissions = strlen($permissions) > 3 ? substr($permissions,-3) : $permissions;
			return $decimal?octdec($permissions):$permissions;
		}


		/**
		 * @param array $permissions
		 * @param array $defaults
		 * @return int
		 */
		protected function _permissionsFromArray(array $permissions,array $defaults = []){
			$array = [
				'owner' => ['read' => null, 'write' => null, 'execute' => null],
				'group' => ['read' => null, 'write' => null, 'execute' => null],
				'world' => ['read' => null, 'write' => null, 'execute' => null]
			];
			$decimal = 0;
			foreach($array as $type => & $rights){
				foreach($rights as $access => & $status){
					$s = null;
					if(isset($permissions[$type]) && isset($permissions[$type][$access]) && !is_null($permissions[$type][$access])){
						$s = $permissions[$type][$access];
					}elseif(isset($defaults[$type]) && isset($defaults[$type][$access]) && !is_null($defaults[$type][$access])){
						$s = $defaults[$type][$access];
					}else{
						$s = false;
					}
					$status =  boolval(is_numeric($s)?intval($s):$s);
					if($status){
						$decimal = $decimal | constant(PermissionsInterface::class . '::PERMISSION_' . strtoupper($type) . '_' . strtoupper($access) );
					}
				}
			}
			return $decimal;
		}

		/**
		 * @param $permissions
		 * @return number
		 */
		protected function _permissionsFromString($permissions){
			return octdec($permissions);
		}


		/**
		 * @param $permissions
		 * @return number
		 */
		protected function _permissionsFromUnix($permissions){
			throw new \BadMethodCallException('_permissionsFromUnix not work!');
		}



		public function __toString(){
			return $this->getMod(false);
		}

	}
}

