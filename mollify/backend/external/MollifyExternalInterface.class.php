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

	require_once("configuration.php");
	global $SETTINGS, $CONFIGURATION_TYPE;
	if (!isset($CONFIGURATION_TYPE)) die("Mollify not configured");
	
	function MollifyExternalInterface() {
		global $SETTINGS, $CONFIGURATION_TYPE;
		return new MollifyExternalInterface($SETTINGS, $CONFIGURATION_TYPE);
	}

	class VoidResponseHandler {
		public function addListener($l) {}
	}
	
	class MollifyExternalInterface {
		private $configuration;
		private $settings;
		private $authentication;
		private $session;
		
		public function __construct($settingsVar, $configurationId) {
			require_once("include/Settings.class.php");
			require_once("include/session/Session.class.php");
			require_once("include/ServiceEnvironment.class.php");
			require_once("include/Util.class.php");
			require_once("include/ConfigurationFactory.class.php");
			require_once("include/Logging.class.php");
			require_once("include/Version.info.php");
			require_once("include/Cookie.class.php");
			require_once("include/Features.class.php");
			require_once("include/Request.class.php");
		
			Logging::initialize($settingsVar);

			$this->settings = new Settings($settingsVar);
			$this->session = new Session(TRUE);
			
			$factory = new ConfigurationFactory();
			$this->configuration = $factory->createConfiguration($configurationId, $this->settings);

			$env = new ServiceEnvironment($this->session, new VoidResponseHandler(), $this->configuration, $this->settings);
			$env->initialize(new Request(FALSE, TRUE));
			$this->authentication = $env->authentication();
		}

		public function logout() {
			$this->authentication->logout();
			$this->session->end();
		}
				
		public function authenticate($userId) {
			$this->authentication->doAuth($this->getUser($userId));
		}
		
		public function isAuthenticated() {
			return $this->authentication->isAuthenticated();
		} 
		
		public function getUserId() {
			return $this->session->userId();
		}

		public function getUsername() {
			return $this->session->username();
		}

		public function isAuthenticationRequired() { 
			return $this->authentication->isAuthenticationRequired(); 
		} 

		public function getAllUsers() {
			return $this->configuration->getAllUsers();
		}
		
		public function getUser($id) {
			return $this->configuration->getUser($id);
		}
		
		public function addUser($name, $pw, $email, $defaultPermission = "RO") {
			return $this->configuration->addUser($name, $pw, $email, $defaultPermission);
		}
		
		public function removeUser($id) {
			return $this->configuration->removeUser($id);
		}
		
		public function addFolder($name, $path) {
			return $this->configuration->addFolder($name, $path);
		}
		
		public function addUserFolder($userId, $folderId, $name = NULL) {
			return $this->configuration->addUserFolder($userId, $folderId, $name);
		}
		
		public function addItemPermission($id, $permission, $userId) {
			return $this->configuration->addItemPermission($id, $permission, $userId);
		}
	}
?>