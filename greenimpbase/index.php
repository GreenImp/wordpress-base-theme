<?php
/**
 * Copyright GreenImp Web - greenimp.co.uk
 * 
 * Author: GreenImp Web
 * Date Created: 13/04/13 17:53
 */
get_header();
?>
	<div class="row">
		<section id="primary" class="column nine site-content">
			<div class="innerContent">
				<?php
				if(have_posts()):
					/* Start the Loop */
					while(have_posts()):
						the_post();

						get_template_part('content', get_post_format());
					endwhile;

					$theme->contentNav('nav-below');
				else:
				?>
				<article id="post-0" class="post no-results not-found">
					<?php if ( current_user_can( 'edit_posts' ) ) : // Show a different message to a logged-in user who can add posts. ?>
					<header class="entry-header">
						<h1 class="entry-title"><?php _e( 'No posts to display', $theme->getThemeName() ); ?></h1>
					</header>

					<div class="entry-content">
						<p><?php printf( __( 'Ready to publish your first post? <a href="%s">Get started here</a>.', $theme->getThemeName() ), admin_url( 'post-new.php' ) ); ?></p>
					</div><!-- .entry-content -->
					<?php else : // Show the default message to everyone else. ?>
					<header class="entry-header">
						<h1 class="entry-title"><?php _e( 'Nothing Found', $theme->getThemeName() ); ?></h1>
					</header>

					<div class="entry-content">
						<p><?php _e( 'Apologies, but no results were found. Perhaps searching will help find a related post.', $theme->getThemeName() ); ?></p>
						<?php get_search_form(); ?>
					</div><!-- .entry-content -->
					<?php endif; // end current_user_can() check ?>
				</article>
				<?php
				endif;
				?>
			</div>
		</section>
		<?php get_sidebar(); ?>
	</div>
<?php
get_footer();