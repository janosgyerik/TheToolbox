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

	class FileViewerEditorController {
		private $plugin;
		private $previewers = array();
		private $viewers = array();
		private $editors = array();
		
		private $viewEnabled;
		private $previewEnabled;
		private $editEnabled;
		
		public function __construct($plugin, $view, $preview, $edit) {
			$this->plugin = $plugin;
			$this->viewEnabled = $view;
			$this->previewEnabled = $preview;
			$this->editEnabled = $edit;
			
			if ($this->viewEnabled) {
				$viewers = $this->getSetting("viewers");
				if ($viewers != NULL and is_array($viewers)) {
					foreach($viewers as $t => $list)
						$this->registerViewer($list, $t);
				}
				$this->plugin->env()->features()->addFeature("file_view");
			}
			
			if ($this->previewEnabled) {
				$previewers = $this->getSetting("previewers");
				if ($previewers != NULL and is_array($previewers)) {
					foreach($previewers as $t => $list)
						$this->registerPreviewer($list, $t);
				}
				$this->plugin->env()->features()->addFeature("file_preview");
			}
			
			if ($this->editEnabled) {
				$editors = $this->getSetting("editors");
				if ($editors != NULL and is_array($editors)) {
					foreach($editors as $t => $list)
						$this->registerEditor($list, $t);
				}
				$this->plugin->env()->features()->addFeature("file_edit");
			}
		}

		private function registerPreviewer($types, $cls) {
			foreach($types as $t)
				$this->previewers[$t] = $cls;
		}
				
		private function registerViewer($types, $cls) {
			foreach($types as $t)
				$this->viewers[$t] = $cls;
		}

		private function registerEditor($types, $cls) {
			foreach($types as $t)
				$this->editors[$t] = $cls;
		}
		
		public function getItemContextData($item, $details, $key, $data) {
			if (!$item->isFile()) return FALSE;
			$type = strtolower($item->extension());
			
			$result = array();
			if ($this->previewEnabled and $this->isPreviewAllowed($type)) {
				$previewer = $this->getPreviewer($type);
				$result["preview"] = $previewer->getUrl($item);
			}
			if ($this->viewEnabled and $this->isViewAllowed($type)) {
				$viewer = $this->getViewer($type);
				$result["view"] = $viewer->getInfo($item);
			}
			if ($this->editEnabled and $this->isEditAllowed($type)) {
				$editor = $this->getEditor($type);
				$result["edit"] = $editor->getInfo($item);
			}
			return $result;
		}

		private function isPreviewAllowed($type) {
			return array_key_exists($type, $this->previewers);
		}
				
		private function isViewAllowed($type) {
			return array_key_exists($type, $this->viewers);
		}

		private function isEditAllowed($type) {
			return array_key_exists($type, $this->editors);
		}
				
		private function getPreviewer($type) {
			$id = $this->previewers[$type];
			
			require_once("previewers/PreviewerBase.class.php");
			require_once("previewers/".$id."/".$id.".previewer.php");
			
			$cls = $id."Previewer";
			return new $cls($this, $id);
		}
				
		private function getViewer($type) {
			$id = $this->viewers[$type];
			
			require_once("viewers/ViewerBase.class.php");
			require_once("viewers/FullDocumentViewer.class.php");
			require_once("viewers/EmbeddedContentViewer.class.php");
			require_once("viewers/".$id."/".$id.".viewer.php");
			
			$cls = $id."Viewer";
			return new $cls($this, $id);
		}

		private function getEditor($type) {
			$id = $this->editors[$type];
			
			require_once("editors/EditorBase.class.php");
			require_once("editors/FullEditor.class.php");
			require_once("editors/".$id."/".$id.".editor.php");
			
			$cls = $id."Editor";
			return new $cls($this, $id);
		}
			
		public function getPreview($item) {
			$type = strtolower($item->extension());
			$previewer = $this->getPreviewer($type);
			return $previewer->getPreview($item);
		}
		
		public function processDataRequest($item, $path) {
			$type = strtolower($item->extension());
			$viewer = $this->getViewer($type);
			$viewer->processDataRequest($item, $path);
		}

		public function processEditRequest($item, $path) {
			$type = strtolower($item->extension());
			$editor = $this->getEditor($type);
			$editor->processRequest($item, $path);
		}
				
		public function getContentUrl($item) {
			return $this->getServiceUrl("view", array($this->getUrlId($item->id()), "content"), TRUE);
		}

		public function response() {
			return $this->plugin->env()->response();
		}

		public function request() {
			return $this->plugin->env()->request();
		}
		
		public function getViewServiceUrl($item, $p, $fullUrl = FALSE) {
			$path = array($this->getUrlId($item->id()));
			if ($p != NULL) $path = array_merge($path, $p);
			return $this->getServiceUrl("view", $path, $fullUrl);
		}

		public function getEditServiceUrl($item, $p, $fullUrl = FALSE) {
			$path = array($this->getUrlId($item->id()));
			if ($p != NULL) $path = array_merge($path, $p);
			return $this->getServiceUrl("edit", $path, $fullUrl);
		}

		public function getServiceUrl($id, $path, $fullUrl = FALSE, $session = TRUE) {
			$url = $this->plugin->env()->getServiceUrl($id, $path, $fullUrl);
			
			if ($session) {
				$url .= (strpos($url, '?') === FALSE) ? "?" : "&";
				if ($this->plugin->env()->session()->isActive()) {
					$s = $this->plugin->env()->session()->getSessionInfo();
					
					$url .= 'session='.$s["session_id"];
				} else {
					$url .= 'nosession=1';
				}
			}
			return $url;
		}

		public function getViewerResourceUrl($id) {
			return $this->plugin->env()->getPluginUrl($this->plugin->id(), "viewers/".$id."/resources");
		}

		public function getEditorResourceUrl($id) {
			return $this->plugin->env()->getPluginUrl($this->plugin->id(), "editors/".$id."/resources");
		}

		public function getCommonResourcesUrl() {
			return $this->plugin->env()->getCommonResourcesUrl();
		}
		
		private function splitTypes($list) {
			$result = array();
			foreach (explode(",", $list) as $t)
				$result[] = strtolower(trim($t));
			return $result;
		}

		public function getViewerSettings($viewerId) {
			$s = $this->plugin->getSettings();
			if (!isset($s[$viewerId])) return array();
			return $s[$viewerId];
		}

		public function getEditorSettings($editorId) {
			$s = $this->plugin->getSettings();
			if (!isset($s[$editorId])) return array();
			return $s[$editorId];
		}
		
		public function getUrlId($id) {
			return strtr($id, '+/=', '-_,');
		}

		private function getSetting($name) {
			return $this->plugin->getSetting($name, NULL);
		}

		public function __toString() {
			return "FileViewerEditorController";
		}
	}
?>