<?php
	/** 
	* Cookie
	* @category classes
	*/
	class Cookie {
		/** 
		* Kiểm tra sự tồn tại của cookie.
		* @param string $name Tên cookie.
		* @return gun Trả về true nếu tồn tại cookie ngược lại trả về false.
		*/
		public static function exists($name) {
			return (isset($_COOKIE[$name])) ? true : false;
		}
		/**
		* Lấy dữ liệu từ cookie
		* @param string $name Tên cookie lấy dữ liệu.
		* @return gun Trả về giá trị của cookie.
		*/
		public static function get($name) {
			return $_COOKIE[$name];
		}
		/**
		* Cài đặt giá trị và thời gian tồn tại cho cookie
		* @param string $name Tên cookie.
		* @param string $value Giá trị biến $name.
		* @param number $expiry Thời gian cookie tồn tại.
		*/
		public static function put($name, $value, $expiry) {
			if (setcookie($name, $value, time()+$expiry, '/')) {
				return true;
			}
			return false;
		}
		/**
		* Hàm thực hiện xóa cookie
		* @param string $name Tên cookie cần xóa.
		*/
		public static function delete($name) {
			self::put($name, '', time()-1);
		}
	}
?>