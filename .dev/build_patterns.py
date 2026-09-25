#!/usr/bin/env python3
"""Generate Daren's patterns.

Run from the theme root:

    python3 .dev/build_patterns.py
    node .dev/normalize-blocks.mjs      # then let the editor re-serialise them
    node .dev/validate-blocks.mjs       # and refuse anything it calls invalid

Every pattern file is committed as generated. Edit this file, never
patterns/*.php.

The design is the HTML template's: a magazine home page that is almost all
posts. Its sections are query blocks, so they fill themselves from whatever the
site has published; the copy around them (headings, the About page, the
footer) is written for the journal the theme was designed around — Daren
Ellis, an illustrator in Lisbon, writing about colour.
"""

import json
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

from patternlib import (  # noqa: E402
    attrs, button, buttons, column, columns, group, heading, image,
    paragraph, separator, shortcode, sp, spacer,
)

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
PATTERNS = os.path.join(ROOT, "patterns")
WRITTEN = []

POSTS = ["daren-posts"]
SECTIONS = ["daren-sections"]
PAGES = ["daren-pages"]

# Query IDs only have to be unique within a page; giving every query its own
# keeps them unique across every page the patterns can be combined into.
QUERY_IDS = iter(range(11, 99))


def write(slug, title, content, categories=None, keywords=None,
          description=None, inserter=True, block_types=None):
    header = ["Title: " + title, "Slug: daren/" + slug]
    if categories:
        header.append("Categories: " + ", ".join(categories))
    if keywords:
        header.append("Keywords: " + ", ".join(keywords))
    if block_types:
        header.append("Block Types: " + ", ".join(block_types))
    if description:
        header.append("Description: " + description)
    if not inserter:
        header.append("Inserter: no")

    body = (
        "<?php\n/**\n * " + "\n * ".join(header) + "\n *\n * @package Daren\n */\n\n"
        "defined( 'ABSPATH' ) || exit;\n?>\n" + content.strip() + "\n"
    )
    with open(os.path.join(PATTERNS, slug + ".php"), "w") as fh:
        fh.write(body)
    WRITTEN.append(slug)


def icon_class(name):
    """The class that draws a Tabler icon, carried by the block itself.

    style.css draws it as a mask filled with the text colour, so it follows the
    palette and dark mode, and it shows in the editor because the class is on
    the block rather than on an empty span inside it. Changing an icon is an
    edit to Advanced → Additional CSS class(es). Every name used here needs a
    `.daren-icon--<name>` rule; .dev/dead-selectors.py checks it."""
    return "daren-icon--%s" % name


def raw_group(inner, data, cls=""):
    """A group whose attributes patternlib's group() has no parameter for.

    normalize-blocks.mjs rewrites it into exactly what core's save() produces.
    """
    class_attr = "wp-block-group" + (" " + cls if cls else "")
    return '<!-- wp:group%s -->\n<div class="%s">\n%s\n</div>\n<!-- /wp:group -->' % (
        attrs(data), class_attr, inner)


def flex_row(inner, justify=None, gap=None, wrap="wrap", vertical="center", extra_class=None):
    layout = {"type": "flex", "flexWrap": wrap}
    if justify:
        layout["justifyContent"] = justify
    if vertical:
        layout["verticalAlignment"] = vertical
    data = {}
    if extra_class:
        data["className"] = extra_class
    if gap:
        data["style"] = {"spacing": {"blockGap": gap if gap.endswith("px") else sp(gap)}}
    data["layout"] = layout
    return raw_group(inner, data, extra_class or "")


def box(inner, extra_class, gap=None, layout="default"):
    """A plain wrapper that exists to be styled: `layout: default`, so nothing
    constrains its children, and one class that style.css knows."""
    data = {"className": extra_class}
    if gap:
        data["style"] = {"spacing": {"blockGap": gap if gap.endswith("px") else sp(gap)}}
    data["layout"] = {"type": layout}
    return raw_group(inner, data, extra_class)


def block(name, data=None):
    """A self-closing dynamic block."""
    return "<!-- wp:%s%s /-->" % (name, attrs(data or {}))


# ---------------------------------------------------------------------------
# The pieces every story card is built from.
# ---------------------------------------------------------------------------
def label():
    """The red capitals above a title — the post's first category."""
    return block("post-terms", {"term": "category", "className": "daren-label"})


def tab():
    """The white tab a category sits in, over the corner of a photograph."""
    return block("post-terms", {"term": "category", "className": "daren-tab"})


def title(level=3, size="xx-large"):
    return block("post-title", {"level": level, "isLink": True, "fontSize": size})


def byline():
    """"By Daren Ellis / 18 September 2026", as the template sets it."""
    return flex_row("\n".join([
        block("post-author", {"showAvatar": False, "byline": "By", "className": "daren-byline__author"}),
        block("post-date", {"className": "daren-byline__date"}),
    ]), gap="20", extra_class="daren-byline")


def actions():
    """Comments, reading time and share — see inc/blocks.php for why these are
    read-more blocks."""
    return flex_row("\n".join([
        block("read-more", {"content": "Comments", "className": "daren-action--comments"}),
        block("read-more", {"content": "min read", "className": "daren-action--time"}),
        block("read-more", {"content": "Share", "className": "daren-action--share"}),
    ]), gap="30", extra_class="daren-actions")


def featured(ratio, extra=None):
    data = {"isLink": True, "aspectRatio": ratio}
    if extra:
        data["className"] = extra
    return block("post-featured-image", data)


def query(inner, per_page, offset=0, extra_class=None, inherit=False, no_results=None, pagination=None,
          align=None):
    data = {
        "queryId": next(QUERY_IDS),
        "query": {"perPage": per_page, "pages": 0, "offset": offset, "postType": "post",
                  "order": "desc", "orderBy": "date", "author": "", "search": "", "exclude": [],
                  "sticky": "", "inherit": inherit},
    }
    if align:
        data["align"] = align
    if extra_class:
        data["className"] = extra_class
    data["layout"] = {"type": "default"}
    parts = ['<!-- wp:post-template {"layout":{"type":"default"}} -->', inner, "<!-- /wp:post-template -->"]
    if no_results:
        parts += ["<!-- wp:query-no-results -->", no_results, "<!-- /wp:query-no-results -->"]
    if pagination:
        parts.append(pagination)
    cls = "wp-block-query" + (" align" + align if align else "") + (" " + extra_class if extra_class else "")
    return '<!-- wp:query%s -->\n<div class="%s">%s</div>\n<!-- /wp:query -->' % (
        attrs(data), cls, "\n".join(parts))


def numbered_pagination():
    return ('<!-- wp:query-pagination {"paginationArrow":"chevron","className":"daren-pagination",'
            '"layout":{"type":"flex","justifyContent":"center"}} -->\n'
            '<!-- wp:query-pagination-previous {"label":"Newer"} /-->\n'
            '<!-- wp:query-pagination-numbers /-->\n'
            '<!-- wp:query-pagination-next {"label":"Older"} /-->\n'
            '<!-- /wp:query-pagination -->')


def more_button(label_text="More stories"):
    """The template's red "LOADING MORE ›" button, as the next page of the list."""
    return ('<!-- wp:query-pagination {"paginationArrow":"none","className":"daren-more",'
            '"layout":{"type":"flex","justifyContent":"center"}} -->\n'
            '<!-- wp:query-pagination-next {"label":"%s"} /-->\n'
            '<!-- /wp:query-pagination -->' % label_text)


def no_posts(text="Nothing has been published here yet."):
    return paragraph(text, color="muted")


def rule():
    """The template's 5px pink line between sections, at the container width."""
    return group(separator("daren-band"), align="full", layout="constrained", extra_class="daren-rule")


def section_title(text, align="left"):
    """A section title with the template's red flag beneath it."""
    return heading(text, level=2, align=align, size="xx-large", style="daren-flag")


# ---------------------------------------------------------------------------
# Story cards
# ---------------------------------------------------------------------------
def banner_card():
    """One of the two opening stories: the photograph, and a white card laid
    across it. style.css sizes the two differently — a narrow portrait beside a
    wide landscape, as the template does."""
    card = box("\n".join([label(), title(2, "heading"), byline()]), "daren-banner__card")
    return box("\n".join([featured("auto", "daren-banner__image"), card]), "daren-banner__item")


def checker_card():
    """Photograph and words, stacked; style.css flips the middle one so the
    row reads as a checkerboard."""
    body = box("\n".join([label(), title(), byline(), actions()]), "daren-checker__body")
    return box("\n".join([featured("380/310", "daren-checker__image"), body]), "daren-checker__item")


def card():
    """A photograph with its category in a tab, then the byline, title and actions."""
    media = box("\n".join([featured("360/336"), tab()]), "daren-card__media")
    body = box("\n".join([byline(), title(), actions()]), "daren-card__body")
    return box(media + "\n" + body, "daren-card")


def list_card():
    """The long list: photograph on the left with its category turned on its
    side, the words in a bordered box beside it."""
    media = box("\n".join([featured("350/340"), tab()]), "daren-row__media")
    body = box("\n".join([byline(), title(), actions()]), "daren-row__body")
    return box(media + "\n" + body, "daren-row")


def mini_card():
    """A sidebar story: a letterbox photograph, the byline and a short title."""
    return box("\n".join([featured("360/172"), byline(), title(3, "x-large")]), "daren-mini")


# ---------------------------------------------------------------------------
# Parts
# ---------------------------------------------------------------------------
def social_links(color="contrast", value="#2a2a2a", extra=None):
    data = {"iconColor": color, "iconColorValue": value, "size": "has-small-icon-size",
            "className": "is-style-logos-only" + (" " + extra if extra else ""),
            "layout": {"type": "flex", "flexWrap": "nowrap"}}
    return (
        '<!-- wp:social-links %s -->\n'
        '<ul class="wp-block-social-links has-small-icon-size has-icon-color is-style-logos-only%s">'
        '<!-- wp:social-link {"url":"#","service":"facebook"} /-->'
        '<!-- wp:social-link {"url":"#","service":"x"} /-->'
        '<!-- wp:social-link {"url":"#","service":"instagram"} /-->'
        '<!-- wp:social-link {"url":"#","service":"pinterest"} /-->'
        '</ul>\n'
        '<!-- /wp:social-links -->' % (json.dumps(data, separators=(",", ":")), (" " + extra if extra else ""))
    )


def build_header():
    # The switch needs text inside it: an empty core/button renders nothing at
    # all. The label is for screen readers; inc/scheme.php adds the pressed state.
    toggle = buttons([button('<span class="screen-reader-text">Switch between light and dark mode</span>', "#",
                             extra_class="daren-scheme-toggle")])
    search = block("search", {"label": "Search", "showLabel": False, "placeholder": "Search here",
                              "buttonText": "Search", "buttonPosition": "button-only", "buttonUseIcon": True,
                              "className": "daren-header__search"})
    tools = flex_row("\n".join([search, social_links(extra="daren-header__social"), toggle]),
                     justify="right", gap="20", wrap="nowrap", extra_class="daren-header__tools")
    brand = flex_row(block("site-logo", {"width": 120}) + "\n" + block("site-title", {"level": 0}),
                     gap="30", wrap="nowrap", extra_class="daren-header__brand")
    nav = block("navigation", {"overlayMenu": "mobile", "className": "daren-nav",
                               "layout": {"type": "flex", "justifyContent": "center", "flexWrap": "nowrap"}})
    row = flex_row("\n".join([brand, nav, tools]), justify="space-between", gap="40", wrap="nowrap",
                   extra_class="daren-header__row")
    write("header", "Header",
          group(row, align="full", background="base", layout="constrained", extra_class="daren-header"),
          keywords=["header", "navigation"],
          description="The wordmark, the navigation, and search, social links and the dark mode switch.",
          block_types=["core/template-part/header"])


def footer_heading(text):
    return heading(text, level=2, color="overlay", size="xx-large", extra_class="daren-footer__title")


def footer_detail(icon, title_text, text):
    inner = "\n".join([
        heading(title_text, level=3, color="overlay", size="medium", extra_class="daren-footer__detail-title"),
        paragraph(text, color="on-dark", size="small"),
    ])
    return box(inner, "daren-detail " + icon_class(icon), gap="0px")


def build_footer():
    about = column("\n".join([
        footer_heading("About Me"),
        paragraph("I'm Daren Ellis, an illustrator and art director in Lisbon. This journal is where I write "
                  "about colour: where it comes from, how it is made, and what it does to the people who "
                  "look at it. A new story most Fridays.", color="on-dark", size="small"),
    ]))
    contact = column("\n".join([
        footer_heading("Contact us"),
        footer_detail("home", "Lisbon, Portugal", "Studio 4, Rua da Rosa 142, Bairro Alto, 1200-389 Lisboa"),
        footer_detail("headphones", "+351 912 480 316", "Monday to Friday, 10:00 to 18:00"),
    ]))
    newsletter = column("\n".join([
        footer_heading("Newsletter"),
        paragraph("One email a month: the new stories, a palette I have been using and one thing worth "
                  "looking at. No noise, and you can leave with one click.", color="on-dark", size="small"),
        shortcode("[daren_newsletter_form]"),
    ]))
    body = columns([about, contact, newsletter], gap="40", extra_class="daren-footer__columns")
    # The site name loses a trailing full stop so that a wordmark written
    # "DarEn." does not read "DarEn.. All rights reserved".
    legal = paragraph('Copyright &copy;<?php echo esc_html( gmdate( \'Y\' ) ); ?> '
                      '<?php echo esc_html( rtrim( get_bloginfo( \'name\' ), \'.\' ) ); ?>. All rights reserved '
                      '<span class="daren-footer__sep" aria-hidden="true">|</span> Theme made with '
                      '<span class="daren-heart" aria-hidden="true">&#9825;</span> by '
                      '<a href="https://colorlib.com/" rel="nofollow">Colorlib</a>',
                      align="center", color="on-dark", size="small", extra_class="daren-footer__legal")
    write("footer", "Footer",
          group(body + "\n" + legal, align="full", background="dark", text="on-dark", layout="constrained",
                extra_class="daren-footer"),
          keywords=["footer", "newsletter"],
          description="About, contact details and a newsletter sign-up on the dark ground, with the copyright line beneath.",
          block_types=["core/template-part/footer"])


def sidebar_widgets(stories_title, offset=0):
    return "\n".join([
        section_title("Search"),
        block("search", {"label": "Search", "showLabel": False, "placeholder": "Search keyword",
                         "buttonText": "Search", "buttonUseIcon": True, "className": "daren-sidebar__search"}),
        section_title(stories_title),
        query(mini_card(), 3, offset, extra_class="daren-minis"),
        section_title("Categories"),
        block("categories", {"showPostCounts": True, "className": "daren-categories"}),
        section_title("Popular tags"),
        block("tag-cloud", {"numberOfTags": 12, "smallestFontSize": "13px", "largestFontSize": "13px",
                            "className": "daren-tags"}),
    ])


def build_sidebar():
    write("sidebar", "Sidebar",
          box(sidebar_widgets("Latest stories"), "daren-sidebar"),
          keywords=["sidebar"], inserter=False,
          description="Search, the latest stories, categories and tags.")


# ---------------------------------------------------------------------------
# Story layouts — the home page, one section at a time.
# ---------------------------------------------------------------------------
def build_banner():
    inner = query(banner_card(), 2, 0, extra_class="daren-banner__query", align="full")
    write("stories-banner", "Stories: opening banner",
          group(inner, align="full", layout="default", extra_class="daren-banner"),
          categories=POSTS, keywords=["hero", "banner", "featured", "latest"],
          description="The two newest stories side by side, full width: a tall photograph and a wide one, each with its title on a white card.")


def build_checker():
    inner = query(checker_card(), 3, 2, extra_class="daren-checker")
    write("stories-checkerboard", "Stories: checkerboard",
          group(inner, align="full", layout="constrained", extra_class="daren-checker-section"),
          categories=POSTS, keywords=["featured", "grid", "posts"],
          description="Three stories in a row, photograph and words alternating like a checkerboard.")


def build_cards():
    inner = query(card(), 3, 5, extra_class="daren-cards")
    write("stories-cards", "Stories: three cards",
          group(inner, align="full", layout="constrained", extra_class="daren-cards-section"),
          categories=POSTS, keywords=["cards", "grid", "posts", "category"],
          description="Three stories as cards, each photograph carrying its category on a tab.")


def build_list_sidebar():
    stories = query(list_card(), 5, 8, extra_class="daren-list",
                    no_results=no_posts(), pagination=more_button())
    inner = columns([
        column(stories, width="67.5%"),
        column(box(sidebar_widgets("From the archive", offset=13), "daren-sidebar"), width="32.5%"),
    ], gap="40", extra_class="daren-with-sidebar")
    write("stories-list-sidebar", "Stories: list with sidebar",
          group(inner, align="full", padding_y="70", layout="constrained", extra_class="daren-list-section"),
          categories=POSTS, keywords=["list", "posts", "sidebar", "archive"],
          description="Five stories in a list beside a sidebar with search, older stories, categories and tags.")


def build_latest_cards():
    head = section_title("From the journal")
    inner = head + "\n" + query(card(), 3, 0, extra_class="daren-cards")
    write("stories-latest", "Stories: latest three",
          group(inner, align="full", padding_y="70", layout="constrained", extra_class="daren-cards-section"),
          categories=POSTS, keywords=["latest", "posts", "cards", "blog"],
          description="A titled row of the three newest stories, as cards.")


# ---------------------------------------------------------------------------
# Sections
# ---------------------------------------------------------------------------
def build_about_intro():
    photo = image("studio-set", "A straw hat and sunglasses on an orange string chair against a pink wall, "
                  "with paper cocktail umbrellas hanging above it", ratio="750/809")
    signature = flex_row("\n".join([
        image("portrait", "", ratio="1/1", rounded="50%", width="72px"),
        paragraph("<strong>Daren Ellis</strong><br>Illustrator and art director, Lisbon", size="small"),
    ]), gap="30", wrap="nowrap", extra_class="daren-signature")
    text = "\n".join([
        paragraph("Hello", size="x-small", extra_class="daren-kicker"),
        heading("I'm Daren. This is a journal about colour.", level=2, size="heading"),
        paragraph("For fifteen years I have been paid to choose colours for other people: for book covers, "
                  "for packaging, for a pool-float company and a tea that tastes of liquorice. This is where "
                  "I write about the ones I choose for myself."),
        paragraph("Most weeks that means a set I have built in the studio, a wall I have walked past too many "
                  "times to ignore, or a paint I cannot stop using. Every picture here was made or found for "
                  "the story it sits in."),
        signature,
        buttons([button("Read the journal", "<?php echo esc_url( home_url( '/blog/' ) ); ?>", style="daren-arrow"),
                 button("Write to me", "<?php echo esc_url( home_url( '/contact/' ) ); ?>", style="daren-outline")]),
    ])
    inner = columns([
        column(photo, width="45%"),
        column(group(text, layout="constrained", gap="40"), width="55%", vertical="center"),
    ], gap="70", vertical="center", extra_class="daren-about")
    write("about-intro", "About: introduction",
          group(inner, align="full", padding_y="70", layout="constrained"),
          categories=SECTIONS, keywords=["about", "author", "introduction"],
          description="A photograph beside a short introduction, a signature and two buttons.")


def topic(slug, alt, kicker, title_text, text):
    return column(group("\n".join([
        image(slug, alt, ratio="360/172"),
        paragraph(kicker, size="x-small", extra_class="daren-kicker"),
        heading(title_text, level=3, size="xx-large"),
        paragraph(text),
    ]), layout="constrained", gap="30"))


def build_about_topics():
    head = group("\n".join([
        section_title("What I write about", align="center"),
        paragraph("Three things I cannot stop looking at, and the categories you will find them under.",
                  align="center"),
    ]), layout="constrained", content_size="620px", gap="40")
    cols = columns([
        topic("topic-colour", "A silver spoon heaped with rainbow sprinkles against a magenta background",
              "Still Life", "Colour you can hold",
              "Sets built in the studio from whatever is the right colour: eggs, bottles, sprinkles, a chair."),
        topic("topic-walls", "Detail of a mural of a bear's face built from triangles of flat orange, blue, green and pink",
              "Street Art", "Walls and the people who paint them",
              "Murals in Lisbon and wherever else I happen to be, looked at closely and slowly."),
        topic("topic-still-life", "A striped succulent in a speckled stone pot against a bright blue wall",
              "Illustration", "How a picture is made",
              "Paint, brushes, briefs and the decisions nobody sees in the finished thing."),
    ], gap="40")
    write("about-topics", "About: three topics",
          group(head + "\n" + spacer("50") + "\n" + cols, align="full", padding_y="70", layout="constrained"),
          categories=SECTIONS, keywords=["about", "topics", "categories", "features"],
          description="A centred title and three columns, each a photograph, a category, a title and a line of copy.")


def build_newsletter():
    inner = columns([
        column(group("\n".join([
            heading("A letter about colour, once a month", level=2, size="heading"),
            paragraph("The new stories, a palette I have been using and one thing worth looking at. "
                      "No noise, and you can leave with one click."),
        ]), layout="constrained", gap="30"), width="55%", vertical="center"),
        column(shortcode("[daren_newsletter_form]"), width="45%", vertical="center"),
    ], gap="60", vertical="center")
    write("newsletter", "Newsletter sign-up",
          group(inner, align="full", background="surface", padding_y="70", layout="constrained",
                extra_class="daren-newsletter"),
          categories=SECTIONS, keywords=["newsletter", "subscribe", "email"],
          description="A tinted band with a heading, a line of copy and the email sign-up.")


def contact_detail(icon, title_text, text):
    inner = "\n".join([
        heading(title_text, level=3, size="medium", extra_class="daren-contact-info__title"),
        paragraph(text),
    ])
    return box(inner, "daren-contact-info " + icon_class(icon), gap="0px")


def build_contact():
    map_block = (
        '<!-- wp:html -->\n'
        '<iframe class="daren-map" title="Map of Bairro Alto, Lisbon, where the studio is" loading="lazy" '
        'src="https://www.openstreetmap.org/export/embed.html?bbox=-9.1500%2C38.7095%2C-9.1405%2C38.7160&amp;layer=mapnik&amp;marker=38.71275%2C-9.14530" '
        'style="width:100%;height:480px;border:0"></iframe>\n'
        '<!-- /wp:html -->'
    )
    form = group("\n".join([
        heading("Get in Touch", level=2, size="title"),
        shortcode("[daren_contact_form]"),
    ]), layout="constrained", gap="30")
    details = group("\n".join([
        contact_detail("home", "Bairro Alto, Lisbon", "Studio 4, Rua da Rosa 142, 1200-389 Lisboa"),
        contact_detail("device-mobile", "+351 912 480 316", "Monday to Friday, 10:00 to 18:00"),
        contact_detail("mail", "hello@yourdomain.com", "Commissions, questions and good walls to look at."),
    ]), layout="constrained", gap="40", extra_class="daren-contact-details")
    inner = columns([column(form, width="67.5%"), column(details, width="32.5%")], gap="40",
                    extra_class="daren-with-sidebar")
    write("contact", "Contact: map, form and details",
          group(map_block + "\n" + spacer("60") + "\n" + inner, align="full", padding_y="70",
                layout="constrained", anchor="contact"),
          categories=SECTIONS, keywords=["contact", "form", "map", "address"],
          description="A map across the top, then the contact form beside the address, phone and email.")


# ---------------------------------------------------------------------------
# Hidden patterns: the pieces templates are built from.
# ---------------------------------------------------------------------------
def band(inner):
    """The pink band a page, archive or post title sits in."""
    return group(inner, align="full", background="surface", layout="constrained", extra_class="daren-page-banner")


def build_hidden():
    write("hidden-page-banner", "Page title band",
          band(block("post-title", {"level": 1, "textAlign": "center", "fontSize": "title"})),
          inserter=False, description="The tinted band a page's title sits in.")

    write("hidden-post-banner", "Post category band",
          band(block("post-terms", {"term": "category", "textAlign": "center", "className": "daren-banner-terms"})),
          inserter=False, description="The tinted band above a post, carrying its category.")

    write("hidden-archive-banner", "Archive title band",
          band(block("query-title", {"type": "archive", "textAlign": "center", "showPrefix": False,
                                     "fontSize": "title"})),
          inserter=False, description="The tinted band an archive's title sits in.")

    write("hidden-search-banner", "Search title band",
          band(block("query-title", {"type": "search", "textAlign": "center", "fontSize": "title"})),
          inserter=False, description="The tinted band search results sit under.")

    write("hidden-blog-heading", "Journal title band",
          band(heading("Journal", level=1, align="center", size="title")),
          inserter=False, description="The title band for the posts page.")

    grid = query(card(), 6, 0, extra_class="daren-cards daren-cards--two", inherit=True,
                 no_results=no_posts(), pagination=numbered_pagination())
    write("hidden-posts-grid", "Posts grid with sidebar",
          group(columns([column(grid, width="67.5%"),
                         column('<!-- wp:template-part {"slug":"sidebar","tagName":"aside"} /-->', width="32.5%")],
                        gap="40", extra_class="daren-with-sidebar"),
                align="full", padding_y="70", layout="constrained"),
          inserter=False, description="The journal and most archives: cards two to a row, beside the sidebar.")

    lst = query(list_card(), 6, 0, extra_class="daren-list", inherit=True,
                no_results=no_posts("Nothing matched. Try another word, or browse the categories."),
                pagination=numbered_pagination())
    write("hidden-posts-list", "Posts list with sidebar",
          group(columns([column(lst, width="67.5%"),
                         column('<!-- wp:template-part {"slug":"sidebar","tagName":"aside"} /-->', width="32.5%")],
                        gap="40", extra_class="daren-with-sidebar"),
                align="full", padding_y="70", layout="constrained"),
          inserter=False, description="Category archives and search results: a list beside the sidebar.")

    meta = flex_row("\n".join([
        block("post-author", {"showAvatar": False, "byline": "By", "className": "daren-byline__author"}),
        block("post-date", {"className": "daren-byline__date"}),
        block("read-more", {"content": "Comments", "className": "daren-action--comments"}),
        block("read-more", {"content": "min read", "className": "daren-action--time"}),
    ]), gap="30", extra_class="daren-byline daren-post-meta")
    write("hidden-post-meta", "Post meta", meta, inserter=False,
          description="Author, date, comments and reading time for a single post.")

    footer_row = flex_row("\n".join([
        block("post-terms", {"term": "post_tag", "className": "daren-post-tags"}),
        block("read-more", {"content": "Share", "className": "daren-action--share"}),
    ]), justify="space-between", gap="30", extra_class="daren-post-footer")
    nav = flex_row("\n".join([
        block("post-navigation-link", {"type": "previous", "showTitle": True, "label": "Previous story",
                                       "arrow": "chevron", "className": "daren-post-nav__link"}),
        block("post-navigation-link", {"showTitle": True, "label": "Next story", "arrow": "chevron",
                                       "className": "daren-post-nav__link"}),
    ]), justify="space-between", gap="40", wrap="nowrap", extra_class="daren-post-nav")
    author = group(block("post-author", {"avatarSize": 96, "showBio": True, "isLink": True,
                                         "className": "daren-author-box__author"}),
                   layout="constrained", style="daren-panel", extra_class="daren-author-box")
    write("hidden-post-footer", "Post footer", "\n".join([footer_row, nav, author]), inserter=False,
          description="Tags and a share link, the previous and next stories, and the author.")

    comments = (
        '<!-- wp:comments {"className":"daren-comments"} -->\n'
        '<div class="wp-block-comments daren-comments">\n'
        '<!-- wp:comments-title {"showPostTitle":false,"level":2,"fontSize":"large"} /-->\n'
        '<!-- wp:comment-template -->\n'
        + flex_row("\n".join([
            block("avatar", {"size": 70, "style": {"border": {"radius": "50%"}}}),
            box("\n".join([
                block("comment-content"),
                flex_row("\n".join([
                    block("comment-author-name", {"fontSize": "medium"}),
                    block("comment-date", {"fontSize": "small"}),
                    block("comment-reply-link", {"fontSize": "small"}),
                ]), gap="30", extra_class="daren-comment__meta"),
            ]), "daren-comment__body"),
        ]), gap="30", wrap="nowrap", vertical="top", extra_class="daren-comment") + '\n'
        '<!-- /wp:comment-template -->\n'
        '<!-- wp:comments-pagination {"layout":{"type":"flex","justifyContent":"center"}} -->\n'
        '<!-- wp:comments-pagination-previous /-->\n'
        '<!-- wp:comments-pagination-numbers /-->\n'
        '<!-- wp:comments-pagination-next /-->\n'
        '<!-- /wp:comments-pagination -->\n'
        '<!-- wp:post-comments-form /-->\n'
        '</div>\n'
        '<!-- /wp:comments -->'
    )
    write("hidden-comments", "Comments", comments, inserter=False,
          description="The comments and the reply form for a single post.")

    notfound = "\n".join([
        paragraph("Error 404", align="center", size="x-small", extra_class="daren-kicker"),
        heading("This page has wandered off", level=2, align="center", size="heading"),
        paragraph("The address may be old, or the story may have been renamed. Try a search, "
                  "or start again from the journal.", align="center"),
        block("search", {"label": "Search", "showLabel": False, "placeholder": "Search keyword",
                         "buttonText": "Search", "buttonUseIcon": True, "className": "daren-sidebar__search"}),
        buttons([button("Back to the journal", "<?php echo esc_url( home_url( '/' ) ); ?>", style="daren-arrow")],
                align="center"),
    ])
    write("hidden-404", "404 content",
          group(notfound, align="full", padding_y="70", layout="constrained", content_size="620px", gap="40"),
          inserter=False, description="What a visitor sees when nothing is there.")


# ---------------------------------------------------------------------------
# Whole pages
# ---------------------------------------------------------------------------
def ref(slug):
    return '<!-- wp:pattern {"slug":"daren/%s"} /-->' % slug


def build_pages():
    home = "\n".join([ref("stories-banner"), ref("stories-checkerboard"), rule(), ref("stories-cards"), rule(),
                      ref("stories-list-sidebar")])
    write("page-home", "Page: home", home, categories=PAGES,
          description="The magazine home page: the opening banner, the checkerboard, three cards and the list with its sidebar.")
    about = "\n".join([ref("about-intro"), rule(), ref("about-topics"), ref("newsletter"), ref("stories-latest")])
    write("page-about", "Page: about", about, categories=PAGES,
          description="An introduction, three topics, the newsletter sign-up and the latest stories.")
    write("page-contact", "Page: contact", ref("contact"), categories=PAGES,
          description="The map, the contact form and the studio's details.")


def main():
    os.makedirs(PATTERNS, exist_ok=True)
    for name in os.listdir(PATTERNS):
        if name.endswith(".php"):
            os.remove(os.path.join(PATTERNS, name))
    build_header()
    build_footer()
    build_sidebar()
    build_banner()
    build_checker()
    build_cards()
    build_list_sidebar()
    build_latest_cards()
    build_about_intro()
    build_about_topics()
    build_newsletter()
    build_contact()
    build_hidden()
    build_pages()

    for slug in sorted(WRITTEN):
        print("  patterns/%s.php" % slug)
    print("\n%d patterns" % len(WRITTEN))


if __name__ == "__main__":
    main()
