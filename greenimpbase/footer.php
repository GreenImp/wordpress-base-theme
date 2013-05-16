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

			<?php if(($attribution = $theme->getAttribution()) != ''){ ?>
			<div class="attribution">
				<?php echo $attribution; ?>
			</div>
			<?php } ?>
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