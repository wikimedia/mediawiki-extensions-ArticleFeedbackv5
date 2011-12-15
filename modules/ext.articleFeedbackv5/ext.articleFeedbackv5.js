/*
 * Script for Article Feedback Extension
 */
( function( $ ) {

/* Load at the bottom of the article */
var $aftDiv = $( '<div id="mw-articlefeedbackv5"></div>' ).articleFeedbackv5();

// Put on bottom of article before #catlinks (if it exists)
// Except in legacy skins, which have #catlinks above the article but inside content-div.
var legacyskins = [ 'standard', 'cologneblue', 'nostalgia' ];
if ( $( '#catlinks' ).length && $.inArray( mw.config.get( 'skin' ), legacyskins ) < 0 ) {
	$aftDiv.insertBefore( '#catlinks' );
} else {
	// CologneBlue, Nostalgia, ...
	mw.util.$content.append( $aftDiv );
}

/* Setup for feedback links */

// Info about each of the links
var linkInfo = {
	'1': { trackId: 'section-link' },
	'2': { trackId: 'titlebar-link' },
	'3': { trackId: 'vertical-link' },
	'4': { trackId: 'toolbox-link' }
};

// Click event
var clickFeedbackLink = function ( $link ) {
	var tracking_id = $aftDiv.articleFeedbackv5( 'bucketName' ) +
		linkInfo[ $link.data( 'linkId' ) ].trackId;
	$aftDiv.articleFeedbackv5( 'trackClick', tracking_id );
	$aftDiv.articleFeedbackv5( 'openAsModal', $link );
};

// Bucketing
var linkBucket = function () {
	// Find out which link bucket they go in:
	// 1. Display buckets 0 or 5?  Always zero.
	// 2. Requested in query string (debug only)
	// 3. Random bucketing
	var displayBucket = $aftDiv.articleFeedbackv5( 'getBucketId' );
	if ( '5' == displayBucket || '0' == displayBucket ) {
		return '0';
	}
	var knownBuckets = { '0': true, '1': true, '2': true, '3': true };
	var requested = mw.util.getParamValue( 'aft_link' );
	if ( $aftDiv.articleFeedbackv5( 'inDebug' ) && requested in knownBuckets ) {
		return requested;
	} else {
		var bucketName = mw.user.bucket( 'ext.articleFeedbackv5-links',
			mw.config.get( 'wgArticleFeedbackv5LinkBuckets' )
		);
		var nameMap = { '-': '0', 'A': '1', 'B': '2', 'C': '3' };
		aft5_debug('Links option: ' + bucketName + ' - ' + nameMap[bucketName]);
		return nameMap[bucketName];
	}
}();
if ( $aftDiv.articleFeedbackv5( 'inDebug' ) ) {
	aft5_debug( 'Using link option #' + linkBucket );
}

/*** REMOVING FEEDBACK LINK FOR 1.0 PER ERIK's REQUEST ***/
/*** TO RESTORE FUNCTIONALITY REMOVE THE FOLLOWING LINE ***/
linkBucket = '0';

/* Add section links */
if ( '1' == linkBucket ) {
	var $wrp = $( '<span class="articleFeedbackv5-sectionlink-wrap"></span>' )
		.html( '&nbsp;[<a href="#mw-articlefeedbackv5" class="articleFeedbackv5-sectionlink"></a>]' );
	$wrp.find( 'a.articleFeedbackv5-sectionlink' )
		.data( 'linkId', 1 )
		.text( mw.msg( 'articlefeedbackv5-section-linktext' ) )
		.click( function ( e ) {
			e.preventDefault();
			clickFeedbackLink( $( e.target ) );
		} );
	$( 'span.editsection' ).append( $wrp );
	$aftDiv.articleFeedbackv5( 'addToRemovalQueue', $wrp );
}

/* Add titlebar link */
if ( '2' == linkBucket ) {
	var $tlk = $( '<a href="#mw-articleFeedbackv5" id="articleFeedbackv5-titlebarlink"></a>' )
		.data( 'linkId', 2 )
		.text( mw.msg( 'articlefeedbackv5-titlebar-linktext' ) )
		.click( function ( e ) {
			e.preventDefault();
			clickFeedbackLink( $( e.target ) );
		} )
		.insertBefore( $aftDiv );
	$aftDiv.articleFeedbackv5( 'addToRemovalQueue', $tlk );
}

/* Add fixed tab link */
if( '3' == linkBucket ) {
	var $fixedTab = $( '\
		<div id="articleFeedbackv5-fixedtab">\
			<div id="articleFeedbackv5-fixedtabbox">\
				<a href="#mw-articleFeedbackv5" id="articleFeedbackv5-fixedtablink"></a>\
			</div>\
		</div>' );
	$fixedTab.find( '#articleFeedbackv5-fixedtablink' )
		.data( 'linkId', 3 )
		.attr( 'title', mw.msg( 'articlefeedbackv5-fixedtab-linktext' ) )
		.click( function( e ) {
			e.preventDefault();
			clickFeedbackLink( $( e.target ) );
		} );
	$fixedTab.insertBefore( $aftDiv );
	$fixedTab.addClass( 'articleFeedbackv5-fixedtab' );
	$fixedTab.find( '#articleFeedbackv5-fixedtabbox' ).addClass( 'articleFeedbackv5-fixedtabbox' );
	$fixedTab.find( '#articleFeedbackv5-fixedtablink' ).addClass( 'articleFeedbackv5-fixedtablink' );
	$aftDiv.articleFeedbackv5( 'addToRemovalQueue', $fixedTab );
}

/* Add toolbox link */
if ( '5' == $aftDiv.articleFeedbackv5( 'getBucketId' ) ) {
	var $tbx = $( '<li id="t-articlefeedbackv5"><a href="#mw-articlefeedbackv5"></a></li>' )
		.find( 'a' )
			.text( mw.msg( 'articlefeedbackv5-bucket5-toolbox-linktext' ) )
			.click( function ( e ) {
				// Just set the link ID -- this should act just like AFTv4
				$aftDiv.articleFeedbackv5( 'setLinkId', 4 );
			} )
		.end();
	$( '#p-tb' ).find( 'ul' ).append( $tbx );
} else {
	var $tbx = $( '<li id="t-articlefeedbackv5"><a href="#mw-articlefeedbackv5"></a></li>' )
		.find( 'a' )
			.text( mw.msg( 'articlefeedbackv5-toolbox-linktext' ) )
			.click( function ( e ) {
				e.preventDefault();
				clickFeedbackLink( $( e.target ) );
			} )
		.end();
	$( '#p-tb' ).find( 'ul' ).append( $tbx );
	$aftDiv.articleFeedbackv5( 'addToRemovalQueue', $tbx );
}

} )( jQuery );
