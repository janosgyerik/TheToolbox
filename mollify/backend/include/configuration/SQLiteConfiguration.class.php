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
	 
	require_once("DbConfiguration.class.php");

	class SQLiteConfiguration extends DbConfiguration {
		const VERSION = "1_8_8";
		
		public function __construct($settings) {
			global $DB_FILE;
			
			if (!isset($DB_FILE)) throw new ServiceException("INVALID_CONFIGURATION", "No database information defined");
						
			require_once("include/sqlite/SQLiteDatabase.class.php");
			
			$this->db = new MollifySQLiteDatabase($DB_FILE);
			$this->db->connect();
			$this->db->registerRegex();
		}
		
		public function getType() {
			return "sqlite";
		}
	}
?>