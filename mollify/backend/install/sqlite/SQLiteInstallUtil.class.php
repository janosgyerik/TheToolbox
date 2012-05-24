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

	class SQLiteInstallUtil {
		private $db;
		
		public function __construct($db) {
			$this->db = $db;
		}
		
		public function db() {
			return $this->db;
		}
		
		public function execCreateTables() {
			$this->db->execSqlFile("include/sqlite/sql/create_tables.sql");
		}
		
		public function execInsertParams() {
			$this->db->execSqlFile("include/sqlite/sql/params.sql");
		}
		
		public function createAdminUser($name, $pw) {
			$a1pw = md5($name.":mollify:".$pw);	//TODO
			$this->db->query("INSERT INTO ".$this->db->table("user")." (name, password, a1password, permission_mode, is_group) VALUES ('".$this->db->string($name)."','".md5($pw)."','".$a1pw."','".Authentication::PERMISSION_VALUE_ADMIN."', 0)", FALSE);
		}
		
		public function updateVersionStep($from, $to) {
			$file = "include/sqlite/sql/".$from."-".$to.".sql";
			$this->db->execSqlFile($file);
		}

		public function execPluginCreateTables($id) {
			$this->db->execSqlFile("plugin/".$id."/sqlite/install.sql");
		}
		
		public function updatePluginVersionStep($id, $from, $to) {
			$file = "plugin/".$id."/sqlite/".$from."-".$to.".sql";
			$this->db->execSqlFile($file);
		}
	}
?>