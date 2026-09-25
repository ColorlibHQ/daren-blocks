# colorlib.com product page

The page at https://colorlib.com/wp/themes/daren/ is rendered from `page-spec.json`
(the words, the screenshots by file name, the features, questions and details)
by `render-page.php`, the renderer all four 2026 block themes share (Daren,
Creative Agency, Dreamrs, Horseclub), so their pages keep one layout: the same
spacing, backgrounds that alternate by section, unique WPBakery css= classes
per row, and the "Two versions" cards.

Edit the spec, not the page. Screenshots live in `images/` (section shots
1140×792, alt text in `images/alt.json`); a replaced screenshot keeps its
file name, so after importing it delete the old attachment first and remove
its stale `.jpg.avif` sidecars, then purge its URLs at Cloudflare.

Run on hetzner as the PHP user, from the WordPress root:

    sudo -u web_colorlib_com env CLT_SPEC=/path/page-spec.json \
      wp --url=https://colorlib.com/wp/ eval "require '/path/render-page.php';"

It updates the existing page in place (status, title, thumbnail and SEO meta
are kept), refuses to save while an image is missing, and prints checks. Then
purge the FastCGI key for the page and `cf-purge-url` it.
