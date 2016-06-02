<?php
	/**
	* băm
	* @category classes
	*/
	class Hash {
		/**
		 * thực hiện băm đoạn ký tự nhập vào.
		 * @param $string Đoạn ký tự cần băm.
		 * @param string $salt Muối trộn thêm trong hàm băm.
		 */
		public static function make($string, $salt = '') {
			return hash('sha256', $string.$salt);
		}
		/**
		 * thực hiện tạo muối.
		 * @param $length Độ dài của muối.
		 */
		public static function salt($length) {
			return mcrypt_create_iv($length);
		}
		
		public static function unique() {
			return self::make(uniqid());
		}
	}
?>