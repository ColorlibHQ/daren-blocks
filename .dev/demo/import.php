<?php
/**
 * Demo content: the journal the theme was designed around.
 *
 * NOT part of the theme. `.dev/` is excluded from the zip (build-zip.sh), and so
 * are these photographs: a theme ships licence-clean and lean, and demo content
 * belongs in a site's media library. This builds the whole demo — site title,
 * author, categories, seventeen posts with featured images, three comments —
 * in a Playground (.dev/blueprint.json) and on the colorlibhub demo site.
 *
 * On a server, with Daren already active on the site (its activation builds
 * the Home, Journal, About and Contact pages and the menu), copy this folder
 * somewhere readable by the PHP user and run, as that user:
 *
 *     wp --url=https://colorlibhub.com/daren-blocks/ eval "require '<dir>/import.php';"
 *
 * `wp eval` and `wp eval-file` both run the file inside a function, so its
 * top-level variables are NOT globals. Nothing here uses `global`: helpers get
 * what they need as arguments, or from __DIR__.
 *
 * Idempotent: run it twice and the second run changes nothing. Posts are
 * matched by slug with get_posts( name + post_type ) — never get_page_by_path(),
 * which also matches attachments — and rewritten from this file, so a re-run
 * also applies corrections. An image already in the library is reused; one
 * whose file has changed here is replaced. Comments are added once.
 *
 * Photographs. The template's own, re-cut from the originals with the same framing,
 * unless marked as a replacement:
 *   summer-set      Lionel Gustave       https://unsplash.com/photos/c1rOy44wuts
 *   dyed-eggs       Laurentiu Iordache   https://unsplash.com/photos/AiXeylszBPA
 *   beetle          Alan Emery           https://unsplash.com/photos/emTCWiq2txk
 *   bottles-cacti   Mae Mu               https://unsplash.com/photos/xtRL02ZuZxE
 *   feather-basket  Ronald Cuyan         https://unsplash.com/photos/AJgFLjnmSs4
 *   teapot-pour     Filip Mroz           https://unsplash.com/photos/5Vnd6GXbNvA
 *   monster-wall    Brandon Jackson      https://unsplash.com/photos/jDpsYElNaxo
 *   painted-hand    Lenny Miles          https://unsplash.com/photos/q3Si_spMPHo
 *   spray-cans      Tim Mossholder       https://unsplash.com/photos/XHZa1W7_F4w
 *   inflatables     Valentina Conde      https://unsplash.com/photos/jQIdL5OKj9o
 *   sprinkles       Sharon McCutcheon    https://unsplash.com/photos/F-uHUndbXhY
 *   succulent       Rémi Müller          https://unsplash.com/photos/hYAmjIvLALE
 *   neon-night      Maurício Mascaro     https://www.pexels.com/photo/948198/        (Pexels)
 *   eye-mural       Eduardo Romero       https://www.pexels.com/photo/1707640/       (Pexels)
 *   rainbow-arcs    Engin Akyurt         https://www.pexels.com/photo/6973330/       (Pexels)
 *   triangles-mural Toa Heftiba Şinca    https://www.pexels.com/photo/1194420/       (Pexels)
 *   red-brush       Juris Freidenfelds   https://www.pexels.com/photo/2013663/       (Pexels)
 *
 * Every one was traced by reverse image search, not taken on trust. The last
 * four replace template photographs that could not ship: three whose Unsplash
 * pages had been taken down, and a paintbrush that is also sold on
 * Shutterstock (#352312724).
 *
 * @package Daren
 */

defined( 'ABSPATH' ) || exit;

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

// The theme builds its pages on activation. If that has not happened yet on
// this site, let the theme do it — the importer never creates those pages.
if ( function_exists( 'daren_create_front_page' ) && ! get_option( 'daren_front_page_created' ) ) {
	daren_create_front_page();
}

/* ---------------------------------------------------------------------------
 * The site.
 * ------------------------------------------------------------------------ */
update_option( 'blogname', 'DarEn.' );
update_option( 'blogdescription', 'A journal of colour, craft and visual culture' );

// A new multisite subsite starts with plain or dated permalinks; the demo's
// links read better by name. An existing structure is left alone.
if ( ! get_option( 'permalink_structure' ) ) {
	update_option( 'permalink_structure', '/%postname%/' );
	flush_rewrite_rules( false );
}

/* ---------------------------------------------------------------------------
 * The author: a user of their own. Renaming user 1 instead would rename the
 * network's super admin on every site of a multisite.
 * ------------------------------------------------------------------------ */
$daren_author_user = get_user_by( 'login', 'daren-ellis' );
if ( $daren_author_user ) {
	$daren_author = (int) $daren_author_user->ID;
} else {
	$daren_author = wp_insert_user(
		wp_slash(
			array(
				'user_login' => 'daren-ellis',
				'user_pass'  => wp_generate_password( 32 ),
				'user_email' => 'daren-ellis@example.com',
				'role'       => 'author',
			)
		)
	);
	if ( is_wp_error( $daren_author ) ) {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::error( 'Could not create the demo author: ' . $daren_author->get_error_message() );
		}
		return;
	}
}
if ( is_multisite() && ! is_user_member_of_blog( $daren_author, get_current_blog_id() ) ) {
	add_user_to_blog( get_current_blog_id(), $daren_author, 'author' );
}
wp_update_user(
	wp_slash(
		array(
			'ID'           => $daren_author,
			'display_name' => 'Daren Ellis',
			'first_name'   => 'Daren',
			'last_name'    => 'Ellis',
			'nickname'     => 'Daren',
			'user_url'     => '',
			'description'  => 'Illustrator and art director in Lisbon. I write about colour: where it comes from, how it is made, and what it does to the people who look at it.',
		)
	)
);

/* ---------------------------------------------------------------------------
 * WordPress's own sample content goes.
 * ------------------------------------------------------------------------ */
foreach ( array( 'hello-world' => 'post', 'sample-page' => 'page' ) as $daren_slug => $daren_type ) {
	foreach ( get_posts( array( 'name' => $daren_slug, 'post_type' => $daren_type, 'post_status' => 'any', 'numberposts' => 1 ) ) as $daren_old ) {
		wp_delete_post( $daren_old->ID, true );
	}
}

/* ---------------------------------------------------------------------------
 * Categories.
 * ------------------------------------------------------------------------ */
$daren_categories = array(
	'creative-design' => array( 'Creative Design', 'Sets, props and the decisions behind a picture.' ),
	'illustration'    => array( 'Illustration', 'Drawing and painting, and what they borrow from everything else.' ),
	'street-art'      => array( 'Street Art', 'Walls, the people who paint them and the cities they belong to.' ),
	'still-life'      => array( 'Still Life', 'Small things arranged on a coloured ground.' ),
	'brand-identity'  => array( 'Brand Identity', 'Colour and type at work for somebody else.' ),
	'travel'          => array( 'Travel', 'Notebooks from somewhere else.' ),
);
$daren_cat_ids = array();
foreach ( $daren_categories as $daren_slug => $daren_cat ) {
	$daren_term = get_term_by( 'slug', $daren_slug, 'category' );
	if ( $daren_term ) {
		wp_update_term( $daren_term->term_id, 'category', wp_slash( array( 'name' => $daren_cat[0], 'description' => $daren_cat[1] ) ) );
		$daren_cat_ids[ $daren_slug ] = (int) $daren_term->term_id;
		continue;
	}
	$daren_term = wp_insert_term( $daren_cat[0], 'category', wp_slash( array( 'slug' => $daren_slug, 'description' => $daren_cat[1] ) ) );
	$daren_cat_ids[ $daren_slug ] = is_wp_error( $daren_term ) ? (int) get_option( 'default_category' ) : (int) $daren_term['term_id'];
}

// Nothing is uncategorised on this site.
wp_update_term( (int) get_option( 'default_category' ), 'category', array( 'name' => 'Notes', 'slug' => 'notes' ) );

/* ---------------------------------------------------------------------------
 * Helpers. Guarded, so a second require in the same process cannot fatal.
 * ------------------------------------------------------------------------ */
if ( ! function_exists( 'daren_demo_p' ) ) {
	function daren_demo_p( $text ) {
		return "<!-- wp:paragraph -->\n<p>" . $text . "</p>\n<!-- /wp:paragraph -->\n\n";
	}

	function daren_demo_h( $text ) {
		return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">" . $text . "</h2>\n<!-- /wp:heading -->\n\n";
	}

	function daren_demo_quote( $text ) {
		return "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><!-- wp:paragraph -->\n<p>" . $text . "</p>\n<!-- /wp:paragraph --></blockquote>\n<!-- /wp:quote -->\n\n";
	}

	function daren_demo_list( $items ) {
		$out = "<!-- wp:list -->\n<ul class=\"wp-block-list\">";
		foreach ( $items as $item ) {
			$out .= "<!-- wp:list-item -->\n<li>" . $item . "</li>\n<!-- /wp:list-item -->";
		}
		return $out . "</ul>\n<!-- /wp:list -->\n\n";
	}

	/**
	 * The attachment for a demo photograph, sideloaded from the file beside
	 * this script. Reused when the same file is already in the library;
	 * replaced when the file here has changed since it was imported.
	 *
	 * @param string $dir     Folder holding the photographs.
	 * @param string $file    File name.
	 * @param string $alt     Alt text, written from the picture.
	 * @param int    $post_id Post to attach it to.
	 * @return int Attachment ID, or 0.
	 */
	function daren_demo_image( $dir, $file, $alt, $post_id ) {
		$path = $dir . $file;
		if ( ! is_readable( $path ) ) {
			return 0;
		}
		$md5 = md5_file( $path );

		$existing = get_posts(
			array(
				'post_type'   => 'attachment',
				'post_status' => 'inherit',
				'meta_key'    => '_daren_demo_file', // phpcs:ignore WordPress.DB.SlowDBQuery
				'meta_value'  => $file, // phpcs:ignore WordPress.DB.SlowDBQuery
				'numberposts' => -1,
				'fields'      => 'ids',
			)
		);
		$stale = array();
		foreach ( $existing as $old ) {
			if ( get_post_meta( $old, '_daren_demo_md5', true ) === $md5 ) {
				update_post_meta( $old, '_wp_attachment_image_alt', wp_slash( $alt ) );
				return (int) $old;
			}
			$stale[] = (int) $old;
		}

		$tmp = wp_tempnam( $file );
		copy( $path, $tmp );
		$id = media_handle_sideload( array( 'name' => $file, 'tmp_name' => $tmp ), $post_id, $alt );
		if ( is_wp_error( $id ) ) {
			wp_delete_file( $tmp );
			return 0;
		}
		update_post_meta( $id, '_wp_attachment_image_alt', wp_slash( $alt ) );
		update_post_meta( $id, '_daren_demo_file', $file );
		update_post_meta( $id, '_daren_demo_md5', $md5 );

		// The superseded copy of this photograph, and its generated sizes.
		foreach ( $stale as $old ) {
			wp_delete_attachment( $old, true );
		}
		return (int) $id;
	}
}

/* ---------------------------------------------------------------------------
 * The posts, newest first. The home page reads them in this order: two in the
 * opening banner, three in the checkerboard, three category cards, five in the
 * list and three in the sidebar's "From the archive".
 * ------------------------------------------------------------------------ */
$daren_posts = array(
	array(
		'slug'  => 'a-summer-set-built-from-cocktail-umbrellas',
		'title' => 'A summer set, built from cocktail umbrellas',
		'cat'   => 'still-life',
		'tags'  => array( 'props', 'photography', 'pink' ),
		'date'  => '2026-09-18 09:10:00',
		'image' => array( 'summer-set.jpg', 'A straw hat and sunglasses on an orange string chair against a pink wall, with paper cocktail umbrellas hanging above it, a coconut, palm leaves, a teal towel and a canvas tote' ),
		'excerpt' => 'Forty paper umbrellas, a string chair and a wall painted the pink of a strawberry milkshake. How a summer cover came together in an afternoon.',
		'body'  => daren_demo_p( 'The brief was one line long: <em>make it feel like the first hot day</em>. No beach, no sea, no people. So the set became a pink wall, an orange string chair and forty paper cocktail umbrellas on fishing line, each one hung at a slightly different height so they read as falling rather than floating.' )
			. daren_demo_p( 'The pink came first. I tried three before this one: a salmon that looked tired under the lights, a magenta that fought the chair, and this, which is closer to the inside of a watermelon than anything on a paint chart. The chair is the only strong orange in the picture, and everything else — the teal towel, the tote bag, the palm leaves — is there to make the orange look hotter.' )
			. daren_demo_quote( 'A set is finished when you can take nothing else away and it still says summer.' )
			. daren_demo_p( 'The hat and the sunglasses were the last thing in and very nearly the first thing out. Without them the chair is furniture. With them, somebody has just got up to fetch a drink.' ),
	),
	array(
		'slug'  => 'what-a-dozen-dyed-eggs-taught-me-about-restraint',
		'title' => 'What a dozen dyed eggs taught me about restraint',
		'cat'   => 'creative-design',
		'tags'  => array( 'colour', 'process' ),
		'date'  => '2026-09-11 08:30:00',
		'image' => array( 'dyed-eggs.jpg', 'Speckled eggs dyed purple, yellow, blue and pink on a bright green paper background, with broken white eggshells scattered between them' ),
		'excerpt' => 'Twelve eggs, four dye baths and a green so loud it should not work. It works because every egg keeps its white.',
		'body'  => daren_demo_p( 'Every spring I dye a dozen eggs, and every spring I relearn the same lesson: the colour that matters is the one you leave out. These were dipped once, briefly, with a sponge dabbed on first so the dye could not reach everywhere. The white left behind is what makes the purple look purple.' )
			. daren_demo_h( 'Four baths, no more' )
			. daren_demo_p( 'Purple, yellow, blue and a pink that comes from beetroot. Anything more and the eggs start to look like a bag of sweets. Four colours on a ground this green is already a lot of noise; the broken shells are there to give the eye somewhere white to rest.' )
			. daren_demo_list( array( 'A sponge, torn rather than cut, so the pattern never repeats.', 'Ten seconds in the bath for the pale ones, a minute for the deep ones.', 'Dry on a rack, not a towel, or every egg gets a flat side.' ) )
			. daren_demo_p( 'The green paper was an accident. It was the only large sheet left in the studio, and I nearly went out for a neutral one. I am glad I did not.' ),
	),
	array(
		'slug'  => 'the-beetle-that-wears-every-colour-at-once',
		'title' => 'The beetle that wears every colour at once',
		'cat'   => 'illustration',
		'tags'  => array( 'colour', 'nature' ),
		'date'  => '2026-09-04 10:00:00',
		'image' => array( 'beetle.jpg', 'Close-up of an iridescent beetle on a green leaf, its shell shifting from gold and orange to deep green and blue' ),
		'excerpt' => 'No pigment in that shell is green, gold or blue. How structural colour works, and why it is so hard to paint.',
		'body'  => daren_demo_p( 'There is no green pigment in this beetle. Its shell is built from thin layers that reflect some wavelengths and cancel others, so the colour changes as the light moves — gold at the shoulders, green down the back, a blue that only appears at the edges.' )
			. daren_demo_p( 'I spent a week trying to paint one in gouache and failed every time, because paint gives you one colour per place and the beetle gives you three. What finally worked was to stop blending: flat patches of gold, green and blue laid side by side, the way a mosaic does it, and let the eye do the mixing.' )
			. daren_demo_p( 'It is the same trick the Impressionists used on water, and it is humbling to find a beetle doing it better.' ),
	),
	array(
		'slug'  => 'three-bottles-five-cacti-and-a-sky-made-of-cotton',
		'title' => 'Three bottles, five cacti and a sky made of cotton',
		'cat'   => 'still-life',
		'tags'  => array( 'props', 'photography', 'blue' ),
		'date'  => '2026-08-28 09:00:00',
		'image' => array( 'bottles-cacti.jpg', 'Three white milk bottles with red caps standing on a blue surface among tiny potted cacti, with cotton-wool clouds hanging from threads above them' ),
		'excerpt' => 'A dairy wanted its bottles to look like a landscape. Cotton wool, clay cacti and one very specific blue.',
		'body'  => daren_demo_p( 'The dairy wanted its bottles to look like a place rather than a product. Three bottles became three buildings, the cacti became a desert town, and the sky is cotton wool pulled apart by hand and hung on thread so the shadows fall on the wall behind.' )
			. daren_demo_p( 'The blue is the whole picture. It had to be warm enough not to make the milk look cold, and deep enough that the red caps read from across a supermarket aisle. We painted the board four times.' )
			. daren_demo_p( 'The cacti are modelling clay, each about the size of a thumbnail. Real ones looked like real ones, which was the wrong kind of real.' ),
	),
	array(
		'slug'  => 'carrying-colour-a-portrait-on-a-yellow-wall',
		'title' => 'Carrying colour: a portrait on a yellow wall',
		'cat'   => 'creative-design',
		'tags'  => array( 'photography', 'yellow' ),
		'date'  => '2026-08-21 11:15:00',
		'image' => array( 'feather-basket.jpg', 'A person holding a woven basket of pink, yellow, purple and teal feathers on their shoulder against a bright yellow wall, a straw hat in their other hand' ),
		'excerpt' => 'A basket of dyed feathers, a yellow wall and a sitter who would rather not show his face. Sometimes the prop is the portrait.',
		'body'  => daren_demo_p( 'He did not want his face in the picture, so the basket became his face. Dyed feathers — pink, yellow, violet and a teal that almost disappears against the sky — piled high enough to hide him from the shoulders up, and a straw hat held low so the frame still has a person in it.' )
			. daren_demo_p( 'Yellow is the hardest wall to shoot against. It bounces onto everything, and skin turns jaundiced in seconds. With no skin in view, that stopped being a problem and became the point: every colour in the basket is sharper for being on yellow.' ),
	),
	array(
		'slug'  => 'cadmium-red-and-why-i-keep-coming-back-to-it',
		'title' => 'Cadmium red, and why I keep coming back to it',
		'cat'   => 'illustration',
		'tags'  => array( 'paint', 'colour', 'red' ),
		'date'  => '2026-08-14 09:40:00',
		'image' => array( 'red-brush.jpg', 'A fine paintbrush drawing a stroke of red through a wash of orange and yellow paint on white paper' ),
		'excerpt' => 'It is toxic, expensive and slow to dry, and nothing else does what it does. A love letter to one tube of paint.',
		'body'  => daren_demo_p( 'Every few years I try to give up cadmium red. It is expensive, it is toxic if you are careless with it, and there are perfectly good modern reds that cost half as much. Every few years I come back, because none of them sit on the canvas the way it does: opaque, warm, and somehow heavier than the colours around it.' )
			. daren_demo_quote( 'A red that looks like it has weight. That is what you are paying for.' )
			. daren_demo_p( 'Pulled through orange and yellow, as here, it glows at the edges where they meet. Over a cool colour it goes almost brown. It is the least forgiving paint I own and the one I would keep if I could keep only one.' ),
	),
	array(
		'slug'  => 'reading-a-mural-one-eye-at-a-time',
		'title' => 'Reading a mural one eye at a time',
		'cat'   => 'street-art',
		'tags'  => array( 'murals', 'travel' ),
		'date'  => '2026-08-07 10:30:00',
		'image' => array( 'eye-mural.jpg', 'Detail of a mural on a brick wall: a woman\'s eye with long dark lashes, painted in shades of orange and brown' ),
		'excerpt' => 'Twelve metres of wall, and the painter spent a day on one eye. What a close-up tells you that the whole mural cannot.',
		'body'  => daren_demo_p( 'From across the street this mural is a face. From the pavement it is a set of decisions: an eye drawn in three browns, lashes laid on like single brushstrokes, and the brick left to show through the orange so that the wall itself becomes the skin.' )
			. daren_demo_p( 'I asked the painter how long the eye took. A day, she said — the rest of the face took two. The eye is where people look first, so it is where the wall has to be right.' )
			. daren_demo_p( 'It is a good rule for anything with a focal point. Spend the time where the viewer will spend theirs.' ),
	),
	array(
		'slug'  => 'pouring-colour-a-brief-for-a-tea-brand',
		'title' => 'Pouring colour: a brief for a tea brand',
		'cat'   => 'brand-identity',
		'tags'  => array( 'branding', 'blue', 'red' ),
		'date'  => '2026-07-31 09:00:00',
		'image' => array( 'teapot-pour.jpg', 'A teal teapot pouring a twisted red liquorice rope into a white cup against a pale blue background' ),
		'excerpt' => 'A fruit tea that does not taste of tea. The identity had to say so without a word: a red pour into a white cup.',
		'body'  => daren_demo_p( 'The tea tastes of cherries and liquorice and almost nothing of tea, and the client wanted the packaging to warn people kindly. We tried words. Words made it sound like a medicine. So the identity became one picture: a red rope of liquorice pouring out of the pot where the tea should be.' )
			. daren_demo_p( 'Three colours carry the whole brand — the teal of the pot, the red of the pour and a white that is a little warm so it never looks clinical. The logotype is set in the red, and nothing else on the box is allowed to be.' ),
	),
	array(
		'slug'  => 'portraits-in-front-of-the-monster-wall',
		'title' => 'Portraits in front of the monster wall',
		'cat'   => 'street-art',
		'tags'  => array( 'murals', 'photography', 'lisbon' ),
		'date'  => '2026-07-24 17:45:00',
		'image' => array( 'monster-wall.jpg', 'A smiling young man in a red jacket and white hoodie sitting on a low step in front of a pink graffiti monster with green eyes and white teeth' ),
		'excerpt' => 'Everyone in the neighbourhood has had their picture taken in front of it. An afternoon photographing the people who stop.',
		'body'  => daren_demo_p( 'The monster is pink, three metres tall and grinning, and nearly everyone who walks past stops to take a picture with it. I spent an afternoon asking them if I could take one instead.' )
			. daren_demo_p( 'The best portraits came from people who had clearly done this before: they knew where the light fell, which step to sit on and how to tilt their head so the monster seemed to be looking over their shoulder. Dimitri, here, has been sitting on that step since he was nine.' ),
	),
	array(
		'slug'  => 'hands-as-a-palette',
		'title' => 'Hands as a palette',
		'cat'   => 'creative-design',
		'tags'  => array( 'paint', 'process' ),
		'date'  => '2026-07-17 10:00:00',
		'image' => array( 'painted-hand.jpg', 'An open hand covered in blue and orange paint held out in front of a blurred red, yellow and blue patterned fabric' ),
		'excerpt' => 'The fastest way to learn what two colours do together is to wear them. A workshop that started with our hands.',
		'body'  => daren_demo_p( 'The workshop began with a rule: no brushes for the first hour. Everyone chose two colours and painted them onto their own hands, then pressed them onto paper, onto fabric, onto each other.' )
			. daren_demo_p( 'Blue and orange looked electric on white paper and muddy on skin, where the warmth underneath pulls the blue towards grey. Against a busy patterned cloth the same hand suddenly looked calm. Nobody forgets a lesson like that, because nobody learns it from a chart.' ),
	),
	array(
		'slug'  => 'a-morning-in-the-spray-can-shop',
		'title' => 'A morning in the spray-can shop',
		'cat'   => 'street-art',
		'tags'  => array( 'murals', 'paint', 'lisbon' ),
		'date'  => '2026-07-10 09:20:00',
		'image' => array( 'spray-cans.jpg', 'Rows of spray paint cans with coloured caps — yellow, red, blue, pink and green — in front of a purple and white graffiti wall' ),
		'excerpt' => 'Three hundred colours on one wall of shelves, and the writers who can name every one. What I learned at the counter.',
		'body'  => daren_demo_p( 'The shop has three hundred colours on one wall, and the writers who come in can name them all by number. What they ask for, mostly, is not colour but pressure: a low-pressure can for outlines, high pressure to fill a wall fast.' )
			. daren_demo_p( 'I came away with six cans and a new respect for the people who work with them. A spray can gives you no second coat and no blending on the palette. Every colour is a decision you make in the air.' ),
	),
	array(
		'slug'  => 'rainbow-arcs-and-the-discipline-of-the-stripe',
		'title' => 'A rainbow arc and the discipline of the stripe',
		'cat'   => 'street-art',
		'tags'  => array( 'murals', 'colour' ),
		'date'  => '2026-07-03 11:00:00',
		'image' => array( 'rainbow-arcs.jpg', 'A rainbow painted in a wide arc across a white wooden wall, in bands of red, orange, yellow, green, blue and purple' ),
		'excerpt' => 'A rainbow is the easiest thing to paint badly. This one works because of its clean edges, and the planks underneath it.',
		'body'  => daren_demo_p( 'A rainbow is the easiest thing in the world to paint badly: seven colours in a row, and they turn to mud wherever they touch. Whoever painted this one on a white plank wall kept it to six bands and let each one dry before the next went on, so every edge is clean.' )
			. daren_demo_p( 'The planks do the rest. Their grooves run straight down through every band, and against those hard vertical lines the curve looks rounder than it is, and the yellow brighter than yellow paint has any right to look.' ),
	),
	array(
		'slug'  => 'inflatables-and-designing-for-joy',
		'title' => 'Inflatables, and designing for joy',
		'cat'   => 'brand-identity',
		'tags'  => array( 'branding', 'summer' ),
		'date'  => '2026-06-26 09:30:00',
		'image' => array( 'inflatables.jpg', 'A red inflatable watermelon slice with black seeds beside a yellow inflatable ring, floating on dark blue water' ),
		'excerpt' => 'Nobody buys a watermelon pool float for its engineering. A few notes on designing things whose only job is to delight.',
		'body'  => daren_demo_p( 'The client makes pool floats, and the first meeting was about everything a float has to do: stay up, stay together, pass a safety test. The second meeting was about the only thing a customer notices, which is whether it makes them smile.' )
			. daren_demo_p( 'Joy, it turns out, is mostly saturation and scale. A watermelon as big as a bed, red enough to look edible, with seeds you could lose a hand in. The identity we built for them uses the same rule: one colour at full strength, and never two.' ),
	),
	array(
		'slug'  => 'triangles-and-a-bear-made-of-nothing-else',
		'title' => 'Triangles, and a bear made of nothing else',
		'cat'   => 'travel',
		'tags'  => array( 'murals', 'travel' ),
		'date'  => '2026-06-19 16:00:00',
		'image' => array( 'triangles-mural.jpg', 'Detail of a mural of a bear\'s face built entirely from triangles of flat orange, blue, green, pink and yellow, one ear spotted with stars' ),
		'excerpt' => 'A bear on a London wall with not one curve in it. Sketchbook pages from an afternoon spent working out why it still looks soft.',
		'body'  => daren_demo_p( 'The bear is wider than a car and there is not a single curve in it. Every part of the face — the ears, the muzzle, the eyes that follow you down the street — is a triangle of flat colour, orange against blue, pink against green, and the eye draws the curves on its own.' )
			. daren_demo_p( 'I filled half a sketchbook on that London street trying to work out how it holds together. I think it is the blues: deep at the edges, pale towards the middle, so the face comes forward and the triangles stop reading as a pattern and start reading as fur.' ),
	),
	array(
		'slug'  => 'sprinkles-and-the-smallest-colour-chart-in-the-kitchen',
		'title' => 'Sprinkles, and the smallest colour chart in the kitchen',
		'cat'   => 'still-life',
		'tags'  => array( 'colour', 'props' ),
		'date'  => '2026-06-12 10:00:00',
		'image' => array( 'sprinkles.jpg', 'A silver spoon heaped with tiny rainbow sprinkles against a magenta background' ),
		'excerpt' => 'A spoonful of hundreds and thousands holds a complete palette. Photographing it took longer than it should have.',
		'body'  => daren_demo_p( 'Hundreds and thousands are a complete colour chart in a spoon: white, yellow, orange, red, pink, violet, blue and green, every one at full saturation. Against magenta the warm ones vanish and the cool ones jump forward, which is the entire lesson of simultaneous contrast in one mouthful.' )
			. daren_demo_p( 'They also roll. The spoon was glued down, the sprinkles were placed with tweezers, and the photograph took most of a morning.' ),
	),
	array(
		'slug'  => 'one-succulent-against-a-blue-wall',
		'title' => 'One succulent against a blue wall',
		'cat'   => 'still-life',
		'tags'  => array( 'photography', 'blue' ),
		'date'  => '2026-06-05 09:00:00',
		'image' => array( 'succulent.jpg', 'A striped zebra succulent in a speckled stone pot on a pale ledge against a bright blue wall' ),
		'excerpt' => 'Sometimes the whole picture is one object and one colour. Why the quietest photographs take the longest to set up.',
		'body'  => daren_demo_p( 'One plant, one pot, one wall. The simplest pictures take the longest, because there is nothing to hide behind: if the blue is slightly wrong, the whole photograph is wrong.' )
			. daren_demo_p( 'This blue took three tins of paint and a week of looking at it in different light. The succulent took ten minutes.' ),
	),
	array(
		'slug'  => 'neon-nights-shooting-under-red-light',
		'title' => 'Neon nights: shooting under red light',
		'cat'   => 'travel',
		'tags'  => array( 'photography', 'red', 'travel' ),
		'date'  => '2026-05-29 23:30:00',
		'image' => array( 'neon-night.jpg', 'Friends in glasses laughing under red club lights, one holding green and yellow glow sticks to his mouth, a PARE stop sign on the wall behind them' ),
		'excerpt' => 'Red light flattens faces and swallows detail. It is also the most flattering light at a party. Notes from a night in São Paulo.',
		'body'  => daren_demo_p( 'Red light is the enemy of a photograph. It flattens faces, swallows detail and turns every shadow into the same dark maroon. It is also, at two in the morning in a club in São Paulo, the only light there is.' )
			. daren_demo_p( 'The trick is to stop fighting it. Let the red fill the frame, find one other colour to carry the picture — here, the green and yellow of a pair of glow sticks — and let the faces be red. Nobody at a party wants to be lit like a passport photo anyway.' )
			. daren_demo_quote( 'Find the one colour that is not red, and build the picture around it.' )
			. daren_demo_h( 'What I carried' )
			. daren_demo_list( array( 'One camera, one fixed 35mm lens.', 'Nothing to change the light, and no flash.', 'A notebook for the names of everyone who agreed to be photographed.' ) )
			. daren_demo_p( 'The stop sign behind them says <em>pare</em> — stop — which nobody in the room did until the lights came on.' ),
		'comments' => array(
			array( 'Marta Oliveira', 'The line about the passport photo made me laugh out loud. I shoot weddings and I am stealing "let the faces be red" for every dance floor from now on.' ),
			array( 'Tomás Reis', 'Was that the club on Rua Augusta? The stop sign looks very familiar. Great set — the glow sticks really do hold the whole frame together.' ),
			array( 'Ines Carvalho', 'I would love a follow-up on how you edit these. Do you pull the red back at all, or leave it exactly as it came out of the camera?' ),
		),
	),
);

// Posts this demo used to carry under another slug: removed, so a re-run on a
// site imported before a photograph was replaced does not keep both.
foreach ( array( 'triangles-on-turquoise-notes-from-a-harbour-wall' ) as $daren_retired ) {
	foreach ( get_posts( array( 'name' => $daren_retired, 'post_type' => 'post', 'post_status' => 'any', 'numberposts' => 1 ) ) as $daren_old ) {
		wp_delete_post( $daren_old->ID, true );
	}
}

$daren_created = 0;
$daren_updated = 0;
foreach ( $daren_posts as $daren_post ) {
	$daren_found = get_posts( array( 'name' => $daren_post['slug'], 'post_type' => 'post', 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids' ) );

	// wp_insert_post() and wp_update_post() unslash what they are given, so
	// everything goes in slashed or its backslashes are lost.
	$daren_args = array(
		'post_title'    => $daren_post['title'],
		'post_name'     => $daren_post['slug'],
		'post_content'  => trim( $daren_post['body'] ),
		'post_excerpt'  => $daren_post['excerpt'],
		'post_status'   => 'publish',
		'post_type'     => 'post',
		'post_author'   => $daren_author,
		'post_date'     => $daren_post['date'],
		'post_date_gmt' => get_gmt_from_date( $daren_post['date'] ),
		'post_category' => array( $daren_cat_ids[ $daren_post['cat'] ] ),
		'tags_input'    => $daren_post['tags'],
	);

	if ( $daren_found ) {
		$daren_id         = (int) $daren_found[0];
		$daren_args['ID'] = $daren_id;
		$daren_result     = wp_update_post( wp_slash( $daren_args ), true );
		if ( is_wp_error( $daren_result ) ) {
			continue;
		}
		$daren_updated++;
	} else {
		$daren_id = wp_insert_post( wp_slash( $daren_args ), true );
		if ( ! $daren_id || is_wp_error( $daren_id ) ) {
			continue;
		}
		$daren_created++;
	}

	$daren_image = daren_demo_image( __DIR__ . '/images/', $daren_post['image'][0], $daren_post['image'][1], $daren_id );
	if ( $daren_image && (int) get_post_thumbnail_id( $daren_id ) !== $daren_image ) {
		set_post_thumbnail( $daren_id, $daren_image );
	}

	// Each comment once: matched on its author and the post.
	if ( ! empty( $daren_post['comments'] ) ) {
		$daren_minutes = 60;
		foreach ( $daren_post['comments'] as $daren_comment ) {
			$daren_has = get_comments(
				array(
					'post_id'      => $daren_id,
					'author_email' => sanitize_title( $daren_comment[0] ) . '@example.com',
					'count'        => true,
					'status'       => 'all',
				)
			);
			if ( ! $daren_has ) {
				wp_insert_comment(
					wp_slash(
						array(
							'comment_post_ID'      => $daren_id,
							'comment_author'       => $daren_comment[0],
							'comment_author_email' => sanitize_title( $daren_comment[0] ) . '@example.com',
							'comment_content'      => $daren_comment[1],
							'comment_approved'     => 1,
							'comment_date'         => gmdate( 'Y-m-d H:i:s', strtotime( $daren_post['date'] ) + $daren_minutes * 60 ),
						)
					)
				);
			}
			$daren_minutes += 95;
		}
	}
}

// Letters for avatars, rather than a row of identical grey silhouettes.
update_option( 'avatar_default', 'identicon' );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::success( sprintf( 'Daren demo: %d posts created, %d updated.', $daren_created, $daren_updated ) );
}
