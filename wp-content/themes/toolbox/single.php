<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<?php
wp_redirect( get_post_meta(get_the_ID(), 'dt_video', true) );
exit;
?>
<?php endwhile; else : ?>

	<p><?php _e('No posts found', 'engine'); ?></p>

<?php endif; ?>