<?php 
global $wp_query;
$found_posts = $wp_query->found_posts;
$per_page = get_option('posts_per_page');
$post_count = $found_posts - $per_page;

if($found_posts > $per_page) :
?>

<!--BEGIN #load-more.item -->	
<div class="item" id="load-more" data-order='999'>

	<a id="load-more-link" href="#">
	
		<span id="plus"></span>
		
		<span id="detail-holder">
			<span class="count-text"><span class="count"><?php echo $post_count; ?></span> posts remaining</span>
			<span class="load-more-text"><span class="load-more-plus">+</span> <?php _e('Load More', 'engine'); ?></span>
		</span>
		
		<span id="loader" data-perpage="<?php echo $per_page; ?>"></span>
		
	</a>
	
<!--END #load-more.item -->	
</div>

<?php endif; ?>
