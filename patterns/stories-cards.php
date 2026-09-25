<?php
/**
 * Title: Stories: three cards
 * Slug: daren/stories-cards
 * Categories: daren-posts
 * Keywords: cards, grid, posts, category
 * Description: Three stories as cards, each photograph carrying its category on a tab.
 *
 * @package Daren
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"align":"full","className":"daren-cards-section","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull daren-cards-section"><!-- wp:query {"queryId":14,"query":{"perPage":3,"pages":0,"offset":5,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"className":"daren-cards","layout":{"type":"default"}} -->
<div class="wp-block-query daren-cards"><!-- wp:post-template {"layout":{"type":"default"}} -->
<!-- wp:group {"className":"daren-card","layout":{"type":"default"}} -->
<div class="wp-block-group daren-card"><!-- wp:group {"className":"daren-card__media","layout":{"type":"default"}} -->
<div class="wp-block-group daren-card__media"><!-- wp:post-featured-image {"isLink":true,"aspectRatio":"360/336"} /-->

<!-- wp:post-terms {"term":"category","className":"daren-tab"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"className":"daren-card__body","layout":{"type":"default"}} -->
<div class="wp-block-group daren-card__body"><!-- wp:group {"className":"daren-byline","style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
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
<!-- /wp:post-template --></div>
<!-- /wp:query --></div>
<!-- /wp:group -->
