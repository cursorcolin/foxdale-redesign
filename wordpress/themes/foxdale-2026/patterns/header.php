<?php
/**
 * Title: Site header (utility bar + navbar)
 * Slug: foxdale-2026/header
 * Inserter: no
 */
?>
<!-- wp:group {"className":"utility"} -->
<div class="wp-block-group utility"><!-- wp:group {"className":"wrap"} -->
<div class="wp-block-group wrap"><!-- wp:paragraph {"className":"tagline"} -->
<p class="tagline">Rooted in Values. Designed for Living.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"secondary"} -->
<p class="secondary"><a href="/about/">About</a> · <a href="/careers/">Careers</a> · <a href="/giving/">Giving</a> | <a href="tel:+18142383322">(814) 238-3322</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"tagName":"header","className":"site"} -->
<header class="wp-block-group site"><!-- wp:group {"className":"wrap navbar"} -->
<div class="wp-block-group wrap navbar"><!-- wp:image {"className":"logo"} -->
<figure class="wp-block-image logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo foxdale_img( 'logo.png' ); ?>" alt="<?php esc_attr_e( 'Foxdale Village — A Life Plan Community Guided by Quaker Values', 'foxdale-2026' ); ?>"/></a></figure>
<!-- /wp:image -->

<!-- wp:navigation {"className":"primary-nav","overlayMenu":"mobile","layout":{"type":"flex","justifyContent":"right"}} -->
<!-- wp:navigation-link {"label":"Home","url":"/"} /-->
<!-- wp:navigation-link {"label":"Life at Foxdale","url":"/life/"} /-->
<!-- wp:navigation-link {"label":"Residences","url":"/residences/"} /-->
<!-- wp:navigation-link {"label":"Healthcare","url":"/healthcare/"} /-->
<!-- wp:navigation-link {"label":"Our Campus","url":"/campus/"} /-->
<!-- wp:navigation-link {"label":"Plan Your Move","url":"/planning/"} /-->
<!-- wp:navigation-link {"label":"Schedule a Visit","url":"/visit/","className":"nav-cta"} /-->
<!-- /wp:navigation --></div>
<!-- /wp:group --></header>
<!-- /wp:group -->
