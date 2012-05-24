<?php

	/**
	 * Copyright (c) 2008- Samuli JŠrvelŠ
	 *
	 * All rights reserved. This program and the accompanying materials
	 * are made available under the terms of the Eclipse Public License v1.0
	 * which accompanies this distribution, and is available at
	 * http://www.eclipse.org/legal/epl-v10.html. If redistributing this code,
	 * this entire header must remain intact.
	 */

	$MAIN_PAGE = "update";
	$installer = NULL;
	
	set_include_path(realpath('../').PATH_SEPARATOR.get_include_path());
	chdir("..");

	if (!file_exists("configuration.php")) die();
	require("configuration.php");
	global $SETTINGS, $CONFIGURATION_TYPE;

	try {
		$installer = createUpdater($CONFIGURATION_TYPE, $SETTINGS);
	} catch (Exception $e) {
		showError($e);
		die();
	} 
	try {
		$installer->process();
	} catch (Exception $e) {
		$installer->onError($e);
		showError($e);
	}
		
	function createUpdater($type, $settings) {
		if (!isset($type) or !isValidConfigurationType($type)) die();
		
		require_once("update/UpdateController.class.php");
		switch (strtolower($type)) {
			case 'mysql':
				require_once("update/mysql/MySQLUpdater.class.php");
				return new UpdateController(new MySQLUpdater($settings));
			case 'sqlite':
				require_once("update/sqlite/SQLiteUpdater.class.php");
				return new UpdateController(new SQLiteUpdater($settings));
			default:
				die("Unsupported updater type: ".$type);
		}
	}
	
	function isValidConfigurationType($type) {
		return in_array(strtolower($type), array("mysql","sqlite"));
	}
	
	function showError($e) {
		$c = get_class($e);
		if ($c === "ServiceException") {
			echo "Mollify error (".$e->type()."): ".$e->details();
		} else {
			echo "Unknown error (".$c."): ".$e->getMessage();
		}
	}
?>