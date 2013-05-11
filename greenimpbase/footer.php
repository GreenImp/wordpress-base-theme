<?php
/**
 * Copyright GreenImp Web - greenimp.co.uk
 * 
 * Author: GreenImp Web
 * Date Created: 13/04/13 17:57
 */

$theme = Theme::getInstance();
?>
		</div>

		<footer id="footer">
			<address>
				&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>
			</address>

			<div class="attribution">
				<a href="http://wordpress.org/" target="_blank" title="<?php esc_attr_e( 'Semantic Personal Publishing Platform', $theme->getThemeName()); ?>"><?php printf( __( 'Powered by %s', $theme->getThemeName()), 'WordPress' ); ?></a>
				|
				<a href="http://greenimp.co.uk" target="_blank" title="web development Devon">Site by GreenImp</a>
			</div>
		</footer>
	</div>

	<?php wp_footer(); ?>
	<script>
		(function($, document){
			$(document).bill('browserNotice');
		})(jQuery, document);
	</script>
</body>
</html>