<?php
/**
 * Title: Header
 * Slug: daren/header
 * Keywords: header, navigation
 * Block Types: core/template-part/header
 * Description: The wordmark, the navigation, and search, social links and the dark mode switch.
 *
 * @package Daren
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"align":"full","className":"daren-header","backgroundColor":"base","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull daren-header has-base-background-color has-background"><!-- wp:group {"className":"daren-header__row","style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group daren-header__row"><!-- wp:group {"className":"daren-header__brand","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"center"}} -->
<div class="wp-block-group daren-header__brand"><!-- wp:site-logo {"width":120} /-->

<!-- wp:site-title {"level":0} /--></div>
<!-- /wp:group -->

<!-- wp:navigation {"className":"daren-nav","layout":{"type":"flex","justifyContent":"center","flexWrap":"nowrap"}} /-->

<!-- wp:group {"className":"daren-header__tools","style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right","verticalAlignment":"center"}} -->
<div class="wp-block-group daren-header__tools"><!-- wp:search {"label":"Search","showLabel":false,"placeholder":"Search here","buttonText":"Search","buttonPosition":"button-only","buttonUseIcon":true,"className":"daren-header__search"} /-->

<!-- wp:social-links {"iconColor":"contrast","iconColorValue":"#2a2a2a","size":"has-small-icon-size","className":"is-style-logos-only daren-header__social","layout":{"type":"flex","flexWrap":"nowrap"}} -->
<ul class="wp-block-social-links has-small-icon-size has-icon-color is-style-logos-only daren-header__social"><!-- wp:social-link {"url":"#","service":"facebook"} /-->

<!-- wp:social-link {"url":"#","service":"x"} /-->

<!-- wp:social-link {"url":"#","service":"instagram"} /-->

<!-- wp:social-link {"url":"#","service":"pinterest"} /--></ul>
<!-- /wp:social-links -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"daren-scheme-toggle"} -->
<div class="wp-block-button daren-scheme-toggle"><a class="wp-block-button__link wp-element-button" href="#"><span class="screen-reader-text">Switch between light and dark mode</span></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
