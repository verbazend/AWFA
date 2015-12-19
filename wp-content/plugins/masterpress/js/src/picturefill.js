/*! Picturefill - Responsive Images that work today. (and mimic the proposed Picture element with divs). Author: Scott Jehl, Filament Group, 2012 | License: MIT/GPLv2 */

(function( w ){
	
	// Enable strict mode
	"use strict";

	w.picturefill = function() {
		var ps = w.document.getElementsByTagName( "div" );
		
		// Loop the pictures
		for( var i = 0, il = ps.length; i < il; i++ ){
			if( ps[ i ].getAttribute( "data-picture" ) !== null ){

				var sources = ps[ i ].getElementsByTagName( "div" ),
					matches = [];
			
				// See if which sources match
				for( var j = 0, jl = sources.length; j < jl; j++ ){
					var media = sources[ j ].getAttribute( "data-media" );
					// if there's no media specified, OR w.matchMedia is supported 
					if( !media || ( w.matchMedia && w.matchMedia( media ).matches ) ){
						matches.push( sources[ j ] );
					}
				}

			// Find any existing img element in the picture element
			var picImg = ps[ i ].getElementsByTagName( "img" )[ 0 ];

			if( matches.length ){			
				if( !picImg ){
					picImg = w.document.createElement( "img" );
					picImg.alt = ps[ i ].getAttribute( "data-alt" );
					ps[ i ].appendChild( picImg );
				}
				
				picImg.src =  matches.pop().getAttribute( "data-src" );
			}
			else if( picImg ){
				ps[ i ].removeChild( picImg );
			}
		}
		}
	};
	
	jQuery( function($) {
		w.picturefill();
		
		$(window).load( function() {
			$('body').addClass("picturefill-loaded");
		});
	
	});
	
	
	
}( this ));