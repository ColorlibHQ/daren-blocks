/**
 * Side-by-side full-page renders: the HTML template on the left, the theme on
 * the right, at 1440 and 390.
 *
 * Comparing properties one at a time is not comparing designs: every value
 * can be defensible and the whole still read as a quieter relative. One look
 * at the two pages next to each other is the check that catches that.
 *
 *   WP_URL=http://127.0.0.1:9491 node .dev/compare.mjs            # all pairs
 *   WP_URL=… node .dev/compare.mjs home single                     # some
 *
 * Writes .dev/compare/<pair>-<width>.png (JPEG-compressed PNGs are not a
 * thing; these are downscaled to half size to keep the repository small).
 */

import { chromium } from 'playwright';
import sharp from 'sharp';
import { mkdirSync } from 'node:fs';
import { dirname, join, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const out = resolve( dirname( fileURLToPath( import.meta.url ) ), 'compare' );
const site = ( process.env.WP_URL || 'http://127.0.0.1:9491' ).replace( /\/$/, '' );
const template = 'https://preview.colorlib.com/theme/daren/';

const PAIRS = {
	home: [ 'index.html', '/' ],
	archive: [ 'archive.html', '/blog/' ],
	category: [ 'category.html', '/category/street-art/' ],
	single: [ 'single-blog.html', '/neon-nights-shooting-under-red-light/' ],
	contact: [ 'contact.html', '/contact/' ],
};

const wanted = process.argv.slice( 2 ).length ? process.argv.slice( 2 ) : Object.keys( PAIRS );
mkdirSync( out, { recursive: true } );

const browser = await chromium.launch();

async function shoot( url, width ) {
	const context = await browser.newContext( {
		viewport: { width, height: 900 },
		deviceScaleFactor: 1,
		isMobile: width <= 480,
		hasTouch: width <= 480,
	} );
	const page = await context.newPage();
	await page.goto( url, { waitUntil: 'networkidle', timeout: 90000 } ).catch( () => {} );
	// The Playground logs every visitor in; a visitor sees no admin bar.
	await page.addStyleTag( { content: '#wpadminbar{display:none!important}html{margin-top:0!important;--wp-admin--admin-bar--height:0px!important}' } ).catch( () => {} );
	await page.evaluate( async () => {
		document.querySelectorAll( 'img[loading="lazy"]' ).forEach( ( img ) => {
			img.loading = 'eager';
		} );
		for ( let y = 0; y < document.body.scrollHeight; y += 600 ) {
			window.scrollTo( 0, y );
			await new Promise( ( r ) => setTimeout( r, 60 ) );
		}
		window.scrollTo( 0, 0 );
		await Promise.allSettled( [ ...document.images ].map( ( img ) => img.decode().catch( () => {} ) ) );
	} );
	await page.waitForTimeout( 1000 );
	const buffer = await page.screenshot( { fullPage: true } );
	await context.close();
	return buffer;
}

for ( const name of wanted ) {
	const [ tpl, path ] = PAIRS[ name ];
	for ( const width of [ 1440, 390 ] ) {
		const left = sharp( await shoot( template + tpl, width ) );
		const right = sharp( await shoot( site + path, width ) );
		const [ a, b ] = await Promise.all( [ left.metadata(), right.metadata() ] );
		const gap = 40;
		const height = Math.max( a.height, b.height );
		const canvas = sharp( {
			create: { width: a.width + b.width + gap, height, channels: 3, background: '#9aa0a6' },
		} ).composite( [
			{ input: await left.toBuffer(), left: 0, top: 0 },
			{ input: await right.toBuffer(), left: a.width + gap, top: 0 },
		] );
		const file = join( out, `${ name }-${ width }.png` );
		const scale = width > 1000 ? 0.5 : 1;
		await sharp( await canvas.png().toBuffer() )
			.resize( Math.round( ( a.width + b.width + gap ) * scale ) )
			.png( { compressionLevel: 9, palette: true, quality: 80 } )
			.toFile( file );
		console.log( `${ file }  template ${ a.height }px, theme ${ b.height }px` );
	}
}

await browser.close();
