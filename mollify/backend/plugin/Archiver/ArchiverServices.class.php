<?php

	/**
	 * Copyright (c) 2008- Samuli JŠrvelŠ
	 *
	 * All rights reserved. This program and the accompanying materials
	 * are made available under the terms of the Eclipse Public License v1.0
	 * which accompanies this distribution, and is available at
	 * http://www.eclipse.org/legal/epl-v10.html. If redistributing this code,
	 * this entire header must remain intact.
	 */

	class ArchiverServices extends ServicesBase {
		protected function isValidPath($method, $path) {
			return count($path) == 2;
		}
		
		public function isAuthenticationRequired() {
			return TRUE;
		}
		
		public function processPost() {
			$itemId = $this->path[0];
			$action = $this->path[1];
			if ($action !== 'extract') throw $this->invalidRequestException();
			$data = $this->request->data;
			
			$overwrite = isset($data['overwrite']) ? $data['overwrite'] : FALSE;
			$archive = $this->item($itemId);
			$parent = $archive->parent();
			$name = str_replace(".", "_", basename($archive->internalPath()));
			$target = $parent->internalPath().DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR;
			
			if (file_exists($target)) {
				if (!$overwrite)
					throw new ServiceException("DIR_ALREADY_EXISTS", $target);
				$parent->folderWithName($name)->delete();
			}
			
			mkdir($target);
			
			require_once("ArchiveManager.class.php");
			$a = new ArchiveManager();
			$a->extract($archive->internalPath(), $target);

			$this->response()->success(array());
		}
						
		public function __toString() {
			return "ArchiverServices";
		}
	}
?>