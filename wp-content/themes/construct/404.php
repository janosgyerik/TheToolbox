<?php get_header(); ?>

	<!--BEGIN #content -->
    <div id="content">
    	
		<!--BEGIN .hentry -->
		<div class="hentry item">

			<h2 class="post-title"><?php _e('Error 404', 'engine'); ?></h2>
			
			<!--BEGIN .post-content -->
			<div class="post-content">
			
				<p><?php _e('The page you are looking for cannot be found.', 'engine'); ?></p>
				
				<!-- .search-wrap -->
				<div class="search-wrap clearfix">
					<?php get_search_form(); ?>
				</div>
				<!-- /.search-wrap -->
				
				<?php echo do_shortcode('[sitemap]'); ?>
			<!--END .post-content -->
			</div>
			
		<!--END .hentry-->  
		</div>

	</div><!-- #content -->

<?php get_footer(); ?>