<?php
/**
 * Copyright GreenImp Web - greenimp.co.uk
 * 
 * Author: GreenImp Web
 * Date Created: 13/04/13 17:54
 */

$theme = Theme::getInstance();
?><!DOCTYPE html>
<!--[if lt IE 8]><html <?php language_attributes(); ?> class="no-js lt-ie8"><![endif]-->
<!--[if IE 8]><html <?php language_attributes(); ?> class="no-js ie8"><![endif]-->
<!--[if IE 9]><html <?php language_attributes(); ?> class="no-js ie9"><![endif]-->
<!--[if gt IE 9]><!--><html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width">
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<meta name="author" content="GreenImp Web - greenimp.co.uk">

	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

	<link href="<?php echo get_template_directory_uri(); ?>/assets/css/bill.min.css" rel="stylesheet" media="all">
	<link href="<?php echo get_template_directory_uri(); ?>/assets/css/bill-ui.min.css" rel="stylesheet" media="all">

	<!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/assets/resources/html5shiv.js"></script>
	<![endif]-->

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<div class="container constrain">
		<header id="header">
			<hgroup>
				<h1 class="site-title">
					<a href="<?php echo esc_url(home_url( '/' )); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" rel="home"><?php bloginfo('name'); ?></a>
				</h1>
				<?php if(get_bloginfo('description') != ''){ ?>
				<h2 class="site-description"><?php bloginfo('description'); ?></h2>
				<?php } ?>
			</hgroup>

			<?php $theme->wpNavMenu(); ?>
		</header>

		<div id="main">