<?php

//简单数据库操作类，实现链式操作
class db{
	//数据库名称
	private $dbhost;
	private $dbname;
	private $dbuser;
	private $dbpassword;
	private $conn;

	private $join;
	private $fields;
	private $where;
	private $tableName;
	private $alias;
	private $group;
	private $limit;
	private $order;
	private $set;
	private $sql;//执行的sql语句
	private $queryType;//查询方式，区分添加，修改，删除

	public function __construct($dbhost, $dbname, $dbuser, $dbpassword) {
		$this->dbhost 		= $dbhost;
		$this->dbname 		= $dbname;
		$this->dbuser 		= $dbuser;
		$this->dbpassword 	= $dbpassword;
		$this->connect();
		$this->changeDb($dbname);
	}

	public function connect() {
		$conn = mysql_connect($this->dbhost, $this->dbuser, $this->dbpassword);
		if (!$conn) {
			throw new Exception("数据库无法连接", 1);
			return false;
		} else {
			$this->conn = $conn;
			mysql_set_charset('utf8',$this->conn);
			return true;
		}
	}

	public function changeDb($dbname) {
		if (!$this->conn) {
			try {
				$this->connect();
				return mysql_query('use '.$dbname);
			} catch (Exception $e) {
				echo $e;
				return false;
			}
		} else {
			return mysql_query('use '.$dbname);
		}
	}

	public function query($sql = '') {
		if ($sql != '') {
			return mysql_query($sql);
		}
	}

	public function select() {
		$this->queryType = 's';
		$this->generateSql();

		$rows = mysql_query($this->sql);
		$row = array();
		$resAry = array();
		while ($row = mysql_fetch_array($rows, MYSQL_ASSOC)) {
			$resAry[] = $row;
			unset($row);
		}
		mysql_free_result($rows);
		return $resAry;
	}

	public function insert($data = array()) {
		$sql = 'INSERT INTO `'.$this->tableName.'`';
		$colstr = $valstr = '';
		foreach ($data as $key => $val) {
			$colstr .= '`'.$key.'`,';
			$valstr .= '\''.$val.'\',';
		}
		$colstr = rtrim($colstr, ',');
		$valstr = rtrim($valstr, ',');
		$sql .= '('.$colstr.') VALUES ('.$valstr.')';
		if (mysql_query($sql)) {
			return $this->getLastInsertId();
		} else {
			return false;
		}
	}

	/**
	 * [update description]
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public function update($data = array()) {
		if (!empty($data)) {
			$this->queryType = 'u';
			$set = '';
			foreach ($data as $key => $val) {
				$set .= '`'.$key.'`='.$val.',';
			}
			$this->set = rtrim($set, ',');
			$this->generateSql();
			return mysql_query($this->sql);
		}
	}

	public function delete() {
		$this->queryType = 'd';
		$this->generateSql();
		return mysql_query($this->sql);
	}

	public function join($str) {
		if ($str) {
			$this->join .= ' '.$str;
		}
		return $this;
	}

	public function fields($str) {
		if ($str) {
			$this->fields .= $str;
		} else {
			$this->fields .= ' * ';
		}
		
		return $this;
	}

	public function where($str) {
		if ($str) {
			$this->where = ' WHERE '.$str;
		}
		return $this;
	}

	public function table($str) {
		$this->tableName = $str;
		return $this;
	}

	public function alias($str) {
		if ($str) {
			$this->alias = ' AS '.$str;
		}
		return $this;
	}

	public function group($str) {
		if ($str) {
			$this->group = ' GROUP BY '.$str;
		}
		return $this;
	}

	public function limit($str) {
		$this->limit = $str;
	}

	public function order($str) {
		$this->order = $str;
	}

	private function getLastInsertId() {
		return mysql_query("select last_insert_id()");
	}

	private function generateSql() {
		switch ($this->queryType) {
			case 's':
				$this->sql = 'SELECT '.$this->fields.' FROM '.$this->tableName.$this->alias.' '.$this->join.$this->where.$this->group.$this->order.$this->limit;
				break;

			case 'u':
				$this->sql = 'UPDATE '.$this->tableName.' SET '.$this->set.$this->where.$this->order.$this->limit;
				break;

			case 'd':
				$this->sql = 'DELETE FROM `'.$this->tableName.'`'.$this->where;
				break;

			default:
				# code...
				break;
		}
	}
}