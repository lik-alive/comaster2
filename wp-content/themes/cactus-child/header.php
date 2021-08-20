<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">

	<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/plugins/jQueryUI-1.12.1/jquery-ui.min.css">
	<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/plugins/DataTables/datatables.min.css">

	<?php wp_head(); ?>
</head>

<script type="text/javascript">
	var SITE_URL = "<?php echo get_site_url(); ?>";
	var ADMIN_URL = "<?php echo admin_url('admin-ajax.php'); ?>";
	var TEMPLATE_URL = "<?php echo get_template_directory_uri(); ?>";
	var USER_ROLE = "<?php echo wp_get_current_user()->roles[0]; ?>";
</script>

<script type='text/javascript' src='<?php echo get_template_directory_uri(); ?>/assets/plugins/jQuery-3.3.1/jquery-3.3.1.min.js'></script>

<?php
wp_enqueue_script('general', get_template_directory_uri() . '/js/general.js');
wp_enqueue_script('jquery.cookie', get_template_directory_uri() . '/assets/plugins/jquery.cookie/jquery.cookie.js');
?>

<script type='text/javascript' src='<?php echo get_template_directory_uri(); ?>/assets/plugins/jQueryUI-1.12.1/jquery-ui.min.js'></script>
<script type='text/javascript' src='<?php echo get_template_directory_uri(); ?>/assets/plugins/DataTables/datatables.min.js'></script>

<body <?php body_class('page blog'); ?>>
	<div class="main-header">
		<div>
			<div class="pull-left">
				<div>
					<div class="logo-wrap pull-left"> <a href="<?php echo esc_url(home_url('/')); ?>">
							<?php the_custom_logo(); ?>
						</a> </div>
					<div class="name-box pull-left"> <a href="<?php echo esc_url(home_url('/')); ?>">
							<h1 class="site-name"><?php bloginfo('name'); ?></h1>
						</a>
						<?php $description = get_bloginfo('description', 'display') . ', ' . wp_get_current_user()->user_firstname;
						if ($description || is_customize_preview()) : ?>
							<p class="site-welcome"><?php echo $description; ?></p>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<div class="pull-right">
				<nav class="site-nav" role="navigation">
					<?php wp_nav_menu(array(
						'theme_location' => 'top',
						'menu_id'        => 'top-menu',
						'fallback_cb'    => 'wp_page_menu'
					)); ?>
				</nav>
			</div>
		</div>
		<div id='status-bar'></div>
	</div>