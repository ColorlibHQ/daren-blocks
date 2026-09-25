<?php
/**
 * Title: Comments
 * Slug: daren/hidden-comments
 * Description: The comments and the reply form for a single post.
 * Inserter: no
 *
 * @package Daren
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:comments {"className":"daren-comments"} -->
<div class="wp-block-comments daren-comments"><!-- wp:comments-title {"showPostTitle":false,"fontSize":"large"} /-->

<!-- wp:comment-template -->
<!-- wp:group {"className":"daren-comment","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"top"}} -->
<div class="wp-block-group daren-comment"><!-- wp:avatar {"size":70,"style":{"border":{"radius":"50%"}}} /-->

<!-- wp:group {"className":"daren-comment__body","layout":{"type":"default"}} -->
<div class="wp-block-group daren-comment__body"><!-- wp:comment-content /-->

<!-- wp:group {"className":"daren-comment__meta","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
<div class="wp-block-group daren-comment__meta"><!-- wp:comment-author-name {"fontSize":"medium"} /-->

<!-- wp:comment-date {"fontSize":"small"} /-->

<!-- wp:comment-reply-link {"fontSize":"small"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
<!-- /wp:comment-template -->

<!-- wp:comments-pagination {"layout":{"type":"flex","justifyContent":"center"}} -->
<!-- wp:comments-pagination-previous /-->

<!-- wp:comments-pagination-numbers /-->

<!-- wp:comments-pagination-next /-->
<!-- /wp:comments-pagination -->

<!-- wp:post-comments-form /--></div>
<!-- /wp:comments -->
