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
	 
	include("installation_page.php");
	global $CONFIGURATION_TYPE;
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
	<?php pageHeader("Mollify Installation", "init"); ?>
	
	<body id="install-instructions-type">
		<?php pageBody("Installation", "Welcome to Mollify Installer"); ?>
		<?php if (isset($CONFIGURATION_TYPE)) { ?>
		<div class="error">
			<div class="title">	
				Configuration type value is invalid.
			</div>
			<div class="details">
				Configuration type "<code><?php echo($CONFIGURATION_TYPE); ?></code>" is invalid. For more information, see <a href="http://code.google.com/p/mollify/wiki/Installation" target="_blank">installation instructions</a>.
			</div>
		</div>
		<?php } ?>
		
		<div class="content">
			<?php if (!isset($CONFIGURATION_TYPE)) { ?>
			<p>
				To continue with Mollify installation, you have to setup the configuration.
			</p>
			<?php } ?>
	
			<p>
				Edit the configuration file <code>configuration.php</code> by adding the configuration type variable, for example:
				<div class="example code">
					&lt;?php<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;$CONFIGURATION_TYPE = &quot;<span class="value">[ENTER VALUE HERE]</span>&quot;;<br/>
					?&gt;<br/>
				</div>
			</p>
			<p>
				Possible values are:
				<ul>
					<li>"<code>mysql</code>" for MySQL</li>
					<li>"<code>sqlite</code>" for SQLite</li>
				</ul>
				
				When this is added, click "Continue". For more information about the installation, see <a href="http://code.google.com/p/mollify/wiki/Installation" target="_blank">installation instructions</a>.

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
				action("retry");
			});
		}
	</script>
</html>