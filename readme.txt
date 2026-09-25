=== Daren ===

Contributors: colorlib
Requires at least: 6.6
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.1
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: blog, news, portfolio, full-site-editing, block-patterns, block-styles, template-editing, wide-blocks, accessibility-ready, translation-ready, custom-colors, custom-menu, custom-logo, featured-images, threaded-comments, one-column, two-columns, right-sidebar, rtl-language-support, sticky-post, theme-options

A magazine-style block theme for a personal journal about art, design and colour.

== Description ==

Daren is a full site editing theme for a blog that is mostly pictures and the
stories behind them. The home page opens on two stories side by side — a tall
photograph and a wide one, each with its title on a white card — then a
checkerboard of three more, a row of cards with their category on a tab, and a
long list beside a sidebar. The journal, categories, tags and search each have
their own layout, and a single post has room for a quotation, the author and
the comments.

Every one of those sections is a query, so it fills itself from whatever you
publish. The About and Contact pages, the footer and the newsletter sign-up are
written for the journal the theme was designed around and are yours to rewrite.

Eight colour palettes and five type pairings, each checked for contrast before
release rather than by eye. Visitor-facing dark mode that follows the reader's
system setting until they choose for themselves. A contact form and a newsletter
sign-up that need no plugin. WooCommerce is styled if you install it and loads
nothing if you do not.

== Installation ==

1. In WordPress, go to Appearance → Themes → Add New Theme → Upload Theme.
2. Choose daren.zip and click Install Now, then Activate.
3. On a new site, Daren creates Home, Journal, About and Contact pages and a menu
   for them. On a site that already has pages it leaves them alone.
4. Appearance → Editor is where the header, footer, colours and templates live.

== Frequently Asked Questions ==

= The home page is nearly empty. Where are the stories? =

The home page lists your posts: the two newest in the opening banner, the next
three in the checkerboard, three more as cards and the rest in the list. Publish
posts with featured images and it fills itself. Each section can be changed or
removed in the editor like any other block.

= Why is one letter of my site title red? =

The template's wordmark is "DarEn." with the E in red. If your site title is
written in camel case — a capital letter straight after a lower-case one — that
capital is set in the brand colour. A title with no such letter is left exactly
as it is. To switch it off, add
add_filter( 'daren_title_accent', '__return_false' ); to a child theme or a small
plugin, or use a logo instead (Appearance → Editor → Header).

= Do I need a plugin for the contact form? =

No. The form is part of the theme and sends with WordPress's own wp_mail(). If
your host cannot send mail, install any SMTP plugin — whatever fixes a lost
password-reset email fixes the form too.

= Where do newsletter sign-ups go? =

By default the theme emails you each new address, so nothing is lost and nothing
needs installing. To send them to a mailing list instead, hook the
daren_newsletter_handlers filter and return true once the address is on your
list; the theme then stops emailing you. MailPoet, Mailchimp and Sendy can all
be connected that way.

= What are "Comments", "min read" and "Share" under each story? =

They are Read More blocks with a class that gives each a job: the number of
comments, an estimate of the reading time, and a Share link that opens the
phone's share sheet or copies the story's address. Change the words in the
editor; the numbers are filled in for you.

= How do I change the colours? =

Appearance → Editor → Styles → Browse styles. Eight palettes are included, and
each one restyles every section. To change a single colour, open Styles →
Colors → Edit palette.

= Why is the red not quite the template's red? =

The template's #ef1313 is 4.40:1 against white, just under the 4.5:1 WCAG AA
asks for text. Daren keeps it as the decorative accent — the rules and icons —
and sets text, links and buttons in #d91010, the same red a shade deeper.

= Can I turn dark mode off? =

Yes: add add_filter( 'daren_enable_dark_mode', '__return_false' ); to a child
theme or a small plugin.

== Theme Check ==

Theme Check reports three REQUIRED findings and no warnings. All three are
deliberate, and each is the price of something the theme does on purpose.

1. **add_shortcode() in inc/contact.php and inc/newsletter.php.** The contact
   form and the newsletter sign-up have to keep working after a pattern is
   expanded into a page's content, or copied into a template by the Site
   Editor, where PHP never runs. A shortcode is the only mechanism WordPress
   offers for that. Moving them to a plugin would mean the forms stop working
   the moment the plugin is disabled, on pages the theme built.
2. **Unsplash photographs.** Three of the About page's photographs are
   Unsplash-licensed (two more are Pexels-licensed), which is not
   GPL-compatible. Replace them with your own and the finding goes with them.
3. **Update URI in style.css.** This theme is distributed outside the
   WordPress.org directory and checks colorlib.com for its own updates. A theme
   inside the directory must not carry this header.

== Copyright ==

Daren WordPress Theme, (C) 2026 Colorlib.
Daren is distributed under the terms of the GNU GPL v2 or later.

Open Sans and Source Serif Pro
License: SIL Open Font License 1.1
Source: https://fontsource.org/

Tabler Icons
License: MIT, https://github.com/tabler/tabler-icons/blob/main/LICENSE
Source: https://tabler.io/icons

Photographs
Each was traced to its source by reverse image search.
* assets/images/studio-set.webp: Lionel Gustave, Unsplash,
  https://unsplash.com/photos/c1rOy44wuts
* assets/images/topic-colour.webp: Sharon McCutcheon, Unsplash,
  https://unsplash.com/photos/F-uHUndbXhY
* assets/images/topic-still-life.webp: Rémi Müller, Unsplash,
  https://unsplash.com/photos/hYAmjIvLALE
* assets/images/topic-walls.webp: Toa Heftiba Şinca, Pexels,
  https://www.pexels.com/photo/1194420/
* assets/images/portrait.webp: Kampus Production, Pexels,
  https://www.pexels.com/photo/7514883/
Unsplash License, https://unsplash.com/license
Pexels License, https://www.pexels.com/license/

== Changelog ==

= 1.0.1 =
* The sample email address in the patterns is now hello@yourdomain.com. The 1.0.0 address used a domain that belongs, or could belong, to someone else.

= 1.0.0 =
* Initial release.
