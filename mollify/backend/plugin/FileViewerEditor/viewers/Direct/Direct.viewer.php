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
	 
	 class DirectViewer extends ViewerBase {		
		protected function getEmbeddedSize() {
			return array("450", "150");
		}
		
		public function getInfo($item) {
			return array(
				"embedded" => $this->getDataUrl($item, "embedded"),
				"full" => $this->getContentUrl($item)
			);
		}
		
		public function processDataRequest($item, $path) {
			if (count($path) != 1 and $path[0] != 'embedded') throw $this->invalidRequestException();
			
			$html = '<iframe id="direct-viewer" src="'.$this->getContentUrl($item).'" style="border: none;"></iframe>';			
			$this->response()->success(array(
				"html" => $html,
				"resized_element_id" => "direct-viewer",
				"size" => "600;400"
			));
		}
	}
?>