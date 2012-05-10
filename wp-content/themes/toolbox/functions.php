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
	remove_action('wp_print_scripts', 'dt_theme_script_enqueues', 0);
	wp_dequeue_script('isotope');
	wp_dequeue_script('slidesjs');
	wp_dequeue_script('colorbox');
	wp_dequeue_script('fitvids');
	wp_dequeue_script('imagesLoaded');
}
add_action('after_setup_theme','remove_scripts');


?>