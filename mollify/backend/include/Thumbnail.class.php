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

	class Thumbnail {
		function generate($item, $maxWidth = 200, $maxHeight = 200) {
			$img = null;
			$ext = $item->extension();

			if (strcasecmp('jpg', $ext) == 0 || strcasecmp('jpeg', $ext) == 0) {
				$img = @imagecreatefromjpeg($item->internalPath());
			} else if (strcasecmp('png', $ext) == 0) {
				$img = @imagecreatefrompng($item->internalPath());
			} else if (strcasecmp('gif', $ext) == 0) {
				$img = @imagecreatefromgif($item->internalPath());
			}
			if ($img == NULL) {
				Logging::logDebug("Could not create thumbnail, format not supported");
				return FALSE;
			}
	
			$w = imagesx($img);
			$h = imagesy($img);
			$s = min($maxWidth/$w, $maxHeight/$h);
			if ($s >= 1) {
				Logging::logDebug("Skipping thumbnail, image smaller than thumbnail");
				return FALSE;
			}
			
			$tw = floor($s*$w);
			$th = floor($s*$h);
			$thumb = imagecreatetruecolor($tw, $th);
			imagecopyresized($thumb, $img, 0, 0, 0, 0, $tw, $th, $w, $h);
			imagedestroy($img);
			if ($thumb == NULL) {
				Logging::logDebug("Failed to create thumbnail");
				return FALSE;
			}
	
			header("Content-type: image/jpeg");
			imagejpeg($thumb);
			return TRUE;
		}
	}
?>
