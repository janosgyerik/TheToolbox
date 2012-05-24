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
	
	require_once("install/MollifyInstallProcessor.class.php");
	
	class DefaultInstaller {
		private $page;
		private $processor;
		
		public function __construct($page) {
			$this->page = $page; 
			$this->processor = new MollifyInstallProcessor("install", NULL, array());
		}
		
		public function process() {
			$this->processor->showPage($this->page);
		}
		
		public function hasError() {
			return $this->processor->hasError();
		}

		public function hasErrorDetails() {
			return $this->processor->hasErrorDetails();
		}
		
		public function error() {
			return $this->processor->error();
		}

		public function errorDetails() {
			return $this->processor->errorDetails();
		}
		
		public function data($name = NULL) {
			return $this->processor->data($name);
		}
		
		public function action() {
			return $this->processor->action();
		}

	}
?>