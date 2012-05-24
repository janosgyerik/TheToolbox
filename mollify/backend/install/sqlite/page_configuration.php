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
	 
	 include("install/installation_page.php");
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
	<?php pageHeader("Mollify Installation", "init"); ?>
	
	<body id="page-mysql-configuration">
		<?php pageBody("Installation", "SQLite Database Configuration"); ?>
		<?php if ($installer->action() === 'continue') { ?>
		<div class="error">
			<div class="details">
				SQLite database file is not set.
			</div>
		</div>
		<?php } ?>
		
		<div class="content">
			<p>
				Installer needs the SQLite database file location set in "<code>configuration.php</code>":
				
				For more information, see <a href="http://code.google.com/p/mollify/wiki/Installation">Installation instructions</a>.
			</p>
			<p>	
				An example configuration:
				<div class="example code">
					&lt;?php<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;$CONFIGURATION_TYPE = &quot;<span class="value">sqlite</span>&quot;;<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;$DB_FILE = &quot;<span class="value">[SQLITE_DB_FILE]</span>&quot;;<br/>
					?&gt;
				</div>
			</p>
			<p>
				When the configuration is updated, click "Continue".
			</p>
			<p>
				<a id="button-continue" href="#" class="btn">Continue</a>
			</p>			
		</div>
		
		<?php pageFooter(); ?>
	</body>
	
	<script type="text/javascript">
		function init() {
			$("#button-continue").click(function() {
				action("continue");
			});
		}
	</script>
</html>