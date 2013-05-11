<?php
/**
 * Copyright GreenImp Web - greenimp.co.uk
 * 
 * Author: GreenImp Web
 * Date Created: 13/04/13 18:31
 */

$theme = Theme::getInstance();
?>
	<div id="sidebar" class="column three">
		<div class="widget-area">
			<?php if(!is_active_sidebar('sidebar-1') || !dynamic_sidebar('sidebar-1')){ ?>
			<aside id="search" class="widget widget_search">
				<?php get_search_form(); ?>
			</aside>

			<aside id="archives" class="widget">
				<h1 class="widget-title"><?php _e( 'Archives', 'shape' ); ?></h1>
				<ul>
					<?php wp_get_archives( array( 'type' => 'monthly' ) ); ?>
				</ul>
			</aside>
			<?php } ?>
		</div>

		<?php if(is_active_sidebar('sidebar-2')){ ?>
		<div class="widget-area">
			<?php dynamic_sidebar( 'sidebar-2' ); ?>
		</div>
		<?php } ?>

		<?php if(is_active_sidebar('sidebar-3')){ ?>
		<div class="widget-area">
			<?php dynamic_sidebar( 'sidebar-3' ); ?>
		</div>
		<?php } ?>
	</div>