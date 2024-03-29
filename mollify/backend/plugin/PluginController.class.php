<?php

	/**
	 * Copyright (c) 2008- Samuli J�rvel�
	 *
	 * All rights reserved. This program and the accompanying materials
	 * are made available under the terms of the Eclipse Public License v1.0
	 * which accompanies this distribution, and is available at
	 * http://www.eclipse.org/legal/epl-v10.html. If redistributing this code,
	 * this entire header must remain intact.
	 */
	 
	require_once("PluginBase.class.php");

	class PluginController {
		protected $env;
		protected $plugins;
		
		public function __construct($env) {
			$this->env = $env;
			$this->plugins = array();
		}
		
		public function setup() {
			global $PLUGINS;
			if (!isset($PLUGINS) or !is_array($PLUGINS)) return;
			
			foreach($PLUGINS as $p => $settings)
				$this->addPlugin($p, $settings);
			
			foreach($this->plugins as $id => $p)
				$p->setup();
		}
		
		private function addPlugin($id, $settings) {
			$cls = $id."/".$id.".plugin.class.php";
			$path = dirname(__FILE__).DIRECTORY_SEPARATOR.$cls;
			if (!file_exists($path)) throw new ServiceException("INVALID_CONFIGURATION", "Plugin not found: ".$id);
			
			require_once($cls);
			$p = new $id($this->env, $id, $settings);
			$this->plugins[$id] = $p;
		}
		
		public function getPlugins() {
			return $this->plugins;
		}

		public function getPlugin($id) {
			return $this->plugins[$id];
		}
		
		public function hasPlugin($id) {
			return isset($this->plugins[$id]);
		}
		
		public function getSessionInfo() {
			$result = array();
			foreach($this->plugins as $id => $p) {
				$info = $p->getSessionInfo();
				$info["admin"] = $p->hasAdminView();

				$clientPlugin = $p->getClientPlugin();
				if ($clientPlugin != NULL)
					$info["client_plugin"] = $this->env->getPluginUrl($id, $clientPlugin, TRUE);
				
				$result[$id] = $info;
			}
			return $result;
		}
		
		public function initialize() {
			foreach($this->plugins as $id => $p)
				$p->initialize();
		}
		
		public function __toString() {
			return "PluginController";
		}
	}
?>
