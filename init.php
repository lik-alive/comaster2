<?php

$env = parse_ini_file('.env');

// Hide output
ob_start();
require __DIR__ . '/wp-blog-header.php';
ob_get_clean();

$user_id = wp_create_user($env['DB_USER'], $env['DB_PASS']);
update_user_meta($user_id, 'wp_capabilities', ['administrator' => true]);
update_user_meta($user_id, 'first_name', 'Администратор');

echo "Success\n";
exit();