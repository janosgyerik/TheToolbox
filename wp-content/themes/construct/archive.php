<?php get_header(); ?>

	<!--BEGIN #content -->
    <div id="content">
    	
    	<!-- #archive-wrap -->
    	<div id="archive-wrap">
    		
    		<!--BEGIN .item -->	
			<div class="item archive-title-item" data-order='1'>
				
				<?php 
				// Get author data 
				if(get_query_var('author_name')) :
					$curauth = get_userdatabylogin(get_query_var('author_name'));
				else :
					$curauth = get_userdata(get_query_var('author'));
				endif;
				?>	
		 	  	
		 	  	<?php if (is_category()) : ?>
					<h1 id="page-title"><?php echo single_cat_title(); ?></h1>
					<?php
						$cat_desc = category_description();
						if ($cat_desc != '') echo '<div class="cat-desc">'.category_description().'</div>';
					?>
					
		 	  	<?php elseif( is_tag() ) : ?>
					<h1 id="page-title"><?php echo single_tag_title(); ?></h1>
					
		 	  	<?php elseif (is_day()) : ?>
					<h1 id="page-title"><?php the_time('F jS, Y'); ?></h1>
					
		 	 	<?php elseif (is_month()) : ?>
					<h1 id="page-title"><?php the_time('F, Y'); ?></h1>
					
		 		<?php elseif (is_year()) : ?>
					<h1 id="page-title"><?php the_time('Y'); ?></h1>
					
			  	<?php elseif (is_author()) : ?>
					<h1 id="page-title"><?php echo $curauth->display_name; ?></h1>
					
		 	  	<?php elseif (isset($_GET['paged']) && !empty($_GET['paged'])) : ?>
					<h1 id="page-title"><?php _e('Blog Archives', 'engine') ?></h1>
					
				<?php elseif (is_search()) : ?>
					<h1 id="page-title"><?php _e('Search Results', 'engine') ?> <?php echo $_GET['s']; ?></h1>
					
				<?php endif; ?>
			
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
			<?php endwhile; endif; ?>
			
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