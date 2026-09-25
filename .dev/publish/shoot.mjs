/**
 * Screenshots for the colorlib.com product page, from a Playground that has the
 * demo content imported (.dev/blueprint.json does that).
 *
 *   node .dev/publish/shoot.mjs            # WP_URL defaults to http://127.0.0.1:9491
 *
 * Viewport captures, not full-page ones: each is one screen of one section,
 * rendered at 1440 CSS px and scaled to 1140 wide, JPEG q82. The palette tiles
 * re-render the same screen under six palettes by overriding the preset
 * variables client-side, which is exact and changes nothing on the site.
 * Raw PNGs go to .dev/publish/raw/ (untracked); .dev/publish/finish.py turns
 * them into the JPEGs in .dev/publish/images/.
 *
 * @package Daren
 */

import { chromium } from 'playwright';
import { mkdirSync, readFileSync, readdirSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const here = dirname( fileURLToPath( import.meta.url ) );
const root = join( here, '..', '..' );
const base = ( process.env.WP_URL || 'http://127.0.0.1:9491' ).replace( /\/$/, '' );
const raw = join( here, 'raw' );
mkdirSync( raw, { recursive: true } );

const hideAdmin = '#wpadminbar{display:none!important}html{margin-top:0!important;--wp-admin--admin-bar--height:0px!important}';

const browser = await chromium.launch();

async function open( path, { width = 1440, height = 900, dark = false, css = '' } = {} ) {
	const context = await browser.newContext( { viewport: { width, height }, deviceScaleFactor: 1 } );
	await context.addInitScript( ( d ) => {
		try {
			localStorage.setItem( 'daren-scheme', d ? 'dark' : 'light' );
		} catch ( e ) {}
	}, dark );
	const page = await context.newPage();
	await page.goto( base + path, { waitUntil: 'networkidle', timeout: 90000 } );
	await page.addStyleTag( { content: hideAdmin + css } );
	await page.evaluate( async () => {
		document.querySelectorAll( 'img[loading="lazy"]' ).forEach( ( img ) => {
			img.loading = 'eager';
		} );
		await Promise.all( [ ...document.images ].map( ( img ) => ( img.complete ? 0 : new Promise( ( r ) => {
			img.onload = img.onerror = r;
		} ) ) ) );
	} );
	await page.waitForTimeout( 600 );
	return { page, context };
}

async function shot( name, path, opts = {} ) {
	const { page, context } = await open( path, opts );
	if ( opts.scrollTo ) {
		await page.evaluate( ( sel ) => {
			const el = document.querySelector( sel );
			if ( el ) {
				window.scrollTo( 0, el.getBoundingClientRect().top + window.scrollY - 40 );
			}
		}, opts.scrollTo );
		await page.waitForTimeout( 400 );
	}
	await page.screenshot( { path: join( raw, name + '.png' ) } );
	await context.close();
	console.log( name );
}

// One screen each.
await shot( 'card', '/', { width: 1440, height: 960 } );
await shot( 'home', '/', { height: 800 } );
await shot( 'story-layouts', '/', { height: 1228, scrollTo: '.daren-checker-section' } );
await shot( 'dark-mode', '/', { dark: true, height: 1000, scrollTo: '.daren-checker-section' } );
await shot( 'contact-form', '/contact/', { height: 1150, scrollTo: 'main iframe' } );
await shot( 'blog', '/blog/' );
await shot( 'single-post', '/neon-nights-shooting-under-red-light/' );

// The same screen under six palettes.
const dir = join( root, 'styles', 'colors' );
const wanted = [ 'scarlet', 'cobalt', 'jade', 'tangerine', 'orchid', 'midnight' ];
for ( const file of readdirSync( dir ).sort() ) {
	const json = JSON.parse( readFileSync( join( dir, file ), 'utf8' ) );
	const slug = json.title.toLowerCase();
	if ( ! wanted.includes( slug ) ) {
		continue;
	}
	const vars = json.settings.color.palette.map( ( c ) => `--wp--preset--color--${ c.slug }:${ c.color }!important;` ).join( '' );
	await shot( 'palette-' + slug, '/blog/', { width: 1440, height: 900, css: `:root,body{${ vars }}` } );
}

await browser.close();
