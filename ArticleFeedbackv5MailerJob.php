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
		if ( null === $wgArticleFeedbackv5OversightEmails ) {
			return true;
		}

		// if we don't have the right params set return false, job can't run
		if (   !array_key_exists( 'user_name', $params)
		    || !array_key_exists( 'user_url', $params)
		    || !array_key_exists( 'page_name', $params)
		    || !array_key_exists( 'page_url', $params)
		    || !array_key_exists( 'permalink', $params)) {
			return false;
		    }

		// get our addresses
		$to = new MailAddress( $wgArticleFeedbackv5OversightEmails, $wgArticleFeedbackv5OversightEmailName );
		$from = new MailAddress( $wgPasswordSender, $wgPasswordSenderName );
		$replyto = new MailAddress( $wgNoReplyAddress );

		// get our text
		list($subject, $body) = $this->composeMail($params['user_name'],
							   $params['user_url'],
							   $params['page_name'],
							   $params['page_url'],
							   $params['permalink']);

		$status = UserMailer::send( $to, $from, $subject, $body, $replyto );

		wfProfileOut( __METHOD__ );

		return $status;
	}

	/**
	 * Generate the "an oversight request has been made" email for sending
	 * to the mailing list
	 *
	 * @param string $requestor_name      user name
	 * @param string $requestor_url       link to user page
	 * @param string $page_name           page title
	 * @param string $page_url            page url
	 * @param string $feedback_permalink  permalink url to feedback
	 */
	protected function composeMail( $requestor_name, $requestor_url, $page_name, $page_url, $feedback_permalink ) {
		global $wgPasswordSender, $wgPasswordSenderName, $wgNoReplyAddress, $wgRequest;
		global $wgArticleFeedbackv5OversightEmailHelp;

		// build the subject
		$subject = wfMessage( 'articlefeedbackv5-email-request-oversight-subject' )->escaped();

		//text version, no need to escape since client will interpret it as plain text
		$body = wfMessage( 'articlefeedbackv5-email-request-oversight-body' )
				->params(
					$requestor_name,
					$page_name)
				->rawParams(
					$feedback_permalink)
				->params(
					$wgArticleFeedbackv5OversightEmailHelp)
				->text();

		return array($subject, $body);
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