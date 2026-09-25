<?php
/**
 * Title: Stories: list with sidebar
 * Slug: daren/stories-list-sidebar
 * Categories: daren-posts
 * Keywords: list, posts, sidebar, archive
 * Description: Five stories in a list beside a sidebar with search, older stories, categories and tags.
 *
 * @package Daren
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"align":"full","className":"daren-list-section","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull daren-list-section" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:columns {"className":"daren-with-sidebar","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|40","left":"var:preset|spacing|40"}}}} -->
<div class="wp-block-columns daren-with-sidebar"><!-- wp:column {"width":"67.5%"} -->
<div class="wp-block-column" style="flex-basis:67.5%"><!-- wp:query {"queryId":15,"query":{"perPage":5,"pages":0,"offset":8,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"className":"daren-list","layout":{"type":"default"}} -->
<div class="wp-block-query daren-list"><!-- wp:post-template {"layout":{"type":"default"}} -->
<!-- wp:group {"className":"daren-row","layout":{"type":"default"}} -->
<div class="wp-block-group daren-row"><!-- wp:group {"className":"daren-row__media","layout":{"type":"default"}} -->
<div class="wp-block-group daren-row__media"><!-- wp:post-featured-image {"isLink":true,"aspectRatio":"350/340"} /-->

<!-- wp:post-terms {"term":"category","className":"daren-tab"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"className":"daren-row__body","layout":{"type":"default"}} -->
<div class="wp-block-group daren-row__body"><!-- wp:group {"className":"daren-byline","style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
<div class="wp-block-group daren-byline"><!-- wp:post-author {"showAvatar":false,"byline":"By","className":"daren-byline__author"} /-->

<!-- wp:post-date {"metadata":{"bindings":{"datetime":{"source":"core/post-data","args":{"field":"date"}}}},"className":"daren-byline__date"} /--></div>
<!-- /wp:group -->

<!-- wp:post-title {"level":3,"isLink":true,"fontSize":"xx-large"} /-->

<!-- wp:group {"className":"daren-actions","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
<div class="wp-block-group daren-actions"><!-- wp:read-more {"content":"Comments","className":"daren-action\u002d\u002dcomments"} /-->

<!-- wp:read-more {"content":"min read","className":"daren-action\u002d\u002dtime"} /-->

<!-- wp:read-more {"content":"Share","className":"daren-action\u002d\u002dshare"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
<!-- /wp:post-template -->

<!-- wp:query-no-results -->
<!-- wp:paragraph {"textColor":"muted"} -->
<p class="has-muted-color has-text-color">Nothing has been published here yet.</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results -->

<!-- wp:query-pagination {"className":"daren-more","layout":{"type":"flex","justifyContent":"center"}} -->
<!-- wp:query-pagination-next {"label":"More stories"} /-->
<!-- /wp:query-pagination --></div>
<!-- /wp:query --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"32.5%"} -->
<div class="wp-block-column" style="flex-basis:32.5%"><!-- wp:group {"className":"daren-sidebar","layout":{"type":"default"}} -->
<div class="wp-block-group daren-sidebar"><!-- wp:heading {"className":"is-style-daren-flag","style":{"typography":{"textAlign":"left"}},"fontSize":"xx-large"} -->
<h2 class="wp-block-heading has-text-align-left is-style-daren-flag has-xx-large-font-size">Search</h2>
<!-- /wp:heading -->

<!-- wp:search {"label":"Search","showLabel":false,"placeholder":"Search keyword","buttonText":"Search","buttonUseIcon":true,"className":"daren-sidebar__search"} /-->

<!-- wp:heading {"className":"is-style-daren-flag","style":{"typography":{"textAlign":"left"}},"fontSize":"xx-large"} -->
<h2 class="wp-block-heading has-text-align-left is-style-daren-flag has-xx-large-font-size">From the archive</h2>
<!-- /wp:heading -->

<!-- wp:query {"queryId":16,"query":{"perPage":3,"pages":0,"offset":13,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"className":"daren-minis","layout":{"type":"default"}} -->
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
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
