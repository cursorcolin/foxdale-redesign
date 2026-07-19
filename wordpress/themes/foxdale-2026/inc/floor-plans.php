<?php
/**
 * Floor Plan custom post type, plan-type taxonomy, and the
 * [foxdale_floor_plans] shortcode that renders the filterable grid.
 *
 * Editors manage each plan as a post: the title is the plan name, the
 * featured image is the plan drawing, and the sidebar box holds the
 * specs line and the optional virtual-tour link. The Residences page
 * just contains the shortcode, so adding a plan never touches layout.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function foxdale_register_floor_plans() {
	register_post_type(
		'floor_plan',
		array(
			'labels'       => array(
				'name'          => __( 'Floor Plans', 'foxdale-2026' ),
				'singular_name' => __( 'Floor Plan', 'foxdale-2026' ),
				'add_new_item'  => __( 'Add New Floor Plan', 'foxdale-2026' ),
				'edit_item'     => __( 'Edit Floor Plan', 'foxdale-2026' ),
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-layout',
			'supports'     => array( 'title', 'thumbnail', 'page-attributes', 'custom-fields' ),
			'hierarchical' => false,
		)
	);

	register_taxonomy(
		'plan_type',
		'floor_plan',
		array(
			'labels'       => array(
				'name'          => __( 'Plan Types', 'foxdale-2026' ),
				'singular_name' => __( 'Plan Type', 'foxdale-2026' ),
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_rest' => true,
			'hierarchical' => true,
		)
	);

	register_post_meta(
		'floor_plan',
		'foxdale_specs',
		array(
			'type'          => 'string',
			'single'        => true,
			'show_in_rest'  => true,
			'auth_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	register_post_meta(
		'floor_plan',
		'foxdale_tour_url',
		array(
			'type'          => 'string',
			'single'        => true,
			'show_in_rest'  => true,
			'auth_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}
add_action( 'init', 'foxdale_register_floor_plans' );

/**
 * Seed the two plan-type terms on theme activation.
 */
function foxdale_seed_plan_types() {
	foreach ( array( 'cottage' => 'Cottage', 'apartment' => 'Apartment' ) as $slug => $name ) {
		if ( ! term_exists( $slug, 'plan_type' ) ) {
			wp_insert_term( $name, 'plan_type', array( 'slug' => $slug ) );
		}
	}
}
add_action( 'after_switch_theme', 'foxdale_seed_plan_types' );

/**
 * Sidebar meta box for the specs line and tour URL.
 */
function foxdale_floor_plan_meta_box() {
	add_meta_box(
		'foxdale-floor-plan-details',
		__( 'Plan Details', 'foxdale-2026' ),
		'foxdale_floor_plan_meta_box_html',
		'floor_plan',
		'side'
	);
}
add_action( 'add_meta_boxes', 'foxdale_floor_plan_meta_box' );

function foxdale_floor_plan_meta_box_html( $post ) {
	wp_nonce_field( 'foxdale_floor_plan_meta', 'foxdale_floor_plan_nonce' );
	$specs = get_post_meta( $post->ID, 'foxdale_specs', true );
	$tour  = get_post_meta( $post->ID, 'foxdale_tour_url', true );
	?>
	<p>
		<label for="foxdale_specs"><strong><?php esc_html_e( 'Specs line', 'foxdale-2026' ); ?></strong></label><br>
		<input type="text" id="foxdale_specs" name="foxdale_specs" class="widefat"
			value="<?php echo esc_attr( $specs ); ?>"
			placeholder="<?php esc_attr_e( 'Cottage · 1 BR · 1 BA · 845 sq ft', 'foxdale-2026' ); ?>">
	</p>
	<p>
		<label for="foxdale_tour_url"><strong><?php esc_html_e( 'Virtual tour URL (optional)', 'foxdale-2026' ); ?></strong></label><br>
		<input type="url" id="foxdale_tour_url" name="foxdale_tour_url" class="widefat"
			value="<?php echo esc_attr( $tour ); ?>" placeholder="https://youtu.be/…">
	</p>
	<p class="description"><?php esc_html_e( 'Set the plan drawing as the Featured Image and pick a Plan Type (Cottage or Apartment). Use the Order field to control display order.', 'foxdale-2026' ); ?></p>
	<?php
}

function foxdale_save_floor_plan_meta( $post_id ) {
	if ( ! isset( $_POST['foxdale_floor_plan_nonce'] ) ||
		! wp_verify_nonce( $_POST['foxdale_floor_plan_nonce'], 'foxdale_floor_plan_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( isset( $_POST['foxdale_specs'] ) ) {
		update_post_meta( $post_id, 'foxdale_specs', sanitize_text_field( wp_unslash( $_POST['foxdale_specs'] ) ) );
	}
	if ( isset( $_POST['foxdale_tour_url'] ) ) {
		update_post_meta( $post_id, 'foxdale_tour_url', esc_url_raw( wp_unslash( $_POST['foxdale_tour_url'] ) ) );
	}
}
add_action( 'save_post_floor_plan', 'foxdale_save_floor_plan_meta' );

/**
 * [foxdale_floor_plans] — filter buttons + plan grid + lightbox.
 */
function foxdale_floor_plans_shortcode() {
	$plans = get_posts(
		array(
			'post_type'      => 'floor_plan',
			'posts_per_page' => -1,
			'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
			'no_found_rows'  => true,
		)
	);

	if ( empty( $plans ) ) {
		return current_user_can( 'edit_posts' )
			? '<p><em>' . esc_html__( 'No floor plans yet. Add some under Floor Plans in the dashboard.', 'foxdale-2026' ) . '</em></p>'
			: '';
	}

	ob_start();
	?>
	<div class="plan-filters reveal" role="group" aria-label="<?php esc_attr_e( 'Filter floor plans', 'foxdale-2026' ); ?>">
		<button class="active" data-filter="all" aria-pressed="true"><?php esc_html_e( 'All Plans', 'foxdale-2026' ); ?></button>
		<button data-filter="cottage" aria-pressed="false"><?php esc_html_e( 'Cottages', 'foxdale-2026' ); ?></button>
		<button data-filter="apartment" aria-pressed="false"><?php esc_html_e( 'Apartments', 'foxdale-2026' ); ?></button>
	</div>

	<div class="plans">
		<?php foreach ( $plans as $plan ) : ?>
			<?php
			$terms = get_the_terms( $plan->ID, 'plan_type' );
			$type  = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->slug : 'cottage';
			$specs = get_post_meta( $plan->ID, 'foxdale_specs', true );
			$tour  = get_post_meta( $plan->ID, 'foxdale_tour_url', true );
			?>
			<figure class="plan reveal" data-type="<?php echo esc_attr( $type ); ?>">
				<?php
				echo get_the_post_thumbnail(
					$plan->ID,
					'large',
					array( 'loading' => 'lazy' )
				);
				?>
				<figcaption class="meta">
					<span class="name"><?php echo esc_html( get_the_title( $plan ) ); ?></span>
					<?php if ( $specs ) : ?>
						<span class="specs"><?php echo esc_html( $specs ); ?></span>
					<?php endif; ?>
					<?php if ( $tour ) : ?>
						<a class="tour" href="<?php echo esc_url( $tour ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Virtual Tour', 'foxdale-2026' ); ?></a>
					<?php endif; ?>
				</figcaption>
			</figure>
		<?php endforeach; ?>
	</div>

	<div class="lightbox" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Enlarged floor plan', 'foxdale-2026' ); ?>">
		<button class="lightbox-close" type="button" aria-label="<?php esc_attr_e( 'Close enlarged floor plan', 'foxdale-2026' ); ?>">&times;</button>
		<img src="" alt="">
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'foxdale_floor_plans', 'foxdale_floor_plans_shortcode' );
