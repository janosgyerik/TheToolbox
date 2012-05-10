<?php get_header(); ?>
	
	<!-- .search-wrap -->
	<div class="search-wrap clearfix">
		<?php get_search_form(); ?>
	</div>
	<!-- /.search-wrap -->

	<!--BEGIN #content -->
    <div id="content">
    	
    	<!-- #archive-wrap -->
    	<div id="archive-wrap">
    		
    		<!--BEGIN .item -->	
			<div class="item archive-title-item" data-order='1'>

					<h1 id="page-title"><?php _e('Search Results', 'engine') ?> <?php echo $_GET['s']; ?></h1>
			
			<!--END .item -->
			</div>
			
			<?php get_sidebar(); ?>

    	</div>
    	<!-- /#archive-wrap -->
 	
    	<!--BEGIN #masonry -->	
		<div id="masonry">
					
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			
			<!--BEGIN .item -->	
			<div class="item normal" data-order='1'>
			
				<!--BEGIN .hentry -->
				<div <?php post_class(); ?> id="featured-<?php the_ID(); ?>">

					<!--BEGIN .featured-image -->
					<div class="featured-image">

						<?php dt_overlay_icon(); ?>
						
						<?php if ( has_post_thumbnail() ) : ?>
							<a href="<?php the_permalink(); ?>"><?php dt_image(300, 180); ?></a>
						<?php endif; ?>
						
					<!--END .featured-image -->
					</div>
					
					<?php $format = get_post_format(); ?>
					
					<span class="meta-category icon-<?php echo $format; ?>"><?php the_category(', '); ?></span>
					
					<h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?><span class="plus"><span>+</span> </span></a></h2>
					
					<!--BEGIN .post-content -->
					<div class="post-content">
						<?php dt_excerpt(20); ?>
					<!--END .post-content -->
					</div>
					
					<a class="read-more" href="<?php the_permalink(); ?>"><?php _e('Read More', 'engine'); ?><span class="plus"><span>+</span> </span></a>
					
					<span class="meta-published"><?php echo human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' '.  __('ago', 'engine'); ?></span>
	
				<!--END .hentry-->  
				</div>
			
			<!--END .item -->	
			</div>
			<?php endwhile; else: ?>
				
				<div class="item none">
					<div class="hentry">
						<p><?php _e('No posts were found.', 'engine'); ?></p>
					</div>
				</div>
								
			<?php endif; ?>
			
			<?php get_template_part('includes/index-loadmore'); ?>
					
		<!--END #masonry -->
		</div>
		
		<div id="masonry-new"></div>
		
		<!--BEGIN .post-navigation -->
		<div class="post-navigation clearfix">
			<?php dt_pagination(); ?>
		<!--END .post-navigation -->
		</div>

	</div><!-- #content -->

<?php get_footer(); ?>