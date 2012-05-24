<?php

	/**
	 * Copyright (c) 2008- Samuli Järvelä
	 *
	 * All rights reserved. This program and the accompanying materials
	 * are made available under the terms of the Eclipse Public License v1.0
	 * which accompanies this distribution, and is available at
	 * http://www.eclipse.org/legal/epl-v10.html. If redistributing this code,
	 * this entire header must remain intact.
	 */

	class MollifySQLiteDatabase {
		private $file;		
		private $db = NULL;
		
		public function __construct($file) {
			Logging::logDebug("SQLite DB: ".$file);
			$this->file = $file;
		}
		
		public function type() {
			return "sqlite";
		}
		
		public function databaseExists() {
			return file_exists($this->file);
		}

		public function file() {
			return $this->file;
		}
		
		public function isConnected() {
			return $this->db != NULL;
		}
		
		public function connect() {
			$db = sqlite_open($this->file, 0666, $error);
			if (!$db) throw new ServiceException("INVALID_CONFIGURATION", "Could not connect to database (file=".$this->file."), error: ".$error);
			$this->db = $db;
		}
		
		public function registerRegex() {
			sqlite_create_function($this->db, 'REGEX', 'sqlite_regex_match', 2);
		}

		public function table($name) {
			return $name;
		}
		
		public function update($query) {
			$result = $this->query($query);
			$affected = $result->affected();
			$result->free();
			return $affected;
		}

		public function query($query) {
			if (Logging::isDebug()) Logging::logDebug("DB: ".$query);
			
			$result = @sqlite_query(str_replace("`", "", $query), $this->db, SQLITE_NUM, $err);
			if (!$result)
				throw new ServiceException("INVALID_CONFIGURATION", "Error executing query (".$query."): ".$err);
			return new Result($this->db, $result);
		}
		
		public function execSqlFile($file) {
			$sql = file_get_contents($file);
			if (!$sql) throw new ServiceException("INVALID_REQUEST", "Error reading sql file (".$file.")");
			$sql = str_replace("{TABLE_PREFIX}", "", str_replace("`", "", $sql));
			$this->queries($sql);
		}

		public function queries($query) {
			if (Logging::isDebug()) Logging::logDebug("DB: ".$query);
			
			@sqlite_query($query, $this->db, SQLITE_NUM, $err);
			if ($err)
				throw new ServiceException("INVALID_CONFIGURATION", "Error executing query (".$query."): ".$err);
			return TRUE;
		}
		
		public function startTransaction() {
			$result = @sqlite_query("BEGIN;", $this->db);
			if (!$result)
				throw new ServiceException("INVALID_CONFIGURATION", "Error starting transaction: ".sqlite_last_error($this->db));
		}

		public function commit() {
			$result = @sqlite_query("COMMIT;", $this->db);
			if (!$result)
				throw new ServiceException("INVALID_CONFIGURATION", "Error committing transaction: ".sqlite_last_error($this->db));
		}
		
		public function rollback() {
			$result = @sqlite_query("ROLLBACK;", $this->db);
			if (!$result)
				throw new ServiceException("INVALID_CONFIGURATION", "Error rollbacking transaction: ".sqlite_last_error($this->db));
		}
		
		public function string($s, $quote = FALSE) {
			if ($s === NULL) return 'NULL';
			$r = sqlite_escape_string($s);
			if ($quote) return "'".$r."'";
			return $r;
		}
		
		public function arrayString($a, $quote = FALSE) {
			$result = '';
			$first = TRUE;
			foreach($a as $s) {
				if (!$first) $result .= ',';
				if ($quote) $result .= "'".$s."'";
				else $result .= $s;
				$first = FALSE;
			}
			return $result;
		}
		
		public function lastId() {
			return sqlite_last_insert_rowid($this->db);
		}
	}
	
	class Result {
		private $db;
		private $result;
		
		public function __construct($db, $result) {
			$this->db = $db;
			$this->result = $result;
		}
		
		public function count() {
			return sqlite_num_rows($this->result);
		}

		public function affected() {
			return sqlite_changes($this->db);
		}
				
		public function rows() {
			$list = array();
			while ($row = sqlite_fetch_array($this->result, SQLITE_ASSOC)) {
				$list[] = $row;
			}
			return $list;
		}

		public function values($col) {
			$list = array();
			while ($row = sqlite_fetch_array($this->result, SQLITE_ASSOC)) {
				$list[] = $row[$col];
			}
			return $list;
		}
		
		public function firstValue($val) {
			$ret = $this->firstRow();
			return $ret[$val];
		}
			
		public function valueMap($keyCol, $valueCol = NULL) {
			$list = array();
			while ($row = sqlite_fetch_array($this->result, SQLITE_ASSOC)) {
				if ($valueCol == NULL)
					$list[$row[$keyCol]] = $row;
				else
					$list[$row[$keyCol]] = $row[$valueCol];
			}
			return $list;
		}
			
		public function firstRow() {
			$ret = sqlite_fetch_array($this->result, SQLITE_ASSOC);
			return $ret;
		}
		
		public function value($i) {
			$ret = sqlite_fetch_array($this->result);
			return $ret[$i];
		}
		
		public function free() {
		}
	}
	
	function sqlite_regex_match($str, $regex) {  
		if (preg_match($regex, $str, $matches)) {  
			return $matches[0];  
		}
		return false;  
	}
?>