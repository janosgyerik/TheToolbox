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

	class FileViewerEditor extends PluginBase {
		private $controller;
		
		public function setup() {
			$preview = $this->getSetting('enable_file_preview', TRUE);
			$view = $this->getSetting('enable_file_view', TRUE);
			$edit = $this->getSetting('enable_file_edit', TRUE);
			if (!$preview and !$view and !$edit) return;
			
			if ($view)
				$this->addService("view", "FileViewerEditorServices");
			if ($preview)
				$this->addService("preview", "FileViewerEditorServices");
			if ($edit)
				$this->addService("edit", "FileViewerEditorServices");
			
			require_once("FileViewerEditorController.class.php");
			
			$this->controller = new FileViewerEditorController($this, $view, $preview, $edit);
			$this->env->filesystem()->registerItemContextPlugin("fileviewereditor", $this->controller);
		}
		
		public function getController() {
			return $this->controller;
		}
		
		public function __toString() {
			return "FileViewerEditorPlugin";
		}
	}
?>