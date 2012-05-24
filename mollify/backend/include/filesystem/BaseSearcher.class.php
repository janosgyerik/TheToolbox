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
	 			
	 abstract class BaseSearcher {
	 	public function key() {
	 		return get_class($this);
	 	}
	 	
		public function match($data, $item, $text) {
			return $this->getMatch($data, $item, $text);
		}
		
		protected abstract function getMatch($data, $item, $text);
		
		public function preData($parent, $text) {
			return NULL;
		}
	}
?>