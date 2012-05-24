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
	
	class InstallerSession extends Session {
		public function __construct($useCookie) {
			parent::__construct($useCookie);
		}
		
		protected function findSessionUser($id) {
			$db = $this->env->configuration()->db();
			return $db->query(sprintf("SELECT id, name, password, email, auth FROM ".$db->table("user")." WHERE id='%s'", $db->string($id)))->firstRow();
		}
	}
?>