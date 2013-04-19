<?php
/**
 * Copyright GreenImp Web - greenimp.co.uk
 * 
 * Author: GreenImp Web
 * Date Created: 14/04/13 01:29
 */

$theme = Theme::getInstance();
?>
<form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" role="search" id="searchform">
	<div>
		<label class="screen-reader-text" for="s"><?php echo __('Search for:'); ?></label>
		<input type="text" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" placeholder="<?php esc_attr_e('Search...', $theme->getThemeName()); ?>" id="s" />
		<input type="submit" id="searchsubmit" value="<?php esc_attr_e('Search', $theme->getThemeName()); ?>" />
	</div>
</form>