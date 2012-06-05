/**
 * ArticleFeedback form plugin
 *
 * This file creates the plugin that will be used to build the Article Feedback
 * form.  The flow goes like this:
 *
 * User arrives at page -> build appropriate form and trigger link(s)
 *  -> User clicks trigger link -> open form in modal window
 *  -> User scrolls to end of article -> open form below article
 *  -> User submits form -> submit to API
 *      -> has errors -> show errors
 *      -> has no errors -> select random CTA and display
 *
 * This plugin supports a choice of forms, trigger links, and CTAs.  Each form
 * option is called a "bucket" because users are sorted into buckets and each
 * bucket gets a different form option.
 *
 * Right now, these buckets are:
 *   0. No Feedback
 *   	Shows nothing at all.
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
 *   6. Share Your Feedback, 2-step
 *   	Pretty much the same at bucket 1, but split in 2 steps: first presenting
 *      Y/N selection, than asking for textual feedback.
 *
 * The available trigger links are:
 *   A.   After the site tagline (below the article title)
 *   B.   Below the titlebar on the right
 *   C.   Button fixed to right side
 *   D.   Button fixed to bottom right
 *   E.   Same as D, with other colors
 *   F.   Button fixed to left side -- NOT IMPLEMENTED
 *   G.   Button below logo -- NOT IMPLEMENTED
 *   H.   Link on each section bar
 *   TBX. In the toolbox section (always added)
 *
 * The available CTAs are:
 *   0. Just a confirmation notice
 *   1. Edit this page
 *   	Just a big glossy button to send the user to the edit page.
 *   2. Learn More
 *   	Just a big glossy button to tell the user about how the wiki works
 *   	(used if the user doesn't have edit privileges on the article).
 *   3. Take a survey
 *      Asks the user to take an external survey, which will pop up in a new
 *      window.
 *   5. View feedback
 *      Just a big glossy button to send the user to the feedback page.
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

// {{{ articleFeedbackv5 definition

	$.articleFeedbackv5 = {};

	// {{{ Properties

	/**
	 * Are we in debug mode?
	 */
	$.articleFeedbackv5.debug = mw.config.get( 'wgArticleFeedbackv5Debug' ) ? true : false;

	/**
	 * Are we tracking clicks?
	 */
	$.articleFeedbackv5.clickTracking = false;

	/**
	 * Have the containers been added?
	 */
	$.articleFeedbackv5.hasContainers = false;

	/**
	 * Has the form been loaded yet?
	 */
	$.articleFeedbackv5.isLoaded = false;

	/**
	 * Are we currently in a dialog?
	 */
	$.articleFeedbackv5.inDialog = false;

	/**
	 * Is form submission enabled?
	 */
	$.articleFeedbackv5.submissionEnabled = false;

	/**
	 * The bucket ID is the variation of the Article Feedback form chosen for this
	 * particualar user.  It set at load time, but if all else fails, default to
	 * Bucket 6 (no form).
	 *
	 * @see http://www.mediawiki.org/wiki/Article_feedback/Version_5/Feature_Requirements#Feedback_form_interface
	 */
	$.articleFeedbackv5.bucketId = '0';

	/**
	 * The CTA is the view presented to a user who has successfully submitted
	 * feedback.
	 *
	 * @see http://www.mediawiki.org/wiki/Article_feedback/Version_5/Feature_Requirements#Calls_to_Action
	 */
	$.articleFeedbackv5.ctaId = '1';

	/**
	 * The selected trigger links are the ones chosen to be loaded onto the
	 * page. Options are "-" or A-H
	 *
	 * @see $wgArticleFeedbackv5LinkBuckets
	 * @see http://www.mediawiki.org/wiki/Article_feedback/Version_5/Feature_Requirements#Feedback_links_on_article_pages
	 */
	$.articleFeedbackv5.selectedLinks = [];

	/**
	 * The floating link ID indicates the trigger link chosen to be added to the
	 * page, in addition to the toolbox link.  Options are "X" or A-H.
	 *
	 * @see $wgArticleFeedbackv5LinkBuckets
	 * @see http://www.mediawiki.org/wiki/Article_feedback/Version_5/Feature_Requirements#Feedback_links_on_article_pages
	 */
	$.articleFeedbackv5.floatingLinkId = 'X';

	/**
	 * The submitted link ID indicates where the user clicked (or not) to get to
	 * the feedback form.  Options are "X" or A-H
	 *
	 * @see $wgArticleFeedbackv5LinkBuckets
	 * @see http://www.mediawiki.org/wiki/Article_feedback/Version_5/Feature_Requirements#Feedback_links_on_article_pages
	 */
	$.articleFeedbackv5.submittedLinkId = 'X';

	/**
	 * Use the mediawiki util resource's config method to find the correct url to
	 * call for all ajax requests.
	 */
	$.articleFeedbackv5.apiUrl = mw.util.wikiScript( 'api' );

	/**
	 * Is this an anonymous user?
	 */
	$.articleFeedbackv5.anonymous = mw.user.anonymous();

	/**
	 * If not, what's their user id?
	 */
	$.articleFeedbackv5.userId = mw.user.id();

	/**
	 * The page ID
	 */
	$.articleFeedbackv5.pageId = mw.config.get( 'wgArticleId' );

	/**
	 * The revision ID
	 */
	$.articleFeedbackv5.revisionId = mw.config.get( 'wgCurRevisionId' );

	/**
	 * What we're meant to be showing: a form, a CTA, a showstopper error, or nothing
	 */
	$.articleFeedbackv5.toDisplay = 'form';

	/**
	 * What we're actually showing
	 */
	$.articleFeedbackv5.nowShowing = 'none';

	/**
	 * The feedback ID (collected on submit, for use in tracking edits)
	 */
	$.articleFeedbackv5.feedbackId = 0;

	/**
	 * The new feedback's permalink (collected on submit, for use in CTA5)
	 */
	$.articleFeedbackv5.permalink = undefined;

	/**
	 * The link to the special page (collected on submit, for use in CTA5)
	 */
	$.articleFeedbackv5.specialUrl = undefined;

	/**
	 * How many feedback posts per hour a given user should be allowed (site-wide).
	 */
	$.articleFeedbackv5.throttleThresholdPostsPerHour = mw.config.get( 'wgArticleFeedbackv5ThrottleThresholdPostsPerHour' );

	// }}}
	// {{{ Templates

	$.articleFeedbackv5.templates = {

		panelOuter: '\
			<div class="articleFeedbackv5-panel">\
				<div class="articleFeedbackv5-buffer">\
					<div class="articleFeedbackv5-title-wrap">\
						<h2 class="articleFeedbackv5-title"></h2>\
					</div>\
					<div class="articleFeedbackv5-ui">\
						<div class="articleFeedbackv5-tooltip-wrap">\
							<div class="articleFeedbackv5-tooltip">\
								<div class="tooltip-top"></div>\
								<div class="tooltip-repeat">\
									<h3><html:msg key="help-tooltip-title" /></h3><span class="articleFeedbackv5-tooltip-close">X</span>\
									<div class="clear"></div>\
									<p class="articleFeedbackv5-tooltip-info"><html:msg key="help-tooltip-info" /></p>\
									<p><a target="_blank" class="articleFeedbackv5-tooltip-link"><html:msg key="help-tooltip-linktext" />&nbsp;&gt;&gt;</a></p>\
								</div>\
								<div class="tooltip-bottom"></div>\
							</div>\
						</div>\
						<div class="articleFeedbackv5-ui-inner"></div>\
					</div>\
				</div>\
			</div>\
			',

		errorPanel: '<div class="articleFeedbackv5-error-wrap">\
				<div class="articleFeedbackv5-error">\
					<div class="articleFeedbackv5-error-message"></div>\
				</div>\
			</div>\
			',

		helpToolTipTrigger: '<div class="articleFeedbackv5-tooltip-trigger-wrap"><a class="articleFeedbackv5-tooltip-trigger"><html:msg key="help-tooltip-title" /></a></div>',

		ctaTitleConfirm: '\
			<div class="articleFeedbackv5-confirmation-text">\
				<span class="articleFeedbackv5-confirmation-thanks"><html:msg key="cta-thanks" /></span>\
				<span class="articleFeedbackv5-confirmation-follow-up"></span>\
			</div>\
			',

		clear: '<div class="clear"></div>',

		disableFlyover: '\
			<div>\
				<div class="articleFeedbackv5-disable-flyover">\
					<div class="articleFeedbackv5-flyover-header">\
						<h3 id="articleFeedbackv5-noteflyover-caption"><html:msg key="disable-flyover-title" /></h3>\
						<a id="articleFeedbackv5-noteflyover-close" class="articleFeedbackv5-form-flyover-closebutton" href="#"></a>\
					</div>\
					<div class="articleFeedbackv5-form-flyover">\
						<div class="articleFeedbackv5-disable-flyover-help" ></div>\
						<div class="articleFeedbackv5-flyover-footer">\
							<a class="articleFeedbackv5-disable-flyover-button" target="_blank"><html:msg key="disable-flyover-prefbutton" /></a>\
						</div>\
					</div>\
				</div>\
			</div>\
			'

	};

	// }}}
	// {{{ Bucket UI objects

	/**
	 * Set up the buckets' UI objects
	 */
	$.articleFeedbackv5.buckets = {

		// {{{ Bucket 0

		/**
		 * Bucket 0: No form
		 */
		'0': { },

		// }}}
		// {{{ Bucket 1

		/**
		 * Bucket 1: Share Your Feedback
		 */
		'1': {

			/**
			 * Currently displayed placeholder text. This is a workaround for Chrome/FF
			 * automatic focus in overlays.
			 */
			currentDefaultText: '',

			// {{{ templates

			/**
			 * Pull out the markup so it's easy to find
			 */
			templates: {

				/**
				 * The template for the whole block
				 */
				block: '\
					<form>\
						<div class="articleFeedbackv5-top-error"></div>\
						<div class="form-row articleFeedbackv5-bucket1-toggle">\
							<p class="instructions-left"><html:msg key="bucket1-question-toggle" /></p>\
							<div class="buttons">\
								<div class="form-item" rel="yes" id="articleFeedbackv5-bucket1-toggle-wrapper-yes">\
									<label for="articleFeedbackv5-bucket1-toggle-yes"><html:msg key="bucket1-toggle-found-yes-full" /></label>\
									<span class="articleFeedbackv5-button-placeholder"><html:msg key="bucket1-toggle-found-yes" value="yes" /></span>\
									<input type="radio" name="toggle" id="articleFeedbackv5-bucket1-toggle-yes" class="query-button" value="yes" />\
								</div>\
								<div class="form-item" rel="no" id="articleFeedbackv5-bucket1-toggle-wrapper-no">\
									<label for="articleFeedbackv5-bucket1-toggle-no"><html:msg key="bucket1-toggle-found-no-full" /></label>\
									<span class="articleFeedbackv5-button-placeholder"><html:msg key="bucket1-toggle-found-no" /></span>\
									<input type="radio" name="toggle" id="articleFeedbackv5-bucket1-toggle-no" class="query-button last" value="no" />\
								</div>\
								<div class="clear"></div>\
							</div>\
							<div class="clear"></div>\
						</div>\
						<div class="articleFeedbackv5-comment">\
							<textarea id="articleFeedbackv5-find-feedback" class="feedback-text" name="comment"></textarea>\
						</div>\
						<div class="articleFeedbackv5-disclosure">\
							<!-- <p class="articlefeedbackv5-shared-on-feedback"></p> -->\
							<p class="articlefeedbackv5-help-transparency-terms"></p>\
						</div>\
						<button class="articleFeedbackv5-submit" type="submit" disabled="disabled" id="articleFeedbackv5-submit-bttn"><html:msg key="bucket1-form-submit" /></button>\
						<div class="clear"></div>\
					</form>\
					'

			},

			// }}}
			// {{{ getTitle

			/**
			 * Gets the title
			 *
			 * @return string the title
			 */
			getTitle: function () {
				return mw.msg( 'articlefeedbackv5-bucket1-title' );
			},

			// }}}
			// {{{ buildForm

			/**
			 * Builds the empty form
			 *
			 * @return Element the form
			 */
			buildForm: function () {

				// Start up the block to return
				var $block = $( $.articleFeedbackv5.currentBucket().templates.block );

				// Fill in the disclosure text
				$block.find( '.articlefeedbackv5-shared-on-feedback' )
					.html( $.articleFeedbackv5.buildLink(
						'articlefeedbackv5-shared-on-feedback',
						{
							href: mw.config.get( 'wgScript' ) + '?' + $.param( {
								title: mw.config.get( 'wgPageName' ),
								action: 'feedback'
							} ),
							text: 'articlefeedbackv5-shared-on-feedback-linktext',
							target: '_blank'
						} ) );
				$block.find( '.articlefeedbackv5-help-transparency-terms' ).msg( 'articlefeedbackv5-help-transparency-terms' );

				// Turn the submit into a slick button
				$block.find( '.articleFeedbackv5-submit' )
					.button()
					.addClass( 'ui-button-blue' );

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

				// Enable submission and switch out the comment default on toggle selection
				$block.find( '.articleFeedbackv5-button-placeholder' )
					.click( function ( e ) {
						var new_val = $( this ).parent().attr( 'rel' );
						var old_val = ( new_val == 'yes' ? 'no' : 'yes' );
						var $wrap = $.articleFeedbackv5.find( '#articleFeedbackv5-bucket1-toggle-wrapper-' + new_val );
						var $other_wrap = $.articleFeedbackv5.find( '#articleFeedbackv5-bucket1-toggle-wrapper-' + old_val );
						// make the button blue
						$wrap.find( 'span' ).addClass( 'articleFeedbackv5-button-placeholder-active' );
						$other_wrap.find( 'span' ).removeClass( 'articleFeedbackv5-button-placeholder-active' );
						// check/uncheck radio buttons
						$wrap.find( 'input' ).attr( 'checked', 'checked' );
						$other_wrap.find( 'input' ).removeAttr( 'checked' );
						// set default comment message
						var $txt = $.articleFeedbackv5.find( '.articleFeedbackv5-comment textarea' );
						var def_msg_yes = mw.msg( 'articlefeedbackv5-bucket1-question-comment-yes' );
						var def_msg_no = mw.msg( 'articlefeedbackv5-bucket1-question-comment-no' );
						if ( $txt.val() == '' || $txt.val() == def_msg_yes || $txt.val() == def_msg_no ) {
							$txt.val( new_val == 'yes' ? def_msg_yes : def_msg_no );
							$.articleFeedbackv5.currentBucket().currentDefaultText = $txt.val();
						}
						// enable submission
						$.articleFeedbackv5.enableSubmission( true );
					} );

				// Clear out the question on focus
				$block.find( '.articleFeedbackv5-comment textarea' )
					.focus( function () {
						if ( $( this ).val() == $.articleFeedbackv5.currentBucket().currentDefaultText ) {
							$( this ).val( '' );
							$( this ).removeClass( 'inactive' );
						}
					} )
					.keyup ( function () {
						if( $( this ).val().length > 0 ) {
							$.articleFeedbackv5.enableSubmission( true );
						}
					} )
					.blur( function () {
						var def_msg = '';
						var val = $.articleFeedbackv5.find( '.articleFeedbackv5-bucket1-toggle input[checked]' ).val();
						if ( val == 'yes' ) {
							def_msg = mw.msg( 'articlefeedbackv5-bucket1-question-comment-yes' );
						} else if ( val == 'no' ) {
							def_msg = mw.msg( 'articlefeedbackv5-bucket1-question-comment-no' );
						}
						if ( $( this ).val() == '' ) {
							$( this ).val( def_msg );
							$( this ).addClass( 'inactive' );
						} else {
							$.articleFeedbackv5.enableSubmission( true );
						}
					} );

				// Attach the submit
				$block.find( '.articleFeedbackv5-submit' )
					.click( function ( e ) {
						e.preventDefault();
						$.articleFeedbackv5.submitForm();
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
				var $check = $.articleFeedbackv5.find( '.articleFeedbackv5-bucket1-toggle input[checked]' );
				if ( $check.val() == 'yes' ) {
					data.found = 1;
				} else if ( $check.val() == 'no' ) {
					data.found = 0;
				}
				data.comment = $.articleFeedbackv5.find( '.articleFeedbackv5-comment textarea' ).val();
				var def_msg_yes = mw.msg( 'articlefeedbackv5-bucket1-question-comment-yes' );
				var def_msg_no = mw.msg( 'articlefeedbackv5-bucket1-question-comment-no' );
				if ( data.comment == def_msg_yes || data.comment == def_msg_no ) {
					data.comment = '';
				}
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
				if ( ( !( 'comment' in formdata ) || formdata.comment == '' )
					&& !( 'found' in formdata ) ) {
					$.articleFeedbackv5.enableSubmission( false );
					error.nofeedback = mw.msg( 'articlefeedbackv5-error-nofeedback' );
					ok = false;
				}
				return ok ? false : error;
			}

			// }}}

		},

		// }}}
		// {{{ Bucket 4

		/**
		 * Bucket 4: Help Improve This Article
		 */
		'4': {

			// {{{ templates

			/**
			 * Pull out the markup so it's easy to find
			 */
			templates: {

				/**
				 * The template for the whole block, if the user can edit the
				 * article
				 */
				editable: '\
					<div>\
						<div class="form-row articleFeedbackv5-bucket4-toggle">\
							<p class="sub-header"><strong><html:msg key="bucket4-subhead" /></strong></p>\
							<p class="instructions-left"><html:msg key="bucket4-teaser-line1" /><br />\
							<html:msg key="bucket4-teaser-line2" /></p>\
						</div>\
						<div class="articleFeedbackv5-disclosure">\
							<p><a class="articleFeedbackv5-learn-to-edit" target="_blank"><html:msg key="bucket4-learn-to-edit" /> &raquo;</a></p>\
						</div>\
						<a class="articleFeedbackv5-cta-button" id="articleFeedbackv5-submit-bttn"><html:msg key="bucket4-form-submit" /></a>\
						<div class="clear"></div>\
					</div>\
					',

				/**
				 * The template for the whole block, if the user cannot edit the
				 * article
				 */
				noneditable: '\
					<div>\
						<div class="form-row articleFeedbackv5-bucket4-toggle">\
							<p class="instructions-left"><html:msg key="bucket4-noedit-teaser-line1" /><br />\
							<html:msg key="bucket4-noedit-teaser-line2" /></p>\
						</div>\
						<div class="articleFeedbackv5-disclosure">\
							<p>&nbsp;</p>\
						</div>\
						<a class="articleFeedbackv5-cta-button" id="articleFeedbackv5-submit-bttn"><html:msg key="bucket4-noedit-form-submit" /></a>\
						<div class="clear"></div>\
					</div>\
					'

			},

			// }}}
			// {{{ getTitle

			/**
			 * Gets the title
			 *
			 * @return string the title
			 */
			getTitle: function () {
				return mw.msg( $.articleFeedbackv5.editable ? 'articlefeedbackv5-bucket4-title' : 'articlefeedbackv5-bucket4-noedit-title' );
			},

			// }}}
			// {{{ buildForm

			/**
			 * Builds the empty form
			 *
			 * @param from string from whence came the request ("bottom" or "overlay")
			 * @return Element the form
			 */
			buildForm: function ( from ) {

				// Start up the block to return
				var $block = $( $.articleFeedbackv5.editable ? $.articleFeedbackv5.currentBucket().templates.editable : $.articleFeedbackv5.currentBucket().templates.noneditable );

				// Fill in the learn to edit link
				$block.find( '.articleFeedbackv5-learn-to-edit' )
					.attr( 'href', mw.msg( 'articlefeedbackv5-cta1-learn-how-url' ) );

				// Fill in the button link
				var track_id = $.articleFeedbackv5.experiment() + '-button_click-' + from;
				if ( $.articleFeedbackv5.editable ) {
					$block.find( '.articleFeedbackv5-cta-button' )
						.attr( 'href', $.articleFeedbackv5.editUrl( track_id, from ) );
				} else {
					var learn_url = mw.msg( 'articlefeedbackv5-cta1-learn-how-url' );
					$block.find( '.articleFeedbackv5-cta-button' )
						.attr( 'href', $.articleFeedbackv5.trackingUrl( learn_url, track_id ) );
				}

				// Turn the submit into a slick button
				$block.find( '.articleFeedbackv5-cta-button' )
					.button()
					.addClass( 'ui-button-blue' )

				return $block;
			},

			// }}}
			// {{{ afterBuild

			/**
			 * Handles any setup that has to be done once the markup is in the
			 * holder
			 */
			afterBuild: function () {
				// Set a custom message
				$.articleFeedbackv5.$holder
					.add( $.articleFeedbackv5.$dialog)
					.find( '.articleFeedbackv5-tooltip-info' )
					.text( mw.msg( 'articlefeedbackv5-bucket4-help-tooltip-info' ) );
				// Add a class so we can drop the tooltip down a bit for the
				// learn-more version
				if ( !$.articleFeedbackv5.editable ) {
					$.articleFeedbackv5.find( '.articleFeedbackv5-ui' )
						.addClass( 'articleFeedbackv5-option-4-noedit' );
				}
			},

			// }}}
			// {{{ onModalToggle

			/**
			 * Handles any setup that has to be done when the modal window gets
			 * toggled on or off
			 */
			onModalToggle: function ( from ) {
				// Fill in the button link
				if ( $.articleFeedbackv5.editable ) {
					var track_id = $.articleFeedbackv5.experiment() + '-button_click-' + from;
					$.articleFeedbackv5.find( '.articleFeedbackv5-cta-button' )
						.attr( 'href', $.articleFeedbackv5.editUrl( track_id, from ) );
				}
			}

			// }}}

		},

		// }}}
		// {{{ Bucket 6

		/**
		 * Bucket 6: Share Your Feedback, 2-step
		 */
		'6': {

			// {{{ templates

			/**
			 * Pull out the markup so it's easy to find
			 */
			templates: {

				/**
				 * The template for the whole block
				 */
				block: '\
					<form>\
						<div class="articleFeedbackv5-top-error"></div>\
						<div class="form-row articleFeedbackv5-bucket6-toggle">\
							<p class="instructions-left"><html:msg key="bucket6-question-toggle" /></p>\
							<div class="buttons">\
								<div class="form-item" rel="yes" id="articleFeedbackv5-bucket6-toggle-wrapper-yes">\
									<label for="articleFeedbackv5-bucket6-toggle-yes"><html:msg key="bucket6-toggle-found-yes-full" /></label>\
									<span class="articleFeedbackv5-button-placeholder"><html:msg key="bucket6-toggle-found-yes" value="yes" /></span>\
									<input type="radio" name="toggle" id="articleFeedbackv5-bucket6-toggle-yes" class="query-button" value="yes" />\
								</div>\
								<div class="form-item" rel="no" id="articleFeedbackv5-bucket6-toggle-wrapper-no">\
									<label for="articleFeedbackv5-bucket6-toggle-no"><html:msg key="bucket6-toggle-found-no-full" /></label>\
									<span class="articleFeedbackv5-button-placeholder"><html:msg key="bucket6-toggle-found-no" /></span>\
									<input type="radio" name="toggle" id="articleFeedbackv5-bucket6-toggle-no" class="query-button last" value="no" />\
								</div>\
								<div class="clear"></div>\
							</div>\
							<div class="clear"></div>\
						</div>\
						<div class="articleFeedbackv5-comment">\
							<p class="instructions-left" id="articlefeedbackv5-feedback-instructions"></p>\
							<p id="articlefeedbackv5-feedback-countdown"></p>\
							<textarea id="articleFeedbackv5-find-feedback" class="feedback-text" name="comment"></textarea>\
						</div>\
						<div class="articleFeedbackv5-disclosure">\
							<!-- <p class="articlefeedbackv5-shared-on-feedback"></p> -->\
							<p class="articlefeedbackv5-help-transparency-terms"></p>\
						</div>\
						<button class="articleFeedbackv5-submit" type="submit" disabled="disabled" id="articleFeedbackv5-submit-bttn"><html:msg key="bucket6-form-submit" /></button>\
						<div class="clear"></div>\
					</form>\
					'

			},

			// }}}
			// {{{ getTitle

			/**
			 * Gets the title
			 *
			 * @return string the title
			 */
			getTitle: function () {
				return mw.msg( 'articlefeedbackv5-bucket6-title' );
			},

			// }}}
			// {{{ buildForm

			/**
			 * Builds the empty form
			 *
			 * @return Element the form
			 */
			buildForm: function () {

				// Start up the block to return
				var $block = $( $.articleFeedbackv5.currentBucket().templates.block );

				// Fill in the disclosure text
				$block.find( '.articlefeedbackv5-shared-on-feedback' )
					.html( $.articleFeedbackv5.buildLink(
						'articlefeedbackv5-shared-on-feedback',
						{
							href: mw.config.get( 'wgScript' ) + '?' + $.param( {
								title: mw.config.get( 'wgPageName' ),
								action: 'feedback'
							} ),
							text: 'articlefeedbackv5-shared-on-feedback-linktext',
							target: '_blank'
						} ) );
				$block.find( '.articlefeedbackv5-help-transparency-terms' ).msg( 'articlefeedbackv5-help-transparency-terms' );

				// Turn the submit into a slick button
				$block.find( '.articleFeedbackv5-submit' )
					.button()
					.addClass( 'ui-button-blue' );

				// Show only step 1
				$.articleFeedbackv5.currentBucket().displayStep1( $block );

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

				// enable submission and switch out the comment default on toggle selection
				$block.find( '.articleFeedbackv5-button-placeholder' )
					.click( function ( e ) {
						var new_val = $( this ).parent().attr( 'rel' );
						$.articleFeedbackv5.trackClick( $.articleFeedbackv5.experiment() + '-click_' + new_val +
							'-' + ( $.articleFeedbackv5.inDialog ? 'overlay' : 'bottom' ) );

						var $wrap = $.articleFeedbackv5.find( '#articleFeedbackv5-bucket6-toggle-wrapper-' + new_val );

						// move on to step 2
						$.articleFeedbackv5.currentBucket().displayStep2( $block );

						// add instructional text for feedback
						$( '#articlefeedbackv5-feedback-instructions' ).text( mw.msg( 'articlefeedbackv5-bucket6-question-instructions-' + new_val ) );

						// make the button blue
						$( 'span.articleFeedbackv5-button-placeholder-active' ).removeClass( 'articleFeedbackv5-button-placeholder-active' );
						$wrap.find( 'span' ).addClass( 'articleFeedbackv5-button-placeholder-active' );

						// check/uncheck radio buttons
						$wrap.find( 'input' ).trigger( 'click' );

						// set default comment message
						var $element = $.articleFeedbackv5.find( '.articleFeedbackv5-comment textarea' );
						var text = mw.msg( 'articlefeedbackv5-bucket6-question-comment-' + new_val );
						$element.attr( 'placeholder', text ).placeholder();

						// allow feedback submission if there is feedback (or if Y/N was positive)
						var enable = $( '.articleFeedbackv5-comment textarea' ).val().length > 0 || $( '#articleFeedbackv5-bucket6-toggle-yes').is( ':checked' );
						$.articleFeedbackv5.enableSubmission( enable );
					} );

				// add character-countdown on feedback-field
				$( document )
					.on( 'keyup', '.articleFeedbackv5-comment textarea', function () {
						$.articleFeedbackv5.currentBucket().countdown( $( this ) );

						// allow feedback submission if there is feedback (or if Y/N was positive)
						var enable = $( this ).val().length > 0 || $( '#articleFeedbackv5-bucket6-toggle-yes').is( ':checked' );
						$.articleFeedbackv5.enableSubmission( enable );
					} );

				// clicking the back-link on step 2 should show step 1 again
				$( document )
					.on( 'click', '.articleFeedbackv5-arrow-back', function ( e ) {
						e.preventDefault();
						$.articleFeedbackv5.currentBucket().displayStep1( $block );
					} );

				// attach the submit
				$block.find( '.articleFeedbackv5-submit' )
					.click( function ( e ) {
						e.preventDefault();
						$.articleFeedbackv5.submitForm();
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
				var $check = $.articleFeedbackv5.find( '.articleFeedbackv5-bucket6-toggle input[checked]' );
				if ( $check.val() == 'yes' ) {
					data.found = 1;
				} else if ( $check.val() == 'no' ) {
					data.found = 0;
				}
				data.comment = $.articleFeedbackv5.find( '.articleFeedbackv5-comment textarea' ).val();
				var def_msg_yes = mw.msg( 'articlefeedbackv5-bucket6-question-comment-yes' );
				var def_msg_no = mw.msg( 'articlefeedbackv5-bucket6-question-comment-no' );
				if ( data.comment == def_msg_yes || data.comment == def_msg_no ) {
					data.comment = '';
				}
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
				if ( ( !( 'comment' in formdata ) || formdata.comment == '' )
					&& !( 'found' in formdata ) && !$( '#articleFeedbackv5-bucket6-toggle-yes').is( ':checked' ) ) {
					$.articleFeedbackv5.enableSubmission( false );
					error.nofeedback = mw.msg( 'articlefeedbackv5-error-nofeedback' );
					ok = false;
				}
				return ok ? false : error;
			},

			// }}}
			// {{{ displayStep1

			/**
			 * Display step 1
			 */
			displayStep1: function ( $block ) {
				var $step1 = $( '.form-row', $block );
				var $step2 = $( '.articleFeedbackv5-comment, .articleFeedbackv5-disclosure, .articleFeedbackv5-submit', $block );

				// hide comment, disclosure & submit first (should only show after clicking Y/N)
				$step1.show();
				$step2.hide();

				// remove back-arrow from title (if present)
				$( '.articleFeedbackv5-title .articleFeedbackv5-arrow-back' ).remove();
			},

			// }}}
			// {{{ displayStep2

			/**
			 * Display step 2
			 */
			displayStep2: function ( $block ) {
				var $step1 = $( '.form-row', $block );
				var $step2 = $( '.articleFeedbackv5-comment, .articleFeedbackv5-disclosure, .articleFeedbackv5-submit', $block );

				// show comment, disclosure & submit; hide Y/N buttons
				$step2.show();
				$step1.hide();

				// spoof a keyup on the textarea, to init the character countdown
				$( '#articleFeedbackv5-find-feedback' ).trigger( 'keyup' );

				// add back-arrow in front of title
				var $backLink = $( '<a href="#" class="articleFeedbackv5-arrow-back"></a>' );
				$backLink.text( mw.msg( 'articlefeedbackv5-bucket6-backlink-text' ) );
				$backLink.attr( 'title', mw.msg( 'articlefeedbackv5-bucket6-backlink-text' ) );
				$( '.articleFeedbackv5-title' ).prepend( $backLink );
			},

			// }}}
			// {{{ countdown

			/**
			 * Character countdown
			 *
			 * Note: will not do server-side check: this is only used to encourage people to keep their
			 * feedback concise, there's no technical reason not to allow more
			 *
			 * @param $element the form element to count the characters down for
			 */
			countdown: function ( $element ) {
				var maxLength = 5000;

				// grab the current length of the form element (or set to 0 if the current text is bogus placeholder)
				var length = maxLength - $element.val().length;

				// display the amount of characters
				var message = mw.msg( 'articlefeedbackv5-bucket6-feedback-countdown', length );
				$( '#articlefeedbackv5-feedback-countdown' ).text( message );

				// remove excessive characters
				if ( length < 0 ) {
					$element.val( $element.val().substr( 0, maxLength ) );
				}
			}

			// }}}

		},

		// }}}

	};

	// }}}
	// {{{ CTA objects

	/**
	 * Set up the CTA options' UI objects
	 */
	$.articleFeedbackv5.ctas = {

		// {{{ CTA 0: Just a confirmation message

		'0': {

			// {{{ build

			/**
			 * Builds the CTA
			 *
			 * @return Element the form
			 */
			build: function () {
				return $( '<div></div>' );
			},

			// }}}
			// {{{ afterBuild

			/**
			 * Handles any setup that has to be done once the markup is in the
			 * holder
			 */
			afterBuild: function () {
				// Drop the tooltip trigger
				$.articleFeedbackv5.$holder
					.add( $.articleFeedbackv5.$dialog)
					.find( '.articleFeedbackv5-tooltip-trigger' ).hide();
			}

			// }}}

		},

		// }}}
		// {{{ CTA 1: Encticement to edit

		'1': {

			// {{{ templates

			/**
			 * Pull out the markup so it's easy to find
			 */
			templates: {

				/**
				 * The template for the whole block
				 */
				block: '\
					<div class="clear"></div>\
					<div class="articleFeedbackv5-confirmation-panel">\
						<div class="articleFeedbackv5-panel-leftContent">\
							<h3 class="articleFeedbackv5-confirmation-title"><html:msg key="cta1-confirmation-title" /></h3>\
							<p class="articleFeedbackv5-confirmation-wikipediaWorks"><html:msg key="cta1-confirmation-call" /></p>\
							<p class="articleFeedbackv5-confirmation-learnHow"><a target="_blank" href="#"><html:msg key="cta1-learn-how" /> &raquo;</a></p>\
						</div>\
						<a href="&amp;action=edit" class="articleFeedbackv5-cta-button"><span class="ui-button-text"><html:msg key="cta1-edit-linktext" /></span></a>\
						<div class="clear"></div>\
					</div>\
					'

			},

			// }}}
			// {{{ verify

			/**
			 * Verifies that this CTA can be displayed
			 *
			 * @return bool whether the CTA can be displayed
			 */
			verify: function () {
				return $.articleFeedbackv5.editable;
			},

			// }}}
			// {{{ build

			/**
			 * Builds the CTA
			 *
			 * @return Element the form
			 */
			build: function () {

				// Start up the block to return
				var $block = $( $.articleFeedbackv5.currentCTA().templates.block );

				// Fill in the tutorial link
				$block.find( '.articleFeedbackv5-confirmation-learnHow a' )
					.attr( 'href', mw.msg( 'articlefeedbackv5-cta1-learn-how-url' ) );

				// Fill in the link
				var edit_track_id = $.articleFeedbackv5.experiment() + '-' +
					$.articleFeedbackv5.ctaName() + '-button_click-' +
					( $.articleFeedbackv5.inDialog ? 'overlay': 'bottom' );
				$block.find( '.articleFeedbackv5-cta-button' )
					.attr( 'href', $.articleFeedbackv5.editUrl( edit_track_id ) );

				return $block;
			},

			// }}}
			// {{{ afterBuild

			/**
			 * Perform adjustments after build
			 */
			afterBuild: function() {
				$( '.articleFeedbackv5-tooltip-trigger' ).remove();
			}

			// }}}

		},

		// }}}
		// {{{ CTA 2: Learn more

		'2': {

			// {{{ templates

			/**
			 * Pull out the markup so it's easy to find
			 */
			templates: {

				/**
				 * The template for the whole block
				 */
				block: '\
					<div class="clear"></div>\
					<div class="articleFeedbackv5-confirmation-panel">\
						<div class="articleFeedbackv5-panel-leftContent">\
							<h3 class="articleFeedbackv5-confirmation-title"><html:msg key="cta2-confirmation-title" /></h3>\
							<p class="articleFeedbackv5-confirmation-wikipediaWorks"><html:msg key="cta2-confirmation-call" /></p>\
						</div>\
						<a href="&amp;action=edit" class="articleFeedbackv5-cta-button"><span class="ui-button-text"><html:msg key="cta2-button-text" /></span></a>\
						<div class="clear"></div>\
					</div>\
					'

			},

			// }}}
			// {{{ build

			/**
			 * Builds the CTA
			 *
			 * @return Element the form
			 */
			build: function () {

				// Start up the block to return
				var $block = $( $.articleFeedbackv5.currentCTA().templates.block );

				// Fill in the button link
				var learn_url = mw.msg( 'articlefeedbackv5-cta1-learn-how-url' );
				var learn_track_id = $.articleFeedbackv5.experiment() + '-' +
					$.articleFeedbackv5.ctaName() + '-button_click-' +
					( $.articleFeedbackv5.inDialog ? 'overlay': 'bottom' );
				$block.find( '.articleFeedbackv5-cta-button' )
					.attr( 'href', $.articleFeedbackv5.trackingUrl( learn_url, learn_track_id ) );

				return $block;
			},

			// }}}
			// {{{ afterBuild

			/**
			 * Perform adjustments after build
			 */
			afterBuild: function() {
				$( '.articleFeedbackv5-tooltip-trigger' ).remove();
			}

			// }}}

		},

		// }}}
		// {{{ CTA 3: Take a survey

		'3': {

			// {{{ templates

			/**
			 * Pull out the markup so it's easy to find
			 */
			templates: {

				/**
				 * The template for the whole block
				 */
				block: '\
					<div class="clear"></div>\
					<div class="articleFeedbackv5-confirmation-panel">\
						<div class="articleFeedbackv5-panel-leftContent">\
							<h3 class="articleFeedbackv5-confirmation-title"><html:msg key="cta3-confirmation-title" /></h3>\
							<p class="articleFeedbackv5-confirmation-call"><html:msg key="cta3-confirmation-call" /></p>\
						</div>\
						<a href="#" class="articleFeedbackv5-cta-button" target="_blank"><span class="ui-button-text"><html:msg key="cta3-button-text" /></span></a>\
						<div class="clear"></div>\
					</div>\
					'

			},

			// }}}
			// {{{ verify

			/**
			 * Verifies that this CTA can be displayed
			 *
			 * @return bool whether the CTA can be displayed
			 */
			verify: function () {

				return $.articleFeedbackv5.ctas['3'].getSurveyUrl() !== false;
			},

			// }}}
			// {{{ build

			/**
			 * Builds the CTA
			 *
			 * @return Element the form
			 */
			build: function () {

				// Start up the block to return
				var $block = $( $.articleFeedbackv5.currentCTA().templates.block );

				// Fill in the go-to-survey link
				var survey_url = $.articleFeedbackv5.currentCTA().getSurveyUrl();
				if ( survey_url ) {
					var survey_track_id = $.articleFeedbackv5.experiment() + '-' +
						$.articleFeedbackv5.ctaName() + '-button_click-' +
						( $.articleFeedbackv5.inDialog ? 'overlay': 'bottom' );
					$block.find( '.articleFeedbackv5-cta-button' )
						.attr( 'href', $.articleFeedbackv5.trackingUrl(
							survey_url + '?c=' + $.articleFeedbackv5.feedbackId,
							survey_track_id
						) );
				}

				return $block;
			},

			// }}}
			// {{{ getSurveyUrl

			/**
			 * Gets the appropriate survey url, or returns false if none was
			 * found
			 *
			 * @return mixed the url, if one is availabe, or false if not
			 */
			getSurveyUrl: function () {
				var base = mw.config.get( 'wgArticleFeedbackv5SurveyUrls' );
				if ( typeof base != 'object' || !( $.articleFeedbackv5.bucketId in base ) ) {
					return false;
				}
				return base[$.articleFeedbackv5.bucketId];
			},

			// }}}
			// {{{ bindEvents

			/**
			 * Binds any events
			 *
			 * @param $block element the form block
			 */
			bindEvents: function ( $block ) {

				// Make the link work as a popup
				$block.find( '.articleFeedbackv5-cta-button' )
					.click( function ( e ) {
						e.preventDefault();
						var link = $( this ).attr( 'href' );
						var params = 'status=0,\
							toolbar=0,\
							location=0,\
							menubar=0,\
							directories=0,\
							resizable=1,\
							scrollbars=1,\
							height=800,\
							width=600';
						var survey = window.open( link, 'survey', params );
						if ( $.articleFeedbackv5.inDialog ) {
							$.articleFeedbackv5.closeAsModal();
						} else {
							$.articleFeedbackv5.clear();
						}
					} );

			},

			// }}}
			// {{{ afterBuild

			/**
			 * Perform adjustments after build
			 */
			afterBuild: function() {
				$( '.articleFeedbackv5-tooltip-trigger' ).remove();
			}

			// }}}

		},

		// }}}
		// {{{ CTA 5: View feedback

		'5': {

			// {{{ templates

			/**
			 * Pull out the markup so it's easy to find
			 */
			templates: {

				/**
				 * The template for the whole block
				 */
				block: '\
					<div class="clear"></div>\
					<div class="articleFeedbackv5-confirmation-panel">\
						<div class="articleFeedbackv5-panel-leftContent">\
							<h3 class="articleFeedbackv5-confirmation-title"><html:msg key="cta5-confirmation-title" /></h3>\
							<p class="articleFeedbackv5-confirmation-wikipediaWorks"><html:msg key="cta5-confirmation-call" /></p>\
						</div>\
						<a href="#" class="articleFeedbackv5-cta-button"><span class="ui-button-text"><html:msg key="cta5-button-text" /></span></a>\
						<div class="clear"></div>\
					</div>\
					'

			},

			// }}}
			// {{{ build

			/**
			 * Builds the CTA
			 *
			 * @return Element the form
			 */
			build: function () {

				// Start up the block to return
				var $block = $( $.articleFeedbackv5.currentCTA().templates.block );

				// Fill in the link
				var feedback_url = $.articleFeedbackv5.specialUrl;
				var feedback_track_id = 'cta_view_feedback-button_click';
				$block.find( '.articleFeedbackv5-cta-button' )
					.attr( 'href', $.articleFeedbackv5.trackingUrl( feedback_url, feedback_track_id ) );

				return $block;
			},

			// }}}
			// {{{ afterBuild

			/**
			 * Perform adjustments after build
			 */
			afterBuild: function() {
				$( '.articleFeedbackv5-tooltip-trigger' ).remove();
				// Track the impression
				$.articleFeedbackv5.trackClick( $.articleFeedbackv5.experiment() + '-cta_view_feedback-impression' );
			}

			// }}}

		}

		// }}}

	};

	// }}}
	// {{{ Trigger link objects

	/**
	 * Set up the trigger link options
	 */
	$.articleFeedbackv5.triggerLinks = {

		// {{{ A: After the site tagline (below the article title)

		'A': {

			// {{{ templates

			/**
			 * Pull out the markup so it's easy to find
			 */
			templates: {

				/**
				 * The link template, when it does not include a close button
				 */
				basic: '<span><a href="#mw-articleFeedbackv5" id="articleFeedbackv5-sitesublink"></a></span>',

				/**
				 * The link template, when it includes a close button
				 */
				closeable: '\
					<span>\
						<a href="#mw-articleFeedbackv5" id="articleFeedbackv5-sitesublink"></a>\
						<a href="#" class="articleFeedbackv5-close-trigger-link">[X]</a>\
					</span>\
					'

			},

			// }}}
			// {{{ closeable

			/**
			 * Returns whether the link includes a close button
			 *
			 * @return boolean
			 */
			closeable: function () {
				return mw.user.name() != null;
			},

			// }}}
			// {{{ build

			/**
			 * Builds the trigger link
			 *
			 * @return Element the link
			 */
			build: function () {
				var self = $.articleFeedbackv5.triggerLinks['A'];
				var $link = $( self.closeable() ? self.templates.closeable : self.templates.basic )
				$link.find('#articleFeedbackv5-sitesublink')
					.data( 'linkId', 'A' )
					.text( mw.msg( 'articlefeedbackv5-sitesub-linktext' ) )
					.click( function ( e ) {
						e.preventDefault();
						$.articleFeedbackv5.clickTriggerLink( $( e.target ) );
					} );
				return $link;
			},

			// }}}
			// {{{ insert

			/**
			 * Inserts the link into the page
			 *
			 * @param Element $link the link
			 */
			insert: function ( $link ) {
				// The link is going to be at different markup locations on different skins,
				// and it needs to show up if the site subhead (e.g., "From Wikipedia, the free
				// encyclopedia") is not visible for any reason.
				if ( $( '#siteSub' ).filter( ':visible' ).length ) {
					$link.prepend( ' &nbsp; ' + mw.msg('pipe-separator') + ' &nbsp; ' );
					$( '#siteSub' ).append( $link );
				} else if ( $( 'h1.pagetitle + p.subtitle' ).filter( ':visible' ).length ) {
					$link.prepend( ' &nbsp; ' + mw.msg('pipe-separator') + ' &nbsp; ' );
					$( 'h1.pagetitle + p.subtitle' ).append( $link );
				} else if ( $( '#mw_contentholder .mw-topboxes' ).length ) {
					$( '#mw_contentholder .mw-topboxes' ).after( $link );
				} else if ( $( '#bodyContent' ).length ) {
					$( '#bodyContent' ).prepend( $link );
				}
			}

			// }}}

		},

		// }}}
		// {{{ B: Below the titlebar on the right

		'B': {

			// {{{ closeable

			/**
			 * Returns whether the link includes a close button
			 *
			 * @return boolean
			 */
			closeable: function () {
				return false;
			},

			// }}}
			// {{{ build

			/**
			 * Builds the trigger link
			 *
			 * @return Element the link
			 */
			build: function () {
				var $link = $( '<a href="#mw-articleFeedbackv5" id="articleFeedbackv5-titlebarlink"></a>' )
					.data( 'linkId', 'B' )
					.text( mw.msg( 'articlefeedbackv5-titlebar-linktext' ) )
					.click( function ( e ) {
						e.preventDefault();
						$.articleFeedbackv5.clickTriggerLink( $( e.target ) );
					} );
				if ( $( '#coordinates' ).length ) {
					$link.css( 'margin-top: 2.5em' );
				}
				return $link;
			}

			// }}}

		},

		// }}}
		// {{{ C: Button fixed to right side

		'C': {

			// {{{ templates

			/**
			 * Pull out the markup so it's easy to find
			 */
			templates: {

				/**
				 * The link template
				 */
				block: '\
					<div id="articleFeedbackv5-fixedtab" class="articleFeedbackv5-fixedtab">\
						<div id="articleFeedbackv5-fixedtabbox" class="articleFeedbackv5-fixedtabbox">\
							<a href="#mw-articleFeedbackv5" id="articleFeedbackv5-fixedtablink" class="articleFeedbackv5-fixedtablink"></a>\
						</div>\
					</div>'

			},

			// }}}
			// {{{ closeable

			/**
			 * Returns whether the link includes a close button
			 *
			 * @return boolean
			 */
			closeable: function () {
				return false;
			},

			// }}}
			// {{{ build

			/**
			 * Builds the trigger link
			 *
			 * @return Element the link
			 */
			build: function () {
				var $link = $( $.articleFeedbackv5.triggerLinks['C'].templates.block );
				$link.find( '#articleFeedbackv5-fixedtablink' )
					.data( 'linkId', 'C' )
					.attr( 'title', mw.msg( 'articlefeedbackv5-fixedtab-linktext' ) )
					.click( function( e ) {
						e.preventDefault();
						$.articleFeedbackv5.clickTriggerLink( $( e.target ) );
					} );
				return $link;
			}

			// }}}

		},

		// }}}
		// {{{ D: Button fixed to bottom right

		'D': {

			// {{{ templates

			/**
			 * Pull out the markup so it's easy to find
			 */
			templates: {

				/**
				 * The link template
				 */
				block: '\
					<div id="articleFeedbackv5-bottomrighttab" class="articleFeedbackv5-bottomrighttab">\
						<div id="articleFeedbackv5-bottomrighttabbox" class="articleFeedbackv5-bottomrighttabbox">\
							<a href="#mw-articleFeedbackv5" id="articleFeedbackv5-bottomrighttablink" class="articleFeedbackv5-bottomrighttablink"></a>\
						</div>\
					</div>'

			},

			// }}}
			// {{{ closeable

			/**
			 * Returns whether the link includes a close button
			 *
			 * @return boolean
			 */
			closeable: function () {
				return false;
			},

			// }}}
			// {{{ build

			/**
			 * Builds the trigger link
			 *
			 * @return Element the link
			 */
			build: function () {
				var $link = $( $.articleFeedbackv5.triggerLinks['D'].templates.block );
				$link.find( '#articleFeedbackv5-bottomrighttablink' )
					.data( 'linkId', 'D' )
					.text( mw.msg( 'articlefeedbackv5-bottomrighttab-linktext' ) )
					.click( function( e ) {
						e.preventDefault();
						$.articleFeedbackv5.clickTriggerLink( $( e.target ) );
					} );
				return $link;
			}

			// }}}

		},

		// }}}
		// {{{ E: Same as D, with other colors

		'E': {

			// {{{ templates

			/**
			 * Pull out the markup so it's easy to find
			 */
			templates: {

				/**
				 * The link template, when it does not include a close button
				 */
				basic: '\
					<div id="articleFeedbackv5-bottomrighttab" class="articleFeedbackv5-bottomrighttab">\
						<div id="articleFeedbackv5-bottomrighttabbox" class="articleFeedbackv5-bottomrighttabbox">\
							<div class="articleFeedbackv5-bottomrighttablink">\
								<a href="#mw-articleFeedbackv5" id="articleFeedbackv5-bottomrighttablink"></a>\
							</div>\
						</div>\
					</div>\
					',

				/**
				 * The link template, when it includes a close button
				 */
				closeable: '\
					<div id="articleFeedbackv5-bottomrighttab" class="articleFeedbackv5-bottomrighttab articleFeedbackv5-trigger-link">\
						<div id="articleFeedbackv5-bottomrighttabbox" class="articleFeedbackv5-bottomrighttabbox">\
							<div class="articleFeedbackv5-bottomrighttablink articleFeedbackv5-closeable">\
								<a href="#mw-articleFeedbackv5" id="articleFeedbackv5-bottomrighttablink"></a>\
								<a href="#" class="articleFeedbackv5-close-trigger-link"></a>\
							</div>\
						</div>\
					</div>\
					'

			},

			// }}}
			// {{{ closeable

			/**
			 * Returns whether the link includes a close button
			 *
			 * @return boolean
			 */
			closeable: function () {
				return mw.user.name() != null;
			},

			// }}}
			// {{{ build

			/**
			 * Builds the trigger link
			 *
			 * @return Element the link
			 */
			build: function () {
				var self = $.articleFeedbackv5.triggerLinks['E'];
				var $link = $( self.closeable() ? self.templates.closeable : self.templates.basic );
				$link.find( '#articleFeedbackv5-bottomrighttablink' )
					.data( 'linkId', 'E' )
					.text( mw.msg( 'articlefeedbackv5-bottomrighttab-linktext' ) )
					.click( function( e ) {
						e.preventDefault();
						$.articleFeedbackv5.clickTriggerLink( $( e.target ) );
					} );
				return $link;
			}

			// }}}

		},

		// }}}
		// {{{ F: Button fixed to left side -- NOT IMPLEMENTED

		'F': {

			// {{{ closeable

			/**
			 * Returns whether the link includes a close button
			 *
			 * @return boolean
			 */
			closeable: function () {
				return false;
			},

			// }}}
			// {{{ build

			/**
			 * Builds the trigger link
			 *
			 * @return Element the link
			 */
			build: function () {
			}

			// }}}

		},

		// }}}
		// {{{ G: Button below logo -- NOT IMPLEMENTED

		'G': {

			// {{{ closeable

			/**
			 * Returns whether the link includes a close button
			 *
			 * @return boolean
			 */
			closeable: function () {
				return false;
			},

			// }}}
			// {{{ build

			/**
			 * Builds the trigger link
			 *
			 * @return Element the link
			 */
			build: function () {
			}

			// }}}

		},

		// }}}
		// {{{ H: Link on each section bar

		'H': {

			// {{{ closeable

			/**
			 * Returns whether the link includes a close button
			 *
			 * @return boolean
			 */
			closeable: function () {
				return false;
			},

			// }}}
			// {{{ build

			/**
			 * Builds the trigger link
			 *
			 * @return Element the link
			 */
			build: function () {
				var $wrap = $( '<span class="articleFeedbackv5-sectionlink-wrap"></span>' )
					.html( '&nbsp;[<a href="#mw-articlefeedbackv5" class="articleFeedbackv5-sectionlink"></a>]' );
				$wrap.find( 'a.articleFeedbackv5-sectionlink' )
					.data( 'linkId', 'H' )
					.text( mw.msg( 'articlefeedbackv5-section-linktext' ) )
					.click( function ( e ) {
						e.preventDefault();
						$.articleFeedbackv5.clickTriggerLink( $( e.target ) );
					} );
				return $wrap;
			},

			// }}}
			// {{{ insert

			/**
			 * Inserts the link into the page
			 *
			 * @param Element $link the link
			 */
			insert: function ( $link ) {
				$( 'span.editsection' ).append( $link );
			}

			// }}}

		},

		// }}}
		// {{{ TBX: In the toolbox section (always added)

		'TBX': {

			// {{{ closeable

			/**
			 * Returns whether the link includes a close button
			 *
			 * @return boolean
			 */
			closeable: function () {
				return false;
			},

			// }}}
			// {{{ build

			/**
			 * Builds the trigger link
			 *
			 * @return Element the link
			 */
			build: function () {
				var $link = $( '<li id="t-articlefeedbackv5"><a href="#mw-articlefeedbackv5"></a></li>' );
				$link.find( 'a' ).text( mw.msg( 'articlefeedbackv5-toolbox-linktext' ) );
				if ( '5' == $.articleFeedbackv5.bucketId ) {
					$link.find( 'a' )
						.click( function ( e ) {
							// Just set the link ID -- this should act just like AFTv4
							$.articleFeedbackv5.setLinkId( 'TBX' );
						} );
				} else {
					$link.find( 'a' )
						.data( 'linkId', 'TBX' )
						.click( function ( e ) {
							e.preventDefault();
							$.articleFeedbackv5.clickTriggerLink( $( e.target ) );
						} );
				}
				return $link;
			},

			// }}}
			// {{{ insert

			/**
			 * Inserts the link into the page
			 *
			 * @param Element $link the link
			 */
			insert: function ( $link ) {
				$( '#p-tb' ).find( 'ul' ).append( $link );
			}

			// }}}

		}

		// }}}

	};

	// }}}
	// {{{ Initialization

	// {{{ init

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
	$.articleFeedbackv5.init = function ( $el, config ) {
		$.articleFeedbackv5.$holder = $el;
		$.articleFeedbackv5.config = config;
		// Debug mode
		var reqDebug = mw.util.getParamValue( 'debug' );
		if ( reqDebug ) {
			$.articleFeedbackv5.debug = reqDebug == 'false' ? false : true;
		}
		// Are we tracking clicks?
		$.articleFeedbackv5.clickTracking = $.articleFeedbackv5.checkClickTracking();
		// Has the user already submitted ratings for this page at this revision?
		$.articleFeedbackv5.alreadySubmitted = $.cookie( $.articleFeedbackv5.prefix( 'submitted' ) ) === 'true';
		// Can the user edit the page?
		$.articleFeedbackv5.editable = $.articleFeedbackv5.userCanEdit();
		// Go ahead and bucket right away
		$.articleFeedbackv5.selectBucket();
		// Select the trigger link(s)
		$.articleFeedbackv5.selectTriggerLinks();
		// Anything the bucket needs to do?
		if ( 'init' in $.articleFeedbackv5.currentBucket() ) {
			$.articleFeedbackv5.currentBucket().init();
		}
		// When the tool is visible, load the form
		$.articleFeedbackv5.$holder.appear( function () {
			if ( !$.articleFeedbackv5.isLoaded ) {
				$.articleFeedbackv5.load( 'auto', 'bottom' );
				$.articleFeedbackv5.trackClick( $.articleFeedbackv5.experiment() + '-impression-bottom' );
			}
		} );
		// Keep track of links that must be removed after a successful submission
		$.articleFeedbackv5.$toRemove = $( [] );
		// Add them
		$.articleFeedbackv5.addTriggerLinks();
		// Track init at 1%
		if ( Math.random() * 100 < 1 ) {
			$.articleFeedbackv5.trackClick( $.articleFeedbackv5.experiment() + '-init' );
		}
	};

	// }}}
	// {{{ selectBucket

	/**
	 * Chooses a bucket
	 *
	 * If the plugin is in debug mode, you'll be able to pass in a particular
	 * bucket in the url.  Otherwise, it will use the core bucketing
	 * (configuration for this module passed in) to choose a bucket.
	 */
	$.articleFeedbackv5.selectBucket = function () {
		// Find out which display bucket they go in:
		// 1. Requested in query string (debug only)
		// 2. Core bucketing
		var knownBuckets = { '0': true, '1': true, '4': true, '6': true };
		var requested = mw.util.getParamValue( 'aftv5_form' );
		var cfg = mw.config.get( 'wgArticleFeedbackv5DisplayBuckets' );
		if ( requested in knownBuckets ) {
			$.articleFeedbackv5.bucketId = requested;
		} else {
			var key = 'ext.articleFeedbackv5@' + cfg.version + '-form'
			var bucketName = mw.user.bucket( key, cfg );
			var nameMap = { zero: '0', one: '1', four: '4', six: '6' };
			$.articleFeedbackv5.bucketId = nameMap[bucketName];
		}
		if ( $.articleFeedbackv5.debug ) {
			aft5_debug( 'Using form option #' + $.articleFeedbackv5.bucketId );
		}
	};

	// }}}
	// {{{ checkClickTracking

	/**
	 * Checks whether click tracking is turned on
	 *
	 * Only track users who have been assigned to the tracking group; don't bucket
	 * at all if we're set to always ignore or always track.
	 */
	$.articleFeedbackv5.checkClickTracking = function () {
		var b = mw.config.get( 'wgArticleFeedbackv5Tracking' );
		if ( b.buckets.ignore == 100 && b.buckets.track == 0 ) {
			return false;
		}
		if ( b.buckets.ignore == 0 && b.buckets.track == 100 ) {
			return true;
		}
		var key = 'ext.articleFeedbackv5@' + b.version + '-tracking'
		return ( 'track' === mw.user.bucket( key, b ) );
	};

	// }}}
	// {{{ selectTriggerLinks

	/**
	 * Chooses the trigger link(s) to add
	 *
	 * If the plugin is in debug mode, you'll be able to pass in a particular
	 * link in the url.  Otherwise, it will use the core bucketing
	 * (configuration for this module passed in) to choose a trigger link.
	 */
	$.articleFeedbackv5.selectTriggerLinks = function () {
		// The bucketed link:
		//   1. Display bucket 0 or 4-not-editable?  Always no link.
		//   2. Requested in query string (debug only)
		//   3. Random bucketing
		var bucketedLink = 'X';
		if ( ! ( '0' == $.articleFeedbackv5.bucketId
			|| ( '4' == $.articleFeedbackv5.bucketId && !$.articleFeedbackv5.editable ) ) ) {
			var cfg = mw.config.get( 'wgArticleFeedbackv5LinkBuckets' );
			if ( 'buckets' in cfg ) {
				var knownBuckets = cfg.buckets;
				var requested = mw.util.getParamValue( 'aftv5_link' );
				if ( requested in knownBuckets || requested == 'X' ) {
					bucketedLink = requested;
				} else {
					var key = 'ext.articleFeedbackv5@' + cfg.version + '-links'
					bucketedLink = mw.user.bucket( key, cfg );
				}
			}
		}
		if ( $.articleFeedbackv5.debug ) {
			aft5_debug( 'Using link option ' + bucketedLink );
		}
		$.articleFeedbackv5.floatingLinkId = bucketedLink;
		if ('X' != bucketedLink) {
			$.articleFeedbackv5.selectedLinks.push(bucketedLink);
		}
		// Always add the toolbox link
		$.articleFeedbackv5.selectedLinks.push('TBX');
	};

	// }}}
	// {{{ userCanEdit

	/**
	 * Returns whether the user can edit the article
	 */
	$.articleFeedbackv5.userCanEdit = function () {
		// An empty restrictions array means anyone can edit
		var restrictions =  mw.config.get( 'wgRestrictionEdit', [] );
		if ( restrictions.length ) {
			var groups =  mw.config.get( 'wgUserGroups' );
			// Verify that each restriction exists in the user's groups
			for ( var i = 0; i < restrictions.length; i++ ) {
				if ( $.inArray( restrictions[i], groups ) < 0 ) {
					return false;
				}
			}
		}
		return true;
	};

	// }}}

	// }}}
	// {{{ Utility methods

	// {{{ prefix

	/**
	 * Utility method: Prefixes a key for cookies or events with extension and
	 * version information
	 *
	 * @param  key    string name of event to prefix
	 * @return string prefixed event name
	 */
	$.articleFeedbackv5.prefix = function ( key ) {
		var version = mw.config.get( 'wgArticleFeedbackv5Tracking' ).version || 0;
		return 'ext.articleFeedbackv5@' + version + '-' + key;
	};

	// }}}
	// {{{ currentBucket

	/**
	 * Utility method: Get the current bucket
	 *
	 * @return object the bucket
	 */
	$.articleFeedbackv5.currentBucket = function () {
		return $.articleFeedbackv5.buckets[$.articleFeedbackv5.bucketId];
	};

	// }}}
	// {{{ currentCTA

	/**
	 * Utility method: Get the current CTA
	 *
	 * @return object the cta
	 */
	$.articleFeedbackv5.currentCTA = function () {
		return $.articleFeedbackv5.ctas[$.articleFeedbackv5.ctaId];
	};

	// }}}
	// {{{ buildLink

	/**
	 * Utility method: Build a link from a href and message keys for the full
	 * text (with $1 where the link goes) and link text
	 *
	 * Can't use .text() with mw.message(, \/* $1 *\/ link).toString(),
	 * because 'link' should not be re-escaped (which would happen if done by mw.message)
	 *
	 * @param  string fulltext the message key for the full text
	 * @param  object link1    the first link, as { href: '#', text: 'click here' }
	 * @param  object link2    [optional] the second link, as above
	 * @param  object link2    [optional] the third link, as above
	 * @return string the html
	 */
	$.articleFeedbackv5.buildLink = function ( fulltext, link1, link2, link3 ) {
		var full = mw.html.escape( mw.msg( fulltext ) );
		var args = arguments;
		return full.replace( /\$(\d+)/g, function( str, number ) {
			var i = parseInt( number, 10 );
			var sub = args[i];
			var replacement = '';
			if ( sub.tag == 'quotes' ) {
				replacement = '&quot;' + mw.msg( sub.text ) + '&quot';
			} else {
				replacement = mw.html.element(
					'tag' in sub ? sub.tag : 'a',
					$.articleFeedbackv5.attribs( sub ),
					mw.msg( sub.text )
				).toString();
			}
			return replacement;
		} );
	};

	// }}}
	// {{{ attribs

	/**
	 * Utility method: Set up the attributes for a link (works with
	 * buildLink())
	 *
	 * @param  object link the first link, as { href: '#', text: 'click here'.
	 *                     other-attrib: 'whatever'}
	 * @return object the attributes
	 */
	$.articleFeedbackv5.attribs = function ( link ) {
		var attr = {};
		for ( var k in link ) {
			if ( 'text' != k && 'tag' != k ) {
				attr[k] = link[k];
			}
		}
		return attr;
	};

	// }}}
	// {{{ enableSubmission

	/**
	 * Utility method: Enables or disables submission of the form
	 *
	 * @param state bool true to enable; false to disable
	 */
	$.articleFeedbackv5.enableSubmission = function ( state ) {
		// this is actually required to resolve jQuery behavior of not triggering the
		// click event when .blur() occurs on the textarea and .click() is supposed to
		// be triggered on the button.
		if($.articleFeedbackv5.submissionEnabled == state ) {
			return;
		}

		if ( state ) {
			$.articleFeedbackv5.find( '.articleFeedbackv5-submit' ).button( 'enable' );
		} else {
			$.articleFeedbackv5.find( '.articleFeedbackv5-submit' ).button( 'disable' );
		}
		var bucket = $.articleFeedbackv5.currentBucket();
		if ( 'enableSubmission' in bucket ) {
			bucket.enableSubmission( state );
		}
		$.articleFeedbackv5.submissionEnabled = state;
		$( '#articleFeedbackv5-submit-bttn span' ).text( mw.msg( 'articlefeedbackv5-bucket1-form-submit' ) );
		$( '#articleFeedbackv5-submit-bttn5 span' ).text( mw.msg( 'articlefeedbackv5-bucket5-form-panel-submit' ) );
	};

	// }}}
	// {{{ find

	/**
	 * Utility method: Find an element, whether it's in the dialog or not
	 *
	 * @param  query mixed what to pass to the appropriate jquery element
	 * @return array the list of elements found
	 */
	$.articleFeedbackv5.find = function ( query ) {
		if ( $.articleFeedbackv5.inDialog ) {
			return $.articleFeedbackv5.$dialog.find( query );
		} else {
			return $.articleFeedbackv5.$holder.find( query );
		}
	};

	// }}}
	// {{{ experiment

	/**
	 * Utility method: Gets the name of the current experiment
	 *
	 * @return string the experiment (e.g. "optionM5_1_edit")
	 */
	$.articleFeedbackv5.experiment = function () {
		return 'optionM5_' + $.articleFeedbackv5.bucketId;
	};

	// }}}
	// {{{ ctaName

	/**
	 * Utility method: Gets the name of the current CTA
	 *
	 * @return string the CTA name
	 */
	$.articleFeedbackv5.ctaName = function () {
		if ( '0' == $.articleFeedbackv5.ctaId ) {
			return 'cta_none';
		} else if ( '1' == $.articleFeedbackv5.ctaId ) {
			return 'cta_edit';
		} else if ( '2' == $.articleFeedbackv5.ctaId ) {
			return 'cta_learn_more';
		} else if ( '3' == $.articleFeedbackv5.ctaId ) {
			return 'cta_survey';
		} else if ( '5' == $.articleFeedbackv5.ctaId ) {
			return 'cta_feedback';
		} else {
			return 'cta_unknown';
		}
	};

	// }}}
	// {{{ trackingUrl

	/**
	 * Creates a URL that tracks a particular click
	 *
	 * @param url        string the url so far
	 * @param trackingId string the tracking ID
	 */
	$.articleFeedbackv5.trackingUrl = function ( url, trackingId ) {
		if ( $.articleFeedbackv5.clickTracking ) {
			return $.articleFeedbackv5.trackActionURL( url, $.articleFeedbackv5.prefix( trackingId ) );
		} else {
			return url;
		}
	};

	// }}}
	// {{{ editUrl

	/**
	 * Builds the edit URL, with tracking if appropriate
	 *
	 * @param trackingId string the tracking ID
	 * @param from string from whence came the request ("bottom" or "overlay"),
	 *                    since the build process happens before inDialog gets set
	 */
	$.articleFeedbackv5.editUrl = function ( trackingId, from ) {
		var params = {
			'title': mw.config.get( 'wgPageName' ),
			'action': 'edit',
			'articleFeedbackv5_click_tracking': $.articleFeedbackv5.clickTracking ? '1' : '0',
		};
		if ( $.articleFeedbackv5.clickTracking ) {
			params.articleFeedbackv5_ct_token   = $.cookie( 'clicktracking-session' );
			params.articleFeedbackv5_bucket_id  = $.articleFeedbackv5.bucketId;
			params.articleFeedbackv5_cta_id     = $.articleFeedbackv5.ctaId;
			params.articleFeedbackv5_link_id    = $.articleFeedbackv5.submittedLinkId;
			params.articleFeedbackv5_f_link_id  = $.articleFeedbackv5.floatingLinkId;
			params.articleFeedbackv5_experiment = $.articleFeedbackv5.experiment();
			if ( from ) {
				params.articleFeedbackv5_location = from;
			} else {
				params.articleFeedbackv5_location = $.articleFeedbackv5.inDialog ? 'overlay' : 'bottom';
			}
		}
		var url = mw.config.get( 'wgScript' ) + '?' + $.param( params );
		if ( trackingId ) {
			return $.articleFeedbackv5.trackingUrl( url, trackingId );
		} else {
			return url;
		}
	};

	// }}}

	// }}}
	// {{{ Process methods

	// {{{ load

	/**
	 * Loads the tool onto the page
	 *
	 * @param display string what to load ("form", "cta", or "auto")
	 * @param from    string from whence came the request ("bottom" or "overlay")
	 */
	$.articleFeedbackv5.load = function ( display, from ) {

		if ( display && 'auto' != display ) {
			$.articleFeedbackv5.toDisplay = ( display == 'cta' ? 'cta' : 'form' );
		}

		$.articleFeedbackv5.clearContainers();
		$.articleFeedbackv5.nowShowing = 'none';

		if ( 'form' == $.articleFeedbackv5.toDisplay ) {
			var bucket = $.articleFeedbackv5.currentBucket();
			if ( !( 'buildForm' in bucket ) ) {
				$.articleFeedbackv5.isLoaded = true;
				return;
			}
			$.articleFeedbackv5.loadContainers();
			$.articleFeedbackv5.showForm( from );
		}

		else if ( 'cta' == $.articleFeedbackv5.toDisplay ) {
			var cta = $.articleFeedbackv5.currentCTA();
			if ( !( 'build' in cta ) ) {
				$.articleFeedbackv5.isLoaded = true;
				return;
			}
			$.articleFeedbackv5.loadContainers();
			$.articleFeedbackv5.showCTA( from );
		}

		$.articleFeedbackv5.isLoaded = true;
	};

	// }}}
	// {{{ loadContainers

	/**
	 * Builds containers and loads them onto the page
	 */
	$.articleFeedbackv5.loadContainers = function () {

		// Set up the panel
		var $wrapper = $( $.articleFeedbackv5.templates.panelOuter );

		// Add the help tooltip
		$wrapper.find( '.articleFeedbackv5-tooltip-link' )
			.click( function ( e ) {
				e.preventDefault();
				window.open( $( e.target ).attr( 'href' ) );
			} );
		$wrapper.find( '.articleFeedbackv5-tooltip-close' ).click( function () {
			$.articleFeedbackv5.find( '.articleFeedbackv5-tooltip' ).toggle();
		} );
		$wrapper.find( '.articleFeedbackv5-tooltip' ).hide();

		// Set up the tooltip trigger for the panel version
		$wrapper.find( '.articleFeedbackv5-title-wrap' ).append( $.articleFeedbackv5.templates.helpToolTipTrigger );
		$wrapper.find( '.articleFeedbackv5-tooltip-trigger' ).click( function () {
			$.articleFeedbackv5.find( '.articleFeedbackv5-tooltip' ).toggle();
		} );

		// Localize
		$wrapper.localize( { 'prefix': 'articlefeedbackv5-' } );

		// Add it to the page
		$.articleFeedbackv5.$holder
			.html( $wrapper )
			.addClass( 'articleFeedbackv5' )
			.append( $( $.articleFeedbackv5.templates.errorPanel ) )
			.append( '<div class="articleFeedbackv5-lock"></div>' );

		// Add an empty dialog
		$.articleFeedbackv5.$dialog = $( '<div id="articleFeedbackv5-dialog-wrap"></div>' );
		$.articleFeedbackv5.$holder.after( $.articleFeedbackv5.$dialog );

		// Set up the dialog
		$.articleFeedbackv5.$dialog.dialog( {
			width: 500,
			height: 300,
			dialogClass: 'articleFeedbackv5-dialog',
			resizable: true,
			draggable: true,
			title: $.articleFeedbackv5.currentBucket().getTitle(),
			modal: true,
			autoOpen: false,
			close: function ( event, ui ) {
				$.articleFeedbackv5.closeAsModal();
			}
		} );
		var $title = $( '#ui-dialog-title-articleFeedbackv5-dialog-wrap' );
		var $titlebar = $title.parent();
		$title.addClass( 'articleFeedbackv5-title' );

		// Set up the tooltip trigger for the dialoag
		$titlebar.append( $.articleFeedbackv5.templates.helpToolTipTrigger );
		$titlebar.find( '.articleFeedbackv5-tooltip-trigger' ).click( function ( e ) {
			$.articleFeedbackv5.find( '.articleFeedbackv5-tooltip' ).toggle();
		} );
		$titlebar.localize( { 'prefix': 'articlefeedbackv5-' } );

		// Mark that we have containers
		$.articleFeedbackv5.hasContainers = true;
	};

	// }}}
	// {{{ showForm

	/**
	 * Builds the form and loads it into the document
	 *
	 * @param from string from whence came the request ("bottom" or "overlay")
	 */
	$.articleFeedbackv5.showForm = function ( from ) {

		// Build the form
		var bucket = $.articleFeedbackv5.currentBucket();
		var $block = bucket.buildForm( from );
		if ( 'bindEvents' in bucket ) {
			bucket.bindEvents( $block );
		}
		$block.localize( { 'prefix': 'articlefeedbackv5-' } );

		// Add it to the appropriate container
		$.articleFeedbackv5.find( '.articleFeedbackv5-ui-inner' )
			.append( $block );

		// Set the appropriate class on the ui block
		$.articleFeedbackv5.find( '.articleFeedbackv5-ui' )
			.addClass( 'articleFeedbackv5-option-' + $.articleFeedbackv5.bucketId )
			.removeClass( 'articleFeedbackv5-cta-' + $.articleFeedbackv5.ctaId );

		// Set the title
		if ( 'getTitle' in bucket ) {
			$.articleFeedbackv5.$holder.find( '.articleFeedbackv5-title' ).html( bucket.getTitle() );
			$.articleFeedbackv5.$dialog.dialog( 'option', 'title', bucket.getTitle() );
		}

		// Link to help is dependent on the group the user belongs to
		var helpLink = mw.msg( 'articlefeedbackv5-help-tooltip-linkurl' );
		if ( mw.config.get( 'wgArticleFeedbackv5Permissions' )['oversighter'] ) {
			helpLink = mw.msg( 'articlefeedbackv5-help-tooltip-linkurl-oversighters' );
		} else if ( mw.config.get( 'wgArticleFeedbackv5Permissions' )['monitor'] ) {
			helpLink = mw.msg( 'articlefeedbackv5-help-tooltip-linkurl-monitors' );
		} else if ( mw.config.get( 'wgArticleFeedbackv5Permissions' )['editor'] ) {
			helpLink = mw.msg( 'articlefeedbackv5-help-tooltip-linkurl-editors' );
		}

		// Set the tooltip link
		$.articleFeedbackv5.find( '.articleFeedbackv5-tooltip-link' )
			.attr( 'href', helpLink );

		// Do anything special the bucket requires
		if ( 'afterBuild' in bucket ) {
			bucket.afterBuild();
		}

		$.articleFeedbackv5.nowShowing = 'form';
	};

	// }}}
	// {{{ submitForm

	/**
	 * Submits the form
	 *
	 * This calls the Article Feedback API method, which stores the user's
	 * responses and returns the name of the CTA to be displayed, if the input
	 * passes local validation.  Local validation is defined by the bucket UI
	 * object.
	 */
	$.articleFeedbackv5.submitForm = function () {

		// Are we allowed to do this?
		if ( !$.articleFeedbackv5.submissionEnabled ) {
			return false;
		}

		// Get the form data
		var bucket = $.articleFeedbackv5.currentBucket();
		var formdata = {};
		if ( 'getFormData' in bucket ) {
			formdata = bucket.getFormData();
		}

		// Perform any local validation
		if ( 'localValidation' in bucket ) {
			var errors = bucket.localValidation( formdata );
			if ( errors ) {
				$.articleFeedbackv5.markFormErrors( errors );
				return;
			}
		}

		// check throttling
		if ( $.articleFeedbackv5.throttleThresholdPostsPerHour != -1 ) {
			var now = Date.now();
			var msInHour = 3600000;

			var priorTimestamps = new Array();
			var savedTimestamps = new Array();

			var priorCookieValue = $.cookie( $.articleFeedbackv5.prefix( 'submission_timestamps' ) );
			if ( priorCookieValue != null ) {
				var priorTimestamps = priorCookieValue.split( ',' );
			}

			for ( var i = 0; i < priorTimestamps.length; i++ ) {
				if ( now - priorTimestamps[i] <= msInHour ) {
					savedTimestamps.push( priorTimestamps[i] );
				}
			}

			var postsInLastHour = savedTimestamps.length;

			if ( postsInLastHour >= $.articleFeedbackv5.throttleThresholdPostsPerHour ) {
				// display throttling message
				$.articleFeedbackv5.markTopError( mw.msg( 'articlefeedbackv5-error-throttled' ) );

				// re-store pruned post timestamp list
				$.cookie( $.articleFeedbackv5.prefix( 'submission_timestamps' ), savedTimestamps.join( ',' ), { expires: 1, path: '/' } );

				return;
			}

			// if we get this far, they haven't been throttled, so update the post timestamp list with the current time and re-store it
			savedTimestamps.push(now);
			$.cookie( $.articleFeedbackv5.prefix( 'submission_timestamps' ), savedTimestamps.join( ',' ), { expires: 1, path: '/' } );
		}

		// Lock the form
		$.articleFeedbackv5.lockForm();

		// this is a good time to hide the help box, if its displayed
		$( '.articleFeedbackv5-tooltip' ).hide();

		// Request data
		var data = $.extend( formdata, {
			'action': 'articlefeedbackv5',
			'format': 'json',
			'anontoken': $.articleFeedbackv5.userId,
			'pageid': $.articleFeedbackv5.pageId,
			'revid': $.articleFeedbackv5.revisionId,
			'bucket': $.articleFeedbackv5.bucketId,
			'experiment': $.articleFeedbackv5.experiment().replace( 'option', '' ),
			'link': $.articleFeedbackv5.submittedLinkId
		} );

		// Track the submit click
		$.articleFeedbackv5.trackClick( $.articleFeedbackv5.experiment() + '-submit_attempt-' +
			( $.articleFeedbackv5.inDialog ? 'overlay' : 'bottom' ) );

		// Send off the ajax request
		$.ajax( {
			'url': $.articleFeedbackv5.apiUrl,
			'type': 'POST',
			'dataType': 'json',
			'data': data,
			'success': function( data ) {
				if ( 'articlefeedbackv5' in data
						&& 'feedback_id' in data.articlefeedbackv5
						&& 'cta_id' in data.articlefeedbackv5
						&& 'aft_url' in data.articlefeedbackv5 ) {
					$.articleFeedbackv5.feedbackId = data.articlefeedbackv5.feedback_id;
					$.articleFeedbackv5.selectCTA( data.articlefeedbackv5.cta_id );
					$.articleFeedbackv5.specialUrl = data.articlefeedbackv5.aft_url;
					$.articleFeedbackv5.permalink = data.articlefeedbackv5.permalink;
					$.articleFeedbackv5.unlockForm();
					$.articleFeedbackv5.showCTA( $.articleFeedbackv5.inDialog ? 'overlay' : 'bottom' );
					// Drop a cookie for a successful submit
					$.cookie( $.articleFeedbackv5.prefix( 'submitted' ), 'true', { 'expires': 365, 'path': '/' } );
					// Clear out anything that needs removing (usually trigger links)
					// Comment this out and uncomment the clear on dialog close to switch to
					// the trigger link replacing the form. _SWITCH_CLEAR_
					$.articleFeedbackv5.$toRemove.remove();
					$.articleFeedbackv5.$toRemove = $( [] );
					// Track the success
					$.articleFeedbackv5.trackClick( $.articleFeedbackv5.experiment() + '-submit_success-' +
						( $.articleFeedbackv5.inDialog ? 'overlay' : 'bottom' ) );
				} else {
					var code = 'unknown';
					var msg;
					if ( 'error' in data ) {
						if ( typeof( data.error ) == 'object' ) {
							msg = data.error;
							if ( 'code' in data.error ) {
								code = data.error.code;
							}
						} else if ( 'articlefeedbackv5-error-abuse' == data.error ) {
							msg = $.articleFeedbackv5.buildLink( data.error, {
								href: mw.msg( 'articlefeedbackv5-error-abuse-link' ),
								text: 'articlefeedbackv5-error-abuse-linktext',
								target: '_blank'
							});
							code = 'afreject';
						} else {
							msg = mw.msg( data.error );
						}
					} else if ( 'warning' in data ) {
						// NB: Warnings come from the AbuseFilter and are
						// already translated.
						msg = data.warning;
						code = 'afwarn';
					} else {
						msg = { info: mw.msg( 'articlefeedbackv5-error-unknown' ) };
					}
					// Track the error
					$.articleFeedbackv5.trackClick( $.articleFeedbackv5.experiment() +
						'-submit_error_' + code + '-' +
						( $.articleFeedbackv5.inDialog ? 'overlay' : 'bottom' ) );
					// Set up error state
					$.articleFeedbackv5.markFormErrors( { _api : msg } );
					$.articleFeedbackv5.unlockForm();
					if ( $.articleFeedbackv5.inDialog ) {
						$.articleFeedbackv5.setDialogDimensions();
					}
				}
			},
			'error': function (xhr, tstatus, error) {
				// Track the error
				$.articleFeedbackv5.trackClick( $.articleFeedbackv5.experiment() +
					'-submit_error_jquery-' +
					( $.articleFeedbackv5.inDialog ? 'overlay' : 'bottom' ) );
				// Set up error state
				var err = { _api: { info: mw.msg( 'articlefeedbackv5-error-submit' ) } };
				$.articleFeedbackv5.markFormErrors( err );
				$.articleFeedbackv5.unlockForm();
			}
		} );

		// Does the bucket need to do anything else on submit (alongside the
		// ajax request, not as a result of it)?
		if ( 'onSubmit' in bucket ) {
			bucket.onSubmit( formdata );
		}
	};

	// }}}
	// {{{ selectCTA

	/**
	 * Chooses a CTA
	 *
	 * @param  requested int the requested id
	 * @return int       the selected id
	 */
	$.articleFeedbackv5.selectCTA = function ( requested ) {

		// default CTA can be overridden using a GET-parameter
		var parameter = mw.util.getParamValue( 'aftv5_cta' );

		// since there's some validation to be done selecting the cta, we'll go recursive, only if
		// the one coming in differs from the GET-parameter
		if ( parameter && parameter != requested ) {
			cta = $.articleFeedbackv5.selectCTA( parameter );

			// verify that the returned CTA is actually the one we were validating, (if not, let's
			// fallback to validating the one initially requested)
			if ( cta == parameter ) {
				return cta;
			}
		}

		if ( !( requested in $.articleFeedbackv5.ctas ) ) {
			requested = '0';
		}
		var temp = $.articleFeedbackv5.ctas[requested];
		if ( 'verify' in temp ) {
			if ( !temp.verify() ) {
				requested = requested == '1' ? '2' : '0';
			}
		}
		$.articleFeedbackv5.ctaId = requested;
		return requested;
	};

	// }}}
	// {{{ showCTA

	/**
	 * Shows a CTA
	 *
	 * @param from string from whence came the request ("bottom" or "overlay")
	 */
	$.articleFeedbackv5.showCTA = function ( from ) {

		// Build the cta
		var cta = $.articleFeedbackv5.currentCTA();
		if ( !( 'build' in cta ) ) {
			return;
		}
		var $block = cta.build();
		if ( 'bindEvents' in cta ) {
			cta.bindEvents( $block );
		}
		$block.localize( { 'prefix': 'articlefeedbackv5-' } );

		// Add it to the appropriate container
		$.articleFeedbackv5.find( '.articleFeedbackv5-ui-inner' ).empty();
		$.articleFeedbackv5.find( '.articleFeedbackv5-ui-inner' )
			.append( $block );

		// Set the appropriate class on the ui block
		$.articleFeedbackv5.find( '.articleFeedbackv5-ui' )
			.removeClass( 'articleFeedbackv5-option-' + $.articleFeedbackv5.bucketId )
			.addClass( 'articleFeedbackv5-cta-' + $.articleFeedbackv5.ctaId );

		// Set the title in both places
		if ( 'getTitle' in cta ) {
			var title = cta.getTitle();
		} else {
			var title = $( '<div></div>' )
				.html( $.articleFeedbackv5.templates.ctaTitleConfirm )
				.localize( { 'prefix': 'articlefeedbackv5-' } );
			var link = $.articleFeedbackv5.trackingUrl(
					$.articleFeedbackv5.permalink,
					'cta_view_feedback-link_click'
			);
			title.find( '.articleFeedbackv5-confirmation-follow-up' ).msg( 'articlefeedbackv5-cta-confirmation-message', link );
			
			title = title.html();
		}
		$.articleFeedbackv5.$dialog.dialog( 'option', 'title', title );
		$.articleFeedbackv5.find( '.articleFeedbackv5-title' ).html( title );

		// Set the tooltip link
		$.articleFeedbackv5.find( '.articleFeedbackv5-tooltip-link' )
			.attr( 'href', mw.config.get( 'wgArticleFeedbackv5LearnToEdit' ) );

		// Add a close button to clear out the panel
		var $close = $( '<a class="articleFeedbackv5-clear-trigger">x</a>' )
			.click( function (e) {
				e.preventDefault();
				$.articleFeedbackv5.clear();
			} );
		$.articleFeedbackv5.$holder.find( '.articleFeedbackv5-title-wrap .articleFeedbackv5-tooltip-trigger' )
			.before( $close );

		// Do anything special the CTA requires
		if ( 'afterBuild' in cta ) {
			cta.afterBuild();
		}

		// The close element needs to be created anyway, to serve as an anchor. However, it needs
		// to be hidden when the CTA is not displayed in a dialog
		if( !$.articleFeedbackv5.inDialog ) {
			$close.hide();
		}

		// Reset the panel dimensions
		$.articleFeedbackv5.setDialogDimensions();

		// Track the event
		$.articleFeedbackv5.trackClick( $.articleFeedbackv5.experiment() + '-' +
			$.articleFeedbackv5.ctaName() + '-impression-' + from );

		$.articleFeedbackv5.nowShowing = 'cta';
	};

	// }}}
	// {{{ clear

	/**
	 * Clears out the panel
	 */
	$.articleFeedbackv5.clear = function () {
		$.articleFeedbackv5.isLoaded = false;
		$.articleFeedbackv5.inDialog = false;
		$.articleFeedbackv5.submissionEnabled = false;
		$.articleFeedbackv5.feedbackId = 0;
		$.articleFeedbackv5.clearContainers();
		$.articleFeedbackv5.nowShowing = 'none';
	};

	// }}}
	// {{{ clearContainers

	/**
	 * Wipes the containers from the page
	 */
	$.articleFeedbackv5.clearContainers = function () {
		$.articleFeedbackv5.$holder.empty();
		if ( $.articleFeedbackv5.$dialog ) {
			$.articleFeedbackv5.$dialog.remove();
		}
	};

	// }}}
	// {{{ addTriggerLinks

	/**
	 * Adds the trigger links to the page
	 */
	$.articleFeedbackv5.addTriggerLinks = function () {
		var hasTipsy = false;
		for ( var i in $.articleFeedbackv5.selectedLinks ) {
			var linkId = $.articleFeedbackv5.selectedLinks[i];
			if ( linkId in $.articleFeedbackv5.triggerLinks ) {
				var option = $.articleFeedbackv5.triggerLinks[linkId];
				var $link = option.build();
				if ( 'insert' in option ) {
					option.insert( $link );
				} else {
					$link.insertBefore( $.articleFeedbackv5.$holder );
				}
				if ( option.closeable ) {
					$.articleFeedbackv5.buildDisableFlyover( linkId, $link );
					hasTipsy = true;
				}
				if ( 'TBX' != linkId && '5' != $.articleFeedbackv5.bucketId ) {
					$.articleFeedbackv5.addToRemovalQueue( $link );
				}
			}
		}
		if ( hasTipsy ) {
			$( '.articleFeedbackv5-form-flyover-closebutton' ).live( 'click', function( e ) {
				e.preventDefault();
				var $host = $( '.articleFeedbackv5-trigger-link-' + $( e.target ).attr( 'rel' ) )
				$host.tipsy( 'hide' );
				$host.closest( '.articleFeedbackv5-trigger-link-holder' )
					.removeClass( 'articleFeedbackv5-tipsy-active' );
			} );
		}
	};

	// }}}
	// {{{ buildDisableFlyover

	/**
	 * Builds a disable flyover for a link
	 *
	 * @param string  linkId the name of the link (A-H, TBX)
	 * @param Element $link  the link object
	 */
	$.articleFeedbackv5.buildDisableFlyover = function ( linkId, $link ) {
		$link.addClass( 'articleFeedbackv5-trigger-link-holder' );
		$link.addClass( 'articleFeedbackv5-trigger-link-holder-' + linkId );
		var gravity = 'se';
		if ( 'A' == linkId ) {
			gravity = 'nw';
		}
		$link.find( '.articleFeedbackv5-close-trigger-link' )
			.addClass( 'articleFeedbackv5-trigger-link-' + linkId )
			.tipsy( {
				delayIn: 0,
				delayOut: 0,
				fade: false,
				fallback: '',
				gravity: gravity,
				html: true,
				live: false,
				offset: 10,
				opacity: 1.0,
				trigger: 'manual',
				className: 'articleFeedbackv5-disable-flyover-tip-' + linkId,
				title: function () {
					var $flyover = $( $.articleFeedbackv5.templates.disableFlyover );
					$flyover.localize( { 'prefix': 'articlefeedbackv5-' } );
					$flyover.find( '.articleFeedbackv5-disable-flyover' )
						.addClass( 'articleFeedbackv5-disable-flyover-' + linkId );

					$flyover.find( '.articleFeedbackv5-disable-flyover-help' )
						.html( $.articleFeedbackv5.buildLink(
							'articlefeedbackv5-disable-flyover-help', {
								tag: 'strong',
								text: 'articlefeedbackv5-disable-flyover-help-emphasis-text'
							}, {
								tag: 'quotes',
								text: 'articlefeedbackv5-disable-flyover-help-location',
							}, {
								tag: 'quotes',
								text: 'articlefeedbackv5-disable-preference',
							} ) );

					var prefLink = mw.config.get( 'wgScript' ) + '?' +
						$.param( { title: 'Special:Preferences' } ) +
						'#mw-prefsection-rendering';
					var prefTrackId = $.articleFeedbackv5.experiment() + '-disable_gotoprefs_click';
					$flyover.find( '.articleFeedbackv5-disable-flyover-button' )
						.attr( 'href', $.articleFeedbackv5.trackingUrl( prefLink, prefTrackId ) )
						.button()
						.addClass( 'ui-button-blue' );

					$flyover.find('.articleFeedbackv5-form-flyover-closebutton')
						.attr( 'href', '#hello' )
						.attr( 'rel', linkId );

					$.articleFeedbackv5.trackClick( $.articleFeedbackv5.experiment() + '-disable_flyover-impression' );
					return $flyover.html();
				}
			} )
			.click( function ( e ) {
				e.preventDefault();
				var $host = $( e.target );
				var $wrap = $host.closest( '.articleFeedbackv5-trigger-link-holder' )
				if ( $wrap.hasClass( 'articleFeedbackv5-tipsy-active' ) ) {
					$host.tipsy( 'hide' );
					$wrap.removeClass( 'articleFeedbackv5-tipsy-active' );
				} else {
					$host.tipsy( 'show' );
					$wrap.addClass( 'articleFeedbackv5-tipsy-active' );
					$.articleFeedbackv5.trackClick( $.articleFeedbackv5.experiment() + '-disable_button_click' );
				}
			} );
	};

	// }}}

	// }}}
	// {{{ UI methods

	// {{{ markShowstopperError

	/**
	 * Marks a showstopper error
	 *
	 * @param string message the message to display, if in dev
	 */
	$.articleFeedbackv5.markShowstopperError = function ( message ) {
		aft5_debug( message );
		if ( $.articleFeedbackv5.inDialog ) {
			$.articleFeedbackv5.$dialog.dialog( 'option', 'title', '' );
			$.articleFeedbackv5.$dialog.dialog( 'option', 'close', function () {
				$.articleFeedbackv5.clear();
			} );
			$.articleFeedbackv5.$dialog.find( '.articleFeedbackv5-ui' ).remove();
			$( '#ui-dialog-title-articleFeedbackv5-dialog-wrap' ).parent()
				.find( '.articleFeedbackv5-tooltip-trigger' ).remove();
			$.articleFeedbackv5.$dialog.append( $( '<div class="articleFeedbackv5-error-message"></div>' ) );
			$.articleFeedbackv5.find( '.articleFeedbackv5-error' ).show();
		} else {
			var $veil = $.articleFeedbackv5.find( '.articleFeedbackv5-error' );
			var $box  = $.articleFeedbackv5.$holder.find( '.articleFeedbackv5-panel' );
			$veil.css( 'top', '-' + $box.height() );
			$veil.css( 'width', $box.width() );
			$veil.css( 'height', $box.height() );
			$veil.show();
			$box.css( 'width', $box.width() );
			$box.css( 'height', $box.height() );
			$box.html( '' );
		}
		var $err = $.articleFeedbackv5.find( '.articleFeedbackv5-error-message' );
		$err.text( $.articleFeedbackv5.debug && message ? message : mw.msg( 'articlefeedbackv5-error' ) );
		$err.html( $err.html().replace( "\n", '<br />' ) );
		$.articleFeedbackv5.$toRemove.remove();
		$.articleFeedbackv5.$toRemove = $( [] );
		$.articleFeedbackv5.nowShowing = 'error';
	};

	// }}}
	// {{{ markTopError

	/**
	 * Marks an error at the top of the form
	 *
	 * @param msg string the error message
	 */
	$.articleFeedbackv5.markTopError = function ( msg ) {
		$.articleFeedbackv5.find( '.articleFeedbackv5-top-error' ).html( msg );
	};

	// }}}
	// {{{ markFormErrors

	/**
	 * Marks any errors on the form
	 *
	 * @param object errors errors, indexed by field name
	 */
	$.articleFeedbackv5.markFormErrors = function ( errors ) {
		if ( '_api' in errors ) {
			if ( typeof errors._api == 'object' ) {
				if ( 'info' in errors._api ) {
					mw.log( mw.msg( errors._api.info ) );
				} else {
					mw.log( mw.msg( 'articlefeedbackv5-error-submit' ) );
				}
				$.articleFeedbackv5.markTopError( mw.msg( 'articlefeedbackv5-error-submit' ) );
			} else {
				mw.log( mw.msg( errors._api ) );
				$.articleFeedbackv5.markTopError( errors._api );
			}
		} else {
			mw.log( mw.msg( 'articlefeedbackv5-error-validation' ) );
			if ( 'nofeedback' in errors ) {
				$.articleFeedbackv5.markTopError( mw.msg( 'articlefeedbackv5-error-nofeedback' ) );
			}
		}
		if ( $.articleFeedbackv5.debug ) {
			aft5_debug( errors );
		}
		if ( 'markFormErrors' in $.articleFeedbackv5.currentBucket() ) {
			$.articleFeedbackv5.currentBucket().markFormErrors( errors );
		}
	};

	// }}}
	// {{{ lockForm

	/**
	 * Locks the form
	 */
	$.articleFeedbackv5.lockForm = function () {
		var bucket = $.articleFeedbackv5.currentBucket();
		$.articleFeedbackv5.enableSubmission( false );
		$.articleFeedbackv5.$holder.find( '.articleFeedbackv5-lock' ).show();
	};

	// }}}
	// {{{ unlockForm

	/**
	 * Unlocks the form
	 */
	$.articleFeedbackv5.unlockForm = function () {
		var bucket = $.articleFeedbackv5.currentBucket();
		$.articleFeedbackv5.enableSubmission( true );
		$.articleFeedbackv5.$holder.find( '.articleFeedbackv5-lock' ).hide();
	};

	// }}}

	// }}}
	// {{{ Outside interaction methods

	// {{{ addToRemovalQueue

	/**
	 * Adds an element (usually a trigger link) to the collection that will be
	 * removed after a successful submission
	 *
	 * @param $el Element the element
	 */
	$.articleFeedbackv5.addToRemovalQueue = function ( $el ) {
		$.articleFeedbackv5.$toRemove = $.articleFeedbackv5.$toRemove.add( $el );
	};

	// }}}
	// {{{ setLinkId

	/**
	 * Sets the link ID
	 *
	 * @param int linkId the link ID
	 */
	$.articleFeedbackv5.setLinkId = function ( linkId ) {
		$.articleFeedbackv5.submittedLinkId = linkId;
	};

	// }}}
	// {{{ inDebug

	/**
	 * Returns whether the plugin is in debug mode
	 *
	 * @return int whether the plugin is in debug mode
	 */
	$.articleFeedbackv5.inDebug = function () {
		return $.articleFeedbackv5.debug;
	};

	// }}}
	// {{{ getBucketId

	/**
	 * Gets the bucket ID
	 *
	 * @return int the bucket ID
	 */
	$.articleFeedbackv5.getBucketId = function () {
		return $.articleFeedbackv5.bucketId;
	};

	// }}}
	// {{{ getShowing

	/**
	 * Returns which is showing: the form, the cta, or nothing
	 *
	 * @return string "form", "cta", or "none"
	 */
	$.articleFeedbackv5.getShowing = function () {
		return $.articleFeedbackv5.nowShowing;
	};

	// }}}
	// {{{ openAsModal

	/**
	 * Opens the feedback tool as a modal window
	 *
	 * @param $link Element the trigger link
	 */
	$.articleFeedbackv5.openAsModal = function ( $link ) {
		if ( 'cta' == $.articleFeedbackv5.nowShowing ) {
			// Uncomment here and comment out link removal to switch to the feedback
			// link replacing the form.  _SWITCH_CLEAR_
			// $.articleFeedbackv5.clear();
		}
		if ( !$.articleFeedbackv5.isLoaded ) {
			$.articleFeedbackv5.load( 'auto', 'overlay' );
		}
		if ( !$.articleFeedbackv5.inDialog ) {
			$.articleFeedbackv5.setDialogDimensions();
			$.articleFeedbackv5.$holder.find( '.articleFeedbackv5-tooltip' ).hide();
			$inner = $.articleFeedbackv5.$holder.find( '.articleFeedbackv5-ui' ).detach();
			$.articleFeedbackv5.$dialog.append( $inner );
			$.articleFeedbackv5.$dialog.dialog( 'option', 'position', [ 'center', 'center' ] );
			$.articleFeedbackv5.$dialog.dialog( 'open' );
			$.articleFeedbackv5.setLinkId( $link.data( 'linkId' ) );

			// Track the impression
			$.articleFeedbackv5.trackClick( $.articleFeedbackv5.experiment() + '-impression-overlay' );

			// Hide the panel
			$.articleFeedbackv5.$holder.hide();

			$.articleFeedbackv5.inDialog = true;
		}
	};

	// }}}
	// {{{ closeAsModal

	/**
	 * Closes the feedback tool as a modal window
	 */
	$.articleFeedbackv5.closeAsModal = function () {
		if ( $.articleFeedbackv5.inDialog ) {
			if ( 'form' == $.articleFeedbackv5.nowShowing ) {
				$.articleFeedbackv5.trackClick( $.articleFeedbackv5.experiment() + '-close-overlay' );
			} else if ('cta' == $.articleFeedbackv5.nowShowing ) {
				$.articleFeedbackv5.trackClick( $.articleFeedbackv5.experiment() + '-' +
					$.articleFeedbackv5.ctaName() + '-close-overlay' );
			}
			$.articleFeedbackv5.setLinkId( 'X' );
			$.articleFeedbackv5.$dialog.find( '.articleFeedbackv5-tooltip' ).hide();
			$inner = $.articleFeedbackv5.$dialog.find( '.articleFeedbackv5-ui' ).detach();
			$.articleFeedbackv5.$holder.find( '.articleFeedbackv5-buffer' ).append( $inner );
			$.articleFeedbackv5.$holder.show();
			$.articleFeedbackv5.inDialog = false;
			if ( 'cta' == $.articleFeedbackv5.nowShowing ) {
				$.articleFeedbackv5.clear();
			}
			if ( 'onModalToggle' in $.articleFeedbackv5.currentBucket() ) {
				$.articleFeedbackv5.currentBucket().onModalToggle( 'bottom' );
			}
		}
	};

	// }}}
	// {{{ toggleModal

	/**
	 * Toggles the modal state
	 *
	 * @param $link Element the trigger link
	 */
	$.articleFeedbackv5.toggleModal = function ( $link ) {
		if ( $.articleFeedbackv5.inDialog ) {
			$.articleFeedbackv5.closeAsModal();
			$.articleFeedbackv5.$dialog.dialog( 'close' );
		} else {
			$.articleFeedbackv5.openAsModal( $link );
		}
		if ( 'onModalToggle' in $.articleFeedbackv5.currentBucket() ) {
			$.articleFeedbackv5.currentBucket().onModalToggle( $.articleFeedbackv5.inDialog ? 'overlay' : 'bottom' );
		}
	};

	// }}}
	// {{{ setDialogDimensions

	/**
	 * Sets the dialog's dimensions
	 */
	$.articleFeedbackv5.setDialogDimensions = function () {
		var w = $.articleFeedbackv5.find( '.articleFeedbackv5-ui' ).width();
		var h = $.articleFeedbackv5.find( '.articleFeedbackv5-ui' ).height();
		$.articleFeedbackv5.$dialog.dialog( 'option', 'width', w + 25 );
		$.articleFeedbackv5.$dialog.dialog( 'option', 'height', h + 85 );
	};

	// }}}
	// {{{ clickTrackingOn

	/**
	 * Returns whether click tracking is on
	 *
	 * @bool whether click tracking is on
	 */
	$.articleFeedbackv5.clickTrackingOn = function () {
		return $.articleFeedbackv5.clickTracking;
	};

	// }}}
	// {{{ trackClick

	/**
	 * Tracks a click
	 *
	 * @param trackingId string the tracking ID
	 */
	$.articleFeedbackv5.trackClick = function ( trackingId ) {
		if ( $.articleFeedbackv5.clickTracking && $.isFunction( $.trackActionWithInfo ) ) {
			$.trackActionWithInfo(
				$.articleFeedbackv5.prefix( trackingId ),
				mw.config.get( 'wgPageName' ) + '|' + $.articleFeedbackv5.revisionId
			);
		}
	};

	// }}}
	// {{{ trackActionURL

	/**
	 * Rewrites a URL to one that runs through the ClickTracking API module
	 * which registers the event and redirects to the real URL
	 *
	 * This is a copy of the one out of the clicktracking javascript API
	 * we have to do our OWN because there is no "additional" option in that
	 * API which we MUST use for the article title
	 *
	 * @param {string} url URL to redirect to
	 * @param {string} id Event identifier
	 */
	$.articleFeedbackv5.trackActionURL = function( url, id ) {
		return mw.config.get( 'wgScriptPath' ) + '/api.php?' + $.param( {
			'action': 'clicktracking',
			'format' : 'json',
			'eventid': id,
			'namespacenumber': mw.config.get( 'wgNamespaceNumber' ),
			'token': $.cookie( 'clicktracking-session' ),
			'additional': mw.config.get( 'wgPageName' ) + '|' + $.articleFeedbackv5.revisionId,
			'redirectto': url
		} );
	};

	// }}}
	// {{{ clickTriggerLink

	/**
	 * Handles the click event on a trigger link
	 *
	 * @param $link Element the trigger link
	 */
	$.articleFeedbackv5.clickTriggerLink = function( $link ) {
		var tracking_id = $.articleFeedbackv5.experiment() +
			'-trigger' + $link.data( 'linkId' ) +
			'-click-overlay';
		$.articleFeedbackv5.trackClick( tracking_id );
		$.articleFeedbackv5.toggleModal( $link );
	};

	// }}}

// }}}
// {{{ articleFeedbackv5 plugin

/**
 * Right now there are no options for this plugin, but there will be in the
 * future, so allow them to be passed in.
 *
 * If a string is passed in, it's considered a public function
 */
$.fn.articleFeedbackv5 = function ( opts, arg ) {
	if ( typeof ( opts ) == 'undefined' || typeof ( opts ) == 'object' ) {
		$.articleFeedbackv5.init( $( this ), opts );
		return $( this );
	}
	var public = {
		setLinkId: { args: 1, ret: false },
		getBucketId: { args: 0, ret: true },
		inDebug: { args: 0, ret: true },
		nowShowing: { args: 0, ret: true },
		prefix: { args: 1, ret: true },
		experiment: { args: 0, ret: true },
		addToRemovalQueue: { args: 1, ret: false },
		openAsModal: { args: 1, ret: false },
		closeAsModal: { args: 0, ret: true },
		toggleModal: { args: 1, ret: false },
		clickTrackingOn: { args: 0, ret: true },
		trackClick: { args: 1, ret: false }
	};
	if ( opts in public ) {
		var r;
		if ( 1 == public[opts].args ) {
			r = $.articleFeedbackv5[opts]( arg );
		} else if ( 0 == public[opts].args ) {
			r = $.articleFeedbackv5[opts]();
		}
		if ( public[opts].ret) {
			return r;
		}
	}
	return $( this );
};

// }}}

} )( jQuery );

