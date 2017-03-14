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
		$sql = '';
		if ($this->fields) {
			$sql .= 'select '.$this->fields;
		} else {
			$sql .= 'select *';
		}

		if ($this->tableName) {
			$sql .= ' from '.$this->tableName;
		}

		if ($this->alias) {
			$sql .= ' as '.$this->alias;
		}

		if ($this->join) {
			$sql .= $this->join;
		}

		if ($this->where) {
			$sql .= ' where '.$this->where;
		}

		if ($this->group) {
			$sql .= ' group by '.$this->group;
		}

		$rows = mysql_query($sql);
		$row = array();
		$resAry = array();
		while ($row = mysql_fetch_array($rows, MYSQL_ASSOC)) {
			$resAry[] = $row;
			unset($row);
		}
		mysql_free_result($rows);
		return $resAry;
	}

	public function add($data = array()) {
		$sql = 'INSERT INTO `'.$this->tableName.'`';
		$colstr = $valstr = '';
		foreach ($data as $key => $val) {
			$colstr .= '`'.$key.'`,';
			$valstr .= '\''.$val.'\',';
		}
		$colstr = rtrim($colstr, ',');
		$valstr = rtrim($valstr, ',');
		$sql .= '('.$colstr.') VALUES ('.$valstr.')';
		return mysql_query($sql);
	}

	public function join($str) {
		$this->join .= ' '.$str;
		return $this;
	}

	public function fields($str) {
		$this->fields .= $str;
		return $this;
	}

	public function where($str) {
		$this->where .= $str;
		return $this;
	}

	public function table($str) {
		$this->tableName = $str;
		return $this;
	}

	public function alias($str) {
		$this->alias = $str;
		return $this;
	}

	public function group($str) {
		$this->group .= $str;
		return $this;
	}
}