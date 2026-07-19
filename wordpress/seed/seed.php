<?php
/**
 * Foxdale Village content seeder.
 *
 * Run once after activating the theme:
 *   wp eval-file wordpress/seed/seed.php
 *
 * Creates the ten site pages from seed/pages/*.html, imports the eleven
 * floor plans (drawing, type, specs, tour link), and configures the
 * front page + permalinks. Safe to re-run: existing pages/plans are
 * updated in place, not duplicated.
 */

if ( ! defined( 'ABSPATH' ) ) {
	echo "Run via: wp eval-file wordpress/seed/seed.php\n";
	exit( 1 );
}

$seed_dir = __DIR__;

/**
 * Resolve {{img:...}} placeholders to theme asset URLs.
 */
function foxdale_seed_resolve_images( $content ) {
	return preg_replace_callback(
		'/\{\{img:([^}]+)\}\}/',
		function ( $m ) {
			return get_theme_file_uri( 'assets/img/' . $m[1] );
		},
		$content
	);
}

// ---------------------------------------------------------------------------
// Pages
// ---------------------------------------------------------------------------

$pages = array(
	'home'       => 'Home',
	'life'       => 'Life at Foxdale',
	'residences' => 'Residences & Floor Plans',
	'healthcare' => 'Healthcare',
	'campus'     => 'Our Campus',
	'planning'   => 'Plan Your Move',
	'visit'      => 'Schedule a Visit',
	'about'      => 'About Foxdale Village',
	'careers'    => 'Careers',
	'giving'     => 'Giving & Volunteering',
);

$page_ids = array();

foreach ( $pages as $slug => $title ) {
	$file = $seed_dir . '/pages/' . $slug . '.html';
	if ( ! file_exists( $file ) ) {
		WP_CLI::warning( "Missing seed file: $file" );
		continue;
	}
	$content = foxdale_seed_resolve_images( file_get_contents( $file ) );

	$existing = get_page_by_path( $slug, OBJECT, 'page' );
	$postarr  = array(
		'post_title'   => $title,
		'post_name'    => $slug,
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_content' => $content,
	);
	if ( $existing ) {
		$postarr['ID'] = $existing->ID;
		$page_ids[ $slug ] = wp_update_post( wp_slash( $postarr ) );
		WP_CLI::log( "Updated page: $slug (#{$page_ids[$slug]})" );
	} else {
		$page_ids[ $slug ] = wp_insert_post( wp_slash( $postarr ) );
		WP_CLI::log( "Created page: $slug (#{$page_ids[$slug]})" );
	}
}

// Front page.
if ( ! empty( $page_ids['home'] ) ) {
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $page_ids['home'] );
	WP_CLI::log( 'Front page set to Home.' );
}

// ---------------------------------------------------------------------------
// Floor plans
// ---------------------------------------------------------------------------

foxdale_seed_plan_types();

$plans = array(
	array( 'Chestnut', 'cottage', 'Cottage · 1 BR · 1 BA · 845 sq ft', 'https://youtu.be/WTwzfd_ynYY', 'plan-chestnut.png' ),
	array( 'Hickory', 'cottage', 'Cottage · 1 BR + Den · 1–1.5 BA · 1,037 sq ft', 'https://youtu.be/8OlqYXrnCkw', 'plan-hickory.png' ),
	array( 'Dogwood', 'cottage', 'Cottage · 2 BR · 1–1.5 BA · 1,041 sq ft', 'https://youtu.be/7luhT4_S0ys', 'plan-dogwood.png' ),
	array( 'Sycamore', 'cottage', 'Cottage · 2 BR · 2 BA · 1,181 sq ft', 'https://youtu.be/OScZWHrt_lY', 'plan-sycamore.png' ),
	array( 'Pine', 'apartment', 'Apartment · 1 BR · 1.5 BA · 900 sq ft', 'https://youtu.be/1VSUxFo3i4E', 'plan-pine.png' ),
	array( 'Elm', 'apartment', 'Apartment · 1 BR + Den · 1.5 BA · 1,050 sq ft', 'https://youtu.be/JPOk2wnTDa8', 'plan-elm.png' ),
	array( 'Maple', 'apartment', 'Apartment · 1 BR + Den · 1.5 BA · 1,050 sq ft', 'https://youtu.be/8QNRBCZ5kKI', 'plan-maple.png' ),
	array( 'Birch', 'apartment', 'Apartment · 2 BR · 2 BA · 1,200 sq ft', '', 'plan-birch.png' ),
	array( 'Walnut', 'apartment', 'Apartment · 2 BR · 2 BA · 1,200 sq ft', 'https://youtu.be/N22rROr4crw', 'plan-walnut.png' ),
	array( 'Hemlock', 'apartment', 'Apartment · 2 BR + Den · 2 BA · 1,350 sq ft', 'https://youtu.be/68FWh0OiJic', 'plan-hemlock.png' ),
	array( 'Oak', 'apartment', 'Apartment · 2 BR · 2.5 BA · 1,450 sq ft', 'https://youtu.be/1GwNDJCEEFY', 'plan-oak.png' ),
);

require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$order = 0;
foreach ( $plans as $plan ) {
	list( $name, $type, $specs, $tour, $image ) = $plan;
	$order += 10;

	$existing = get_page_by_path( sanitize_title( $name ), OBJECT, 'floor_plan' );
	$postarr  = array(
		'post_title'  => $name,
		'post_name'   => sanitize_title( $name ),
		'post_type'   => 'floor_plan',
		'post_status' => 'publish',
		'menu_order'  => $order,
	);
	if ( $existing ) {
		$postarr['ID'] = $existing->ID;
		$post_id = wp_update_post( $postarr );
	} else {
		$post_id = wp_insert_post( $postarr );
	}

	update_post_meta( $post_id, 'foxdale_specs', $specs );
	update_post_meta( $post_id, 'foxdale_tour_url', $tour );
	wp_set_object_terms( $post_id, $type, 'plan_type' );

	if ( ! has_post_thumbnail( $post_id ) ) {
		$src = get_theme_file_path( 'assets/img/' . $image );
		if ( file_exists( $src ) ) {
			$tmp = wp_tempnam( $image );
			copy( $src, $tmp );
			$attachment_id = media_handle_sideload(
				array( 'name' => $image, 'tmp_name' => $tmp ),
				$post_id,
				$name . ' floor plan'
			);
			if ( ! is_wp_error( $attachment_id ) ) {
				set_post_thumbnail( $post_id, $attachment_id );
			} else {
				WP_CLI::warning( "Image failed for $name: " . $attachment_id->get_error_message() );
			}
		}
	}
	WP_CLI::log( "Floor plan: $name (#$post_id)" );
}

flush_rewrite_rules();
WP_CLI::success( 'Foxdale seed complete.' );
