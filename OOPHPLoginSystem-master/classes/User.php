<?php
	/**
	* người dùng.
	* @category classes
	*/
	class User {
		private $_db,
				$_data,
				$_sessionName,
				$_cookieName,
				$_isLoggedIn;
		/**
		 * thực hiện việc tạo tên bảng cơ sở dữ liệu, tạo session, tạo cookie cho user.
		 * @param null $user
		 */
		public function __construct($user = null) {
			$this->_db 			= Database::getInstance();
			$this->_sessionName = Config::get('session/sessionName');
			$this->_cookieName 	= Config::get('remember/cookieName');

			if (!$user) {
				if (Session::exists($this->_sessionName)) {
					$user = Session::get($this->_sessionName);

					if ($this->find($user)) {
						$this->_isLoggedIn = true;
					} else {
						self::logout();
					}
				}
			} else {
				$this->find($user);
			}
		}
		/**
		 * thực hiện cập nhật các dữ liệu người dùng.
		 * @param array $fields các dữ liệu cập nhật.
		 * @param number|null $id id của người dùng.
		 * @throws Exception
		 */
		public function update($fields = array(), $id = null) {

			if (!$id && $this->isLoggedIn()) {
				$id = $this->data()->ID;
			}

			if (!$this->_db->update('users', $id, $fields)) {
				throw new Exception("There was a problem updating your details");
			}
		}
		/**
		 * thực hiện tạo mới người dùng.
		 * @param array $fields các dữ liệu người dùng.
		 * @throws Exception
		 */
		public function create($fields = array()) {
			if (!$this->_db->insert('users', $fields)) {
				throw new Exception("There was a problem creating your account");
			}
		}
		/**
		 * thực hiện tìm kiếm một người dùng trong cơ sở dữ liệu theo id hoặc username.
		 * @param string $user Người dùng cần tìm kiếm là tên hoặc username.
		 */
		public function find($user = null) {
			if ($user) {
				$fields = (is_numeric($user)) ? 'id' : 'username';	//Numbers in username issues
				$data 	= $this->_db->get('users', array($fields, '=', $user));

				if ($data->count()) {
					$this->_data = $data->first();
					return true;
				}
			}
			return false;
		}
		/**
		 * thực hiện kiểm tra đăng nhập người dùng.
		 * @param string $username Tên đăng nhập của người dùng.
		 * @param string $password Mật khẩu của người dùng.
		 * @param bool $remember Trạng thái có nhớ đăng nhập hay không.
		 */
		public function login($username = null, $password = null, $remember = false) {
			if (!$username && !$password && $this->exists()) {
				Session::put($this->_sessionName, $this->data()->ID);
			} else {
				$user = $this->find($username);
				if ($user) {
					if ($this->data()->password === Hash::make($password,$this->data()->salt)) {
						Session::put($this->_sessionName, $this->data()->ID);

						if ($remember) {
							$hash = Hash::unique();
							$hashCheck = $this->_db->get('usersSessions', array('userID','=',$this->data()->ID));

							if (!$hashCheck->count()) {
								$this->_db->insert('usersSessions', array(
									'userID' 	=> $this->data()->ID,
									'hash' 		=> $hash
								));
							} else {
								$hash = $hashCheck->first()->hash;
							}
							Cookie::put($this->_cookieName, $hash, Config::get('remember/cookieExpiry'));
						}

						return true;
					}
				}
			}
			return false;
		}
		/**
		 * thực hiện kiểm tra quyền của người dùng.
		 * @param $key khóa quyền của người dùng.
		 */
		public function hasPermission($key) {
			$group = $this->_db->get('groups', array('ID', '=', $this->data()->userGroup));
			if ($group->count()) {
				$permissions = json_decode($group->first()->permissions,true);

				if ($permissions[$key] == true) {
					return true;
				}
			}
			return false;
		}
		/**
		 * thực hiện kiểm tra sự tồn tại của người dùng.
		 */
		public function exists() {
			return (!empty($this->_data)) ? true : false;
		}
		/**
		 * thực hiện đăng xuất.
		 */
		public function logout() {
			$this->_db->delete('usersSessions', array('userID', '=', $this->data()->ID));
			Session::delete($this->_sessionName);
			Cookie::delete($this->_cookieName);
		}
		/**
		 * thực hiện lấy kết quả dữ liệu của người dùng.
		 */
		public function data() {
			return $this->_data;
		}
		/**
		 * thực hiện kiểm tra trạng thái đăng nhập.
		 */
		public function isLoggedIn() {
			return $this->_isLoggedIn;
		}
	}
?>