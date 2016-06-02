<?php
	/**
	* token
	* @category classes
	*/
	class Token {
		/**
		* thực hiện tạo token.
		*/
		public static function generate() {
			return Session::put(Config::get('session/tokenName'), md5(uniqid()));
		}
		/**
		 * thực hiện kiểm tra token.
		 *
		 * @param $token Giá trị token cần kiểm tra.
		 */
		public static function check($token) {
			$tokenName = Config::get('session/tokenName');

			if (Session::exists($tokenName) && $token === Session::get($tokenName)) {
				Session::delete($tokenName);
				return true;
			} else {
				return false;
			}
		}
	}
?>