			<!--END #content -->
			</div>

		<!--END #main -->
		</div>

	<!--END #page -->
    </div>

<!--END #wrapper -->
</div>

<!--BEGIN #bottom -->
<div id="bottom">

	<!--BEGIN #footer -->
	<div id="footer">
		
		<!-- #footer-inner.clearfix -->
		<div id="footer-inner" class="clearfix">
		
			<!--BEGIN #footer-menu -->
			<div id="footer-menu" class="clearfix">
				<?php if ( has_nav_menu( 'footer-menu' ) ) : wp_nav_menu( array( 'theme_location' => 'footer-menu', 'depth' => '1' ) ); endif; ?>
			<!--END #footer-menu -->
			</div>
	
			<!--BEGIN #credits -->
			<div id="credits">
				<p>Copyright <?php echo date('Y'); ?> <?php bloginfo('title'); ?> - <a href="http://designerthemes.com/themes/<?php echo strtolower(DT_THEME_NAME) ?>/" title="<?php echo DT_THEME_NAME; ?> Theme by DesignerThemes.com"><?php echo DT_THEME_NAME; ?> Theme</a> by <a href="http://designerthemes.com/" title="Premium WordPress Themes">DesignerThemes.com</a></p>
			<!--END #credits -->
			</div>
		
		</div>
		<!-- /#footer-inner.clearfix -->

	<!--END #footer -->
	</div>

<!--END #bottom -->
</div>

<script> // for contact form
	var ajaxurl='<?php echo admin_url('admin-ajax.php'); ?>';
</script>

<?php wp_footer(); ?>

</body>

</html>