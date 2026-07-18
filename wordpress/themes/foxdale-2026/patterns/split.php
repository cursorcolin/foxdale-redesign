<?php
/**
 * Title: Split — text and photo
 * Slug: foxdale-2026/split
 * Categories: foxdale
 * Description: Two-column band with kicker, heading, copy, button, and a framed photo.
 */
?>
<!-- wp:group {"tagName":"section","className":"band paper"} -->
<section class="wp-block-group band paper"><!-- wp:group {"className":"wrap"} -->
<div class="wp-block-group wrap"><!-- wp:group {"className":"split reveal"} -->
<div class="wp-block-group split reveal"><!-- wp:group -->
<div class="wp-block-group"><!-- wp:paragraph {"className":"kicker"} -->
<p class="kicker">Kicker line</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Section heading goes here.</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>First paragraph of supporting copy.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Second paragraph of supporting copy.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"btn-ghost"} -->
<div class="wp-block-button btn-ghost"><a class="wp-block-button__link wp-element-button" href="/">Call to Action</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->

<!-- wp:image {"className":"photo"} -->
<figure class="wp-block-image photo"><img src="<?php echo foxdale_img( 'pk-003.jpg' ); ?>" alt=""/></figure>
<!-- /wp:image --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
