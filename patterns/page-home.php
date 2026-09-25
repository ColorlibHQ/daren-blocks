<?php
/**
 * Title: Page: home
 * Slug: daren/page-home
 * Categories: daren-pages
 * Description: The magazine home page: the opening banner, the checkerboard, three cards and the list with its sidebar.
 *
 * @package Daren
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:pattern {"slug":"daren/stories-banner"} /-->

<!-- wp:pattern {"slug":"daren/stories-checkerboard"} /-->

<!-- wp:group {"align":"full","className":"daren-rule","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull daren-rule"><!-- wp:separator {"className":"is-style-daren-band"} -->
<hr class="wp-block-separator has-alpha-channel-opacity is-style-daren-band"/>
<!-- /wp:separator --></div>
<!-- /wp:group -->

<!-- wp:pattern {"slug":"daren/stories-cards"} /-->

<!-- wp:group {"align":"full","className":"daren-rule","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull daren-rule"><!-- wp:separator {"className":"is-style-daren-band"} -->
<hr class="wp-block-separator has-alpha-channel-opacity is-style-daren-band"/>
<!-- /wp:separator --></div>
<!-- /wp:group -->

<!-- wp:pattern {"slug":"daren/stories-list-sidebar"} /-->
