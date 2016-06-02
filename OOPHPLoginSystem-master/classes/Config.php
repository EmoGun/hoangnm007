<?php
	
	/**
	* Cấu hình
	* @Category classes
	* 
	*/ 
	class Config {

		/**
		* 		Lấy giá trị từ biến Config. 
		* @param string $path tên giá trị trong mảng config truy xuất.
		* @return string|false Trả về giá trị nếu tồn tại ngược lại trả về false.
		*/
		public static function get($path = null) {
			if ($path) {
				$config = $GLOBALS['config'];
				$path	= explode('/', $path);

				foreach ($path as $bit) {
					if (isset($config[$bit])) {
						$config = $config[$bit];
					}
				}

				return $config;
			}
			
			return false;
		}
	}
?>