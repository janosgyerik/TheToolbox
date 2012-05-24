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

	class Html5UploaderServices extends ServicesBase {
		public function isAuthenticationRequired() {
			return TRUE;
		}
		
		protected function isValidPath($method, $path) {
			return count($path) == 1;
		}
				
		public function processPost() {
			$folder = $this->item($this->path[0]);
			if ($folder->isFile()) throw $this->invalidRequestException("Target not a folder");
			
			$this->env->features()->assertFeature("file_upload");
			$this->env->filesystem()->assertRights($folder, Authentication::RIGHTS_WRITE, "upload");
			
			if (count($this->path) == 2) {
				if (!isset($this->request->data["files"])) throw $this->invalidRequestException();
				$this->checkUpload($folder, $this->request->data["files"]);
				return;
			}
			
			$this->uploadTo($folder, $this->env->filesystem()->getUploadTempDir(), $this->env->filesystem());
			$this->response()->html(json_encode(array("result" => TRUE)));
			die();
		}
		
		private function uploadTo($to) {
			//TODO
		}
	}
?>