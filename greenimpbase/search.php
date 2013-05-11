<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

get_header();
?>
<div class="row">
	<section id="primary" class="column nine site-content">
		<div class="innerContent">
			<?php if ( have_posts() ) : ?>

				<header class="page-header">
					<h1 class="page-title"><?php printf( __( 'Search Results for: %s', $theme->getThemeName() ), '<span>' . get_search_query() . '</span>' ); ?></h1>
				</header>

				<?php $theme->contentNav('nav-above'); ?>

				<?php /* Start the Loop */ ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'content', get_post_format() ); ?>
				<?php endwhile; ?>

				<?php $theme->contentNav('nav-below'); ?>

			<?php else : ?>

				<article id="post-0" class="post no-results not-found">
					<header class="entry-header">
						<h1 class="entry-title"><?php _e( 'Nothing Found', $theme->getThemeName() ); ?></h1>
					</header>

					<div class="entry-content">
						<p><?php _e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.', $theme->getThemeName() ); ?></p>
						<?php get_search_form(); ?>
					</div><!-- .entry-content -->
				</article><!-- #post-0 -->

			<?php endif; ?>
		</div>
	</section>
	<?php get_sidebar(); ?>
</div>
<?php
get_footer();