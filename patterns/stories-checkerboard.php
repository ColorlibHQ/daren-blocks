<?php
/**
 * Title: Stories: checkerboard
 * Slug: daren/stories-checkerboard
 * Categories: daren-posts
 * Keywords: featured, grid, posts
 * Description: Three stories in a row, photograph and words alternating like a checkerboard.
 *
 * @package Daren
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"align":"full","className":"daren-checker-section","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull daren-checker-section"><!-- wp:query {"queryId":13,"query":{"perPage":3,"pages":0,"offset":2,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"className":"daren-checker","layout":{"type":"default"}} -->
<div class="wp-block-query daren-checker"><!-- wp:post-template {"layout":{"type":"default"}} -->
<!-- wp:group {"className":"daren-checker__item","layout":{"type":"default"}} -->
<div class="wp-block-group daren-checker__item"><!-- wp:post-featured-image {"isLink":true,"aspectRatio":"380/310","className":"daren-checker__image"} /-->

<!-- wp:group {"className":"daren-checker__body","layout":{"type":"default"}} -->
<div class="wp-block-group daren-checker__body"><!-- wp:post-terms {"term":"category","className":"daren-label"} /-->

<!-- wp:post-title {"level":3,"isLink":true,"fontSize":"xx-large"} /-->

<!-- wp:group {"className":"daren-byline","style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
<div class="wp-block-group daren-byline"><!-- wp:post-author {"showAvatar":false,"byline":"By","className":"daren-byline__author"} /-->

<!-- wp:post-date {"metadata":{"bindings":{"datetime":{"source":"core/post-data","args":{"field":"date"}}}},"className":"daren-byline__date"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"className":"daren-actions","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
<div class="wp-block-group daren-actions"><!-- wp:read-more {"content":"Comments","className":"daren-action\u002d\u002dcomments"} /-->

<!-- wp:read-more {"content":"min read","className":"daren-action\u002d\u002dtime"} /-->

<!-- wp:read-more {"content":"Share","className":"daren-action\u002d\u002dshare"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
<!-- /wp:post-template --></div>
<!-- /wp:query --></div>
<!-- /wp:group -->
