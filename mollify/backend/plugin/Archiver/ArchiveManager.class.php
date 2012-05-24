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

	class ArchiveManager {
		public function extract($archive, $to) {
			$zip = new ZipArchive;
			if ($zip->open($archive) !== TRUE)
				throw new ServiceException("REQUEST_FAILED", "Could not open archive ".$archive);
			
			$zip->extractTo($to);
			$zip->close();
		}
			
		public function __toString() {
			return "ArchiverManager";
		}
	}
?>