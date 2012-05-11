<?php

add_filter( 'show_admin_bar', '__return_false' );

function load_typekit(){
	?>
	<script type="text/javascript" src="http://use.typekit.com/xos2ccs.js"></script>
<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
	<?php
}
add_action('wp_head', 'load_typekit');

function remove_scripts() {
	wp_dequeue_script('tabs');
	wp_dequeue_script('dt_custom');
	wp_register_script('toolbox', get_stylesheet_directory_uri() . '/javascripts/toolbox.js', 'jquery', '1.0', TRUE);
	wp_enqueue_script('toolbox');
}
add_action('init','remove_scripts', 100);

function remove_more_scripts(){
	wp_dequeue_script('easing');
	wp_dequeue_script('isotope');
	wp_dequeue_script('slidesjs');
	wp_dequeue_script('colorbox');
	wp_dequeue_script('fitvids');
	wp_dequeue_script('imagesLoaded');
}
add_action('wp_print_scripts', 'remove_more_scripts', 100);


?>