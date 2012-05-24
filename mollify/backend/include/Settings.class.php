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

	class Settings {
		private $settings = array();
		
		private static $VALUES = array(
			"email_login" => FALSE,
			"host_public_address" => NULL,
			"session_name" => NULL,
			"session_time" => 7200,
			"timezone" => NULL,
			"enable_limited_http_methods" => FALSE,
			"enable_file_upload" => TRUE,
			"enable_folder_actions" => TRUE,
			"enable_file_upload_progress" => FALSE,
			"enable_zip_download" => FALSE,
			"zipper" => "ZipArchive",
			"enable_change_password" => TRUE,
			"enable_descriptions" => FALSE,
			"enable_mail_notification" => FALSE,
			"enable_retrieve_url" => FALSE,
			"allowed_file_upload_types" => array(),
			"firebug_logging" => FALSE,
			"mail_notification_from" => "Admin",
			"new_folder_permission_mask" => 0755,
			"convert_filenames" => FALSE,
			"support_output_buffer" => FALSE,
			"mail_notificator_class" => "mail/MailNotificator.class.php",
			"url_retriever_class" => "UrlRetriever.class.php",
			"datetime_format" => "d.m.Y H:i:s",
			"mime_types" => array(),
			"authentication_methods" => array("pw"),
			"ldap_server" => NULL,
			"ldap_conn_string" => NULL,
			"upload_temp_dir" => NULL,
			"enable_thumbnails" => TRUE,
			"enable_folder_protection" => FALSE,
			"enable_guest_mode" => FALSE,
			"guest_user_id" => FALSE,
			"debug" => FALSE,
			"debug_log" => NULL
		);
		
		function __construct($settings) {
			$settingsExist = (isset($settings) and $settings != NULL);
			
			foreach(self::$VALUES as $s=>$v) {
				if (!$settingsExist or !array_key_exists($s, $settings)) continue;
				$this->settings[$s] = $settings[$s];
			}
		}

		public function setting($setting, $allowDefaultIfNotDefined = FALSE) {
			if (!$this->hasSetting($setting)) {
				if (!$allowDefaultIfNotDefined) return NULL;
				if (!isset(self::$VALUES[$setting])) throw new ServiceException("Invalid setting: ".$setting);
				return self::$VALUES[$setting];
			}
			return $this->settings[$setting];
		}
		
		public function hasSetting($setting) {
			return array_key_exists($setting, $this->settings);
		}
		
		public function getAllSettings() {
			return $this->settings;
		}
		
		function log() {
			Logging::logDebug("SETTINGS: ".Util::array2str($this->settings));
		}
		
		public function __toString() {
			return "Settings";
		}
	}
?>
