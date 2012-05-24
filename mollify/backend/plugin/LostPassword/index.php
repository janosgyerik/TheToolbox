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
	 
	$PATH = "../../";
	
	if (!file_exists($PATH."configuration.php")) die("Mollify not configured");
?>
<html>
	<head>
		<title>Reset password</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		
		<link rel="stylesheet" href="<?php echo $PATH ?>resources/style.css">
		<link rel="stylesheet" href="pages/style.css">

		<script type="text/javascript" src="<?php echo $PATH ?>resources/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="<?php echo $PATH ?>resources/json.js"></script>
		<script type="text/javascript" src="<?php echo $PATH ?>resources/template.js"></script>
		<script type="text/javascript" src="pages/password.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				init('<?php echo $PATH ?>');
			});
		</script>
	</head>

	<body>
		<div id="reset-password-form" style="display:none">
			<div class="title">
				To reset your password, enter your email address and click "Reset".
			</div>
			<div class="reset-password-form-field">
				<div class="reset-password-field-title">E-mail:</div>
				<input type="text" id="email-field" class="reset-password-field"></input>
				<div id="email-hint" class="reset-password-field-hint"></div>
			</div>
			<div class="buttons">
				<a id="reset-button" href="#" class="btn">Reset</a>
			</div>
		</div>
	</body>
</html>