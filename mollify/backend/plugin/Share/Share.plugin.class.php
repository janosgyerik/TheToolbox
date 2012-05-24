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
	
	require_once("ShareHandler.class.php");
	
	class Share extends PluginBase {
		private $handler;
		
		public function version() {
			return "1_1";
		}

		public function versionHistory() {
			return array("1_0", "1_1");
		}
		
		public function setup() {
			$this->addService("share", "ShareServices");
			$this->addService("public", "PublicShareServices");
			
			$this->handler = new ShareHandler($this->env, $this->getSettings());
			$this->env->events()->register("filesystem/", $this->handler);

			$this->env->filesystem()->registerItemContextPlugin("plugin-share", $this->handler);
		}
				
		public function getHandler() {
			return $this->handler;
		}
				
		public function __toString() {
			return "SharePlugin";
		}
	}
?>
