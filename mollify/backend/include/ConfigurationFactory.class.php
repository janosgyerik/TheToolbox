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
	 
	class ConfigurationFactory {
		public function createConfiguration($id, $settings) {
			if (!$id) throw new ServiceException("INVALID_CONFIGURATION", "No configuration defined");
			
			if (!$id or strcasecmp($id, 'mysql') == 0) {
				require_once("configuration/MySQLConfiguration.class.php");
				return new MySQLConfiguration($settings);
			} else if (strcasecmp($id, 'postgresql') == 0) {
				require_once("configuration/PostgresqlConfiguration.class.php");
				return new PostgresqlConfiguration($settings);
			} else if (strcasecmp($id, 'sqlite') == 0) {
				require_once("configuration/SQLiteConfiguration.class.php");
				return new SQLiteConfiguration($settings);
			} else {
				throw new ServiceException("INVALID_CONFIGURATION", "Unsupported data provider: [".$id."]");
			}
		}
		
		public function __toString() {
			return "ConfigurationFactory";
		}
	}
?>