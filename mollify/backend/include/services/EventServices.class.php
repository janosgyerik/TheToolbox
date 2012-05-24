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

	class EventServices extends ServicesBase {
		protected function isValidPath($method, $path) {
			return count($path) == 1;
		}
				
		protected function isAdminRequired() { return TRUE; }
		
		public function processGet() {
			if ($this->path[0] === 'types') {
				$this->response()->success($this->env->events()->getTypes());
				return;
			}
			throw $this->invalidRequestException();
		}
		
		public function __toString() {
			return "EventServices";
		}
	}
?>