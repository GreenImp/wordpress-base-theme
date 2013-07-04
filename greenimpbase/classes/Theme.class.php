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

		protected $themeName = '';
		protected $options = array();
		protected $scripts = array();

		private function __construct($themeName, $options = array(), $scripts = array()){
			$this->themeName = $themeName;
			$this->options = $options;
			$this->scripts = $scripts;

			// add the setup call
			add_action('after_setup_theme', array($this, 'setup'));

			// add favicon link call
			add_action( 'wp_head', array($this, 'faviconLink'));

			// add nicer meta titles
			add_filter('wp_title', array($this, 'wpTitle'), 10, 2);

			// add meta data (keywords, description)
			add_filter('wp_head', array($this, 'headerMeta'));

			add_filter('body_class', array($this, 'bodyClass'));

			// page menu call
			add_filter('wp_page_menu_args', array($this, 'pageMenuArgs'));

			// widgets call
			add_action('widgets_init', array($this, 'widgetsInit'));

			// load Google CDN jQuery
			add_action('init', array($this, 'loadJQuery'));
			// enqueue CSS and JS
			add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'));

			// add hook for theme specific smilies :)
			add_filter('smilies_src', array($this, 'customSmilies'), 1, 10);

			// add hook for allowing custom default avatars on a per-theme basis
			add_filter('avatar_defaults', array($this, 'defaultAvatar'));

			// add hook for adding buttons to the WYSIWYG
			add_filter('mce_buttons_2', array($this, 'wysiwygButtons'));

			// modify how tag clouds are output
			add_filter('widget_tag_cloud_args', array($this, 'tagCloudSettings'));

			// replace the captions with HTML5 fig and figcaptions
			add_filter('img_caption_shortcode', array($this, 'captionShortcode'), 10, 3);
		}

		/**
		 * returns the singleton instance of Theme
		 *
		 * @param $themeName
		 * @param array $options
		 * @param array $scripts
		 * @return Theme
		 */
		public static function getInstance($themeName = '', $options = array(), $scripts = array()){
			if(!is_a(self::$instance, 'Theme')){
				self::$instance = new Theme($themeName, $options, $scripts);
			}

			return self::$instance;
		}

		/**
		 * Sub strings the given string
		 *
		 * @param $str
		 * @param $len
		 * @param bool $byWord
		 * @param bool $addEnd
		 * @return string
		 */
		protected function subStr($str, $len, $byWord = false, $addEnd = true){
			$end = ' [...]';

			// try mb_substr first, as it's far more accurate
			if(function_exists('mb_strlen') && function_exists('mb_substr') && (mb_strlen($str) > $len)){
				// the string is too long
				$str = mb_substr($str, 0, $len-($addEnd ? strlen($end) : 0));
			}elseif(strlen($str) > $len){
				// no mb_substr, but the string is still too long
				$str = substr($str, 0, $len-($addEnd ? strlen($end) : 0));
			}else{
				// string is within the boundaries - just return it
				return $str;
			}

			// if we're here, then the string was sub-stringed
			if($byWord){
				// we are sub stringing by whole word
				$str = preg_replace('/\s+?(\S+)?$/', '', $str);
			}
			if($addEnd){
				// we need to add the 'end' string
				$str .= $end;
			}

			return $str;
		}

		/**
		 * Returns the theme's name
		 *
		 * @return string
		 */
		public function getThemeName(){
			return $this->themeName;
		}

		/**
		 * Returns the current theme's directory path,
		 * for server-side use
		 *
		 * @return string
		 */
		public function getThemeDirectory(){
			return rtrim(get_stylesheet_directory(), '/') . '/';
		}

		/**
		 * Returns the current themes full URL
		 *
		 * @return mixed
		 */
		public function getThemeURL(){
			return rtrim(get_stylesheet_directory_uri(), '/') . '/';
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
		 * Returns the theme attribution
		 *
		 * @return bool
		 */
		public function getAttribution(){
			return isset($this->options['attribution']) ?
					$this->options['attribution'] :
					'<a href="http://wordpress.org/" target="_blank" title="' . esc_attr(__('Semantic Personal Publishing Platform', $this->getThemeName())) . '">' . sprintf( __( 'Powered by %s', $this->getThemeName()), 'WordPress' ) . '</a>
					|
					<a href="http://greenimp.co.uk" target="_blank" title="Web development Devon">Theme by GreenImp</a>';
		}

		/**
		 * Adds a favicon link to the page head
		 */
		public function faviconLink(){
			// list of directories to check for icons
			$directories = array(
				$this->getThemeDirectory()	=> $this->getThemeURL(),		// theme directory
				rtrim(ABSPATH, '/') . '/'	=> rtrim(site_url(), '/') . '/'	// web root
			);

			/**
			 * Add the favicon
			 * Loop through each directory and check for the favicon
			 */
			foreach($directories as $dir => $url){
				if(file_exists($dir . 'favicon.ico')){
					// ico file exists
					echo '<link rel="shortcut icon" type="image/x-icon" href="' . $url . 'favicon.ico">' . "\n";
					break;
				}elseif(file_exists($dir . 'favicon.png')){
					// png file exists
					echo '<link rel="icon" type="image/png" href="' . $url . 'favicon.png">' . "\n";
					break;
				}
			}

			/**
			 * Add references to any existing IOS icons
			 */
			$iconName = 'apple-icon%s%s.png';	// the icon name convention
			$iconSizes = array(					// available icon sizes
				'',
				'57x57',
				'72x72',
				'114x114',
				'144x144'
			);

			foreach($iconSizes as $size){
				$hasSize = $size != '';	// check whether we actually have a size defined or if it is empty (default)

				$files = array(
					'apple-touch-icon'				=> sprintf($iconName, $hasSize ? '-' . $size : '', ''),
					'apple-touch-icon-precomposed'	=> sprintf($iconName, $hasSize ? '-' . $size : '', '-precomposed')
				);

				foreach($directories as $dir => $url){
					foreach($files as $rel => $file){
						if(file_exists($dir . $file)){
							// we have an icon file
							echo '<link rel="' . $rel . '"' . ($hasSize ? ' sizes="' . $size . '"' : '') . ' href="' . $url . $file . '">' . "\n";
							break 2;	// break out of the file and directories loop
						}
					}
				}
			}
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
 		 * Output the meta data for the current page (keywords, description)
		 */
		public function headerMeta(){
			// we only want to continue if no SEO plugins are enabled

			if(isset($this->options['outputMeta']) && (false == $this->options['outputMeta'])){
				// options define no meta output
				return;
			}

			// list of SEO plugins that should stop our own meta data being used
			$pluginChecks = array(
				// WordPress SEO by Yoast
				// http://wordpress.org/extend/plugins/wordpress-seo/
				'wordpress-seo/wp-seo.php',

				// WordPress Meta Keywords
				// http://wordpress.org/extend/plugins/wordpress-meta-keywords/
				'wordpress-meta-keywords/wordpress-meta-keywords.php'
			);
			if(!empty($pluginChecks)){
				// include the plugin function
				include_once(ABSPATH . 'wp-admin/includes/plugin.php');

				// loop through each SEO plugin and check if it is active
				foreach($pluginChecks as $plugin){
					if(is_plugin_active($plugin)){
						// this SEO plugin is enabled - end the function call
						return;
					}
				}
			}


			$keywords = '';
			$description = '';

			if(is_single() || is_page()){
				// we're on a single post or a page
				if(have_posts()){
					while(have_posts()){
						the_post();

						// check if we have a custom field for keywords
						$keywords = trim(trim(get_post_meta(get_the_ID(), 'meta_keywords', true), ','));
						if($keywords == ''){
							// no custom field defined or is empty - get keywords from post tags
							foreach(get_the_tags() as $tag){
								$keywords .= utf8_decode(apply_filters('the_tags', $tag->name)) . ',';
							}
							$keywords = rtrim($keywords, ',');
						}

						// check if we have a custom field for the description
						$description = trim(get_post_meta(get_the_ID(), 'meta_description', true));
						if($description == ''){
							// no custom field defined or is empty - get the post excerpt
							$description = get_the_excerpt();
						}
					}
				}
			}elseif(is_category()){
				// we're on a category page
				$description = category_description();
			}elseif(is_archive()){
				// we're on an archive page
				if(is_day()){
					$description = sprintf( __('Daily Archives: %s', $this->getThemeName()), get_the_date());
				}elseif(is_month()){
					$description = sprintf( __('Monthly Archives: %s', $this->getThemeName()), get_the_date(_x('F Y', 'monthly archives date format', $this->getThemeName())));
				}elseif(is_year()){
					$description = sprintf( __('Yearly Archives: %s', $this->getThemeName()), get_the_date(_x('Y', 'yearly archives date format', $this->getThemeName())));
				}else{
					$description = __('Archives', $this->getThemeName());
				}

				$description .= ' - ' . get_bloginfo('description');
			}

			// if no description was defined, default to the blog description
			$description = ($description == '') ? get_bloginfo('description') : $description;

			if($keywords == ''){
				// no keywords defined - let's try and generate some

				// get a list of post tags
				$keywords = wp_tag_cloud(array(
					'number'	=> 20,
					'format'	=> 'flat',
					'separator'	=> ',',
					'orderby'	=> 'count',
					'order'		=> 'DESC',
					'echo'		=> false
				));
				if(!is_null($keywords)){
					// post tags defined - remove html entities and use as keywords
					$keywords = strip_tags($keywords);
				}else{
					// no post tags defined - generate keywords based on the page title

					// list the words which we want to remove
					$blacklist = array(
						'a', 'and', 'it', 'of', 'off', 'or', 'the', 'where', 'which'
					);
					// build our keywords, from the page title
					$keywords = array_unique(array_filter(explode(' ', preg_replace('/[^a-z0-9 ]+/', ' ', strtolower(wp_title('|', false, 'right'))))));
					// loop through and remove any blacklisted words
					foreach($keywords as $k => $word){
						if(in_array($word, $blacklist)){
							unset($keywords[$k]);
						}
					}
					$keywords = implode(',', $keywords);
				}
			}

			echo '<meta name="keywords" content="' . $this->subStr($keywords, 255, true, false) . '">' . "\n" .
				'<meta name="description" content="' . $this->subStr($description, 160) . '">' . "\n";
		}

		/**
		 * Add extra (helpful) classes to the body
		 *
		 * @param $classes
		 * @return array
		 */
		public function bodyClass($classes){
			global $post;
			if(isset($post)){
				$classes[] = $post->post_type . '-' . $post->post_name;
			}

			if(isset($this->options['bodyClass'])){
				if(!is_array($this->options['bodyClass'])){
					$this->options['bodyClass'] = array($this->options['bodyClass']);
				}

				foreach($this->options['bodyClass'] as $class){
					$classes[] = $class;
				}
			}

			return $classes;
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
		 * Ensure that Wordpress uses the specified version of
		 * jQuery (defined in $options['jQuery'], when initialising
		 * the class), if newer than the default.
		 * It also loads jquery from the Google CDN
		 * and sets it to load in the footer
		 */
		public function loadJQuery(){
			global $wp_scripts;

			if(!is_admin()){
				$currentVersion = $wp_scripts->registered['jquery']->ver;
				$version = isset($this->options['jQuery']) ? $this->options['jQuery'] : '1.9.1';
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
			// include bill and bill-ui (from greenimpbase)
			wp_enqueue_style('bill', get_template_directory_uri() . '/assets/css/bill.min.css');
			wp_enqueue_style('bill-ui', get_template_directory_uri() . '/assets/css/bill-ui.min.css');

			// optional - include RTL (Right To Left) support
			if(isset($this->options['rtl']) && $this->options['rtl']){
				wp_enqueue_style('bill', get_template_directory_uri() . '/assets/css/bill-rtl.min.css');
			}

			// include the theme's style css file (from the current theme - usually a child)
			wp_enqueue_style('style', get_stylesheet_uri());

			// include bill and main JS (from greenimpbase)
			wp_enqueue_script('bill-js', get_template_directory_uri() . '/assets/js/bill.min.js', array('jquery'), '0.1', true);
			wp_enqueue_script('greenimp-js', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '0.1', true);

			// include modernizr (from greenimpbase)
			wp_enqueue_script('modernizr', get_template_directory_uri() . '/assets/vendor/modernizr-2.6.2.min.js', null, '2.6.2', true);

			if(is_singular() && comments_open() && get_option('thread_comments')){
				wp_enqueue_script( 'comment-reply' );
			}

			// check for any CSS/JS to enqueue
			if(count($this->scripts) > 0){
				foreach($this->scripts as $item){
					$data = array(
						'type'		=> '',
						'handle'	=> '',
						'src'		=> '',
						'depend'	=> array(),
						'version'	=> '0.0.1',
						'media'		=> 'all',
						'inFooter'	=> true
					);

					if(is_array($item)){
						if(!isset($item['src'])){
							// no URL defined - continue to the next script
							continue;
						}

						// set the type
						if(isset($item['type']) && (($item['type'] == 'css') || ($item['type'] == 'js'))){
							$data['type'] = $item['type'];
						}

						// set the name
						if(isset($item['handle'])){
							$data['handle'] = trim($item['handle']);
						}

						// set the dependencies
						if(isset($item['depend']) && is_array($item['depend'])){
							$data['depend'] = $item['depend'];
						}

						// set the media (only applies to css)
						if(isset($item['media'])){
							$data['media'] = $item['media'];
						}

						// set whether it should be in the header or footer (only applies to JS)
						if(isset($item['inFooter']) && (false == $item['inFooter'])){
							$data['inFooter'] = false;
						}
					}else{
						// item is just a URL
						$data['src'] = $item;
					}

					// check the assigned data
					if($data['type'] == ''){
						// no data type defined
						if(preg_match('/\.(css|js)$/', strtolower($data['src']), $matches)){
							// data type determined from the URL
							$data['type'] = $matches[1];
						}else{
							// unable to determine data type
							continue;
						}
					}

					if($data['handle'] == ''){
						// no name defined - generate a random one
						$data['handle'] = $this->themeName . '_' . time() . mt_rand(0, 9999) . '_' . mt_rand(0, 9999);
					}

					switch($data['type']){
						case 'css':
							wp_enqueue_style($data['handle'], $data['src'], $data['depend'], $data['version'], $data['media']);
						break;
						case 'js':
							wp_enqueue_script($data['handle'], $data['src'], $data['depend'], $data['version'], $data['inFooter']);
						break;
					}
				}
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
				'container'			=> false
			), $args);

			wp_nav_menu($args);
		}

		/**
		 * Builds a Wordpress page nav,
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
				$output = '<nav' . str_replace('class="menu"', 'class="nav"', $matches[2]) . ' data-nav>' . $matches[3] . '</nav>';
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

		/**
		 * Enables theme specific smilies
		 *
		 * @param $imageSrc
		 * @param $img
		 * @param $siteURL
		 * @return string
		 */
		public function customSmilies($imageSrc, $img, $siteURL){
			$themePath = 'assets/images/smilies/';
			$customPath = 'images/smilies/';

			if(file_exists($this->getThemeDirectory() . $themePath . $img)){
				// the current theme has custom smilies
				return $this->getThemeURL() . $themePath . $img;
			}elseif(file_exists(rtrim(WP_CONTENT_DIR, '/') . '/' . $customPath . $img)){
				// custom smilies exist in the wp-content folder
				return rtrim(WP_CONTENT_URL, '/') . '/' . $customPath . $img;
			}

			// no custom smilies found - show default
			return $imageSrc;
		}

		/**
		 * Enables theme specific default avatars
		 *
		 * @param $avatar_defaults
		 * @return mixed
		 */
		public function defaultAvatar($avatar_defaults){
			$filename = '%sassets/images/avatar.%s';

			$fileTypes = array('png', 'jpg', 'jpeg', 'gif');
			foreach($fileTypes as $type){
				if(file_exists(sprintf($filename, $this->getThemeDirectory(), $type))){
					// file found - add it to the list and end the loop
					$avatar_defaults[sprintf($filename, $this->getThemeURL(), $type)] = 'Theme';
					break;
				}
			}

		    return $avatar_defaults;
		}

		/**
		 * Defines the default tag cloud settings
		 *
		 * @param array $args
		 * @return array
		 */
		public function tagCloudSettings(array $args){
			// define the font size and unit
			$args['unit']		= 'em';
			$args['smallest']	= '0.75';
			$args['largest']	= '1.5';

			return $args;
		}

		/**
		 * Replace the default captions with HTML5 equivalents
		 *
		 * @param string $val
		 * @param array $attr
		 * @param null|string $content
		 * @return string
		 */
		function captionShortcode($val, $attr, $content = null){
			extract(shortcode_atts(
				array(
					'id'		=> '',
					'align'		=> 'alignnone',
					'width'		=> '',
					'caption'	=> ''
				),
				$attr
			));

			if(empty($caption)){
				// no width or no caption defined
				return $val;
			}

			return '<figure id="' . $id . '" aria-describedby="figcaption_' . $id . '" class="wp-caption ' . esc_attr($align) . '"' . (($width != '') ? ' style="width: ' . $width . 'px;"' : '') . '>' .
						do_shortcode($content) .
						'<figcaption id="figcaption_' . $id . '" class="wp-caption-text">' . $caption . '</figcaption>
					</figure>';
		}
	}
}