<?php
	/**
	* Dữ liệu nhập vào
	* @category classes
	*/
	class Input {
		/**
		* thực hiện kiểm tra sự tồn tại của biến.
		*
		* @param string $type phương thức kiểm tra.
		*/
		public static function exists($type = 'post') {
			switch ($type) {
				case 'post':
					return (!empty($_POST)) ? true : false;
					break;
				case 'get':
					return (!empty($_GET)) ? true : false;
					break;
				default:
					return false;
					break;
			}
		}
		/**
		* thực hiện lấy dữ liệu biến.
		*
		* @param string $item Tên biến trong POST hoặc GET. 
		*/
		public static function get($item) {
			if (isset($_POST[$item])) {
				return $_POST[$item];
			} else if (isset($_GET[$item])) {
				return $_GET[$item];
			}
			return '';
		}
	}
?>