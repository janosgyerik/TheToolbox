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

	class Util {
		public static function inBytes($a) {
		    $amount = trim($a);
		    $last = strtolower($amount[strlen($amount)-1]);
		    
		    switch ($last) {
		        case 'g':
		            $amount *= 1024;
		        case 'm':
		            $amount *= 1024;
		        case 'k':
		            $amount *= 1024;
		    }

		    return (float)$amount;
		}
		
		function base64_url_encode($input) {
			return strtr(base64_encode($input), '+/=', '-_,');
		}

		function base64_url_decode($input) {
			return base64_decode(strtr($input, '-_,', '+/='));
		}
		
		static function toString($a) {
			if (is_array($a)) return self::array2str($a);
			return $a;
		}
		
		static function array2str($a, $ignoredKeys = NULL) {
			if ($a === NULL) return "NULL";
			
			$r = "{";
			$first = TRUE;
			foreach($a as $k=>$v) {
				if ($ignoredKeys != null and in_array($k, $ignoredKeys)) continue;
				
				if (!$first) $r .= ", ";
				
				$val = $v;
				if (is_array($v)) $val = self::array2str($v);
				
				$r .= $k.':'.$val;
				$first = FALSE;
			}
			return $r."}";
		}
		
		static function convertArrayCharset($a) {
			$result = array();
			foreach($a as $k=>$v) {
				if (is_array($v)) $result[$k] = self::convertArrayCharset($v);
				else $result[$k] = self::convertCharset($v);
			}
			return $result;
		}
		
		static function convertCharset($v, $charset = NULL, $encode = TRUE) {
			if (!$charset or $charset === NULL) {
				if ($encode)
					return utf8_encode($v);
				return utf8_decode($v);
			}
			$from = $encode ? $charset : 'UTF-8';
			$to = $encode ? 'UTF-8' : $charset;
			return iconv($from, $to, $v);
		}
		
		static function replaceParams($text, $values) {
			foreach($values as $k => $v)
				$text = str_replace('%'.$k.'%', $v, $text);
			return $text;
		}
	}
?>