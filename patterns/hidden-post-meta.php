<?php
/**
 * Title: Post meta
 * Slug: daren/hidden-post-meta
 * Description: Author, date, comments and reading time for a single post.
 * Inserter: no
 *
 * @package Daren
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"className":"daren-byline daren-post-meta","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
<div class="wp-block-group daren-byline daren-post-meta"><!-- wp:post-author {"showAvatar":false,"byline":"By","className":"daren-byline__author"} /-->

<!-- wp:post-date {"metadata":{"bindings":{"datetime":{"source":"core/post-data","args":{"field":"date"}}}},"className":"daren-byline__date"} /-->

<!-- wp:read-more {"content":"Comments","className":"daren-action\u002d\u002dcomments"} /-->

<!-- wp:read-more {"content":"min read","className":"daren-action\u002d\u002dtime"} /--></div>
<!-- /wp:group -->
