<?php
	/**
	* session
	* @category classes
	*/
	class Session {
		/**
		 * thực hiện kiểm tra sự tồn tại của session.
		 * @param string $name Tên session.
		 * trả về true nếu tồn tại ngược lại trả về false.
		 */
		public static function exists($name) {
			return (isset($_SESSION[$name])) ? true : false;
		}
		/**
		 * thực hiện đặt giá trị của session.
		 *
		 * @param $name Tên biến session.
		 * @param $value Giá trị được đặt cho biến $name.
		 */
		public static function put($name, $value) {
			return $_SESSION[$name] = $value;
		}
		/**
		 * thực hiện lấy giá trị của biến session.
		 *
		 * @param string $name Tên biến session.
		 */
		public static function get($name) {
			return $_SESSION[$name];
		}
		/**
		 * thực hiện xóa session.
		 *
		 * @param $name Tên biến session cần xóa.
		 */
		public static function delete($name) {
			if (self::exists($name)) {
				unset($_SESSION[$name]);
			}
		}
		/**
		 * thực hiện đặt session cho flash.
		 *
		 * @param string $name Tên biến.
		 * @param string $string
		 */
		public static function flash($name, $string = '') {
			if (self::exists($name)) {
				$session = self::get($name);
				self::delete($name);
				return $session;
			} else {
				self::put($name, $string);
			}
		}
	}	
?>