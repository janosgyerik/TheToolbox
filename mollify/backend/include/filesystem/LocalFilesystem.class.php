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

	class LocalFilesystem extends MollifyFilesystem {
		private $rootPath;
		
		function __construct($id, $def, $filesystemInfo) {
			parent::__construct($id, $def['name'] != NULL ? $def['name'] : $def['default_name'], $filesystemInfo);
			if ($def == NULL or !isset($def["path"])) throw new ServiceException("INVALID_CONFIGURATION", "Invalid filesystem definition");
			$this->rootPath = self::folderPath($def["path"]);
		}
		
		public function isDirectDownload() {
			return TRUE;
		}
		
		public function assert() {
			if (!$this->exists())
				throw new NonExistingFolderException("INVALID_CONFIGURATION", "Invalid folder definition, path does not exist [".$this->id()."]");
		}
		
		public function exists() {
			return file_exists($this->rootPath);
		}
		
		public function create() {
			if (!mkdir($this->rootPath, 0755)) return FALSE;
			if ($this->filesystemInfo->env()->features()->isFeatureEnabled("folder_protection")) {
				copy($this->filesystemInfo->env()->getScriptRootPath()."/include/apache/htaccess", $this->rootPath.'.htaccess');
			}
			return TRUE;
		}
		
		public function type() {
			return MollifyFilesystem::TYPE_LOCAL;
		}

		public function createItemWithInternalPath($id, $path) {
			if (strpos($path, $this->rootPath) === FALSE)
				throw new ServiceException("INVALID_REQUEST", "Illegal filesystem (".$this->rootPath.") for path: ".$path);
			return $this->createItem($id, $this->publicPath($path));
		}
				
		public function createItem($id, $path) {
			if (strlen($path) > 0 and (strpos($path, "..\\") != FALSE or strpos($path, "../") != FALSE))
				throw new ServiceException("INVALID_REQUEST", "Illegal path: ".$path);
			
			$fullPath = self::joinPath($this->rootPath, $path);
			$isFile = (strcasecmp(substr($fullPath, -1), DIRECTORY_SEPARATOR) != 0);
			
			if ($isFile) return new File($id, $this->rootId(), $path, self::basename($fullPath), $this);
			return new Folder($id, $this->rootId(), $path, self::basename($fullPath), $this);
		}

		private function publicPath($path) {
			return substr($path, strlen($this->rootPath));
		}
		
		public function internalPath($item) {
			return $this->localPath($item);
		}
		
		public function localPath($item) {
			return $this->filesystemInfo->env()->convertCharset(self::joinPath($this->rootPath, $item->path()), FALSE);
		}
		
		public function itemExists($item) {
			return file_exists($this->internalPath($item));
		}
		
		public function details($item) {
			$datetimeFormat = $this->internalTimestampFormat();
			
			$details = array("id" => $item->id());
			if ($item->isFile()) {
				$path = $this->localPath($item);
				$details["last_changed"] = date($datetimeFormat, filectime($path));
				$details["last_modified"] = date($datetimeFormat, filemtime($path));
				$details["last_accessed"] = date($datetimeFormat, fileatime($path));
			}
			return $details;
		}

		public function extension($item) {
			if (!$item->isFile()) return NULL;
			
			$extPos = strrpos($item->name(), '.');
			if ($extPos > 0)
				return substr($item->name(), $extPos + 1);
			return "";
		}

		public function items($parent) {
			$parentPath = $this->localPath($parent);
			
			$items = scandir($parentPath);
			if (!$items) throw new ServiceException("INVALID_PATH", $parent->id());
			
			$ignored = $this->ignoredItems($this->publicPath($parentPath));
				
			$result = array();
			foreach($items as $i => $name) {
				if (substr($name, 0, 1) == '.') continue;
				if (in_array(strtolower($name), $ignored)) continue;
				
				$nativePath = self::joinPath($parentPath, $name);
				$itemName = $this->filesystemInfo->env()->convertCharset($name);
				$path = $this->filesystemInfo->env()->convertCharset($nativePath);
				
				if (!is_dir($nativePath)) {	
					$p = $this->publicPath($path);
					$result[] = new File($this->itemId($p), $this->rootId(), $p, $itemName, $this);
				} else {
					$p = $this->publicPath(self::folderPath($path));
					$result[] = new Folder($this->itemId($p), $this->rootId(), $p, $itemName, $this);
				}
			}
			
			return $result;
		}
		
		public function hierarchy($item) {
			$p = $item->parent();
			$result = array();
			if ($p != NULL) {
				$path = $this->localPath($p);

				$root = $item->root();
				$rootPath = $root->internalPath();
				
				$parts = preg_split("/\//", substr($path, strlen($rootPath)), -1, PREG_SPLIT_NO_EMPTY);
				$current = $rootPath;
				$result[] = $root;
				
				foreach($parts as $p) {
					$public = $this->publicPath(self::folderPath($current));
					$result[] = new Folder($this->itemId($public), $this->rootId(), $public, $this->filesystemInfo->env()->convertCharset($p), $this);				
					$current .= $p.DIRECTORY_SEPARATOR;
				}
				if (!$item->isFile()) $result[] = $item;
			}
			return $result;
		}
		
		private function allFilesRecursively($path) {
			$files = scandir($path);
			if (!$files) throw new ServiceException("INVALID_PATH", $this->path);
			
			$ignored = $this->ignoredItems($this->publicPath($path));
			$result = array();
			
			foreach($files as $i => $name) {
				if (substr($name, 0, 1) == '.' || in_array(strtolower($name), $ignored))
					continue;
	
				$fullPath = self::joinPath($path, $name);
				if (is_dir($fullPath)) {
					$result = array_merge($result, $this->allFilesRecursively($fullPath));
					continue;
				}
				
				$result[] = $fullPath;
			}
			return $result;
		}
		
		public function parent($item) {
			if ($item->path() === '') return NULL;
			
			$path = $this->localPath($item);
			return $this->itemWithPath($this->publicPath(self::folderPath($this->filesystemInfo->env()->convertCharset(dirname($path)))));
		}

		public function rename($item, $name) {
			$old = $this->localPath($item);
			$new = self::joinPath(dirname($old), $this->filesystemInfo->env()->convertCharset($name, FALSE));
			
			if (!$item->isFile()) $new = self::folderPath($new);

			if (file_exists($new))
				throw new ServiceException("FILE_ALREADY_EXISTS", "Failed to rename [".$item->id()."], target already exists (".$new.")");

			if (!rename($old, $new)) throw new ServiceException("REQUEST_FAILED", "Failed to rename [".$item->id()."]");
			
			return $this->createItem($item->id(), $this->publicPath($this->filesystemInfo->env()->convertCharset($new)));
		}

		public function copy($item, $to) {			
			$target = $to->internalPath();

			if (file_exists($target)) throw new ServiceException("FILE_ALREADY_EXISTS", "Failed to copy [".$item->id()."] to [".$to->id()."], target already exists (".$target.")");
			
			$result = FALSE;
			if ($item->isFile()) {
				$result = copy($item->internalPath(), $target);
			} else {
				$result = $this->copyFolderRecursively($item->internalPath(), $target);
			}
			if (!$result) throw new ServiceException("REQUEST_FAILED", "Failed to copy [".$item->id()." to .".$to->id()."]");
			
			return $to;
		}
		
		private function copyFolderRecursively($from, $to) { 
			$dir = opendir($from); 
			@mkdir($to);
		    
		    while (false !== ($item = readdir($dir))) { 
		        if (($item == '.') or ($item == '..')) continue;
		        
		        $source = $from.DIRECTORY_SEPARATOR.$item;
		        $target = $to.DIRECTORY_SEPARATOR.$item;
		        
				if (is_dir($source))
					$this->copyFolderRecursively($source, $target);
				else
					copy($source, $target);
		    } 
		    closedir($dir);
		    return TRUE; 
		} 
		
		public function move($item, $to) {			
			$target = self::joinPath($to->internalPath(), $this->filesystemInfo->env()->convertCharset($item->name(), FALSE));
			if (!$item->isFile()) $target = self::folderPath($target);
			if (file_exists($target)) throw new ServiceException("FILE_ALREADY_EXISTS", "Failed to move [".$item->id()."] to [".$to->id()."], target already exists (".$target.")");
			if (!rename($item->internalPath(), $target)) throw new ServiceException("REQUEST_FAILED", "Failed to move [".$item->id()."] to ".$target);
			
			return $to->filesystem()->createItemWithInternalPath($item->id(), $this->filesystemInfo->env()->convertCharset($target));
		}
		
		public function delete($item) {
			if ($item->isFile()) {
				if (!unlink($this->localPath($item)))
					throw new ServiceException("REQUEST_FAILED", "Cannot delete [".$item->id()."]");				
			} else {		
				$this->deleteFolderRecursively($this->localPath($item));
			}
		}
		
		private function deleteFolderRecursively($path) {
			$path = self::folderPath($path);
			$handle = opendir($path);
			
			if (!$handle)
				throw new ServiceException("REQUEST_FAILED", "Could not open directory for traversal (recurse): ".$path);
		    
		    while (false !== ($item = readdir($handle))) {
				if ($item != "." and $item != ".." ) {
					$fullpath = $path.$item;
	
					if (is_dir($fullpath)) {
						$this->deleteFolderRecursively($fullpath);
					} else {
						if (!unlink($fullpath)) {
							closedir($handle);
							throw new ServiceException("REQUEST_FAILED", "Failed to remove file (recurse): ".$fullpath);
						}
					}
				}
			}
			
			closedir($handle);
			
			if (!rmdir($path))
				throw new ServiceException("REQUEST_FAILED", "Failed to remove directory (delete_directory_recurse): ".$path);
		}
		
		public function createFolder($folder, $name) {
			$path = self::folderPath(self::joinPath($this->localPath($folder), $this->filesystemInfo->env()->convertCharset($name, FALSE)));
			if (file_exists($path)) throw new ServiceException("DIR_ALREADY_EXISTS", $folder->id()."/".$name);
			if (!mkdir($path, $this->filesystemInfo->setting("new_folder_permission_mask", TRUE))) {
				throw new ServiceException("CANNOT_CREATE_FOLDER", $folder->id()."/".$name);
			} else {
				chmod($path, $this->filesystemInfo->setting("new_folder_permission_mask", TRUE));
			}
			return $this->itemWithPath($this->publicPath($this->filesystemInfo->env()->convertCharset($path)));
		}
		
		public function createFile($folder, $name) {
			$target = self::joinPath($this->localPath($folder), $this->filesystemInfo->env()->convertCharset($name, FALSE));
			if (file_exists($target)) throw new ServiceException("FILE_ALREADY_EXISTS");
			return $this->itemWithPath($this->publicPath($this->filesystemInfo->env()->convertCharset($target)));
		}

		public function fileWithName($folder, $name) {
			$path = self::joinPath($this->localPath($folder), $this->filesystemInfo->env()->convertCharset($name, FALSE));
			return $this->itemWithPath($this->publicPath($this->filesystemInfo->env()->convertCharset($path)));
		}

		public function folderWithName($folder, $name) {
			$path = self::joinPath($this->localPath($folder), $this->filesystemInfo->env()->convertCharset($name, FALSE).DIRECTORY_SEPARATOR);
			return $this->itemWithPath($this->publicPath($this->filesystemInfo->env()->convertCharset($path)));
		}
		
		public function size($file) {
			return sprintf("%u", filesize($this->localPath($file)));
		}
		
		public function lastModified($item) {
			return filemtime($this->localPath($item));
		}

		public function read($item, $range = NULL) {
			$handle = @fopen($this->localPath($item), "rb");
			if (!$handle)
				throw new ServiceException("REQUEST_FAILED", "Could not open file for reading: ".$item->id());
			return $handle;
		}
		
		public function write($item, $s) {
			$handle = @fopen($this->localPath($item), "wb");
			if (!$handle)
				throw new ServiceException("REQUEST_FAILED", "Could not open file for writing: ".$item->id());
			while (!feof($s)) {
				set_time_limit(0);
				fwrite($handle, fread($s, 1024));
			}
			fclose($handle);
		}
		
		public function put($item, $content) {
			file_put_contents($this->localPath($item), $content);
		}

		public function addToZip($item, $zip) {
			if ($item->isFile()) {
				$zip->add($item->name(), $this->localPath($item), $item->size());
			} else {
				if ($zip->acceptFolders()) {
					$zip->add($item->name(), $this->localPath($item));
				} else {
					$offset = strlen($this->localPath($item)) - strlen($item->name()) - 1;
					$files = $this->allFilesRecursively($this->localPath($item));	//TODO rights!
					
					foreach($files as $file) {
						$st = stat($file);
						$zip->add(substr($file, $offset), $file, $st['size']);
					}
				}
			}
		}
		
		public function __toString() {
			return "LOCAL (".$this->id.") ".$this->name."(".$this->rootPath.")";
		}
				
		static function joinPath($item1, $item2) {
			return self::folderPath($item1).$item2;
		}
		
		static function folderPath($path) {
			return rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
		}
		
		static function basename($path) {
			$name = strrchr(rtrim($path, DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR);
			if (!$name) return "";
			return substr($name, 1);
		}
	}
?>