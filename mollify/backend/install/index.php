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

	$MAIN_PAGE = "install";
	$installer = NULL;
	
	set_include_path(realpath('../').PATH_SEPARATOR.get_include_path());
	require_once("MollifyInstallProcessor.class.php");
	require_once("install/DefaultInstaller.class.php");

	chdir("..");
	if (!file_exists("configuration.php")) {
		$installer = new DefaultInstaller("instructions_configuration_create");
	} else {
		@include("configuration.php");
		global $SETTINGS, $CONFIGURATION_TYPE;
		if (!isset($CONFIGURATION_TYPE) or !isValidConfigurationType($CONFIGURATION_TYPE))
			$installer = new DefaultInstaller("instructions_configuration_type");
	}
	
	if (!$installer)
		$installer = createInstaller($SETTINGS, $CONFIGURATION_TYPE);

	try {
		$installer->process();
	} catch (Exception $e) {
		$installer->onError($e);
	}
	
	function createInstaller($settings, $type) {
		switch (strtolower($type)) {
			case 'mysql':
				require_once("install/mysql/MySQLInstaller.class.php");
				return new MySQLInstaller($settings);
			case 'sqlite':
				require_once("install/sqlite/SQLiteInstaller.class.php");
				return new SQLiteInstaller($settings);
			default:
				die("Invalid configuration type");
		}
	}
	
	function isValidConfigurationType($type) {
		return in_array(strtolower($type), array("mysql","sqlite"));
	}
?>