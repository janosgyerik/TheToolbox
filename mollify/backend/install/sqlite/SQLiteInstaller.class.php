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

	require_once("install/MollifyInstallProcessor.class.php");
	require_once("include/ServiceEnvironment.class.php");
	require_once("include/mysql/DatabaseUtil.class.php");
	require_once("install/sqlite/SQLiteInstallUtil.class.php");
	
	class SQLiteInstaller {
		protected $processor;
		private $configured;
		protected $db;

		public function __construct($settings, $type = "install") {
			$this->processor = new MollifyInstallProcessor($type, "sqlite", $settings);
			
			global $DB_FILE;
			$this->configured = isset($DB_FILE);
			$this->db = $this->createDB($DB_FILE);
			$this->dbUtil = new DatabaseUtil($this->db);
		}
		
		public function processor() {
			return $this->processor;
		}
		
		public function onError($e) {
			$this->processor->onError($e);
		}

		private function createDB($file) {			
			require_once("include/sqlite/SQLiteDatabase.class.php");
			return new MollifySQLiteDatabase($file);
		}
		
		public function util() {
			require_once("install/sqlite/SQLiteInstallUtil.class.php");
			return new SQLiteInstallUtil($this->db);
		}
		
		public function isConfigured() {
			return $this->configured;
		}

		public function isInstalled() {
			if (!$this->isConfigured())
				return FALSE;
			
			try {
				if (!$this->db->isConnected()) $this->db->connect();
			} catch (ServiceException $e) {
				return FALSE;
			}
			
			try {
				$ver = $this->dbUtil->installedVersion();
			} catch (ServiceException $e) {
				Logging::logDebug('Mollify not installed');
				return FALSE;
			}

			if ($ver != NULL)
				Logging::logDebug('Mollify installed version: '.$ver);
			else
				Logging::logDebug('Mollify not installed');

			return $ver != NULL;
		}
		
		public function isCurrentVersionInstalled() {
			return ($this->installedVersion() === $this->currentVersion());
		}
		
		public function installedVersion() {
			return $this->dbUtil->installedVersion();
		}
		
		public function pluginInstalledVersion($id) {
			return $this->dbUtil->pluginInstalledVersion($id);
		}

		public function currentVersion() {
			return SQLiteConfiguration::VERSION;
		}
		
		public function db() {
			return $this->db;
		}
		
		public function hasError() {
			return $this->processor->hasError();
		}

		public function hasErrorDetails() {
			return $this->processor->hasErrorDetails();
		}
		
		public function error() {
			return $this->processor->error();
		}

		public function errorDetails() {
			return $this->processor->errorDetails();
		}

		public function data($name = NULL) {
			return $this->processor->data($name);
		}

		public function action() {
			return $this->processor->action();
		}

		public function process() {
			$this->checkSystem();
			$this->checkInstalled();
			$this->checkConfiguration();

			$phase = $this->processor->phase();
			if ($phase == NULL) $phase = 'db';
			Logging::logDebug("Installer phase: [".$phase."]");	
			
			$this->onPhase($phase);
		}
		
		private function checkSystem() {
			if (!function_exists('sqlite_open')) {
				$this->processor->setError("SQLite not detected", "Mollify cannot be installed to this system when SQLite is not available. Check your system configuration or choose different configuration type.");
				$this->processor->showPage("install_error");
			}
		}
		
		private function checkInstalled() {
			if (!$this->isInstalled()) return;
			
			$this->processor->createEnvironment();
			if (!$this->processor->authentication()->isAdmin()) die("Mollify Installer requires administrator user");
			
			$this->processor->showPage("installed");
		}
		
		private function checkConfiguration() {
			if (!$this->isConfigured())
				$this->processor->showPage("configuration");

			try {
				$this->db->connect(FALSE);
			} catch (ServiceException $e) {
				if ($e->type() === 'INVALID_CONFIGURATION') {
					$this->setError("Could not connect to database", '<code>'.$e->details().'</code>');
					$this->showPage("configuration");
					die();
				}
				throw $e;
			}
		}
		
		private function onPhase($phase) {
			$this->processor->setPhase($phase);
			
			switch ($phase) {
				case 'db':
					$this->onPhaseDatabase();
					break;
				case 'admin':
					$this->onPhaseAdmin();
					break;
				case 'success':
					$this->processor->showPage("success");
					break;
				default:
					Logging::logError("Invalid installer phase: ".$phase);
					die();
			}
		}
		
		// PHASES
				
		private function onPhaseDatabase() {
			if ($this->processor->action() === 'continue_db') {
				$this->processor->clearAction();
				
				if (!$this->db->databaseExists()) {
					try {
						$this->dbUtil->createDatabase();
					} catch (ServiceException $e) {
						$this->processor->setError("Unable to create database", '<code>'.$e->details().'</code>');
						$this->onPhase('db');
					}
				}
				
				$this->onPhase('admin');
			}
			
			$this->processor->showPage("database");
		}
		
		private function onPhaseAdmin() {
			if ($this->processor->action() === 'install')
				$this->install();
			$this->processor->showPage("admin");
		}
		
		private function install() {
			$this->db->startTransaction();
			
			try {
				$this->util()->execCreateTables();
				$this->util()->execInsertParams();
			} catch (ServiceException $e) {
				$this->processor->setError("Could not install", '<code>'.$e->details().'</code>');
				$this->processor->showPage("install_error");
			}

			try {
				$this->util()->createAdminUser($this->data("name"), $this->data("password"));
			} catch (ServiceException $e) {
				$this->processor->setError("Could not create admin user", '<code>'.$e->details().'</code>');
				$this->processor->showPage("install_error");
			}
			
			try {
				$this->db->commit();
			} catch (ServiceException $e) {
				$this->processor->setError("Could not install", '<code>'.$e->details().'</code>');
				$this->processor->showPage("install_error");
			}
			
			$this->onPhase('success');
		}
	}
?>