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
	
	class CommentDao {
		private $env;

		public function __construct($env) {
			$this->env = $env;
		}
		
		public function getCommentCount($item) {
			$db = $this->env->configuration()->db();
			return $db->query("select count(`id`) from ".$db->table("comment")." where `item_id` = ".$db->string($item->id(), TRUE))->value(0);
		}

		public function getCommentCountForChildren($parent) {
			$db = $this->env->configuration()->db();
			$parentLocation = $db->string(str_replace("\\", "\\\\", $parent->location()));
			
			if (strcasecmp("mysql", $this->env->configuration()->getType()) == 0) {
				$itemFilter = "select id from ".$db->table("item_id")." where path REGEXP '^".$parentLocation."[^/\\\\]+[/\\\\]?$'";
			} else {
				$itemFilter = "select id from ".$db->table("item_id")." where REGEX(path, \"#^".$parentLocation."[^/\\\\]+[/\\\\]?$#\")";
			}
			return $db->query("select item_id, count(`id`) as count from ".$db->table("comment")." where item_id in (".$itemFilter.") group by item_id")->valueMap("item_id", "count");
		}
		
		public function getComments($item) {
			$db = $this->env->configuration()->db();
			return $db->query("select c.id as id, u.id as user_id, u.name as username, c.time as time, c.comment as comment from ".$db->table("comment")." c, ".$db->table("user")." u where c.`item_id` = ".$db->string($item->id(), TRUE)." and u.id = c.user_id order by time desc")->rows();
		}
		
		public function addComment($userId, $item, $time, $comment) {
			$db = $this->env->configuration()->db();
			$db->update(sprintf("INSERT INTO ".$db->table("comment")." (user_id, item_id, time, comment) VALUES (%s, %s, %s, %s)", $db->string($userId, TRUE), $db->string($item->id(), TRUE), $db->string(date('YmdHis', $time)), $db->string($comment, TRUE)));
			return $db->lastId();
		}
		
		public function removeComment($item, $id, $userId = NULL) {
			$db = $this->env->configuration()->db();
			$userRestriction = "";
			if ($userId != NULL) $userRestriction = " and user_id=".$db->string($userId, TRUE);
			return $db->update("DELETE FROM ".$db->table("comment")." WHERE item_id = ".$db->string($item->id(), TRUE)." and id=".$db->string($id, TRUE).$userRestriction);
		}
		
		public function deleteComments($item) {
			$db = $this->env->configuration()->db();
			if ($item->isFile())
				return $db->update("DELETE FROM ".$db->table("comment")." WHERE item_id = ".$db->string($item->id(), TRUE));
			else
				return $db->update(sprintf("DELETE FROM ".$db->table("comment")." WHERE item_id in (select id from ".$db->table("item_id")." where path like '%s%%')", str_replace("'", "\'", $db->string($item->location()))));
		}
						
		public function __toString() {
			return "CommentDao";
		}
	}
?>