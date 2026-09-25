<?php
/**
 * Title: Stories: opening banner
 * Slug: daren/stories-banner
 * Categories: daren-posts
 * Keywords: hero, banner, featured, latest
 * Description: The two newest stories side by side, full width: a tall photograph and a wide one, each with its title on a white card.
 *
 * @package Daren
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"align":"full","className":"daren-banner","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull daren-banner"><!-- wp:query {"queryId":12,"query":{"perPage":2,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"align":"full","className":"daren-banner__query","layout":{"type":"default"}} -->
<div class="wp-block-query alignfull daren-banner__query"><!-- wp:post-template {"layout":{"type":"default"}} -->
<!-- wp:group {"className":"daren-banner__item","layout":{"type":"default"}} -->
<div class="wp-block-group daren-banner__item"><!-- wp:post-featured-image {"isLink":true,"aspectRatio":"auto","className":"daren-banner__image"} /-->

<!-- wp:group {"className":"daren-banner__card","layout":{"type":"default"}} -->
<div class="wp-block-group daren-banner__card"><!-- wp:post-terms {"term":"category","className":"daren-label"} /-->

<!-- wp:post-title {"isLink":true,"fontSize":"heading"} /-->

<!-- wp:group {"className":"daren-byline","style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
<div class="wp-block-group daren-byline"><!-- wp:post-author {"showAvatar":false,"byline":"By","className":"daren-byline__author"} /-->

<!-- wp:post-date {"metadata":{"bindings":{"datetime":{"source":"core/post-data","args":{"field":"date"}}}},"className":"daren-byline__date"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
<!-- /wp:post-template --></div>
<!-- /wp:query --></div>
<!-- /wp:group -->
