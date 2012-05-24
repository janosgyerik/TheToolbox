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

	class FilesystemServices extends ServicesBase {
		protected function isValidPath($method, $path) {
			if (count($path) < 1 or count($path) > 3)
				return FALSE;
			return TRUE;
		}
		
		public function processGet() {
			if ($this->path[0] === 'upload') {
				$this->processGetUpload();
				return;
			}
			
			if ($this->path[0] === 'zip' and count($this->path) == 2) {		
				$mobile = ($this->env->request()->hasParam("m") and strcmp($this->env->request()->param("m"), "1") == 0);
				$id = $this->path[1];
				$this->env->filesystem()->downloadStoredZip($id, $mobile);
				return;
			}

			$item = $this->item($this->path[0]);
			if ($item->isFile())
				$this->processGetFile($item);
			else
				$this->processGetFolder($item);
		}

		public function processPut() {
			if ($this->path[0] === 'permissions') {
				$this->env->authentication()->assertAdmin();
				$this->response()->success($this->env->configuration()->updateItemPermissions($this->request->data));
				return;
			}
			
			$item = $this->item($this->path[0]);
			if ($item->isFile())
				$this->processPutFile($item);
			else
				$this->processPutFolder($item);
		}
		
		public function processPost() {
			if ($this->path[0] === 'items') {
				$this->processMultiItemAction();
				return;
			}
			if ($this->path[0] === 'search') {
				$data = $this->request->data;
				if (!isset($data['text'])) throw $this->invalidRequestException();
				
				$this->response()->success($this->env->filesystem()->search(NULL, $data['text']));
				return;
			}

			$item = $this->item($this->path[0]);
			if ($item->isFile())
				$this->processPostFile($item);
			else
				$this->processPostFolder($item);
		}
		
		public function processDelete() {
			if (count($this->path) == 1) {
				$this->env->filesystem()->delete($this->item($this->path[0]));
				$this->response()->success(TRUE);
				return;
			}
			if (count($this->path) == 2 and $this->path[1] === 'description') {
				$this->env->filesystem()->removeDescription($this->item($this->path[0]));
				$this->response()->success(TRUE);
				return;
			}
			
			throw $this->invalidRequestException();
		}
				
		private function processMultiItemAction() {
			if (count($this->path) != 1) throw invalidRequestException();
			$data = $this->request->data;
			if (!isset($data['action']) or !isset($data['items']) or count($data['items']) < 1) throw $this->invalidRequestException();

			$items = array();
			foreach($data['items'] as $id)
				$items[] = $this->item($id);
			
			switch($data['action']) {
				case 'copy':
					if (!isset($data['to'])) throw $this->invalidRequestException();
					$this->env->filesystem()->copyItems($items, $this->item($data['to']));
					$this->response()->success(TRUE);
					return;
				case 'move':
					if (!isset($data['to'])) throw $this->invalidRequestException();
					$this->env->filesystem()->moveItems($items, $this->item($data['to']));
					$this->response()->success(TRUE);
					return;
				case 'delete':
					$this->env->filesystem()->deleteItems($items);
					$this->response()->success(TRUE);
					return;
				case 'zip':
					$itemIds = $data['items'];
					if (count($itemIds) < 1) throw $this->invalidRequestException();
				
					$items = array();
					foreach($itemIds as $id)
						$items[] = $this->item($id);

					$zipId = $this->env->filesystem()->storeZip($items);
					$this->response()->success(array("id" => $zipId));
					return;
				default:
					throw $this->invalidRequestException();
			}
		}
				
		private function processGetFile($item) {
			if (count($this->path) == 1) {
				$mobile = ($this->env->request()->hasParam("m") and strcmp($this->env->request()->param("m"), "1") == 0);
				if (isset($_SERVER['HTTP_RANGE'])) {
					$this->env->filesystem()->download($item, $mobile, $_SERVER['HTTP_RANGE']);
				} else {
					$this->env->filesystem()->download($item, $mobile);
				}
				return;
			}
						
			switch (strtolower($this->path[1])) {
				case 'thumbnail':
					if (!$item->isFile()) throw $this->invalidRequestException();
					if (!in_array(strtolower($item->extension()), array("gif", "png", "jpg", "jpeg"))) throw $this->invalidRequestException();

					if ($this->env->settings()->setting("enable_thumbnails", TRUE)) {
						require_once("include/Thumbnail.class.php");
						$maxWidth = 200;
						$maxHeight = 200;
						if ($this->env->request()->hasParam("mw") and $this->env->request()->hasParam("mh")) {
							$maxWidth = intval($this->env->request()->param("mw"));
							$maxHeight = intval($this->env->request()->param("mh"));
						}
						$t = new Thumbnail();
						if ($t->generate($item, $maxWidth, $maxHeight)) die();
					}
					
					$this->env->filesystem()->view($item);
					return;
				case 'zip':
					$mobile = ($this->env->request()->hasParam("m") and strcmp($this->env->request()->param("m"), "1") == 0);
					$this->env->filesystem()->downloadAsZip($item, $mobile);
					return;
				case 'view':
					$this->env->filesystem()->view($item);
					die();
				case 'details':
					$this->response()->success($this->env->filesystem()->details($item));
					break;
				case 'permissions':
					$this->response()->success($this->env->filesystem()->allPermissions($item));
					break;
				default:
					throw $this->invalidRequestException();
			}
		}
		
		private function processPutFile($item) {
			if (count($this->path) != 2) throw invalidRequestException();
			$data = $this->request->data;
			
			switch (strtolower($this->path[1])) {
				case 'name':
					if (!isset($data['name'])) throw $this->invalidRequestException();
					$this->env->filesystem()->rename($item, $data['name']);
					$this->response()->success(TRUE);
					break;
				case 'description':
					if (!isset($data['description'])) throw $this->invalidRequestException();
					$this->env->filesystem()->setDescription($item, $data["description"]);
					$this->response()->success(TRUE);
					break;
				default:
					throw $this->invalidRequestException();
			}
		}
		
		private function processPostFile($item) {
			if (count($this->path) != 2) throw $this->invalidRequestException();
			
			switch (strtolower($this->path[1])) {
				case 'details':
					$data = isset($this->request->data["data"]) ? $this->request->data["data"] : null;
					$this->response()->success($this->env->filesystem()->details($item, $data));
					return;
				case 'move':
					$data = $this->request->data;
					if (!isset($data['id'])) throw $this->invalidRequestException();
					$this->env->filesystem()->move($item, $this->item($data['id'], FALSE));
					break;
				case 'copy':
					$data = $this->request->data;
					if (!isset($data['folder']) and !isset($data['name'])) throw $this->invalidRequestException();
					if (isset($data['folder']) and isset($data['name'])) throw $this->invalidRequestException();
					
					if (isset($data['folder'])) {
						$folder = $this->item($data['folder'], FALSE);
						$to = $folder->createFile($item->name());
					} else {
						$to = $item->parent()->createFile($data['name']);
					}
					$this->env->filesystem()->copy($item, $to);
					break;
				case 'content':
					$this->env->filesystem()->updateFileContents($item, file_get_contents("php://input"));
					break;
				default:
					throw $this->invalidRequestException();
			}
			
			$this->response()->success(TRUE);
		}

		private function processGetFolder($item) {
			if (count($this->path) != 2) throw invalidRequestException();
			
			switch (strtolower($this->path[1])) {
				case 'zip':
					$mobile = ($this->env->request()->hasParam("m") and strcmp($this->env->request()->param("m"), "1") == 0);
					$this->env->filesystem()->downloadAsZip($item, $mobile);
					return;
				case 'info':
					$includeHierarchy = ($this->request->hasParam("h") and strcmp($this->request->param("h"), "1") == 0);
					$this->response()->success($this->getFolderInfo($item, $includeHierarchy));
					break;
				case 'files':
					$items = $this->env->filesystem()->items($item);
					$files = array();
					foreach($items as $i)
						if ($i->isFile()) $files[] = $i->data();
					$this->response()->success($files);
					break;
				case 'folders':
					$items = $this->env->filesystem()->items($item);
					$folders = array();
					foreach($items as $i)
						if (!$i->isFile()) $folders[] = $i->data();
					$this->response()->success($folders);
					break;
				case 'details':
					$this->response()->success($this->env->filesystem()->details($item));
					break;
				case 'permissions':
					$this->response()->success($this->env->filesystem()->allPermissions($item));
					break;
				default:
					throw $this->invalidRequestException();
			}
		}
		
		private function getFolderInfo($item, $includeHierarchy, $data = NULL) {
			$requestDataResult = array();
			$items = $this->env->filesystem()->items($item);
			$files = array();
			$folders = array();
			foreach($items as $i) {
				if ($i->isFile()) $files[] = $i->data();
				else $folders[] = $i->data();
			}
			$result["files"] = $files;
			$result["folders"] = $folders;
			$result["permission"] = $this->env->filesystem()->permission($item);
			
			if ($data != NULL) {
				foreach($this->env->filesystem()->getDataRequestPlugins() as $key => $plugin) {
					if (!array_key_exists($key, $data)) continue;
					
					$d = $plugin->getRequestData($item, $items, $result, $key, $data[$key]);
					if ($d !== NULL) $requestDataResult[$key] = $d;
				}
			}
			$result["data"] = $requestDataResult;
			
			if ($includeHierarchy) {
				$h = array();
				foreach($this->env->filesystem()->hierarchy($item) as $i) {
					$h[] = $i->data();
				}
				$result["hierarchy"] = $h;
			}
			return $result;
		}
		
		private function processPutFolder($item) {
			if (count($this->path) != 2) throw invalidRequestException();
			$data = $this->request->data;
				
			switch (strtolower($this->path[1])) {
				case 'name':		
					if (!isset($data['name'])) throw $this->invalidRequestException();
					$this->env->filesystem()->rename($item, $data['name']);
					$this->response()->success(TRUE);
					break;
				case 'description':
					if (!isset($data['description'])) throw $this->invalidRequestException();
					$this->env->filesystem()->setDescription($item, $data['description']);
					$this->response()->success(TRUE);
					break;
				default:
					throw $this->invalidRequestException();
			}
		}
		
		private function processPostFolder($item) {
			if (count($this->path) != 2) throw $this->invalidRequestException();
			
			switch (strtolower($this->path[1])) {
				case 'details':
					$data = isset($this->request->data["data"]) ? $this->request->data["data"] : null;
					$this->response()->success($this->env->filesystem()->details($item, $data));
					return;
				case 'info':
					$includeHierarchy = ($this->request->hasParam("h") and strcmp($this->request->param("h"), "1") == 0);
					$this->response()->success($this->getFolderInfo($item, $includeHierarchy, $this->request->data["data"]));
					return;
				case 'check':
					if (!isset($this->request->data["files"])) throw $this->invalidRequestException();
					$existing = $this->env->filesystem()->checkExisting($item, $this->request->data["files"]);
					$this->response()->success(array("ok" => (count($existing) == 0), "existing" => $existing));
					return;
				case 'files':
					$this->env->filesystem()->uploadTo($item);
					$this->response()->html(json_encode(array("result" => TRUE)));
					die();
					break;
				case 'folders':
					$data = $this->request->data;
					if (!isset($data['name'])) throw $this->invalidRequestException();
					$this->env->filesystem()->createFolder($item, $data['name']);
					break;
				case 'copy':
					$data = $this->request->data;
					if (!isset($data['folder'])) throw $this->invalidRequestException();
					
					$folder = $this->item($data['folder']);
					$to = $folder->folderWithName($item->name());
					if ($to->exists()) throw new ServiceException("DIR_ALREADY_EXISTS");
					$this->env->filesystem()->copy($item, $to);
					break;
				case 'move':
					$data = $this->request->data;
					if (!isset($data['id'])) throw $this->invalidRequestException();
					$this->env->filesystem()->move($item, $this->item($data['id'], FALSE));
					break;
				case 'retrieve':
					$this->env->features()->assertFeature("retrieve_url");
					$data = $this->request->data;
					if (!isset($data['url'])) throw $this->invalidRequestException();
					
					$retrieved = $this->env->urlRetriever()->retrieve($data['url']);
					if (!$retrieved["success"]) {
						if ($retrieved["result"] === 404)
							$this->response()->fail(301, "Resource not found [".$data['url']."]");
						else if ($retrieved["result"] === 401)
							$this->response()->fail(302, "Unauthorized");
						else
							$this->response()->fail(108, "Failed to retrieve resource [".$data['url']."], http status ".$retrieved["result"]);
						return;
					}
					$this->env->filesystem()->uploadFrom($item, $retrieved["name"], $retrieved["stream"], $data['url']);
					fclose($retrieved["stream"]);
					unlink($retrieved["file"]);
					break;
				case 'search':
					$data = $this->request->data;
					if (!isset($data['text'])) throw $this->invalidRequestException();
					
					$this->response()->success($this->env->filesystem()->search($item, $data['text']));
					return;

				default:
					throw $this->invalidRequestException();
			}
			$this->response()->success(TRUE);
		}
		
		private function processGetUpload() {
			if (count($this->path) != 3 or $this->path[2] != 'status') throw invalidRequestException();
			$this->env->features()->assertFeature("file_upload_progress");
			
			Logging::logDebug('upload status '.$this->path[1]);
			$this->response()->success(apc_fetch('upload_'.$this->path[1]));
		}
		
		public function __toString() {
			return "FileSystemServices";
		}
	}
?>
