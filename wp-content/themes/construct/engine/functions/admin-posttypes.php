<?php

/* ==  Theme Post Types  ======================================================
*
*	   This file contains functions that enable you to
*	   create custom post types with ease. Edit at your own risk.
*
* ============================================================================*/


/* ==  Register Post Type  ==============================*/

function dt_register_post_type( $name, $slug , $supports = array('title', 'editor', 'thumbnail', 'custom-fields', 'excerpt', 'comments'), $labels = null, $exclude_from_search = false ) {
	
	if(!$labels) {
		$labels = array(
			'name' => __( ucfirst($name),'engine'),
			'singular_name' => __( ucfirst($name),'engine' ),
			'add_new' => __('Add New','engine'),
			'add_new_item' => __('Add New ' . ucfirst($name),'engine'),
			'edit_item' => __('Edit ' . ucfirst($name),'engine'),
			'new_item' => __('New ' . ucfirst($name),'engine'),
			'view_item' => __('View ' . ucfirst($name),'engine'),
			'search_items' => __('Search ' . ucfirst($name),'engine'),
			'not_found' =>  __('No ' . ucfirst($name) . ' Found','engine'),
			'not_found_in_trash' => __('No ' . ucfirst($name) . ' Found in Trash','engine'), 
			'parent_item_colon' => ''
		  );
	  }
	  
	  $args = array(
		'labels' => $labels,
		'public' => true,
		'exclude_from_search' => $exclude_from_search,
		'publicly_queryable' => true,
		'show_ui' => true, 
		'query_var' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => null,
		'rewrite' => array('slug' => $slug),
		'supports' => $supports
	  ); 
	  
	  register_post_type( __( strtolower($name) ), $args );

}



/* ==  Register Custom Taxonomy  ==============================*/

function dt_register_taxonomy($name, $slug, $posttype, $hierarchical) {

	register_taxonomy(
		__( $slug, 'engine' ), 
		array( __( strtolower($posttype), 'engine' ) ), 
		array(
			"hierarchical" => $hierarchical,
		 	"label" => __($name, 'engine'), 
		 	"singular_label" => __( ucfirst($name) ), 
		 	"rewrite" => 
			 	array(
			 		'slug' => strtolower($slug), 
			 		'hierarchical' => true
			 	)
		)
	); 
}