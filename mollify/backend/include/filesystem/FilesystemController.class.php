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
	 
	 require_once("include/event/EventHandler.class.php");
	 			
	 class FilesystemController {	 	
	 	const EVENT_TYPE_FILE = "filesystem";
		
		private $env;
		private $allowedUploadTypes;
		private $permissionCache = array();
		private $folderCache = array();
		private $contextPlugins = array();
		private $dataRequestPlugins = array();
		private $searchers = array();
		private $filesystems = array();
		private $idProvider;
		
		public $allowFilesystems = FALSE;

		function __construct($env) {
			require_once("MollifyFilesystem.class.php");
			require_once("LocalFilesystem.class.php");
			require_once("FilesystemItem.class.php");
			require_once("BaseSearcher.class.php");
			require_once("FilesystemSearcher.class.php");
			require_once("CoreFileDataProvider.class.php");
			require_once("ItemIdProvider.class.php");
			
			$this->env = $env;
			$this->idProvider = new ItemIdProvider($env);
			
			$this->allowedUploadTypes = $env->settings()->setting('allowed_file_upload_types', TRUE);
			$this->registerSearcher(new FileSystemSearcher($this->env));
			
			$coreData = new CoreFileDataProvider($this->env);
			$coreData->init($this);
						
			FileEvent::register($this->env->events());
		}
		
		public function initialize() {}
		
		public function itemIdProvider() {
			return $this->idProvider;
		}
		
		public function registerFilesystem($id, $factory) {
			Logging::logDebug("Filesystem registered: ".$id);
			$this->filesystems[$id] = $factory;
		}

		public function registerItemContextPlugin($key, $plugin) {
			$this->contextPlugins[$key] = $plugin;
		}

		public function registerDataRequestPlugin($keys, $plugin) {
			foreach($keys as $key)
				$this->dataRequestPlugins[$key] = $plugin;
		}
		
		public function getDataRequestPlugins() {
			return $this->dataRequestPlugins;
		}
		
		public function registerSearcher($searcher) {
			$this->searchers[] = $searcher;
		}
		
		public function getRootFolders($all = FALSE) {
			$list = array();
			
			foreach($this->getFolderDefs($all) as $folderDef) {
				$root = $this->filesystem($folderDef, !$all)->root();
				if (!$this->env->authentication()->hasReadRights($this->permission($root))) continue;
				$list[$folderDef['id']] = $root;
			}
			
			return $list;
		}
		
		private function getFolderDefs($all = FALSE) {
			if (!$all)
				$folderDefs = $this->env->configuration()->getUserFolders($this->env->session()->userId(), TRUE);
			else
				$folderDefs = $this->env->configuration()->getFolders();

			$list = array();
			
			foreach($folderDefs as $folderDef) {
				if (array_key_exists($folderDef['id'], $list)) continue;
				if (!$this->isFolderValid($folderDef, !$all)) continue;
				
				if (!isset($folderDef["name"]) and !isset($folderDef["default_name"])) {
					$this->env->session()->end();
					throw new ServiceException("INVALID_CONFIGURATION", "Folder definition does not have a name (".$folderDef['id'].")");
				}
				if (!isset($folderDef["path"])) {
					$this->env->session()->end();
					throw new ServiceException("INVALID_CONFIGURATION", "Folder definition does not have a path (".$folderDef['id'].")");
				}
				
				$list[$folderDef['id']] = $folderDef;
			}
			
			return $list;
		}
		
		private function isFolderValid($folderDef, $mustExist = TRUE) {
			$root = $this->filesystem($folderDef, $mustExist)->root();
			if ($mustExist and !$root->exists()) throw new ServiceException("DIR_DOES_NOT_EXIST", 'root id:'.$folderDef['id']);
			if (!$this->allowFilesystems and !$this->env->authentication()->hasReadRights($this->permission($root))) return FALSE;
			return TRUE;
		}
		
		private function createFilesystem($folderDef) {
			if ($folderDef == NULL) throw new ServiceException("INVALID_CONFIGURATION", "Invalid root folder definition");

			$id = isset($folderDef['id']) ? $folderDef['id'] : '';
			
			//TODO this is hack, support real filesystem types
			if (array_key_exists("S3FS", $this->filesystems)) {
				$factory = $this->filesystems["S3FS"];
				return $factory->createFilesystem($id, $folderDef, $this);
			}
			
			switch ($this->filesystemType($folderDef)) {
				case MollifyFilesystem::TYPE_LOCAL:
					return new LocalFilesystem($id, $folderDef, $this);
				default:
					throw new ServiceException("INVALID_CONFIGURATION", "Invalid root folder definition (".$id."), type unknown");
			}
		}
		
		private function filesystemType($folderDef) {
			return MollifyFilesystem::TYPE_LOCAL;	// include type in definition when more types are supported
		}
		
		public function getSessionInfo() {
			$result = array();
			
			$result['filesystem'] = array(
				"folder_separator" => DIRECTORY_SEPARATOR,
				"max_upload_file_size" => Util::inBytes(ini_get("upload_max_filesize")),
				"max_upload_total_size" => Util::inBytes(ini_get("post_max_size")),
				"allowed_file_upload_types" => $this->allowedFileUploadTypes()
			);
			
			$this->itemIdProvider()->loadRoots();
			
			$result["folders"] = array();
			foreach($this->getRootFolders() as $id => $folder) {
				$nameParts = explode("/", str_replace("\\", "/",$folder->name()));
				$name = array_pop($nameParts);
				
				$result["folders"][] = array(
					"id" => $folder->id(),
					"name" => $name,
					"group" => implode("/", $nameParts),
					"parent_id" => NULL,
					"root_id" => $folder->id(),
					"path" => ""
				);
			}

			return $result;
		}

		public function filesystemFromId($id, $assert=TRUE) {
			return $this->filesystem($this->env->configuration()->getFolder($id), $assert);
		}
		
		public function filesystem($def, $assert=TRUE) {
			$fs = $this->createFilesystem($def);
			if ($assert) $fs->assert();
			return $fs;
		}
		
		public function item($id, $nonexisting = FALSE) {
			$location = $this->itemIdProvider()->getLocation($id);
			$parts = explode(":".DIRECTORY_SEPARATOR, $location);
			if (count($parts) != 2) throw new ServiceException("INVALID_CONFIGURATION", "Invalid item location: ".$location);
			
			$filesystemId = $parts[0];
			$path = $parts[1];
			if (strpos($path, "../") != FALSE or strpos($path, "..\\") != FALSE) new ServiceException("INVALID_CONFIGURATION", "Invalid item location: ".$location);
			
			if (array_key_exists($filesystemId, $this->folderCache)) {
				$folderDef = $this->folderCache[$filesystemId];
			} else {
				$folderDef = $this->env->configuration()->getFolder($filesystemId);
				if (!$folderDef or !$this->isFolderValid($folderDef)) throw new ServiceException("UNAUTHORIZED");
				
				$this->folderCache[$filesystemId] = $folderDef;
			}
			
			return $this->filesystem($folderDef)->createItem($id, $path, $nonexisting);
		}
				
		public function assertFilesystem($folderDef) {
			$this->filesystem($folderDef, TRUE);
		}

		public function assertRights($item, $required, $desc = "Unknown action") {
			if (is_array($item)) {
				foreach($item as $i)
					$this->env->authentication()->assertRights($this->permission($i), $required, "filesystemitem ".$i->id()."/".$desc);
			} else {
				$this->env->authentication()->assertRights($this->permission($item), $required, "filesystemitem ".$item->id()."/".$desc);
			}
		}

		public function ignoredItems($filesystem, $path) {
			return array('mollify.dsc', 'mollify.uac');	//TODO get from settings and/or configuration etc
		}
		
		public function items($folder) {
			//make sure folder permissions are fetched into cache
			$this->fetchPermissions($folder);
			$this->assertRights($folder, Authentication::RIGHTS_READ, "items");
			$this->itemIdProvider()->load($folder);
			
			$list = array();
			foreach($folder->items() as $i) {
				if (!$this->isItemVisible($i)) continue;
				$list[] = $i;
			}
			return $list;
		}

		public function hierarchy($folder) {
			$this->assertRights($folder, Authentication::RIGHTS_READ, "hierarchy");
			return $folder->hierarchy();
		}
		
		private function isItemVisible($item) {
			if ($this->env->authentication()->isAdmin()) return TRUE;
			$permission = $this->getItemUserPermissionFromCache($item);
			if (strcmp(Authentication::PERMISSION_VALUE_NO_RIGHTS, $permission) != 0) return TRUE;
			return FALSE;
		}

		public function details($item, $data = NULL) {
			$this->assertRights($item, Authentication::RIGHTS_READ, "details");
			
			$details = $item->details();
			$details["description"] = $this->description($item);
			$details["permission"] = $this->permission($item);
			
			foreach($this->contextPlugins as $k=>$p) {
				$d = ($data != NULL and isset($data[$k])) ? $data[$k] : NULL;
				$l = $p->getItemContextData($item, $details, $k, $d);
				if (!$l) continue;
				$details[$k] = $l;
			}
			return $details;
		}
		
		public function checkExisting($folder, $files) {
			$existing = array();
			
			foreach($files as $file) {
				$f = $folder->fileWithName($file);
				if ($f->exists()) $existing[] = $file;
			}
			return $existing;
		}
		
		public function env() {
			return $this->env;
		}

		public function description($item) {
			return $this->env->configuration()->getItemDescription($item);
		}

		public function setDescription($item, $desc) {
			$this->assertRights($item, Authentication::RIGHTS_WRITE, "set description");
			return $this->env->configuration()->setItemDescription($item, $desc);
		}

		public function removeDescription($item) {
			$this->assertRights($item, Authentication::RIGHTS_WRITE, "remove description");
			return $this->env->configuration()->removeItemDescription($item);
		}
		
		public function permission($item) {
			if ($this->env->authentication()->isAdmin()) return Authentication::PERMISSION_VALUE_READWRITE;
			
			$permission = $this->getItemUserPermission($item);
			if (!$permission) return $this->env->authentication()->getDefaultPermission();
			return $permission;
		}
		
		public function fetchPermissions($folder) {
			if ($this->env->authentication()->isAdmin()) return;
			$permissions = $this->env->configuration()->getAllItemPermissions($folder, $this->env->session()->userId());

			$this->permissionCacheFolders[] = $folder->id();
			foreach($permissions as $id => $p)
				$this->permissionCache[$id] = $p;
		}
		
		public function temporaryItemPermission($item, $permission) {
			$this->permissionCache[$item->id()] = $permission;
		}
		
		private function getItemUserPermission($item) {
			if (array_key_exists($item->id(), $this->permissionCache)) {
				$permission = $this->permissionCache[$item->id()];
				Logging::logDebug("Permission cache get [".$item->id()."]=".$permission);
			} else {
				$permission = $this->env->configuration()->getItemPermission($item, $this->env->session()->userId());
				if (!$permission) return $this->env->authentication()->getDefaultPermission();
				
				$this->permissionCache[$item->id()] = $permission;
				Logging::logDebug("Permission cache put [".$item->id()."]=".$permission);
			}
			return $permission;
		}

		private function getItemUserPermissionFromCache($item) {
			if (array_key_exists($item->id(), $this->permissionCache)) {
				$permission = $this->permissionCache[$item->id()];
				Logging::logDebug("Permission cache get [".$item->id()."]=".$permission);
			} else {
				$parentId = $item->parent()->id();
				if ($item->isFile() and array_key_exists($parentId, $this->permissionCache)) {
					$permission = $this->permissionCache[$parentId];
					Logging::logDebug("Permission cache get [".$item->id()."->".$parentId."]=".$permission);
				} else {
					return $this->env->authentication()->getDefaultPermission();
				}
			}
			return $permission;
		}
		
		public function allPermissions($item) {
			return $this->env->configuration()->getItemPermissions($item);
		}
		
		private function allowedFileUploadTypes() {
			$types = array();
			foreach ($this->allowedUploadTypes as $type) {
				$pos = strrpos($type, ".");
				if ($pos === FALSE) $types[] = $type;
				else $types[] = substr($type, $pos+1);
			}
			return $types;
		}
		
		public function rename($item, $name) {
			Logging::logDebug('rename from ['.$item->path().'] to ['.$name.']');
			$this->assertRights($item, Authentication::RIGHTS_WRITE, "rename");
			$to = $item->rename($name);
			
			$this->env->events()->onEvent(FileEvent::rename($item, $to));
			$this->idProvider->move($item, $to);
		}

		public function copy($item, $to) {
			Logging::logDebug('copying '.$item->id()."[".$item->path().'] to ['.$to.']');
			
			if (!$item->isFile() and $to->isFile()) throw new ServiceException("NOT_A_DIR", $to->path());
			if ($item->isFile() and !$to->isFile()) throw new ServiceException("NOT_A_FILE", $to->path());
			
			$this->assertRights($item, Authentication::RIGHTS_READ, "copy");
			$this->assertRights($to->parent(), Authentication::RIGHTS_WRITE, "copy");

			$to = $item->copy($to);
			$this->env->events()->onEvent(FileEvent::copy($item, $to));
		}
		
		public function copyItems($items, $folder) {
			Logging::logDebug('copying '.count($items).' items to ['.$folder.']');
			$this->assertRights($items, Authentication::RIGHTS_READ, "copy");
			
			foreach($items as $item) {
				if ($item->isFile())
					$this->copy($item, $folder->createFile($item->name()));
				else
					$this->copy($item, $folder->folderWithName($item->name()));
			}
		}
		
		public function move($item, $to) {
			Logging::logDebug('moving '.$item->id()."[".$item->path().'] to ['.$to.']');

			if ($to->isFile()) throw new ServiceException("NOT_A_DIR", $to->path());
			$this->assertRights($item, Authentication::RIGHTS_READ, "move");
			$this->assertRights($to, Authentication::RIGHTS_WRITE, "move");

			$to = $item->move($to);
						
			$this->env->events()->onEvent(FileEvent::move($item, $to));
			$this->idProvider->move($item, $to);
		}
		
		public function moveItems($items, $to) {
			Logging::logDebug('moving '.count($items).' items');
			$this->assertRights($items, Authentication::RIGHTS_WRITE, "move");
			
			foreach($items as $item)
				$this->move($item, $to);
		}
		
		public function delete($item) {
			Logging::logDebug('deleting ['.$item->id().']');
			
			if (!$item->isFile()) $this->env->features()->assertFeature("folder_actions");
			$this->assertRights($item, Authentication::RIGHTS_WRITE, "delete");
			
			$item->delete();
			
			if ($this->env->features()->isFeatureEnabled("descriptions"))
				$this->env->configuration()->removeItemDescription($item);
			
			$this->env->configuration()->removeItemPermissions($item);
			
			$this->env->events()->onEvent(FileEvent::delete($item));
			$this->idProvider->delete($item);
		}
		
		public function deleteItems($items) {
			Logging::logDebug('deleting '.count($items).' items');
			$this->assertRights($items, Authentication::RIGHTS_WRITE, "delete");
			
			foreach($items as $item)
				$this->delete($item);
		}
		
		public function createFolder($parent, $name) {
			Logging::logDebug('creating folder ['.$parent->id().'/'.$name.']');
			$this->env->features()->assertFeature("folder_actions");
			$this->assertRights($parent, Authentication::RIGHTS_WRITE, "create folder");

			$new = $parent->createFolder($name);
			$this->env->events()->onEvent(FileEvent::createFolder($new));
			
			if (!$this->env->authentication()->isAdmin() and !in_array("permission_inheritance", $this->env->configuration()->getSupportedFeatures()))
				$this->env->configuration()->addItemPermission($new->id(), Authentication::PERMISSION_VALUE_READWRITE, $this->env->session()->userId());
		}

		public function download($file, $mobile, $range = NULL) {
			if (!$range)
				Logging::logDebug('download ['.$file->id().']');
			$this->assertRights($file, Authentication::RIGHTS_READ, "download");
			if (!$file->filesystem()->isDirectDownload()) {
				$this->env->response()->redirect($file->filesystem()->getDownloadUrl($file));
				return;
			}
			
			$name = $file->name();
			$size = $file->size();
			
			if ($range != NULL) {
				list($unit, $range) = explode('=', $range, 2);
				
				if ($unit == 'bytes') {
					$pos = strpos(",", $range);
					if ($pos != false) {
						if ($pos === 0) $range = NULL;
						else if ($pos >= 0) $range = substr($range, 0, $pos);
					}
				} else {
					$range = NULL;
				}
			}
			
			if ($range != NULL) {
				list($start, $end) = explode('-', $range, 2);

				$end = (empty($end)) ? ($size - 1) : min(abs(intval($end)),($size - 1));
				$start = (empty($start) || $end < abs(intval($start))) ? 0 : max(abs(intval($start)),0);
				$range = array($start, $end, $size);
				Logging::logDebug("Download range ".$start."-".$end);
			}

			if (!$range)
				$this->env->events()->onEvent(FileEvent::download($file));

			$this->env->response()->download($name, $file->extension(), $mobile, $file->read($range), $size, $range);
		}

		public function view($file) {
			Logging::logDebug('view ['.$file->id().']');
			$this->assertRights($file, Authentication::RIGHTS_READ, "view");
			$this->env->events()->onEvent(FileEvent::view($file));
			$this->env->response()->send($file->name(), $file->extension(), $file->read(), $file->size());
		}
		
		public function updateFileContents($item, $content) {
			if (!$item->isFile()) throw new ServiceException("NOT_A_FILE", $item->path());
			Logging::logDebug('update file contents ['.$item->id().']');
			$this->assertRights($item, Authentication::RIGHTS_WRITE, "update content");
			$this->env->events()->onEvent(FileEvent::upload($item));
			$item->put($content);
		}
		
		public function getUploadTempDir() {
			$dir = $this->env->settings()->setting("upload_temp_dir");
			if ($dir != NULL and strlen($dir) > 0) return $dir;
			return sys_get_temp_dir();
		}
		
		public function uploadTo($folder) {
			$this->env->features()->assertFeature("file_upload");
			$this->assertRights($folder, Authentication::RIGHTS_WRITE, "upload");
			
			if (!isset($_FILES['uploader-http']) and !isset($_FILES['uploader-flash']))
				throw new ServiceException("NO_UPLOAD_DATA");
			
			if (Logging::isDebug()) Logging::logDebug("Upload to ".$folder->id().", FILES=".Util::array2str($_FILES));
			
			// flash uploader (uploads one file at a time)
			if (isset($_FILES['uploader-flash'])) {
				if (!isset($_FILES['uploader-flash']['tmp_name'])) throw new ServiceException("UPLOAD_FAILED");
				
				$this->upload($folder, $_FILES['uploader-flash']['name'], $_FILES['uploader-flash']['tmp_name']);
				return;
			}
	
			// http
			if (isset($_FILES["file"]) && isset($_FILES["file"]["error"]) && $_FILES["file"]["error"] != UPLOAD_ERR_OK)
				throw new ServiceException("UPLOAD_FAILED", $_FILES["file"]["error"]);
					
			foreach ($_FILES['uploader-http']['name'] as $key => $value) { 
				$name = $_FILES['uploader-http']['name'][$key];
				$origin = $_FILES['uploader-http']['tmp_name'][$key];
				$this->upload($folder, $name, $origin);
			}
		}
		
		private function upload($folder, $name, $origin) {
			$target = $folder->createFile($name);
			if ($target->exists()) throw new ServiceException("FILE_ALREADY_EXISTS");
			Logging::logDebug('uploading to ['.$target.']');
			
			$src = @fopen($origin, "rb");
			if (!$src)
				throw new ServiceException("SAVING_FAILED", "Failed to read uploaded data");			
			$target->write($src);
			fclose($src);
			unlink($origin);
			
			$this->env->events()->onEvent(FileEvent::upload($target));
		}
		
		public function uploadFrom($folder, $name, $stream, $src = '[Unknown]') {
			$this->env->features()->assertFeature("file_upload");
			$this->assertRights($folder, Authentication::RIGHTS_WRITE, "upload");

			$targetItem = $folder->createFile($name);
			if (Logging::isDebug()) Logging::logDebug("Upload from $src ($name) to ".$targetItem->id());
			$targetItem->write($stream);
			
			$this->env->events()->onEvent(FileEvent::upload($targetItem));
		}
		
		public function downloadAsZip($items, $mobile) {
			$zip = $this->createZip($items);
			$name = "items.zip";
			if (!is_array($items)) $name = $items->name().".zip";
			$this->env->response()->download($name, "zip", $mobile, $zip->stream());	
		}
		
		public function storeZip($items) {
			$id = uniqid();
			$zip = $this->createZip($items);
			$this->env->session()->param("zip_".$id, $zip->filename());
			return $id;
		}
		
		public function downloadStoredZip($id, $mobile) {
			$p = "zip_".$id;
			if (!$this->env->session()->hasParam($p))
				throw new ServiceException("INVALID_REQUEST", "Stored zip id does not exist: ".$p);
			
			$filename = $this->env->session()->param($p);
			if (!file_exists($filename))
				throw new ServiceException("INVALID_REQUEST", "Stored zip file does not exist");

			$handle = @fopen($filename, "rb");
			if (!$handle)
				throw new ServiceException("REQUEST_FAILED", "Could not open zip for reading: ".$this->name);
			$this->env->response()->download("items.zip", "zip", $mobile, $handle);
		}
		
		private function createZip($items) {
			$this->env->features()->assertFeature("zip_download");
			
			if (is_array($items)) {
				$this->assertRights($items, Authentication::RIGHTS_READ, "download as zip");
				
				$zip = $this->zipper();
				foreach($items as $item) {
					$item->addToZip($zip);
					$this->env->events()->onEvent(FileEvent::download($item));
				}
				$zip->finish();
			} else {
				$item = $items;
				$this->assertRights($item, Authentication::RIGHTS_READ, "download as zip");

				$zip = $this->zipper();
				$item->addToZip($zip);
				$zip->finish();
				
				$this->env->events()->onEvent(FileEvent::download($item));
			}
			
			return $zip;
		}
		
		public function search($parent, $text) {
			if ($parent == NULL) {
				$m = array();
				foreach($this->getRootFolders() as $id => $root) {
					$data = array();
					foreach($this->searchers as $searcher)
						$data[$searcher->key()] = $searcher->preData($root, $text);
					$m = array_merge($m, $this->searchRecursive($data, $root, $text));
				}
			} else {
				$this->itemIdProvider()->load($parent, TRUE);
				$data = array();
				foreach($this->searchers as $searcher)
					$data[$searcher->key()] = $searcher->preData($parent, $text);
				$m = $this->searchRecursive($data, $parent, $text);
			}
			return array("count" => count($m), "matches" => $m);
		}
		
		private function searchRecursive($data, $parent, $text) {
			$result = array();
			
			foreach($parent->items() as $item) {
				$id = $item->id();
				
				foreach($this->searchers as $searcher) {
					$match = $searcher->match($data[$searcher->key()], $item, $text);
					if (!$match) continue;
					
					if (in_array($id, $result)) {
						$result[$id]["matches"] = array_merge($match, $result[$id]["matches"]);
					} else {
						$result[$id] = array("item" => $item->data(), "matches" => $match);
					}
				}
				if (!$item->isFile()) $result = array_merge($result, $this->searchRecursive($data, $item, $text));
			}
			return $result;
		}
		
		public function zipper() {
			require_once('zip/MollifyZip.class.php');
			$zipper = $this->setting("zipper", TRUE);
			
			if (strcasecmp($zipper, "ziparchive") === 0) {
				require_once('zip/MollifyZipArchive.class.php');
				return new MollifyZipArchive($this->env);
			} else if (strcasecmp($zipper, "native") === 0) {
				require_once('zip/MollifyZipNative.class.php');
				return new MollifyZipNative($this->env);
			} else if (strcasecmp($zipper, "raw") === 0) {
				require_once('zip/MollifyZipRaw.class.php');
				return new MollifyZipRaw($this->env);
			}
			
			throw new ServiceException("INVALID_CONFIGURATION", "Unsupported zipper configured: ".$zipper);
		}
		
		public function setting($setting, $allowDefaultIfNotDefined = FALSE) {
			return $this->env->settings()->setting($setting, $allowDefaultIfNotDefined);
		}

		public function log() {
			Logging::logDebug("FILESYSTEM: allowed_file_upload_types=".Util::array2str($this->allowedUploadTypes));
		}

		public function __toString() {
			return "FILESYSTEMCONTROLLER";
		}
	 }
	 
	 class FileEvent extends Event {
		const COPY = "copy";
		const RENAME = "rename";
		const MOVE = "move";
		const DELETE = "delete";
		const CREATE_FOLDER = "create_folder";
		const DOWNLOAD = "download";
		const UPLOAD = "upload";
		const VIEW = "view";
		
		private $item;
		private $info;
		
		static function register($eventHandler) {
			$eventHandler->registerEventType(FilesystemController::EVENT_TYPE_FILE, self::COPY, "Copy file");
			$eventHandler->registerEventType(FilesystemController::EVENT_TYPE_FILE, self::RENAME, "Rename file");
			$eventHandler->registerEventType(FilesystemController::EVENT_TYPE_FILE, self::MOVE, "Move file");
			$eventHandler->registerEventType(FilesystemController::EVENT_TYPE_FILE, self::DELETE, "Delete file");
			$eventHandler->registerEventType(FilesystemController::EVENT_TYPE_FILE, self::CREATE_FOLDER, "Create folder");
			$eventHandler->registerEventType(FilesystemController::EVENT_TYPE_FILE, self::DOWNLOAD, "Download file");
			$eventHandler->registerEventType(FilesystemController::EVENT_TYPE_FILE, self::VIEW, "View file");
			$eventHandler->registerEventType(FilesystemController::EVENT_TYPE_FILE, self::UPLOAD, "Upload file");
		}
		
		static function rename($item, $to) {
			return new FileEvent($item, self::RENAME, $to);
		}

		static function copy($item, $to) {
			return new FileEvent($item, self::COPY, $to);
		}

		static function move($item, $to) {
			return new FileEvent($item, self::MOVE, $to);
		}

		static function delete($item) {
			return new FileEvent($item, self::DELETE);
		}

		static function createFolder($folder) {
			return new FileEvent($folder, self::CREATE_FOLDER);
		}

		static function download($item) {
			return new FileEvent($item, self::DOWNLOAD);
		}

		static function upload($item) {
			return new FileEvent($item, self::UPLOAD);
		}
		
		static function view($item) {
			return new FileEvent($item, self::VIEW);
		}
		
		function __construct($item, $type, $info = NULL) {
			parent::__construct(time(), FileSystemController::EVENT_TYPE_FILE, $type);
			$this->item = $item;
			$this->info = $info;
		}

		public function item() {
			return $this->item;
		}

		public function info() {
			return $this->info;
		}
		
		public function itemToStr() {
			return $this->item->internalPath();
		}
				
		public function details() {
			$f = $this->item->id()." (".$this->item->filesystem()->name().")";
			
			if ($this->subType() === self::RENAME or $this->subType() === self::COPY or $this->subType() === self::MOVE)
				return 'item id='.$f.';to='.$this->info->id()." (".$this->info->filesystem()->name().")";
			return 'item id='.$f;
		}
		
		public function values($formatter) {
			$values = parent::values($formatter);
			$values["item_id"] = $this->item->id();
			$values["item_name"] = $this->item->name();
			$values["item_path"] = $this->item->path();
			$values["item_internal_path"] = $this->item->internalPath();
			$values["root_name"] = $this->item->root()->name();

			if ($this->subType() === self::RENAME or $this->subType() === self::COPY or $this->subType() === self::MOVE) {
				$values["to_item_id"] = $this->info->id();
				$values["to_item_name"] = $this->info->name();
				$values["to_item_path"] = $this->info->path();
				$values["to_item_internal_path"] = $this->info->internalPath();
				$values["to_root_name"] = $this->info->root()->name();
			}

			return $values;
		}
		
		public function __toString() {
			return "FILESYSTEMEVENT ".get_class($this);
		}
	}
?>
