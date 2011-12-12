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

// Only track users who have been assigned to the tracking group; don't bucket
// at all if we're set to always ignore or always track.
var useClickTracking = function () {
	var b = mw.config.get( 'wgArticleFeedbackv5Tracking' );
	if ( b.buckets.ignore == 100 && b.buckets.track == 0 ) {
		return false;
	}
	if ( b.buckets.ignore == 0 && b.buckets.track == 100 ) {
		return true;
	}
	return ( 'track' === mw.user.bucket( 'ext.articleFeedbackv5-tracking', b ) );
}();

// Info about each of the links
var linkInfo = {
	'1': {
		clickTracking: $aftDiv.articleFeedbackv5( 'prefix', 'section-link' )
	},
	'2': {
		clickTracking: $aftDiv.articleFeedbackv5( 'prefix', 'titlebar-link' )
	},
	'4': {
		clickTracking: $aftDiv.articleFeedbackv5( 'prefix', 'toolbox-link' )
	}
};

// Click event
var clickFeedbackLink = function ( $link ) {
	// Click tracking
	if ( useClickTracking && $.isFunction( $.trackActionWithInfo ) ) {
		$.trackActionWithInfo( linkInfo[ $link.data( 'linkId' ) ].clickTracking, mw.config.get( 'wgTitle' ) );
	}
	// Open as modal
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
		.text( mw.msg( 'articlefeedbackv5-fixedtab-linktext' ) )
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
