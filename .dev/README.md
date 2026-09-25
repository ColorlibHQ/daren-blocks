# Build tooling

Nothing in `patterns/`, `theme.json` or `styles/` is written by hand. Edit the
generator, run it, and commit what it produces. `.dev/` never ships: the zip is
built without it (`build-zip.sh`).

```bash
python3 .dev/build_theme.py        # theme.json + styles/colors/* + styles/typography/* (audits contrast first)
python3 .dev/build_patterns.py     # patterns/*.php
node    .dev/build-fonts.mjs       # assets/fonts/*.woff2 (only when the faces change)
python3 .dev/make-pot.py           # languages/daren.pot (or `wp i18n make-pot` where WP-CLI exists)
```

## A WordPress to check against

```bash
npx -y @wp-playground/cli@3.1.54 server --port=9491 --php=8.3 --wp=latest \
  --mount-before-install="$PWD:/wordpress/wp-content/themes/daren" \
  --blueprint=.dev/blueprint.json --login
export WP_URL=http://127.0.0.1:9491 WP_USER=admin WP_PASS=password
```

The blueprint activates the theme (which builds the starter pages) and then
runs `.dev/demo/import.php`: seventeen posts with the HTML template's own
photographs, six categories, one author, three comments. Demo content is never
in the theme; `import.php` also documents how to load it on a real demo site.

The Playground's `--login` logs *every* visitor in. The screenshot and compare
scripts hide the admin bar so that what they capture is what a visitor sees.

## The order that matters

```bash
python3 .dev/build_patterns.py
node .dev/normalize-blocks.mjs     # re-serialise every pattern as the editor would
node .dev/refresh-pages.mjs        # rebuild the starter pages from the new patterns
node .dev/validate-blocks.mjs      # fail on anything invalid: templates, parts, patterns, pages, menus
```

Generated block markup is a guess until the editor has seen it: block comment
attributes must match what a block's `save()` writes, or the editor shows
"unexpected or invalid content" while the front end looks perfect.
`normalize-blocks.mjs` stashes the `<?php … ?>` snippets first — identical
snippets share one token — and writes back the real serialiser's output.

Starter pages are **copies** made at activation, so a pattern change does not
reach them. Every rendered check runs against those pages: run
`refresh-pages.mjs` (development sites only — it replaces the pages) or start a
fresh Playground before believing a check.

## The checks

```bash
node .dev/editor-check.mjs                          # every pattern opened in the editor: no invalid/missing
                                                    # blocks, no empty placeholders, icons drawn; shots in .dev/editor/
DAREN_PALETTES=all node .dev/contrast-rendered.mjs  # measured text contrast, 8 palettes x 8 pages
DAREN_DARK=1 DAREN_PALETTES=all node .dev/contrast-rendered.mjs
node .dev/overflow-check.mjs                        # elements wider than their parent, 4 widths
node .dev/alignment-check.mjs                       # constrained children that took the cap but not the centring
node .dev/button-boundary.mjs                       # every button visible as a button (fill/border 3:1, label 4.5:1)
DAREN_DARK=1 node .dev/button-boundary.mjs
python3 .dev/dead-selectors.py                      # CSS nothing emits, unstyled block styles, unused assets
node .dev/compare.mjs                               # template vs theme, side by side, 1440 and 390 -> .dev/compare/
node .dev/screenshot.mjs http://127.0.0.1:9491/ screenshot.png
bash .dev/build-zip.sh "$SCRATCH/daren-build"      # the distributable (never a fixed /tmp path)
node .dev/theme-check.mjs                           # Theme Check on the BUILT theme; see its header
node .dev/publish/shoot.mjs && python3 .dev/publish/finish.py   # product page screenshots
```

All page-walking checks take `DAREN_PATHS=/,/about/` to narrow the pages; the
default list covers home, the journal, About, Contact, a category, a tag, a
single post, search results and a 404.

`build_theme.py` audits every palette before writing and **refuses to emit one
that fails WCAG AA** on any pair the design produces, light and dark. It reads
the dark-mode values out of `assets/css/scheme.css` so the two cannot drift, and
refuses any slug scheme.css names that the palette does not define.

## Spacing, sizes and colour are vocabularies, not values

`sp()` refuses a spacing step that is not registered, `size_slug()` a font size,
and patterns name palette slugs, never a hex value — an undefined preset makes
WordPress drop the declaration silently, and a literal would not follow the
eight palettes.
