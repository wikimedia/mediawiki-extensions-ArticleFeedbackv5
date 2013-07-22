<?php
/**
 * SpecialArticleFeedbackv5 class
 *
 * @package    ArticleFeedback
 * @subpackage Job
 * @author     Elizabeth M Smith <elizabeth@omniti.com>
 * @version    $Id$
 */

/**
 * This is a job to do mailings for oversight requests
 *
 * @package    ArticleFeedback
 * @subpackage Job
 */
class ArticleFeedbackv5MailerJob extends Job {
	/**
	 * Params required to be able to send email.
	 *
	 * @var array
	 */
	protected $requiredParams = array(
		'user_name',
		'user_url',
		'page_name',
		'page_url',
		'permalink',
	);

	/**
	 * Passthrough that sends the name of the class as the name of the job
	 *
	 * @param $command
	 * @param $title
	 * @param $params array
	 * @param int $id
	 */
	function __construct( $title, $params, $id = 0 ) {
		parent::__construct( __CLASS__, $title, $params, $id );
	}

	/**
	 * Run the job
	 * @return boolean success
	 */
	function run() {
		wfProfileIn( __METHOD__ );

		global $wgArticleFeedbackv5OversightEmails, $wgArticleFeedbackv5OversightEmailName;
		global $wgPasswordSender, $wgPasswordSenderName, $wgNoReplyAddress;

		$params = $this->params;

		// if the oversight email address is empty we're going to just skip all this, but return true
		if ( $wgArticleFeedbackv5OversightEmails === null ) {
			wfProfileOut( __METHOD__ );
			return true;
		}

		// if we don't have the right params set return false, job can't run
		$missing = array_diff( $this->requiredParams, array_keys( $params ) );
		if ( $missing ) {
			wfProfileOut( __METHOD__ );
			return false;
	    }

		// get our addresses
		$to = new MailAddress( $wgArticleFeedbackv5OversightEmails, $wgArticleFeedbackv5OversightEmailName );
		$from = new MailAddress( $wgPasswordSender, $wgPasswordSenderName );
		$replyto = new MailAddress( $wgNoReplyAddress );

		// get our text
		list( $subject, $body ) = $this->composeMail(
			$params['user_name'],
			$params['user_url'],
			$params['page_name'],
			$params['page_url'],
			$params['permalink'],
			isset( $params['notes'] ) ? $params['notes'] : ''
		);

		$status = UserMailer::send( $to, $from, $subject, $body, $replyto );

		wfProfileOut( __METHOD__ );

		return $status->isOK();
	}

	/**
	 * Generate the "an oversight request has been made" email for sending
	 * to the mailing list
	 *
	 * @param string $requestorName      user name
	 * @param string $requestorUrl       link to user page
	 * @param string $pageName           page title
	 * @param string $pageUrl            page url
	 * @param string $feedbackPermalink  permalink url to feedback
	 * @param string[optional] $notes    additional text
	 */
	protected function composeMail( $requestorName, $requestorUrl, $pageName, $pageUrl, $feedbackPermalink, $notes = '' ) {
		global $wgArticleFeedbackv5OversightEmailHelp;

		// build the subject
		$subject = wfMessage( 'articlefeedbackv5-email-request-oversight-subject' )->escaped();

		// form notes text block only if notes have been added
		if ( $notes ) {
			$notes = wfMessage( 'articlefeedbackv5-email-request-oversight-body-notes', $notes )->plain();
		}

		// text version, no need to escape since client will interpret it as plain text
		$body = wfMessage( 'articlefeedbackv5-email-request-oversight-body' )
					->params( $requestorName, $pageName )
					->rawParams( $feedbackPermalink )
					->params( $wgArticleFeedbackv5OversightEmailHelp, $notes )
					->text();

		return array( $subject, $body );
	}

	/**
	 * Override insert - prototype does not even have a jobs table and will
	 * bomb miserably on this
	 *
	 * @return bool true on success
	 */
	function insert() {
		global $wgArticleFeedbackv5OversightEmails;

		// if the oversight email address is empty we're going to just skip all this, but return true
		if ( null === $wgArticleFeedbackv5OversightEmails ) {
			return true;
		}

		return parent::insert();
	}
}
