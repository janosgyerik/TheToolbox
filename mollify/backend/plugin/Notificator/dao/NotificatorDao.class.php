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
	
	class NotificatorDao {
		private $env;

		public function __construct($env) {
			$this->env = $env;
		}
		
		public function getAllNotifications() {
			$db = $this->env->configuration()->db();
			$result = $db->query("select id, name from ".$db->table("notificator_notification")." order by id asc")->rows();
			return $result;
		}

		public function getNotification($id) {
			$db = $this->env->configuration()->db();
			
			$query = "select ntf.id as id, ntf.name as name, ntf.message_title as message_title, ntf.message as message, evt.event_type as event_type, ntf_user.id as ntf_usr_id, ntf_user.name as ntf_usr_name, ntf_user.email as ntf_usr_email, ntf_rcp_user.id as ntf_rcp_usr_id, ntf_rcp_user.name as ntf_rcp_usr_name, ntf_rcp_user.email as ntf_rcp_usr_email ";
			
			$query .= "from ".$db->table("notificator_notification")." ntf left outer join ".$db->table("notificator_notification_event")." evt on evt.notification_id = ntf.id left outer join ".$db->table("notificator_notification_user")." ntf_usr on ntf_usr.notification_id = ntf.id left outer join ".$db->table("user")." ntf_user on ntf_user.id = ntf_usr.user_id left outer join ".$db->table("notificator_notification_recipient")." ntf_rcp on ntf_rcp.notification_id = ntf.id left outer join ".$db->table("user")." ntf_rcp_user on ntf_rcp_user.id = ntf_rcp.user_id";
			
			$query .= " where ntf.id = ".$db->string($id, TRUE);
			
			$rows = $db->query($query)->rows();
			if (count($rows) == 0) return FALSE;
			
			$first = $rows[0];
			$result = array(
				"id" => $first["id"],
				"name" => $first["name"],
				"message" => $first["message"],
				"message_title"  => $first["message_title"],
				"events" => array(),
				"users" => array(),
				"recipients" => array()
			);
						
			foreach($rows as $row) {
				$event = $row["event_type"];
				if ($event != NULL and !in_array($event, $result["events"]))
					$result["events"][] = $event;
					
				$recipient = $row["ntf_rcp_usr_id"];
				if ($recipient != NULL and !in_array($recipient, $result["recipients"])) {
					$result["recipients"][] = $recipient;
				}
			}
			
			return $result;
		}

		public function findNotifications($typeId, $userId) {
			$db = $this->env->configuration()->db();
			
			$query = "select distinct ntf.id as id, ntf.name as name, ntf.message_title as message_title, ntf.message as message, evt.event_type as event_type, ntf_user.id as ntf_usr_id, ntf_user.name as ntf_usr_name, ntf_user.email as ntf_usr_email, ntf_rcp_user.id as ntf_rcp_usr_id, ntf_rcp_user.is_group as ntf_rcp_usr_is_group, ntf_rcp_user.name as ntf_rcp_usr_name, ntf_rcp_user.email as ntf_rcp_usr_email ";
			
			$query .= "from ".$db->table("notificator_notification")." ntf left outer join ".$db->table("notificator_notification_event")." evt on evt.notification_id = ntf.id left outer join ".$db->table("notificator_notification_user")." ntf_usr on ntf_usr.notification_id = ntf.id left outer join ".$db->table("user")." ntf_user on ntf_user.id = ntf_usr.user_id left outer join ".$db->table("notificator_notification_recipient")." ntf_rcp on ntf_rcp.notification_id = ntf.id left outer join ".$db->table("user")." ntf_rcp_user on ntf_rcp_user.id = ntf_rcp.user_id";

			$query .= " where (evt.event_type is null or evt.event_type = '$typeId') and ntf_rcp_user.id is not null";
			$query .= " order by ntf.id asc";
						
			$rows = $db->query($query)->rows();
			
			$result = array();
			$recipients = array();
			$recipientIds = array();
			$prev = NULL;

			foreach($rows as $row) {
				if ($prev == NULL) $prev = $row;
				
				if (strcmp($prev["id"], $row["id"]) != 0) {
					$result[] = new Notification($prev["id"], $prev["name"], $prev["message_title"], $prev["message"], $recipients);
					$recipients = array();
					$recipientIds = array();
				}
				if ($row["ntf_rcp_usr_is_group"] == 1) {
					$users = $this->env->configuration()->getGroupUsers($row["ntf_rcp_usr_id"]);
					foreach($users as $u) {
						if (in_array($u["id"], $recipientIds)) continue;
						$recipients[] = array("id" => $u["id"], "name" => $u["name"], "email" => $u["email"]);
						$recipientIds[] = $u["id"];
					}
				} else {
					if (in_array($row["ntf_rcp_usr_id"], $recipientIds)) continue;
					$recipients[] = array("id" => $row["ntf_rcp_usr_id"], "name" => $row["ntf_rcp_usr_name"], "email" => $row["ntf_rcp_usr_email"]);
					$recipientIds[] = $row["ntf_rcp_usr_id"];
				}
				$prev = $row;
			}
			if (count($recipients) > 0)
				$result[] = new Notification($prev["id"], $prev["name"], $prev["message_title"], $prev["message"], $recipients);
						
			return $result;
		}
		
		public function addNotification($data) {
			$db = $this->env->configuration()->db();
			$db->update(sprintf("INSERT INTO ".$db->table("notificator_notification")." (name) VALUES ('%s')", $db->string($data["name"])));
			return $db->lastId();
			return TRUE;
		}

		public function editNotificationName($id, $name) {
			$db = $this->env->configuration()->db();
			$affected = $db->update(sprintf("UPDATE ".$db->table("notificator_notification")." SET name = '%s' where id=%s", $db->string($name), $db->string($id)));
			if ($affected != 1) throw new ServiceException("REQUEST_FAILED", "Invalid update for id=".$id);
			return TRUE;
		}

		public function editNotificationMessage($id, $title, $message) {
			$db = $this->env->configuration()->db();
			$affected = $db->update(sprintf("UPDATE ".$db->table("notificator_notification")." SET message_title = '%s', message = '%s' where id=%s", $db->string($title), $db->string($message), $db->string($id)));
			if ($affected != 1) throw new ServiceException("REQUEST_FAILED", "Invalid update for id=".$id);
			return TRUE;
		}

		public function editNotificationEvents($id, $events) {
			$db = $this->env->configuration()->db();
			
			$db->startTransaction();
			$db->update(sprintf("DELETE FROM ".$db->table("notificator_notification_event")." WHERE notification_id = '%s'", $db->string($id)));
			foreach ($events as $event)
				$db->update(sprintf("INSERT INTO ".$db->table("notificator_notification_event")." (notification_id, event_type) VALUES ('%s', '%s')", $db->string($id), $db->string($event)));
			$db->commit();
			
			return TRUE;
		}

		public function editNotificationRecipients($id, $recipients) {
			$db = $this->env->configuration()->db();
			
			$db->startTransaction();
			$db->update(sprintf("DELETE FROM ".$db->table("notificator_notification_recipient")." WHERE notification_id = '%s'", $db->string($id)));
			foreach ($recipients as $recipient)
				$db->update(sprintf("INSERT INTO ".$db->table("notificator_notification_recipient")." (notification_id, user_id) VALUES ('%s', '%s')", $db->string($id), $db->string($recipient)));
			$db->commit();
			
			return TRUE;
		}
		
		public function removeNotification($id) {
			$db = $this->env->configuration()->db();
			
			$db->startTransaction();
			$db->update(sprintf("DELETE FROM ".$db->table("notificator_notification_recipient")." WHERE notification_id = '%s'", $db->string($id)));
			$db->update(sprintf("DELETE FROM ".$db->table("notificator_notification_event")." WHERE notification_id = '%s'", $db->string($id)));
			$db->update(sprintf("DELETE FROM ".$db->table("notificator_notification_user")." WHERE notification_id = '%s'", $db->string($id)));
			$db->update(sprintf("DELETE FROM ".$db->table("notificator_notification")." where id=%s", $db->string($id)));
			$db->commit();
			
			return TRUE;
		}
						
		public function __toString() {
			return "NotificatorDao";
		}
	}
?>
