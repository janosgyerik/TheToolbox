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

	abstract class ServicesBase {
		protected $env;
		protected $request;
		protected $id;
		protected $path;
		
		public function __construct($serviceEnvironment, $request, $id, $path) {
			$this->env = $serviceEnvironment;
			$this->request = $request;
			$this->id = $id;
			$this->path = $path;
			
			if (!$this->isValidPath($this->request->method(), $this->path)) throw $this->invalidRequestException();
			$this->init();
		}
		
		protected function init() {}

		public function isAuthenticated() {
			if ($this->isAuthenticationRequired() and !$this->env->authentication()->isAuthenticated()) return FALSE;
			if ($this->isAuthenticationRequired() and $this->isAdminRequired() and !$this->env->authentication()->isAdmin()) return FALSE;
			return TRUE;
		}
		
		protected function isAuthenticationRequired() {
			return $this->env->configuration()->isAuthenticationRequired();
		}
		
		protected function isAdminRequired() { return FALSE; }
		
		protected function isValidPath($method, $path) {
			return FALSE;
		}
		
		public function response() {
			return $this->env->response();
		}
		
		public function processRequest() {
			switch($this->request->method()) {
				case Request::METHOD_GET:
					$this->processGet();
					break;
				case Request::METHOD_PUT:
					$this->processPut();
					break;
				case Request::METHOD_POST:
					$this->processPost();
					break;
				case Request::METHOD_DELETE:
					$this->processDelete();
					break;
				default:
					throw new RequestException("Unsupported method '".$this->request->method()."'");
			}
		}
		
		function processGet() { throw new ServiceException("INVALID_REQUEST", "Unimplemented method 'get'"); }
		
		function processPut() { throw new ServiceException("INVALID_REQUEST", "Unimplemented method 'put'"); }
		
		function processPost() { throw new ServiceException("INVALID_REQUEST", "Unimplemented method 'post'"); }
		
		function processDelete() { throw new ServiceException("INVALID_REQUEST", "Unimplemented method 'delete'"); }
		
		protected function item($id) {
			return $this->env->filesystem()->item($id);
		}
		
		protected function invalidRequestException($details = NULL) {
			return new ServiceException("INVALID_REQUEST", "Invalid ".get_class($this)." request: ".strtoupper($this->request->method())." ".$this->request->URI().($details != NULL ? (" ".$details): ""));
		}
		
		function log() {
			if (!Logging::isDebug()) return;
			Logging::logDebug("SERVICE (".get_class($this)."): is_auth_required=".$this->isAuthenticationRequired($this->request));
		}
	}
?>