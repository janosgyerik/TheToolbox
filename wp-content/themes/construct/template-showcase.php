<?php
/*
Template Name: Showcase
*/

get_header(); 

?>

	<!--BEGIN #content -->
    <div id="content">
    	
    	<!--BEGIN #masonry -->	
		<div id="masonry">
		
			<?php 
			
			$all_terms = get_terms( 'group', array('hide_empty' => 0 ) );
			
			$count = 1;
			
			query_posts(array(
				'post_type' => 'showcase',
				'posts_per_page' => -1
			)); 
			
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
						wp_list_categories( array(
								'taxonomy' => 'group',
								'hide_empty' => 0,
								'title_li' => '',
								'depth' => 1,
								'walker' => new Group_Walker(),
								'show_count' => 1
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