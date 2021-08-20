<?php
add_filter('admin_email_check_interval', '__return_false');

require_once get_template_directory() . '/scripts/scriptlinker.php';

function my_forcelogin_whitelist($whitelist)
{
	$whitelist[] = home_url('/reminder/');
	return $whitelist;
}
add_filter('v_forcelogin_whitelist', 'my_forcelogin_whitelist');

function cactus_setup()
{
	//date_default_timezone_set('Asia/Baku');
	load_theme_textdomain('cactus');

	add_theme_support('automatic-feed-links');

	add_theme_support('title-tag');

	add_theme_support('post-thumbnails');

	add_image_size('cactus-featured-image', 960, 720, true);

	// Set the default content width.
	$GLOBALS['content_width'] = 850;


	register_nav_menus(array(
		'top'    => __('Top Menu', 'cactus'),
	));

	/*
			* Switch default core markup for search form, comment form, and comments
			* to output valid HTML5.
		*/
	add_theme_support('html5', array(
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	));


	// Add theme support for Custom Logo.
	add_theme_support('custom-logo');

	// Setup the WordPress core custom header feature.
	add_theme_support('custom-header', array(
		'default-image'          => '',
		'random-default'         => false,
		'width'                  => '1920',
		'height'                 => '70',
		'flex-height'            => true,
		'flex-width'             => true,
		'default-text-color'     => '#333333',
		'header-text'            => true,
		'uploads'                => true,
		'wp-head-callback'       => '',
		'admin-head-callback'    => '',
		'admin-preview-callback' => ''
	));

	// Setup the WordPress core custom background feature.
	add_theme_support('custom-background',  array(
		'default-color' => 'ffffff',
		'default-image' => '',
	));

	// Add theme support for selective refresh for widgets.
	add_theme_support('customize-selective-refresh-widgets');

	/*
			* This theme styles the visual editor to resemble the theme style,
			* specifically font, colors, and column width.
		*/
	add_editor_style(array('assets/css/editor-style.css'));
}
add_action('after_setup_theme', 'cactus_setup');


/**
 * Enqueue scripts and styles.
 */
function cactus_scripts()
{

	global $cactus_options;

	$cactus_options = get_option('cactus_options');

	wp_enqueue_style('bootstrap',  get_template_directory_uri() . '/assets/plugins/bootstrap/css/bootstrap.css', false, '', false);
	wp_enqueue_style('font-awesome',  get_template_directory_uri() . '/assets/plugins/font-awesome/css/font-awesome.min.css', false, '', false);

	// Theme stylesheet.
	wp_enqueue_style('cactus-style', get_stylesheet_uri());

	wp_enqueue_script('bootstrap', get_template_directory_uri() . '/assets/plugins/bootstrap/js/bootstrap.js', array('jquery'), null, true);
	wp_enqueue_script('respond', get_template_directory_uri() . '/assets/plugins/respond.min.js', array('jquery'), null, true);

	if (is_singular() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}


	$custom_css = '';
	$header_text_color = get_header_textcolor();

	if ('blank' != $header_text_color) :

		$custom_css .= ".site-name,
		.site-tagline {
		color: #" . esc_attr($header_text_color) . " !important;
		}\r\n";
	else :

		$custom_css .= ".site-name,
		.site-tagline {
		display: none;
		}\r\n";

	endif;
	wp_add_inline_style('cactus-style', wp_filter_nohtml_kses($custom_css));
}
add_action('wp_enqueue_scripts', 'cactus_scripts');

/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function cactus_posted_on()
{

	// Get the author name; wrap it in a link.
	$byline = sprintf(
		/* translators: %s: post author */
		__('by %s', 'cactus'),
		'<span class="entry-author"> <a href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . get_the_author() . '</a></span>'
	);

	// Finally, let's write all of this to the page.
	echo '<span class="entry-date">' . cactus_time_link() . '</span> | ' . $byline . '';
}


/**
 * Gets a nicely formatted string for the published date.
 */
function cactus_time_link()
{
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	$time_string = sprintf(
		$time_string,
		get_the_date(DATE_W3C),
		get_the_date(),
		get_the_modified_date(DATE_W3C),
		get_the_modified_date()
	);

	// Wrap the time string in a link, and preface it with 'Posted on'.
	return sprintf(
		/* translators: %s: post date */
		__('<span class="screen-reader-text">Posted on</span> %s ', 'cactus'),
		'<a href="' . esc_url(get_permalink()) . '" rel="bookmark">' . $time_string . '</a>'
	);
}


/**
 * Returns an accessibility-friendly link to edit a post or page.
 */
function cactus_edit_link()
{

	$link = edit_post_link(
		sprintf(
			/* translators: %s: Name of current post */
			__('Edit<span class="screen-reader-text"> "%s"</span>', 'cactus'),
			get_the_title()
		),
		'<span class="edit-link">',
		'</span>'
	);

	return $link;
}

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function cactus_widgets_init()
{
	register_sidebar(array(
		'name'          => __('Sidebar', 'cactus'),
		'id'            => 'sidebar-1',
		'description'   => __('Add widgets here to appear in your sidebar.', 'cactus'),
		'before_widget' => '<section id="%1$s" class="widget-box %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	));

	register_sidebar(array(
		'name'          => __('Footer 1', 'cactus'),
		'id'            => 'footer-1',
		'description'   => __('Add widgets here to appear in your footer.', 'cactus'),
		'before_widget' => '<section id="%1$s" class="widget-box %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	));

	register_sidebar(array(
		'name'          => __('Footer 2', 'cactus'),
		'id'            => 'footer-2',
		'description'   => __('Add widgets here to appear in your footer.', 'cactus'),
		'before_widget' => '<section id="%1$s" class="widget-box %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	));

	register_sidebar(array(
		'name'          => __('Footer 3', 'cactus'),
		'id'            => 'footer-3',
		'description'   => __('Add widgets here to appear in your footer.', 'cactus'),
		'before_widget' => '<section id="%1$s" class="widget-box %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	));

	register_sidebar(array(
		'name'          => __('Footer 4', 'cactus'),
		'id'            => 'footer-4',
		'description'   => __('Add widgets here to appear in your footer.', 'cactus'),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	));
}
add_action('widgets_init', 'cactus_widgets_init');


/**
 *  Custom comments list
 */
function cactus_comment($comment, $args, $depth)
{

?>

	<li <?php comment_class("comment media-comment"); ?> id="comment-<?php comment_ID(); ?>">
		<div class="media-avatar media-left">
			<?php echo get_avatar($comment, '70', ''); ?>
		</div>
		<div class="media-body">
			<div class="media-inner">
				<h4 class="media-heading clearfix">
					<?php echo get_comment_author_link(); ?> - <?php comment_date(); ?> <?php edit_comment_link(__('(Edit)', 'cactus'), '  ', ''); ?>
					<?php comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
				</h4>

				<?php if ($comment->comment_approved == '0') : ?>
					<em><?php _e('Your comment is awaiting moderation.', 'cactus'); ?></em>
					<br />
				<?php endif; ?>

				<div class="comment-content"><?php comment_text(); ?></div>
			</div>
		</div>
	</li>

<?php
}

/**
 * Returns breadcrumbs.
 */
function cactus_breadcrumbs()
{
	$delimiter = '/';
	$before = '<span class="current">';
	$after = '</span>';
	if (!is_home() && !is_front_page() || is_paged()) {
		echo '<div itemscope itemtype="http://schema.org/WebPage" id="crumbs"><i class="fa fa-home"></i>';
		global $post;
		$homeLink = esc_url(home_url());
		echo ' <a itemprop="breadcrumb" href="' . $homeLink . '">' . __('Home', 'cactus') . '</a> ' . $delimiter . ' ';
		if (is_category()) {
			global $wp_query;
			$cat_obj = $wp_query->get_queried_object();
			$thisCat = $cat_obj->term_id;
			$thisCat = get_category($thisCat);
			$parentCat = get_category($thisCat->parent);
			if ($thisCat->parent != 0) {
				$cat_code = get_category_parents($parentCat, TRUE, ' ' . $delimiter . ' ');
				echo $cat_code = str_replace('<a', '<a itemprop="breadcrumb"', $cat_code);
			}
			echo $before . '' . single_cat_title('', false) . '' . $after;
		} elseif (is_day()) {
			echo '<a itemprop="breadcrumb" href="' . esc_url(get_year_link(get_the_time('Y'))) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
			echo '<a itemprop="breadcrumb"  href="' . esc_url(get_month_link(get_the_time('Y'), get_the_time('m'))) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
			echo $before . get_the_time('d') . $after;
		} elseif (is_month()) {
			echo '<a itemprop="breadcrumb" href="' . esc_url(get_year_link(get_the_time('Y'))) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
			echo $before . get_the_time('F') . $after;
		} elseif (is_year()) {
			echo $before . get_the_time('Y') . $after;
		} elseif (is_single() && !is_attachment()) {
			if (get_post_type() != 'post') {
				$post_type = get_post_type_object(get_post_type());
				$slug = $post_type->rewrite;
				echo '<a itemprop="breadcrumb" href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a> ' . $delimiter . ' ';
				echo $before . get_the_title() . $after;
			} else {
				$cat = get_the_category();
				$cat = $cat[0];
				$cat_code = get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
				echo $cat_code = str_replace('<a', '<a itemprop="breadcrumb"', $cat_code);
				echo $before . get_the_title() . $after;
			}
		} elseif (!is_single() && !is_page() && get_post_type() != 'post') {
			$post_type = get_post_type_object(get_post_type());
			if ($post_type)
				echo $before . $post_type->labels->singular_name . $after;
		} elseif (is_attachment()) {
			$parent = get_post($post->post_parent);
			$cat = get_the_category($parent->ID);
			$cat = $cat[0];
			echo '<a itemprop="breadcrumb" href="' . esc_url(get_permalink($parent)) . '">' . $parent->post_title . '</a> ' . $delimiter . ' ';
			echo $before . get_the_title() . $after;
		} elseif (is_page() && !$post->post_parent) {
			echo $before . get_the_title() . $after;
		} elseif (is_page() && $post->post_parent) {
			$parent_id  = $post->post_parent;
			$breadcrumbs = array();
			while ($parent_id) {
				$page = get_page($parent_id);
				$breadcrumbs[] = '<a itemprop="breadcrumb" href="' . esc_url(get_permalink($page->ID)) . '">' . get_the_title($page->ID) . '</a>';
				$parent_id  = $page->post_parent;
			}
			$breadcrumbs = array_reverse($breadcrumbs);
			foreach ($breadcrumbs as $crumb) echo $crumb . ' ' . $delimiter . ' ';
			echo $before . get_the_title() . $after;
		} elseif (is_search()) {
			echo $before;
			printf(__('Search Results for: %s', 'cactus'),  get_search_query());
			echo  $after;
		} elseif (is_tag()) {
			echo $before;
			printf(__('Tag Archives: %s', 'cactus'), single_tag_title('', false));
			echo  $after;
		} elseif (is_author()) {
			global $author;
			$userdata = get_userdata($author);
			echo $before;
			printf(__('Author Archives: %s', 'cactus'),  $userdata->display_name);
			echo  $after;
		} elseif (is_404()) {
			echo $before;
			_e('Not Found', 'cactus');
			echo  $after;
		}
		if (get_query_var('paged')) { // иом│
			if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author())
				echo sprintf(__('( Page %s )', 'cactus'), get_query_var('paged'));
		}
		echo '</div>';
	}
}

/**
 * Theme options
 */

function cactus_customize_register($wp_customize)
{

	$wp_customize->add_section('cactus_footer', array(
		'title'      => __('Footer Options', 'cactus'),
		'priority'   => 30,
	));

	// Display Footer Widgets Area
	$wp_customize->add_setting('cactus_options[enable_footer_widgets]', array(
		'default'        => '',
		'type'           => 'option',
		'capability'     => 'edit_theme_options',
		'transport'      => 'refresh',
		'sanitize_callback' => 'absint'
	));

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'enable_footer_widgets',
			array(
				'label'          => __('Display Footer Widgets Area', 'cactus'),
				'section'        => 'cactus_footer',
				'settings'       => 'cactus_options[enable_footer_widgets]',
				'type'           => 'checkbox',

			)
		)
	);
	// Footer Logo
	$wp_customize->add_setting('cactus_options[footer_logo]', array(
		'default'        => '',
		'type'           => 'option',
		'capability'     => 'edit_theme_options',
		'transport'      => 'refresh',
		'sanitize_callback' => 'esc_url_raw'
	));

	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'footer_logo',
			array(
				'label'      => __('Upload Footer Logo', 'cactus'),
				'section'    => 'cactus_footer',
				'settings'   => 'cactus_options[footer_logo]',
				'context'    => ''
			)
		)
	);

	$wp_customize->selective_refresh->add_partial('footer_logo_selective', array(
		'selector' => '.cactus-footer-logo',
		'settings' => array('cactus_options[footer_logo]'),
		'render_callback' => '',
	));

	// Display Footer Social Icons
	$wp_customize->add_setting('cactus_options[enable_footer_icons]', array(
		'default'        => '',
		'type'           => 'option',
		'capability'     => 'edit_theme_options',
		'transport'      => 'refresh',
		'sanitize_callback' => 'absint'
	));

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'enable_footer_icons',
			array(
				'label'          => __('Display Footer Social Icons (Font Awesome Icon, e.g. fa-facebook)', 'cactus'),
				'section'        => 'cactus_footer',
				'settings'       => 'cactus_options[enable_footer_icons]',
				'type'           => 'checkbox',

			)
		)
	);


	// Social Icons
	$wp_customize->add_setting('cactus_options[footer_icon_1]', array(
		'default'        => '',
		'type'           => 'option',
		'capability'     => 'edit_theme_options',
		'transport'      => 'refresh',
		'sanitize_callback' => 'esc_attr'
	));

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'footer_icon_1',
			array(
				'label'          => __('Footer Social Icon 1', 'cactus'),
				'section'        => 'cactus_footer',
				'settings'       => 'cactus_options[footer_icon_1]',
				'type'           => 'text',

			)
		)
	);

	$wp_customize->add_setting('cactus_options[footer_icon_link_1]', array(
		'default'        => '#',
		'type'           => 'option',
		'capability'     => 'edit_theme_options',
		'transport'      => 'refresh',
		'sanitize_callback' => 'esc_url_raw'

	));

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'footer_icon_link_1',
			array(
				'label'          => __('Footer Social Icon Link 1', 'cactus'),
				'section'        => 'cactus_footer',
				'settings'       => 'cactus_options[footer_icon_link_1]',
				'type'           => 'text',

			)
		)
	);

	$wp_customize->add_setting('cactus_options[footer_icon_2]', array(
		'default'        => '',
		'type'           => 'option',
		'capability'     => 'edit_theme_options',
		'transport'      => 'refresh',
		'sanitize_callback' => 'esc_attr'
	));

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'footer_icon_2',
			array(
				'label'          => __('Footer Social Icon 2', 'cactus'),
				'section'        => 'cactus_footer',
				'settings'       => 'cactus_options[footer_icon_2]',
				'type'           => 'text',

			)
		)
	);

	$wp_customize->add_setting('cactus_options[footer_icon_link_2]', array(
		'default'        => '#',
		'type'           => 'option',
		'capability'     => 'edit_theme_options',
		'transport'      => 'refresh',
		'sanitize_callback' => 'esc_url_raw'
	));

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'footer_icon_link_2',
			array(
				'label'          => __('Footer Social Icon Link 2', 'cactus'),
				'section'        => 'cactus_footer',
				'settings'       => 'cactus_options[footer_icon_link_2]',
				'type'           => 'text',

			)
		)
	);


	$wp_customize->add_setting('cactus_options[footer_icon_3]', array(
		'default'        => '',
		'type'           => 'option',
		'capability'     => 'edit_theme_options',
		'transport'      => 'refresh',
		'sanitize_callback' => 'esc_attr'
	));

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'footer_icon_3',
			array(
				'label'          => __('Footer Social Icon 3', 'cactus'),
				'section'        => 'cactus_footer',
				'settings'       => 'cactus_options[footer_icon_3]',
				'type'           => 'text',

			)
		)
	);

	$wp_customize->add_setting('cactus_options[footer_icon_link_3]', array(
		'default'        => '#',
		'type'           => 'option',
		'capability'     => 'edit_theme_options',
		'transport'      => 'refresh',
		'sanitize_callback' => 'esc_url_raw'
	));

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'footer_icon_link_3',
			array(
				'label'          => __('Footer Social Icon Link 3', 'cactus'),
				'section'        => 'cactus_footer',
				'settings'       => 'cactus_options[footer_icon_link_3]',
				'type'           => 'text',

			)
		)
	);

	$wp_customize->add_setting('cactus_options[footer_icon_4]', array(
		'default'        => '',
		'type'           => 'option',
		'capability'     => 'edit_theme_options',
		'transport'      => 'refresh',
		'sanitize_callback' => 'esc_attr'
	));

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'footer_icon_4',
			array(
				'label'          => __('Footer Social Icon 4', 'cactus'),
				'section'        => 'cactus_footer',
				'settings'       => 'cactus_options[footer_icon_4]',
				'type'           => 'text',

			)
		)
	);

	$wp_customize->add_setting('cactus_options[footer_icon_link_4]', array(
		'default'        => '#',
		'type'           => 'option',
		'capability'     => 'edit_theme_options',
		'transport'      => 'refresh',
		'sanitize_callback' => 'esc_url_raw'
	));

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'footer_icon_link_4',
			array(
				'label'          => __('Footer Social Icon Link 4', 'cactus'),
				'section'        => 'cactus_footer',
				'settings'       => 'cactus_options[footer_icon_link_4]',
				'type'           => 'text',

			)
		)
	);

	$wp_customize->add_setting('cactus_options[footer_icon_5]', array(
		'default'        => '',
		'type'           => 'option',
		'capability'     => 'edit_theme_options',
		'transport'      => 'refresh',
		'sanitize_callback' => 'esc_attr'
	));

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'footer_icon_5',
			array(
				'label'          => __('Footer Social Icon 5', 'cactus'),
				'section'        => 'cactus_footer',
				'settings'       => 'cactus_options[footer_icon_5]',
				'type'           => 'text',

			)
		)
	);

	$wp_customize->add_setting('cactus_options[footer_icon_link_5]', array(
		'default'        => '#',
		'type'           => 'option',
		'capability'     => 'edit_theme_options',
		'transport'      => 'refresh',
		'sanitize_callback' => 'esc_url_raw'
	));

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'footer_icon_link_5',
			array(
				'label'          => __('Footer Social Icon Link 5', 'cactus'),
				'section'        => 'cactus_footer',
				'settings'       => 'cactus_options[footer_icon_link_5]',
				'type'           => 'text',

			)
		)
	);

	$wp_customize->selective_refresh->add_partial('footer_sns_selective', array(
		'selector' => '.cactus-footer-sns',
		'settings' => array('cactus_options[footer_icon_1]'),
		'render_callback' => '',
	));
}
add_action('customize_register', 'cactus_customize_register');

add_filter('auth_cookie_expiration', 'my_expiration_filter', 99, 3);
function my_expiration_filter($seconds, $user_id, $remember)
{
	//if "remember me" is checked;
	if ($remember) {
		//WP defaults to 2 weeks;
		$expiration = 14 * 24 * 60 * 60; //UPDATE HERE;
	} else {
		//WP defaults to 48 hrs/2 days;
		$expiration = 1 * 12 * 60 * 60; //UPDATE HERE;
	}
	//http://en.wikipedia.org/wiki/Year_2038_problem
	if (PHP_INT_MAX - time() < $expiration) {
		//Fix to a little bit earlier!
		$expiration = PHP_INT_MAX - time() - 5;
	}
	return $expiration;
}

add_action('wp_ajax_is_user_logged_in', 'ajax_check_user_logged_in');
add_action('wp_ajax_nopriv_is_user_logged_in', 'ajax_check_user_logged_in');
function ajax_check_user_logged_in()
{
	echo is_user_logged_in();
	exit();
}
