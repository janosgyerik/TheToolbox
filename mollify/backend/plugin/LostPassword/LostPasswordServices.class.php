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

	class LostPasswordServices extends ServicesBase {
		protected function isValidPath($method, $path) {
			return count($path) == 0;
		}
		
		public function isAuthenticationRequired() {
			return FALSE;
		}
		
		public function processPost() {
			$data = $this->request->data;
			if (!isset($data['email']))
				throw $this->invalidRequestException();
			
			$user = $this->getUser($data['email']);
			if (!$user) {
				$this->response()->fail(101, "NO_SUCH_USER");
				return;
			}
			
			$pw = $this->createNewPassword();
			if (!$this->env->configuration()->changePassword($user['id'], $pw)) {
				$this->response()->fail(108, "PASSWORD_RESET_FAILED");
				return;
			}
			
			$this->notify($data['email'], $user, $pw);
			$this->response()->success(array());
		}
		
		private function getUser($email) {
			$db = $this->env->configuration()->db();
			$query = "select `id`, `name` from ".$db->table("user")." where `email`=".$db->string($email,TRUE);
			$result = $db->query($query);
			if ($result->count() != 1) return NULL;
			return $result->firstRow();
		}
		
		private function createNewPassword() {
			$chars = "abcdefghijkmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
			srand((double)microtime()*1000000);
			$count = 0;
			$result = '';

			while (TRUE) {
				$result .= substr($chars, rand() % 58, 1);
				if ($count++ > 8) break;
		    }
		    return $result;
		}
		
		private function notify($email, $user, $pw) {
			require_once("Messages.php");
			$values = array("email" => $email, "name" => $user["name"], "password" => $pw);
			
			$subject = $this->replaceParams($RESET_PASSWORD_NOTIFICATION_SUBJECT, $values);
			$msg = $this->replaceParams($RESET_PASSWORD_NOTIFICATION_MESSAGE, $values);
			$recipient = array(array("name" => NULL, "email" => $email));
			$this->env->notificator()->send($recipient, $subject, $msg);
		}
		
		private function replaceParams($text, $values) {
			foreach($values as $k => $v)
				$text = str_replace('%'.$k.'%', $v, $text);
			return $text;
		}
				
		public function __toString() {
			return "LostPasswordServices";
		}
	}
?>