#!/usr/bin/env python3
"""
Generates theme.json and every style variation from one table of colours.

Daren's red, #ef1313, is 4.40:1 on white and 4.00:1 on the pink page banner.
That is fine for a rule, an icon or the letter in the wordmark, and it fails AA
for the category labels, links and button labels the design sets in it. So the
palette keeps it as `accent`, for decoration only, and `primary` is #d91010 —
the same red a shade deeper, 5.21:1 on white and 4.74:1 on the banner. Side by
side the two are hard to tell apart, which is the point.

The template's grey for bylines and dates, #8a8a8a, is 3.45:1 on white. The
design's paragraph grey, #646464 (5.92:1), carries both here.

Nothing is eyeballed: audit() computes every pair the design actually produces
and refuses to write a palette that fails, and it reads the dark-mode numbers
straight out of assets/css/scheme.css so the two cannot drift apart.

Usage:  python3 .dev/build_theme.py
"""

import collections
import json
import os
import re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
os.chdir(ROOT)

# ---------------------------------------------------------------------------
# Palette
# ---------------------------------------------------------------------------
# Fifteen slugs, the same in every variation, so a pattern written against them
# works under all of them. Each names a JOB, not a colour:
#
# - `surface` is the tinted band the page titles sit on (the template's pink).
# - `subtle` is the neutral panel: tag chips, the quotation box, the author box.
# - `rule` is the thick 5px line the home page draws between its sections.
# - `overlay` is text on a photograph and on the footer, which stays near-white
#   in every palette and in dark mode. Writing that as `base` is what turns a
#   dark palette's footer black-on-black.
# - `on-dark` is body text on the footer; `on-primary` is a label on a red fill.
#   Neither is a value a pattern can hardcode and still work in eight palettes.
PALETTE = [
    ("Base",         "base"),
    ("Surface",      "surface"),
    ("Subtle",       "subtle"),
    ("Contrast",     "contrast"),
    ("Muted",        "muted"),
    ("Primary",      "primary"),
    ("Primary deep", "primary-deep"),
    # The brand colour as dark mode shows it, and as the footer's button uses
    # it. A mix toward white cannot serve every palette: any share of white
    # that keeps Scarlet red leaves Ink's near-black accent under 3:1 on a dark
    # page. So each palette names its own, and the audit measures it there.
    ("Primary on dark", "primary-lifted"),
    ("Accent",       "accent"),
    ("Rule",         "rule"),
    ("Divider",      "divider"),
    ("Success",      "success"),
    ("Dark",         "dark"),
    ("On dark",      "on-dark"),
    ("Overlay",      "overlay"),
    ("On primary",   "on-primary"),
]

COLOR_SETS = collections.OrderedDict([
    # The template's own colours. `primary-deep` is the charcoal its buttons turn
    # to on hover, not a darker red.
    ("colors-1-scarlet", ("Scarlet", {
        "base": "#ffffff", "primary-lifted": "#ff4d4d", "surface": "#fff1f1", "subtle": "#f2f3f4", "contrast": "#2a2a2a",
        "muted": "#646464", "primary": "#d91010", "primary-deep": "#2a2a2a", "accent": "#ef1313",
        "rule": "#ffe4e4", "divider": "#e9ecee", "success": "#1f7a45", "dark": "#111516",
        "on-dark": "#8f8f8f", "overlay": "#ffffff", "on-primary": "#ffffff",
    })),
    ("colors-2-cobalt", ("Cobalt", {
        "base": "#ffffff", "primary-lifted": "#86abff", "surface": "#eef3ff", "subtle": "#f1f3f7", "contrast": "#1d2433",
        "muted": "#5a6275", "primary": "#1f58c9", "primary-deep": "#1d2433", "accent": "#2f6fec",
        "rule": "#dae5ff", "divider": "#e3e8f0", "success": "#1f7a45", "dark": "#0e1422",
        "on-dark": "#98a3b8", "overlay": "#ffffff", "on-primary": "#ffffff",
    })),
    ("colors-3-jade", ("Jade", {
        "base": "#ffffff", "primary-lifted": "#4fd19a", "surface": "#ebf7f1", "subtle": "#f0f4f2", "contrast": "#1c2a24",
        "muted": "#56655e", "primary": "#0b7550", "primary-deep": "#1c2a24", "accent": "#13a36b",
        "rule": "#d2eee1", "divider": "#e1e9e5", "success": "#0b7550", "dark": "#0d1714",
        "on-dark": "#95a59d", "overlay": "#ffffff", "on-primary": "#ffffff",
    })),
    # The template's link orange, #ff8b23, is 2.34:1 on white: decorative only.
    ("colors-4-tangerine", ("Tangerine", {
        "base": "#ffffff", "primary-lifted": "#ffa45c", "surface": "#fff3e8", "subtle": "#f5f3f1", "contrast": "#2b2521",
        "muted": "#645c56", "primary": "#b04f00", "primary-deep": "#2b2521", "accent": "#ff8b23",
        "rule": "#ffe0c4", "divider": "#ece7e2", "success": "#1f7a45", "dark": "#1a1512",
        "on-dark": "#a39a93", "overlay": "#ffffff", "on-primary": "#ffffff",
    })),
    ("colors-5-orchid", ("Orchid", {
        "base": "#ffffff", "primary-lifted": "#f08ad0", "surface": "#fbeff8", "subtle": "#f4f2f4", "contrast": "#2b1f29",
        "muted": "#65596a", "primary": "#9c2378", "primary-deep": "#2b1f29", "accent": "#d0399f",
        "rule": "#f6d9ee", "divider": "#ece4ea", "success": "#1f7a45", "dark": "#1a1119",
        "on-dark": "#a697a4", "overlay": "#ffffff", "on-primary": "#ffffff",
    })),
    # Black and white, for a journal that lets its photographs carry the colour.
    ("colors-6-ink", ("Ink", {
        "base": "#ffffff", "primary-lifted": "#d4d4d4", "surface": "#f3f3f1", "subtle": "#f2f2f2", "contrast": "#1a1a1a",
        "muted": "#5e5e5e", "primary": "#1a1a1a", "primary-deep": "#555555", "accent": "#1a1a1a",
        "rule": "#e6e6e3", "divider": "#e6e6e6", "success": "#1f7a45", "dark": "#141414",
        "on-dark": "#9a9a9a", "overlay": "#ffffff", "on-primary": "#ffffff",
    })),
    # Dark palettes: `base` is the page, so it is dark here, and a button is a
    # light red with a DARK label — the opposite of the usual rule, which is why
    # button_text() measures instead of assuming.
    ("colors-7-midnight", ("Midnight", {
        "base": "#131517", "primary-lifted": "#ff6f6f", "surface": "#221b1c", "subtle": "#1c1f22", "contrast": "#f2f2f1",
        "muted": "#a9adb2", "primary": "#ff6f6f", "primary-deep": "#f2f2f1", "accent": "#ef1313",
        "rule": "#3a2427", "divider": "#2b2f33", "success": "#57d693", "dark": "#0b0c0d",
        "on-dark": "#a9adb2", "overlay": "#ffffff", "on-primary": "#0b0c0d",
    })),
    ("colors-8-graphite", ("Graphite", {
        "base": "#16181d", "primary-lifted": "#8fb4ff", "surface": "#1c2330", "subtle": "#1d2026", "contrast": "#eef1f5",
        "muted": "#a6adb8", "primary": "#8fb4ff", "primary-deep": "#eef1f5", "accent": "#2f6fec",
        "rule": "#263247", "divider": "#2c313a", "success": "#5fd19a", "dark": "#0d0f12",
        "on-dark": "#a6adb8", "overlay": "#ffffff", "on-primary": "#0d0f12",
    })),
])

# Typography. The template pairs a serif for headings, navigation and the
# sidebar with a sans for text; the alternatives only rearrange those two, so
# every pairing costs nothing extra to download.
TYPE_SETS = collections.OrderedDict([
    ("type-1-serif-sans", ("Source Serif headings, Open Sans text", "source-serif", "open-sans")),
    ("type-2-serif", ("Source Serif throughout", "source-serif", "source-serif")),
    ("type-3-sans", ("Open Sans throughout", "open-sans", "open-sans")),
    ("type-4-sans-serif", ("Open Sans headings, Source Serif text", "open-sans", "source-serif")),
    ("type-5-system", ("System fonts", "system-serif", "system")),
])

LATIN = ("U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, "
         "U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD")
LATIN_EXT = ("U+0100-02BA, U+02BD-02C5, U+02C7-02CC, U+02CE-02D7, U+02DD-02FF, U+0304, U+0308, U+0329, "
             "U+1D00-1DBF, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C0, U+2113, "
             "U+2C60-2C7F, U+A720-A7FF")
SUBSETS = (("latin", LATIN), ("latin-ext", LATIN_EXT))


def _open_sans_faces():
    return [(("300 800"), style, "open-sans-%s-wght-%s.woff2" % (subset, style), rng)
            for style in ("normal", "italic") for subset, rng in SUBSETS]


def _serif_faces():
    return [(str(weight), "normal", "source-serif-pro-%s-%d-normal.woff2" % (subset, weight), rng)
            for weight in (400, 600, 700) for subset, rng in SUBSETS]


FAMILIES = collections.OrderedDict([
    ("source-serif", ("Source Serif Pro", "'Source Serif Pro', Georgia, 'Times New Roman', serif", _serif_faces())),
    ("open-sans", ("Open Sans", "'Open Sans', system-ui, -apple-system, 'Segoe UI', sans-serif", _open_sans_faces())),
    ("system-serif", ("System serif", "Charter, 'Bitstream Charter', 'Sitka Text', Cambria, Georgia, serif", [])),
    ("system", ("System sans", "system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif", [])),
])


def fluid(minimum, maximum):
    return collections.OrderedDict([("min", minimum), ("max", maximum)])


# The template's scale, in its own pixels: 11 (labels), 13 (bylines, navigation),
# 15 (text), 18 (category list), 20 (sidebar titles), 24 (card titles), 30
# (section and banner titles) and 36 (h1), plus the 27 of the page-title band.
FONT_SIZES = [
    ("X Small",  "x-small",  "0.6875rem", None),
    ("Small",    "small",    "0.8125rem", None),
    ("Medium",   "medium",   "0.9375rem", None),
    ("Large",    "large",    "1.125rem",  None),
    ("X Large",  "x-large",  "1.25rem",   fluid("1.125rem", "1.25rem")),
    # The template keeps its card titles at 24px and its banner titles at 30px
    # on a phone, so these barely shrink: a quieter phone layout was one of the
    # differences a side-by-side render showed at once.
    ("XX Large", "xx-large", "1.5rem",    None),
    ("Title",    "title",    "1.6875rem", fluid("1.5rem", "1.6875rem")),
    ("Heading",  "heading",  "1.875rem",  fluid("1.625rem", "1.875rem")),
    ("Display",  "display",  "2.25rem",   fluid("1.875rem", "2.25rem")),
]

# The template's section padding is 100px, 80 on a laptop and 70 on a phone;
# the feature row opens at 140. `70` and `80` are those, fluid.
SPACING = [
    ("20", "0.5rem"),
    ("30", "1rem"),
    ("40", "1.875rem"),
    ("50", "clamp(2rem, 4vw, 3rem)"),
    ("60", "clamp(2.5rem, 5vw, 3.75rem)"),
    ("70", "clamp(4.375rem, 8vw, 6.25rem)"),
    ("80", "clamp(5rem, 10vw, 8.75rem)"),
]

# Every foreground/background pair the design actually puts together.
CONTRAST_CHECKS = [
    ("contrast", "base"), ("contrast", "surface"), ("contrast", "subtle"),
    ("muted", "base"), ("muted", "surface"), ("muted", "subtle"),
    # Category labels, links and the page banner's small print.
    ("primary", "base"), ("primary", "surface"), ("primary", "subtle"),
    # A hovered title or link.
    ("primary-deep", "base"),
    # The footer: headings and links in `overlay`, text in `on-dark`.
    ("overlay", "dark"), ("on-dark", "dark"),
    # Every button label, resting and hovered.
    ("on-primary", "primary"), ("on-primary", "primary-deep"),
]

# The footer's newsletter button sits on `dark`, where `primary` is chosen for
# the wrong ground: Ink's near-black button measured 1.06:1 there, Orchid's
# 2.57:1. It is filled with `primary-lifted` — the brand colour made for a dark
# ground — and its arrow is cut out in `dark`, which that colour has to carry.
CONTRAST_CHECKS.append(("dark", "primary-lifted"))

# Pairs that only have to be SEEN, not read (WCAG 1.4.11): a button's fill
# against the page it sits on.
BOUNDARY_CHECKS = [("primary", "base")]


# ---------------------------------------------------------------------------
# Contrast
# ---------------------------------------------------------------------------
def _channel(value):
    value = value / 255
    return value / 12.92 if value <= 0.04045 else ((value + 0.055) / 1.055) ** 2.4


def luminance(hex_colour):
    r, g, b = (int(hex_colour[i:i + 2], 16) for i in (1, 3, 5))
    return 0.2126 * _channel(r) + 0.7152 * _channel(g) + 0.0722 * _channel(b)


def contrast_ratio(a, b):
    la, lb = luminance(a), luminance(b)
    lighter, darker = max(la, lb), min(la, lb)
    return (lighter + 0.05) / (darker + 0.05)


def button_text(colors):
    """The best label colour for a button, chosen by measurement."""
    best, best_ratio = None, 0
    for slug in ("overlay", "base", "contrast", "dark"):
        ratio = min(contrast_ratio(colors[slug], colors["primary"]),
                    contrast_ratio(colors[slug], colors["primary-deep"]))
        if ratio > best_ratio:
            best, best_ratio = slug, ratio
    return best if best_ratio >= 4.5 else None


# ---------------------------------------------------------------------------
# theme.json
# ---------------------------------------------------------------------------
def od(*pairs):
    return collections.OrderedDict(pairs)


def var(slug):
    return "var(--wp--preset--color--%s)" % slug


def fs(slug):
    valid = {s for _, s, _, _ in FONT_SIZES}
    if slug not in valid:
        raise SystemExit("font size %r is not on the scale %s" % (slug, sorted(valid)))
    return "var(--wp--preset--font-size--%s)" % slug


def ff(slug):
    return "var(--wp--preset--font-family--%s)" % slug


def sp(slug):
    valid = {s for s, _ in SPACING}
    if slug not in valid:
        raise SystemExit("spacing %r is not on the scale %s" % (slug, sorted(valid)))
    return "var(--wp--preset--spacing--%s)" % slug


def palette(colors):
    missing = [slug for _, slug in PALETTE if slug not in colors]
    if missing:
        raise SystemExit("palette is missing %s" % ", ".join(missing))
    return [od(("name", name), ("slug", slug), ("color", colors[slug])) for name, slug in PALETTE]


def font_families():
    out = []
    for key, (name, stack, faces) in FAMILIES.items():
        entry = od(("name", name), ("slug", key), ("fontFamily", stack))
        if faces:
            entry["fontFace"] = [od(
                ("fontFamily", name), ("fontStyle", style), ("fontWeight", weight),
                ("fontDisplay", "swap"),
                ("src", ["file:./assets/fonts/%s" % filename]),
                ("unicodeRange", rng),
            ) for weight, style, filename, rng in faces]
        out.append(entry)
    return out


def build_settings():
    return od(
        ("appearanceTools", True),
        ("useRootPaddingAwareAlignments", True),
        # The template is a Bootstrap 4 layout: a 1110px container, with a 730px
        # column beside a 350px sidebar. The home page's opening banner is the
        # one thing that runs the full width.
        ("layout", od(("contentSize", "1110px"), ("wideSize", "1320px"))),
        ("color", od(("custom", True), ("defaultPalette", False), ("defaultGradients", False),
                     ("defaultDuotone", False),
                     ("palette", palette(COLOR_SETS["colors-1-scarlet"][1])))),
        ("typography", od(
            # Fluid sizes reach their maximum at the template's 1200px container
            # breakpoint, not WordPress's default 1600px, so a laptop sees the
            # sizes the design was drawn at.
            ("fluid", od(("minViewportWidth", "390px"), ("maxViewportWidth", "1200px"))),
            ("customFontSize", True), ("defaultFontSizes", False),
            ("fontFamilies", font_families()),
            # A size with no `fluid` key is NOT fixed: with fluid type on,
            # WordPress derives a clamp() for it, and the 24px card titles came
            # out at 15.7px on a phone. Fixed sizes say so with `false`.
            ("fontSizes", [od(("name", name), ("slug", slug), ("size", size), ("fluid", f if f else False))
                           for name, slug, size, f in FONT_SIZES]),
        )),
        ("spacing", od(("units", ["px", "em", "rem", "vh", "vw", "%"]),
                       ("padding", True), ("margin", True), ("blockGap", True),
                       ("defaultSpacingSizes", False),
                       ("spacingSizes", [od(("name", name), ("slug", name), ("size", size))
                                         for name, size in SPACING]))),
        ("border", od(("color", True), ("radius", True), ("style", True), ("width", True))),
        ("shadow", od(("defaultPresets", False), ("presets", [
            od(("name", "Soft"), ("slug", "soft"), ("shadow", "0 10px 20px rgba(42, 34, 123, 0.08)")),
            od(("name", "Sticky"), ("slug", "sticky"), ("shadow", "0 10px 15px rgba(0, 0, 0, 0.05)")),
        ]))),
    )


def build_styles():
    return od(
        ("color", od(("background", var("base")), ("text", var("muted")))),
        ("typography", od(("fontFamily", ff("open-sans")), ("fontSize", fs("medium")),
                          ("fontWeight", "400"), ("lineHeight", "1.93"))),
        # Root padding needs units: WordPress's navigation overlay feeds it to a
        # clamp(), where a bare 0 invalidates the whole declaration.
        ("spacing", od(("blockGap", "1.5rem"),
                       ("padding", od(("top", "0px"), ("bottom", "0px"),
                                      ("left", sp("30")), ("right", sp("30")))))),
        ("elements", od(
            ("heading", od(("typography", od(("fontFamily", ff("source-serif")), ("fontWeight", "600"),
                                             ("lineHeight", "1.333"))),
                           ("color", od(("text", var("contrast")))))),
            ("h1", od(("typography", od(("fontSize", fs("display")), ("lineHeight", "1.222"))))),
            ("h2", od(("typography", od(("fontSize", fs("heading")), ("lineHeight", "1.222"))))),
            ("h3", od(("typography", od(("fontSize", fs("xx-large")))))),
            ("h4", od(("typography", od(("fontSize", fs("x-large")))))),
            ("h5", od(("typography", od(("fontSize", fs("large")))))),
            ("h6", od(("typography", od(("fontSize", fs("medium")))))),
            ("link", od(("color", od(("text", var("primary")))),
                        ("typography", od(("textDecoration", "none"))),
                        (":hover", od(("color", od(("text", var("primary-deep")))))))),
            ("button", od(
                # Labelled `on-primary`, not `overlay`: dark mode lifts `primary`
                # to a light red, and `on-primary` turns over with it while
                # `overlay` deliberately never changes.
                ("color", od(("background", var("primary")), ("text", var("on-primary")))),
                ("typography", od(("fontFamily", ff("open-sans")), ("fontWeight", "400"),
                                  ("fontSize", "0.875rem"), ("lineHeight", "1.5"))),
                ("border", od(("radius", "0px"), ("width", "0px"), ("style", "none"))),
                ("spacing", od(("padding", od(("top", "0.8rem"), ("bottom", "0.8rem"),
                                              ("left", "1.375rem"), ("right", "1.375rem"))))),
                (":hover", od(("color", od(("background", var("primary-deep")), ("text", var("on-primary")))))),
                (":focus", od(("color", od(("background", var("primary-deep")), ("text", var("on-primary")))))),
            )),
            ("caption", od(("typography", od(("fontSize", fs("small")))),
                           ("color", od(("text", var("muted")))))),
        )),
        ("blocks", od(
            ("core/separator", od(("color", od(("text", var("divider")))))),
            # The wordmark: the template's logo is Source Serif at 700, tight.
            ("core/site-title", od(("typography", od(("fontFamily", ff("source-serif")), ("fontWeight", "700"),
                                                     ("fontSize", fs("display")), ("lineHeight", "1"),
                                                     ("letterSpacing", "-0.01em"))),
                                   ("color", od(("text", var("contrast")))),
                                   ("elements", od(("link", od(("color", od(("text", var("contrast")))),
                                                               (":hover", od(("color", od(("text", var("contrast")))))))))))),
            ("core/navigation", od(("typography", od(("fontFamily", ff("source-serif")), ("fontSize", fs("small")),
                                                     ("fontWeight", "600"), ("textTransform", "uppercase"))),
                                   ("color", od(("text", var("contrast")))))),
            # A linked title takes the link colour unless told otherwise.
            ("core/post-title", od(("elements", od(("link", od(("color", od(("text", var("contrast")))),
                                                              (":hover", od(("color", od(("text", var("primary")))))))))))),
            ("core/quote", od(("typography", od(("fontStyle", "italic"), ("lineHeight", "1.733"))))),
            ("core/pullquote", od(("typography", od(("fontFamily", ff("source-serif")))))),
            ("core/post-date", od(("typography", od(("fontSize", fs("small")), ("fontWeight", "600"))))),
            ("core/post-author-name", od(("typography", od(("fontSize", fs("small")), ("fontWeight", "800"))),
                                         ("color", od(("text", var("contrast")))),
                                         ("elements", od(("link", od(("color", od(("text", var("contrast")))))))))),
            ("core/categories", od(("typography", od(("fontFamily", ff("source-serif")), ("fontSize", fs("large")))))),
            ("core/code", od(("typography", od(("fontSize", fs("small")))),
                             ("color", od(("background", var("subtle")), ("text", var("contrast")))))),
        )),
    )


def build_theme():
    return od(
        ("$schema", "https://schemas.wp.org/trunk/theme.json"),
        ("version", 3),
        ("settings", build_settings()),
        ("styles", build_styles()),
        ("customTemplates", [
            od(("name", "page-no-title"), ("title", "Page without title"), ("postTypes", ["page"])),
            od(("name", "page-with-sidebar"), ("title", "Page with sidebar"), ("postTypes", ["page"])),
            od(("name", "single-no-sidebar"), ("title", "Post without sidebar"), ("postTypes", ["post"])),
        ]),
        ("templateParts", [
            od(("name", "header"), ("title", "Header"), ("area", "header")),
            od(("name", "footer"), ("title", "Footer"), ("area", "footer")),
            od(("name", "sidebar"), ("title", "Sidebar"), ("area", "uncategorized")),
        ]),
    )


def build_color_variation(slug, name, colors):
    return od(
        ("$schema", "https://schemas.wp.org/trunk/theme.json"),
        ("version", 3), ("title", name),
        ("settings", od(("color", od(("palette", palette(colors)))))),
    )


def build_type_variation(slug, name, heading, body):
    """Typography only. The Site Editor lists a variation under Typography only
    if it carries nothing else, so the element and block styles that name a
    family are all restated here and nothing more."""
    return od(
        ("$schema", "https://schemas.wp.org/trunk/theme.json"),
        ("version", 3), ("title", name),
        ("styles", od(
            ("typography", od(("fontFamily", ff(body)))),
            ("elements", od(
                ("heading", od(("typography", od(("fontFamily", ff(heading)))))),
                ("button", od(("typography", od(("fontFamily", ff(body)))))),
            )),
            ("blocks", od(
                ("core/site-title", od(("typography", od(("fontFamily", ff(heading)))))),
                ("core/navigation", od(("typography", od(("fontFamily", ff(heading)))))),
                ("core/categories", od(("typography", od(("fontFamily", ff(heading)))))),
                ("core/pullquote", od(("typography", od(("fontFamily", ff(heading)))))),
            )),
        )),
    )


def write(path, data):
    os.makedirs(os.path.dirname(path) or ".", exist_ok=True)
    with open(path, "w", encoding="utf-8") as handle:
        json.dump(data, handle, indent="\t", ensure_ascii=False)
        handle.write("\n")
    return path


def _mix_with_white(hex_colour, percent):
    """CSS `color-mix(in srgb, <colour> <percent>%, white)`, per channel."""
    channels = [int(hex_colour[i:i + 2], 16) for i in (1, 3, 5)]
    share = percent / 100
    return "#" + "".join("%02x" % round(c * share + 255 * (1 - share)) for c in channels)


def _dark_scheme():
    """What assets/css/scheme.css does to the palette, read from the file itself.

    Read rather than restated, so the numbers cannot drift from the CSS. It also
    refuses any colour slug the palette does not define: a color-mix() on an
    undefined variable makes the declaration invalid, `primary` stops resolving,
    and every button renders as bare text while every text check still passes.
    """
    css = open("assets/css/scheme.css", encoding="utf-8").read()
    defined = {slug for _, slug in PALETTE}
    referenced = set(re.findall(r"var\(--wp--preset--color--([a-z0-9-]+)\)", css))
    unknown = sorted(referenced - defined)
    if unknown:
        raise SystemExit("scheme.css reads colour slugs the palette does not define: %s"
                         % ", ".join(unknown))

    def value(slug):
        m = re.search(r"--wp--preset--color--%s:\s*color-mix\(in srgb,\s*"
                      r"var\(--wp--preset--color--([a-z0-9-]+)\)\s*(\d+)%%,\s*white\)"
                      % re.escape(slug), css)
        if m:
            return ("mix", m.group(1), int(m.group(2)))
        m = re.search(r"--wp--preset--color--%s:\s*(#[0-9a-fA-F]{6})\s*;" % re.escape(slug), css)
        if m:
            return ("fixed", m.group(1).lower())
        m = re.search(r"--wp--preset--color--%s:\s*var\(--wp--preset--color--([a-z0-9-]+)\)\s*;" % re.escape(slug), css)
        if m:
            return ("slug", m.group(1))
        raise SystemExit("scheme.css: cannot read the dark-mode `%s`" % slug)

    return {slug: value(slug) for slug in (
        "base", "surface", "subtle", "contrast", "muted", "primary", "primary-deep", "on-primary")}


def _resolve(spec, colors):
    if spec[0] == "fixed":
        return spec[1]
    if spec[0] == "slug":
        return colors[spec[1]]
    return _mix_with_white(colors[spec[1]], spec[2])


def audit():
    problems = []
    global DARK_FOOTER
    css = open("assets/css/scheme.css", encoding="utf-8").read()
    m = re.search(r"--wp--preset--color--dark:\s*(#[0-9a-fA-F]{6})", css)
    if not m:
        raise SystemExit("scheme.css: cannot read the dark-mode `dark`")
    DARK_FOOTER = m.group(1)

    print("  light       label        resting  hover   footer button")
    # (the footer button's figure is its arrow on its fill: `dark` on `primary-lifted`)
    for slug, (name, colors) in COLOR_SETS.items():
        for fg, bg in CONTRAST_CHECKS:
            ratio = contrast_ratio(colors[fg], colors[bg])
            if ratio < 4.5:
                problems.append("%s: %s on %s is %.2f" % (name, fg, bg, ratio))
        for fg, bg in BOUNDARY_CHECKS:
            ratio = contrast_ratio(colors[fg], colors[bg])
            if ratio < 3.0:
                problems.append("%s: %s against %s is %.2f, needs 3.0" % (name, fg, bg, ratio))
        resting = contrast_ratio(colors["on-primary"], colors["primary"])
        hover = contrast_ratio(colors["on-primary"], colors["primary-deep"])
        best = button_text(colors)
        if best is not None:
            best_worst = min(contrast_ratio(colors[best], colors[g]) for g in ("primary", "primary-deep"))
            if min(resting, hover) + 0.005 < best_worst:
                problems.append("%s: on-primary (%.2f) is a worse label than %s (%.2f)"
                                % (name, min(resting, hover), best, best_worst))
        print("  %-11s %-12s %6.2f  %6.2f  %6.2f" % (name, "on-primary", resting, hover,
                                                     contrast_ratio(colors["dark"], colors["primary-lifted"])))

    dark = _dark_scheme()
    print("\n  dark mode, as scheme.css applies it")
    print("  dark        text/base  text/surf  link/base  link/surf  label  label-hover  boundary")
    for slug, (name, colors) in COLOR_SETS.items():
        d = {k: _resolve(v, colors) for k, v in dark.items()}
        measured = (
            ("primary text on base", contrast_ratio(d["primary"], d["base"]), 4.5),
            ("primary text on surface", contrast_ratio(d["primary"], d["surface"]), 4.5),
            ("primary text on subtle", contrast_ratio(d["primary"], d["subtle"]), 4.5),
            ("hovered link on base", contrast_ratio(d["primary-deep"], d["base"]), 4.5),
            ("button label on its fill", contrast_ratio(d["on-primary"], d["primary"]), 4.5),
            ("button label on the hover fill", contrast_ratio(d["on-primary"], d["primary-deep"]), 4.5),
            # WCAG 1.4.11: a button has to be visible as a button, not merely
            # carry a readable label.
            ("footer button arrow on its fill", contrast_ratio(DARK_FOOTER, colors["primary-lifted"]), 4.5),
            ("button fill against the page",
             min(contrast_ratio(d["primary"], d["base"]), contrast_ratio(d["primary"], d["surface"])), 3.0),
        )
        for label, value, need in measured:
            if value < need:
                problems.append("%s (dark): %s is %.2f, needs %.1f" % (name, label, value, need))
        print("  %-11s %9.2f  %9.2f  %9.2f  %9.2f  %5.2f  %11.2f  %8.2f" % (
            name, contrast_ratio(d["contrast"], d["base"]), contrast_ratio(d["contrast"], d["surface"]),
            measured[0][1], measured[1][1], measured[4][1], measured[5][1], measured[6][1]))
        for fg, bg in (("contrast", "base"), ("contrast", "surface"), ("contrast", "subtle"),
                       ("muted", "base"), ("muted", "surface"), ("muted", "subtle")):
            ratio = contrast_ratio(d[fg], d[bg])
            if ratio < 4.5:
                problems.append("%s (dark): %s on %s is %.2f" % (name, fg, bg, ratio))

    print("\n  for the record: the template's #ef1313 is %.2f:1 on white and %.2f:1 on its #fff1f1 banner,"
          % (contrast_ratio("#ef1313", "#ffffff"), contrast_ratio("#ef1313", "#fff1f1")))
    print("  and its #8a8a8a bylines are %.2f:1. Both are kept for decoration only."
          % contrast_ratio("#8a8a8a", "#ffffff"))
    if problems:
        raise SystemExit("\nContrast failures:\n  " + "\n  ".join(problems))


def main():
    audit()
    written = [write("theme.json", build_theme())]
    for slug, (name, colors) in COLOR_SETS.items():
        written.append(write("styles/colors/%s.json" % slug, build_color_variation(slug, name, colors)))
    for slug, (name, heading, body) in TYPE_SETS.items():
        written.append(write("styles/typography/%s.json" % slug, build_type_variation(slug, name, heading, body)))
    print("\n  %d files written" % len(written))
    for path in written:
        print("    " + path)


if __name__ == "__main__":
    main()
