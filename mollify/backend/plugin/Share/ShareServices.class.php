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

	class ShareServices extends ServicesBase {		
		protected function isValidPath($method, $path) {
			return TRUE;
		}
		
		public function isAuthenticationRequired() {
			return TRUE;
		}
		
		public function processGet() {
			if (count($this->path) != 2 or strcmp($this->path[0], 'items') != 0) throw $this->invalidRequestException();
			
			$item = $this->item($this->path[1]);
			$this->response()->success($this->handler()->getShares($item));
		}

		public function processDelete() {
			if (count($this->path) != 1) throw $this->invalidRequestException();
			
			$id = $this->path[0];
			$this->handler()->deleteShare($id);
			$this->response()->success(array());
		}
				
		public function processPost() {
			if (count($this->path) != 2 or strcmp($this->path[0], 'items') != 0) throw $this->invalidRequestException();
			
			$item = $this->item($this->path[1]);
			$data = $this->request->data;
			
			if (!isset($data["name"])) throw $this->invalidRequestException("No data");			
			if ($data["expiration"] and !is_int($data["expiration"])) throw $this->invalidRequestException("Invalid datatype: expiration");
			
			$this->handler()->addShare($item, $data["name"], $data["expiration"], isset($data["active"]) ? $data["active"] : TRUE);
			$this->response()->success($this->handler()->getShares($item));
		}
		
		public function processPut() {
			if (count($this->path) != 1) throw $this->invalidRequestException();
			
			$id = $this->path[0];
			$data = $this->request->data;
			if (!isset($data["name"])) throw $this->invalidRequestException("No data");
			if ($data["expiration"] and !is_int($data["expiration"])) throw $this->invalidRequestException("Invalid datatype: expiration");
			
			$this->handler()->editShare($id, $data["name"], $data["expiration"], isset($data["active"]) ? $data["active"] : TRUE);
			$this->response()->success(array());
		}
		
		private function handler() {
			return $this->env->plugins()->getPlugin("Share")->getHandler();
		}
	}
?>