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
	
	require_once("dao/CommentDao.class.php");
	
	class CommentHandler {
		private $env;
		
		public function __construct($env) {
			$this->env = $env;			
		}
				
		public function getDetail($item, $key) {
			return $this->getDao()->getCommentCount($item);
		}
		
		public function getItemContextData($item, $details, $key, $data) {
			return array(
				"count" => $this->getDao()->getCommentCount($item)
			);
		}
		
		public function onEvent($e) {
			if (strcmp(FilesystemController::EVENT_TYPE_FILE, $e->type()) != 0) return;
			$type = $e->subType();			
			if ($type === FileEvent::DELETE)
				$this->getDao()->deleteComments($e->item());
		}
		
		public function getCommentCount($item) {
			return $this->getDao()->getCommentCount($item);
		}

		public function getComments($item) {
			return $this->getDao()->getComments($item);
		}
		
		public function addComment($user, $item, $comment) {
			$this->getDao()->addComment($user, $item, time(), $comment);
		}
		
		public function removeComment($item, $commentId) {
			$user = $this->env->session()->userId();
			if ($this->env->authentication()->isAdmin()) $user = NULL;
			$this->getDao()->removeComment($item, $commentId, $user);
		}
						
		public function getRequestData($parent, $items, $result, $key, $dataRequest) {
			$counts = $this->getDao()->getCommentCountForChildren($parent);
			$result = array();
			foreach($counts as $id=>$c) {
				$result[$this->env->filesystem()->item($id)->id()] = $c;
			} 
			return $result;
		}
		
		private function getDao() {
			return new CommentDao($this->env);
		}
				
		public function __toString() {
			return "CommentHandler";
		}
	}
?>