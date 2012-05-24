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

	class PublicUploader {
		private $env;
		
		public function __construct($env) {
			$this->env = $env;
		}
		
		public function showPage($shareId, $folder) {
			$this->show($shareId, $folder, "upload.php");
		}
		
		public function uploadTo($shareId, $folder) {
			$this->env->filesystem()->uploadTo($folder);
			$this->show($shareId, $folder, "success.php");
		}
		
		private function show($shareId, $folder, $page) {
			global $UPLOAD_URL, $RESOURCE_URL, $FOLDER_NAME;
			$UPLOAD_URL = $this->env->getServiceUrl("public", array($shareId), TRUE);
			$RESOURCE_URL = $this->env->getPluginUrl("Share", "upload/http");
			$FOLDER_NAME = $folder->name();
			include($page);
		}
	}
?>