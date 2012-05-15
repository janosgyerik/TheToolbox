<?php
get_header(); 
$total = $wp_query->post_count;
?>

<div class="top clearfix">
	<div class="filters">
		<ul id="sort-by" class="filter">
			<li class="label">Sort by:</li>
			<li class="active"><a href="#order">Date Added</a></li>
			<li><a href="#name">Name</a></li>
			<li><a href="#votes">Most Used</a></li>
		</ul>
		<ul id="filter-by" class="filter">
			<li class="label">Show: </li>		
			<li class="active"><a href="/" data-filter="all"><?php _e('All', 'engine'); ?> <span class="count">(<?php echo $total; ?>)</span></a></li>
			<?php 
			wp_list_categories( array(
					'hide_empty' => 1,
					'title_li' => '',
					'depth' => 1,
					'walker' => new Site_Walker(),
					'show_count' => 1
				) 
			); 
			?> 			
		</ul>
	</div>

	<div class="ad">
		<!-- Yoggrt Zone Code -->
		<div id="bsap_1275456" class="bsarocks bsap_ab4a3f708e0d8c0162387aae5506bc7c"></div>
		<a href="http://yoggrt.com" id="bsap_aplink">ads by Yoggrt</a>
		<!-- End Yoggrt Zone Code -->
	</div>
</div>
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
		<div <?php post_class("item"); ?> id="post-<?php the_ID(); ?>" data-order='<?php echo $i; ?>' >
		
			<!--BEGIN .hentry -->

				<!--BEGIN .featured-image -->
				<div class="featured-image">

					<?php dt_overlay_icon(); ?>
					
					<a href="<?php echo $tool_url; ?>" target="_blank"><?php the_post_thumbnail( "full" ); ?></a>
					
				<!--END .featured-image -->
				</div>
				
				<?php $format = get_post_format(); ?>
				<div class="inner">

					<span class="meta-category icon-<?php echo $format; ?>"><?php the_category(', '); ?></span>
					
					<h2 class="post-title"><a href="<?php echo $tool_url; ?>" target="_blank"><?php the_title(); ?></a></h2>
					
					<!--BEGIN .post-content -->
					<div class="post-content">
						<?php dt_excerpt(20); ?>
					<!--END .post-content -->
					</div>
					<?php do_action("insert_rating"); ?> 
			
					<span class="meta-published"><?php echo human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' '.  __('ago', 'engine'); ?></span>
	
				</div>
		
		<!--END .item -->	
		</div>
		<?php if(false){?>
			<hr class="separator"/>
		<?php } ?>
		<?php if(false && $i==5){
			$i++;
			?>
			<div class="ad-block item" data-order='1'>
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
