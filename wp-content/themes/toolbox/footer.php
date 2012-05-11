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
				<p>Made by <a href="http://sachagreif.com">Sacha Greif</a> - Copyright <?php echo date('Y'); ?> <?php bloginfo('title'); ?> - <a href="http://designerthemes.com/themes/<?php echo strtolower(DT_THEME_NAME) ?>/" title="<?php echo DT_THEME_NAME; ?> Theme by DesignerThemes.com"><?php echo DT_THEME_NAME; ?> Theme</a> by <a href="http://designerthemes.com/" title="Premium WordPress Themes">DesignerThemes.com</a></p>
			<!--END #credits -->
			</div>
		
		</div>
		<!-- /#footer-inner.clearfix -->

	<!--END #footer -->
	</div>

<!--END #bottom -->

</div>
<div class="overlay hidden"></div>

<script> // for contact form
	var ajaxurl='<?php echo admin_url('admin-ajax.php'); ?>';
</script>

<?php wp_footer(); ?>

<script src="//static.getclicky.com/js" type="text/javascript"></script>
<script type="text/javascript">try{ clicky.init(66582887); }catch(e){}</script>
<noscript><p><img alt="Clicky" width="1" height="1" src="//in.getclicky.com/66582887ns.gif" /></p></noscript>

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/fr_FR/all.js#xfbml=1&appId=120226234679972";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<!-- Place this render call where appropriate -->
<script type="text/javascript">
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>

</body>

</html>