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
	 			
	 class FilesystemSearcher extends BaseSearcher {
	 	private $env;
	 	private $searchDescriptions;
	 	
		function __construct($env) {
			$this->env = $env;
			$this->searchDescriptions = $env->features()->isFeatureEnabled("descriptions");
		}
		
		protected function getMatch($data, $item, $text) {
			$result = array();
			if (stripos($item->name(), $text) !== FALSE)
				$result[] = array("type" => "name");
			if ($this->searchDescriptions and array_key_exists($item->id(), $data))
				$result[] = array("type" => "description", "description" => $data[$item->id()]);
			return $result;
		}
		
		public function preData($parent, $text) {
			if (!$this->searchDescriptions) return NULL;
			$descMatches = $this->env->configuration()->findItemsWithDescription($parent, $text, TRUE);
			//Logging::logDebug(Util::array2str($descMatches));
			return $descMatches;
		}
	}
?>