# Daren

A magazine-style block theme for a personal journal about art, design and colour.

A free WordPress block theme by [Colorlib](https://colorlib.com/). Full site editing,
no page builder and no plugins required.

- **Theme page:** https://colorlib.com/wp/themes/daren/
- **Live demo:** https://colorlibhub.com/daren-blocks/
- **Download:** https://updates.colorlib.com/download/theme/daren.zip (or the zip attached to the [latest release](../../releases/latest))

## Description

Daren is a full site editing theme for a blog that is mostly pictures and the stories behind them. The home page opens on two stories side by side — a tall photograph and a wide one, each with its title on a white card — then a checkerboard of three more, a row of cards with their category on a tab, and a long list beside a sidebar. The journal, categories, tags and search each have their own layout, and a single post has room for a quotation, the author and the comments.

Every one of those sections is a query, so it fills itself from whatever you publish. The About and Contact pages, the footer and the newsletter sign-up are written for the journal the theme was designed around and are yours to rewrite.

Eight colour palettes and five type pairings, each checked for contrast before release rather than by eye. Visitor-facing dark mode that follows the reader's system setting until they choose for themselves. A contact form and a newsletter sign-up that need no plugin. WooCommerce is styled if you install it and loads nothing if you do not.

## Two versions

This repository is the **block theme**. The same design also exists as an
**Elementor edition** for sites built with Elementor: [live demo](https://colorlibhub.com/daren/),
[source](https://github.com/ColorlibHQ/daren). It needs the free Elementor plugin. For a new site
the block theme is the one to use.

## Installation

1. In WordPress, go to Appearance → Themes → Add New Theme → Upload Theme. 2. Choose daren.zip and click Install Now, then Activate. 3. On a new site, Daren creates Home, Journal, About and Contact pages and a menu for them. On a site that already has pages it leaves them alone. 4. Appearance → Editor is where the header, footer, colours and templates live.

The theme updates itself from colorlib.com: it is distributed outside the
WordPress.org directory, so it checks `updates.colorlib.com` for new versions.

## Development

The files in `.dev/` generate and check the theme (palettes, patterns, block
validation, rendered contrast, overflow and alignment checks) and build the zip.
They are not part of the distributed theme. See `.dev/README.md` where present,
and `CLAUDE.md` for the conventions.

## Licence

GNU General Public License v2 or later. Photographs and fonts carry their own
licences, listed in `readme.txt`.
