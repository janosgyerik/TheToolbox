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

	class PublicShareServices extends ServicesBase {		
		protected function isValidPath($method, $path) {
			return TRUE;
		}
		
		public function isAuthenticationRequired() {
			return FALSE;
		}
		
		public function processGet() {
			if (count($this->path) != 1) throw $this->invalidRequestException();
			$this->handler()->processShareGet($this->path[0]);
		}
				
		public function processPost() {
			if (count($this->path) != 1) throw $this->invalidRequestException();
			$this->handler()->processSharePost($this->path[0]);
		}
		
		private function handler() {
			return $this->env->plugins()->getPlugin("Share")->getHandler();
		}
	}
?>