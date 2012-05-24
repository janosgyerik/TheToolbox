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

	class MySQLConfiguration extends DbConfiguration {
		const VERSION = "1_8_8";
		
		public function __construct($settings) {
			global $DB_HOST, $DB_USER, $DB_PASSWORD, $DB_DATABASE, $DB_TABLE_PREFIX, $DB_CHARSET, $DB_SOCKET, $DB_PORT;
			
			if (!isset($DB_USER) or !isset($DB_PASSWORD)) throw new ServiceException("INVALID_CONFIGURATION", "No database information defined");
			
			if (isset($DB_HOST)) $host = $DB_HOST;
			else $host = "localhost";
			
			if (isset($DB_DATABASE)) $database = $DB_DATABASE;
			else $database = "mollify";

			if (isset($DB_TABLE_PREFIX)) $tablePrefix = $DB_TABLE_PREFIX;
			else $tablePrefix = "";
			
			if (isset($DB_PORT)) $port = $DB_PORT;
			else $port = NULL;

			if (isset($DB_SOCKET)) {
				$socket = $DB_SOCKET;
				$port = NULL;
			} else {
				$socket = NULL;
			}
			
			require_once("include/mysql/MySQLDatabase.class.php");
			$this->db = new MySQLDatabase($host, $DB_USER, $DB_PASSWORD, $database, $tablePrefix, $port, $socket);
			
			$this->db->connect();
			if (isset($DB_CHARSET)) $this->db->setCharset($DB_CHARSET);
		}
		
		public function getType() {
			return "mysql";
		}
	}
?>