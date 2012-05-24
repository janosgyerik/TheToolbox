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
	
	class ShareDao {
		private $env;

		public function __construct($env) {
			$this->env = $env;
		}
		
		public function getShareCount($item, $userId) {
			$db = $this->env->configuration()->db();
			return $db->query("select count('id') from ".$db->table("share")." where item_id = ".$db->string($item->id(), TRUE)." and user_id = ".$db->string($userId, TRUE))->value(0);
		}

		public function getShare($id, $mustBeValidAfter = NULL) {
			$db = $this->env->configuration()->db();
			$query = "select id, name, item_id, active from ".$db->table("share")." where active=1 and id = ".$db->string($id, TRUE);
			if ($mustBeValidAfter)
				$query .= ' and (expiration is null or expiration >= '.$db->string($mustBeValidAfter).')';
			return $db->query($query)->firstRow();
		}

		public function getShares($item, $userId) {
			$db = $this->env->configuration()->db();
			$list = $db->query("select id, name, expiration, active from ".$db->table("share")." where item_id = ".$db->string($item->id(), TRUE)." and user_id = ".$db->string($userId, TRUE)." order by created asc")->rows();
			
			$res = array();
			foreach($list as $s)
				$res[] = array("id" => $s["id"], "name" => $s["name"], "expiration" => $s["expiration"],"active" => ($s["active"] == 1));
			return $res;
		}
		
		public function addShare($id, $item, $name, $userId, $expirationTime, $time, $active = TRUE) {
			$db = $this->env->configuration()->db();
			$db->update(sprintf("INSERT INTO ".$db->table("share")." (id, name, item_id, user_id, expiration, created, active) VALUES (%s, %s, %s, %s, %s, %s, %s)", $db->string($id, TRUE), $db->string($name, TRUE), $db->string($item->id(), TRUE), $db->string($userId, TRUE), $db->string($expirationTime), $db->string($time), ($active ? "1" : "0")));
		}
		
		public function editShare($id, $name, $expirationTime, $active) {
			$db = $this->env->configuration()->db();
			$db->update(sprintf("UPDATE ".$db->table("share")." SET name = %s, active = %s, expiration = %s WHERE id=%s", $db->string($name, TRUE), ($active ? "1" : "0"), $db->string($expirationTime), $db->string($id, TRUE)));
		}

		public function deleteShare($id) {
			$db = $this->env->configuration()->db();
			return $db->update("DELETE FROM ".$db->table("share")." WHERE id = ".$db->string($id, TRUE));
		}
		
		public function deleteShares($item) {
			$db = $this->env->configuration()->db();
			if ($item->isFile())
				return $db->update("DELETE FROM ".$db->table("share")." WHERE item_id = ".$db->string($item->id(), TRUE));
			else
				return $db->update(sprintf("DELETE FROM ".$db->table("share")." WHERE item_id in (select id from ".$db->table("item_id")." where path like '%s%%')", str_replace("'", "\'", $db->string($item->location()))));
		}
						
		public function __toString() {
			return "ShareDao";
		}
	}
?>