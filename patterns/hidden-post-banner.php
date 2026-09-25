<?php
/**
 * Title: Post category band
 * Slug: daren/hidden-post-banner
 * Description: The tinted band above a post, carrying its category.
 * Inserter: no
 *
 * @package Daren
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"align":"full","className":"daren-page-banner","backgroundColor":"surface","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull daren-page-banner has-surface-background-color has-background"><!-- wp:post-terms {"term":"category","className":"daren-banner-terms","style":{"typography":{"textAlign":"center"}}} /--></div>
<!-- /wp:group -->
