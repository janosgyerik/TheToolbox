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

	class MailNotificator {
		private $env;
		private $enabled;
		
		public function __construct($env) {
			$this->env = $env;
			$this->enabled = $env->features()->isFeatureEnabled("mail_notification");
		}
		
		public function send($to, $subject, $message, $from = NULL) {
			if (Logging::isDebug())
				Logging::logDebug("Sending mail to [".Util::array2str($to)."]: [".$message."]");
			
			if ($this->enabled) {
				$f = ($from != NULL ? $from : $this->env->settings()->setting("mail_notification_from"));
				
				$validRecipients = $this->getValidRecipients($to);
				if (count($validRecipients) === 0) {
					Logging::logDebug("No valid recipient email addresses, no mail sent");
					return;
				}
				
				$toAddress = '';
				$headers = 'From:'.$f;
				
				if (count($validRecipients) == 1) {
					$toAddress = $this->getRecipientString($validRecipients[0]);
				} else {
					$headers .= PHP_EOL.$this->getBccHeaders($validRecipients);
				}
				
				mail($toAddress, $subject, str_replace("\n", "\r\n", wordwrap($message)), $headers);
			}
		}

		private function getBccHeaders($recipients) {
			$headers = 'Bcc:';
			$first = TRUE;
			
			foreach ($recipients as $recipient) {
				if (!$first) $headers .= ',';
				$headers .= $this->getRecipientString($recipient);				
				$first = FALSE;
			}
			return $headers;
		}
				
		private function getRecipientString($r) {
			return $r["name"].'<'.$r["email"].'>';
		}
		
		private function getValidRecipients($recipients) {
			$valid = array();
			foreach ($recipients as $recipient) {
				if ($recipient["email"] === NULL or strlen($recipient["email"]) == 0) continue;
				$valid[] = $recipient;
			}
			return $valid;
		}
				
		public function __toString() {
			return "MailNotificator";
		}
	}
?>