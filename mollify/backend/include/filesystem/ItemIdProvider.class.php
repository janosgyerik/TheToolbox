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
	 			
	 class ItemIdProvider {
	 	private $env;
	 	private $cache = array();
	 	
		function __construct($env) {
			$this->env = $env;
		}

	 	public function getItemId($p) {
	 		if (!array_key_exists($p, $this->cache))
	 			$this->cache[$p] = $this->getOrCreateItemId($p);
	 		return $this->cache[$p];
	 	}
	 	
	 	public function getLocation($id) {
		 	$db = $this->env->configuration()->db();
			$query = "select path from ".$db->table("item_id")." where id=".$db->string($id,TRUE);
			$result = $db->query($query);
			
			if ($result->count() === 1) return $result->value(0);
			throw new ServiceException("No item id found ".$id);
	 	}

	 	public function loadRoots() {
		 	$db = $this->env->configuration()->db();
		 	
			if (strcasecmp("mysql", $this->env->configuration()->getType()) == 0) {
				$pathFilter = "path REGEXP '^.:[/\\\\]$'";
			} else {
				$pathFilter = "REGEX(path, \"#^.:[/\\\\]$#\")";
			}

		 	$query = "select id, path from ".$db->table("item_id")." where ".$pathFilter;
		 	foreach ($db->query($query)->rows() as $r)
		 		$this->cache[$r["path"]] = $r["id"];
	 	}
	 	
	 	public function load($parent, $recursive = FALSE) {
		 	$db = $this->env->configuration()->db();
		 	
		 	if ($recursive) {
			 	$pathFilter = "path like '".$db->string($this->itemPath($parent))."%'";
		 	} else {
				if (strcasecmp("mysql", $this->env->configuration()->getType()) == 0) {
					$pathFilter = "path REGEXP '^".$db->string(str_replace("\\", "\\\\", $this->itemPath($parent)))."[^/\\\\]+[/\\\\]?$'";
				} else {
					$pathFilter = "REGEX(path, \"#^".$db->string(str_replace("\\", "\\\\", $this->itemPath($parent)))."[^/\\\\]+[/\\\\]?$#\")";
				}
			}

		 	$query = "select id, path from ".$db->table("item_id")." where ".$pathFilter;
		 	foreach ($db->query($query)->rows() as $r)
		 		$this->cache[$r["path"]] = $r["id"];
	 	}
	 	
	 	private function getOrCreateItemId($p) {
		 	$db = $this->env->configuration()->db();
			$query = "select id from ".$db->table("item_id")." where path=".$db->string($p, TRUE);
			$result = $db->query($query);
			
			if ($result->count() === 1) return $result->value(0);

			$id = uniqid("");
			$db->update(sprintf("INSERT INTO ".$db->table("item_id")." (id, path) VALUES (%s,%s)", $db->string($id, TRUE), $db->string($p, TRUE)));
			return $id;
	 	}
	 	
		public function delete($item) {
			$db = $this->env->configuration()->db();
			if ($item->isFile())
				return $db->update("DELETE FROM ".$db->table("item_id")." WHERE id = ".$db->string($item->id(), TRUE));
			else
				return $db->update(sprintf("DELETE FROM ".$db->table("item_id")." WHERE path like '%s%%'", $db->string(str_replace("\\", "\\\\", $this->itemPath($item)))));
		}

		public function move($item, $to) {
			$db = $this->env->configuration()->db();
			if ($item->isFile())
				return $db->update("UPDATE ".$db->table("item_id")." SET path = ".$db->string($this->itemPath($to), TRUE) ." where path = ".$db->string($this->itemPath($item), TRUE));
			else {
				$path = $this->itemPath($item);
				$len = mb_strlen($path, "UTF-8");

				return $db->update(sprintf("UPDATE ".$db->table("item_id")." SET path=CONCAT('%s', SUBSTR(path, %d)) WHERE path like '%s%%'", $db->string($this->itemPath($to)), $len+1, $db->string(str_replace("\\", "\\\\", $path))));
			}
		}
		
		private function itemPath($i) {
			return $i->location();
		}
	 }
?>