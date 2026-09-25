/**
 * Open every Daren pattern in the block editor and look at it there.
 *
 * validate-blocks.mjs proves the markup parses. It cannot see what an editor
 * sees: an earlier Colorlib theme shipped a release in which every icon tile read "Type / to
 * choose a block", because the editor does not draw an empty inline element
 * and no checker ever opened the editor. This one does, for each pattern:
 *
 *   - no block shows the "unexpected or invalid content" warning,
 *   - no block is missing (core/missing),
 *   - no text block is empty and showing a placeholder,
 *   - every `daren-icon--*` / `daren-action--*` element draws its icon,
 *
 * and it saves a screenshot of the canvas to .dev/editor/<pattern>.png for a
 * person to look at. Nothing is saved to the site.
 *
 *   WP_URL=http://127.0.0.1:9491 WP_USER=admin WP_PASS=password node .dev/editor-check.mjs
 */

import { chromium } from 'playwright';
import { mkdirSync } from 'node:fs';
import { dirname, join, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const out = resolve( dirname( fileURLToPath( import.meta.url ) ), 'editor' );
mkdirSync( out, { recursive: true } );

const url = ( process.env.WP_URL || 'http://127.0.0.1:9491' ).replace( /\/$/, '' );
const user = process.env.WP_USER || 'admin';
const pass = process.env.WP_PASS || 'password';
const only = process.argv.slice( 2 );

const browser = await chromium.launch();
const context = await browser.newContext( { viewport: { width: 1440, height: 1000 }, deviceScaleFactor: 1 } );
const page = await context.newPage();

await page.goto( url + '/wp-login.php', { waitUntil: 'commit' } );
if ( await page.$( '#user_login' ) ) {
	await page.fill( '#user_login', user );
	await page.fill( '#user_pass', pass );
	await page.click( '#wp-submit' );
}
await page.waitForLoadState( 'domcontentloaded' );

// A page, not a post: the pages are what these patterns are made for, and a
// page has no title-and-excerpt furniture of a post around them.
await page.goto( url + '/wp-admin/post-new.php?post_type=page', { waitUntil: 'domcontentloaded', timeout: 90000 } );
await page.waitForFunction( () => window.wp?.data?.select( 'core/block-editor' ) && window.wp.blocks?.parse, null, { timeout: 90000 } );
await page.waitForTimeout( 3000 );
await page.evaluate( () => {
	window.onbeforeunload = null;
	window.wp.data.dispatch( 'core/preferences' )?.set( 'core/edit-post', 'welcomeGuide', false );
	window.wp.data.dispatch( 'core/preferences' )?.set( 'core', 'welcomeGuide', false );
	window.wp.data.dispatch( 'core/editor' ).updateEditorSettings( { autosaveInterval: 100000 } );
} );
await page.keyboard.press( 'Escape' );

const patterns = await page.evaluate( async () => {
	const all = await window.wp.apiFetch( { path: '/wp/v2/block-patterns/patterns' } );
	return all.filter( ( p ) => p.name.startsWith( 'daren/' ) ).map( ( p ) => ( { name: p.name, content: p.content } ) );
} );

let failures = 0;

for ( const pattern of patterns ) {
	const slug = pattern.name.replace( 'daren/', '' );
	if ( only.length && ! only.includes( slug ) ) {
		continue;
	}

	await page.evaluate( ( content ) => {
		const blocks = window.wp.blocks.parse( content );
		window.wp.data.dispatch( 'core/block-editor' ).resetBlocks( blocks );
		window.wp.data.dispatch( 'core/block-editor' ).clearSelectedBlock();
	}, pattern.content );
	await page.waitForTimeout( 3500 );

	const frame = page.frameLocator( 'iframe[name="editor-canvas"]' );
	const canvas = page.frames().find( ( f ) => 'editor-canvas' === f.name() ) || page.mainFrame();

	// Let lazy images and query previews arrive before looking.
	await canvas.evaluate( async () => {
		for ( let y = 0; y < document.body.scrollHeight; y += 700 ) {
			window.scrollTo( 0, y );
			await new Promise( ( r ) => setTimeout( r, 120 ) );
		}
		window.scrollTo( 0, 0 );
	} ).catch( () => {} );
	await page.waitForTimeout( 1500 );

	const report = await canvas.evaluate( () => {
		const problems = [];
		document.querySelectorAll( '.block-editor-warning' ).forEach( ( w ) => {
			const text = w.textContent.trim();
			// Not a defect: this checker opens patterns in a PAGE, where comments
			// are off. The comments pattern is only ever used on posts.
			if ( /Comments are not enabled/.test( text ) ) {
				return;
			}
			problems.push( 'warning: ' + text.slice( 0, 90 ) );
		} );
		document.querySelectorAll( '[data-type="core/missing"]' ).forEach( () => problems.push( 'missing block' ) );
		// An empty rich-text block shows its placeholder: "Type / to choose a block".
		document.querySelectorAll( '.block-editor-rich-text__editable[data-empty="true"]' ).forEach( ( el ) => {
			problems.push( 'empty text block showing a placeholder: ' + ( el.getAttribute( 'aria-label' ) || el.className ).slice( 0, 60 ) );
		} );
		let icons = 0;
		document.querySelectorAll( '[class*="daren-icon--"], [class*="daren-action--"]' ).forEach( ( el ) => {
			const before = getComputedStyle( el, '::before' );
			const mask = before.maskImage || before.webkitMaskImage || '';
			icons++;
			if ( 'none' === before.content || ! /url\(/.test( mask ) || 0 === parseFloat( before.width ) ) {
				problems.push( 'icon not drawn on .' + [ ...el.classList ].find( ( c ) => /daren-(icon|action)--/.test( c ) ) );
			}
		} );
		return { problems, icons, height: document.body.scrollHeight };
	} );

	const root = frame.locator( '.is-root-container' ).first();
	await root.screenshot( { path: join( out, slug + '.png' ), timeout: 30000 } ).catch( () => page.screenshot( { path: join( out, slug + '.png' ) } ) );

	const unique = [ ...new Set( report.problems ) ];
	if ( unique.length ) {
		failures++;
		console.log( `FAIL ${ slug }` );
		unique.slice( 0, 8 ).forEach( ( p ) => console.log( '     ' + p ) );
	} else {
		console.log( `ok   ${ slug }  (${ report.icons } icons drawn)` );
	}
}

await browser.close();

if ( failures ) {
	console.error( `\n${ failures } pattern(s) have problems in the editor` );
	process.exit( 1 );
}
console.log( `\nEvery pattern opens clean in the editor; screenshots in .dev/editor/` );
