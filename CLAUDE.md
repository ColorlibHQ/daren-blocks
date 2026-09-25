# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

**Daren 1.0.0** is a Colorlib **WordPress block theme** (full site editing) for a
personal journal about art, design and colour, rebuilt from the Daren HTML
template (`preview.colorlib.com/theme/daren/`, Bootstrap 4). Text domain and
slug `daren`. 26 patterns, 14 templates, 3 parts, 8 colour palettes x 5 type
pairings, visitor dark mode, a contact form and a newsletter sign-up with no
plugin, WooCommerce styling. **No companion plugin, no page builder, no jQuery.**

Distribution is **outside WordPress.org**: `Update URI` in style.css points at
`updates.colorlib.com`; `inc/updates.php` hooks `update_themes_{host}`. It is not
an HTML template — the Colorlib R2 preview/download flow and the HTML upgrade
phases in the global instructions do not apply.

The toolchain and `inc/` architecture came from Unioncorp (`~/Fresh Projects/
unioncorp-blocks`), which came from Pato; read `.dev/README.md` for commands.

Requires WP 6.6+, tested to 7.1, PHP 7.4+. No build step for the theme itself:
every file is committed as generated.

## The design, and where it lives

The home page is almost all posts. Each section is a **query block**, so it fills
itself from what the site publishes (`patterns/stories-*.php`):

| Section | Pattern | Query |
| --- | --- | --- |
| Opening banner: portrait 36% + landscape 63.5%, 810px, white cards laid over | `stories-banner` | 2 newest |
| Checkerboard: three columns, the middle one words-then-photo | `stories-checkerboard` | offset 2, 3 |
| Cards with the category on a white tab | `stories-cards` | offset 5, 3 |
| List (photo + rotated tab + bordered box) beside a sidebar | `stories-list-sidebar` | offset 8, 5; sidebar "From the archive" offset 13 |

Offsets mean the demo needs 16+ posts to show no repeats. Layout is CSS on the
query's class (`style.css`), not the block's grid layout, because the design's
columns are unequal and reorder per item (`:nth-child`).

Inner pages use the template's pink title band (`daren-page-banner`, `surface`),
then content beside the sidebar part at 67.5% / 32.5% (the template's 730/350).
Journal and tag/author/date archives are two-up cards; category and search are
the list. A single post's band carries its category, the title sits under the
featured image, as in the template.

## Conventions that matter

- **Generated, never hand-edited:** `theme.json` + `styles/**` from
  `.dev/build_theme.py`; `patterns/*.php` from `.dev/build_patterns.py`, then
  `normalize-blocks.mjs` rewrites them through the real serialiser. Templates and
  parts are small hand-written files that reference hidden patterns.
- **Palette slugs name jobs.** `surface` = the pink band, `subtle` = neutral
  panels/chips, `rule` = the 5px line between home sections, `accent` = the
  template's #ef1313 (**decorative only**, 4.40:1), `primary` = #d91010 for text,
  links and buttons, `primary-deep` = the charcoal buttons hover to, `dark` = the
  footer, `on-dark` = footer text, `overlay` = text that stays light on dark
  grounds, `on-primary` = button labels, `primary-lifted` = the brand colour on a
  dark ground (dark mode's `primary`, and the footer's newsletter button). **Any
  colour on a non-`base` ground needs its own slug.** Never name a slug after a
  core utility class (`text`, `background`, `border`, `link`).
- **Dark mode** (`assets/css/scheme.css`) redefines the preset variables on
  `.daren-dark`; `build_theme.py` reads those values back and audits them. A slug
  scheme.css names that the palette lacks fails the build.
- **Font sizes with no `fluid` key are NOT fixed** when fluid type is on: WordPress
  derives a clamp() and the 24px card titles came out 15.7px on a phone. Fixed
  sizes are emitted with `"fluid": false`; fluid ones reach their maximum at the
  template's 1200px breakpoint (`settings.typography.fluid.maxViewportWidth`).
- **The row under each story is three `core/read-more` blocks** with classes
  `daren-action--comments|time|share`; `inc/blocks.php` fills in the number.
  Core's Comments Link / Time to Read blocks only exist from WP 6.9 and this theme
  supports 6.6. Icons come from the class via `::before`, so they show in the
  editor too.
- **The wordmark's red letter** is `inc/blocks.php`: the first capital after a
  lower-case letter in the site title gets `daren-title-accent` (`primary`).
  Filter `daren_title_accent`.
- **Forms are shortcodes** (`[daren_contact_form]`, `[daren_newsletter_form]`),
  because patterns are expanded into stored page content where PHP never runs,
  and the Site Editor may copy the footer into the database. Honeypot, nonce,
  the return URL carried in a hidden field and checked with
  `wp_validate_redirect()` (`wp_get_referer()` is false for a self-posting form),
  POST/redirect/GET. `render_block_core/shortcode` runs `do_shortcode` so they
  work inside templates. The newsletter emails the site owner unless
  `daren_newsletter_handlers` returns true.
- **Labels are visually hidden, never missing.** The design shows placeholders
  only; placeholders are `muted` so they pass AA.
- **Icons**: Tabler (MIT) as CSS masks, `daren-icon--<name>` on the block itself.
  `dead-selectors.py` fails on a rule nothing emits and on an asset file nothing
  references.
- **Block styles live in style.css**, never behind `style_handle`.
- **Starter pages** (`inc/front-page-setup.php`): Home (page-no-title), Journal
  (posts page, slug `blog`), About, Contact, and a `wp_navigation` menu. The flag
  is claimed only after something was created; `admin_init` retries. Content goes
  through `wp_slash()`.
- **Section spacing**: sections carry their own padding; `.wp-site-blocks` and
  `main` gaps are zeroed so bands meet. Template section padding 100px is spacing
  `70`; the feature row's 140px is `80`.

## Verify before committing

See `.dev/README.md`. In short, against a Playground on port 9491: build →
normalize → refresh-pages → validate-blocks → editor-check → contrast (all
palettes, light and dark) → overflow → alignment → button-boundary →
dead-selectors → compare. Theme Check on the **built zip installed as `daren`**:
expected REQUIRED only `add_shortcode` (x2), the Unsplash reference and `Update
URI`; 0 warnings (`.dev/theme-check.mjs` + `.dev/blueprint-themecheck.json`).
Product page material is in `.dev/publish/` (`shoot.mjs` → `finish.py` →
`images/`, run against a Playground with the demo imported).

## Traps already paid for

- Pages are copies: after changing a pattern, `refresh-pages.mjs` or a fresh
  Playground, or every rendered check measures the old markup.
- Core's `.wp-block-post-author__byline { width: 100% }` breaks an inline byline;
  `.wp-block-separator:not(.is-style-wide):not(.is-style-dots)` (theme.css) caps a
  separator at 100px unless the style's selector outranks it.
- `.daren-page-content p a` underlined the tag cloud (it is a `<p>`): content-link
  rules exclude `p[class*="wp-block-"]`.
- Links transition their colour, so flipping dark mode faded every title in from
  dark; `.daren-scheme-switching` suspends transitions for two frames.
- **Photographs are traced, not trusted.** Every shipped and demo photo was
  reverse-searched (Yandex by URL or upload, TinEye, Bing) and matched to its
  original with SIFT before use; credits are in readme.txt (shipped) and the
  header of `.dev/demo/import.php` (demo). A template photo whose Unsplash page
  is gone, or that also sells on a stock library with an earlier ID, was
  replaced with a Pexels one. A Shutterstock copy with a LATER ID than the
  Unsplash upload is a resale of the free photo, not its source.
- The sidebar-strip photos (sprinkles, succulent, triangles) are 2.09:1 but
  render in square-ish boxes too; they are exported 1480 wide so the cover
  crop stays sharp.
- `wp eval` runs a required file inside a function: the importer uses no
  `global`. It creates its own author, never renames user 1 (on colorlibhub
  that is the network super admin).
