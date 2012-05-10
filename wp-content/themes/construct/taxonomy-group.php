<?php get_header(); ?>

	<!--BEGIN #content -->
    <div id="content">
    	
    	<!--BEGIN #masonry -->	
		<div id="masonry">
		
			<?php 
			
			$all_terms = get_terms( 'group', array('hide_empty' => 0 ) );
			
			$count = 1;
			
			$wp_query->set( 'posts_per_page', 9999 );
			$wp_query->query($wp_query->query_vars);
			$total = $wp_query->post_count;
			
			?>
			
			<!--BEGIN .item -->	
			<div class="item groups <?php if($all_terms) : foreach ($all_terms as $term) { echo 'term-'.$term->term_id.' '; } endif; ?>" data-order='1'>
				
				<!--BEGIN .inner -->	
				<div class="inner">
				
					<h1><?php the_title(); ?></h1>
					
					<ul id="filter" class="clearfix">
					
						<li class="active"><a href="#" data-filter="*"><?php _e('All', 'engine'); ?></a> (<?php echo $total; ?>)</li>
						<?php 
						
						$term_id = get_query_var('term');
			
						$groups = get_term_by('slug', $term_id, 'group');
						
						//print_r($groups);
						
						$id =  $groups->term_id;
			
						wp_list_categories( array(
								'taxonomy' => 'group',
								'hide_empty' => 0,
								'depth' => 1,
								'hierarchical' => 1,
								'title_li' => '',
								'walker' => new Group_Walker(),
								'show_count' => 1,
								'child_of' => $id,
								'show_option_none' => ''
							) 
						); 
						?> 
						
					</ul>
					
				<!--END .inner -->
				</div>
				
			<!--END .item -->	
			</div>
			
			
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			
			<?php $terms = get_the_terms( get_the_ID(), 'group' ); ?>
			
			<!--BEGIN .item -->	
			<div data-order='1' data-id="id-<?php echo $count; ?>" class="item normal <?php if($terms) : foreach ($terms as $term) { echo 'term-'.$term->term_id.' '; } endif; ?>">
			
				<!--BEGIN .hentry -->
				<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">

					<!--BEGIN .featured-image -->
					<div class="featured-image">

						<?php dt_overlay_icon(); ?>
						
						<?php if ( has_post_thumbnail() ) : ?>
							<a href="<?php the_permalink(); ?>"><?php dt_image(300, ''); ?></a>
						<?php endif; ?>
						
					<!--END .featured-image -->
					</div>
					
					<?php $format = get_post_format(); ?>
					
					<h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?><span class="plus"><span>+</span> </span></a></h2>
					
					<!--BEGIN .post-content -->
					<div class="post-content">
						<?php 
						if ( !empty( $post->post_excerpt ) ) {
							the_excerpt();
						}
						?>
					<!--END .post-content -->
					</div>

				<!--END .hentry-->  
				</div>
			
			<!--END .item -->	
			</div>
			<?php $count++; endwhile; endif; ?>
			
			<?php //get_template_part('includes/index-loadmore'); ?>
					
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