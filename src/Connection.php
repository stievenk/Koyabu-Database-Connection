<?php
namespace Koyabu\DatabaseConnection;

class Connection {
    public $conn;
	public $error;
	public $config;
	
	public function __construct($config) {
		$this->config = $config;
		$this->conn = new \mysqli($config['host'],$config['user'],$config['pass'],$config['data']);
		if ($this->conn->connect_error) {
			echo '<h1>Server Currently Maintenance</h1>';
			echo "Mohon kembali lagi 2 menit dari sekarang";
			echo '<script>setTimeout(function(){ window.location.reload(); },10000);</script>';
			die('Connect Error (' . $this->conn->connect_errno . ') ' . $this->conn->connect_error);
		}
	}
	
	public function table_exists($table) {
		$g = $this->query("SELECT COUNT(*) 
		FROM information_schema.tables
		WHERE table_schema = '{$this->config['data']}' 
			AND table_name = '{$table}'");
		$t = $this->fetch_row($g);
		return $t[0];
	}
	
	public function FieldTypeData($num) {
		if ($num >= 250) { return 'TEXT'; }
		if ($num >= 240 AND $num < 250) { return 'DECIMAL'; }
		if ($num == 10) { return 'DATE'; }
		if ($num == 11) { return 'TIME'; }
		if ($num == 12) { return 'DATETIME'; }
		if ($num == 13) { return 'YEAR'; }
		if ($num == 7) { return 'TIMESTAMP'; }
		if ($num == 1) { return 'BOOL'; }
		if ($num == 2) { return 'SMALLINT'; }
		if ($num == 3) { return 'INTEGER'; }
		if ($num == 4) { return 'FLOAT'; }
		if ($num == 5) { return 'DOUBLE'; }
		if ($num == 16) { return 'BIT'; }
		if ($num == 8) { return 'BIGINT'; }
		if ($num == 9) { return 'MEDIUMINT'; }
	}
	
	public function error($die=0) {
		$logfile = 'logs/error.log';
		$odata = @file_get_contents($logfile);
		$this->error = $this->conn->error;
		if ($this->error) {
			@file_put_contents($logfile,$odata."\r\n[".date("Y-m-d H:i:s")."] {$this->error}");
		}
		if ($die) { die("Error: {$this->error}"); } 
		else {
			return $this->error;
		}
	}
	
	public function query($query) {
		if ($query) {
			$qry = $this->conn->query($query);
			if ($qry) { return $qry; } else {
				//echo $this->conn->error;
				return false;
			}
		} else { $this->error='No-Query'; echo 'No-Query'; return false; }
	}
	
	public function insert_id() {
		return $this->conn->insert_id;
	}
	
	public function fetch_assoc($result) {
		return $result->fetch_assoc();
	}
	
	public function fetch_array($result) {
		return $result->fetch_array();
	}

	public function fetch_row($result) {
		return $result->fetch_row();
	}
	
	public function fetch_field($result) {
		return $result->fetch_field();
	}
	
	public function fetch_fields($result) {
		return $result->fetch_fields();
	}
	
	public function num_rows($result) {
		return $result->num_rows;
	}
	
	public function fetch_length($result) {
		return $result->length;
	}
	
	public function escape_string($string) {
		return $this->conn->real_escape_string($string);
	}
	
	public function field_name($SQL) {
		$result = $this->query($SQL);
		while($r = $this->fetch_field($result)) {
			$s = $this->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='{$r->db}' AND TABLE_NAME = '{$r->table}' AND COLUMN_NAME = '{$r->name}' limit 1");
			$t = $this->fetch_assoc($s);
			$f[]= array( 'name' => $r->name, 
						'table' => $r->table,
						'db' => $r->db,
						'data_type' => $t['DATA_TYPE'],
						'col_type' => $t['COLUMN_TYPE'],
						'type_code' => $r->type,
						'char_length' => $t['CHARACTER_MAXIMUM_LENGTH'],
						'length' => $r->length);
		}
		return $f;
	}
	
	public function array_sql_field($var,$t = 'sql') {
		foreach ($var as $k => $v) {
			if ($t == 'sql') {
				$f[] = "`$v` = ''";
			} else if ($t == 'array') {
				$f[] = "\r\n'$v' => '' ";
			}
		}
		
		$V = implode(", ",$f);
		return $V;
	}
	
	public function tablelog($table,$data) {
		return $this->tablelogs($table,$data);
	}
	
	public function tablelogs($table,$data) {
		$this->query("insert into z_table_log set nama_table='drawer', data='". $this->escape_string(json_encode($data)) ."', tanggal=now()");
		$id = $this->insert_id();
		return $id;
	}
	
	public function pages($table,$page=1,$limit=10,$where = '') {
		$page =  $page ? $page : 1;
		$g = $this->query("select count(*) from `{$table}` where 1 {$where}");
		$t = $this->fetch_row($g);
		$record = $t[0];
		$limit = $limit <= 0 ? 10 : $limit;
		$allpage = ceil($record / $limit);
		$allpage = $allpage < 1 ? 1 : $allpage;
		$page = $page > $allpage ? $allpage : $page;
		$page = $page < 1 ? 1 : $page;
		$start = ($page - 1) * $limit;
		$start = $start < 0 ? 0 : $start;
		return array('page' => $page, 'start' => $start, 'totalpage' => $allpage, 'record' => $record, 'limit' => $limit);
	}
}
?>