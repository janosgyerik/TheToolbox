<?php get_header(); ?>

	<!--BEGIN #content -->
    <div id="content">
    	
    	<!--BEGIN #masonry -->	
		<div id="masonry">

			<?php 
			$i=0;
			$slider = get_option('dt_slider'); 
			
			if($slider == 'true') get_template_part('includes/home-slider'); 
			
			?>
			
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<?php 
			$i++; 
			$tool_url = get_post_meta(get_the_ID(), 'dt_video', true);
			?>
			<!--BEGIN .item -->	
			<div class="item normal" data-order='1'>
			
				<!--BEGIN .hentry -->
				<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">

					<!--BEGIN .featured-image -->
					<div class="featured-image">

						<?php dt_overlay_icon(); ?>
						
						<a href="<?php echo $tool_url; ?>" target="_blank"><?php dt_image(300, 180); ?></a>
						
					<!--END .featured-image -->
					</div>
					
					<?php $format = get_post_format(); ?>
					
					<span class="meta-category icon-<?php echo $format; ?>"><?php the_category(', '); ?></span>
					
					<h2 class="post-title"><a href="<?php echo $tool_url; ?>" target="_blank"><?php the_title(); ?></a></h2>
					
					<!--BEGIN .post-content -->
					<div class="post-content">
						<?php dt_excerpt(20); ?>
					<!--END .post-content -->
					</div>
										
					<span class="meta-published"><?php echo human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' '.  __('ago', 'engine'); ?></span>
	
				<!--END .hentry-->  
				</div>
			
			<!--END .item -->	
			</div>
			<hr class="separator"/>
			<?php if($i==5){
				$i++;
				?>
				<div class="ad-block item normal" data-order='1'>
					<div class="ad">
						<div id="fusion_ad">
							<span class="fusionentire">
								<a href="http://sachagreif.com/ebook" title="" target="_blank">
									<img src="<?php echo get_stylesheet_directory_uri();?>/images/ebook-banner.png" class="fusionimg" alt="My eBook will show you how to design a web app from scratch for only $5.99." border="0" height="100" width="130">
								</a>
								<a href="http://sachagreif.com/ebook" class="fusiontext" title="My eBook will show you how to design a web app from scratch, for only $5.99." target="_blank">My eBook will show you how to design a web app from scratch, for only $5.99.</a>
							</span>
							<!-- <a class="fusion-link" href="http://fusionads.net">Powered by Fusion</a> -->
						</div>
					</div>
				</div>
				<hr class="separator"/>
			<?php } ?>
			<?php endwhile; endif; ?>
			
			<?php get_template_part('includes/index-loadmore'); ?>
			<hr class="separator"/>
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