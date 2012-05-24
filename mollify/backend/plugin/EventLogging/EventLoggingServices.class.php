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

	class EventLoggingServices extends ServicesBase {
		protected function isAdminRequired() { return TRUE; }
		
		protected function isValidPath($method, $path) {
			if (count($path) < 1 or count($path) > 2)
				return FALSE;
			return TRUE;
		}
		
		public function processPost() {
			if (count($this->path) == 1 and $this->path[0] === 'query') {
				$this->response()->success($this->processQuery());
				return;
			} else if ($this->path[0] === 'downloads') {
				if (count($this->path) == 2 and $this->path[1] === 'events')
					$this->response()->success($this->processTypeQuery('filesystem/download', $this->path[1]));
				else
					$this->response()->success($this->processTypeQuery('filesystem/download'));
				return;
			}
			throw $this->invalidRequestException();
		}
		
		private function processQuery() {
			$data = $this->request->data;
			if (!isset($data)) throw $this->invalidRequestException();
			
			$db = $this->env->configuration()->db();
			$query = "from ".$db->table("event_log")." where 1=1";
			
			if (isset($data['start_time'])) {
				$query .= ' and time >= '.$db->string($data['start_time']);
			}
			if (isset($data['end_time'])) {
				$query .= ' and time < '.$db->string($data['end_time']);
			}
			if (isset($data['user'])) {
				$query .= " and user like '".str_replace("*", "%", $db->string($data['user']))."'";
			}
			if (isset($data['item'])) {
				$query .= " and item like '".str_replace("*", "%", $db->string($data['item']))."'";
			}
			if (isset($data['type'])) {
				$query .= " and type like '".str_replace("*", "%", $db->string($data['type']))."'";
			}

			$query .= ' order by time desc';
			
			$count = $db->query("select count(id) ".$query)->value(0);
			$rows = isset($data["rows"]) ? $data["rows"] : 50;
			$start = isset($data["start"]) ? $data["start"] : 0;
			$result = $db->query("select id, time, user, ip, type, item, details ".$query." limit ".$rows." offset ".$start)->rows();
			
			return array("start" => $start, "count" => count($result), "total" => $count, "events" => $result);
		}

		private function processTypeQuery($type, $events = FALSE) {
			$data = $this->request->data;
			if (!isset($data)) throw $this->invalidRequestException();
			
			$db = $this->env->configuration()->db();
			
			if (!$events) {
				$query = "select distinct item ";
			} else {
				$query = "select id, time, user, ip ";
			}
			$query .= "from ".$db->table("event_log")." where type='".$type."'";
			
			if (isset($data['start_time'])) {
				$query .= ' and time >= '.$db->string($data['start_time']);
			}
			if (isset($data['end_time'])) {
				$query .= ' and time < '.$db->string($data['end_time']);
			}
			if ($events) {
				$query .= " and item = '".$db->string($data['file'])."'";
			} else if (isset($data['file'])) {
				$query .= " and item like '".str_replace("*", "%", $db->string($data['file']))."'";
			}

			if (!$events) {
				$query .= ' order by item asc';
			} else {
				$query .= ' order by time desc';
			}
			
			return $db->query($query)->rows();
		}
		
		public function __toString() {
			return "EventServices";
		}
	}
?>