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
	
	class Notification {
		private $id;
		private $name;
		private $title;
		private $msg;
		private $recipients;

		public function __construct($id, $name, $title, $msg, $recipients) {
			$this->id = $id;
			$this->name = $name;
			$this->title = $title;
			$this->msg = $msg;
			$this->recipients = $recipients;
		}

		public function id() {
			return $this->id;
		}

		public function getName() {
			return $this->name;
		}

		public function getTitle() {
			return $this->title;
		}
		
		public function getMessage() {
			return $this->msg;
		}
		
		public function getRecipients() {
			return $this->recipients;
		}
		
		public function __toString() {
			return "Notification [".$this->id."]";
		}
	}
?>