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

	class Cookie {
		private $settings = NULL;
		
		function __construct($settings) {
			$this->settings = $settings;
		}
		
		function add($name, $val, $expire = NULL) {
			setcookie($this->getName($name), $val, $expire, "/");
		}
		
		function get($name) {
			return $_COOKIE[$this->getName($name)];
		}
		
		function remove($name) {
			$this->add($name, "", time()-42000);
		}
		
		function exists($name) {
			return isset($_COOKIE[$this->getName($name)]);
		}
		
		private function getName($n) {
			$id = $this->settings ? $this->settings->setting("session_name") : FALSE;
			if (!$id) $id = "app";
			return "mollify_".$id."_".$n;
		}
	}
?>