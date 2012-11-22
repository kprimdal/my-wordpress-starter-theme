<?php

/* =============================================================================
   External Modules/Files
   ========================================================================== */


/* =============================================================================
   Theme Support
   ========================================================================== */
	
	if (function_exists('add_theme_support')) {
	    // Add Menu Support
	    add_theme_support('menus');
	    
	    // Add Thumbnail Theme Support
	    add_theme_support('post-thumbnails');
	    // add_image_size('custom-size', 700, 200, true); // Custom Thumbnail Size call using the_post_thumbnail('custom-size');
	    	    
	    // Localisation Support
	    // load_theme_textdomain('html5blank', get_template_directory() . '/languages');
	}

/* =============================================================================
   Functions
   ========================================================================== */

	// Load Custom Theme Scripts using Enqueue
	function html5blank_scripts() {
	    if (!is_admin()) {
	        wp_deregister_script('jquery'); // Deregister WordPress jQuery
	        wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js', 'jquery', '1.8.2'); // Load Google CDN jQuery
	        wp_enqueue_script('jquery'); // Enqueue it!
	        
	        wp_register_script('modernizr', get_template_directory_uri() . '/js/modernizr.js', 'jquery', '2.6.2'); // Modernizr with version Number at the end
	        wp_enqueue_script('modernizr'); // Enqueue it!
	        
	        wp_register_script('mux-script', get_template_directory_uri() . '/js/scripts.js', 'jquery', '1.0.0'); // HTML5 Blank script with version number
	        wp_enqueue_script('mux-script'); // Enqueue it!
	        
	    }
	}
	
	// Loading Conditional Scripts
	function conditional_scripts() {
	    // if (is_page('pagenamehere')) {
	    // }
	}
	
	
	// jQuery Fallbacks load in the footer
	function add_jquery_fallback() {
	    echo "<!-- Protocol Relative jQuery fall back if Google CDN offline -->";
	    echo "<script>";
	    echo "window.jQuery || document.write('<script src='" . get_bloginfo('template_url') . "/js/jquery-1.8.2.min.js'><\/script>')";
	    echo "</script>";
	}
	
	// Threaded Comments
	function enable_threaded_comments() {
	    if (!is_admin()) {
	        if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
	            wp_enqueue_script('comment-reply');
	    }
	}
	
	// Theme Stylesheets using Enqueue
	function html5blank_styles() {
		// Load Open Sans from Google Fonts, http://www.google.com/webfonts/specimen/Open+Sans
		$subsets = 'latin,latin-ext';
		$protocol = is_ssl() ? 'https' : 'http';
		$query_args = array(
			'family' => 'Open+Sans:400italic,700italic,400,700',
			'subset' => $subsets,
		);
		wp_enqueue_style( 'twentytwelve-fonts', add_query_arg( $query_args, "$protocol://fonts.googleapis.com/css" ), array(), null );

	    wp_register_style('mux-style', get_template_directory_uri() . '/css/style.css', array(), '1.0', 'all');
	    wp_enqueue_style('mux-style'); // Enqueue it!
	}
	
	// Register HTML5 Blank's Navigation
	function register_html5_menu() {
	    register_nav_menus(array( // Using array to specify more menus if needed
	        'header-menu' => __('Header Menu', 'html5blank'), // Main Navigation
	        // 'sidebar-menu' => __('Sidebar Menu', 'html5blank'), // Sidebar Navigation
	    ));
	}
	
	// Remove the <div> surrounding the dynamic navigation to cleanup markup
	function my_wp_nav_menu_args($args = '') {
	    $args['container'] = false;
	    return $args;
	}
	
	
	// Remove invalid rel attribute values in the categorylist
	function remove_category_rel_from_category_list($thelist) {
	    return str_replace('rel="category tag"', 'rel="tag"', $thelist);
	}
	
	// Add page slug to body class, love this - Credit: Starkers Wordpress Theme
	function add_slug_to_body_class($classes) {
	    global $post;
	    if (is_home()) {
	        $key = array_search('blog', $classes);
	        if ($key > -1) {
	            unset($classes[$key]);
	        }
	        ;
	    } elseif (is_page()) {
	        $classes[] = sanitize_html_class($post->post_name);
	    } elseif (is_singular()) {
	        $classes[] = sanitize_html_class($post->post_name);
	    }
	    ;
	    
	    return $classes;
	}
	
	// If Dynamic Sidebar Exists
	if (function_exists('register_sidebar')) {
	    // Define Sidebar Widget Area 1
	    register_sidebar(array(
	        'name' => __('Widget Area 1', 'html5blank'),
	        'description' => __('Discription for this widget-area...', 'html5blank'),
	        'id' => 'widget-area-1',
	        'before_widget' => '<div id="%1$s" class="%2$s">',
	        'after_widget' => '</div>',
	        'before_title' => '<h3>',
	        'after_title' => '</h3>'
	    ));
	}
	
	// Remove wp_head() injected Recent Comment styles
	function my_remove_recent_comments_style()
	{
	    global $wp_widget_factory;
	    remove_action('wp_head', array(
	        $wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
	        'recent_comments_style'
	    ));
	}
	
	// Pagination for paged posts, Page 1, Page 2, Page 3, with Next and Previous Links, No plugin
	function html5wp_pagination() {
	    global $wp_query;
	    $big = 999999999;
	    echo paginate_links(array(
	        'base' => str_replace($big, '%#%', get_pagenum_link($big)),
	        'format' => '?paged=%#%',
	        'current' => max(1, get_query_var('paged')),
	        'total' => $wp_query->max_num_pages
	    ));
	}
	
	
	// Remove 'text/css' from our enqueued stylesheet
	function html5_style_remove($tag) {
	    return preg_replace('~\s+type=["\'][^"\']++["\']~', '', $tag);
	}

	// Add the class current-menu-item if one of the references is shown
	function current_type_nav_class($classes, $item) {
	    $post_type = get_query_var('post_type');
	    if ($item->attr_title != '' && $item->attr_title == $post_type) {
	        array_push($classes, 'current-menu-item');
	    };
	    return $classes;
	}

/* =============================================================================
   Actions + Filters + ShortCodes
   ========================================================================== */

	// Add Actions
	add_action('init', 'html5blank_scripts'); // Add Custom Scripts
	add_action('wp_print_scripts', 'conditional_scripts'); // Add Conditional Page Scripts
	add_action('wp_footer', 'add_jquery_fallback'); // jQuery fallbacks loaded through footer
	add_action('get_header', 'enable_threaded_comments'); // Enable Threaded Comments
	add_action('wp_enqueue_scripts', 'html5blank_styles'); // Add Theme Stylesheet
	add_action('init', 'register_html5_menu'); // Add HTML5 Blank Menu
	add_action('widgets_init', 'my_remove_recent_comments_style'); // Remove inline Recent Comment Styles from wp_head()
	
	// Remove Actions
	remove_action('wp_head', 'feed_links_extra', 3); // Display the links to the extra feeds such as category feeds
	remove_action('wp_head', 'feed_links', 2); // Display the links to the general feeds: Post and Comment Feed
	remove_action('wp_head', 'rsd_link'); // Display the link to the Really Simple Discovery service endpoint, EditURI link
	remove_action('wp_head', 'wlwmanifest_link'); // Display the link to the Windows Live Writer manifest file.
	remove_action('wp_head', 'index_rel_link'); // index link
	remove_action('wp_head', 'parent_post_rel_link', 10, 0); // prev link
	remove_action('wp_head', 'start_post_rel_link', 10, 0); // start link
	remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0); // Display relational links for the posts adjacent to the current post.
	remove_action('wp_head', 'wp_generator'); // Display the XHTML generator that is generated on the wp_head hook, WP version
	remove_action('wp_head', 'start_post_rel_link', 10, 0);
	remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
	remove_action('wp_head', 'rel_canonical');
	remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
	
	// Add Filters
	add_filter('body_class', 'add_slug_to_body_class'); // Add slug to body class (Starkers build)
	add_filter('widget_text', 'do_shortcode'); // Allow shortcodes in Dynamic Sidebar
	add_filter('widget_text', 'shortcode_unautop'); // Remove <p> tags in Dynamic Sidebars (better!)
	add_filter('wp_nav_menu_args', 'my_wp_nav_menu_args'); // Remove surrounding <div> from WP Navigation
	add_filter('the_category', 'remove_category_rel_from_category_list'); // Remove invalid rel attribute
	add_filter('the_excerpt', 'shortcode_unautop'); // Remove auto <p> tags in Excerpt (Manual Excerpts only)
	add_filter('the_excerpt', 'do_shortcode'); // Allows Shortcodes to be executed in Excerpt (Manual Excerpts only)
	add_filter('style_loader_tag', 'html5_style_remove'); // Remove 'text/css' from enqueued stylesheet
	add_filter('nav_menu_css_class', 'current_type_nav_class', 10, 2 ); // Add the class current-menu-item if one of the references is shown


/* =============================================================================
   Custom Post Types
   ========================================================================== */


/* =============================================================================
   Shortcodes
   ========================================================================== */


?>