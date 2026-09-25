<?php
/**
 * Title: Posts list with sidebar
 * Slug: daren/hidden-posts-list
 * Description: Category archives and search results: a list beside the sidebar.
 * Inserter: no
 *
 * @package Daren
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:columns {"className":"daren-with-sidebar","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|40","left":"var:preset|spacing|40"}}}} -->
<div class="wp-block-columns daren-with-sidebar"><!-- wp:column {"width":"67.5%"} -->
<div class="wp-block-column" style="flex-basis:67.5%"><!-- wp:query {"queryId":19,"query":{"perPage":6,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true},"className":"daren-list","layout":{"type":"default"}} -->
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
<p class="has-muted-color has-text-color">Nothing matched. Try another word, or browse the categories.</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results -->

<!-- wp:query-pagination {"paginationArrow":"chevron","className":"daren-pagination","layout":{"type":"flex","justifyContent":"center"}} -->
<!-- wp:query-pagination-previous {"label":"Newer"} /-->

<!-- wp:query-pagination-numbers /-->

<!-- wp:query-pagination-next {"label":"Older"} /-->
<!-- /wp:query-pagination --></div>
<!-- /wp:query --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"32.5%"} -->
<div class="wp-block-column" style="flex-basis:32.5%"><!-- wp:template-part {"slug":"sidebar","tagName":"aside"} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
