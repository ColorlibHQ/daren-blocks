<?php
/**
 * Title: Sidebar
 * Slug: daren/sidebar
 * Keywords: sidebar
 * Description: Search, the latest stories, categories and tags.
 * Inserter: no
 *
 * @package Daren
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"className":"daren-sidebar","layout":{"type":"default"}} -->
<div class="wp-block-group daren-sidebar"><!-- wp:heading {"className":"is-style-daren-flag","style":{"typography":{"textAlign":"left"}},"fontSize":"xx-large"} -->
<h2 class="wp-block-heading has-text-align-left is-style-daren-flag has-xx-large-font-size">Search</h2>
<!-- /wp:heading -->

<!-- wp:search {"label":"Search","showLabel":false,"placeholder":"Search keyword","buttonText":"Search","buttonUseIcon":true,"className":"daren-sidebar__search"} /-->

<!-- wp:heading {"className":"is-style-daren-flag","style":{"typography":{"textAlign":"left"}},"fontSize":"xx-large"} -->
<h2 class="wp-block-heading has-text-align-left is-style-daren-flag has-xx-large-font-size">Latest stories</h2>
<!-- /wp:heading -->

<!-- wp:query {"queryId":11,"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"className":"daren-minis","layout":{"type":"default"}} -->
<div class="wp-block-query daren-minis"><!-- wp:post-template {"layout":{"type":"default"}} -->
<!-- wp:group {"className":"daren-mini","layout":{"type":"default"}} -->
<div class="wp-block-group daren-mini"><!-- wp:post-featured-image {"isLink":true,"aspectRatio":"360/172"} /-->

<!-- wp:group {"className":"daren-byline","style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
<div class="wp-block-group daren-byline"><!-- wp:post-author {"showAvatar":false,"byline":"By","className":"daren-byline__author"} /-->

<!-- wp:post-date {"metadata":{"bindings":{"datetime":{"source":"core/post-data","args":{"field":"date"}}}},"className":"daren-byline__date"} /--></div>
<!-- /wp:group -->

<!-- wp:post-title {"level":3,"isLink":true,"fontSize":"x-large"} /--></div>
<!-- /wp:group -->
<!-- /wp:post-template --></div>
<!-- /wp:query -->

<!-- wp:heading {"className":"is-style-daren-flag","style":{"typography":{"textAlign":"left"}},"fontSize":"xx-large"} -->
<h2 class="wp-block-heading has-text-align-left is-style-daren-flag has-xx-large-font-size">Categories</h2>
<!-- /wp:heading -->

<!-- wp:categories {"showPostCounts":true,"className":"daren-categories"} /-->

<!-- wp:heading {"className":"is-style-daren-flag","style":{"typography":{"textAlign":"left"}},"fontSize":"xx-large"} -->
<h2 class="wp-block-heading has-text-align-left is-style-daren-flag has-xx-large-font-size">Popular tags</h2>
<!-- /wp:heading -->

<!-- wp:tag-cloud {"numberOfTags":12,"smallestFontSize":"13px","largestFontSize":"13px","className":"daren-tags"} /--></div>
<!-- /wp:group -->
