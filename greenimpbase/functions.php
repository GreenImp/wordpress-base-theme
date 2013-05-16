<?php
/**
 * Copyright GreenImp Web - greenimp.co.uk
 * 
 * Author: GreenImp Web
 * Date Created: 13/04/13 18:35
 */
/**
 * Theme functions and definitions.
 *
 * Sets up the theme and provides some helper functions, which are used
 * in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook.
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 */

require_once(dirname(__FILE__) . '/classes/Theme.class.php');

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * @since greenimpbase 0.1
 */
if ( ! isset( $content_width ) ){
    $content_width = 1140; /* pixels */
}

$theme = Theme::getInstance('greenimpbase', isset($themeOptions) ? $themeOptions : array(), isset($themeScripts) ? $themeScripts : array());