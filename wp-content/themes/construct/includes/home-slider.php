<!--BEGIN .item.featured -->
<div class="item featured" data-order="0">

	<?php

		$slider_enabled = get_option('dt_slider', 'true');
		$slider_type = get_option('dt_posts_from', 'categories'); //categories, tags or any
		$slider_order = get_option('dt_slider_order', 'comment_count'); //comment_count, date or rand
		$slider_number = get_option('dt_slider_number', '6'); //number of slides

		$slider_auto = get_option('dt_slider_auto'); //auto slide

		$dt_slider_head = get_option('dt_slider_head', 'Featured');

		if($slider_auto == '') {
			$slider_auto = 0;
		}

		$args = array();

		//Post type
		if($slider_type == "categories"):
			$categories = get_option('dt_slider_categories');
			$args['category__in']=$categories;
		elseif($slider_type == "tags"):
			$tags = get_option('dt_slider_tags');
			$args['tag__in']=$tags;
		endif;

		//Number of posts
		$args['posts_per_page'] = $slider_number;

		//Order by
		$args['orderby']= $slider_order;

		$query = new WP_Query($args);

		$loader = '/images/ajax-loader.gif';

	?>


	<!--BEGIN #slides-->
	<div id="slides" data-img="<?php echo get_template_directory_uri() . $loader; ?>" data-auto="<?php echo $slider_auto; ?>">

		<!--BEGIN .slides_container -->
		<div class="slides_container">

			<?php if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post(); ?>

			<!--BEGIN .hentry -->
			<div <?php post_class(); ?> id="featured-<?php the_ID(); ?>">

				<div class="featured-details">

					<div class="inner">
						<span class="meta-category"><?php echo stripslashes(htmlspecialchars_decode($dt_slider_head)); ?></span>
						
						<h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?><span class="plus"><span>+</span></span></a></h2>
						
						<!--BEGIN .post-content -->
						<div class="post-content">
							<?php dt_excerpt(20); ?>
						<!--END .post-content -->
						</div>
						
						<a class="read-more" href="<?php the_permalink(); ?>"><?php _e('Read More', 'engine'); ?><span class="plus"><span>+</span></span></a>
					</div>

				<!--END .featured-details -->
				</div>

				<!--BEGIN .featured-image -->
				<div class="featured-image">

					<?php if ( has_post_thumbnail() ) : ?>
						<?php dt_overlay_icon(true); ?>
						<a href="<?php the_permalink(); ?>"><?php dt_image(400, 390); ?></a>
					<?php endif; ?>

				<!--END .featured-image -->
				</div>

			<!--END .hentry-->
			</div>

			<?php endwhile; endif; ?>

		<!--END .slides_container-->
		</div>

		<!--BEGIN .slide-cntrols-->
		<div id="slide-controls">

			<a href="#" class="next"></a>
			<a href="#" class="prev"></a>

		<!--END .slide-cntrols-->
		</div>

	<!--END #slides -->
	</div>

<!--END .item.featured -->
</div>