<?php
/**
 * Foxdale Village 2026 block theme.
 *
 * Copyright © 2026 Foxdale Village. All rights reserved.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FOXDALE_THEME_VERSION', '1.0.0' );

require_once get_theme_file_path( 'inc/floor-plans.php' );

/**
 * Theme supports and editor styles.
 */
function foxdale_setup() {
	add_theme_support( 'editor-styles' );
	add_editor_style( array(
		foxdale_fonts_url(),
		'assets/css/foxdale.css',
		'assets/css/wp-adapter.css',
	) );
}
add_action( 'after_setup_theme', 'foxdale_setup' );

/**
 * Google Fonts URL (Playfair Display + Lato), same as the static site.
 */
function foxdale_fonts_url() {
	return 'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,500;1,600;1,700&family=Lato:ital,wght@0,400;0,700;1,400&display=swap';
}

/**
 * Front-end styles and scripts.
 */
function foxdale_enqueue_assets() {
	wp_enqueue_style( 'foxdale-fonts', foxdale_fonts_url(), array(), null );
	wp_enqueue_style(
		'foxdale-design-system',
		get_theme_file_uri( 'assets/css/foxdale.css' ),
		array(),
		FOXDALE_THEME_VERSION
	);
	wp_enqueue_style(
		'foxdale-wp-adapter',
		get_theme_file_uri( 'assets/css/wp-adapter.css' ),
		array( 'foxdale-design-system' ),
		FOXDALE_THEME_VERSION
	);
	wp_enqueue_script(
		'foxdale-main',
		get_theme_file_uri( 'assets/js/main.js' ),
		array(),
		FOXDALE_THEME_VERSION,
		array( 'in_footer' => true, 'strategy' => 'defer' )
	);
}
add_action( 'wp_enqueue_scripts', 'foxdale_enqueue_assets' );

/**
 * Pattern category so all Foxdale patterns group together in the inserter.
 */
function foxdale_register_pattern_category() {
	register_block_pattern_category(
		'foxdale',
		array( 'label' => __( 'Foxdale Village', 'foxdale-2026' ) )
	);
	register_block_pattern_category(
		'foxdale-pages',
		array( 'label' => __( 'Foxdale Full Pages', 'foxdale-2026' ) )
	);
}
add_action( 'init', 'foxdale_register_pattern_category' );

/**
 * Helper used by patterns to reference theme images.
 */
function foxdale_img( $file ) {
	return esc_url( get_theme_file_uri( 'assets/img/' . $file ) );
}
