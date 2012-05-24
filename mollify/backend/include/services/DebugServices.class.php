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

	class DebugServices extends ServicesBase {
		protected function isValidPath($method, $path) {
			return TRUE;
		}
		
		protected function isAuthenticationRequired() {
			return TRUE;
		}
		
		protected function isAdminRequired() {
			return TRUE;
		}

		public function processGet() {
			if (!$this->env->settings()->hasSetting("debug_log"))
				$this->response()->html("<html><body><h1>Mollify Debug</h1><p>No debug log file specified in configuration.php.</p><p>Add setting <code>debug_log</code> with absolute path to the log file. NOTE! PHP must have read and write permissions to this file.</body></html>");
			else if (!file_exists($this->env->settings()->setting("debug_log")))
				$this->response()->html("<html><body><h1>Mollify Debug</h1><p>Debug log file not found (<code>".$this->env->settings()->setting("debug_log")."</code>).</p><p>This means that either no log has been generated (log is emptied after viewed by this viewer), or PHP has no read/write permissions to this file.</p></body></html>");
			else
				$this->response()->html($this->getDebugHtml());
		}
		
		private function getDebugHtml() {
			$log = $this->env->settings()->setting("debug_log");
			$html = "<html><body><h1>Mollify Debug</h1><p>";
			
			$handle = @fopen($log, "rb");
			if (!$handle) {
				$html .= "Cannot read log file ".$log;
				return $html;
			}

			$html .= "<p><code>";
			while (!feof($handle)) {
				$html .= htmlspecialchars(fread($handle, 1024), ENT_QUOTES);
			}
			fclose($handle);
			unlink($log);
			
			return $html."</code></p></body></html>";
		}
		
		public function __toString() {
			return "DebugServices";
		}
	}
?>
