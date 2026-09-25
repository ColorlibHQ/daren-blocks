/**
 * Rebuild the starter pages from the current patterns, on a development site.
 *
 * Activation copies each page pattern into a page, expanded, so the copy is
 * editable. The price is that a pattern change never reaches pages that
 * already exist — and every rendered check (contrast, overflow, buttons) runs
 * against those pages, so it goes on measuring the old markup. That cost a
 * real debugging session on an earlier theme. Run this after build_patterns.py and
 * normalize-blocks.mjs, or start a fresh Playground.
 *
 * It expands nested pattern references the way inc/front-page-setup.php does,
 * and writes through the REST API, which slashes correctly.
 *
 *   WP_URL=http://127.0.0.1:9491 WP_USER=admin WP_PASS=password node .dev/refresh-pages.mjs
 *
 * Never point it at a site whose pages someone has edited: it replaces them.
 */

import { chromium } from 'playwright';

const url = ( process.env.WP_URL || 'http://127.0.0.1:9491' ).replace( /\/$/, '' );
const user = process.env.WP_USER || 'admin';
const pass = process.env.WP_PASS || 'password';

const browser = await chromium.launch();
const page = await ( await browser.newContext() ).newPage();

await page.goto( url + '/wp-login.php', { waitUntil: 'commit' } );
if ( await page.$( '#user_login' ) ) {
	await page.fill( '#user_login', user );
	await page.fill( '#user_pass', pass );
	await page.click( '#wp-submit' );
}
await page.waitForLoadState( 'domcontentloaded' );
await page.goto( url + '/wp-admin/edit.php?post_type=page', { waitUntil: 'domcontentloaded' } );
await page.waitForFunction( () => window.wp && window.wp.apiFetch, null, { timeout: 60000 } );

const done = await page.evaluate( async () => {
	const patterns = await window.wp.apiFetch( { path: '/wp/v2/block-patterns/patterns' } );
	const byName = Object.fromEntries( patterns.map( ( p ) => [ p.name, p.content ] ) );
	const expand = ( name, seen = [] ) => {
		const content = byName[ name ] || '';
		if ( seen.includes( name ) ) {
			return content;
		}
		return content.replace( /<!--\s*wp:pattern\s+(\{.*?\})\s*\/-->/gs, ( match, json ) => {
			const nested = expand( JSON.parse( json ).slug, [ ...seen, name ] );
			return nested || match;
		} );
	};
	const map = { home: 'daren/page-home', about: 'daren/page-about', contact: 'daren/page-contact' };
	const pages = await window.wp.apiFetch( { path: '/wp/v2/pages?per_page=100&context=edit' } );
	const out = [];
	for ( const item of pages ) {
		if ( ! map[ item.slug ] ) {
			continue;
		}
		const content = expand( map[ item.slug ] );
		await window.wp.apiFetch( { path: '/wp/v2/pages/' + item.id, method: 'POST', data: { content } } );
		out.push( `${ item.slug } (${ content.length } bytes)` );
	}
	return out;
} );

await browser.close();
console.log( done.length ? 'rebuilt: ' + done.join( ', ' ) : 'no starter pages found' );
