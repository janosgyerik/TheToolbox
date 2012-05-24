<?php

	 $PLUGINS = array(
		"FileViewerEditor" => array(
				"viewers" => array(
						"Image" => array("gif", "png", "jpg"),
						"TextFile" => array("txt", "php", "html", "htaccess", "js", "css", "scss")
				),
				"previewers" => array(
						"Image" => array("gif", "png", "jpg")
				),
				"editors" => array(
						"TextFile" => array("txt", "php", "html", "htaccess", "js", "css", "scss")
				),
		)
	);

	if(getenv('MYSQL_DB_NAME')){
		//remote
		$SETTINGS = array(
		"timezone" => "Asia/Tokyo",
		"debug" => FALSE,
		"debug_log" => "/var/fog/apps/20490/toolbox.phpfogapp.com/mollify/backend/debug/debug.log"
		);
		$CONFIGURATION_TYPE = 'mysql';

		$DB_HOST = getenv('MYSQL_DB_HOST');
		$DB_DATABASE = getenv('MYSQL_DB_NAME');
		$DB_USER = getenv('MYSQL_USERNAME');
		$DB_PASSWORD = getenv('MYSQL_PASSWORD');
		$DB_TABLE_PREFIX = "mollify_";

	}else{
		//local
		$SETTINGS = array(
		"timezone" => "Asia/Tokyo",
		"debug" => TRUE,
		"debug_log" => "/Users/Sacha/Sites/toolbox/mollify/backend/debug/debug.log"
		);

		$CONFIGURATION_TYPE = "mysql";
		$DB_HOST = "127.0.0.1";
		$DB_DATABASE = "toolbox";
		$DB_USER = "root";
		$DB_PASSWORD = "duralexsedlex";
		$DB_TABLE_PREFIX = "mollify_";
	}
?>