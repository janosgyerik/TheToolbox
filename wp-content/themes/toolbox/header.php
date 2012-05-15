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

	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

	<link rel="stylesheet" href="<?php echo bloginfo('stylesheet_url').'?'.filemtime(get_stylesheet_directory().'/style.css'); ?>">

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

<!-- Yoggrt Ad Code -->
<script type="text/javascript">
(function(){
  var bsa = document.createElement('script');
     bsa.type = 'text/javascript';
     bsa.async = true;
     bsa.src = '//s3.buysellads.com/ac/bsa.js';
  (document.getElementsByTagName('head')[0]||document.getElementsByTagName('body')[0]).appendChild(bsa);
})();
</script>
<!-- End Yoggrt Ad Code -->

<!-- BEGIN #wrapper-->
<div id="wrapper">

	<!-- BEGIN #page-->
    <div id="page">
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
				<div class="nav-container clearfix">
					<!-- BEGIN #primary-menu -->
					<div id="primary-menu" class="clearfix">
		
						<?php if ( has_nav_menu( 'primary-menu' ) ) : wp_nav_menu( array( 'theme_location' => 'primary-menu' ) ); endif; ?>
						
					<!-- END #primary-menu -->
					</div>
					<div class="share-widget">
						<a class="share-link" href="#">Share</a>
						<div class="share-options">
							<div class="twitter">
								<a href="https://twitter.com/share" data-count="vertical" class="twitter-share-button" data-via="SachaGreif">Tweet</a>
								<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
							</div>
							<div class="facebook">
								<div class="fb-like" data-send="false" data-layout="box_count" data-width="70" data-show-faces="false"></div>
							</div>
							<div class="google">
								<!-- Place this tag where you want the +1 button to render -->
								<g:plusone size="tall"></g:plusone>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- /#header-inner -->
			</div>
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
