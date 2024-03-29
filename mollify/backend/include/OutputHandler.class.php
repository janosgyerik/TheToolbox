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

	class OutputHandler {
		private $codes = array(  
			100 => 'Continue',  
			101 => 'Switching Protocols',  
			200 => 'OK',  
			201 => 'Created',  
			202 => 'Accepted',  
			203 => 'Non-Authoritative Information',  
			204 => 'No Content',  
			205 => 'Reset Content',  
			206 => 'Partial Content',  
			300 => 'Multiple Choices',  
			301 => 'Moved Permanently',  
			302 => 'Found',  
			303 => 'See Other',  
			304 => 'Not Modified',  
			305 => 'Use Proxy',  
			306 => '(Unused)',  
			307 => 'Temporary Redirect',  
			400 => 'Bad Request',  
			401 => 'Unauthorized',  
			402 => 'Payment Required',  
			403 => 'Forbidden',  
			404 => 'Not Found',  
			405 => 'Method Not Allowed',  
			406 => 'Not Acceptable',  
			407 => 'Proxy Authentication Required',  
			408 => 'Request Timeout',  
			409 => 'Conflict',  
			410 => 'Gone',  
			411 => 'Length Required',  
			412 => 'Precondition Failed',  
			413 => 'Request Entity Too Large',  
			414 => 'Request-URI Too Long',  
			415 => 'Unsupported Media Type',  
			416 => 'Requested Range Not Satisfiable',  
			417 => 'Expectation Failed',  
			500 => 'Internal Server Error',  
			501 => 'Not Implemented',  
			502 => 'Bad Gateway',  
			503 => 'Service Unavailable',  
			504 => 'Gateway Timeout',  
			505 => 'HTTP Version Not Supported'  
        );
        
        private $defaultMimeTypes = array(
			'ogg' => 'audio/ogg',
			'oga' => 'audio/ogg',
			'mov' => 'video/quicktime',
			'mp3' => 'audio/mpeg',
			'm4a' => 'audio/mp4',
			'webma' => 'audio/webm',
			'mp4' => 'video/mp4',
			'aif' => "audio/x-aiff",
			'wav' => "audio/wav",
			'divx' => "video/divx",
			'avi' => "video/divx",
			'mkv' => "video/divx",
			'pdf' => "application/pdf",
			'jpg' => 'image/jpeg',
			"doc" => "application/msword",
			"html" => "text/html",
			"docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
			"xls" => "application/vnd.ms-excel",
			"xlsx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
			"ppt" => "application/vnd.ms-powerpoint",
			"pptx" => "application/vnd.openxmlformats-officedocument.presentationml.presentation"
        );
        
        private $mimeTypes;
        private $supportOutputBuffer;
        
		function __construct($mimeTypes = array(), $supportOutputBuffer = FALSE) {
			$this->mimeTypes = $mimeTypes;
			$this->supportOutputBuffer = $supportOutputBuffer;
		}
		
		public function sendResponse($response) {
			header($this->getStatus($response));
			header("Cache-Control: no-store, no-cache, must-revalidate");
			
			$data = $response->data();
			if (!$data) return;
			
			if ($response->type() === 'json') {
				header('Content-type: application/json');
				$encoded = json_encode($data);
				header('Content-length: '.strlen($encoded));
				echo $encoded;
			} else {
				header('Content-type: text/html');
				echo $data;
			}
		}
		
		public function downloadBinary($filename, $type, $mobile, $stream, $size = NULL, $range = NULL) {
			if ($range) {
				$start = $range[0];
				$end = $range[1];
				$size = $range[2];
				
				if ($start > 0 || $end < ($size - 1))
					header('HTTP/1.1 206 Partial Content');
				header("Cache-Control:");
				header("Cache-Control: public");
				header('Accept-Ranges: bytes');
				header('Content-Range: bytes '.$start.'-'.$end.'/'.$size);
				header('Content-Length: '.($end - $start + 1));
			} else {
				if ($size) header("Content-Length: ".$size);
				header("Cache-Control: public, must-revalidate");
				header("Content-Type: application/octet-stream");
				if (!$mobile) {
					header("Content-Type: application/force-download");	// mobile browsers don't like these
					header("Content-Type: application/download");
				}
				header("Content-Disposition: attachment; filename=\"".$filename."\";");
				header("Content-Transfer-Encoding: binary");
				header("Pragma: hack");
			}

			Logging::logDebug("Sending $filename ($size)");			
			if ($range) fseek($stream, $range[0]);

			$this->doSendBinary($stream);
		}

		public function sendBinary($filename, $type, $stream, $size = NULL) {
			if ($size) header("Content-Length: ".$size);
			header("Content-Type: ".$this->getMime(trim(strtolower($type))));
			
			$this->doSendBinary($stream);
		}
		
		private function doSendBinary($stream) {
			//if ($this->supportOutputBuffer) ob_start();
			$count = 0;
			while (!feof($stream)) {
				$count = $count + 1;
				set_time_limit(0);
				echo fread($stream, 1024);
				if ($this->supportOutputBuffer and ob_get_length() != FALSE) @ob_flush();
				flush();
			}
			fclose($stream);
			Logging::logDebug("Sent $count chunks");
		}

		public function redirect($url) {
			header("Location: ".$url);
		}
				
		private function getStatus($response) {
			return 'HTTP/1.1 '.$response->code().' '.$this->codes[$response->code()];
		}
		
		private function getMime($type) {
			if (array_key_exists($type, $this->mimeTypes)) return $this->mimeTypes[$type];
			if (array_key_exists($type, $this->defaultMimeTypes)) return $this->defaultMimeTypes[$type];
			return 'application/octet-stream';
		}
		
		public function __toString() {
			return "OutputHandler";
		}
	}
?>