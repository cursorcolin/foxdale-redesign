<?php
/**
 * Title: Card grid (three-up)
 * Slug: foxdale-2026/card-grid
 * Categories: foxdale
 * Description: Centered section head plus three photo cards with optional links.
 */
?>
<!-- wp:group {"tagName":"section","className":"band"} -->
<section class="wp-block-group band"><!-- wp:group {"className":"wrap"} -->
<div class="wp-block-group wrap"><!-- wp:group {"className":"section-head center reveal"} -->
<div class="wp-block-group section-head center reveal"><!-- wp:paragraph {"className":"kicker"} -->
<p class="kicker">Kicker line</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Section heading.</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>One or two sentences introducing the cards below.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"cards"} -->
<div class="wp-block-group cards"><!-- wp:group {"tagName":"article","className":"card reveal"} -->
<article class="wp-block-group card reveal"><!-- wp:image {"className":"thumb"} -->
<figure class="wp-block-image thumb"><img src="<?php echo foxdale_img( 'pk-004.jpg' ); ?>" alt=""/></figure>
<!-- /wp:image -->

<!-- wp:group {"className":"body"} -->
<div class="wp-block-group body"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Card title</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Card copy goes here.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"more-wrap"} -->
<p class="more-wrap"><a class="more" href="/">Read more</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></article>
<!-- /wp:group -->

<!-- wp:group {"tagName":"article","className":"card reveal"} -->
<article class="wp-block-group card reveal"><!-- wp:image {"className":"thumb"} -->
<figure class="wp-block-image thumb"><img src="<?php echo foxdale_img( 'pk-006.jpg' ); ?>" alt=""/></figure>
<!-- /wp:image -->

<!-- wp:group {"className":"body"} -->
<div class="wp-block-group body"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Card title</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Card copy goes here.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></article>
<!-- /wp:group -->

<!-- wp:group {"tagName":"article","className":"card reveal"} -->
<article class="wp-block-group card reveal"><!-- wp:image {"className":"thumb"} -->
<figure class="wp-block-image thumb"><img src="<?php echo foxdale_img( 'pk-008.jpg' ); ?>" alt=""/></figure>
<!-- /wp:image -->

<!-- wp:group {"className":"body"} -->
<div class="wp-block-group body"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Card title</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Card copy goes here.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></article>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
