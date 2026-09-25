/**
 * Run the Theme Check plugin against the installed theme and summarise it.
 *
 * Theme Check must see the BUILT theme (bash .dev/build-zip.sh), not the
 * working tree, or it reports on the tooling in .dev/ as well. So the
 * Playground for this mounts the build output as `daren` and installs the
 * plugin from the directory:
 *
 *   bash .dev/build-zip.sh "$SCRATCH/daren-build"
 *   npx -y @wp-playground/cli@3.1.54 server --port=9491 --php=8.3 --wp=latest \
 *     --mount-before-install="$SCRATCH/daren-build/daren:/wordpress/wp-content/themes/daren" \
 *     --blueprint=.dev/blueprint-themecheck.json --login
 *   WP_URL=http://127.0.0.1:9491 node .dev/theme-check.mjs
 *
 * @package Daren
 */

import { chromium } from 'playwright';

const url = ( process.env.WP_URL || 'http://127.0.0.1:9491' ).replace( /\/$/, '' );
const theme = process.env.THEME || 'daren';

const browser = await chromium.launch();
const page = await ( await browser.newContext() ).newPage();

await page.goto( url + '/wp-login.php', { waitUntil: 'commit' } );
await page.fill( '#user_login', 'admin' );
await page.fill( '#user_pass', 'password' );
await page.click( '#wp-submit' );
await page.waitForFunction( () => 'complete' === document.readyState );

await page.goto( url + '/wp-admin/themes.php?page=themecheck', { waitUntil: 'domcontentloaded', timeout: 120000 } );
await page.selectOption( 'select[name="themename"]', theme );
await Promise.all( [
	page.waitForNavigation( { waitUntil: 'domcontentloaded', timeout: 300000 } ),
	page.click( 'input[type="submit"][value*="Check"], button[type="submit"]' ),
] );

const result = await page.evaluate( () => {
	const box = document.querySelector( '.tc-box' ) || document.querySelector( '#theme-check' ) || document.body;
	const items = [ ...box.querySelectorAll( 'li' ) ].map( ( li ) => li.innerText.trim().replace( /\s+/g, ' ' ) );
	const summary = ( document.querySelector( '.tc-success, .tc-fail' ) || {} ).innerText || '';
	return { items, summary };
} );

await browser.close();

const kinds = { REQUIRED: [], WARNING: [], RECOMMENDED: [], INFO: [] };
for ( const item of result.items ) {
	const kind = Object.keys( kinds ).find( ( k ) => item.startsWith( k ) );
	if ( kind ) {
		kinds[ kind ].push( item );
	}
}

console.log( result.summary.trim() || '(no summary line)' );
for ( const [ kind, list ] of Object.entries( kinds ) ) {
	console.log( `\n${ kind }: ${ list.length }` );
	list.forEach( ( item ) => console.log( '  - ' + item.slice( 0, 400 ) ) );
}
