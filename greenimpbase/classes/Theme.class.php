<?php
/**
 * Copyright GreenImp Web - greenimp.co.uk
 * 
 * Author: GreenImp Web
 * Date Created: 14/04/13 00:07
 */

if(!class_exists('Theme')){
	class Theme{
		private static $instance = null;

		private $themeName = '';

		private function __construct($themeName, $setupOptions = array(), $jQuery = null, $scripts = array()){
			$this->themeName = $themeName;

			// add the setup call
			add_action('after_setup_theme', array($this, 'setup'));

			// add favicon link call
			add_action( 'wp_head', array($this, 'faviconLink'));

			// add nicer meta titles
			add_filter('wp_title', array($this, 'wpTitle'), 10, 2);

			// page menu call
			add_filter('wp_page_menu_args', array($this, 'pageMenuArgs'));

			// widgets call
			add_action('widgets_init', array($this, 'widgetsInit'));

			// load Google CDN jQuery
			add_action('init', array($this, 'loadJQuery'));
			// enqueue CSS and JS
			add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'));
		}

		/**
		 * returns the singleton instance of Theme
		 *
		 * @param $themeName
		 * @param array $setupOptions
		 * @param null $jQuery
		 * @param array $scripts
		 * @return null|Theme
		 */
		public static function getInstance($themeName = '', $setupOptions = array(), $jQuery = null, $scripts = array()){
			if(!is_a(self::$instance, 'Theme')){
				self::$instance = new Theme($themeName, $setupOptions = array(), $jQuery = null, $scripts = array());
			}

			return self::$instance;
		}

		public function getThemeName(){
			return $this->themeName;
		}

		/**
		 * Sets up theme defaults and registers the various WordPress features that
		 * the theme supports.
		 */
		public function setup(){
			/*
			 * Makes theme available for translation.
			 *
			 * Translations can be added to the /languages/ directory.
			 */
			load_theme_textdomain($this->getThemeName(), get_template_directory() . '/languages' );

			// Adds RSS feed links to <head> for posts and comments.
			add_theme_support('automatic-feed-links');

			// This theme supports a variety of post formats.
			add_theme_support( 'post-formats', array( 'aside', 'image', 'link', 'quote', 'status' ) );

			// adds filter to page menus
			add_filter('wp_page_menu', array($this, 'filterPageMenu'));

			// This theme uses wp_nav_menu() in one location.
			register_nav_menu('primary', __('Primary Menu', $this->getThemeName()));

			// This theme uses a custom image size for featured images, displayed on "standard" posts.
			add_theme_support('post-thumbnails');
			set_post_thumbnail_size(624, 9999); // Unlimited height, soft crop
		}

		/**
		 * Adds a favicon link to the page head
		 */
		public function faviconLink(){
			echo '<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />' . "\n";
		}

		/**
		 * Creates a nicely formatted and more specific title element text
		 * for output in head of document, based on current view.
		 *
		 * @param string $title Default title text for current view.
		 * @param string $sep Optional separator.
		 * @return string Filtered title.
		 */
		public function wpTitle($title, $sep){
			global $paged, $page;

			if ( is_feed() )
				return $title;

			// Add the site name.
			$title .= get_bloginfo( 'name' );

			// Add the site description for the home/front page.
			$site_description = get_bloginfo( 'description', 'display' );
			if ( $site_description && ( is_home() || is_front_page() ) )
				$title = "$title $sep $site_description";

			// Add a page number if necessary.
			if ( $paged >= 2 || $page >= 2 )
				$title = "$title $sep " . sprintf( __( 'Page %s', $this->getThemeName() ), max( $paged, $page ) );

			return $title;
		}

		/**
		 * Makes our wp_nav_menu() fallback -- wp_page_menu() -- show a home link.
		 *
		 * @since Twenty Twelve 1.0
		 */
		public function pageMenuArgs($args){
			if ( ! isset( $args['show_home'] ) )
				$args['show_home'] = true;
			return $args;
		}

		/**
		 * Registers our main widget area and the front page widget areas.
		 */
		public function widgetsInit(){
			register_sidebar( array(
				'name' => __( 'Main Sidebar', $this->getThemeName() ),
				'id' => 'sidebar-1',
				'description' => __( 'Appears on posts and pages except the optional Front Page template, which has its own widgets', $this->getThemeName() ),
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget' => '</aside>',
				'before_title' => '<h3 class="widget-title">',
				'after_title' => '</h3>',
			) );

			register_sidebar( array(
				'name' => __( 'First Front Page Widget Area', $this->getThemeName() ),
				'id' => 'sidebar-2',
				'description' => __( 'Appears when using the optional Front Page template with a page set as Static Front Page', $this->getThemeName() ),
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget' => '</aside>',
				'before_title' => '<h3 class="widget-title">',
				'after_title' => '</h3>',
			) );

			register_sidebar( array(
				'name' => __( 'Second Front Page Widget Area', $this->getThemeName() ),
				'id' => 'sidebar-3',
				'description' => __( 'Appears when using the optional Front Page template with a page set as Static Front Page', $this->getThemeName() ),
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget' => '</aside>',
				'before_title' => '<h3 class="widget-title">',
				'after_title' => '</h3>',
			) );
		}

		/*
		 * Ensure that Wordpress uses the specified
		 * version of jQuery, if newer than the default.
		 * It also loads jquery from the Google CDN
		 * and sets it to load in the footer
		 */
		public function loadJQuery($version = '1.9.1'){
			global $wp_scripts;

			if(!is_admin()){
				$currentVersion = $wp_scripts->registered['jquery']->ver;
				$version = ($version == '') ? '1.9.1' : $version;
				$version = (version_compare($version, $currentVersion) == 1) ? $version : $currentVersion;

				// comment out the next two lines to load the local copy of jQuery
				wp_deregister_script('jquery');
				wp_register_script('jquery', 'http' . (is_ssl() ? 's' : '') . '://ajax.googleapis.com/ajax/libs/jquery/' . $version . '/jquery.min.js', array(), $version, true);
				wp_enqueue_script('jquery');
			}
		}

		/**
		 * Enqueue scripts and styles
		 */
		public function enqueueScripts(){
			wp_enqueue_style('bill', get_template_directory_uri() . '/assets/css/bill.min.css');
			wp_enqueue_style('bill-ui', get_template_directory_uri() . '/assets/css/bill-ui.min.css');

			wp_enqueue_style('style', get_stylesheet_uri());


			wp_enqueue_script('bill-js', get_template_directory_uri() . '/assets/js/bill.min.js', array('jquery'), '0.1', true);

			wp_enqueue_script('modernizr', get_template_directory_uri() . '/assets/resources/modernizr-2.6.2.min.js', null, '2.6.2', true);

			if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
				wp_enqueue_script( 'comment-reply' );
			}
		}

		/**
		 * Builds a Wordpress nav menu,
		 * with some pre-defined settings
		 *
		 * @param $args
		 */
		public function wpNavMenu($args = array()){
			$args = array_merge(array(
				'theme_location'	=> 'primary',
				'container'			=> 'nav',
				'container_class'	=> 'nav'
			), $args);

			wp_nav_menu($args);
		}

		/**
		 * Builds a Wordpress page nave,
		 * with some pre-defined settings
		 *
		 * @param array $args
		 */
		public function wpPageMenu($args = array()){
			$args = array_merge(array(
				'menu_class'  => 'nav',
				'show_home'   => true
			), $args);

			wp_page_menu($args);
		}

		/**
		 * Ensures that page navs are surrounded
		 * in nav elements, rather than divs
		 *
		 * @param $output
		 * @return string
		 */
		function filterPageMenu($output){
			if(preg_match('/^(\n|\r)*<div([^>]+)>(.*?)<\/div>(\n|\r)*$/', $output, $matches)){
				$output = '<nav' . str_replace('class="menu"', 'class="nav"', $matches[2]) . '>' . $matches[3] . '</nav>';
			}

			return $output;
		}

		/**
		 * Displays navigation to next/previous pages when applicable.
		 */
		public function contentNav($html_id){
			global $wp_query;

			$html_id = esc_attr( $html_id );

			if ( $wp_query->max_num_pages > 1 ) : ?>
				<nav id="<?php echo $html_id; ?>" class="navigation" role="navigation">
					<h3 class="assistive-text"><?php _e( 'Post navigation', $this->getThemeName() ); ?></h3>
					<div class="nav-previous alignleft"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', $this->getThemeName() ) ); ?></div>
					<div class="nav-next alignright"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', $this->getThemeName() ) ); ?></div>
				</nav><!-- #<?php echo $html_id; ?> .navigation -->
			<?php endif;
		}

		/**
		 * Template for comments and pingbacks.
		 *
		 * Used as a callback by wp_list_comments() for displaying the comments.
		 *
		 * @since Twenty Twelve 1.0
		 */
		function comment($comment, $args, $depth){
			$GLOBALS['comment'] = $comment;
			switch ( $comment->comment_type ) :
				case 'pingback' :
				case 'trackback' :
				// Display trackbacks differently than normal comments.
			?>
			<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
				<p><?php _e( 'Pingback:', $this->getThemeName() ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', $this->getThemeName() ), '<span class="edit-link">', '</span>' ); ?></p>
			<?php
					break;
				default :
				// Proceed with normal comments.
				global $post;
			?>
			<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
				<article id="comment-<?php comment_ID(); ?>" class="comment">
					<header class="comment-meta comment-author vcard">
						<?php
							echo get_avatar( $comment, 44 );
							printf( '<cite class="fn">%1$s %2$s</cite>',
								get_comment_author_link(),
								// If current post author is also comment author, make it known visually.
								( $comment->user_id === $post->post_author ) ? '<span> ' . __( 'Post author', $this->getThemeName() ) . '</span>' : ''
							);
							printf( '<a href="%1$s"><time datetime="%2$s">%3$s</time></a>',
								esc_url( get_comment_link( $comment->comment_ID ) ),
								get_comment_time( 'c' ),
								/* translators: 1: date, 2: time */
								sprintf( __( '%1$s at %2$s', $this->getThemeName() ), get_comment_date(), get_comment_time() )
							);
						?>
					</header><!-- .comment-meta -->

					<?php if ( '0' == $comment->comment_approved ) : ?>
						<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', $this->getThemeName() ); ?></p>
					<?php endif; ?>

					<section class="comment-content comment">
						<?php comment_text(); ?>
						<?php edit_comment_link( __( 'Edit', $this->getThemeName() ), '<p class="edit-link">', '</p>' ); ?>
					</section><!-- .comment-content -->

					<div class="reply">
						<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', $this->getThemeName() ), 'after' => ' <span>&darr;</span>', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
					</div><!-- .reply -->
				</article><!-- #comment-## -->
			<?php
				break;
			endswitch; // end comment_type check
		}

		/**
		 * Prints HTML with meta information for current post: categories, tags, permalink, author, and date.
		 */
		public function entryMeta(){
			// Translators: used between list items, there is a space after the comma.
			$categories_list = get_the_category_list( __( ', ', $this->getThemeName() ) );

			// Translators: used between list items, there is a space after the comma.
			$tag_list = get_the_tag_list( '', __( ', ', $this->getThemeName() ) );

			$date = sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a>',
				esc_url( get_permalink() ),
				esc_attr( get_the_time() ),
				esc_attr( get_the_date( 'c' ) ),
				esc_html( get_the_date() )
			);

			$author = sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
				esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
				esc_attr( sprintf( __( 'View all posts by %s', $this->getThemeName() ), get_the_author() ) ),
				get_the_author()
			);

			// Translators: 1 is category, 2 is tag, 3 is the date and 4 is the author's name.
			if ( $tag_list ) {
				$utility_text = __( 'This entry was posted in %1$s and tagged %2$s on %3$s<span class="by-author"> by %4$s</span>.', $this->getThemeName() );
			} elseif ( $categories_list ) {
				$utility_text = __( 'This entry was posted in %1$s on %3$s<span class="by-author"> by %4$s</span>.', $this->getThemeName() );
			} else {
				$utility_text = __( 'This entry was posted on %3$s<span class="by-author"> by %4$s</span>.', $this->getThemeName() );
			}

			printf(
				$utility_text,
				$categories_list,
				$tag_list,
				$date,
				$author
			);
		}
	}
}