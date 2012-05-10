<!DOCTYPE html>
<!--[if lt IE 7]><html <?php language_attributes(); ?> class="ie6 ie67"><![endif]-->
<!--[if IE 7]><html <?php language_attributes(); ?> class="ie7 ie67"><![endif]-->
<!--[if IE 8]><html <?php language_attributes(); ?> class="ie8"><![endif]-->
<!--[if gt IE 8]><!--><html <?php language_attributes(); ?>><!--<![endif]-->
<head>

	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!--[if lt IE 9]>
	<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
	<![endif]-->

	<title><?php wp_title('|', true, 'right'); ?><?php bloginfo('name'); ?></title>

	<link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri();?>/images/favicon.ico">

	<?php if(get_option('dt_custom_favicon') != '') : ?>
	<link rel="shortcut icon" href="<?php echo stripslashes(get_option('dt_custom_favicon')); ?>">
	<link rel="apple-touch-icon" href="<?php echo stripslashes(get_option('dt_custom_favicon')); ?>">
	<?php endif; ?>

	<link rel="profile" href="http://gmpg.org/xfn/11" />

	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />

	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

	<link rel="stylesheet" href="<?php echo bloginfo('stylesheet_url').'?'.filemtime(get_stylesheet_directory().'/style.css'); ?>">

	<link rel="stylesheet" href="<?php echo get_template_directory_uri().'/custom-style.css'.'?'.filemtime(get_stylesheet_directory().'/custom-style.css'); ?>">

	<?php if(get_option('dt_custom_css') && get_option('dt_custom_css')!=""): ?>
	<style type="text/css">
		<?php echo stripslashes(get_option('dt_custom_css')); ?>
	</style>
	<?php endif; ?>

	<?php wp_head(); ?>

	<!--[if lt IE 9]>
		<script src="<?php echo get_template_directory_uri();  ?>/engine/js/selectivizr.js"></script>
	<![endif]-->

</head>

<body <?php body_class(); ?>>

<!-- BEGIN #wrapper-->
<div id="wrapper">

	<!-- BEGIN #page-->
    <div id="page">


		<!-- BEGIN #top-bar-->
		<div id="top-bar">

		<!-- END #top-bar-->
		</div>

		<!-- BEGIN #header-->
		<div id="header">
			
			<!-- #header-inner -->
			<div id="header-inner">
				<div class="header-wrapper clearfix">
					<?php $logo = get_option('dt_custom_logo'); ?>
		
					<div id="logo">
		
				    <?php if ($logo == '' || !$logo): ?>
		
						<?php if (is_home() || is_front_page()): ?>
		
							<h1 id="site-title"><span><a href="<?php echo home_url() ?>/" title="<?php bloginfo('name') ?>" rel="home"><?php bloginfo('name') ?></a></span></h1>
		
						<?php else: ?>
		
							<div id="site-title"><a href="<?php echo home_url() ?>/" title="<?php bloginfo('name') ?>" rel="home"><?php bloginfo('name') ?></a></div>
		
						<?php endif; ?>
		
					<?php else: ?>
		
						<?php if (is_home() || is_front_page()): ?>
		
							<h1 id="site-title"><span><a href="<?php echo home_url() ?>/" title="<?php bloginfo('name') ?> - <?php bloginfo('description') ?>" rel="home"><img class="logo" alt="<?php bloginfo('name') ?>" src="<?php echo stripslashes($logo); ?>" /></a></span></h1>
		
						<?php else: ?>
		
							<div id="site-title"><span><a href="<?php echo home_url() ?>/" title="<?php bloginfo('name') ?> - <?php bloginfo('description') ?>" rel="home"><img class="logo" alt="<?php bloginfo('name') ?>" src="<?php echo stripslashes($logo); ?>" /></a></span></div>
		
						<?php endif; ?>
		
					<?php endif; ?>
		
					<!-- END #logo -->
				</div>
					<!-- BEGIN #primary-menu -->
					<div id="primary-menu" class="clearfix">
		
						<?php if ( has_nav_menu( 'primary-menu' ) ) : wp_nav_menu( array( 'theme_location' => 'primary-menu' ) ); endif; ?>
		
					<!-- END #primary-menu -->
					</div>


				</div>
			</div>
			<!-- /#header-inner -->
			
		<!-- END #header -->
		</div>

		<?php $welcome = get_option('dt_welcome_message'); ?>

		<?php if(is_home() || is_front_page() && $welcome != '') : ?>

		<h3 id="home-title"><?php echo stripslashes(htmlspecialchars_decode(nl2br($welcome))); ?></h3>

		<?php endif; ?>

		<div id="main" class="clearfix">

			<div id="container" class="clearfix">

				<?php if(!is_home() || !is_front_page()) : ?>

				<div id="breadcrumb-wrap" class="clearfix">
					<?php
					dt_breadcrumb( array(
			    		'before' => '',
			    		'separator' => ' '
			    		)
			    	);
			    	?>
			    </div>
		    	<?php endif; ?>
