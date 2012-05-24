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

	class NotificatorServices extends ServicesBase {
		protected function isAdminRequired() { return TRUE; }
		
		protected function isValidPath($method, $path) {
			return count($path) == 1 or count($path) == 2;
		}

		public function processGet() {
			if ($this->path[0] != 'list') throw $this->invalidRequestException();
			
			if (count($this->path) == 1) {
				$dao = $this->getDao();
				$this->response()->success($dao->getAllNotifications());
				return;
			}
			if (count($this->path) == 2) {
				$dao = $this->getDao();
				$this->response()->success($dao->getNotification($this->path[1]));
				return;
			}

			throw $this->invalidRequestException();
		}
		
		public function processPost() {
			if (count($this->path) != 1 or $this->path[0] != 'list') throw $this->invalidRequestException();
			
			$notification = $this->request->data;
			if (!isset($notification["name"])) throw $this->invalidRequestException("No data");
			
			$dao = $this->getDao();
			$this->response()->success($dao->addNotification($notification));
		}

		public function processDelete() {
			if (count($this->path) != 2 or $this->path[0] != 'list') throw $this->invalidRequestException();
			
			$id = $this->path[1];
			$dao = $this->getDao();
			$this->response()->success($dao->removeNotification($id));
		}
		
		public function processPut() {
			if (count($this->path) != 2 or $this->path[0] != 'list') throw $this->invalidRequestException();
			
			$id = $this->path[1];
			$data = $this->request->data;
			$dao = $this->getDao();
			
			if (isset($data["name"])) {
				$this->response()->success($dao->editNotificationName($id, $data["name"]));
				return;
			}
			if (isset($data["message"])) {
				$this->response()->success($dao->editNotificationMessage($id, isset($data["message_title"]) ? $data["message_title"] : "", $data["message"]));
				return;
			}
			if (isset($data["events"])) {
				$this->response()->success($dao->editNotificationEvents($id, $data["events"]));
				return;
			}
			if (isset($data["recipients"])) {
				$this->response()->success($dao->editNotificationRecipients($id, $data["recipients"]));
				return;
			}
						
			throw $this->invalidRequestException();
		}
		
		private function getDao() {
			require_once("dao/NotificatorDao.class.php");
			return new NotificatorDao($this->env);
		}
				
		public function __toString() {
			return "NotificatorServices";
		}
	}
?>