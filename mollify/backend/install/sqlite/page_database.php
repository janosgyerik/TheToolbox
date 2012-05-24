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
	
	<body id="page-database">
		<?php pageBody("Installation", "1/2 Database Information"); ?>

		<div class="content">
			<p>
				Mollify will be installed in following SQLite database: <code><?php echo realpath($installer->db()->file()); ?></code>
			</p>
			<p>
				If this database is correct, click "Continue Installation". Otherwise, modify the configuration file and click "Refresh Configuration".
			</p>
			<p>
				<a id="button-continue" href="#" class="btn green">Continue Installation</a>
				<a id="button-refresh" href="#" class="btn blue">Refresh Configuration</a>
			</p>
		</div>
		<?php pageFooter(); ?>
	</body>
	
	<script type="text/javascript">
		function init() {
			$("#button-refresh").click(function() {
				action("refresh");
			});
			$("#button-continue").click(function() {
				action("continue_db");
			});
		}
	</script>
</html>