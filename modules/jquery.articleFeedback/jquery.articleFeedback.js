/**
 * ArticleFeedback form plugin
 *
 * This file creates the plugin that will be used to build the Article Feedback
 * form.  The flow goes like this:
 *
 * User arrives at page -> build appropriate form
 *  -> User clicks the view link -> replace form with average ratings display
 *  -> User submits form -> submit to API
 *      -> has errors -> show errors
 *      -> has no errors -> select random CTA and display
 *
 * This plugin supports a choice of forms and CTAs.  Each form option is called
 * a "bucket" because users are sorted into buckets and each bucket gets a
 * different form option.  Right now, these buckets are:
 *   1. Share Your Feedback
 *   	Has a yes/no toggle on "Did you find what you were looking for?" and a
 *   	text area for comments.
 *   2. Make A Suggestion
 *   	Modeled after getsatisfaction.com; users can say that their comment is a
 *   	suggestion, question, problem, or praise.
 *   3. Review This Page
 *   	Has a single star rating field and a comment box.
 *   4. Help Edit This Page
 *   	Has no input fields; just links to the Edit page.
 *   5. Rate This Page
 *   	The existing article feedback tool, except that it can use any of the
 *   	CTA types.
 *   6. No Feedback
 *   	Shows nothing at all.
 *
 * This file is really long, so it's commented with manual fold markers.  To use
 * folds this way in vim:
 *   set foldmethod=marker
 *   set foldlevel=0
 *   set foldcolumn=0
 *
 * @package    ArticleFeedback
 * @subpackage Resources
 * @author     Reha Sterbin <reha@omniti.com>
 * @version    $Id$
 */

( function ( $ ) {

// {{{ articleFeedback definition

	$.articleFeedback = {};

	// {{{ Properties

	/**
	 * Temporary -- this will need to come from the config.
	 */
	$.articleFeedback.debug = true;

	/**
	 * The bucket ID is the variation of the Article Feedback form chosen for this
	 * particualar user.  It will be passed in at load time, but if all else fails,
	 * default to Option Six (no form).
	 *
	 * @see http://www.mediawiki.org/wiki/Article_feedback/Version_5/Feature_Requirements#Feedback_form_interface
	 */
	$.articleFeedback.bucketId = 6;

	/**
	 * The CTA is the view presented to a user who has successfully submitted
	 * feedback.
	 *
	 * @see http://www.mediawiki.org/wiki/Article_feedback/Version_5/Feature_Requirements#Calls_to_Action
	 */
	$.articleFeedback.ctaId = 1;

	/**
	 * Use the mediawiki util resource's config method to find the correct url to
	 * call for all ajax requests.
	 */
	$.articleFeedback.apiUrl = mw.config.get( 'wgScriptPath' ) + '/api.php';

	/**
	 * Is this an anonymous user?
	 */
	$.articleFeedback.anonymous = mw.user.anonymous();

	/**
	 * If not, what's their user id?
	 */
	$.articleFeedback.userId = mw.user.id();

	/**
	 * The page ID
	 */
	$.articleFeedback.pageId = mw.config.get( 'wgArticleId' );

	/**
	 * The revision ID
	 */
	$.articleFeedback.revisionId = mw.config.get( 'wgCurRevisionId' );

	// }}}
	// {{{ Bucket UI objects

	/**
	 * Set up the buckets' UI objects
	 */
	$.articleFeedback.buckets = {

		// {{{ Bucket 5

		/**
		 * Bucket 5: Old ratings form
		 */
		'5': {

			// {{{ properties

			/**
			 * The ratings right now are coming from the config, but they really
			 * can't be configured.  Eventually, these should just be hardcoded.
			 */
			ratingInfo: mw.config.get( 'wgArticleFeedbackv5RatingTypesFlipped' ),

			/**
			 * Only certain users can see the expertise checkboxes and email
			 */
			showOptions: 'show' === mw.user.bucket( 'ext.articleFeedback-options', mw.config.get( 'wgArticleFeedbackv5Options' ) ),

			// }}}
			// {{{ buildForm

			/**
			 * Builds the empty form
			 *
			 * @return Element the form
			 */
			buildForm: function () {

				// The overall template
				var block_tpl = '\
					<form>\
						<div class="articleFeedback-panel">\
							<div class="articleFeedback-buffer articleFeedback-ui">\
								<div class="articleFeedback-switch articleFeedback-switch-report articleFeedback-visibleWith-form" rel="report"><html:msg key="report-switch-label" /></div>\
								<div class="articleFeedback-switch articleFeedback-switch-form articleFeedback-visibleWith-report" rel="form"><html:msg key="form-switch-label" /></div>\
								<div class="articleFeedback-title articleFeedback-visibleWith-form"><html:msg key="form-panel-title" /></div>\
								<div class="articleFeedback-title articleFeedback-visibleWith-report"><html:msg key="report-panel-title" /></div>\
								<div class="articleFeedback-explanation articleFeedback-visibleWith-form"><a class="articleFeedback-explanation-link"><html:msg key="form-panel-explanation" /></a></div>\
								<div class="articleFeedback-description articleFeedback-visibleWith-report"><html:msg key="report-panel-description" /></div>\
								<div style="clear:both;"></div>\
								<div class="articleFeedback-ratings"></div>\
								<div style="clear:both;"></div>\
								<div class="articleFeedback-options">\
									<div class="articleFeedback-expertise articleFeedback-visibleWith-form" >\
										<input type="checkbox" value="general" disabled="disabled" /><label class="articleFeedback-expertise-disabled"><html:msg key="form-panel-expertise" /></label>\
										<div class="articleFeedback-expertise-options">\
											<div><input type="checkbox" value="studies" /><label><html:msg key="form-panel-expertise-studies" /></label></div>\
											<div><input type="checkbox" value="profession" /><label><html:msg key="form-panel-expertise-profession" /></label></div>\
											<div><input type="checkbox" value="hobby" /><label><html:msg key="form-panel-expertise-hobby" /></label></div>\
											<div><input type="checkbox" value="other" /><label><html:msg key="form-panel-expertise-other" /></label></div>\
											<div class="articleFeedback-helpimprove">\
												<input type="checkbox" value="helpimprove-email" />\
												<label><html:msg key="form-panel-helpimprove" /></label>\
												<input type="text" placeholder="" class="articleFeedback-helpimprove-email" />\
												<div class="articleFeedback-helpimprove-note"></div>\
											</div>\
										</div>\
									</div>\
									<div style="clear:both;"></div>\
								</div>\
								<button class="articleFeedback-submit articleFeedback-visibleWith-form" type="submit" disabled="disabled"><html:msg key="form-panel-submit" /></button>\
								<div class="articleFeedback-success articleFeedback-visibleWith-form"><span><html:msg key="form-panel-success" /></span></div>\
								<div class="articleFeedback-pending articleFeedback-visibleWith-form"><span><html:msg key="form-panel-pending" /></span></div>\
								<div style="clear:both;"></div>\
								<div class="articleFeedback-notices articleFeedback-visibleWith-form">\
									<div class="articleFeedback-expiry">\
										<div class="articleFeedback-expiry-title"><html:msg key="form-panel-expiry-title" /></div>\
										<div class="articleFeedback-expiry-message"><html:msg key="form-panel-expiry-message" /></div>\
									</div>\
								</div>\
							</div>\
							<div class="articleFeedback-error"><div class="articleFeedback-error-message"><html:msg key="error" /></div></div>\
							<div class="articleFeedback-pitches"></div>\
							<div style="clear:both;"></div>\
						</div>\
						<input type="hidden" name="feedback_id" value="" />\
					</form>\
					';

				// A single rating block
				var rating_tpl = '\
					<div class="articleFeedback-rating">\
						<div class="articleFeedback-label"></div>\
						<input type="hidden" />\
						<div class="articleFeedback-rating-labels articleFeedback-visibleWith-form">\
							<div class="articleFeedback-rating-label" rel="1"></div>\
							<div class="articleFeedback-rating-label" rel="2"></div>\
							<div class="articleFeedback-rating-label" rel="3"></div>\
							<div class="articleFeedback-rating-label" rel="4"></div>\
							<div class="articleFeedback-rating-label" rel="5"></div>\
							<div class="articleFeedback-rating-clear"></div>\
						</div>\
						<div class="articleFeedback-visibleWith-form">\
							<div class="articleFeedback-rating-tooltip"></div>\
						</div>\
						<div class="articleFeedback-rating-average articleFeedback-visibleWith-report"></div>\
						<div class="articleFeedback-rating-meter articleFeedback-visibleWith-report"><div></div></div>\
						<div class="articleFeedback-rating-count articleFeedback-visibleWith-report"></div>\
						<div style="clear:both;"></div>\
					</div>\
					';

				// Start up the block to return
				var $block = $( block_tpl );

				// Add the ratings from the options
				$block.find( '.articleFeedback-ratings' ).each( function () {
					for ( var key in $.articleFeedback.currentBucket().ratingInfo ) {
						var	tip_msg = 'articlefeedbackv5-field-' + key + '-tip';
						var label_msg = 'articlefeedbackv5-field-' + key + '-label';
						var $rtg = $( rating_tpl ).attr( 'rel', key );
						$rtg.find( '.articleFeedback-label' )
							.attr( 'title', mw.msg( tip_msg ) )
							.text( mw.msg( label_msg ) );
						$rtg.find( '.articleFeedback-rating-clear' )
							.attr( 'title', mw.msg( 'articlefeedbackv5-form-panel-clear' ) );
						$rtg.appendTo( $(this) );
					}
				} );

				// Fill in the link to the What's This page
				$block.find( '.articleFeedback-explanation-link' )
					.attr( 'href', mw.config.get( 'wgArticlePath' ).replace(
						'$1', mw.config.get( 'wgArticleFeedbackv5WhatsThisPage' )
					) );

				// Fill in the Help Improve message and links
				$block.find( '.articleFeedback-helpimprove-note' )
					// Can't use .text() with mw.message(, /* $1 */ link).toString(),
					// because 'link' should not be re-escaped (which would happen if done by mw.message)
					.html( function () {
						var link = mw.html.element(
							'a', {
								href: mw.util.wikiGetlink( mw.msg('articlefeedbackv5-form-panel-helpimprove-privacylink') )
							}, mw.msg('articlefeedbackv5-form-panel-helpimprove-privacy')
						);
						return mw.html.escape( mw.msg( 'articlefeedbackv5-form-panel-helpimprove-note') )
							.replace( /\$1/, mw.message( 'parentheses', link ).toString() );
					});
				$block.find( '.articleFeedback-helpimprove-email' )
					.attr( 'placeholder', mw.msg( 'articlefeedbackv5-form-panel-helpimprove-email-placeholder' ) )
					.placeholder(); // back. compat. for older browsers

				// Localize the block
				$block.localize( { 'prefix': 'articlefeedbackv5-' } );

				// Activate tooltips
				$block.find( '[title]' )
					.tipsy( {
						'gravity': 'sw',
						'center': false,
						'fade': true,
						'delayIn': 300,
						'delayOut': 100
					} );

				// Set id and for on expertise checkboxes
				$block.find( '.articleFeedback-expertise input:checkbox' )
					.each( function () {
						var id = 'articleFeedback-expertise-' + $(this).attr( 'value' );
						$(this).attr( 'id', id );
						$(this).next().attr( 'for', id );
					} );
				$block.find( '.articleFeedback-helpimprove > input:checkbox' )
					.each( function () {
						var id = 'articleFeedback-expertise-' + $(this).attr( 'value' );
						$(this).attr( 'id', id );
						$(this).next().attr( 'for', id );
					})

				// Turn the submit into a slick button
				$block.find( '.articleFeedback-submit' )
					.button()
					.addClass( 'ui-button-blue' )

				// Hide report elements initially
				$block.find( '.articleFeedback-visibleWith-report' ).hide();

				// Name the hidden rating fields
				$block.find( '.articleFeedback-rating' )
					.each( function () {
						var name = $.articleFeedback.currentBucket().ratingInfo[$(this).attr( 'rel' )];
						$(this).find( 'input:hidden' ) .attr( 'name', 'r' + name );
					} );

				// Hide the additional options, if the user's in a bucket that
				// requires it
				if ( !$.articleFeedback.currentBucket().showOptions ) {
					$block.find( '.articleFeedback-options' ).hide();
				}

				// Grab the results in the background
				$.articleFeedback.currentBucket().loadAggregateRatings();

				return $block;
			},

			// }}}
			// {{{ bindEvents

			/**
			 * Binds any events
			 *
			 * @param $block element the form block
			 */
			bindEvents: function ( $block ) {

				// On-blur validity check for Help Improve email field
				$block.find( '.articleFeedback-helpimprove-email' )
					.one( 'blur', function () {
						var $el = $(this);
						var bucket = $.articleFeedback.currentBucket();
						bucket.updateMailValidityLabel( $el.val() );
						$el.keyup( function () {
							bucket.updateMailValidityLabel( $el.val() );
						} );
					} );

				// Slide-down for the expertise checkboxes
				$block.find( '.articleFeedback-expertise > input:checkbox' )
					.change( function () {
						var $options = $.articleFeedback.$holder.find( '.articleFeedback-expertise-options' );
						if ( $(this).is( ':checked' ) ) {
							$options.slideDown( 'fast' );
						} else {
							$options.slideUp( 'fast', function () {
								$options.find( 'input:checkbox' ).attr( 'checked', false );
							} );
						}
					} );

				// Enable submission when at least one rating is set
				$block.find( '.articleFeedback-expertise input:checkbox' )
					.each( function () {
						var id = 'articleFeedback-expertise-' + $(this).attr( 'value' );
						$(this).click( function () {
							$.articleFeedback.currentBucket().enableSubmission( true );
						} );
					} );

				// Clicking on the email field checks the associted box
				$block.find( '.articleFeedback-helpimprove-email' )
					.bind( 'mousedown click', function ( e ) {
						$(this).closest( '.articleFeedback-helpimprove' )
							.find( 'input:checkbox' )
							.attr( 'checked', true );
					} );

				// Attach the submit
				$block.find( '.articleFeedback-submit' )
					.click( function ( e ) {
						e.preventDefault();
						$.articleFeedback.submitForm();
					} );

				// Set up form/report switch behavior
				$block.find( '.articleFeedback-switch' )
					.click( function ( e ) {
						$.articleFeedback.$holder.find( '.articleFeedback-visibleWith-' + $(this).attr( 'rel' ) )
							.show();
						$.articleFeedback.$holder.find( '.articleFeedback-switch' )
							.not( $(this) )
							.each( function () {
								$.articleFeedback.$holder.find( '.articleFeedback-visibleWith-' + $(this).attr( 'rel' ) ).hide();
							} );
						e.preventDefault();
						return false;
					} );

				// Set up rating behavior
				var rlabel = $block.find( '.articleFeedback-rating-label' );
				rlabel.hover( function () {
					// mouse on
					var	$el = $( this );
					var $rating = $el.closest( '.articleFeedback-rating' );
					$el.addClass( 'articleFeedback-rating-label-hover-head' );
					$el.prevAll( '.articleFeedback-rating-label' )
						.addClass( 'articleFeedback-rating-label-hover-tail' );
					$rating.find( '.articleFeedback-rating-tooltip' )
						.text( mw.msg( 'articlefeedbackv5-field-' + $rating.attr( 'rel' ) + '-tooltip-' + $el.attr( 'rel' ) ) )
						.show();
				}, function () {
					// mouse off
					var	$el = $( this );
					var $rating = $el.closest( '.articleFeedback-rating' );
					$el.removeClass( 'articleFeedback-rating-label-hover-head' );
					$el.prevAll( '.articleFeedback-rating-label' )
						.removeClass( 'articleFeedback-rating-label-hover-tail' );
					$rating.find( '.articleFeedback-rating-tooltip' )
						.hide();
					var bucket = $.articleFeedback.currentBucket();
					bucket.updateRating( $rating );
				});
				rlabel.mousedown( function () {
					var bucket = $.articleFeedback.currentBucket();
					bucket.enableSubmission( true );
					var $h = $.articleFeedback.$holder;
					if ( $h.hasClass( 'articleFeedback-expired' ) ) {
						// Changing one means the rest will get submitted too
						$h.removeClass( 'articleFeedback-expired' );
						$h.find( '.articleFeedback-rating' )
							.addClass( 'articleFeedback-rating-new' );
					}
					$h.find( '.articleFeedback-expertise' )
						.each( function () {
							bucket.enableExpertise( $(this) );
						} );
					var $el = $( this );
					var $rating = $el.closest( '.articleFeedback-rating' );
					$rating.addClass( 'articleFeedback-rating-new' );
					$rating.find( 'input:hidden' ).val( $el.attr( 'rel' ) );
					$el.addClass( 'articleFeedback-rating-label-down' );
					$el.nextAll()
						.removeClass( 'articleFeedback-rating-label-full' );
					$el.parent().find( '.articleFeedback-rating-clear' ).show();
				} );
				rlabel.mouseup( function () {
					$(this).removeClass( 'articleFeedback-rating-label-down' );
				} );

				// Icon to clear out the ratings
				$block.find( '.articleFeedback-rating-clear' )
					.click( function () {
						var bucket = $.articleFeedback.currentBucket();
						bucket.enableSubmission( true );
						$(this).hide();
						var $rating = $(this).closest( '.articleFeedback-rating' );
						$rating.find( 'input:hidden' ).val( 0 );
						bucket.updateRating( $rating );
					} );

			},

			// }}}
			// {{{ updateRating

			/**
			 * Updates the stars to match the associated hidden field
			 *
			 * @param $rating the rating block
			 */
			updateRating: function ( $rating ) {
				$rating.find( '.articleFeedback-rating-label' )
					.removeClass( 'articleFeedback-rating-label-full' );
				var val = $rating.find( 'input:hidden' ).val();
				var $label = $rating.find( '.articleFeedback-rating-label[rel="' + val + '"]' );
				if ( $label.length ) {
					$label.prevAll( '.articleFeedback-rating-label' )
						.add( $label )
						.addClass( 'articleFeedback-rating-label-full' );
					$label.nextAll( '.articleFeedback-rating-label' )
						.removeClass( 'articleFeedback-rating-label-full' );
					$rating.find( '.articleFeedback-rating-clear' ).show();
				} else {
					$rating.find( '.articleFeedback-rating-clear' ).hide();
				}
			},

			// }}}
			// {{{ enableSubmission

			/**
			 * Enables or disables submission of the form
			 *
			 * @param state bool true to enable; false to disable
			 */
			enableSubmission: function ( state ) {
				var $h = $.articleFeedback.$holder;
				if ( state ) {
					if ($.articleFeedback.successTimeout) {
						clearTimeout( $.articleFeedback.successTimeout );
					}
					$h.find( '.articleFeedback-submit' ).button( { 'disabled': false } );
					$h.find( '.articleFeedback-success span' ).hide();
					$h.find( '.articleFeedback-pending span' ).fadeIn( 'fast' );
				} else {
					$h.find( '.articleFeedback-submit' ).button( { 'disabled': true } );
					$h.find( '.articleFeedback-pending span' ).hide();
				}
			},

			// }}}
			// {{{ enableExpertise

			/**
			 * Enables the expertise checkboxes
			 *
			 * @param element $el the element containing checkboxes to enable
			 */
			enableExpertise: function ( $el ) {
				$el.find( 'input:checkbox[value=general]' )
					.attr( 'disabled', false )
				$el.find( '.articleFeedback-expertise-disabled' )
					.removeClass( 'articleFeedback-expertise-disabled' );
			},

			// }}}
			// {{{ updateMailValidityLabel

			/**
			 * Given an email sting, gets validity status (true, false, null) and updates
			 * the label's CSS class
			 *
			 * @param string mail the email address
			 */
			updateMailValidityLabel: function ( mail ) {
				var	isValid = mw.util.validateEmail( mail );
				var $label = $.articleFeedback.$holder.find( '.articleFeedback-helpimprove-email' );
				if ( isValid === null ) { // empty address
					$label.removeClass( 'valid invalid' );
				} else if ( isValid ) {
					$label.addClass( 'valid' ).removeClass( 'invalid' );
				} else {
					$label.addClass( 'invalid' ).removeClass( 'valid' );
				}
			},

			// }}}
			// {{{ loadAggregateRatings

			/**
			 * Pulls the aggregate ratings via ajax request
			 * the label's CSS class
			 */
			loadAggregateRatings: function () {
				var usecache = !( !$.articleFeedback.anonymous || $.articleFeedback.alreadySubmitted );

				$.ajax( {
					'url': $.articleFeedback.apiUrl,
					'type': 'GET',
					'dataType': 'json',
					'cache': usecache,
					'data': {
						'action': 'query',
						'format': 'json',
						'list': 'articlefeedbackv5',
						'afsubaction': 'showratings',
						'afpageid': $.articleFeedback.pageId,
						'afanontoken': usecache ? $.articleFeedback.userId : '',
						'afuserrating': Number( !usecache ),
						'maxage': 0,
						'smaxage': usecache ? 0 : mw.config.get( 'wgArticleFeedbackv5SMaxage' )
					},
					'success': function ( data ) {
						// Get data
						if (
							!( 'query' in data )
							|| !( 'articlefeedbackv5' in data.query )
							|| !$.isArray( data.query.articlefeedbackv5 )
							|| !data.query.articlefeedbackv5.length
						) {
							mw.log( 'ArticleFeedback invalid response error.' );
							if ($.articleFeedback.debug && 'error' in data && 'info' in data.error) {
								console.log( data.error.info );
								$.articleFeedback.$holder.find( '.articleFeedback-error-message' ).html( data.error.info.replace( "\n", '<br />' ) );
							}
							$.articleFeedback.$holder.find( '.articleFeedback-error' ).show();
							return;
						}
						var feedback = data.query.articlefeedbackv5[0];

						// Index rating data by rating ID
						var ratings = {};
						if ( typeof feedback.ratings === 'object' && feedback.ratings !== null ) {
							for ( var i = 0; i < feedback.ratings.length; i++ ) {
								ratings[feedback.ratings[i].ratingid] = feedback.ratings[i];
							}
						}

						// Ratings
						$.articleFeedback.$holder.find( '.articleFeedback-rating' ).each( function () {
							var name = $(this).attr( 'rel' );
							var info = $.articleFeedback.currentBucket().ratingInfo;
							var rating = name in info && info[name] in ratings ?  ratings[info[name]] : null;
							if (
								rating !== null
								&& 'total' in rating
								&& 'count' in rating
								&& rating.total > 0
							) {
								var average = Math.round( ( rating.total / rating.count ) * 10 ) / 10;
								$(this).find( '.articleFeedback-rating-average' )
									.text( mw.language.convertNumber( average + ( average % 1 === 0 ? '.0' : '' ) , false ) );
								$(this).find( '.articleFeedback-rating-meter div' )
									.css( 'width', Math.round( average * 21 ) + 'px' );
								$(this).find( '.articleFeedback-rating-count' )
									.text( mw.msg( 'articlefeedbackv5-report-ratings', rating.countall ) );
							} else {
								// Special case for no ratings
								$(this).find( '.articleFeedback-rating-average' )
									.html( '&nbsp;' );
								$(this).find( '.articleFeedback-rating-meter div' )
									.css( 'width', 0 );
								$(this).find( '.articleFeedback-rating-count' )
									.text( mw.msg( 'articlefeedbackv5-report-empty' ) );
							}
						} );

						// Expiration
						if ( typeof feedback.status === 'string' && feedback.status === 'expired' ) {
							$.articleFeedback.$holder.addClass( 'articleFeedback-expired' );
							$.articleFeedback.$holder.find( '.articleFeedback-expiry' )
								.slideDown( 'fast' );
						} else {
							$.articleFeedback.$holder.removeClass( 'articleFeedback-expired' )
							$.articleFeedback.$holder.find( '.articleFeedback-expiry' )
								.slideUp( 'fast' );
						}

						// Status change - un-new the rating controls
						$.articleFeedback.$holder.find( '.articleFeedback-rating-new' )
							.removeClass( 'articleFeedback-rating-new' );
					},
					'error': function () {
						mw.log( 'Report loading error' );
						$.articleFeedback.$holder.find( '.articleFeedback-error' ).show();
					}
				} );

			},

			// }}}
			// {{{ getFormData

			/**
			 * Pulls down form data
			 *
			 * @return object the form data
			 */
			getFormData: function () {
				var data = {};
				var info = $.articleFeedback.currentBucket().ratingInfo;
				for ( var key in info ) {
					var id = info[key];
					data['r' + id] = $.articleFeedback.$holder.find( 'input[name="r' + id + '"]' ).val();
				}
				var expertise = [];
				$.articleFeedback.$holder.find( '.articleFeedback-expertise input:checked' ).each( function () {
					expertise.push( $(this).val() );
				} );
				data.expertise = expertise.join( '|' );
				return data;
			},

			// }}}
			// {{{ localValidation

			/**
			 * Performs any local validation
			 *
			 * @param  object formdata the form data
			 * @return mixed  if ok, false; otherwise, an object as { 'field name' : 'message' }
			 */
			localValidation: function ( formdata ) {
				var error = {};
				var ok = true;
				if ( $.articleFeedback.$holder.find( '.articleFeedback-helpimprove input:checked' ).length > 0 ) {
					var mail = $.articleFeedback.$holder.find( '.articleFeedback-helpimprove-email' ).val();
					if ( !mw.util.validateEmail( mail ) ) {
						error.helpimprove_email = 'That email address is not valid';
						ok = false;
					}
				}
				return ok ? false : error;
			},

			// }}}
			// {{{ lockForm

			/**
			 * Locks the form
			 */
			lockForm: function () {
				$.articleFeedback.currentBucket().enableSubmission( false );
				$.articleFeedback.$holder.find( '.articleFeedback-lock' ).show();
			},

			// }}}
			// {{{ unlockForm

			/**
			 * Unlocks the form
			 */
			unlockForm: function () {
				$.articleFeedback.currentBucket().enableSubmission( true );
				$.articleFeedback.$holder.find( '.articleFeedback-lock' ).hide();
			},

			// }}}
			// {{{ markFormError

			/**
			 * Marks any errors on the form
			 *
			 * @param object error errors, indexed by field name
			 */
			markFormError: function ( error ) {
				if ( '_api' in error ) {
					if ($.articleFeedback.debug) {
						$.articleFeedback.$holder.find( '.articleFeedback-error-message' )
							.html( error._api.info.replace( "\n", '<br />' ) );
					}
					$.articleFeedback.$holder.find( '.articleFeedback-error' ).show();
				} else {
					if ( 'helpimprove_email' in error ) {
						$.articleFeedback.$holder.find( '.articleFeedback-helpimprove-email' )
							.addClass( 'invalid' )
							.removeClass( 'valid' );
					}
					alert( 'Validation error' );
					mw.log( 'Validation error' );
				}
				console.log( error );
			},

			// }}}
			// {{{ setSuccessState

			/**
			 * Sets the success state
			 */
			setSuccessState: function () {
				var $h = $.articleFeedback.$holder;
				$h.find( '.articleFeedback-success span' ).fadeIn( 'fast' );
				$.articleFeedback.successTimeout = setTimeout( function () {
					$.articleFeedback.$holder.find( '.articleFeedback-success span' )
						.fadeOut( 'slow' );
				}, 5000 );
			},

			// }}}
			// {{{ onSubmit

			/**
			 * Sends off the email tracking request alongside the regular form
			 * submit
			 */
			onSubmit: function () {


/////////////////////////////////////////////////////////////////////////////////
// BOOKMARK
/////////////////////////////////////////////////////////////////////////////////

//		'submit': function () {
//
//			// Build data from form values for 'action=emailcapture'
//			// Ignore if email was invalid
//			if ( context.$ui.find( '.articleFeedback-helpimprove-email.valid' ).length
//				// Ignore if email field was empty (it's optional)
//				 && !$.isEmpty( context.$ui.find( '.articleFeedback-helpimprove-email' ).val() )
//				 // Ignore if checkbox was unchecked (ie. user can enter and then decide to uncheck,
//				 // field fades out, then we shouldn't submit)
//				 && context.$ui.find('.articleFeedback-helpimprove input:checked' ).length
//			) {
//				$.ajax( {
//					'url': mw.config.get( 'wgScriptPath' ) + '/api.php',
//					'type': 'POST',
//					'dataType': 'json',
//					'context': context,
//					'data': {
//						'email': context.$ui.find( '.articleFeedback-helpimprove-email' ).val(),
//						'info': $.toJSON( {
//							'ratingData': data,
//							'pageTitle': mw.config.get( 'wgTitle' ),
//							'pageCategories': mw.config.get( 'wgCategories' )
//						} ),
//						'action': 'emailcapture',
//						'format': 'json'
//					},
//					'success': function ( data ) {
//						var context = this;
//
//						if ( 'error' in data ) {
//							mw.log( 'EmailCapture: Form submission error' );
//							mw.log( data.error );
//							updateMailValidityLabel( 'triggererror', context );
//
//						} else {
//							// Hide helpimprove-email for when user returns to Rate-view
//							// without reloading page
//							context.$ui.find( '.articleFeedback-helpimprove' ).hide();
//
//							// Set cookie if it was successful, so it won't be asked again
//							$.cookie(
//								prefix( 'helpimprove-email' ),
//								// Path must be set so it will be remembered
//								// for all article (not just current level)
//								// @XXX: '/' may be too wide (multi-wiki domains)
//								'hide', { 'expires': 30, 'path': '/' }
//							);
//						}
//					}
//				} );
//
//			// If something was invalid, reset the helpimprove-email part of the form.
//			// When user returns from submit, it will be clean
//			} else {
//				context.$ui
//					.find( '.articleFeedback-helpimprove' )
//						.find( 'input:checkbox' )
//							.removeAttr( 'checked' )
//							.end()
//						.find( '.articleFeedback-helpimprove-email' )
//							.val( '' )
//							.removeClass( 'valid invalid' );
//			}
//		},

			}

			// }}}

		},

		// }}}
		// {{{ Bucket 6

		/**
		 * Bucket 6: No form
		 */
		'6': { }

		// }}}

	};

	// }}}
	// {{{ CTA objects

	/**
	 * Set up the CTA options' UI objects
	 */
	$.articleFeedback.ctas = {

		// {{{ CTA 1: Encticement to edit

		'1': {

			// {{{ build

			/**
			 * Builds the CTA
			 *
			 * @return Element the form
			 */
			build: function () {

				// The overall template
				var block_tpl = '\
					<div class="articleFeedback-cta-panel">\
						<h5>TODO: EDIT CTA</h5>\
						<p>Eventually this will have a pretty button and some nice messages.  For now, though...</p>\
						<p><a href="" class="articleFeedback-edit-cta-link">EDIT THIS PAGE</a></p>\
					</div>\
					';

				// Start up the block to return
				var $block = $( block_tpl );

				// Fill in the link
				$block.find( '.articleFeedback-edit-link' )
					.attr( 'href', mw.config.get( 'wgArticlePath' ).replace(
						'$1', mw.config.get( 'wgArticleFeedbackv5WhatsThisPage' )
					) );

				return $block;
			}

			// }}}

		},

		// }}}

	};

	// }}}
	// {{{ Initialization

	/**
	 * Initializes the object
	 *
	 * The init method sets up the object once the plugin has been called.  Note
	 * that this plugin is only intended be used on one element at a time.  Further
	 * calls to it will overwrite the existing object, so don't do it.
	 *
	 * @param $el    element the element on which this plugin was called (already
	 *                       jQuery-ized)
	 * @param config object  the config object
	 */
	$.articleFeedback.init = function ( $el, config ) {
		$.articleFeedback.$holder = $el;
		$.articleFeedback.config = config;
		// Has the user already submitted ratings for this page at this revision?
		$.articleFeedback.alreadySubmitted = $.cookie( $.articleFeedback.prefix( 'submitted' ) ) === 'true';
		// Go ahead and load the form
		// When the tool is visible, load the form
		$.articleFeedback.$holder.appear( function () {
			$.articleFeedback.loadForm();
		} );
	};

	// }}}
	// {{{ Utility methods

	/**
	 * Utility method: Prefixes a key for cookies or events with extension and
	 * version information
	 *
	 * @param  key    string name of event to prefix
	 * @return string prefixed event name
	 */
	$.articleFeedback.prefix = function ( key ) {
		var version = mw.config.get( 'wgArticleFeedbackv5Tracking' ).version || 0;
		return 'ext.articleFeedback@' + version + '-' + key;
	};

	/**
	 * Utility method: Get the current bucket
	 *
	 * @return object the bucket
	 */
	$.articleFeedback.currentBucket = function () {
		return $.articleFeedback.buckets[$.articleFeedback.bucketId];
	};

	/**
	 * Utility method: Get the current CTA
	 *
	 * @return object the cta
	 */
	$.articleFeedback.currentCTA = function () {
		return $.articleFeedback.ctas[$.articleFeedback.ctaId];
	};

	// }}}
	// {{{ Form loading methods

	/**
	 * Loads the appropriate form
	 *
	 * The load method uses an ajax request to pull down the bucket ID, the
	 * feedback ID, and using those, build the form.
	 */
	$.articleFeedback.loadForm = function () {
		$.ajax( {
			'url': $.articleFeedback.apiUrl,
			'type': 'GET',
			'dataType': 'json',
			'data': {
				'list': 'articlefeedbackv5',
				'action': 'query',
				'format': 'json',
				'afsubaction': 'newform',
				'afanontoken': $.articleFeedback.userId,
				'afpageid': $.articleFeedback.pageId,
				'afrevid': $.articleFeedback.revisionId
			},
			'success': function ( data ) {
				if ( !( 'form' in data ) || !( 'bucketId' in data.form ) ) {
					mw.log( 'ArticleFeedback invalid response error.' );
					if ( 'error' in data && 'info' in data.error ) {
						console.log(data.error.info);
					} else {
						console.log(data);
					}
					$.articleFeedback.bucketId = 6; // No form
				} else {
					$.articleFeedback.bucketId = data.form.bucketId;
				}
				$.articleFeedback.buildForm( 'form' in data ? data.form.response : null );
			},
			'error': function () {
				mw.log( 'Report loading error' );
				$.articleFeedback.buildForm();
			}
		} );
	};

	/**
	 * Build the form
	 *
	 * @param response object any existing answers
	 */
	$.articleFeedback.buildForm = function ( response ) {
		$.articleFeedback.bucketId = 5; // For now, only use Option 5
		var bucket = $.articleFeedback.currentBucket();
		if ( !( 'buildForm' in bucket ) ) {
			return;
		}
		var $block = bucket.buildForm();
		if ( 'bindEvents' in bucket ) {
			bucket.bindEvents( $block );
		}
		if ( 'fillForm' in bucket ) {
			bucket.fillForm( $block, response );
		}
		$.articleFeedback.$holder
			.html( $block )
			.append( '<div class="articleFeedback-lock"></div>' );
	};

	// }}}
	// {{{ Form submission methods

	/**
	 * Submits the form
	 *
	 * This calls the Article Feedback API method, which stores the user's
	 * responses and returns the name of the CTA to be displayed, if the input
	 * passes local validation.  Local validation is defined by the bucket UI
	 * object.
	 */
	$.articleFeedback.submitForm = function () {

		// For anonymous users, keep a cookie around so we know they've rated before
		if ( mw.user.anonymous() ) {
			$.cookie( $.articleFeedback.prefix( 'rated' ), 'true', { 'expires': 365, 'path': '/' } );
		}

		// Get the form data
		var bucket = $.articleFeedback.currentBucket();
		var formdata = {};
		if ( 'getFormData' in bucket ) {
			formdata = bucket.getFormData();
		}

		// Perform any local validation
		if ( 'localValidation' in bucket ) {
			var error = bucket.localValidation( formdata );
			if ( error ) {
				if ( 'markFormError' in bucket ) {
					bucket.markFormError( error );
				} else {
					alert( error.join( "\n" ) );
				}
				return;
			}
		}

		// If the form locks, lock it
		if ( 'lockForm' in bucket ) {
			bucket.lockForm( false );
		}

		// Send off the ajax request
		$.ajax( {
			'url': $.articleFeedback.apiUrl,
			'type': 'POST',
			'dataType': 'json',
			'data': $.extend( formdata, {
				'action': 'articlefeedbackv5',
				'format': 'json',
				'anontoken': $.articleFeedback.userId,
				'pageid': $.articleFeedback.pageId,
				'revid': $.articleFeedback.revisionId,
				'bucket': $.articleFeedback.bucketId
			} ),
			'success': function( data ) {
				if ( 'error' in data ) {
					if ( 'markFormError' in bucket ) {
						bucket.markFormError( { _api : data.error } );
					} else {
						alert( 'ArticleFeedback: Form submission error : ' + data.error );
					}
				} else {
					if ( 'lockForm' in bucket ) {
						bucket.lockForm( false );
					}
					if ( 'onSuccess' in bucket ) {
						bucket.onSuccess( formdata );
					}
					$.articleFeedback.showCTA();
				}
			},
			'error': function () {
				mw.log( 'Form submission error' );
				alert( 'ArticleFeedback: Form submission error' );
			}
		} );

		// Does the bucket need to do anything else on submit (alongside the
		// ajax request, not as a result of it)?
		if ( 'onSubmit' in bucket ) {
			bucket.onSubmit( formdata );
		}
	};

	// }}}
	// {{{ CTA methods

	/**
	 * Shows a CTA
	 *
	 * @param cta_name string the name of the CTA to display
	 */
	$.articleFeedback.showCTA = function ( ctaId ) {
		$.articleFeedback.ctaId = 1; // For now, just use the edit CTA
		var cta = $.articleFeedback.currentCTA();
		if ( !( 'build' in cta ) ) {
			return;
		}
		var $block = cta.build();
		if ( 'bindEvents' in cta ) {
			cta.bindEvents( $block );
		}
		$.articleFeedback.$holder.html( $block );
	};

	// }}}

// }}}
// {{{ articleFeedback plugin

/**
 * Can be called with an options object like...
 *
 * 	$( ... ).articleFeedback( {
 * 		'ratings': {
 * 			'rating-name': {
 * 				'id': 1, // Numeric identifier of the rating, same as the rating_id value in the db
 * 				'label': 'msg-key-for-label', // String of message key for label
 * 				'tip': 'msg-key-for-tip', // String of message key for tip
 * 			},
 *			// More ratings here...
 * 		}
 * 	} );
 *
 * Rating IDs need to be included in $wgArticleFeedbackv5RatingTypes, which is an array mapping allowed IDs to rating names.
 */
$.fn.articleFeedback = function ( opts ) {
	if ( typeof( opts ) == 'object' ) {
		$.articleFeedback.init( $( this ), opts );
	}
	return $( this );
};

// }}}

} )( jQuery );

