<?php
	/**
	* Cơ sở dữ liệu
	* @category classes
	*/
	class Database {
		private static $_instance = null;
		private $_pdo,
				$_query,
				$_error = false,
				$_results,
				$_count = 0;
		/**
		 * Hàm kết nối với cơ sở dữ liệu.
		 * xử dụng try catch để xử lý nếu lỗi.
		*/
		private function __construct() {
			try {
				$this->_pdo = new PDO('mysql:host='.Config::get('mysql/host').';dbname='.Config::get('mysql/db'),Config::get('mysql/username'),Config::get('mysql/password'));
			} catch (PDOException $e) {
				die($e->getMessage());
			}
		}
		/**
		* Tạo database nếu chưa tồn tại.
		*/
		public static function getInstance() {
			if (!isset(self::$_instance)) {
				self::$_instance = new Database();
			}
			return self::$_instance;
		}
		/**
		* thực hiện truy vấn sql.
		* @param string $sql Câu truy vấn sql cần thực hiện.
		* @param array $params Danh sách các tham số.
		* @return $this
		*/
		public function query($sql, $params = array()) {
			$this->_error = false;
			if ($this->_query = $this->_pdo->prepare($sql)) {
				$x = 1;
				if (count($params)) {
					foreach ($params as $param) {
						$this->_query->bindValue($x, $param);
						$x++;
					}
				}

				if ($this->_query->execute()) {
					$this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
					$this->_count	= $this->_query->rowCount();
				} else {
					$this->_error = true;
				}
			}

			return $this;
		}
		/**
		* thực hiện hoạt động trong câu truy vấn sql.
		* @param string $action hoạt động.
		* @param string $table Tên bảng trong cơ sở dữ liệu.
		* @param array $where mảng truy vấn vị trí.
		*/
		public function action($action, $table, $where = array()) {
			if (count($where) === 3) {	//Allow for no where
				$operators = array('=','>','<','>=','<=','<>');

				$field		= $where[0];
				$operator	= $where[1];
				$value		= $where[2];

				if (in_array($operator, $operators)) {
					$sql = "{$action} FROM {$table} WHERE ${field} {$operator} ?";
					if (!$this->query($sql, array($value))->error()) {
						return $this;
					}
				}
			}
			return false;
		}
		/**
		* Thực hiện lấy dữ liệu từ bảng từ vị trí.
		* @param string $table Tên bảng cơ sở dữ liệu.
		* @param string $where Câu truy vấn vị trí.
		*/
		public function get($table, $where) {
			return $this->action('SELECT *', $table, $where); //ToDo: Allow for specific SELECT (SELECT username)
		}
		/**
		* thực hiện câu lệnh xóa trong bảng từ vị trí.
		* @param string $table Tên bảng cơ sở dữ liệu.
		* @param string $where Câu truy vấn vị trí.
		*/
		public function delete($table, $where) {
			return $this->action('DELETE', $table, $where);
		}
		/**
		 * thực hiện thêm mới dữ liệu vào bảng.
		 * @param string $table Tên bảng cơ sở dữ liệu.
		 * @param array $fields các dữ liệu thêm mới.
		*/
		public function insert($table, $fields = array()) {
			if (count($fields)) {
				$keys 	= array_keys($fields);
				$values = null;
				$x 		= 1;

				foreach ($fields as $field) {
					$values .= '?';
					if ($x<count($fields)) {
						$values .= ', ';
					}
					$x++;
				}

				$sql = "INSERT INTO {$table} (`".implode('`,`', $keys)."`) VALUES({$values})";

				if (!$this->query($sql, $fields)->error()) {
					return true;
				}
			}
			return false;
		}
		/**
		 * thực hiện cập nhật dữ liệu.
		 * @param string $table Tên bảng cơ sở dữ liêu.
		 * @param number $id id của dòng dữ liệu.
		 * @param array $fields các dữ liêu.
		*/
		public function update($table, $id, $fields = array()) {
			$set 	= '';
			$x		= 1;

			foreach ($fields as $name => $value) {
				$set .= "{$name} = ?";
				if ($x<count($fields)) {
					$set .= ', ';
				}
				$x++;
			}

			$sql = "UPDATE {$table} SET {$set} WHERE id = {$id}";
			
			if (!$this->query($sql, $fields)->error()) {
				return true;
			}
			return false;
		}
		/**
		* thực hiện lấy kết quả dữ liệu.
		*/
		public function results() {
			return $this->_results;
		}
		/**
		 * thực hiện lấy kết quả đầu tiên của dữ liệu.
		*/
		public function first() {
			return $this->_results[0];
		}

		/**
		 * thực hiện lấy thông báo lỗi.
		 */
		public function error() {
			return $this->_error;
		}

		/**
		 * thực hiện lấy số.
		*/
		public function count() {
			return $this->_count;
		}
	}
?>