<?php
/**
 * Title: Interior page hero
 * Slug: foxdale-2026/page-hero
 * Categories: foxdale
 * Description: Shorter photo hero used at the top of interior pages.
 */
?>
<!-- wp:group {"tagName":"section","className":"page-hero"} -->
<section class="wp-block-group page-hero"><!-- wp:image {"className":"hero-bg"} -->
<figure class="wp-block-image hero-bg"><img src="<?php echo foxdale_img( 'pk-006.jpg' ); ?>" alt=""/></figure>
<!-- /wp:image -->

<!-- wp:group {"className":"wrap"} -->
<div class="wp-block-group wrap"><!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">Page headline&hellip; <em>with an accent.</em></h1>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>A one-sentence introduction to the page.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
