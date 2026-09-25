<?php
/**
 * Title: Post footer
 * Slug: daren/hidden-post-footer
 * Description: Tags and a share link, the previous and next stories, and the author.
 * Inserter: no
 *
 * @package Daren
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"className":"daren-post-footer","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group daren-post-footer"><!-- wp:post-terms {"term":"post_tag","className":"daren-post-tags"} /-->

<!-- wp:read-more {"content":"Share","className":"daren-action\u002d\u002dshare"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"className":"daren-post-nav","style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group daren-post-nav"><!-- wp:post-navigation-link {"type":"previous","label":"Previous story","showTitle":true,"arrow":"chevron","className":"daren-post-nav__link"} /-->

<!-- wp:post-navigation-link {"label":"Next story","showTitle":true,"arrow":"chevron","className":"daren-post-nav__link"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"className":"is-style-daren-panel daren-author-box","layout":{"type":"constrained"}} -->
<div class="wp-block-group is-style-daren-panel daren-author-box"><!-- wp:post-author {"avatarSize":96,"showBio":true,"isLink":true,"className":"daren-author-box__author"} /--></div>
<!-- /wp:group -->
