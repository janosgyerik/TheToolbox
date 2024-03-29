<?php

	/**
	 * Copyright (c) 2008- Samuli J�rvel�
	 *
	 * All rights reserved. This program and the accompanying materials
	 * are made available under the terms of the Eclipse Public License v1.0
	 * which accompanies this distribution, and is available at
	 * http://www.eclipse.org/legal/epl-v10.html. If redistributing this code,
	 * this entire header must remain intact.
	 */
	 
	class UserEvent extends Event {
		const EVENT_TYPE_USER = "user";
		
		const USER_ADD = "add-user";
		const USER_REMOVE = "remove-user";
		const GROUP_ADD = "add-group";
		const GROUP_REMOVE = "remove-group";
		
		private $id;
		private $info;
		
		static function register($eventHandler) {
			$eventHandler->registerEventType(self::EVENT_TYPE_USER, self::USER_ADD, "User added");
		}
		
		static function userAdded($id, $name, $email) {
			return new UserEvent(self::USER_ADD, $id, array("name" => $name, "email" => $email));
		}

		static function userRemoved($id) {
			return new UserEvent(self::USER_REMOVE, $id, array());
		}

		static function groupAdded($id, $name) {
			return new UserEvent(self::GROUP_ADD, $id, array("name" => $name));
		}

		static function groupRemoved($id) {
			return new UserEvent(self::GROUP_REMOVE, $id, array());
		}
		
		function __construct($type, $id, $info) {
			parent::__construct(time(), self::EVENT_TYPE_USER, $type);
			$this->id = $id;
			$this->info = $info;
		}
				
		public function details() {
			$d = 'user id='.$this->id;
			foreach ($this->info as $k=>$v)
				$d .= (";".$k."=".$v);
			return $d;
		}
		
		public function values($formatter) {
			$values = parent::values($formatter);
			$values["user_id"] = $this->id;
			$values = array_merge($values, $this->info);
			return $values;
		}
		
		public function __toString() {
			return "USEREVENT ".get_class($this);
		}
	}
?>