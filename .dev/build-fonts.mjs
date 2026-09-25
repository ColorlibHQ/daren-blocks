/**
 * Downloads the theme's webfonts into assets/fonts/.
 *
 * The HTML template loads Open Sans (400, 400 italic, 600, 700, and the 800 its
 * bylines use) and Source Serif Pro (400, 600, 700) from Google Fonts. Here they
 * are self-hosted, so no page contacts Google:
 *
 * - Open Sans as its variable cut: one file covers every weight from 300 to 800,
 *   which is what the design's five weights would otherwise cost in five files.
 * - Source Serif Pro as three static cuts. Fontsource ships the real 600 and 700;
 *   there is no variable Source Serif Pro (its successor, Source Serif 4, draws
 *   differently at display sizes, and the design was drawn with Pro).
 *
 * Each face comes in two subsets, latin and latin-ext, and theme.json declares
 * both with their unicode ranges, so an English page downloads only latin and a
 * Latvian, Polish or Czech one gets its letters from the same family instead of
 * a fallback. build_theme.py names exactly these files: an undeclared file is
 * dead weight in the zip, and a declared file that is missing is a 404.
 *
 * Usage:  node .dev/build-fonts.mjs
 */

import { writeFileSync, mkdirSync } from 'node:fs';
import { dirname, join, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve( dirname( fileURLToPath( import.meta.url ) ), '..' );
const out = join( root, 'assets/fonts' );

const FILES = [];
for ( const subset of [ 'latin', 'latin-ext' ] ) {
	for ( const style of [ 'normal', 'italic' ] ) {
		FILES.push( {
			url: `https://cdn.jsdelivr.net/npm/@fontsource-variable/open-sans@5.2.7/files/open-sans-${ subset }-wght-${ style }.woff2`,
			name: `open-sans-${ subset }-wght-${ style }.woff2`,
		} );
	}
	for ( const weight of [ 400, 600, 700 ] ) {
		FILES.push( {
			url: `https://cdn.jsdelivr.net/npm/@fontsource/source-serif-pro@5.2.5/files/source-serif-pro-${ subset }-${ weight }-normal.woff2`,
			name: `source-serif-pro-${ subset }-${ weight }-normal.woff2`,
		} );
	}
}

mkdirSync( out, { recursive: true } );
let bytes = 0;

for ( const { url, name } of FILES ) {
	const res = await fetch( url );
	if ( ! res.ok ) {
		throw new Error( `${ res.status } ${ url }` );
	}
	const buf = Buffer.from( await res.arrayBuffer() );
	writeFileSync( join( out, name ), buf );
	bytes += buf.length;
	console.log( `  ${ name.padEnd( 44 ) } ${ ( buf.length / 1024 ).toFixed( 1 ) }KB` );
}

console.log( `\n${ ( bytes / 1024 ).toFixed( 0 ) }KB in assets/fonts/` );
