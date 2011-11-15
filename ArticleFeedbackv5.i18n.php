<?php
$messages = array();

/** English
 * @author Nimish Gautam
 * @author Sam Reed
 * @author Brandon Harris
 * @author Trevor Parscal
 * @author Arthur Richards
 */
$messages['en'] = array(
	'articlefeedbackv5' => 'Article feedback dashboard',
	'articlefeedbackv5-desc' => 'Article feedback',
	/* ArticleFeedback survey */
	'articlefeedbackv5-survey-question-origin' => 'What page were you on when you started this survey?',
	'articlefeedbackv5-survey-question-whyrated' => 'Please let us know why you rated this page today (check all that apply):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'I wanted to contribute to the overall rating of the page',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'I hope that my rating would positively affect the development of the page',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'I wanted to contribute to {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'I like sharing my opinion',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => "I didn't provide ratings today, but wanted to give feedback on the feature",
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Other',
	'articlefeedbackv5-survey-question-useful' => 'Do you believe the ratings provided are useful and clear?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Why?',
	'articlefeedbackv5-survey-question-comments' => 'Do you have any additional comments?',
	'articlefeedbackv5-survey-submit' => 'Submit',
	'articlefeedbackv5-survey-title' => 'Please answer a few questions',
	'articlefeedbackv5-survey-thanks' => 'Thanks for filling out the survey.',
	'articlefeedbackv5-survey-disclaimer' => 'By submitting, you agree to transparency under these $1.',
	'articlefeedbackv5-survey-disclaimerlink' => 'terms',
	/* ext.articleFeedbackv5 and jquery.articleFeedbackv5 */
	'articlefeedbackv5-error' => 'An error has occured. Please try again later.',
	'articlefeedbackv5-form-switch-label' => 'Rate this page',
	'articlefeedbackv5-form-panel-title' => 'Rate this page',
	'articlefeedbackv5-form-panel-explanation' => 'What\'s this?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:ArticleFeedback',
	'articlefeedbackv5-form-panel-clear' => 'Remove this rating',
	'articlefeedbackv5-form-panel-expertise' => 'I am highly knowledgeable about this topic (optional)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'I have a relevant college/university degree',
	'articlefeedbackv5-form-panel-expertise-profession' => 'It is part of my profession',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'It is a deep personal passion',
	'articlefeedbackv5-form-panel-expertise-other' => 'The source of my knowledge is not listed here',
	'articlefeedbackv5-form-panel-helpimprove' => 'I would like to help improve Wikipedia, send me an e-mail (optional)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'We will send you a confirmation e-mail. We will not share your e-mail address with outside parties as per our $1.',
	'articlefeedbackv5-form-panel-helpimprove-email-placeholder' => 'email@example.org', // Optional
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'feedback privacy statement',
	'articlefeedbackv5-form-panel-submit' => 'Submit ratings',
	'articlefeedbackv5-form-panel-pending' => 'Your ratings have not been submitted yet',
	'articlefeedbackv5-form-panel-success' => 'Saved successfully',
	'articlefeedbackv5-form-panel-expiry-title' => 'Your ratings have expired',
	'articlefeedbackv5-form-panel-expiry-message' => 'Please reevaluate this page and submit new ratings.',
	'articlefeedbackv5-report-switch-label' => 'View page ratings',
	'articlefeedbackv5-report-panel-title' => 'Page ratings',
	'articlefeedbackv5-report-panel-description' => 'Current average ratings.',
	'articlefeedbackv5-report-empty' => 'No ratings',
	'articlefeedbackv5-report-ratings' => '$1 ratings',
	'articlefeedbackv5-field-trustworthy-label' => 'Trustworthy',
	'articlefeedbackv5-field-trustworthy-tip' => 'Do you feel this page has sufficient citations and that those citations come from trustworthy sources?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Lacks reputable sources',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Few reputable sources',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Adequate reputable sources',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Good reputable sources',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Great reputable sources',
	'articlefeedbackv5-field-complete-label' => 'Complete',
	'articlefeedbackv5-field-complete-tip' => 'Do you feel that this page covers the essential topic areas that it should?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Missing most information',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Contains some information',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Contains key information, but with gaps',
	'articlefeedbackv5-field-complete-tooltip-4' => 'Contains most key information',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Comprehensive coverage',
	'articlefeedbackv5-field-objective-label' => 'Objective',
	'articlefeedbackv5-field-objective-tip' => 'Do you feel that this page shows a fair representation of all perspectives on the issue?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Heavily biased',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Moderate bias',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Minimal bias',
	'articlefeedbackv5-field-objective-tooltip-4' => 'No obvious bias',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Completely unbiased',
	'articlefeedbackv5-field-wellwritten-label' => 'Well-written',
	'articlefeedbackv5-field-wellwritten-tip' => 'Do you feel that this page is well-organized and well-written?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Incomprehensible',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Difficult to understand',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Adequate clarity',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Good clarity',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Exceptional clarity',
	'articlefeedbackv5-pitch-reject' => 'Maybe later',
	'articlefeedbackv5-pitch-or' => 'or',
	'articlefeedbackv5-pitch-thanks' => 'Thanks! Your ratings have been saved.',
	'articlefeedbackv5-pitch-survey-message' => 'Please take a moment to complete a short survey.',
	'articlefeedbackv5-pitch-survey-body' => '',
	'articlefeedbackv5-pitch-survey-accept' => 'Start survey',
	'articlefeedbackv5-pitch-join-message' => 'Did you want to create an account?',
	'articlefeedbackv5-pitch-join-body' => 'An account will help you track your edits, get involved in discussions, and be a part of the community.',
	'articlefeedbackv5-pitch-join-accept' => 'Create an account',
	'articlefeedbackv5-pitch-join-login' => 'Log in',
	'articlefeedbackv5-pitch-edit-message' => 'Did you know that you can edit this page?',
	'articlefeedbackv5-pitch-edit-body' => '',
	'articlefeedbackv5-pitch-edit-accept' => 'Edit this page',
	'articlefeedbackv5-survey-message-success' => 'Thanks for filling out the survey.',
	'articlefeedbackv5-survey-message-error' => 'An error has occurred.
Please try again later.',
	'articlefeedbackv5-privacyurl' => 'http://wikimediafoundation.org/wiki/Feedback_privacy_statement',
	/* Special:ArticleFeedback */
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Today\'s highs and lows',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Pages with highest ratings: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Pages with lowest ratings: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'This week\'s most changed',
	'articleFeedbackv5-table-caption-recentlows' => 'Recent lows',
	'articleFeedbackv5-table-heading-page' => 'Page',
	'articleFeedbackv5-table-heading-average' => 'Average',
	'articlefeedbackv5-table-noratings' => '-',
	'articleFeedbackv5-copy-above-highlow-tables' => 'This is an experimental feature.  Please provide feedback on the [$1 discussion page].',
	'articlefeedbackv5-dashboard-bottom' => "'''Note''': We will continue to experiment with different ways of surfacing articles in these dashboards.  At present, the dashboards include the following articles:
* Pages with highest/lowest ratings: articles that have received at least 10 ratings within the last 24 hours.  Averages are calculated by taking the mean of all ratings submitted within the last 24 hours.
* Recent lows: articles that got 70% or more low (2 stars or lower) ratings in any category in the last 24 hours. Only articles that have received at least 10 ratings in the last 24 hours are included.",
	/* Special:Preferences */
	'articlefeedbackv5-disable-preference' => "Don't show the Article feedback widget on pages",
	/* EmailCapture */
	'articlefeedbackv5-emailcapture-response-body' => 'Hello!

Thank you for expressing interest in helping to improve {{SITENAME}}.

Please take a moment to confirm your e-mail by clicking on the link below: 

$1

You may also visit:

$2

And enter the following confirmation code:

$3

We will be in touch shortly with how you can help improve {{SITENAME}}.

If you did not initiate this request, please ignore this e-mail and we will not send you anything else.

Best wishes, and thank you,
The {{SITENAME}} team',
);

/** Message documentation (Message documentation)
 * @author Arthur Richards
 * @author Brandon Harris
 * @author EugeneZelenko
 * @author Krinkle
 * @author Minh Nguyen
 * @author Praveenp
 * @author Purodha
 * @author Raymond
 * @author Sam Reed
 * @author Siebrand
 * @author Yekrats
 */
$messages['qqq'] = array(
	'articlefeedbackv5' => 'The title of the feature. It is about reader feedback.
	
Please visit http://prototype.wikimedia.org/articleassess/Main_Page for a prototype installation.',
	'articlefeedbackv5-desc' => '{{desc}}',
	'articlefeedbackv5-survey-question-whyrated' => 'This is a question in the survey with checkboxes for the answers. The user can check multiple answers.',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'This is a possible answer for the "Why did you rate this article today?" survey question.',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'This is a possible answer for the "Why did you rate this article today?" survey question.',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'This is a possible answer for the "Why did you rate this article today?" survey question.',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'This is a possible answer for the "Why did you rate this article today?" survey question.',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'This is a possible answer for the "Why did you rate this article today?" survey question.',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'This is a possible answer for the "Why did you rate this article today?" survey question. The user can check this to fill out an answer that wasn\'t provided as a checkbox.
{{Identical|Other}}',
	'articlefeedbackv5-survey-question-useful' => 'This is a question in the survey with "yes" and "no" (prefswitch-survey-true and prefswitch-survey-false) as possible answers.',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'This question appears when the user checks "no" for the "Do you believe the ratings provided are useful and clear?" question. The user can enter their answer in a text box.',
	'articlefeedbackv5-survey-question-comments' => 'This is a question in the survey with a text box that the user can enter their answer in.',
	'articlefeedbackv5-survey-submit' => 'This is the caption for the button that submits the survey.
{{Identical|Submit}}',
	'articlefeedbackv5-survey-title' => 'This text appears in the title bar of the survey dialog.',
	'articlefeedbackv5-survey-thanks' => 'This text appears when the user has successfully submitted the survey.',
	'articlefeedbackv5-survey-disclaimer' => 'This text appears on the survey form below the comment field and above the submit button. $1 is a link pointing to the privacy policy. The link text is in the articlefeedbackv5-survey-disclaimerlink message.',
	'articlefeedbackv5-form-panel-explanation' => '{{Identical|What is this}}',
	'articlefeedbackv5-form-panel-explanation-link' => 'Do not translate "Project:". Also translate the "ArticleFeedback" special page name at [[Special:AdvancedTranslate]].',
	'articlefeedbackv5-form-panel-helpimprove' => 'This message should use {{SITENAME}}.',
	'articlefeedbackv5-form-panel-helpimprove-note' => '$1 is a link pointing to the privacy policy. The link text is in the articlefeedbackv5-form-panel-helpimprove-privacy message.',
	'articlefeedbackv5-form-panel-helpimprove-email-placeholder' => '{{Optional}}',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => '{{Identical|Privacy}}',
	'articlefeedbackv5-report-ratings' => "Needs plural support.
This message is used in JavaScript by module 'jquery.articleFeedback'.
$1 is an integer, and the rating count.",
	'articlefeedbackv5-pitch-or' => '{{Identical|Or}}',
	'articlefeedbackv5-pitch-join-body' => 'Based on {{msg-mw|articleFeedbackv5-pitch-join-message}}.',
	'articlefeedbackv5-pitch-join-accept' => '{{Identical|Create an account}}',
	'articlefeedbackv5-pitch-join-login' => '{{Identical|Log in}}',
	'articlefeedbackv5-privacyurl' => 'This URL can be changed to point to a translated version of the page if it exists.',
	'articleFeedbackv5-table-heading-page' => 'This is used in the [[mw:Extension:ArticleFeedback|Article Feedback extension]].
{{Identical|Page}}',
	'articleFeedbackv5-table-heading-average' => '{{Identical|Average}}',
	'articlefeedbackv5-table-noratings' => '{{Optional}}

Text to display in a table cell if there is no number to be shown',
	'articleFeedbackv5-copy-above-highlow-tables' => 'The variable $1 will contain a full URL to a discussion page where the dashboard can be discussed - since the dashboard is powered by a special page, we can not rely on the built-in MediaWiki talk page.',
	'articlefeedbackv5-emailcapture-response-body' => 'Body of an e-mail sent to a user wishing to participate in [[mw:Extension:ArticleFeedback|article feedback]] (see the extension documentation).
* <code>$1</code> – URL of the confirmation link
* <code>$2</code> – URL to type in the confirmation code manually.
* <code>$3</code> – Confirmation code for the user to type in',
);

/** Afrikaans (Afrikaans)
 * @author Naudefj
 */
$messages['af'] = array(
	'articlefeedbackv5' => 'Bladsybeoordeling',
	'articlefeedbackv5-desc' => 'Bladsybeoordeling',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Ek wil bydrae tot {{site name}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Ek hou daarvan om my mening te deel',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Ander',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Hoekom?',
	'articlefeedbackv5-survey-question-comments' => 'Het u enige addisionele kommentaar?',
	'articlefeedbackv5-survey-submit' => 'Dien in',
	'articlefeedbackv5-survey-title' => "Antwoord asseblief 'n paar vrae",
	'articlefeedbackv5-survey-thanks' => 'Dankie dat u die opname ingevul het.',
	'articlefeedbackv5-form-switch-label' => 'Beoordeel hierdie bladsy',
	'articlefeedbackv5-form-panel-title' => 'Beoordeel hierdie bladsy',
	'articlefeedbackv5-form-panel-explanation' => 'Wat is dit?',
	'articlefeedbackv5-form-panel-clear' => 'Verwyder hierdie gradering',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Privaatheidsbeleid',
	'articlefeedbackv5-form-panel-submit' => 'Stuur beoordeling',
	'articlefeedbackv5-form-panel-success' => 'Suksesvol gestoor',
	'articlefeedbackv5-form-panel-expiry-title' => 'U graderings het verstryk',
	'articlefeedbackv5-report-switch-label' => 'Wys bladsygraderings',
	'articlefeedbackv5-report-panel-title' => 'Bladsygraderings',
	'articlefeedbackv5-report-panel-description' => 'Huidige gemiddelde gradering.',
	'articlefeedbackv5-report-empty' => 'Geen beoordelings',
	'articlefeedbackv5-report-ratings' => '$1 beoordelings',
	'articlefeedbackv5-field-trustworthy-label' => 'Betroubaar',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Sonder betroubare bronne',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Weinig betroubare bronne',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Voldoende betroubare bronne',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Goeie betroubare bronne',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Uitstekend betroubare bronne',
	'articlefeedbackv5-field-complete-label' => 'Voltooid',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Die meeste inligting ontbreek',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Bevat sommige inligting',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Bevat belangrike inligting, maar met die leemtes',
	'articlefeedbackv5-field-complete-tooltip-4' => 'Bevat die mees belangrike inligting',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Omvattende dekking',
	'articlefeedbackv5-field-objective-label' => 'Onbevooroordeeld',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Swaar partydig',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Matig partydig',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Bietjie partydig',
	'articlefeedbackv5-field-objective-tooltip-4' => 'Geen duidelike partydigheid',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Glad nie partydig nie',
	'articlefeedbackv5-field-wellwritten-label' => 'Goed geskryf',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Onverstaanbaar',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Moeilik om te verstaan',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Voldoende duidelikheid',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Heel duidelikheid',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Uitsonderlik duidelik',
	'articlefeedbackv5-pitch-reject' => 'Miskien later',
	'articlefeedbackv5-pitch-or' => 'of',
	'articlefeedbackv5-pitch-thanks' => 'Dankie! U beoordeling is gestoor.',
	'articlefeedbackv5-pitch-survey-accept' => 'Begin vraelys',
	'articlefeedbackv5-pitch-join-accept' => "Skep 'n gebruiker",
	'articlefeedbackv5-pitch-join-login' => 'Meld aan',
	'articlefeedbackv5-pitch-edit-accept' => 'Wysig hierdie bladsy',
	'articlefeedbackv5-survey-message-success' => 'Dankie dat u die opname ingevul het.',
	'articlefeedbackv5-survey-message-error' => "'n Fout het voorgekom.
Probeer asseblief later weer.",
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Vandag se hoogte- en laagtepunte',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Bladsye met die hoogste graderings: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Bladsye met die laagste graderings: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Hierdie week se mees veranderde',
	'articleFeedbackv5-table-caption-recentlows' => 'Onlangse laagtepunte',
	'articleFeedbackv5-table-heading-page' => 'Bladsy',
	'articleFeedbackv5-table-heading-average' => 'Gemiddelde',
);

/** Aragonese (Aragonés)
 * @author Juanpabl
 */
$messages['an'] = array(
	'articleFeedbackv5-table-heading-page' => 'Pachina',
);

/** Arabic (العربية)
 * @author Ciphers
 * @author Meno25
 * @author Mido
 * @author OsamaK
 * @author زكريا
 */
$messages['ar'] = array(
	'articlefeedbackv5' => 'لوحة تعليقات المقالة',
	'articlefeedbackv5-desc' => 'ملاحظات على المقال',
	'articlefeedbackv5-survey-question-origin' => 'في أي صفحة كنت عندما بدأت هذا الاستطلاع؟',
	'articlefeedbackv5-survey-question-whyrated' => 'الرجاء إخبارنا لماذا قمت بتقييم هذه الصفحة اليوم (ضع علامة أمام كل ما ينطبق):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'أردت أن أساهم في التقييم الكلي للصفحة',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'آمل أن التصويت الذي أدلي به سيؤثر إيجابا على تطوير الصفحة',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => ' أردت أن أساهم في {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'أود مشاركة رأيي',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'لم أقدم أي تقييمات اليوم، لكني أردت إعطاء ملاحظات عن هذه الأداة',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'ܐܚܪܢܐ',
	'articlefeedbackv5-survey-question-useful' => 'هل تعتقد أن التقييم المقدم مفيد وواضح؟',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'ܠܡܢܐ?',
	'articlefeedbackv5-survey-question-comments' => 'هل لديك أي تعليقات إضافية؟',
	'articlefeedbackv5-survey-submit' => 'أرسل',
	'articlefeedbackv5-survey-title' => 'الرجاء الإجابة على بعض الأسئلة',
	'articlefeedbackv5-survey-thanks' => 'شكرا لملء الاستبيان.',
	'articlefeedbackv5-survey-disclaimer' => 'للمساعدة على تحسين هذه الخاصية، يمكنك لك أن تشارك مجتمع ويكيبيديا في التعليق وبصفة مجهولة.',
	'articlefeedbackv5-error' => 'لقد حدث خطأ. كرر المحاولة لاحقا.',
	'articlefeedbackv5-form-switch-label' => 'قيم هذه الصفحة',
	'articlefeedbackv5-form-panel-title' => 'قيم هذه الصفحة',
	'articlefeedbackv5-form-panel-explanation' => 'ما هذا؟',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:تعليقات_المقالة',
	'articlefeedbackv5-form-panel-clear' => 'أزل هذا التقييم',
	'articlefeedbackv5-form-panel-expertise' => 'أنا على دراية كبيرة بهذا الموضوع (اختياري)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'أنا حاصل على درجة جامعية مناسبة',
	'articlefeedbackv5-form-panel-expertise-profession' => 'من اختصاصي المهني',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'من أحب هواياتي',
	'articlefeedbackv5-form-panel-expertise-other' => 'مصدر معرفتي غير مدرج هنا',
	'articlefeedbackv5-form-panel-helpimprove' => 'أود المساعدة على تحسين ويكيبيديا، أرسل لي رسالة بالبريد الإلكتروني (اختياري)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'سوف نرسل لك رسالة تأكيد بالبريد إلكتروني، ولن يعلم أحد بعنوانه. $1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'سياسة الخصوصية',
	'articlefeedbackv5-form-panel-submit' => 'أرسل التقييمات',
	'articlefeedbackv5-form-panel-pending' => 'ما زالت تقييمك لم يرسل',
	'articlefeedbackv5-form-panel-success' => 'حُفظ بنجاح',
	'articlefeedbackv5-form-panel-expiry-title' => 'لم يعد تقييمك صالحا',
	'articlefeedbackv5-form-panel-expiry-message' => 'أعد تقييم هذه الصفحة وأرسل هذا التقييم.',
	'articlefeedbackv5-report-switch-label' => 'عرض تقييمات الصفحة',
	'articlefeedbackv5-report-panel-title' => 'تقييمات الصفحة',
	'articlefeedbackv5-report-panel-description' => 'متوسط التقييمات الحالية.',
	'articlefeedbackv5-report-empty' => 'لا توجد تقييمات',
	'articlefeedbackv5-report-ratings' => 'تقييمات $1',
	'articlefeedbackv5-field-trustworthy-label' => 'جدير بالثقة',
	'articlefeedbackv5-field-trustworthy-tip' => 'هل تظن أن لهذه الصفحة استشهادات كافية وأن تلك الاستشهادات تأتي من مصادر جديرة بالثقة؟',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'ينقص مصادر مشهورة',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'بضع مصادر مشهورة',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'ما يكفي من المصادر المشهورة',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'مصادر مشهورة حسنة',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'مصادر مشهورة فضلى',
	'articlefeedbackv5-field-complete-label' => 'مكتمل',
	'articlefeedbackv5-field-complete-tip' => 'هل تشعر بأن هذه الصفحة تغطي مجالات الموضوع الأساسية كما ينبغي؟',
	'articlefeedbackv5-field-complete-tooltip-1' => 'ينقص معظم المعلومات',
	'articlefeedbackv5-field-complete-tooltip-2' => 'به بعض المعلومات',
	'articlefeedbackv5-field-complete-tooltip-3' => 'به معلومات أساسية، لكنها غير منظمة',
	'articlefeedbackv5-field-complete-tooltip-4' => 'به معظم المعلومات الأساسية',
	'articlefeedbackv5-field-complete-tooltip-5' => 'إحاطة بالمفهوم',
	'articlefeedbackv5-field-objective-label' => 'غير متحيز',
	'articlefeedbackv5-field-objective-tip' => 'هل تشعر أن تظهر هذه الصفحة هي تمثيل عادل لجميع وجهات النظر حول هذ الموضوع؟',
	'articlefeedbackv5-field-objective-tooltip-1' => 'تحيز واضح',
	'articlefeedbackv5-field-objective-tooltip-2' => 'شيء من تحيز',
	'articlefeedbackv5-field-objective-tooltip-3' => 'تحيز طفيف',
	'articlefeedbackv5-field-objective-tooltip-4' => 'ما من تحيز واضح',
	'articlefeedbackv5-field-objective-tooltip-5' => 'ما من تحيز',
	'articlefeedbackv5-field-wellwritten-label' => 'مكتوبة بشكل جيد',
	'articlefeedbackv5-field-wellwritten-tip' => 'هل تشعر بأن هذه الصفحة منظمة تنظيماً جيدا ومكتوبة بشكل جيد؟',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'مبهم',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'صعب الفهم',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'وضوح كاف',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'وضوح جيد',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'وضوح كامل',
	'articlefeedbackv5-pitch-reject' => 'ربما لاحقا',
	'articlefeedbackv5-pitch-or' => 'أو',
	'articlefeedbackv5-pitch-thanks' => 'قد حفظ تقييمك فشكرا',
	'articlefeedbackv5-pitch-survey-message' => 'استطلاع بسيط لن يأخذ من وقتك الكثير',
	'articlefeedbackv5-pitch-survey-accept' => 'بدء الاستقصاء',
	'articlefeedbackv5-pitch-join-message' => 'أتريد إنشاء حساب؟',
	'articlefeedbackv5-pitch-join-body' => 'حساب سوف يساعدك على تتبع ما تحرره، والمشاركة في النقاشات، الانضمام إلى المجتمع.',
	'articlefeedbackv5-pitch-join-accept' => 'أنشئ حسابا',
	'articlefeedbackv5-pitch-join-login' => 'لُج',
	'articlefeedbackv5-pitch-edit-message' => 'أتعلم أن بإمكانك تحرير هذه الصفحة؟',
	'articlefeedbackv5-pitch-edit-accept' => 'عدل هذه الصفحة',
	'articlefeedbackv5-survey-message-success' => 'شكرا للمشاركة في الاستطلاع',
	'articlefeedbackv5-survey-message-error' => 'لقد حدث خطأ.
كرر المحاولة لاحقا.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'تقييمات اليوم',
	'articleFeedbackv5-table-caption-dailyhighs' => 'أعلى الصفحات تقييما: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'أدنى الصفحات تقييما: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'أشد الصفحات تغيرا هذا الأسبوع',
	'articleFeedbackv5-table-caption-recentlows' => 'المتدنية حديثا',
	'articleFeedbackv5-table-heading-page' => 'صفحة',
	'articleFeedbackv5-table-heading-average' => 'متوسط',
	'articleFeedbackv5-copy-above-highlow-tables' => 'هذه خاصية قيد التجربة. أعطي تقييمك في [صفحة نقاش $1].',
	'articlefeedbackv5-dashboard-bottom' => "'''تنبيه''': سنستمر في تجربة مختلف طرق التعريف بالمقالات على هذه اللوحة (لوحة القيادة). الآن، توجد على لوحة القيادة المقالات التالية:
* أعلى المقالات تقييما وأدناها: وهي التي قيمت على الأقل عشر مرات في الساعات الأربع والعشرين الأخيرة. المتوسط يحسب بقياس القيمة الوسطى للجميع التقييمات المرسلة في الساعات الأربع والعشرين الأخيرة.
* المتدنية حديثا: وهي ما حصل على ما لا يقل عن سبعين بالمئة (نجمتين) في التقييم على أي تصنيف في الساعات الأربع والعشرين الأخيرة. لا تحتسب إلا المقالات التي حصلت على ما لا يقل عن عشرة تقييمات  في الساعات الأربع والعشرين الأخيرة.",
	'articlefeedbackv5-disable-preference' => 'لا تظهر ودجة تقييم المقالات في الصفحات',
	'articlefeedbackv5-emailcapture-response-body' => 'أهلا!

شكرا لك على رغبتك في المساعدة على تحسين {{SITENAME}}.

أكد بريدك الإلكتروتي بالضغط على الزر أسفله: 

$1

يمكنك أيضا زيارة:

$2

وإدخال رمز التأكيد التالي:

$3

سوف نتصل بك عما قريب لإطلاعك عن كيفية المساعدة على تحسين {{SITENAME}}.

إن لم تكن من قدم هذا الطلب، فلا تبالي بهذه الرسالة ولم نرسل لك رسالة أخرى.

مع أحر التماني، وشكرا،
فريق {{SITENAME}}',
);

/** Aramaic (ܐܪܡܝܐ) */
$messages['arc'] = array(
	'articlefeedbackv5-survey-answer-whyrated-other' => 'ܐܚܪܢܐ',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'ܠܡܢܐ?',
	'articlefeedbackv5-survey-submit' => 'ܫܕܪ',
);

/** Azerbaijani (Azərbaycanca)
 * @author Cekli829
 * @author Vago
 * @author Wertuose
 */
$messages['az'] = array(
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Digər',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Niyə?',
	'articlefeedbackv5-survey-submit' => 'Təsdiq et',
	'articlefeedbackv5-form-panel-explanation' => 'Bu nədir?',
	'articlefeedbackv5-report-panel-title' => 'Səhifənin qiyməti',
	'articlefeedbackv5-report-empty' => 'Qiymət yoxdur',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Yaxşı etibarlı mənbələr',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Əla etibarlı mənbələr',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Məlumatın böyük hissəsi yoxdur',
	'articlefeedbackv5-pitch-join-login' => 'Daxil ol',
	'articleFeedbackv5-table-heading-page' => 'Səhifə',
);

/** Bashkir (Башҡортса)
 * @author Assele
 * @author Roustammr
 */
$messages['ba'] = array(
	'articlefeedbackv5' => 'Мәҡәләне баһалау',
	'articlefeedbackv5-desc' => 'Мәҡәләне баһалау (һынау өсөн)',
	'articlefeedbackv5-survey-question-whyrated' => 'Зинһар, ниңә һеҙ бөгөн был биткә баһа биреүегеҙҙе беҙгә белгертегеҙ (бөтә тап килгән яуаптарҙы билдәләгеҙ):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Минең был биттең дөйөм баһаһына өлөш индергем килде.',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Минең баһам был биттең үҫешенә ыңғай йоғонто яһар, тип өмөт итәм.',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Минең {{SITENAME}} проектына өлөш индергем килде.',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Мин үҙ фекерем менән бүлешергә ярятам',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Бин бөгөн баһа ҡуйманым, әммә был мөмкинлек тураһында үҙ фекеремде ҡалдырырға теләйем',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Башҡа',
	'articlefeedbackv5-survey-question-useful' => 'Ҡуйылған баһалар файҙалы һәм аңлайышлы, тип иҫәпләйһегеҙме?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Ниңә?',
	'articlefeedbackv5-survey-question-comments' => 'Һеҙҙең берәй төрлө өҫтәмә иҫкәрмәләрегеҙ бармы?',
	'articlefeedbackv5-survey-submit' => 'Ебәрергә',
	'articlefeedbackv5-survey-title' => 'Зинһар, бер нисә һорауға яуап бирегеҙ',
	'articlefeedbackv5-survey-thanks' => 'Һорауҙарға яуап биреүегеҙ өсөн рәхмәт.',
	'articlefeedbackv5-error' => 'Хата килеп сыҡты. Зинһар, һуңыраҡ яңынан ҡабатлап ҡарағыҙ.',
	'articlefeedbackv5-form-switch-label' => 'Был битте баһалау',
	'articlefeedbackv5-form-panel-title' => 'Был битте баһалау',
	'articlefeedbackv5-form-panel-clear' => 'Был баһаламаны юйырға',
	'articlefeedbackv5-form-panel-expertise' => 'Мин был һорау менән яҡшы танышмын',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Мин был һорау буйынса юғары белем алғанмын',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Был — минең һөнәремдең өлөшө',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Был — минең оло шәхси мауығыуым',
	'articlefeedbackv5-form-panel-expertise-other' => 'Минең белемем сығанағы бында күрһәтелмәгән',
	'articlefeedbackv5-form-panel-submit' => 'Баһаламаны ебәрергә',
	'articlefeedbackv5-form-panel-success' => 'Уңышлы һаҡланды',
	'articlefeedbackv5-form-panel-expiry-title' => 'Һеҙҙең баһаламаларығыҙ иҫкергән',
	'articlefeedbackv5-form-panel-expiry-message' => 'Зинһар, был битте ҡабаттан ҡарап сығығыҙ һәм яңы баһалама ебәрегеҙ.',
	'articlefeedbackv5-report-switch-label' => 'Биттең баһаламаларын күрһәтергә',
	'articlefeedbackv5-report-panel-title' => 'Биттең баһаламалары',
	'articlefeedbackv5-report-panel-description' => 'Ағымдағы уртаса баһалар.',
	'articlefeedbackv5-report-empty' => 'Баһаламалар юҡ',
	'articlefeedbackv5-report-ratings' => '$1 баһалама',
	'articlefeedbackv5-field-trustworthy-label' => 'Дөрөҫлөк',
	'articlefeedbackv5-field-trustworthy-tip' => 'Һеҙ был биттә етәрлек сығанаҡтар бар һәм сығанаҡтар ышаныслы, тип һанайһығыҙмы?',
	'articlefeedbackv5-field-complete-label' => 'Тулылыҡ',
	'articlefeedbackv5-field-complete-tip' => 'Һеҙ был бит төп һорауҙарҙы етәрлек кимәлдә аса, тип һанайһығыҙмы?',
	'articlefeedbackv5-field-objective-label' => 'Битарафлыҡ',
	'articlefeedbackv5-field-objective-tip' => 'Һеҙ был бит ҡағылған һорау буйынса бөтә фекерҙәрҙе лә ғәҙел сағылдыра, тип һанайһығыҙмы?',
	'articlefeedbackv5-field-wellwritten-label' => 'Уҡымлылыҡ',
	'articlefeedbackv5-field-wellwritten-tip' => 'Һеҙ был бит яҡшы ойошторолған һәм яҡшы яҙылған, тип һанайһығыҙмы?',
	'articlefeedbackv5-pitch-reject' => 'Бәлки, һуңғараҡ',
	'articlefeedbackv5-pitch-or' => 'йәки',
	'articlefeedbackv5-pitch-thanks' => 'Рәхмәт! Һеҙҙең баһаламағыҙ һаҡланды.',
	'articlefeedbackv5-pitch-survey-message' => 'Зинһар, ҡыҫҡа баһалама үткәреү өсөн бер аҙ ваҡыт бүлегеҙ.',
	'articlefeedbackv5-pitch-survey-accept' => 'Башларға',
	'articlefeedbackv5-pitch-join-message' => 'Иҫәп яҙмаһын булдырырға теләр инегеҙме?',
	'articlefeedbackv5-pitch-join-body' => 'Иҫәп яҙмаһы һеҙгә үҙгәртеүҙәрегеҙҙе күҙәтергә, фекер алышыуҙарҙа ҡатнашырға һәм берләшмәнең өлөшө булып торорға ярҙам итәсәк.',
	'articlefeedbackv5-pitch-join-accept' => 'Иҫәп яҙмаһын булдырырға',
	'articlefeedbackv5-pitch-join-login' => 'Танылыу',
	'articlefeedbackv5-pitch-edit-message' => 'Һеҙ был битте мөхәррирләп була икәнен беләһегеҙме?',
	'articlefeedbackv5-pitch-edit-accept' => 'Был битте үҙгәртергә',
	'articlefeedbackv5-survey-message-success' => 'Һорауҙарға яуап биреүегеҙ өсөн рәхмәт.',
	'articlefeedbackv5-survey-message-error' => 'Хата килеп сыҡты. Зинһар, һуңыраҡ яңынан ҡабатлап ҡарағыҙ.',
	'articleFeedbackv5-table-heading-page' => 'Бит',
);

/** Belarusian (Taraškievica orthography) (‪Беларуская (тарашкевіца)‬)
 * @author EugeneZelenko
 * @author Jim-by
 * @author Wizardist
 */
$messages['be-tarask'] = array(
	'articlefeedbackv5' => 'Дошка адзнакі артыкулаў',
	'articlefeedbackv5-desc' => 'Адзнака артыкулаў (пачатковая вэрсія)',
	'articlefeedbackv5-survey-question-origin' => 'На якой старонцы Вы знаходзіліся, калі пачалося апытаньне?',
	'articlefeedbackv5-survey-question-whyrated' => 'Калі ласка, паведаміце нам, чаму Вы адзначылі сёньня гэтую старонку (пазначце ўсе падыходзячыя варыянты):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Я жадаю зрабіць унёсак у агульную адзнаку старонкі',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Я спадзяюся, што мая адзнака пазытыўна паўплывае на разьвіцьцё старонкі',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Я жадаю садзейнічаць разьвіцьцю {{GRAMMAR:родны|{{SITENAME}}}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Я жадаю падзяліцца маім пунктам гледжаньня',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Я не адзначыў сёньня, але хацеў даць водгук пра гэтую магчымасьць',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Іншае',
	'articlefeedbackv5-survey-question-useful' => 'Вы верыце, што пададзеныя адзнакі карысныя і зразумелыя?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Чаму?',
	'articlefeedbackv5-survey-question-comments' => 'Вы маеце якія-небудзь дадатковыя камэнтары?',
	'articlefeedbackv5-survey-submit' => 'Даслаць',
	'articlefeedbackv5-survey-title' => 'Калі ласка, адкажыце на некалькі пытаньняў',
	'articlefeedbackv5-survey-thanks' => 'Дзякуй за адказы на пытаньні.',
	'articlefeedbackv5-survey-disclaimer' => 'Дасылаючы, Вы згаджаецеся на распаўсюджаньне на [http://wikimediafoundation.org/wiki/Feedback_privacy_statement ўмовах]',
	'articlefeedbackv5-error' => 'Узьнікла памылка. Калі ласка, паспрабуйце потым',
	'articlefeedbackv5-form-switch-label' => 'Адзначце гэтую старонку',
	'articlefeedbackv5-form-panel-title' => 'Адзначце гэтую старонку',
	'articlefeedbackv5-form-panel-explanation' => 'Што гэта?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:Адзнака артыкула',
	'articlefeedbackv5-form-panel-clear' => 'Выдаліць гэтую адзнаку',
	'articlefeedbackv5-form-panel-expertise' => 'Я маю значныя веды па гэтай тэме (па жаданьні)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Я маю адпаведную ступень вышэйшай адукацыі',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Гэта частка маёй прафэсіі',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Гэта мая асабістая жарсьць',
	'articlefeedbackv5-form-panel-expertise-other' => 'Крыніцы маіх ведаў няма ў гэтым сьпісе',
	'articlefeedbackv5-form-panel-helpimprove' => 'Я жадаю дапамагчы палепшыць {{GRAMMAR:вінавальны|{{SITENAME}}}}, дашліце мне ліст (па жаданьні)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Вам будзе дасланы ліст з пацьверджаньнем. Ваш адрас ня будзе разгалошаны. $1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'водгук пра правілы адносна прыватнасьці',
	'articlefeedbackv5-form-panel-submit' => 'Даслаць адзнакі',
	'articlefeedbackv5-form-panel-pending' => 'Вашыя адзнакі не адпраўленыя',
	'articlefeedbackv5-form-panel-success' => 'Пасьпяхова захаваны',
	'articlefeedbackv5-form-panel-expiry-title' => 'Вашыя адзнакі састарэлі',
	'articlefeedbackv5-form-panel-expiry-message' => 'Калі ласка, адзначце зноў гэтую старонку і дашліце новыя адзнакі.',
	'articlefeedbackv5-report-switch-label' => 'Паказаць адзнакі старонкі',
	'articlefeedbackv5-report-panel-title' => 'Адзнакі старонкі',
	'articlefeedbackv5-report-panel-description' => 'Цяперашнія сярэднія адзнакі.',
	'articlefeedbackv5-report-empty' => 'Адзнакаў няма',
	'articlefeedbackv5-report-ratings' => '$1 {{PLURAL:$1|адзнака|адзнакі|адзнакаў}}',
	'articlefeedbackv5-field-trustworthy-label' => 'Надзейны',
	'articlefeedbackv5-field-trustworthy-tip' => 'Вы лічыце, што гэтая старонка мае дастаткова цытатаў, і яны паходзяць з крыніц вартых даверу?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Няма аўтарытэтных крыніцаў',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Няшмат аўтарытэтных крыніцаў',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Дастаткова аўтарытэтных крыніцаў',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Добрыя аўтарытэтныя крыніцы',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Выдатныя аўтарытэтныя крыніцы',
	'articlefeedbackv5-field-complete-label' => 'Скончанасьць',
	'articlefeedbackv5-field-complete-tip' => 'Вы лічыце, што гэтая старонка раскрывае асноўныя пытаньні тэмы як сьлед?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Большая частка інфармацыі адсутнічае',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Утрымлівае некаторую інфармацыю',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Утрымлівае ключавую інфармацыю, але ёсьць пропускі',
	'articlefeedbackv5-field-complete-tooltip-4' => 'Утрымлівае самую важную інфармацыю',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Вычарпальны ахоп тэмы',
	'articlefeedbackv5-field-objective-label' => "Аб'ектыўны",
	'articlefeedbackv5-field-objective-tip' => 'Вы лічыце, што на гэтай старонцы адлюстраваныя усе пункты гледжаньня на пытаньне?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Вельмі тэндэнцыйны',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Памяркоўна тэндэнцыйны',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Крыху тэндэнцыйны',
	'articlefeedbackv5-field-objective-tooltip-4' => 'Няма адназначнай тэндэнцыйнасьці',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Поўнасьцю бесстароньні',
	'articlefeedbackv5-field-wellwritten-label' => 'Добра напісаны',
	'articlefeedbackv5-field-wellwritten-tip' => 'Вы лічыце, што гэтая старонка добра арганізаваная і добра напісаная?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Незразумелая',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Складаная для зразуменьня',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Дастаткова зразумелая',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Добра зразумелая',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Выключна добра зразумелая',
	'articlefeedbackv5-pitch-reject' => 'Можа потым',
	'articlefeedbackv5-pitch-or' => 'ці',
	'articlefeedbackv5-pitch-thanks' => 'Дзякуй! Вашая адзнака была захаваная.',
	'articlefeedbackv5-pitch-survey-message' => 'Калі ласка, знайдзіце час каб прыняць удзел у невялікім апытаньні.',
	'articlefeedbackv5-pitch-survey-accept' => 'Пачаць апытаньне',
	'articlefeedbackv5-pitch-join-message' => 'Вы жадаеце стварыць рахунак?',
	'articlefeedbackv5-pitch-join-body' => 'Рахунак дапаможа Вам сачыць за Вашымі рэдагаваньнямі, удзельнічаць у абмеркаваньнях і быць часткай супольнасьці.',
	'articlefeedbackv5-pitch-join-accept' => 'Стварыць рахунак',
	'articlefeedbackv5-pitch-join-login' => 'Увайсьці ў сыстэму',
	'articlefeedbackv5-pitch-edit-message' => 'Вы ведалі, што можаце рэдагаваць гэтую старонку?',
	'articlefeedbackv5-pitch-edit-accept' => 'Рэдагаваць гэтую старонку',
	'articlefeedbackv5-survey-message-success' => 'Дзякуй за адказы на гэтае апытаньне.',
	'articlefeedbackv5-survey-message-error' => 'Узьнікла памылка.
Калі ласка, паспрабуйце потым.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Сёньняшнія ўзьлёты і падзеньні',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Артыкулы з найвышэйшымі адзнакамі: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Артыкулы з найніжэйшымі адзнакамі: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Найбольш зьмененыя на гэтым тыдні',
	'articleFeedbackv5-table-caption-recentlows' => 'Апошнія падзеньні',
	'articleFeedbackv5-table-heading-page' => 'Старонка',
	'articleFeedbackv5-table-heading-average' => 'Сярэдняе',
	'articleFeedbackv5-copy-above-highlow-tables' => 'Гэта экспэрымэнтальная магчымасьць. Калі ласка, падайце Ваш водгук на [$1 старонцы абмеркаваньня].',
	'articlefeedbackv5-dashboard-bottom' => "'''Заўвага''': Мы ўсё яшчэ працягваем экспэрымэнтаваць з апрацоўкай артыкулаў на гэтых пляцоўках.  У цяперашні час пляцоўкі ўтрымліваюць наступныя артыкулы:
* Старонкі з вышэйшымі/ніжэйшымі адзнакамі: артыкулы, якія атрымалі ня менш 10 адзнакаў за апошнія 24 гадзіны. Сярэдняя адзнака вылічаная на падставе усіх адзнакаў атрыманых за апошнія 24 гадзіны.
* Апошнія самыя нізкія адзнакі: артыкулы, якія маюць 70% ці болей нізкіх (2 зоркі ці ніжэй) адзнакаў у любой катэгорыі за апошнія 24 гадзіны. Улічваюцца толькі артыкулы якія атрымалі ня менш 10 адзнакаў за апошнія 24 гадзіны.",
	'articlefeedbackv5-disable-preference' => 'Не паказваць на старонках віджэт адзнакі артыкула',
	'articlefeedbackv5-emailcapture-response-body' => 'Вітаем!

Дзякуй, за дапамогу ў паляпшэньні {{GRAMMAR:родны|{{SITENAME}}}}.

Калі ласка, знайдзіце час каб пацьвердзіць Ваш адрас электроннай пошты. Перайдзіце па спасылцы пададзенай ніжэй: 

$1

Таксама, Вы можаце наведаць:

$2

І увесьці наступны код пацьверджаньня:

$3

Хутка мы перададзім Вам інфармацыю, як Вы можаце дапамагчы ў паляпшэньні {{GRAMMAR:родны|{{SITENAME}}}}.

Калі Вы не дасылалі гэты запыт, калі ласка, праігнаруйце гэты ліст, і мы больш не будзем Вас турбаваць.

З найлепшымі пажаданьнямі, і дзякуй Вам,
Каманда {{GRAMMAR:родны|{{SITENAME}}}}',
);

/** Bulgarian (Български)
 * @author DCLXVI
 * @author Spiritia
 * @author Turin
 */
$messages['bg'] = array(
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Исках да допринеса за общата оценка на страницата',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Надявам се, че оценката ми ще се отрази положително върху развитието на страницата',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Исках да допринеса за {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Харесва ми да споделям мнението си',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Друго',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Защо?',
	'articlefeedbackv5-survey-question-comments' => 'Имате ли някакви допълнителни коментари?',
	'articlefeedbackv5-survey-submit' => 'Изпращане',
	'articlefeedbackv5-survey-title' => 'Моля, отговорете на няколко въпроса',
	'articlefeedbackv5-survey-thanks' => 'Благодарим ви, че попълнихте въпросника!',
	'articlefeedbackv5-survey-disclaimer' => 'С цел подобряване на инструмента, мнението ви може да бъде анонимно споделено пред уикипедианската общност.',
	'articlefeedbackv5-form-switch-label' => 'Оценяване на страницата',
	'articlefeedbackv5-form-panel-title' => 'Оценяване на страницата',
	'articlefeedbackv5-form-panel-explanation' => 'Какво е това?',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Защита на личните данни',
	'articlefeedbackv5-report-switch-label' => 'Преглеждане на оценките на страницата',
	'articlefeedbackv5-report-empty' => 'Няма оценки',
	'articlefeedbackv5-pitch-or' => 'или',
	'articlefeedbackv5-pitch-thanks' => 'Благодарности! Вашите оценки бяха съхранени.',
	'articlefeedbackv5-pitch-join-accept' => 'Създаване на сметка',
	'articlefeedbackv5-pitch-join-login' => 'Влизане',
	'articlefeedbackv5-pitch-edit-accept' => 'Редактиране на тази страница',
	'articlefeedbackv5-survey-message-success' => 'Благодарим ви, че попълнихте въпросника!',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Страници с най-високи оценки: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Страници с най-ниски оценки: $1',
	'articleFeedbackv5-table-heading-page' => 'Страница',
	'articleFeedbackv5-copy-above-highlow-tables' => 'Това е експериментална функцоиналност. Можете да дадете мнения и препоръки на [$1 беседата].',
	'articlefeedbackv5-disable-preference' => 'Без показване на притурката за Оценяване на статиите в страниците',
);

/** Bengali (বাংলা)
 * @author Bellayet
 * @author Wikitanvir
 */
$messages['bn'] = array(
	'articlefeedbackv5' => 'নিবন্ধ প্রতিক্রিয়া ড্যাসবোর্ড',
	'articlefeedbackv5-desc' => 'নিবন্ধ প্ররিক্রিয়া',
	'articlefeedbackv5-survey-question-origin' => 'এই জরিপ শুরুর সময় আপনি কোন পাতায় ছিলেন?',
	'articlefeedbackv5-survey-question-whyrated' => 'অনুগ্রহপূর্বক আমাদের বলুন, কেনো আজ আপনি এই পাতাটিকে রেট করলেন (প্রযোজ্য সকল অপশন চেক করুন):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'আমি এই পাতার পূর্ণাঙ্গ রেটিংয়ে অবদান রাখতে চেয়েছিলাম',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'আমি আশা করি আমার রেটিং এই পাতাটির উন্নয়নে ইতিবাচক প্রভাব ফেলবে',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'আমি {{SITENAME}} সাইটে অবদান রাখতে চেয়েছিলাম',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'আমি আমার মতামত জানাতে পছন্দ করি',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'আমি আজকে কোনো রেটিং প্রদান করিনি, কিন্তু সুবিধার ওপর প্রতিক্রিয়া জানাতে চেয়েছিলাম',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'অন্যান্য',
	'articlefeedbackv5-survey-question-useful' => 'আপনি কি মনে করেন যে প্রদানকৃত রেটিংগুলো কার্যকরী ও বোধগম্য?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'কেন?',
	'articlefeedbackv5-survey-question-comments' => 'আপনার কী প্রদান করার মতো আরও কোনো মন্তব্য রয়েছে?',
	'articlefeedbackv5-survey-submit' => 'জমা দাও',
	'articlefeedbackv5-survey-title' => 'অনুগ্রহ করে কয়েকটি প্রশ্নের উত্তর দিন',
	'articlefeedbackv5-survey-thanks' => 'জরিপে অংশ নেওয়ার জন্য আপনাকে ধন্যবাদ।',
	'articlefeedbackv5-survey-disclaimer' => 'এই বৈশিষ্ট্যের আরও উন্নয়নে, আপনার প্রতিক্রিয়া উইকিপিডিয়া সম্প্রদায়ের সাথে বেনামে শেয়ার করা হতে পারে।',
	'articlefeedbackv5-error' => 'একটি ত্রুটি দেখা দিয়েছে। অনুগ্রহ করে পরবর্তীতে আবার চেষ্টা করুন।',
	'articlefeedbackv5-form-switch-label' => 'এই পাতায় রেট করুন',
	'articlefeedbackv5-form-panel-title' => 'এই পাতায় রেট করুন',
	'articlefeedbackv5-form-panel-explanation' => 'এইটি কী?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:ArticleFeedback',
	'articlefeedbackv5-form-panel-clear' => 'এই রেটিং অপসারণ করো',
	'articlefeedbackv5-form-panel-expertise' => 'আমি এই বিষয় সম্পর্কে উচ্চমানের জ্ঞান রাখি (ঐচ্ছিক)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'আমার এই সম্পর্কিত কলেজ/বিশ্ববিদ্যালয় ডিগ্রি রয়েছে',
	'articlefeedbackv5-form-panel-expertise-profession' => 'এটি আমার পেশার অংশ',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'এটি আমার খুবই পছন্দের একটি ব্যাক্তিগত শখ',
	'articlefeedbackv5-form-panel-expertise-other' => 'এ বিষয়ে আমার জ্ঞানের উৎস এই তালিকায় নেই',
	'articlefeedbackv5-form-panel-helpimprove' => 'আমি উইকিপিডিয়ার উন্নয়নে সাহায্য করতে চাই, আমাকে ই-মেইল পাঠান (ঐচ্ছিক)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'আমরা আপনাকে একটি নিশ্চিতকরণ ই-মেইল পাঠাবো। আমরা কারও সাথে আপনার ই-মেইল ঠিকানা শেয়ার করবো না। $1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'গোপনীয়তা নীতি',
	'articlefeedbackv5-form-panel-submit' => 'রেটিং জমা দাও',
	'articlefeedbackv5-form-panel-pending' => 'আপনার রেটিং এখনও জমা হয়নি',
	'articlefeedbackv5-form-panel-success' => 'সফলভাবে সংরক্ষিত',
	'articlefeedbackv5-form-panel-expiry-title' => 'আপনার রেটিং মেয়াদ উত্তীর্ণ হয়ে গেছে',
	'articlefeedbackv5-form-panel-expiry-message' => 'অনুগ্রহ করে এই পাতাটি পূনর্বিবেচনা করুন, এবং নতুন রেটিং প্রদান করুন।',
	'articlefeedbackv5-report-switch-label' => 'পাতার রেটিং দেখাও',
	'articlefeedbackv5-report-panel-title' => 'পাতার রেটিং',
	'articlefeedbackv5-report-panel-description' => 'বর্তমান গড় রেটিং।',
	'articlefeedbackv5-report-empty' => 'রেটিং নেই',
	'articlefeedbackv5-report-ratings' => '$1 রেটিং',
	'articlefeedbackv5-field-trustworthy-label' => 'বিশ্বস্ত',
	'articlefeedbackv5-field-trustworthy-tip' => 'আপনি কী মনে করেন এই পাতায় যথেষ্ট পরিমাণ তথ্যসূত্র রয়েছে এবং সেগুলো বিশ্বস্ত সূত্র হতে এসেছে?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'সুখ্যাত তথ্যসূত্রের অভাব রয়েছে',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'কিছু সুখ্যাত তথ্যসূত্র রয়েছে',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'পর্যাপ্ত সুখ্যাত তথ্যসূত্র রয়েছে',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'ভাল পরিমাণে সুখ্যাত তথ্যসূত্র রয়েছে',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'প্রচুর পরিমাণে সুখ্যাত তথ্যসূত্র রয়েছে',
	'articlefeedbackv5-field-complete-label' => 'সম্পূর্ণ',
	'articlefeedbackv5-field-complete-tip' => 'আপনি কী মনে করেনে এই পাতায় প্রয়োজনীয় সকল বিষয় সম্পর্কে ধারাণা দিতে পেরেছে?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'অধিকাংশ তথ্য অনুপস্থিত',
	'articlefeedbackv5-field-complete-tooltip-2' => 'কিছু তথ্য রয়েছে',
	'articlefeedbackv5-field-complete-tooltip-3' => 'মূল তথ্যগুলো রয়েছে, তবে অনেক শূন্যস্থান রয়েছে',
	'articlefeedbackv5-field-complete-tooltip-4' => 'অধিকাংশ মূল তথ্য রয়েছে',
	'articlefeedbackv5-field-objective-label' => 'উদ্দেশ্য',
	'articlefeedbackv5-field-objective-tip' => 'আপনি কি মনে করেন, এই পাতাটি সকল পক্ষের মতামত বা দৃষ্টিভঙ্গির ন্যায্য উপস্থাপনে সমর্থ হয়েছে?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'অত্যন্ত পক্ষপাতদুষ্ট',
	'articlefeedbackv5-field-objective-tooltip-2' => 'সংযমী পক্ষপাত',
	'articlefeedbackv5-field-objective-tooltip-3' => 'নূন্যতম পক্ষপাত',
	'articlefeedbackv5-field-objective-tooltip-4' => 'সুস্পষ্ট পক্ষপাত নেই',
	'articlefeedbackv5-field-objective-tooltip-5' => 'সম্পূর্ণ নিরপেক্ষ',
	'articlefeedbackv5-field-wellwritten-label' => 'ভালোভাবে লিখিত',
	'articlefeedbackv5-field-wellwritten-tip' => 'আপনি কী মনে করেন এই পাতাটি ভালোভাবে সাজানো ও ভালোভাবে লেখা হয়েছে?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'অবোধ্য',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'বোঝা কঠিন',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'যথেষ্ট স্পষ্ট',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'ভাল স্পষ্ট',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'অসাধারণ স্পষ্ট',
	'articlefeedbackv5-pitch-reject' => 'সম্ভবত পরে',
	'articlefeedbackv5-pitch-or' => 'অথবা',
	'articlefeedbackv5-pitch-thanks' => 'ধন্যবাদ! আপনার রেটিং সংরক্ষিত হয়েছে।',
	'articlefeedbackv5-pitch-survey-message' => 'অনুগ্রহ করে একটি ছোট জরিপ সম্পূর্ণ করতে কিছু সময় ব্যয় করুন।',
	'articlefeedbackv5-pitch-survey-accept' => 'জরিপ শুরু',
	'articlefeedbackv5-pitch-join-message' => 'আপনি কি কোনো অ্যাকাউন্ট তৈরি করতে চান?',
	'articlefeedbackv5-pitch-join-body' => 'একটি অ্যাকাউন্ট আপনার সম্পাদনাগুলো অনুসরণ করতে, আলোচনায় অংশ নিতে, এবং সম্প্রদায়ের অংশ হতে আপনাকে সাহায্য করবে।',
	'articlefeedbackv5-pitch-join-accept' => 'অ্যাকাউন্ট তৈরি করুন',
	'articlefeedbackv5-pitch-join-login' => 'প্রবেশ',
	'articlefeedbackv5-pitch-edit-message' => 'আপনি কী জানেন যে আপনি এই পাতা সম্পাদনা করতে পারেন?',
	'articlefeedbackv5-pitch-edit-accept' => 'পাতাটি সম্পাদনা করুন',
	'articlefeedbackv5-survey-message-success' => 'জরিপটিতে অংশ নেওয়ার জন্য আপনাকে ধন্যবাদ।',
	'articlefeedbackv5-survey-message-error' => 'একটি ত্রুটি দেখা দিয়েছে।
অনুগ্রহ করে পরবর্তীতে আবার চেষ্টা করুন।',
	'articleFeedbackv5-table-heading-page' => 'পাতা',
	'articleFeedbackv5-table-heading-average' => 'গড়',
	'articlefeedbackv5-disable-preference' => 'পাতায় নিবন্ধ প্রতিক্রিয়া উইজেটটি দেখিও না',
);

/** Breton (Brezhoneg)
 * @author Fohanno
 * @author Fulup
 * @author Gwendal
 * @author Y-M D
 */
$messages['br'] = array(
	'articlefeedbackv5' => 'Taolenn vourzh priziañ ar pennad',
	'articlefeedbackv5-desc' => 'Priziadenn pennadoù (stumm stur)',
	'articlefeedbackv5-survey-question-origin' => "E peseurt pajenn e oac'h p'hoc'h eus kroget gant an enselladenn-mañ ?",
	'articlefeedbackv5-survey-question-whyrated' => "Roit deomp an abeg d'ar perak ho peus priziet ar bajenn-mañ hiziv (kevaskit an abegoù gwirion) :",
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => "C'hoant em boa da reiñ sikour evit priziañ ar bajenn en ur mod hollek",
	'articlefeedbackv5-survey-answer-whyrated-development' => "Spi am eus e servijo d'un doare pozitivel ma friziadenn evit dioreiñ ar bajenn",
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => "C'hoant em boa da gemer perzh e {{SITENAME}}",
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Plijout a ra din reiñ ma ali',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => "N'am eus ket priziet ar bajenn hiziv, reiñ ma soñj diwar-benn an arc'hweladur an hini eo",
	'articlefeedbackv5-survey-answer-whyrated-other' => 'All',
	'articlefeedbackv5-survey-question-useful' => "Hag-eñ e soñjoc'h ez eo ar briziadennoù roet talvoudus ha sklaer ?",
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Perak ?',
	'articlefeedbackv5-survey-question-comments' => 'Evezhiadennoù all ho pefe ?',
	'articlefeedbackv5-survey-submit' => 'Kas',
	'articlefeedbackv5-survey-title' => "Trugarez da respont d'un nebeut goulennoù",
	'articlefeedbackv5-survey-thanks' => 'Trugarez da vezañ leuniet ar goulennaoueg.',
	'articlefeedbackv5-error' => "C'hoarvezet ez eus ur fazi. Esaeit en-dro diwezhtaoc'h, mar plij.",
	'articlefeedbackv5-form-switch-label' => "Reiñ un notenn d'ar bajenn-mañ",
	'articlefeedbackv5-form-panel-title' => "Reiñ un notenn d'ar bajenn-mañ",
	'articlefeedbackv5-form-panel-explanation' => 'Petra eo se ?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:ArticleFeedback',
	'articlefeedbackv5-form-panel-clear' => 'Lemel an notenn-mañ',
	'articlefeedbackv5-form-panel-expertise' => 'Gouzout a ran mat-tre diouzh an danvez-se (diret)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Un diplom skol-veur pe skol-uhel a zere am eus tapet',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Ul lodenn eus ma micher eo',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Dik on gant an danvez-se ent personel',
	'articlefeedbackv5-form-panel-expertise-other' => "Orin ma anaouedegezh n'eo ket renablet aze",
	'articlefeedbackv5-form-panel-helpimprove' => 'Me a garfe skoazellañ da wellaat Wikipedia, kasit din ur postel (diret)',
	'articlefeedbackv5-form-panel-helpimprove-note' => "Kaset e vo deoc'h ur chomlec'h kadarnaat. Ne vo ket kaset ho chomlec'h postel da zen ebet. $1",
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Reolennoù prevezded',
	'articlefeedbackv5-form-panel-submit' => 'Kas ar priziadennoù',
	'articlefeedbackv5-form-panel-pending' => "N'eo ket bet kaset ho priziadenn evit c'hoazh",
	'articlefeedbackv5-form-panel-success' => 'Enrollet ervat',
	'articlefeedbackv5-form-panel-expiry-title' => "Aet eo ho priziadenn d'he zermen",
	'articlefeedbackv5-form-panel-expiry-message' => 'Adpriziit ar bajenn-mañ ha kasit en-dro ho priziadenn nevez.',
	'articlefeedbackv5-report-switch-label' => 'Gwelet notennoù ar bajenn',
	'articlefeedbackv5-report-panel-title' => 'Priziadennoù ar bajenn',
	'articlefeedbackv5-report-panel-description' => 'Notennoù keitat evit ar mare.',
	'articlefeedbackv5-report-empty' => 'Priziadenn ebet',
	'articlefeedbackv5-report-ratings' => '$1 priziadenn',
	'articlefeedbackv5-field-trustworthy-label' => 'A fiziañs',
	'articlefeedbackv5-field-trustworthy-tip' => "Ha soñjal a ra deoc'h ez eus arroudennoù a-walc'h er bajenn-mañ ? Ha diwar mammennoù sirius e teuont ?",
	'articlefeedbackv5-field-trustworthy-tooltip-1' => "Mankout a ra mammennoù a-feson a c'hallfed fiziout warno",
	'articlefeedbackv5-field-trustworthy-tooltip-2' => "Nebeut a vammennoù a c'hallfed fiziout warno",
	'articlefeedbackv5-field-trustworthy-tooltip-3' => "Mammennoù a c'haller fiziout warno zo, evel m'eo dleet",
	'articlefeedbackv5-field-trustworthy-tooltip-4' => "Mammennoù a-feson a c'haller fiziout warno",
	'articlefeedbackv5-field-trustworthy-tooltip-5' => "Mammennoù eus an dibab a c'haller fiziout warno",
	'articlefeedbackv5-field-complete-label' => 'Graet',
	'articlefeedbackv5-field-complete-tip' => "Ha soñjal a ra deoc'h e vez graet mat tro temoù pennañ ar sujed ?",
	'articlefeedbackv5-field-complete-tooltip-1' => 'Mankout a ra ar pep brasañ eus an titouroù',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Tammoù titouroù zo',
	'articlefeedbackv5-field-complete-tooltip-3' => 'E-barzh emañ an titouroù pennañ met mankoù zo ivez',
	'articlefeedbackv5-field-complete-tooltip-4' => 'E-barzh emañ ar pep brasañ eus an titouroù pennañ',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Klok eo an teul',
	'articlefeedbackv5-field-objective-label' => 'Diuntu',
	'articlefeedbackv5-field-objective-tip' => "Ha soñjal a ra deoc'h e vez kavet displeget er bajenn-mañ, en un doare reizh a-walc'h, holl tuioù ar sujed ?",
	'articlefeedbackv5-field-wellwritten-label' => 'Skrivet brav',
	'articlefeedbackv5-field-wellwritten-tip' => "Ha soñjal a ra deoc'h eo skrivet brav ha frammet mat ar bajenn-mañ ?",
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Digomprenus',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Diaes da gompren',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => "Sklaer a-walc'h",
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Sklaer-kenañ',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Peursklaer',
	'articlefeedbackv5-pitch-reject' => "Diwezhatoc'hik marteze",
	'articlefeedbackv5-pitch-or' => 'pe',
	'articlefeedbackv5-pitch-thanks' => 'Trugarez ! Enrollet eo bet ho priziadenn.',
	'articlefeedbackv5-pitch-survey-message' => "Tapit un tammig amzer da respont d'ur sontadeg vihan.",
	'articlefeedbackv5-pitch-survey-accept' => 'Kregiñ gant an enklask',
	'articlefeedbackv5-pitch-join-message' => "Krouiñ ur gont a felle deoc'h ober ?",
	'articlefeedbackv5-pitch-join-body' => "Gant ur gont e c'hallot heuliañ ar c'hemmoù degaset ganeoc'h, kemer perzh e kaozeadennoù ha bezañ ezel eus ar gumuniezh.",
	'articlefeedbackv5-pitch-join-accept' => 'Krouiñ ur gont',
	'articlefeedbackv5-pitch-join-login' => 'Kevreañ',
	'articlefeedbackv5-pitch-edit-message' => "Ha gouzout a rit e c'hallit degas kemmoù war ar bajenn-mañ ?",
	'articlefeedbackv5-pitch-edit-accept' => 'Degas kemmoù war ar bajenn-mañ',
	'articlefeedbackv5-survey-message-success' => 'Trugarez da vezañ leuniet ar goulennaoueg.',
	'articlefeedbackv5-survey-message-error' => "Ur fazi zo bet.
Klaskit en-dro diwezhatoc'h.",
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Berzh ha droukverzh an devezh',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Pajennoù gwellañ priziet : $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Pajennoù priziet an nebeutañ : $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Ar re gemmet ar muiañ er sizhun-mañ',
	'articleFeedbackv5-table-caption-recentlows' => 'Droukverzh nevesañ',
	'articleFeedbackv5-table-heading-page' => 'Pajenn',
	'articleFeedbackv5-table-heading-average' => 'Keidenn',
	'articleFeedbackv5-copy-above-highlow-tables' => "Un arc'hwel arnodel eo hemañ. Lakait an evezhiadennoù er [$1 bajenn gaozeal].",
	'articlefeedbackv5-dashboard-bottom' => "'''Notenn''' : Kenderc'hel a raimp da amprouiñ doareoù disheñvel da ginnig ar pennadoù en taolennoù-bourzh-mañ. Evit ar mare emañ enno ar pennadoù da-heul :
* Pajennoù ar gwellañ/fallañ priziet : pennadoù zo bet priziet da nebeutañ 10 gwezh e-kerzh an devezh diwezhañ. C'hoarvezout a ra ar c'heidennoù diwar jediñ keidenn an holl briziadennoù bet abaoe 24 eurvezh.
* Pennadoù a zisplij : pennadoù bet priziet gant 2 steredenn pe nebeutoc'h, e-pad 70 % eus an amzer pe pelloc'h, ne vern o rummad e-pad ar 24 eurvezh tremenet. Ne sell nemet ouzh ar pennadoù bet priziet da nebeutañ 10 gwezh e-pad ar 24 eurvezh diwezhañ.",
	'articlefeedbackv5-disable-preference' => 'Arabat diskwel ar bitrak Priziañ ar pennadoù er pajennoù.',
	'articlefeedbackv5-emailcapture-response-body' => "Demat deoc'h !

Trugarez deoc'h da vezañ diskouezet bezañ dedennet d'hor skoazellañ evit gwellaat {{SITENAME}}.

Kemerit ur pennadig amzer evit kadarnaat ho chomlec'h postel en ur glikañ war al liamm a-is : 

$1

Gallout a rit ivez mont da welet :

$2

Ha merkañ ar c'hod kadarnaat da-heul :

$3

A-barzh pell ez aimp e darempred ganeoc'h evit ho skoazellañ da wellaat {{SITENAME}}.

Ma n'eo ket deuet ar goulenn ganeoc'h, na rit ket van ouzh ar postel-mañ, ne vo ket kaset mann ebet all deoc'h.

A wir galon ganeoc'h ha trugarez deoc'h,
Skipailh {{SITENAME}}",
);

/** Bosnian (Bosanski)
 * @author CERminator
 */
$messages['bs'] = array(
	'articlefeedbackv5' => 'Tabla za ocjenjivanje članaka',
	'articlefeedbackv5-desc' => 'Ocjenjivanje članaka (probna verzija)',
	'articlefeedbackv5-survey-question-origin' => 'Koja je stranica na kojoj ste bili kada ste počeli ovu anketu?',
	'articlefeedbackv5-survey-question-whyrated' => 'Molimo recite nam zašto se ocijenili danas ovu stranicu (označite sve koje se može primijeniti):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Želio sam da pridonesem sveukupnoj ocjeni stranice',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Nadam se da će moja ocjena imati pozitivan odjek na uređivanje stranice',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Želim da pridonosim na projektu {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Volim dijeliti svoje mišljenje',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Nisam dao ocjene danas, ali sam želio da dadnem povratne podatke o mogućnostima',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Ostalo',
	'articlefeedbackv5-survey-question-useful' => 'Da li vjerujete da su date ocjene korisne i jasne?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Zašto?',
	'articlefeedbackv5-survey-question-comments' => 'Da li imate dodatnih komentara?',
	'articlefeedbackv5-survey-submit' => 'Pošalji',
	'articlefeedbackv5-survey-title' => 'Molimo odgovorite na nekoliko pitanja',
	'articlefeedbackv5-survey-thanks' => 'Hvala vam na popunjavanju ankete.',
	'articlefeedbackv5-error' => 'Desila se greška. Molimo pokušajte kasnije.',
	'articlefeedbackv5-form-switch-label' => 'Ocijeni ovu stranicu',
	'articlefeedbackv5-form-panel-title' => 'Ocijeni ovu stranicu',
	'articlefeedbackv5-form-panel-explanation' => 'Šta je ovo?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:OcjenjivanjeČlanaka',
	'articlefeedbackv5-form-panel-clear' => 'Ukloni ovu ocjenu',
	'articlefeedbackv5-form-panel-expertise' => 'Visoko sam obrazovan o ovoj temi (neobavezno)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Imam odgovarajući fakultetsku/univerzitetsku diplomu',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Ovo je dio moje struke',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Ovo je moja duboka lična strast',
	'articlefeedbackv5-form-panel-expertise-other' => 'Izvor mog znanja nije prikazan ovdje',
	'articlefeedbackv5-form-panel-helpimprove' => 'Želio bih pomoći da unaprijedim Wikipediju, pošalji mi e-mail (neobavezno)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Poslat ćemo vam e-mail potvrde. Nećemo dijeliti vašu adresu ni s kim. $1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Politika privatnosti',
	'articlefeedbackv5-form-panel-submit' => 'Pošalji ocjene',
	'articlefeedbackv5-form-panel-pending' => 'Vaše ocjene još nisu poslane',
	'articlefeedbackv5-form-panel-success' => 'Uspješno sačuvano',
	'articlefeedbackv5-form-panel-expiry-title' => 'Vaše ocjene su istekle',
	'articlefeedbackv5-form-panel-expiry-message' => 'Molimo ponovo ocijenite ovu stranicu i pošaljite nove ocjene.',
	'articlefeedbackv5-report-switch-label' => 'Prikaži ocjene stranice',
	'articlefeedbackv5-report-panel-title' => 'Ocjene stranice',
	'articlefeedbackv5-report-panel-description' => 'Trenutni prosječni rejtinzi.',
	'articlefeedbackv5-report-empty' => 'Bez ocjena',
	'articlefeedbackv5-report-ratings' => '$1 ocjena',
	'articlefeedbackv5-field-trustworthy-label' => 'Vjerodostojno',
	'articlefeedbackv5-field-trustworthy-tip' => 'Da li smatrate da ova stranica ima dovoljno izvora i da su oni iz provjerljivih izvora?',
	'articlefeedbackv5-field-complete-label' => 'Završeno',
	'articlefeedbackv5-field-complete-tip' => 'Da li mislite da ova stranica pokriva osnovna područja teme koja bi trebala?',
	'articlefeedbackv5-field-objective-label' => 'Nepristrano',
	'articlefeedbackv5-field-objective-tip' => 'Da li smatrate da ova stranica prikazuje neutralni prikaz iz svih perspektiva o temi?',
	'articlefeedbackv5-field-wellwritten-label' => 'Dobro napisano',
	'articlefeedbackv5-field-wellwritten-tip' => 'Da li mislite da je ova stranica dobro organizirana i dobro napisana?',
	'articlefeedbackv5-pitch-reject' => 'Možda kasnije',
	'articlefeedbackv5-pitch-or' => 'ili',
	'articlefeedbackv5-pitch-thanks' => 'Hvala! Vaše ocjene su spremljene.',
	'articlefeedbackv5-pitch-survey-message' => 'Molimo izdvojite trenutak za ispunite kratku anketu.',
	'articlefeedbackv5-pitch-survey-accept' => 'Započni anketu',
	'articlefeedbackv5-pitch-join-message' => 'Da li želite napraviti račun?',
	'articlefeedbackv5-pitch-join-body' => 'Račun će vam pomoći da pratite vaše izmjene, da se uključite u razgovore i da budete dio zajednice.',
	'articlefeedbackv5-pitch-join-accept' => 'Napravi račun',
	'articlefeedbackv5-pitch-join-login' => 'Prijavi se',
	'articlefeedbackv5-pitch-edit-message' => 'Da li znate da možete urediti ovu stranicu?',
	'articlefeedbackv5-pitch-edit-accept' => 'Uredite ovu stranicu',
	'articlefeedbackv5-survey-message-success' => 'Hvala vam na popunjavanju ankete.',
	'articlefeedbackv5-survey-message-error' => 'Desila se greška.
Molimo pokušajte kasnije.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Današnji najviši i najniži',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Stranice sa najvišim ocjenama: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Stranice sa najnižim ocjenama: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Najviše mijenjano ove sedmice',
	'articleFeedbackv5-table-caption-recentlows' => 'Nedavne najniže ocjene',
	'articleFeedbackv5-table-heading-page' => 'Stranica',
	'articleFeedbackv5-table-heading-average' => 'Prosjek',
	'articleFeedbackv5-copy-above-highlow-tables' => 'Ovo je probna osobina. Molimo da nam pošaljete povratne informacije na [$1 stranicu za razgovor].',
	'articlefeedbackv5-dashboard-bottom' => "'''Napomena''': Mi ćemo nastaviti da probavamo sa raznim načinima prikaza članaka na ovim tablama.  Trenutno, table uključuju slijedeće članke:
* Stranice sa najboljim/najslabijim ocjenama: članke koji imaju najmanje 10 ocjena u posljednja 24 sata.  Prosjeci su računati tako što su izračunati prosjeci svih poslanih ocjena u posljednja 24 sata.
* Nedavne padovi: članci koji su dobili 70% ili manje (2 zvijezde ili niže) ocjene u bilo kojoj kategoriji u posljednja 24 sata. Samo članci koji su dobili najmanje 10 ocjena u posljednja 24 sata su ovdje uključeni.",
	'articlefeedbackv5-disable-preference' => 'Ne prikazuj dodatak Povratne informacije o članku na stranicama',
	'articlefeedbackv5-emailcapture-response-body' => 'Zdravo!

Hvala što ste izrazili zanimanje za poboljšanje {{SITENAME}}.

Molimo vas potvrdite vaš e-mail putem klika na link ispod: 

$1

Također možete posjetiti:

$2

I unijeti slijedeći kod potvrde:

$3

Bit ćemo ubrzo u kontaktu podacima kako možete pomoći oko poboljšanja {{SITENAME}}.

Ako niste inicirali ovaj zahtjev, molimo zanemarite ovaj e-mail i nećemo vam slati ništa više.

Srdačne čestitke i hvala najljepša,
Vaš {{SITENAME}} tim',
);

/** Catalan (Català)
 * @author Aleator
 * @author BroOk
 * @author El libre
 * @author Solde
 * @author Toniher
 */
$messages['ca'] = array(
	'articlefeedbackv5' => "Avaluació de l'article",
	'articlefeedbackv5-desc' => "Avaluació de l'article",
	'articlefeedbackv5-survey-question-whyrated' => "Per favor, diga'ns per què has valorat aquesta pàgina avui (marca totes les opcions que creguis convenient):",
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Vull contribuir a la qualificació global de la pàgina',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Espero que la meva qualificació afecti positivament al desenvolupament de la pàgina',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Volia contribuir a {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Vull compartir la meva opinió',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'No he valorat res avui, però volia donar resposta a la característica',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Altres',
	'articlefeedbackv5-survey-question-useful' => 'Creus que les valoracions proporcionades són útils i clares?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Per què?',
	'articlefeedbackv5-survey-question-comments' => 'Tens algun comentari addicional?',
	'articlefeedbackv5-survey-submit' => 'Trametre',
	'articlefeedbackv5-survey-title' => 'Si us plau, contesti algunes preguntes',
	'articlefeedbackv5-survey-thanks' => "Gràcies per omplir l'enquesta.",
	'articlefeedbackv5-form-switch-label' => 'Proporciona informació',
	'articlefeedbackv5-form-panel-title' => 'Valoreu la pàgina',
	'articlefeedbackv5-form-panel-submit' => 'Envia comentaris',
	'articlefeedbackv5-form-panel-success' => 'Desat correctament',
	'articlefeedbackv5-report-switch-label' => 'Mostra els resultats',
	'articlefeedbackv5-report-panel-title' => 'Resultats dels comentaris',
	'articlefeedbackv5-report-panel-description' => 'Actual mitjana de qualificacions.',
	'articlefeedbackv5-report-empty' => 'No hi ha valoracions',
	'articlefeedbackv5-report-ratings' => '$1 valoracions',
	'articlefeedbackv5-field-trustworthy-label' => 'Digne de confiança',
	'articlefeedbackv5-field-complete-label' => 'Complet',
	'articlefeedbackv5-field-complete-tip' => 'Consideres que aquesta pàgina aborda els temes essencials que havien de ser coberts?',
	'articlefeedbackv5-field-objective-label' => 'Imparcial',
	'articlefeedbackv5-field-objective-tip' => "Creus que aquesta pàgina representa, de forma equilibrada, tots els punts de vista sobre l'assumpte?",
	'articlefeedbackv5-field-wellwritten-label' => 'Ben escrit',
	'articlefeedbackv5-pitch-reject' => 'Potser més tard',
	'articlefeedbackv5-pitch-or' => 'o',
	'articlefeedbackv5-pitch-survey-accept' => "Comença l'enquesta",
	'articlefeedbackv5-pitch-join-accept' => 'Crea un compte',
	'articlefeedbackv5-pitch-edit-accept' => 'Comença a editar',
	'articleFeedbackv5-table-heading-page' => 'Pàgina',
	'articleFeedbackv5-table-heading-average' => 'Mitjana',
);

/** Chechen (Нохчийн)
 * @author Sasan700
 */
$messages['ce'] = array(
	'articlefeedbackv5-form-panel-submit' => 'Дlадахьийта хетарг',
);

/** Czech (Česky)
 * @author Jkjk
 * @author Kuvaly
 * @author Mormegil
 * @author Mr. Richard Bolla
 */
$messages['cs'] = array(
	'articlefeedbackv5' => 'Přehled hodnocení článků',
	'articlefeedbackv5-desc' => 'Hodnocení článků (pilotní verze)',
	'articlefeedbackv5-survey-question-origin' => 'Ze které stránky jste {{gender:|přišel|přišla|přišli}} na tento průzkum?',
	'articlefeedbackv5-survey-question-whyrated' => 'Proč jste dnes hodnotili tuto stránku (zaškrtněte všechny platné možnosti)?',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Chtěl jsem ovlivnit výsledné ohodnocení stránky',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Doufám, že mé hodnocení pozitivně ovlivní budoucí vývoj stránky',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Chtěl jsem pomoci {{grammar:3sg|{{SITENAME}}}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Rád sděluji svůj názor',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Dnes jsem nehodnotil, ale chtěl jsem poskytnout svůj názor na tuto funkci',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Jiný důvod',
	'articlefeedbackv5-survey-question-useful' => 'Myslíte si, že poskytovaná hodnocení jsou užitečná a pochopitelná?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Proč?',
	'articlefeedbackv5-survey-question-comments' => 'Máte nějaké další komentáře?',
	'articlefeedbackv5-survey-submit' => 'Odeslat',
	'articlefeedbackv5-survey-title' => 'Odpovězte prosím na několik otázek',
	'articlefeedbackv5-survey-thanks' => 'Děkujeme za vyplnění průzkumu.',
	'articlefeedbackv5-survey-disclaimer' => 'V zájmu zlepšení této funkce může být váš názor anonymně sdílen s komunitou Wikipedie.',
	'articlefeedbackv5-error' => 'Došlo k chybě. Zkuste to prosím později.',
	'articlefeedbackv5-form-switch-label' => 'Hodnoťte tuto stránku',
	'articlefeedbackv5-form-panel-title' => 'Ohodnoťte tuto stránku',
	'articlefeedbackv5-form-panel-explanation' => 'Co tohle je?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:Hodnocení článků',
	'articlefeedbackv5-form-panel-clear' => 'Odstranit hodnocení',
	'articlefeedbackv5-form-panel-expertise' => 'Mám rozsáhlé znalosti tohoto tématu (nepovinné)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Mám příslušný vysokoškolský titul',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Jde o součást mé profese',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Je to můj velký koníček',
	'articlefeedbackv5-form-panel-expertise-other' => 'Původ mých znalostí zde není uveden',
	'articlefeedbackv5-form-panel-helpimprove' => 'Rád bych pomohl vylepšit Wikipedii, pošlete mi e-mail (nepovinné)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Pošleme vám potvrzovací e-mail. Vaši e-mailovou adresu nikomu neposkytneme. $1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Zásady ochrany osobních údajů',
	'articlefeedbackv5-form-panel-submit' => 'Odeslat hodnocení',
	'articlefeedbackv5-form-panel-pending' => 'Vaše hodnocení zatím nebylo odesláno',
	'articlefeedbackv5-form-panel-success' => 'Úspěšně uloženo',
	'articlefeedbackv5-form-panel-expiry-title' => 'Platnost vašeho hodnocení vypršela',
	'articlefeedbackv5-form-panel-expiry-message' => 'Ohodnoťte prosím stránku znovu a zadejte nové hodnocení.',
	'articlefeedbackv5-report-switch-label' => 'Zobrazit hodnocení',
	'articlefeedbackv5-report-panel-title' => 'Hodnocení stránky',
	'articlefeedbackv5-report-panel-description' => 'Aktuální průměrné hodnocení',
	'articlefeedbackv5-report-empty' => 'Bez hodnocení',
	'articlefeedbackv5-report-ratings' => '$1 hodnocení',
	'articlefeedbackv5-field-trustworthy-label' => 'Důvěryhodnost',
	'articlefeedbackv5-field-trustworthy-tip' => 'Máte pocit, že tato stránka dostatečně odkazuje na zdroje a použité zdroje jsou důvěryhodné?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Chybí věrohodné zdroje',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Málo věrohodných zdrojů',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Postačující věrohodné zdroje',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Kvalitní věrohodné zdroje',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Skvělé věrohodné zdroje',
	'articlefeedbackv5-field-complete-label' => 'Úplnost',
	'articlefeedbackv5-field-complete-tip' => 'Máte pocit, že tato stránka pokrývá všechny důležité části tématu?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Chybí většina informací',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Nějaké informace obsahuje',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Klíčové informace obsahuje, ale s mezerami',
	'articlefeedbackv5-field-complete-tooltip-4' => 'Obsahuje většinu klíčových informací',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Vyčerpávající pokrytí',
	'articlefeedbackv5-field-objective-label' => 'Objektivita',
	'articlefeedbackv5-field-objective-tip' => 'Máte pocit, že tato stránka spravedlivě pokrývá všechny pohledy na dané téma?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Silně zkreslené',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Mírné zkreslení',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Minimální zkreslení',
	'articlefeedbackv5-field-objective-tooltip-4' => 'Bez viditelných zkreslení',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Naprosto nezaujaté',
	'articlefeedbackv5-field-wellwritten-label' => 'Čitelnost',
	'articlefeedbackv5-field-wellwritten-tip' => 'Máte pocit, že tato stránka je správně organizována a dobře napsána?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Nesrozumitelné',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Obtížné pochopit',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Dostatečná srozumitelnost',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Dobrá srozumitelnost',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Výjimečná srozumitelnost',
	'articlefeedbackv5-pitch-reject' => 'Možná později',
	'articlefeedbackv5-pitch-or' => 'nebo',
	'articlefeedbackv5-pitch-thanks' => 'Děkujeme! Vaše hodnocení bylo uloženo.',
	'articlefeedbackv5-pitch-survey-message' => 'Věnujte prosím chvilku vyplnění krátkého průzkumu.',
	'articlefeedbackv5-pitch-survey-accept' => 'Spustit průzkum',
	'articlefeedbackv5-pitch-join-message' => 'Chtěli byste si založit uživatelský účet?',
	'articlefeedbackv5-pitch-join-body' => 'Účet vám umožní sledovat vaše editace, účastnit se diskusí a stát se součástí komunity.',
	'articlefeedbackv5-pitch-join-accept' => 'Založit účet',
	'articlefeedbackv5-pitch-join-login' => 'Přihlásit se',
	'articlefeedbackv5-pitch-edit-message' => 'Věděli jste, že můžete tuto stránku upravit?',
	'articlefeedbackv5-pitch-edit-accept' => 'Editovat stránku',
	'articlefeedbackv5-survey-message-success' => 'Děkujeme za vyplnění dotazníku.',
	'articlefeedbackv5-survey-message-error' => 'Došlo k chybě.
Zkuste to prosím později.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Dnešní maxima a minima',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Stránky s nejvyšším hodnocením: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Stránky s nejnižším hodnocením: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Největší změny tohoto týdne',
	'articleFeedbackv5-table-caption-recentlows' => 'Nedávná minima',
	'articleFeedbackv5-table-heading-page' => 'Stránka',
	'articleFeedbackv5-table-heading-average' => 'Průměr',
	'articleFeedbackv5-copy-above-highlow-tables' => 'Toto je pokusná funkce. Sdělte nám svůj názor na [$1 diskusní stránce].',
	'articlefeedbackv5-dashboard-bottom' => "'''Poznámka''': I nadále budeme experimentovat s různými způsoby zobrazení článků na tomto přehledu. V současné chvíli přehled zahrnuje následující články:
* Stránky s nejvyšším/nejnižším hodnocením: články, které za posledních 24 hodin byly hodnoceny nejméně 10krát. Průměry se počítají ze všech hodnocení odeslaných v posledních 24 hodinách.
* Nedávná minima: články, které mají za posledních 24 hodin 70 % nebo více nízkých hodnocení (2 hvězdičky nebo horší) v libovolné kategorii. Zahrnuty jsou jen články, které byly za posledních 24 hodin hodnoceny nejméně 10krát.",
	'articlefeedbackv5-disable-preference' => 'Nezobrazovat na stránkách komponentu pro hodnocení článků',
	'articlefeedbackv5-emailcapture-response-body' => 'Dobrý den!

Děkujeme za vyjádření zájmu pomoci vylepšit {{grammar:4sg|{{SITENAME}}}}.

Věnujte prosím chvilku potvrzení vaší e-mailové adresy kliknutím na následující odkaz:

$1

Také můžete navštívit:

$2

A zadat následující potvrzovací kód:

$3

Brzy se vám ozveme s informacemi, jak můžete pomoci {{grammar:4sg|{{SITENAME}}}} vylepšit.

Pokud tato žádost nepochází od vás, ignorujte prosím tento e-mail, nic dalšího vám posílat nebudeme.

Děkujeme, s pozdravem
tým {{grammar:2sg|{{SITENAME}}}}',
);

/** Welsh (Cymraeg)
 * @author Pwyll
 */
$messages['cy'] = array(
	'articlefeedbackv5' => 'Dangosfwrdd adborth erthygl',
	'articlefeedbackv5-desc' => 'Adborth am erthygl',
	'articlefeedbackv5-survey-question-origin' => 'Ar ba dudalen oeddech chi pan ddechreuoch chi ar yr holiadur hwn?',
	'articlefeedbackv5-survey-question-whyrated' => "Rhowch wybod i ni pam roeddech chi wedi teilyngu'r dudalen hon heddiw, os gwelwch yn dda (ticiwch bob un sy'n berthnasol):",
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Roeddwn i eisiau cyfrannu at fesur gwerth y dudalen',
	'articlefeedbackv5-survey-answer-whyrated-development' => "Gobeithiaf y bydd fy marn yn effeithio'n gadarnhaol at ddatblygiad y dudalen",
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Roeddwn eisiau cyfrannu at {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => "Dw i'n hoffi mynegi fy marn",
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Ni roddais sgôrau heddiw, ond roeddwn i eisiau rhoi adborth am yr erthygl',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Arall',
	'articlefeedbackv5-survey-question-useful' => "Ydych chi'n credu fod y sgôr a ddarparwyd yn ddefnyddiol a chlir?",
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Pam?',
	'articlefeedbackv5-survey-question-comments' => 'A oes gennych unrhyw sylwadau ychwanegol?',
	'articlefeedbackv5-survey-submit' => 'Cyflwyner',
	'articlefeedbackv5-survey-title' => 'Atebwch ambell gwestiwn os gwelwch yn dda.',
	'articlefeedbackv5-survey-thanks' => "Diolch am gwblhau'r holiadur.",
	'articlefeedbackv5-survey-disclaimer' => "Er mwyn ceisio gwella'r adnodd hwn, mae'n bosib y bydd eich adborth yn cael ei rannu'n anhysbys gyda'r gymuned Wicipedia.",
	'articlefeedbackv5-error' => 'Cafwyd gwall. Ceisiwch eto nes ymlaen os gwelwch yn dda.',
	'articlefeedbackv5-form-switch-label' => "Rhowch sgôr i'r dudalen hon.",
	'articlefeedbackv5-form-panel-title' => "Rhowch sgôr i'r dudalen hon.",
	'articlefeedbackv5-form-panel-explanation' => 'Beth yw hwn?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:ArticleFeedback',
	'articlefeedbackv5-form-panel-clear' => 'Dilëer y sgôr hwn.',
	'articlefeedbackv5-form-panel-expertise' => 'Rwyf yn hynod wybodus am y pwnc hwn (dewisol).',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Mae gennyf radd coleg/prifysgol perthnasol.',
	'articlefeedbackv5-form-panel-expertise-profession' => "Mae'n rhan o'm swyddogaeth",
	'articlefeedbackv5-form-panel-expertise-hobby' => "Mae'n ddiddordeb personol, dwfn.",
	'articlefeedbackv5-form-panel-expertise-other' => 'Ni rhestrir ffynhonnell fy ngwybodaeth yn y fan hon',
	'articlefeedbackv5-form-panel-helpimprove' => 'Hoffwn gynorthwyo i wella Wicipedia, danfonwch e-bost ataf (dewisol)',
	'articlefeedbackv5-form-panel-helpimprove-note' => "Byddwn yn danfon e-bost atoch i gadarnhau. Ni fyddwn yn rhannu'ch cyfeiriad e-bost gydag unrhyw un. $1",
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Polisi preifatrwydd',
	'articlefeedbackv5-form-panel-submit' => 'Cyflwyno sgôr',
	'articlefeedbackv5-form-panel-pending' => "Nid yw'ch sgôr wedi cael ei gyflwyno eto",
	'articlefeedbackv5-form-panel-success' => 'Cadwyd yn llwyddiannus',
	'articlefeedbackv5-form-panel-expiry-title' => "Mae'ch sgôr wedi dirwyn i ben",
	'articlefeedbackv5-form-panel-expiry-message' => 'Ail-werthuswch y dudalen hon a chyflwynwch sgôr newydd os gwelwch yn dda',
	'articlefeedbackv5-report-switch-label' => "Gweld sgôrau'r dudalen",
	'articlefeedbackv5-report-panel-title' => "Sgôrau'r dudalen",
	'articlefeedbackv5-report-panel-description' => 'Sgôrau cyfartalog ar hyn o bryd',
	'articlefeedbackv5-report-empty' => 'Dim sgôr',
	'articlefeedbackv5-report-ratings' => '$1 sgôr',
	'articlefeedbackv5-field-trustworthy-label' => 'Dibynadwy',
	'articlefeedbackv5-field-trustworthy-tip' => "Ydych chi'n credu fod gan y dudalen hon ddigon o gyfeiriadau a bod y cyfeiriadau hynny'n dod o ffynonnellau dibynadwy?",
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Heb ddigon o ffynonnellau dibynadwy',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Ambell ffynhonnell ddibynadwy',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Ffynonnellau dibynadwy digonol',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Ffynonnellau dibynadwy da',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Ffynonnellau dibynadwy rhagorol',
	'articlefeedbackv5-field-complete-label' => 'Cyflawn',
	'articlefeedbackv5-field-complete-tip' => "Ydych chi'n teimlo fod y dudalen hon yn ymdrin â'r meysydd pynciol allweddol a ddylai fod yno?",
	'articlefeedbackv5-field-complete-tooltip-1' => "Gyda'r mwyaf o wybodaeth coll",
	'articlefeedbackv5-field-complete-tooltip-2' => 'Yn cynnwys peth gwybodaeth',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Yn cynnwys y prif wybodaeth, ond gyda bylchau',
	'articlefeedbackv5-field-complete-tooltip-4' => "Yn cynnwys y rhan fwyaf o'r prif wybodaeth",
	'articlefeedbackv5-field-complete-tooltip-5' => 'Ymdriniaeth gynhwysfawr',
	'articlefeedbackv5-field-objective-label' => 'Gwrthrychol',
	'articlefeedbackv5-field-objective-tip' => "Ydych chi'n teimlo fod y dudalen yn ddarlun teg o'r holl safbwyntiau am y pwnc?",
	'articlefeedbackv5-field-objective-tooltip-1' => 'Yn llawn tuedd',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Peth tuedd',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Ychydig bach o duedd',
	'articlefeedbackv5-field-objective-tooltip-4' => 'Dim tuedd amlwg',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Yn gwbl ddi-duedd',
	'articlefeedbackv5-field-wellwritten-label' => "Wedi'i ysgrifennu'n dda",
	'articlefeedbackv5-field-wellwritten-tip' => "Ydych chi'n teimlo fod y dudalen hon wedi'i threfnu a'i hysgrifennu'n llwyddiannus?",
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Annealladwy',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => "Anodd i'w deall",
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Eglurder boddhaol',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Eglurder da',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Hynod eglur',
	'articlefeedbackv5-pitch-reject' => 'Nes ymlaen efallai',
	'articlefeedbackv5-pitch-or' => 'neu',
	'articlefeedbackv5-pitch-thanks' => 'Diolch! Cadwyd eich sgôrau.',
	'articlefeedbackv5-pitch-survey-message' => "A fyddech gystal â threulio ychydig funudau'n cwblhau holiadur byr, os gwelwch yn dda?",
	'articlefeedbackv5-pitch-survey-accept' => "Dechrau'r holiadur",
	'articlefeedbackv5-pitch-join-message' => 'Oeddech chi eisiau creu cyfrif?',
	'articlefeedbackv5-pitch-join-body' => "Bydd cyfrif yn eich galluogi i dracio'ch golygiadau, gymryd rhan mewn trafodaethau, a bod yn rhan o'r gymuned.",
	'articlefeedbackv5-pitch-join-accept' => 'Crëwch gyfrif',
	'articlefeedbackv5-pitch-join-login' => 'Mewngofnodi',
	'articlefeedbackv5-pitch-edit-message' => "Wyddoch chi y gallech chi olygu'r dudalen hon?",
	'articlefeedbackv5-pitch-edit-accept' => 'Golygwch y dudalen hon',
	'articlefeedbackv5-survey-message-success' => "Diolch am gwblhau'r holiadur.",
	'articlefeedbackv5-survey-message-error' => 'Cafwyd gwall. Ceisiwch eto nes ymlaen os gwelwch yn dda.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Uchafbwyntiau ac iselfannau heddiw',
	'articleFeedbackv5-table-caption-dailyhighs' => "Tudalennau gyda'r sgôrau uchaf: $1",
	'articleFeedbackv5-table-caption-dailylows' => "Tudalennau gyda'r sgôrau isaf: $1",
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Newidiadau mwyaf yr wythnos hon',
	'articleFeedbackv5-table-caption-recentlows' => 'Iselfannau diweddar',
	'articleFeedbackv5-table-heading-page' => 'Tudalen',
	'articleFeedbackv5-table-heading-average' => 'Cyfartaledd',
	'articleFeedbackv5-copy-above-highlow-tables' => 'Nodwedd arbrofol yw hon. Darparwch adborth ar [dudalen sgwrs $1] os gwelwch yn dda.',
	'articlefeedbackv5-dashboard-bottom' => "'''Noder''': Byddwn yn parhau i arbrofi gyda ffyrdd gwahanol o gyflwyno erthyglau ar y dangosfyrddau hyn. Ar hyn o bryd, mae'r dangosfyrddau'n cynnwys yr erthyglau canlynol:
* Tudalennau gyda'r sgôrau uchaf/isaf: erthyglau sydd wedi derbyn 10 sgôr o leiaf yn ystod y 24 awr diwethaf. Daw'r cyfartaleddau trwy gymryd y cymedr o'r holl sgôrau a gyflwynwyd yn ystod y 24 awr diwethaf.
* Iselfannau diweddar: erthyglau a gafodd sgôrau o 70% neu'n is (2 seren neu'n is) mewn unrhyw gategori yn ystod y 24 awr diwethaf. Dim ond erthyglau a dderbyniodd 10 sgôr o leiaf yn ystod y 24 awr diwethaf sy'n cael eu cynnwys.",
	'articlefeedbackv5-disable-preference' => 'Peidiwch dangos y teclyn adborth erthygl ar dudalennau.',
	'articlefeedbackv5-emailcapture-response-body' => "Helo!
Diolch am ddangos eich diddordeb i wella {{SITENAME}}.

A fyddech gystal â chadarnhau eich e-bost trwy glicio ar y ddolen isod:
$1

Hefyd gallwch ymweld â:
$2

A nodi'r côd cadarnhau canlynol:
$3

Byddwn ni mewn cysylltiad â chi'n fuan ynglyn â sut y gallwch chi wella {{SITENAME}}.

Os nad oeddech wedi gwneud y cais hwn, anwybyddwch yr e-bost hwn os gwelwch yn dda. Ni fyddwn yn danfon dim byd arall atoch.

Dymuniadau gorau, a diolch,
Tîm {{SITENAME}}",
);

/** Danish (Dansk)
 * @author Peter Alberti
 * @author Sarrus
 */
$messages['da'] = array(
	'articlefeedbackv5-survey-question-origin' => 'Hvilken side var du på, da du startede denne undersøgelse?',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Jeg ville bidrage til {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Jeg kan godt lide at sige min mening',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Andre',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Hvorfor?',
	'articlefeedbackv5-survey-question-comments' => 'Har du nogle yderligere kommentarer?',
	'articlefeedbackv5-survey-submit' => 'Indsend',
	'articlefeedbackv5-survey-title' => 'Vær så venlig at svare på et par spørgsmål',
	'articlefeedbackv5-error' => 'En fejl opstod. Prøv venligst igen senere.',
	'articlefeedbackv5-form-switch-label' => 'Bedøm denne side',
	'articlefeedbackv5-form-panel-title' => 'Bedøm denne side',
	'articlefeedbackv5-form-panel-explanation' => 'Hvad er dette?',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Jeg har en relevant universitetsgrad',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Det er en del af mit erhverv',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Behandling af personlige oplysninger',
	'articlefeedbackv5-form-panel-success' => 'Gemt',
	'articlefeedbackv5-report-empty' => 'Ingen bedømmelser',
	'articlefeedbackv5-report-ratings' => '$1 bedømmelser',
	'articlefeedbackv5-field-trustworthy-label' => 'Pålidelig',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Mangler troværdige kilder',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Få troværdige kilder',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Tilstrækkelige, troværdige kilder',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Gode troværdige kilder',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Fremragende troværdige kilder',
	'articlefeedbackv5-field-complete-label' => 'Fuldstændig',
	'articlefeedbackv5-field-objective-label' => 'Objektiv',
	'articlefeedbackv5-field-wellwritten-label' => 'Velskrevet',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Uforståelig',
	'articlefeedbackv5-pitch-reject' => 'Måske senere',
	'articlefeedbackv5-pitch-or' => 'eller',
	'articlefeedbackv5-pitch-join-message' => 'Ønskede du at oprette en konto?',
	'articlefeedbackv5-pitch-join-accept' => 'Opret en konto',
	'articlefeedbackv5-pitch-join-login' => 'Log ind',
	'articlefeedbackv5-pitch-edit-message' => 'Vidste du, at du kan redigere denne side?',
	'articlefeedbackv5-survey-message-error' => 'En fejl opstod.
Prøv venligst igen senere.',
	'articleFeedbackv5-table-heading-page' => 'Side',
	'articleFeedbackv5-table-heading-average' => 'Gennemsnit',
);

/** German (Deutsch)
 * @author Kghbln
 * @author Metalhead64
 * @author Purodha
 */
$messages['de'] = array(
	'articlefeedbackv5' => 'Arbeits- und Übersichtsseite zu Seiteneinschätzungen',
	'articlefeedbackv5-desc' => 'Ermöglicht die Einschätzung von Seiten (Pilotversion)',
	'articlefeedbackv5-survey-question-origin' => 'Auf welcher Seite befandest du dich zu Anfang dieser Umfrage?',
	'articlefeedbackv5-survey-question-whyrated' => 'Bitte lasse uns wissen, warum du diese Seite heute eingeschätzt hast (Zutreffendes bitte ankreuzen):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Ich wollte mich an der Einschätzung der Seite beteiligen',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Ich hoffe, dass meine Einschätzung die künftige Entwicklung der Seite positiv beeinflusst',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Ich wollte mich an {{SITENAME}} beteiligen',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Ich teile meine Einschätzung gerne mit',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Ich habe heute keine Einschätzung vorgenommen, wollte allerdings eine Rückmeldung zu dieser Funktion zur Einschätzung der Seite geben',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Anderes',
	'articlefeedbackv5-survey-question-useful' => 'Glaubst du, dass die abgegebenen Einschätzungen nützlich und verständlich sind?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Warum?',
	'articlefeedbackv5-survey-question-comments' => 'Hast du noch weitere Anmerkungen?',
	'articlefeedbackv5-survey-submit' => 'Speichern',
	'articlefeedbackv5-survey-title' => 'Bitte beantworte uns ein paar Fragen',
	'articlefeedbackv5-survey-thanks' => 'Vielen Dank für die Teilnahme an der Umfrage.',
	'articlefeedbackv5-survey-disclaimer' => 'Mit dem Speichern erklärst du dich mit diesen $1 einverstanden.',
	'articlefeedbackv5-survey-disclaimerlink' => 'Bedingungen',
	'articlefeedbackv5-error' => 'Ein Fehler ist aufgetreten. Bitte versuche es später erneut.',
	'articlefeedbackv5-form-switch-label' => 'Diese Seite einschätzen',
	'articlefeedbackv5-form-panel-title' => 'Diese Seite einschätzen',
	'articlefeedbackv5-form-panel-explanation' => 'Worum handelt es sich?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:Artikeleinschätzung',
	'articlefeedbackv5-form-panel-clear' => 'Einschätzung entfernen',
	'articlefeedbackv5-form-panel-expertise' => 'Ich habe umfangreiche Kenntnisse zu diesem Thema (optional)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Ich habe einen entsprechenden Abschluss/Hochschulabschluss',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Es ist ein Teil meines Berufes',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Ich habe ein sehr starkes persönliches Interesse an diesem Thema',
	'articlefeedbackv5-form-panel-expertise-other' => 'Die Grund für meine Kenntnisse ist hier nicht aufgeführt',
	'articlefeedbackv5-form-panel-helpimprove' => 'Ich möchte dabei helfen, {{SITENAME}} zu verbessern. Sende mir bitte eine E-Mail. (optional)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Wir werden dir eine Bestätigungs-E-Mail senden. Wir geben deine E-Mail-Adresse, gemäß unserer $1, nicht an Dritte weiter.',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Datenschutzerklärung für Rückmeldungen',
	'articlefeedbackv5-form-panel-submit' => 'Einschätzung senden',
	'articlefeedbackv5-form-panel-pending' => 'Deine Bewertung wurde noch nicht übertragen',
	'articlefeedbackv5-form-panel-success' => 'Erfolgreich gespeichert',
	'articlefeedbackv5-form-panel-expiry-title' => 'Deine Einschätzung liegt zu lange zurück.',
	'articlefeedbackv5-form-panel-expiry-message' => 'Bitte beurteile die Seite erneut und speichere eine neue Einschätzung.',
	'articlefeedbackv5-report-switch-label' => 'Einschätzungen zu dieser Seite ansehen',
	'articlefeedbackv5-report-panel-title' => 'Einschätzungen zu dieser Seite',
	'articlefeedbackv5-report-panel-description' => 'Aktuelle Durchschnittsergebnisse der Einschätzungen',
	'articlefeedbackv5-report-empty' => 'Keine Einschätzungen',
	'articlefeedbackv5-report-ratings' => '$1 Einschätzungen',
	'articlefeedbackv5-field-trustworthy-label' => 'Vertrauenswürdig',
	'articlefeedbackv5-field-trustworthy-tip' => 'Hast du den Eindruck, dass diese Seite über genügend Quellenangaben verfügt und diese zudem aus vertrauenswürdigen Quellen stammen?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Enthält keine seriösen Quellenangaben',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Enthält wenig seriöse Quellenangaben',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Enthält angemessen seriöse Quellen',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Enthält seriöse Quellenangaben',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Enthält sehr seriöse Quellenangaben',
	'articlefeedbackv5-field-complete-label' => 'Vollständig',
	'articlefeedbackv5-field-complete-tip' => 'Hast du den Eindruck, dass diese Seite alle wichtigen Aspekte enthält, die mit dessen Inhalt zusammenhängen?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Enthält kaum Informationen',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Enthält einige Informationen',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Enthält wichtige Information- en, hat aber Lücken',
	'articlefeedbackv5-field-complete-tooltip-4' => 'Enthält die meisten wichtigen Informationen',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Enthält umfassende Informationen',
	'articlefeedbackv5-field-objective-label' => 'Sachlich',
	'articlefeedbackv5-field-objective-tip' => 'Hast du den Eindruck, dass diese Seite eine ausgewogene Darstellung aller mit deren Inhalt verbundenen Aspekte enthält?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Ist sehr einseitig',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Ist mäßig einseitig',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Ist kaum einseitig',
	'articlefeedbackv5-field-objective-tooltip-4' => 'Ist nicht offensichtlich einseitig',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Ist nicht einseitig',
	'articlefeedbackv5-field-wellwritten-label' => 'Gut geschrieben',
	'articlefeedbackv5-field-wellwritten-tip' => 'Hast du den Eindruck, dass diese Seite gut strukturiert sowie geschrieben wurde?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Ist unverständlich',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Ist schwer verständlich',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Ist ausreichend verständlich',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Ist gut verständlich',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Ist außergewöhnlich gut verständlich',
	'articlefeedbackv5-pitch-reject' => 'Vielleicht später',
	'articlefeedbackv5-pitch-or' => 'oder',
	'articlefeedbackv5-pitch-thanks' => 'Vielen Dank! Deine Einschätzung wurde gespeichert.',
	'articlefeedbackv5-pitch-survey-message' => 'Bitte nehme dir einen Moment Zeit, um an einer kurzen Umfrage teilzunehmen.',
	'articlefeedbackv5-pitch-survey-accept' => 'Umfrage starten',
	'articlefeedbackv5-pitch-join-message' => 'Wolltest du ein Benutzerkonto anlegen?',
	'articlefeedbackv5-pitch-join-body' => 'Ein Benutzerkonto hilft dir deine Bearbeitungen besser nachvollziehen zu können, dich einfacher an Diskussionen zu beteiligen sowie ein Teil der Benutzergemeinschaft zu werden.',
	'articlefeedbackv5-pitch-join-accept' => 'Benutzerkonto erstellen',
	'articlefeedbackv5-pitch-join-login' => 'Anmelden',
	'articlefeedbackv5-pitch-edit-message' => 'Wusstest du, dass du diesen Artikel bearbeiten kannst?',
	'articlefeedbackv5-pitch-edit-accept' => 'Diesen Artikel bearbeiten',
	'articlefeedbackv5-survey-message-success' => 'Vielen Dank für die Teilnahme an der Umfrage.',
	'articlefeedbackv5-survey-message-error' => 'Ein Fehler ist aufgetreten.
Bitte später erneut versuchen.',
	'articlefeedbackv5-privacyurl' => 'http://wikimediafoundation.org/wiki/Feedback_privacy_statement',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Heutige Hochs und Tiefs',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Artikel mit den höchsten Bewertungen: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Artikel mit den niedrigsten Bewertungen: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Diese Woche am meisten geändert',
	'articleFeedbackv5-table-caption-recentlows' => 'Aktuelle Tiefs',
	'articleFeedbackv5-table-heading-page' => 'Seite',
	'articleFeedbackv5-table-heading-average' => 'Durchschnitt',
	'articleFeedbackv5-copy-above-highlow-tables' => 'Dies ist ein experimenteller Funktionsbestandteil. Bitte hierzu auf der [$1 Diskussionsseite] eine Rückmeldung geben.',
	'articlefeedbackv5-dashboard-bottom' => "'''Hinweis:''' Wir werden weiterhin unterschiedliche Möglichkeiten ausprobieren, Artikel auf diesen Arbeits- und Übersichtseiten anzuzeigen. Momentan werden hier die folgenden Artikel angezeigt:
* Seiten mit den höchsten/niedrigsten Bewertungen: Artikel, die mindestens zehn Bewertungen während der vergangenen 24 Stunden erhalten haben. Die Durchschnittswerte sind dabei der Mittelwert aller Bewertungen während der vergangenen 24 Stunden.
* Aktuelle schlechte Bewertungen: Artikel, die während der vergangenen 24 Stunden 70 % oder schlechtere Bewertungen (zwei Sterne oder weniger) in jeder der Kategorien erhalten haben. Lediglich Artikel mit wenigstens zehn Bewertungen während der vergangenen 24 Stunden werden dabei einbezogen.",
	'articlefeedbackv5-disable-preference' => 'Das Widget zum Einschätzen von Seiten nicht anzeigen',
	'articlefeedbackv5-emailcapture-response-body' => 'Hallo!

Vielen Dank für dein Interesse an der Verbesserung von {{SITENAME}}.

Bitte nimm dir einen Moment Zeit, deine E-Mail-Adresse zu bestätigen, indem du auf den folgenden Link klickst:

$1

Du kannst auch die folgende Seite besuchen:

$2

Gib dort den nachfolgenden Bestätigungscode ein:

$3

Wir melden uns in Kürze dazu, wie du helfen kannst, {{SITENAME}} zu verbessern.

Sofern du diese Anfrage nicht ausgelöst hast, ignoriere einfach diese E-Mail. Wir werden dir dann nichts mehr zusenden.

Viele Grüße und vielen Dank,
Das {{SITENAME}}-Team',
);

/** German (formal address) (‪Deutsch (Sie-Form)‬)
 * @author Catrope
 * @author Kghbln
 */
$messages['de-formal'] = array(
	'articlefeedbackv5-survey-question-origin' => 'Auf welcher Seite befanden Sie sich zu Anfang dieser Umfrage?',
	'articlefeedbackv5-survey-question-whyrated' => 'Bitte lassen Sie uns wissen, warum Sie diese Seite heute eingeschätzt haben (Zutreffendes bitte ankreuzen):',
	'articlefeedbackv5-survey-question-useful' => 'Glauben Sie, dass die abgegebenen Einschätzungen nützlich und verständlich sind?',
	'articlefeedbackv5-survey-question-comments' => 'Haben Sie noch weitere Anmerkungen?',
	'articlefeedbackv5-survey-title' => 'Bitte beantworten Sie uns ein paar Fragen',
	'articlefeedbackv5-survey-disclaimer' => 'Mit dem Speichern erklären Sie sich mit diesen [http://wikimediafoundation.org/wiki/Feedback_privacy_statement Bedingungen] einverstanden.',
	'articlefeedbackv5-error' => 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.',
	'articlefeedbackv5-form-panel-helpimprove' => 'Ich möchte dabei helfen, {{SITENAME}} zu verbessern. Senden Sie mir bitte eine E-Mail. (optional)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Wir werden Ihnen eine Bestätigungs-E-Mail senden. Wir geben Ihre E-Mail-Adresse, gemäß unserer $1, nicht an Dritte weiter.',
	'articlefeedbackv5-form-panel-pending' => 'Ihre Bewertung wurde noch nicht übertragen',
	'articlefeedbackv5-form-panel-expiry-title' => 'Ihre Einschätzung liegt zu lange zurück.',
	'articlefeedbackv5-form-panel-expiry-message' => 'Bitte beurteilen Sie die Seite erneut und speichern Sie eine neue Einschätzung.',
	'articlefeedbackv5-field-trustworthy-tip' => 'Haben Sie den Eindruck, dass diese Seite über genügend Quellenangaben verfügt und diese zudem aus vertrauenswürdigen Quellen stammen?',
	'articlefeedbackv5-field-complete-tip' => 'Haben Sie den Eindruck, dass diese Seite alle wichtigen Aspekte enthält, die mit dessen Inhalt zusammenhängen?',
	'articlefeedbackv5-field-objective-tip' => 'Haben Sie den Eindruck, dass diese Seite eine ausgewogene Darstellung aller mit dessen Inhalt verbundenen Aspekte enthält?',
	'articlefeedbackv5-field-wellwritten-tip' => 'Haben Sie den Eindruck, dass diese Seite gut strukturiert sowie geschrieben wurde?',
	'articlefeedbackv5-pitch-thanks' => 'Vielen Dank! Ihre Einschätzung wurde gespeichert.',
	'articlefeedbackv5-pitch-survey-message' => 'Bitte nehmen Sie sich einen Moment Zeit, um an einer kurzen Umfrage teilzunehmen.',
	'articlefeedbackv5-pitch-join-message' => 'Wollten Sie ein Benutzerkonto anlegen?',
	'articlefeedbackv5-pitch-edit-message' => 'Wussten Sie, dass Sie diesen Artikel bearbeiten können?',
	'articlefeedbackv5-survey-message-error' => 'Ein Fehler ist aufgetreten.
Bitte versuchen Sie es später erneut.',
	'articlefeedbackv5-emailcapture-response-body' => 'Hallo!

Vielen Dank für Ihr Interesse an der Verbesserung von {{SITENAME}}.

Bitte nehmen Sie sich einen Moment Zeit, Ihre E-Mail-Adresse zu bestätigen, indem Sie auf den folgenden Link klicken:

$1

Sie können auch die folgende Seite besuchen:

$2

Geben Sie dort den nachfolgenden Bestätigungscode ein:

$3

Wir melden uns in Kürze dazu, wie Sie helfen können, {{SITENAME}} zu verbessern.

Sofern Sie diese Anfrage nicht ausgelöst haben, ignorieren Sie einfach diese E-Mail. Wir werden Ihnen dann nichts mehr zusenden.

Viele Grüße und vielen Dank,
Das {{SITENAME}}-Team',
);

/** Lower Sorbian (Dolnoserbski)
 * @author Michawiki
 */
$messages['dsb'] = array(
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Som kśěł k {{SITENAME}} pśinosowaś',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Druge',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Cogodla?',
	'articlefeedbackv5-survey-question-comments' => 'Maš hyšći dalšne komentary?',
	'articlefeedbackv5-survey-submit' => 'Wótpósłaś',
	'articlefeedbackv5-survey-title' => 'Pšosym wótegroń na někotare pšašanja',
	'articlefeedbackv5-error' => 'Zmólka jo nastała. Pšosym wopytaj pózdźej hyšći raz.',
	'articlefeedbackv5-form-panel-expertise' => 'Mam wobšyrne znaśa k toś tej temje (na žycenje)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Som wušu šulu/uniwersitu wótzamknuł',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Jo źěl mójogo pówołanja',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Jo kšuta wósobinska zagóritosć.',
	'articlefeedbackv5-form-panel-expertise-other' => 'Žrědło mójich znajobnosćow njejo how pódane',
	'articlefeedbackv5-report-switch-label' => 'Pogódnośenja boka pokazaś',
	'articlefeedbackv5-report-empty' => 'Žedne pogódnośenja',
	'articlefeedbackv5-report-ratings' => '$1 pogódnosénjow',
	'articlefeedbackv5-field-trustworthy-label' => 'Dowěry gódny',
	'articlefeedbackv5-field-complete-label' => 'Dopołny',
	'articlefeedbackv5-field-wellwritten-label' => 'Derje napisany',
	'articlefeedbackv5-pitch-reject' => 'Snaź pózdźej',
	'articlefeedbackv5-pitch-or' => 'abo',
	'articlefeedbackv5-pitch-join-accept' => 'Konto załožyś',
	'articlefeedbackv5-pitch-join-login' => 'Pśizjawiś',
	'articlefeedbackv5-pitch-edit-accept' => 'Toś ten nastawk wobźěłaś',
	'articlefeedbackv5-survey-message-error' => 'Zmólka jo nastała. Pšosym wopytaj pózdźej hyšći raz.',
);

/** Greek (Ελληνικά)
 * @author Glavkos
 * @author Kiolalis
 * @author Omnipaedista
 * @author ZaDiak
 */
$messages['el'] = array(
	'articlefeedbackv5' => 'Ταμπλό ανατροφοδότησης άρθρου',
	'articlefeedbackv5-desc' => 'Αξιολόγηση Άρθρου (πιλοτική έκδοση)',
	'articlefeedbackv5-survey-question-origin' => 'Σε ποιά σελίδα  ήσασταν όταν ξεκινήσατε αυτή την έρευνα;',
	'articlefeedbackv5-survey-question-whyrated' => 'Bonvolu informigi nin  kial vi taksis ĉi tiun paĝon hodiaŭ (marku ĉion taŭgan):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Mi volis kontribui al la suma taksado de la paĝo',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Mi esperas ke mia takso pozitive influus la disvolvadon de la paĝo',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Mi volis kontribui al {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Plaĉas al mi doni mian opinion.',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Mi ne provizas taksojn hodiaŭ, se volis doni komentojn pri la ilo',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Alia',
	'articlefeedbackv5-survey-question-useful' => 'Ĉu vi konsideras ke la taksoj provizitaj estas utilaj kaj klara?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Kial?',
	'articlefeedbackv5-survey-question-comments' => 'Ĉu vi havas iujn suplementajn komentojn?',
	'articlefeedbackv5-survey-submit' => 'Enigi',
	'articlefeedbackv5-survey-title' => 'Bonvolu respondi al kelkaj demandoj',
	'articlefeedbackv5-survey-thanks' => 'Dankon pro plenumante la enketon.',
	'articlefeedbackv5-survey-disclaimer' => 'Προκειμένου να βελτιωθεί αυτή η λειτουργία, η ανατροφοδότησή σας ενδέχεται να διαμοιραστεί ανώνυμα με την κοινότητα της Βικιπαίδεια.',
	'articlefeedbackv5-error' => 'Παρουσιάστηκε σφάλμα. Παρακαλώ δοκιμάστε αργότερα.',
	'articlefeedbackv5-form-switch-label' => 'Βαθμολογήστε αυτή τη σελίδα',
	'articlefeedbackv5-form-panel-title' => 'Βαθμολογήστε αυτή τη σελίδα',
	'articlefeedbackv5-form-panel-explanation' => 'Τι είναι αυτό;',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:ΑνατροφοδότησηΆρθρου',
	'articlefeedbackv5-form-panel-clear' => 'Καταργήστε αυτή την αξιολόγηση',
	'articlefeedbackv5-form-panel-expertise' => 'Είμαι πολύ καλά πληροφορημένος σχετικά με αυτό το θέμα (προαιρετικό)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Έχω ένα αντίστοιχο πτυχίο κολλεγίου/πανεπιστημίου',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Είναι μέρος του επαγγέλματος μου',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Είναι ένα βαθύ  προσωπικό πάθος',
	'articlefeedbackv5-form-panel-expertise-other' => 'Η πηγή της γνώσης μου δεν αναφέρεται εδώ',
	'articlefeedbackv5-form-panel-helpimprove' => 'Θα ήθελα να συμβάλλω  στη βελτίωση της Wikipedia, στείλτε μου ένα e-mail (προαιρετικά)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Θα σας στείλουμε ένα μήνυμα e-mail για επιβεβαίωση. Δεν θα γνωστοποιήσουμε την ηλεκτρονική σας διεύθυνση σε κανέναν. $1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Πολιτική απορρήτου',
	'articlefeedbackv5-form-panel-submit' => 'Υποβολή βαθμολογιών',
	'articlefeedbackv5-form-panel-pending' => 'Οι βαθμολογήσεις σας δεν έχουν καταχωρηθεί ακόμη',
	'articlefeedbackv5-form-panel-success' => 'Αποθηκεύτηκαν με επιτυχία',
	'articlefeedbackv5-form-panel-expiry-title' => 'Οι βαθμολογήσεις σας έχουν λήξει',
	'articlefeedbackv5-form-panel-expiry-message' => 'Παρακαλούμε να επανεκτιμήσετε αυτή τη σελίδα και να υποβάλετε νέες βαθμολογίες.',
	'articlefeedbackv5-report-switch-label' => 'Δείτε τις βαθμολογήσεις της σελίδας',
	'articlefeedbackv5-report-panel-title' => 'Βαθμολογήσεις σελίδας',
	'articlefeedbackv5-report-panel-description' => 'Τρέχουσες μέσες βαθμολογίες.',
	'articlefeedbackv5-report-empty' => 'Δεν υπάρχουν αξιολογήσεις',
	'articlefeedbackv5-report-ratings' => '$1 αξιολογήσεις',
	'articlefeedbackv5-field-trustworthy-label' => 'Αξιόπιστη',
	'articlefeedbackv5-field-trustworthy-tip' => 'Αισθάνεστε ότι αυτή η σελίδα αυτή έχει επαρκείς παραπομπές και ότι οι παραπομπές προέρχονται από αξιόπιστες πηγές;',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Δεν διαθέτει αξιόπιστες πηγές',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Λίγες αξιόπιστες πηγές',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Επαρκείς αξιόπιστες πηγές',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Καλές αξιόπιστες πηγές',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Πολύ καλές αξιόπιστες πηγές',
	'articlefeedbackv5-field-complete-label' => 'Πλήρης',
	'articlefeedbackv5-field-complete-tip' => 'Πιστεύετε ότι η σελίδα αυτή καλύπτει τις βασικές θεματικές περιοχές που θα έπρεπε;',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Απουσία των περισσότερων πληροφοριών',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Περιέχει μερικές πληροφορίες',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Περιέχει βασικές πληροφορίες, αλλά με κενά',
	'articlefeedbackv5-field-complete-tooltip-4' => 'Περιέχει τις πιο κρίσιμες πληροφορίες',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Πλήρης κάλυψη',
	'articlefeedbackv5-field-objective-label' => 'Στόχος',
	'articlefeedbackv5-field-objective-tip' => 'Αισθάνεστε ότι η σελίδα αυτή δείχνει μια ίση αντιπροσώπευση όλων των πλευρών σε αυτό το θέμα;',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Βαριά προκατειλημμένη',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Μέτρια προκατειλημμένη',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Ελάχιστα προκατειλημμένη',
	'articlefeedbackv5-field-objective-tooltip-4' => 'Καμιά προφανής προκατάληψη',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Εντελώς αμερόληπτη',
	'articlefeedbackv5-field-wellwritten-label' => 'Καλογραμμένο',
	'articlefeedbackv5-field-wellwritten-tip' => 'Αισθάνεστε ότι αυτή η σελίδα είναι καλά οργανωμένη και γραμμένη;',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Ακατανόητο',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Δυσνόητο',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Επαρκής σαφήνεια',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Καλή σαφήνεια',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Εξαιρετική σαφήνεια',
	'articlefeedbackv5-pitch-reject' => 'Ίσως αργότερα',
	'articlefeedbackv5-pitch-or' => 'ή',
	'articlefeedbackv5-pitch-thanks' => 'Ευχαριστώ! Οι βαθμολογίες σας έχουν αποθηκευτεί.',
	'articlefeedbackv5-pitch-survey-message' => 'Αφιερώστε λίγο χρόνο για να συμπληρώσετε μια μικρή έρευνα.',
	'articlefeedbackv5-pitch-survey-accept' => 'Αρχίστε  έρευνα',
	'articlefeedbackv5-pitch-join-message' => 'Μήπως θέλετε να δημιουργήσετε ένα λογαριασμό;',
	'articlefeedbackv5-pitch-join-body' => 'Ένας λογαριασμός θα σας βοηθήσει να παρακολουθείτε τις αλλαγές σας, να πάρετε μέρος σε συζητήσεις, και να είστε μέρος της κοινότητας.',
	'articlefeedbackv5-pitch-join-accept' => 'Δημιουργήστε έναν λογαριασμό',
	'articlefeedbackv5-pitch-join-login' => 'Είσοδος',
	'articlefeedbackv5-pitch-edit-message' => 'Ξέρατε ότι μπορείτε να επεξεργαστείτε αυτή τη σελίδα;',
	'articlefeedbackv5-pitch-edit-accept' => 'Επεξεργαστείτε αυτή τη σελίδα',
	'articlefeedbackv5-survey-message-success' => 'Ευχαριστώ για τη συμπλήρωση της έρευνας.',
	'articlefeedbackv5-survey-message-error' => 'Παρουσιάστηκε ένα σφάλμα.
Προσπαθήστε ξανά αργότερα.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Σημερινά υψηλά και χαμηλά',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Σελίδες με την υψηλότερη βαθμολογία: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Σελίδες με τις χαμηλότερες βαθμολογίες: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Τα πιο αλλαγμένα αυτής της εβδομάδας',
	'articleFeedbackv5-table-caption-recentlows' => 'Πρόσφατα χαμηλά',
	'articleFeedbackv5-table-heading-page' => 'Σελίδα',
	'articleFeedbackv5-table-heading-average' => 'Μέσος όρος',
	'articleFeedbackv5-copy-above-highlow-tables' => 'Αυτό είναι ένα πειραματικό χαρακτηριστικό. Παρακαλώ παράσχετε ανατροφοδότηση στη [$1 σελίδα συζήτησης].',
	'articlefeedbackv5-disable-preference' => 'Να μην εμφανίζεται το εργαλείο ανατροφοδότησης Άρθρων στις σελίδες',
);

/** Esperanto (Esperanto)
 * @author Yekrats
 */
$messages['eo'] = array(
	'articlefeedbackv5' => 'Stirpanelo pri artikolo-komentoj',
	'articlefeedbackv5-desc' => 'Artikola takso (testa versio)',
	'articlefeedbackv5-survey-question-origin' => 'En kiu paĝo vi estis kiam vi komencis la etikedon?',
	'articlefeedbackv5-survey-question-whyrated' => 'Bonvolu informigi nin  kial vi taksis ĉi tiun paĝon hodiaŭ (marku ĉion taŭgan):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Mi volis kontribui al la suma taksado de la paĝo',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Mi esperas ke mia takso pozitive influus la disvolvadon de la paĝo',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Mi volis kontribui al {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Plaĉas al mi doni mian opinion.',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Mi ne provizas taksojn hodiaŭ, se volis doni komentojn pri la ilo',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Alia',
	'articlefeedbackv5-survey-question-useful' => 'Ĉu vi konsideras ke la taksoj provizitaj estas utilaj kaj klara?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Kial?',
	'articlefeedbackv5-survey-question-comments' => 'Ĉu vi havas iujn suplementajn komentojn?',
	'articlefeedbackv5-survey-submit' => 'Enigi',
	'articlefeedbackv5-survey-title' => 'Bonvolu respondi al kelkaj demandoj',
	'articlefeedbackv5-survey-thanks' => 'Dankon pro plenumante la enketon.',
	'articlefeedbackv5-survey-disclaimer' => 'Por helpi plibonigi ĉi tiun econ, via komentaro eble estos donita anonime kun la Vikipedia komunumo.',
	'articlefeedbackv5-error' => 'Eraro okazis. Bonvolu reprovi baldaŭ.',
	'articlefeedbackv5-form-switch-label' => 'Taksi ĉi tiun paĝon',
	'articlefeedbackv5-form-panel-title' => 'Taksi ĉi tiun paĝon',
	'articlefeedbackv5-form-panel-explanation' => 'Kio estas?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:ArticleFeedback',
	'articlefeedbackv5-form-panel-clear' => 'Forigi ĉi tiun taksadon',
	'articlefeedbackv5-form-panel-expertise' => 'Mi estas fake sperta pri ĉi tiu temo (nedeviga)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Mi havas ĉi-teman diplomon de kolegio aŭ universitato',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Ĝi estas parto de mia profesio.',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Ĝi estas profunda persona pasio',
	'articlefeedbackv5-form-panel-expertise-other' => 'La fonto de mia scio ne estas montrita ĉi tie',
	'articlefeedbackv5-form-panel-helpimprove' => 'Mi volus helpi plibonigi Vikipedion; sendu al mi retpoŝton (nedeviga)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Ni sendos al vi konfirmantan retpoŝton. Ni ne donos vian adreson al iu ajn. $1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Regularo pri respekto de la privateco',
	'articlefeedbackv5-form-panel-submit' => 'Sendi taksojn',
	'articlefeedbackv5-form-panel-pending' => 'Viaj taksoj ne jam estas sendita.',
	'articlefeedbackv5-form-panel-success' => 'Sukcese konservita',
	'articlefeedbackv5-form-panel-expiry-title' => 'Viaj taksoj findatiĝis',
	'articlefeedbackv5-form-panel-expiry-message' => 'Bonvolu retaksi ĉi tiun paĝon kaj sendi novajn taksojn.',
	'articlefeedbackv5-report-switch-label' => 'Vidi taksadon de paĝoj',
	'articlefeedbackv5-report-panel-title' => 'Taksado de paĝoj',
	'articlefeedbackv5-report-panel-description' => 'Aktualaj averaĝaj taksoj.',
	'articlefeedbackv5-report-empty' => 'Sen takso',
	'articlefeedbackv5-report-ratings' => '$1 taksoj',
	'articlefeedbackv5-field-trustworthy-label' => 'Fidinda',
	'articlefeedbackv5-field-trustworthy-tip' => 'Ĉu vi opinias ke ĉi tiu paĝo havas sufiĉajn citaĵojn kaj tiuj citaĵoj venas de fidindaj fontoj?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Mankas fidelaj informofontoj',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Malmultaj fidelaj informofontoj',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Sufiĉaj fidelaj informofontoj',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Bonaj fidelaj informofontoj',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Bonegaj fidelaj informofontoj',
	'articlefeedbackv5-field-complete-label' => 'Kompleta',
	'articlefeedbackv5-field-complete-tip' => 'Ĉu vi opinias ke ĉi tiu paĝo kovras la esencan temon de la subjekto?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Mankas preskaŭ ĉiu infomo',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Enhavas iom da informo',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Enhavas gravan informon, sed mankas iom',
	'articlefeedbackv5-field-complete-tooltip-4' => 'Enhavas plej gravan informon',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Ampleksa verko',
	'articlefeedbackv5-field-objective-label' => 'Objektiva',
	'articlefeedbackv5-field-objective-tip' => 'Ĉu vi opinias ke ĉi tiu paĝo montras justan reprezentadon de ĉiuj perspektivoj pri la afero?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Malobjektivega',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Malobjektiva',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Mezoblektiva',
	'articlefeedbackv5-field-objective-tooltip-4' => 'Objektiva',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Objektivega',
	'articlefeedbackv5-field-wellwritten-label' => 'Bone verkita',
	'articlefeedbackv5-field-wellwritten-tip' => 'Ĉu vi opinias ke ĉi tiu paĝo estas bone organizita kaj bone verkita?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Nekomprenebla',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Kofuze',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Sufiĉe klara',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Bone klara',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Bonege klara',
	'articlefeedbackv5-pitch-reject' => 'Eble baldaŭ',
	'articlefeedbackv5-pitch-or' => 'aŭ',
	'articlefeedbackv5-pitch-thanks' => 'Dankon! Viaj taksoj estis konservitaj.',
	'articlefeedbackv5-pitch-survey-message' => 'Bonvolu doni momenton por kompletigi mallongan enketon.',
	'articlefeedbackv5-pitch-survey-accept' => 'Ekfari enketon',
	'articlefeedbackv5-pitch-join-message' => 'Ĉu vi volus krei konton?',
	'articlefeedbackv5-pitch-join-body' => 'Konto helpos al vi atenti viajn redaktojn, interdiskuti, kaj esti parto de la komunumo.',
	'articlefeedbackv5-pitch-join-accept' => 'Krei konton',
	'articlefeedbackv5-pitch-join-login' => 'Ensaluti',
	'articlefeedbackv5-pitch-edit-message' => 'Ĉu vi scias ke vi povas redakti ĉi tiun paĝon?',
	'articlefeedbackv5-pitch-edit-accept' => 'Redakti ĉi tiun paĝon',
	'articlefeedbackv5-survey-message-success' => 'Dankon pro plenumante la enketon.',
	'articlefeedbackv5-survey-message-error' => 'Eraro okazis. 
Bonvolu reprovi baldaŭ.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'La altoj kaj malaltoj hodiaŭ',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Paĝoj kun la plej bonaj taksoj: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Paĝoj kun la plej malbonaj taksoj: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Plej ŝanĝitaj ĉi-semajne',
	'articleFeedbackv5-table-caption-recentlows' => 'Lastatempaj malaltoj',
	'articleFeedbackv5-table-heading-page' => 'Paĝo',
	'articleFeedbackv5-table-heading-average' => 'Averaĝo',
	'articleFeedbackv5-copy-above-highlow-tables' => 'Ĉi tiu estas eksperimenta eco. Bonvolu provizi komentojn en la [$1 diskuto-paĝo].',
	'articlefeedbackv5-dashboard-bottom' => "'''Notu''': Ni eksperimentos plu pri aliaj fojo enmeti artikolojn en kontrolskatoloj. Nune, la kontrolskatoloj inkluzivas la jenaj artikoloj:
* Paĝoj kun la plej bonaj aŭ malbonaj rangoj: artikoloj ricevis almenaŭ 10 taksojn en la lastaj 24 horoj. Averaĝoj estas kalkulitaj laŭ la averaĝaj taskoj faritaj en la lastaj 24 horoj.
* Lastaj malaltaĵoj: Artikoloj ricevantaj 70% aŭ pli malgrandajn (2 steloj aŭ malpli) taksojn en iu kategorio en la lasta 24 horoj. Nur artikoloj ricevantaj almenaŭ 10 taksojn en la lastaj 24 horoj estas inkluzivitaj.",
	'articlefeedbackv5-disable-preference' => 'Ne montri la funkcion pri artikoloj opinioj en paĝoj',
	'articlefeedbackv5-emailcapture-response-body' => 'Saluton!

Dankon por esprimante intereson por helpi plibonigi je {{SITENAME}}.

Bonvolu konfirmi vian retpoŝtadreson klakante la jenan ligilon:

$1

Vi povas ankaŭ viziti:

$2

Kaj enigi la jenan konfirmkodon:

$3

Ni mesaĝos vin baldaŭ pri kiel vi povas plibonigi je {{SITENAME}}.

Se vi ne eksendis ĉi tiun peton, bonvolu ignori ĉi tiu retpoŝto, kaj ni ne sendos al vi ion ajn.

Koran dankon,
La teamo {{SITENAME}}',
);

/** Spanish (Español)
 * @author Dferg
 * @author Drini
 * @author Fitoschido
 * @author Locos epraix
 * @author Mashandy
 * @author Od1n
 * @author Sanbec
 * @author Translationista
 */
$messages['es'] = array(
	'articlefeedbackv5' => 'Panel de evaluación de artículos',
	'articlefeedbackv5-desc' => 'Evaluación del artículo',
	'articlefeedbackv5-survey-question-origin' => '¿En qué página estabas cuando iniciaste esta encuesta?',
	'articlefeedbackv5-survey-question-whyrated' => 'Por favor, dinos por qué decidiste valorar esta página (marca todas las opciones que correspondan):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Deseo contribuir a la calificación global de la página',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Espero que mi calificación afecte de forma positiva al desarrollo de la página',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Quería contribuir a {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Me gusta compartir mi opinión',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'No evalué ninguna página hoy, solo quise comentar acerca de la funcionalidad.',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Otro',
	'articlefeedbackv5-survey-question-useful' => '¿Crees que las valoraciones proporcionadas son útiles y claras?',
	'articlefeedbackv5-survey-question-useful-iffalse' => '¿Por qué?',
	'articlefeedbackv5-survey-question-comments' => '¿Tienes algún comentario adicional?',
	'articlefeedbackv5-survey-submit' => 'Enviar',
	'articlefeedbackv5-survey-title' => 'Por favor, contesta algunas preguntas',
	'articlefeedbackv5-survey-thanks' => 'Gracias por completar la encuesta.',
	'articlefeedbackv5-survey-disclaimer' => 'Para ayudar a mejorar esta característica, sus comentarios podrían compartirse anónimamente con la comunidad de Wikipedia.',
	'articlefeedbackv5-error' => 'Ha ocurrido un error. Por favor inténtalo de nuevo más tarde.',
	'articlefeedbackv5-form-switch-label' => 'Evalúa este artículo',
	'articlefeedbackv5-form-panel-title' => 'Evalúa este artículo',
	'articlefeedbackv5-form-panel-explanation' => '¿Qué es esto?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:EvaluaciónArtículo',
	'articlefeedbackv5-form-panel-clear' => 'Quitar esta evaluación',
	'articlefeedbackv5-form-panel-expertise' => 'Estoy muy bien informado sobre este tema (opcional)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Tengo un grado universitario relevante',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Es parte de mi profesión',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Es una pasión personal',
	'articlefeedbackv5-form-panel-expertise-other' => 'La fuente de mi conocimiento no está en esta lista',
	'articlefeedbackv5-form-panel-helpimprove' => 'Me gustaría ayudar a mejorar Wikipedia, enviarme un correo electrónico (opcional)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Te enviaremos un correo electrónico de confirmación. No compartiremos tu dirección con nadie. $1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Política de privacidad',
	'articlefeedbackv5-form-panel-submit' => 'Enviar calificaciones',
	'articlefeedbackv5-form-panel-pending' => 'Tu valoración aún no ha sido enviada',
	'articlefeedbackv5-form-panel-success' => 'Guardado correctamente',
	'articlefeedbackv5-form-panel-expiry-title' => 'Tus calificaciones han caducado',
	'articlefeedbackv5-form-panel-expiry-message' => 'Por favor, reevalúa esta página y envía calificaciones nuevas.',
	'articlefeedbackv5-report-switch-label' => 'Ver las calificaciones de la página',
	'articlefeedbackv5-report-panel-title' => 'Evaluaciones de la página',
	'articlefeedbackv5-report-panel-description' => 'Promedio actual de calificaciones.',
	'articlefeedbackv5-report-empty' => 'No hay valoraciones',
	'articlefeedbackv5-report-ratings' => '$1 valoraciones',
	'articlefeedbackv5-field-trustworthy-label' => 'Confiable',
	'articlefeedbackv5-field-trustworthy-tip' => '¿Este artículo posee suficientes referencias y éstas vienen de fuentes de confianza?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Carece de fuentes confiables',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Pocas fuentes confiables',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Fuentes confiables adecuadas',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Buenas fuentes confiables',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Muy buenas fuentes confiables',
	'articlefeedbackv5-field-complete-label' => 'Completo',
	'articlefeedbackv5-field-complete-tip' => '¿Crees que este artículo abarca las áreas esenciales que deberían incluirse sobre el tema?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Falta mucha información',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Contiene algo de información',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Contiene información clave, pero con carencias',
	'articlefeedbackv5-field-complete-tooltip-4' => 'Contiene la mayoría de información clave',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Cobertura completa',
	'articlefeedbackv5-field-objective-label' => 'Objetivo',
	'articlefeedbackv5-field-objective-tip' => '¿Crees que este artículo muestra una representación justa de todas las perspectivas sobre el tema?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Fuertemente sesgado',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Sesgo moderado',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Sesgo mínimo',
	'articlefeedbackv5-field-objective-tooltip-4' => 'No hay sesgo evidente',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Totalmente imparcial',
	'articlefeedbackv5-field-wellwritten-label' => 'Bien escrito',
	'articlefeedbackv5-field-wellwritten-tip' => '¿Crees que el artículo está bien organizado y escrito adecuadamente?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Incomprensible',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Difícil de entender',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Suficiente claridad',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Buena claridad',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Claridad excepcional',
	'articlefeedbackv5-pitch-reject' => 'Quizá más tarde',
	'articlefeedbackv5-pitch-or' => 'o',
	'articlefeedbackv5-pitch-thanks' => '¡Gracias! Se han guardado tus valoraciones.',
	'articlefeedbackv5-pitch-survey-message' => 'Tómate un momento para completar una breve encuesta.',
	'articlefeedbackv5-pitch-survey-accept' => 'Iniciar encuesta',
	'articlefeedbackv5-pitch-join-message' => '¿Quieres crear una cuenta?',
	'articlefeedbackv5-pitch-join-body' => 'Una cuenta te ayudará a realizar un seguimiento de tus cambios y te permitirá participar en debates y ser parte de la comunidad.',
	'articlefeedbackv5-pitch-join-accept' => 'Crear una cuenta',
	'articlefeedbackv5-pitch-join-login' => 'Iniciar sesión',
	'articlefeedbackv5-pitch-edit-message' => '¿Sabías que puedes editar esta página?',
	'articlefeedbackv5-pitch-edit-accept' => 'Editar esta página',
	'articlefeedbackv5-survey-message-success' => 'Gracias por completar la encuesta.',
	'articlefeedbackv5-survey-message-error' => 'Ha ocurrido un error.
Por favor inténtalo de nuevo más tarde.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Altibajos de hoy',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Páginas con las calificaciones más altas: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Páginas con las calificaciones más bajas: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Lo más modificado de la semana',
	'articleFeedbackv5-table-caption-recentlows' => 'Calificaciones bajas recientes',
	'articleFeedbackv5-table-heading-page' => 'Página',
	'articleFeedbackv5-table-heading-average' => 'Promedio',
	'articleFeedbackv5-copy-above-highlow-tables' => 'Esta es una característica experimental. Por favor, proporciona tus comentarios en su [$1 página de discusión].',
	'articlefeedbackv5-dashboard-bottom' => "'''Nota''': Continuaremos experimentando con diferentes formas de presentar los artículos en estos paneles. Ahora, los paneles incluyen los siguientes artículos:
* Las páginas con las calificaciones más altas y más bajas: artpiculos que han recibido al menos diez calificaciones en las últimas 24 horas. Se calculan promedios tomando en cuenta las calificaciones enviadas en las últimas 24 horas.
* Calificaciones bajas recientes: artículos que obtuvieron el 70% o más de calificaciones bajas (2 estrellas o menos) en cualquier categoría en las últimas 24 horas. Solamente se incluyen aquellos artículos que hayan recibido al menos diez calificaciones en las últimas 24 horas.",
	'articlefeedbackv5-disable-preference' => "No mostrar el ''widget'' de comentarios de artículos en las páginas",
	'articlefeedbackv5-emailcapture-response-body' => '¡Hola!

Te agradecemos el interés por ayudar a mejorar {{SITENAME}}.

Por favor, toma un momento para confirmar tu correo electrónico haciendo clic en el siguiente enlace:

$1

Quizás quieras visitar:

$2

E ingresa el siguiente código de confirmación:

$3

Nos pondremos en contacto contigo con información para para ayudarte a mejorar {{SITENAME}}.

Si tú no realizaste esta solicitud, por favor ignora este correo y no te enviaremos más información.

Agradecidos y con los mejores deseos,
El equipo de {{SITENAME}}.',
);

/** Estonian (Eesti)
 * @author Avjoska
 * @author Pikne
 */
$messages['et'] = array(
	'articlefeedbackv5' => 'Artiklite hindamise ülevaade',
	'articlefeedbackv5-desc' => 'Artikli hindamine (prooviversioon)',
	'articlefeedbackv5-survey-question-whyrated' => 'Miks seda lehekülge täna hindasid (vali kõik sobivad):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Tahtsin leheküljele üldist hinnangut anda',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Loodan, et minu hinnang aitab lehekülje arendamisele kaasa',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Tahtsin {{GRAMMAR:inessive|{{SITENAME}}}} kaastööd teha',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Mulle meeldib oma arvamust jagada',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Ma ei hinnanud täna seda lehekülge, vaid tahtsin tagasisidet anda',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Muu',
	'articlefeedbackv5-survey-question-useful' => 'Kas pead antud hinnanguid kasulikuks ja selgeks?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Miks?',
	'articlefeedbackv5-survey-question-comments' => 'Kas sul on lisamärkusi?',
	'articlefeedbackv5-survey-submit' => 'Saada',
	'articlefeedbackv5-survey-title' => 'Palun vasta mõnele küsimusele.',
	'articlefeedbackv5-survey-thanks' => 'Aitäh küsitlusele vastamast!',
	'articlefeedbackv5-error' => 'Ilmnes tõrge. Palun proovi hiljem uuesti.',
	'articlefeedbackv5-form-switch-label' => 'Hinda seda lehekülge',
	'articlefeedbackv5-form-panel-title' => 'Selle lehekülje hindamine',
	'articlefeedbackv5-form-panel-explanation' => 'Mis see on?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:Artikli hindamine',
	'articlefeedbackv5-form-panel-clear' => 'Eemalda see hinnang',
	'articlefeedbackv5-form-panel-expertise' => 'Mul on sellel alal väga head teadmised (valikuline)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Mul on vastav kõrgharidus',
	'articlefeedbackv5-form-panel-expertise-profession' => 'See on seotud minu elukutsega',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Ma olen sellest teemast sügavalt huvitatud',
	'articlefeedbackv5-form-panel-expertise-other' => 'Minu teadmiste allikas on nimetamata',
	'articlefeedbackv5-form-panel-helpimprove' => 'Soovin aidata Vikipeediat täiustada. Saatke mulle palun e-kiri. (valikuline)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Me saadame sulle kinnitus-e-kirja. $1 järgi ei jaga me sinu e-posti aadressi kellegi kolmandaga.',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Privaatsuspõhimõtete',
	'articlefeedbackv5-form-panel-submit' => 'Saada hinnang',
	'articlefeedbackv5-form-panel-pending' => 'Sinu hinnangut pole veel saadetud.',
	'articlefeedbackv5-form-panel-success' => 'Edukalt salvestatud',
	'articlefeedbackv5-form-panel-expiry-title' => 'Sinu hinnangud on aegunud.',
	'articlefeedbackv5-form-panel-expiry-message' => 'Palun iseloomusta uuesti seda lehekülge ja saada uued hinnangud.',
	'articlefeedbackv5-report-switch-label' => 'Vaata leheküljele antud hinnanguid',
	'articlefeedbackv5-report-panel-title' => 'Leheküljele antud hinnangud',
	'articlefeedbackv5-report-panel-description' => 'Praegused keskmised hinnangud',
	'articlefeedbackv5-report-empty' => 'Hinnanguteta',
	'articlefeedbackv5-report-ratings' => '$1 hinnangut',
	'articlefeedbackv5-field-trustworthy-label' => 'Usaldusväärne',
	'articlefeedbackv5-field-trustworthy-tip' => 'Kas sinu meelest on artikkel vajalikul määral viidatud ja kas viidatakse usaldusväärsetele allikatele?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Usaldusväärsed allikad puuduvad',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Vähe usaldusväärseid allikaid',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Sobivad usaldusväärsed allikad',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Head usaldusväärsed allikad',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Väga head usaldusväärsed allikad',
	'articlefeedbackv5-field-complete-label' => 'Täielik',
	'articlefeedbackv5-field-complete-tip' => 'Kas sinu meelest on artiklis kõik põhiline käsitletud?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Suurem osa teabest puudub',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Osa teabest on olemas',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Olulisim on käsitletud, aga lünklikult',
	'articlefeedbackv5-field-complete-tooltip-4' => 'Suurem osa olulisimast teabest on olemas',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Igakülgne käsitlus',
	'articlefeedbackv5-field-objective-label' => 'Erapooletu',
	'articlefeedbackv5-field-objective-tip' => 'Kas sinu meelest on artiklis kõik vaatenurgad võrdselt esindatud?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Väga erapoolik',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Erapoolik',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Natuke erapoolik',
	'articlefeedbackv5-field-objective-tooltip-4' => 'Ilmse erapoolikuseta',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Täiesti erapooletu',
	'articlefeedbackv5-field-wellwritten-label' => 'Hästi kirjutatud',
	'articlefeedbackv5-field-wellwritten-tip' => 'Kas sinu meelest on see artikkel hästi üles ehitatud ja kirjutatud?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Arusaamatu',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Raskesti mõistetav',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Piisavalt arusaadav',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Selgesti kirjutatud',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Erakordselt selgesti kirjutatud',
	'articlefeedbackv5-pitch-reject' => 'Ehk hiljem',
	'articlefeedbackv5-pitch-or' => 'või',
	'articlefeedbackv5-pitch-thanks' => 'Suur tänu! Sinu hinnang on salvestatud.',
	'articlefeedbackv5-pitch-edit-message' => 'Kas teadsid, et saad seda lehekülge redigeerida?',
	'articlefeedbackv5-pitch-edit-accept' => 'Redigeeri',
	'articlefeedbackv5-survey-message-error' => 'Ilmnes tõrge.
Palun proovi hiljem uuesti.',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Parimate hinnangutega leheküljed: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Halvimate hinnangutega leheküljed: $1',
	'articleFeedbackv5-table-heading-page' => 'Lehekülg',
	'articleFeedbackv5-table-heading-average' => 'Keskmine',
	'articlefeedbackv5-disable-preference' => 'Ära näita lehekülgedel artikli hindamise dialoogikasti',
	'articlefeedbackv5-emailcapture-response-body' => 'Tere!

Aitäh, et näitasid üles huvi {{GRAMMAR:genitive|{{SITENAME}}}} täiustamise vastu.

Palun leia hetk, et oma e-posti aadress kinnitada. Selleks klõpsa allolevale lingile:

$1

Samuti võid külastata lehekülge

$2

ja sisestada seal järgmise kinnituskoodi:

$3

Anname sulle peagi teada, kuidas saad {{GRAMMAR:partitive|{{SITENAME}}}} täiustada.

Kui sa pole sellist teadet palunud, siis eira seda e-kirja ja me ei saada sulle rohkem midagi.

Kõike paremat!

{{GRAMMAR:genitive|{{SITENAME}}}} meeskond',
);

/** Basque (Euskara)
 * @author Abel2es
 * @author Joxemai
 * @author Theklan
 */
$messages['eu'] = array(
	'articlefeedbackv5' => 'Artikuluen gaineko ekarpenen arbela',
	'articlefeedbackv5-desc' => 'Artikuluaren ekarpenak',
	'articlefeedbackv5-survey-question-origin' => 'Ze orrialdetan zeunden inkesta hau betetzen hasi zarenean?',
	'articlefeedbackv5-survey-question-whyrated' => 'Esaiguzu, mesedez, zergatik baloratu duzun orrialde hau gaur (klik egin nahi duzun guztien gainean):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Orrialde honen balorazio orokorrean parte hartu nahi nuen',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Uste dut nire ekarpenarekin orrialde honen garapena hobetu ahal dela',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => '{{SITENAME}}rekin kolaboratu nahi nuen',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Nire iritzia ematen gustoko dut',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Gaur ez dut baloraziorik egin, baina tresna honen gaineko ekarpenak egin nahi nituen',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Beste bat',
	'articlefeedbackv5-survey-question-useful' => 'Uste duzu emandako puntuazioak baliagarriak eta argigarriak direla?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Zergatik?',
	'articlefeedbackv5-survey-question-comments' => 'Beste iruzkinik al duzu?',
	'articlefeedbackv5-survey-submit' => 'Bidali',
	'articlefeedbackv5-survey-title' => 'Erantzun, mesedez, galdera hauei',
	'articlefeedbackv5-survey-thanks' => 'Eskerrik asko inkesta betetzeagatik.',
	'articlefeedbackv5-error' => 'Arazo bat egon da. Saia zaitez beranduago.',
	'articlefeedbackv5-form-switch-label' => 'Kalifikatu orri hau',
	'articlefeedbackv5-form-panel-title' => 'Kalifikatu orri hau',
	'articlefeedbackv5-form-panel-clear' => 'Balorazio hau ezabatu',
	'articlefeedbackv5-form-panel-expertise' => 'Gai honen inguruko ezagutza handia dut (aukerazkoa)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Unibertsitateko titulazio bat dut gai honetan',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Nire ogibidearen parte da',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Oso gogoko dudan gai bat da',
	'articlefeedbackv5-form-panel-expertise-other' => 'Nire ezagutzaren jatorria ez da goiko aukeren artean agertzen',
	'articlefeedbackv5-form-panel-helpimprove' => 'Wikipedia hobetzen lagundu nahi dut, bidalidazue e-posta bat (aukeran)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'E-posta bat bidaliko dizugu konfirmaziorako. Ez diogu zure helbidea beste inori bidaliko. $1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Pribazitate arauak',
	'articlefeedbackv5-form-panel-submit' => 'Bidali balorazioa',
	'articlefeedbackv5-form-panel-pending' => 'Zure balorazioak ez dira oraindik bidali',
	'articlefeedbackv5-form-panel-success' => 'Ondo gorde da',
	'articlefeedbackv5-form-panel-expiry-title' => 'Zure balorazioak iraungi du',
	'articlefeedbackv5-form-panel-expiry-message' => 'Mesedez, berriro ebaluatu orrialde hau eta bidali zure balorazio berria.',
	'articlefeedbackv5-report-switch-label' => 'Ikus orriaren balorazioak',
	'articlefeedbackv5-report-panel-title' => 'Orrialdearen balorazioak',
	'articlefeedbackv5-report-panel-description' => 'Oraingo bataz besteko balorazioa.',
	'articlefeedbackv5-report-empty' => 'Ez dago baloraziorik',
	'articlefeedbackv5-report-ratings' => '$1 balorazio',
	'articlefeedbackv5-field-trustworthy-label' => 'Sinisgarria',
	'articlefeedbackv5-field-complete-label' => 'Osorik',
	'articlefeedbackv5-field-objective-label' => 'Helburua',
	'articlefeedbackv5-field-wellwritten-label' => 'Ondo idatzia',
	'articlefeedbackv5-pitch-reject' => 'Agian beranduago',
	'articlefeedbackv5-pitch-or' => 'edo',
	'articlefeedbackv5-pitch-thanks' => 'Eskerrik asko! Zure balorazioa bidali da.',
	'articlefeedbackv5-pitch-survey-accept' => 'Hasi inkestarekin',
	'articlefeedbackv5-pitch-join-message' => 'Kontu bat sortu nahi al zenuen?',
	'articlefeedbackv5-pitch-join-body' => 'Kontu bat sortzeak lagunduko dizu zure aldaketak jarraitzen, eztabaidetan parte hartzen eta komunitatearen parte izaten.',
	'articlefeedbackv5-pitch-join-accept' => 'Kontua sortu',
	'articlefeedbackv5-pitch-join-login' => 'Saioa hasi',
	'articlefeedbackv5-pitch-edit-message' => 'Ba al zenekien artikulu hau alda zenezakeela?',
	'articlefeedbackv5-pitch-edit-accept' => 'Orrialde hau aldatu',
	'articlefeedbackv5-survey-message-success' => 'Eskerrik asko inkesta betetzeagatik.',
	'articlefeedbackv5-survey-message-error' => 'Akats bat egon da.
Saia zaitez bearnduago.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Gaurko goi eta beheak',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Baloraziorik altuena duten orrialdeak: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Balorazio eskasena duten orrialdeak: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Aste honetan gehien aldatu direnak',
	'articleFeedbackv5-table-caption-recentlows' => 'Balorazio eskasa izan duten azkenak',
	'articleFeedbackv5-table-heading-page' => 'Orrialdea',
	'articleFeedbackv5-table-heading-average' => 'Bataz bestekoa',
);

/** Persian (فارسی)
 * @author Ebraminio
 * @author Huji
 * @author Mjbmr
 * @author ZxxZxxZ
 */
$messages['fa'] = array(
	'articlefeedbackv5' => 'داشبورد بازخورد مقاله',
	'articlefeedbackv5-desc' => 'ارزیابی مقاله‌ها (نسخهٔ آزمایشی)',
	'articlefeedbackv5-survey-question-origin' => 'زمان شروع نظرسنجی در کدام صفحه قرار داشتید؟',
	'articlefeedbackv5-survey-question-whyrated' => 'لطفاً به ما اطلاع دهید که چرا شما امروز به این صفحه نمره دادید (تمام موارد مرتبط را انتخاب کنید):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'می‌خواستم در نمره کلی صفحه مشارکت کنم',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'امیدوارم که نمره‌ای که دادم اثر مثبتی روی پیشرفت صفحه داشته باشد',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'می‌خواستم به {{SITENAME}} کمک کنم',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'علاقه دارم نظر خودم را به اشتراک بگذارم',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'امروز نمره‌ای ندادم، اما می‌خواهم راجع به این ویژگی نظر بدهم',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'غیره',
	'articlefeedbackv5-survey-question-useful' => 'آیا فکر می‌کنید نمره‌های ارائه شده مفید و واضح هستند؟',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'چرا؟',
	'articlefeedbackv5-survey-question-comments' => 'آیا هر گونه نظر اضافی دارید؟',
	'articlefeedbackv5-survey-submit' => 'ارسال',
	'articlefeedbackv5-survey-title' => 'لطفاً به چند پرسش پاسخ دهید',
	'articlefeedbackv5-survey-thanks' => 'از این که نظرسنجی را تکمیل کردید متشکریم.',
	'articlefeedbackv5-survey-disclaimer' => 'برای بهبود این ویژگی، بازخورد شما به طور ناشناس با جامعهٔ {{SITENAME}} به اشتراک گذاشته می‌شود.',
	'articlefeedbackv5-error' => 'خطایی رخ داده است. لطفا بعداً دوباره سعی کنید.',
	'articlefeedbackv5-form-switch-label' => 'رای دادن به این صفحه',
	'articlefeedbackv5-form-panel-title' => 'رای دادن به این صفحه',
	'articlefeedbackv5-form-panel-explanation' => 'این چیست؟',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:بازخورد مقاله',
	'articlefeedbackv5-form-panel-clear' => 'حذف این رتبه بندی',
	'articlefeedbackv5-form-panel-expertise' => 'من دربارهٔ این موضوع آگاهی زیادی دارم (اختیاری)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'من یک مدرک دانشگاهی مرتبط دارم',
	'articlefeedbackv5-form-panel-expertise-profession' => 'این بخشی از حرفهٔ من است',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'این یک علاقهٔ شدید شخصی است',
	'articlefeedbackv5-form-panel-expertise-other' => 'منبع دانش من در اینجا فهرست نشده است',
	'articlefeedbackv5-form-panel-helpimprove' => 'من می‌خواهم برای بهبود {{SITENAME}} کمک کنم، به من یک پست الکترونیکی بفرستید (اختیاری)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'ما به شما یک تأییدهٔ پست الکترونیکی خواهیم فرستاد. ما نشانی شما را با هیچ کس به اشتراک نخواهیم گذاشت. $1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'سیاست حفظ اسرار',
	'articlefeedbackv5-form-panel-submit' => 'ثبت رأی',
	'articlefeedbackv5-form-panel-pending' => 'رأی شما هنوز ثبت نشده است',
	'articlefeedbackv5-form-panel-success' => 'با موفقیت ذخیره شد',
	'articlefeedbackv5-form-panel-expiry-title' => 'رأی شما منقضی شده است',
	'articlefeedbackv5-form-panel-expiry-message' => 'لطفاً این صفحه را مجدد مورد ارزیابی قرار دهید و رأی جدید را ثبت کنید.',
	'articlefeedbackv5-report-switch-label' => 'مشاهدهٔ آرای صفحه',
	'articlefeedbackv5-report-panel-title' => 'درجه‌بندی صفحه',
	'articlefeedbackv5-report-panel-description' => 'میانگین رتبه بندی‌های فعلی.',
	'articlefeedbackv5-report-empty' => 'بدون رأی',
	'articlefeedbackv5-report-ratings' => '$1 رأی',
	'articlefeedbackv5-field-trustworthy-label' => 'قابل اعتماد',
	'articlefeedbackv5-field-trustworthy-tip' => 'آیا احساس می‌کنید که این صفحه به اندازهٔ کافی مستند می‌باشد و آن اسناد از منابع قابل اعتمادی آمده‌اند؟',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'فاقد منابع معتبر',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'تعداد کمی معتبر منابع دارد',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'منابع معتبر کافی دارد',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'منابع معتبر خوب دارد',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'منابع معتبر عالی دارد',
	'articlefeedbackv5-field-complete-label' => 'کامل بودن',
	'articlefeedbackv5-field-complete-tip' => 'آیا احساس می‌کنید که این صفحهٔ پوشش کافی در محدودهٔ عنوان دارد که باید داشته باشد؟',
	'articlefeedbackv5-field-complete-tooltip-1' => 'بدون اطلاعات کافی',
	'articlefeedbackv5-field-complete-tooltip-2' => 'شامل اطلاعات کم می‌باشد',
	'articlefeedbackv5-field-complete-tooltip-3' => 'حاوی اطلاعات کلیدی است اما با شکاف',
	'articlefeedbackv5-field-complete-tooltip-4' => 'دارای بیشترین اطلاعات کلیدی است',
	'articlefeedbackv5-field-complete-tooltip-5' => 'پوشش جامع',
	'articlefeedbackv5-field-objective-label' => 'بی‌طرفی',
	'articlefeedbackv5-field-objective-tip' => 'آیا شما احساس می‌کنید که این صفحه بازنمایی عادلانه از را تمام دیدگاه مسئله، نشان می‌دهد؟',
	'articlefeedbackv5-field-objective-tooltip-1' => 'به شدت مغرضانه',
	'articlefeedbackv5-field-objective-tooltip-2' => 'تعصب متوسط',
	'articlefeedbackv5-field-objective-tooltip-3' => 'تعصب حداقل',
	'articlefeedbackv5-field-objective-tooltip-4' => 'بدون تعصب آشکار',
	'articlefeedbackv5-field-objective-tooltip-5' => 'کاملا بی‌غرض',
	'articlefeedbackv5-field-wellwritten-label' => 'خوب نوشته شده',
	'articlefeedbackv5-field-wellwritten-tip' => 'آیا شما احساس می کنید که این صفحه به خوبی سازمان یافته و خوب نوشته شده است؟',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'دور از فهم',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'درک دشوار',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'وضوح کافی',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'وضوح خوب',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'وضوح استثنایی',
	'articlefeedbackv5-pitch-reject' => 'شاید بعداً',
	'articlefeedbackv5-pitch-or' => 'یا',
	'articlefeedbackv5-pitch-thanks' => 'با تشکر! رتبه‌بندی‌های شما، ذخیره شده‌است.',
	'articlefeedbackv5-pitch-survey-message' => 'لطفاً یک لحظه برای تکمیل نظرسنجی کوتاه وقت بگذارید.',
	'articlefeedbackv5-pitch-survey-accept' => 'شروع نظرسنجی',
	'articlefeedbackv5-pitch-join-message' => 'می‌خواستید یک حساب کاربری ایجاد کنید؟',
	'articlefeedbackv5-pitch-join-body' => 'یک حساب کاربری به شما کمک می‌کند ویرایش‌هایتان را پی‌گیری کنید، در بحث‌ها درگیر شوید، و بخشی از جامعه باشید.',
	'articlefeedbackv5-pitch-join-accept' => 'ایجاد یک حساب کاربری',
	'articlefeedbackv5-pitch-join-login' => 'ورود به سامانه',
	'articlefeedbackv5-pitch-edit-message' => 'آیا می دانید که شما می توانید این صفحه را ویرایش کنید؟',
	'articlefeedbackv5-pitch-edit-accept' => 'ویرایش این صفحه',
	'articlefeedbackv5-survey-message-success' => 'سپاس از شما بابت پر کردن فرم نظرسنجی.',
	'articlefeedbackv5-survey-message-error' => 'خطایی رخ داده است.
لطفاً بعداً دوباره سعی کنید.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'بالاترین‌ها و پایین‌ترین‌های امروز',
	'articleFeedbackv5-table-caption-dailyhighs' => 'صفحات با بالاترین رأی:$1',
	'articleFeedbackv5-table-caption-dailylows' => 'صفحات با کمترین رأی:$1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'بیشترین تغییر این هفته',
	'articleFeedbackv5-table-caption-recentlows' => 'سطوح پایین اخیر',
	'articleFeedbackv5-table-heading-page' => 'صفحه',
	'articleFeedbackv5-table-heading-average' => 'میانگین',
	'articleFeedbackv5-copy-above-highlow-tables' => 'این یک ویژگی تجربی است.  لطفاً بازخورد را در [$1 صفحهٔ بحث] ارائه دهید.',
	'articlefeedbackv5-disable-preference' => 'ابزار نظرسنجی مقاله را در صفحات نشان نده',
	'articlefeedbackv5-emailcapture-response-body' => 'سلام!

از شما برای ابراز علاقه در بهبود {{SITENAME}} تشکر می‌کنم.

لطفاً لحظه‌ای برای تأیید پست الکترونیکی خود با کلیک بر روی پیوند مقابل وقت بگذارید: 

$1

شما همچنین می‌توانید صفحهٔ مقابل را مشاهده کنید:

$2

و کد تأیید مقابل را وارد کنید:

$3

ما به زودی با شما برای چگونگی کمک به {{SITENAME}} تماس می‌گیریم.

اگر شما این درخواست را نکرده بودید، لطفاً این درخواست را نادیده بگیرید و ما چیز دیگری برای شما ارسال نمی‌کنیم.

با تشکر از شما، بهترین آرزوها را برایتان داریم،
گروه {{SITENAME}}',
);

/** Finnish (Suomi)
 * @author Nike
 * @author Olli
 */
$messages['fi'] = array(
	'articlefeedbackv5' => 'Artikkelin arvioinnin koostesivu',
	'articlefeedbackv5-desc' => 'Artikkelin arviointi (kokeiluversio)',
	'articlefeedbackv5-survey-question-origin' => 'Millä sivulla olit, kun aloitit tämän kyselyn?',
	'articlefeedbackv5-survey-question-whyrated' => 'Kerro meille, miksi arvostelit tämän sivun tänään (lisää merkki kaikkiin, jotka pitävät paikkaansa):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Haluan vaikuttaa sivun kokonaisarvosanaan',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Toivon, että arvosteluni vaikuttaisi positiivisesti sivun kehitykseen',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Haluan osallistua {{SITENAME}}-sivuston kehitykseen',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Pidän mielipiteeni kertomisesta',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'En antanut arvosteluja tänään, mutta halusin antaa palautetta arvosteluominaisuudesta',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Muu',
	'articlefeedbackv5-survey-question-useful' => 'Ovatko annetut arvostelut mielestäsi hyödyllisiä ja todellisia?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Miksi?',
	'articlefeedbackv5-survey-question-comments' => 'Onko sinulla muita kommentteja?',
	'articlefeedbackv5-survey-submit' => 'Lähetä',
	'articlefeedbackv5-survey-title' => 'Vastaathan muutamiin kysymyksiin',
	'articlefeedbackv5-survey-thanks' => 'Kiitos kyselyn täyttämisestä.',
	'articlefeedbackv5-survey-disclaimer' => 'Palautteesi saatetaan jakaa nimettömänä Wikipedia-yhteisön sisällä tämän toiminnon kehittämiseksi.',
	'articlefeedbackv5-error' => 'Virhe ilmaantui. Yritä myöhemmin uudelleen.',
	'articlefeedbackv5-form-switch-label' => 'Arvioi sivu',
	'articlefeedbackv5-form-panel-title' => 'Arvioi sivu',
	'articlefeedbackv5-form-panel-explanation' => 'Mikä tämä on?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:Sivupalaute',
	'articlefeedbackv5-form-panel-clear' => 'Poista tämä arviointi',
	'articlefeedbackv5-form-panel-expertise' => 'Tunnen tämän aihepiirin hyvin (vapaaehtoinen)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Minulla on vastaava yliopiston/lukion loppututkinto',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Tämä on osa ammattiani',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Tämä on syvä henkilökohtainen intohimo',
	'articlefeedbackv5-form-panel-expertise-other' => 'Tietojeni lähde ei ole näkyvillä tässä',
	'articlefeedbackv5-form-panel-helpimprove' => 'Haluaisin auttaa Wikipedian kehittämisessä, lähettäkää minulle sähköposti (vapaaehtoinen)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Lähetämme sinulle vahvistussähköpostin. Emme jaa osoitettasi kenellekään. $1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Tietosuojakäytäntö',
	'articlefeedbackv5-form-panel-submit' => 'Lähetä arviot',
	'articlefeedbackv5-form-panel-pending' => 'Arvioitasi ei ole vielä lähetetty',
	'articlefeedbackv5-form-panel-success' => 'Tallennus onnistui',
	'articlefeedbackv5-form-panel-expiry-title' => 'Arviosi ovat vanhentuneet',
	'articlefeedbackv5-form-panel-expiry-message' => 'Katso sivu uudestaan ja lähetä uudet arviot.',
	'articlefeedbackv5-report-switch-label' => 'Näytä sivun arviot',
	'articlefeedbackv5-report-panel-title' => 'Sivun arviot',
	'articlefeedbackv5-report-panel-description' => 'Arviointien keskiarvo tällä hetkellä.',
	'articlefeedbackv5-report-empty' => 'Ei arvioita',
	'articlefeedbackv5-report-ratings' => '$1 arviota',
	'articlefeedbackv5-field-trustworthy-label' => 'Luotettavuus',
	'articlefeedbackv5-field-trustworthy-tip' => 'Onko tällä sivulla mielestäsi riittävät lähdeviitteet ja viittaavaatko ne luotettaviin lähteisiin?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Puuttuu hyviä lähteitä',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Muutamia hyviä lähteitä',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Riittävät hyvät lähteet',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Hyvät lähteet',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Erinomaiset lähteet',
	'articlefeedbackv5-field-complete-label' => 'Kattavuus',
	'articlefeedbackv5-field-complete-tip' => 'Kattaako tämä sivu mielestäsi kaikki olennaiset asiat aiheesta?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Suurin osa tiedoista puuttuu',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Sisältää joitain tietoja',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Sisältää avaintiedot, mutta puutteitakin on',
	'articlefeedbackv5-field-complete-tooltip-4' => 'Sisältää suurimman osan avaintiedoista',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Kattavat tiedot',
	'articlefeedbackv5-field-objective-label' => 'Puolueettomuus',
	'articlefeedbackv5-field-objective-tip' => 'Onko sinun mielestäsi tällä sivulla tasapuolinen näkökulma asioihin?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Hyvin puolueellinen',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Jonkin verran puolueellinen',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Vähän puolueellinen',
	'articlefeedbackv5-field-objective-tooltip-4' => 'Ei ilmeistä puolueellisuutta',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Täysin puolueeton',
	'articlefeedbackv5-field-wellwritten-label' => 'Hyvin kirjoitettu',
	'articlefeedbackv5-field-wellwritten-tip' => 'Onko tämä sivu mielestäsi hyvin jäsennelty ja kirjoitettu?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Käsittämätön',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Vaikea ymmärtää',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Riittävän selkeä',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Hyvin selkeä',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Poikkeuksellisen selkeä',
	'articlefeedbackv5-pitch-reject' => 'Ehkä myöhemmin',
	'articlefeedbackv5-pitch-or' => 'tai',
	'articlefeedbackv5-pitch-thanks' => 'Kiitos! Arviosi on tallennettu.',
	'articlefeedbackv5-pitch-survey-message' => 'Käytä hetki lyhyen kyselyn täyttämiseen.',
	'articlefeedbackv5-pitch-survey-accept' => 'Aloita kysely',
	'articlefeedbackv5-pitch-join-message' => 'Halusitko luoda tilin?',
	'articlefeedbackv5-pitch-join-body' => 'Tili auttaa sinua seuraamaan muutoksiasi, osallistumaan keskusteluihin, ja olet osa yhteisöä.',
	'articlefeedbackv5-pitch-join-accept' => 'Luo tili',
	'articlefeedbackv5-pitch-join-login' => 'Kirjaudu sisään',
	'articlefeedbackv5-pitch-edit-message' => 'Tiesitkö, että voit muokata tätä sivua?',
	'articlefeedbackv5-pitch-edit-accept' => 'Muokkaa tätä sivua',
	'articlefeedbackv5-survey-message-success' => 'Kiitos kyselyn täyttämisestä.',
	'articlefeedbackv5-survey-message-error' => 'Tapahtui virhe.
Yritä myöhemmin uudelleen.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Tämän päivän ennätykset',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Sivut, joilla on parhaat arviot: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Sivut, joilla on huonoimmat arviot: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Tällä viikolla eniten muutettu',
	'articleFeedbackv5-table-caption-recentlows' => 'Viimeisimmät matalat arviot',
	'articleFeedbackv5-table-heading-page' => 'Sivu',
	'articleFeedbackv5-table-heading-average' => 'Keskiarvo',
	'articleFeedbackv5-copy-above-highlow-tables' => 'Tämä on kokeellinen ominaisuus.  Anna palautetta [$1 keskustelusivulla].',
	'articlefeedbackv5-disable-preference' => 'Älä näytä Sivupalaute-toimintoa sivuilla',
	'articlefeedbackv5-emailcapture-response-body' => 'Hei!

Kiitos mielenkiinnon osoittamisesta sivun {{SITENAME}} parantamiseen.

Käytäthän hetken sähköpostiosoitteesi vahvistamiseen napsauttamalla allaolevaa linkkiä:

$1

Voit myös käydä:

$2

Ja syöttää seuraavan vahvistuskoodin:

$3

Otamme sinuun pian yhteyttä, ja kerromme kuinka voit auttaa sivuston {{SITENAME}} parantamisessa.

Jos et tehnyt itse tätä pyyntöä, hylkää sähköposti ja emme lähetä sinulle enää uutta viestiä.

Kiitos! Terveisin,
{{SITENAME}} tiimi',
);

/** French (Français)
 * @author Crochet.david
 * @author Faure.thomas
 * @author IAlex
 * @author Jean-Frédéric
 * @author Od1n
 * @author Peter17
 * @author Seb35
 * @author Sherbrooke
 * @author Urhixidur
 */
$messages['fr'] = array(
	'articlefeedbackv5' => 'Tableau de bord de l’évaluation d’article',
	'articlefeedbackv5-desc' => 'Évaluation d’article (version pilote)',
	'articlefeedbackv5-survey-question-origin' => 'À quelle page étiez-vous lorsque vous avez commencé cette évaluation ?',
	'articlefeedbackv5-survey-question-whyrated' => 'Veuillez nous indiquer pourquoi vous avez évalué cette page aujourd’hui (cochez tout ce qui s’applique) :',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Je voulais contribuer à l’évaluation globale de la page',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'J’espère que mon évaluation aura une incidence positive sur le développement de la page',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Je voulais contribuer à {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'J’aime partager mon opinion',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Je n’ai pas évalué la page, mais je voulais donner un retour sur cette fonctionnalité',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Autre',
	'articlefeedbackv5-survey-question-useful' => 'Pensez-vous que les évaluations fournies soient utiles et claires ?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Pourquoi ?',
	'articlefeedbackv5-survey-question-comments' => 'Avez-vous d’autres commentaires ?',
	'articlefeedbackv5-survey-submit' => 'Soumettre',
	'articlefeedbackv5-survey-title' => 'Veuillez répondre à quelques questions',
	'articlefeedbackv5-survey-thanks' => 'Merci d’avoir rempli le questionnaire.',
	'articlefeedbackv5-survey-disclaimer' => 'Pour aider à améliorer cette fonctionnalité, vous pouvez partager anonymement votre feedback avec la communauté Wikipédia.',
	'articlefeedbackv5-error' => 'Une erreur s’est produite. Veuillez réessayer plus tard.',
	'articlefeedbackv5-form-switch-label' => 'Noter cette page',
	'articlefeedbackv5-form-panel-title' => 'Noter cette page',
	'articlefeedbackv5-form-panel-explanation' => 'Qu’est-ce que c’est?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:ArticleFeedback',
	'articlefeedbackv5-form-panel-clear' => 'Supprimer cette évaluation',
	'articlefeedbackv5-form-panel-expertise' => 'Je suis très bien informé sur ce sujet (facultatif)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Je détiens un diplôme d’études supérieures (université ou grande école)',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Cela fait partie de ma profession',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'C’est une passion profonde et personnelle',
	'articlefeedbackv5-form-panel-expertise-other' => 'La source de ma connaissance n’est pas répertoriée ici',
	'articlefeedbackv5-form-panel-helpimprove' => 'J’aimerais aider à améliorer {{SITENAME}}, envoyez-moi un courriel (facultatif)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Nous vous enverrons un courriel de confirmation. Nous ne partagerons votre adresse avec personne. $1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Politique de confidentialité',
	'articlefeedbackv5-form-panel-submit' => 'Envoyer les cotes',
	'articlefeedbackv5-form-panel-pending' => 'Vos votes n’ont pas encore été soumis',
	'articlefeedbackv5-form-panel-success' => 'Enregistré avec succès',
	'articlefeedbackv5-form-panel-expiry-title' => 'Votre évaluation a expiré',
	'articlefeedbackv5-form-panel-expiry-message' => 'Veuillez réévaluer cette page et soumettre votre nouvelle évaluation.',
	'articlefeedbackv5-report-switch-label' => 'Voir les notes des pages',
	'articlefeedbackv5-report-panel-title' => 'Évaluation des pages',
	'articlefeedbackv5-report-panel-description' => 'Notations moyennes actuelles.',
	'articlefeedbackv5-report-empty' => 'Aucune évaluation',
	'articlefeedbackv5-report-ratings' => 'Notations $1',
	'articlefeedbackv5-field-trustworthy-label' => 'Digne de confiance',
	'articlefeedbackv5-field-trustworthy-tip' => 'Pensez-vous que cette page a suffisamment de citations et que celles-ci proviennent de sources dignes de confiance ?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Manque de sources fiables',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Peu de sources fiables',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Sources fiables suffisantes',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Bonnes sources fiables',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Très bonnes sources fiables',
	'articlefeedbackv5-field-complete-label' => 'Complet',
	'articlefeedbackv5-field-complete-tip' => 'Pensez-vous que cette page couvre les thèmes essentiels du sujet ?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Il manque la plupart des informations',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Il y a quelques informations',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Il y a les informations clés, mais avec des manques',
	'articlefeedbackv5-field-complete-tooltip-4' => 'Il y a la plupart des informations clés',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Couverture exhaustive',
	'articlefeedbackv5-field-objective-label' => 'Impartial',
	'articlefeedbackv5-field-objective-tip' => 'Pensez-vous que cette page fournit une présentation équitable de toutes les perspectives du sujet traité ?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Fortement biaisé',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Biais modérés',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Biais minimal',
	'articlefeedbackv5-field-objective-tooltip-4' => 'Pas de biais évident',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Pas du tout biaisé',
	'articlefeedbackv5-field-wellwritten-label' => 'Bien écrit',
	'articlefeedbackv5-field-wellwritten-tip' => 'Pensez-vous que cette page soit bien organisée et bien écrite ?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Incompréhensible',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Difficile à comprendre',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Clarté correcte',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Bonne clarté',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Clarté exceptionnelle',
	'articlefeedbackv5-pitch-reject' => 'Peut-être plus tard',
	'articlefeedbackv5-pitch-or' => 'ou',
	'articlefeedbackv5-pitch-thanks' => 'Merci ! Votre évaluation a été enregistrée.',
	'articlefeedbackv5-pitch-survey-message' => 'Veuillez prendre quelques instants pour remplir un court sondage.',
	'articlefeedbackv5-pitch-survey-accept' => 'Démarrer l’enquête',
	'articlefeedbackv5-pitch-join-message' => 'Vouliez-vous créer un compte ?',
	'articlefeedbackv5-pitch-join-body' => 'Un compte vous aidera à suivre vos modifications, vous impliquer dans les discussions et faire partie de la communauté.',
	'articlefeedbackv5-pitch-join-accept' => 'Créer un compte',
	'articlefeedbackv5-pitch-join-login' => 'Se connecter',
	'articlefeedbackv5-pitch-edit-message' => 'Saviez-vous que vous pouvez modifier cette page ?',
	'articlefeedbackv5-pitch-edit-accept' => 'Modifier cette page',
	'articlefeedbackv5-survey-message-success' => 'Merci d’avoir rempli le questionnaire.',
	'articlefeedbackv5-survey-message-error' => 'Une erreur est survenue.
Veuillez ré-essayer plus tard.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Les hauts et les bas d’aujourd’hui',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Pages avec les plus hautes cotes : $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Pages avec cotes les plus basses : $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Les plus modifiés cette semaine',
	'articleFeedbackv5-table-caption-recentlows' => 'Dernières cotes basses',
	'articleFeedbackv5-table-heading-page' => 'Page',
	'articleFeedbackv5-table-heading-average' => 'Moyenne',
	'articleFeedbackv5-copy-above-highlow-tables' => 'Il s’agit d’une fonctionnalité expérimentale. Veuillez fournir des commentaires sur la [$1 page de discussion].',
	'articlefeedbackv5-dashboard-bottom' => "'''Note''' : Nous allons continuer à expérimenter avec différentes façons de représenter les articles dans ces tableaux de bord. Ceux-ci contiennent les articles suivants :
* pages qui ont les taux les plus faibles ou plus élevés : ce sont les articles qui ont reçu au moins 10 évaluations dans les dernières 24 heures. Les moyennes sont obtenues en calculant la moyenne de toutes les évaluations des dernières 24 heures.
* bas récents : articles qui ont reçu deux étoiles ou moins, 70 % du temps ou plus, peu importe la catégorie dans les dernières 24 heures. Cela s’applique seulement aux articles qui ont reçu au moins 10 évaluations dans les dernières 24 heures.",
	'articlefeedbackv5-disable-preference' => 'Ne pas afficher le widget Évaluation d’article sur les pages',
	'articlefeedbackv5-emailcapture-response-body' => "Bonjour !

Merci pour votre aider à améliorer {{SITENAME}}.

Veuillez prendre un moment pour confirmer votre courriel en cliquant sur le lien ci-dessous :

$1

Vous pouvez aussi visiter :

$2

et entrer le code ce confirmation suivant :

$3

Nous serons en contact prochainement pour connaître la façon dont vous pouvez aider à améliorer {{SITENAME}}.

Si vous n'avez pas initié cette demande, veuillez ignorer ce courriel et nous ne vous enverrons rien d’autre.

Meilleurs vœux, et merci,

L’équipe de {{SITENAME}}",
);

/** Franco-Provençal (Arpetan)
 * @author ChrisPtDe
 */
$messages['frp'] = array(
	'articlefeedbackv5' => 'Tablô de bôrd de l’èstimacion d’articllo',
	'articlefeedbackv5-desc' => 'Èstimacion d’articllo (vèrsion pilote)',
	'articlefeedbackv5-survey-question-origin' => 'A quinta pâge érâd-vos quand vos éd comenciê cela èstimacion ?',
	'articlefeedbackv5-survey-question-whyrated' => 'Nos volyéd endicar porquè vos éd èstimâ cela pâge houé (pouentâd tot cen que s’aplique) :',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Volévo contribuar a l’èstimacion globâla de la pâge',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'J’èspero que mon èstimacion arat un rèsultat positif sur lo dèvelopament de la pâge',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Volévo contribuar a {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'J’âmo partagiér mon avis',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'J’é pas èstimâ la pâge, mas volévo balyér mon avis sur cela fonccionalitât',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Ôtra',
	'articlefeedbackv5-survey-question-useful' => 'Pensâd-vos que les èstimacions balyês seyont utiles et cllâres ?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Porquè ?',
	'articlefeedbackv5-survey-question-comments' => 'Avéd-vos d’ôtros comentèros ?',
	'articlefeedbackv5-survey-submit' => 'Sometre',
	'articlefeedbackv5-survey-title' => 'Volyéd rèpondre a quârques quèstions',
	'articlefeedbackv5-survey-thanks' => 'Grant-marci d’avêr rempli lo quèstionèro.',
	'articlefeedbackv5-survey-disclaimer' => 'Por édiér a mèlyorar cela fonccionalitât, vos pouede partagiér anonimament voutron avis avouéc la comunôtât Vouiquipèdia.',
	'articlefeedbackv5-error' => 'Una èrror est arrevâ. Volyéd tornar èprovar ples târd.',
	'articlefeedbackv5-form-switch-label' => 'Èstimar cela pâge',
	'articlefeedbackv5-form-panel-title' => 'Èstimar cela pâge',
	'articlefeedbackv5-form-panel-explanation' => 'Qu’est-o qu’il est ?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:Èstimacion d’articllo',
	'articlefeedbackv5-form-panel-clear' => 'Enlevar cela èstimacion',
	'articlefeedbackv5-form-panel-expertise' => 'Su brâvament bien enformâ sur cél sujèt (u chouèx)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Dètegno un diplomo d’ètudes supèriores (univèrsitât ou ben granta ècoula)',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Cen fât partia de mon metiér',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'O est una passion provonda a mè',
	'articlefeedbackv5-form-panel-expertise-other' => 'La sôrsa de ma cognessence est pas listâ ique',
	'articlefeedbackv5-form-panel-helpimprove' => 'J’amerê édiér a mèlyorar Vouiquipèdia, mandâd-mè un mèssâjo (u chouèx)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Nos vos manderens un mèssâjo de confirmacion. Nos partagierens pas voutra adrèce avouéc pèrsona. $1',
	'articlefeedbackv5-form-panel-helpimprove-email-placeholder' => 'mèssâjo@ègzemplo.org',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Politica de confidencialitât',
	'articlefeedbackv5-form-panel-submit' => 'Mandar les èstimacions',
	'articlefeedbackv5-form-panel-pending' => 'Voutres èstimacions ont p’oncor étâ somêses',
	'articlefeedbackv5-form-panel-success' => 'Encartâ avouéc reusséta',
	'articlefeedbackv5-form-panel-expiry-title' => 'Voutres èstimacions ont èxpirâs',
	'articlefeedbackv5-form-panel-expiry-message' => 'Volyéd tornar èstimar cela pâge et pués sometre voutra novèla èstimacion.',
	'articlefeedbackv5-report-switch-label' => 'Vêre les èstimacions de la pâge',
	'articlefeedbackv5-report-panel-title' => 'Èstimacions de la pâge',
	'articlefeedbackv5-report-panel-description' => 'Èstimacions moyenes d’ora.',
	'articlefeedbackv5-report-empty' => 'Gins d’èstimacion',
	'articlefeedbackv5-report-ratings' => 'Èstimacions $1',
	'articlefeedbackv5-field-trustworthy-label' => 'Digno de confiance',
	'articlefeedbackv5-field-trustworthy-tip' => 'Pensâd-vos que cela pâge at sufisament de citacions et que cetes vegnont de sôrses dignes de fiance ?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Manca de sôrses fiâbles',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Pou de sôrses fiâbles',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Sôrses fiâbles sufisentes',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Bônes sôrses fiâbles',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Rudes bônes sôrses fiâbles',
	'articlefeedbackv5-field-complete-label' => 'Complèt',
	'articlefeedbackv5-field-complete-tip' => 'Pensâd-vos que cela pâge côvre los tèmos èssencièls du sujèt ?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Manque la plepârt de les enformacions',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Y at quârques enformacions',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Y at les enformacions cllâfs, mas avouéc des manques',
	'articlefeedbackv5-field-complete-tooltip-4' => 'Y at la plepârt de les enformacions cllâfs',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Cuvèrta complèta',
	'articlefeedbackv5-field-objective-label' => 'Emparciâl',
	'articlefeedbackv5-field-objective-tip' => 'Pensâd-vos que cela pâge balye una presentacion èquitâbla de totes les pèrspèctives du sujèt trètâ ?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Fortament bièsiê',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Biès moderâs',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Biès minimâl',
	'articlefeedbackv5-field-objective-tooltip-4' => 'Gins de biès visiblo',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Pas du tot bièsiê',
	'articlefeedbackv5-field-wellwritten-label' => 'Bien ècrit',
	'articlefeedbackv5-field-wellwritten-tip' => 'Pensâd-vos que cela pâge seye bien organisâ et bien ècrita ?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Pas compréhensiblo',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Mâlésiê a comprendre',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Cllartât justa',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Bôna cllartât',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Cllartât èxcèpcionèla',
	'articlefeedbackv5-pitch-reject' => 'Pôt-étre ples târd',
	'articlefeedbackv5-pitch-or' => 'ou ben',
	'articlefeedbackv5-pitch-thanks' => 'Grant-marci ! Voutra èstimacion at étâ encartâ.',
	'articlefeedbackv5-pitch-survey-message' => 'Volyéd prendre doux-três moments por remplir un côrt sondâjo.',
	'articlefeedbackv5-pitch-survey-accept' => 'Emmodar l’enquéta',
	'articlefeedbackv5-pitch-join-message' => 'Volévâd-vos fâre un compto ?',
	'articlefeedbackv5-pitch-join-body' => 'Un compto vos édierat a siuvre voutros changements, vos molyér dens les discussions et fâre partia de la comunôtât.',
	'articlefeedbackv5-pitch-join-accept' => 'Fâre un compto',
	'articlefeedbackv5-pitch-join-login' => 'Sè branchiér',
	'articlefeedbackv5-pitch-edit-message' => 'Saviâd-vos que vos pouede changiér cela pâge ?',
	'articlefeedbackv5-pitch-edit-accept' => 'Changiér ceta pâge',
	'articlefeedbackv5-survey-message-success' => 'Grant-marci d’avêr rempli lo quèstionèro.',
	'articlefeedbackv5-survey-message-error' => 'Una èrror est arrevâ.
Volyéd tornar èprovar ples târd.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Los hôts et bâs d’houé',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Pâges avouéc quotes les ples hôtes : $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Pâges avouéc quotes les ples bâsses : $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Los ples changiês de cela semana',
	'articleFeedbackv5-table-caption-recentlows' => 'Dèrriérs bâs',
	'articleFeedbackv5-table-heading-page' => 'Pâge',
	'articleFeedbackv5-table-heading-average' => 'Moyena',
	'articleFeedbackv5-copy-above-highlow-tables' => 'O est una fonccionalitât èxpèrimentâla. Volyéd balyér voutron avis sur la [$1 pâge de discussion].',
	'articlefeedbackv5-dashboard-bottom' => "'''Nota :''' nos volens continuar a èxpèrimentar difèrentes façons de reprèsentar los articllos dens celos tablôs de bôrd.  Ora, celos contegnont cetos articllos :
* pâges qu’ont les quotes les ples hôtes / fêbles : sont los articllos qu’ont reçus u muens 10 èstimacions dens les 24 hores passâs.  Les moyenes sont avues en calculent la moyena de totes les èstimacions de les 24 hores passâs.
* bâs novéls : sont los articllos qu’ont reçus 70 % ou ben una quota ples fêbla (2 ètêles ou ben muens) dens una catègorie quinta que seye dens les 24 hores passâs. Cen s’aplique ren qu’ux articllos qu’ont reçus u muens 10 èstimacions dens les 24 hores passâs.",
	'articlefeedbackv5-disable-preference' => 'Pas fâre vêre lo vouidgèt Èstimacion d’articllo sur les pâges',
	'articlefeedbackv5-emailcapture-response-body' => 'Bonjorn !

Grant-marci d’avêr èxprimâ voutron entèrèt por édiér a mèlyorar {{SITENAME}}.

Volyéd prendre un moment por confirmar voutra adrèce èlèctronica en cliquent sur lo lim ce-desot : 

$1

Vos pouede asse-ben visitar :

$2

et pués buchiér ceti code de confirmacion :

$3

Nos nos volens d’abôrd veriér vers vos avouéc la façon que vos pouede édiér a mèlyorar {{SITENAME}}.

Se vos éd pas fêt cela demanda, volyéd ignorar ceti mèssâjo et pués nos vos manderens ren d’ôtro.

Mèlyors vôs, et grant-marci,

L’èquipa de {{SITENAME}}',
);

/** Friulian (Furlan)
 * @author Klenje
 */
$messages['fur'] = array(
	'articlefeedbackv5-survey-submit' => 'Invie',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'articlefeedbackv5' => 'Panel de avaliación de artigos',
	'articlefeedbackv5-desc' => 'Versión piloto da avaliación dos artigos',
	'articlefeedbackv5-survey-question-origin' => 'En que páxina estaba cando comezou a enquisa?',
	'articlefeedbackv5-survey-question-whyrated' => 'Díganos por que valorou esta páxina (marque todas as opcións que cumpran):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Quería colaborar na valoración da páxina',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Agardo que a miña valoración afecte positivamente ao desenvolvemento da páxina',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Quería colaborar con {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Gústame dar a miña opinión',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Non dei ningunha valoración, só quería deixar os meus comentarios sobre a característica',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Outra',
	'articlefeedbackv5-survey-question-useful' => 'Cre que as valoracións dadas son útiles e claras?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Por que?',
	'articlefeedbackv5-survey-question-comments' => 'Ten algún comentario adicional?',
	'articlefeedbackv5-survey-submit' => 'Enviar',
	'articlefeedbackv5-survey-title' => 'Responda algunhas preguntas',
	'articlefeedbackv5-survey-thanks' => 'Grazas por encher a enquisa.',
	'articlefeedbackv5-survey-disclaimer' => 'Para axudar a mellorar esta característica, os seus comentarios compartiranse de xeito anónimo coa comunidade da Wikipedia.',
	'articlefeedbackv5-error' => 'Houbo un erro. Inténteo de novo máis tarde.',
	'articlefeedbackv5-form-switch-label' => 'Avaliar esta páxina',
	'articlefeedbackv5-form-panel-title' => 'Avaliar esta páxina',
	'articlefeedbackv5-form-panel-explanation' => 'Que é isto?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:Avaliación de artigos',
	'articlefeedbackv5-form-panel-clear' => 'Eliminar a avaliación',
	'articlefeedbackv5-form-panel-expertise' => 'Estou moi ben informado sobre este tema (opcional)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Teño un grao escolar ou universitario pertinente',
	'articlefeedbackv5-form-panel-expertise-profession' => 'É parte da miña profesión',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'É unha das miñas afeccións persoais',
	'articlefeedbackv5-form-panel-expertise-other' => 'A fonte do meu coñecemento non está nesta lista',
	'articlefeedbackv5-form-panel-helpimprove' => 'Gustaríame axudar a mellorar a Wikipedia; enviádeme un correo electrónico (opcional)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Enviarémoslle un correo electrónico de confirmación. Non compartiremos o seu enderezo con ninguén. $1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Política de protección de datos',
	'articlefeedbackv5-form-panel-submit' => 'Enviar a avaliación',
	'articlefeedbackv5-form-panel-pending' => 'Non enviou as súas valoracións',
	'articlefeedbackv5-form-panel-success' => 'Gardado correctamente',
	'articlefeedbackv5-form-panel-expiry-title' => 'As súas avaliacións caducaron',
	'articlefeedbackv5-form-panel-expiry-message' => 'Volva avaliar esta páxina e envíe as novas valoracións.',
	'articlefeedbackv5-report-switch-label' => 'Ollar as avaliacións da páxina',
	'articlefeedbackv5-report-panel-title' => 'Avaliacións da páxina',
	'articlefeedbackv5-report-panel-description' => 'Avaliacións medias.',
	'articlefeedbackv5-report-empty' => 'Sen avaliacións',
	'articlefeedbackv5-report-ratings' => '$1 avaliacións',
	'articlefeedbackv5-field-trustworthy-label' => 'Fidedigno',
	'articlefeedbackv5-field-trustworthy-tip' => 'Cre que esta páxina ten citas suficientes e que estas son de fontes fiables?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Carece de fontes fidedignas',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Ten poucas fontes respectables',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'As fontes son suficientes',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'As fontes son boas',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'As fontes son excelentes',
	'articlefeedbackv5-field-complete-label' => 'Completo',
	'articlefeedbackv5-field-complete-tip' => 'Cre que esta páxina aborda as áreas esenciais do tema que debería?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Carece da información máis importante',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Contén información parcial',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Contén a información clave, pero aínda faltan datos',
	'articlefeedbackv5-field-complete-tooltip-4' => 'Contén a meirande parte da información clave',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Contén toda a información importante',
	'articlefeedbackv5-field-objective-label' => 'Imparcial',
	'articlefeedbackv5-field-objective-tip' => 'Cre que esta páxina mostra unha representación xusta de todas as perspectivas do tema?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Moi parcial',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Moderadamente parcial',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Minimamente parcial',
	'articlefeedbackv5-field-objective-tooltip-4' => 'Sen parcialidades obvias',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Completamente imparcial',
	'articlefeedbackv5-field-wellwritten-label' => 'Ben escrito',
	'articlefeedbackv5-field-wellwritten-tip' => 'Cre que esta páxina está ben organizada e escrita?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Incomprensible',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Difícil de entender',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Claridade adecuada',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Claridade boa',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Claridade excepcional',
	'articlefeedbackv5-pitch-reject' => 'Talvez logo',
	'articlefeedbackv5-pitch-or' => 'ou',
	'articlefeedbackv5-pitch-thanks' => 'Grazas! Gardáronse as súas valoracións.',
	'articlefeedbackv5-pitch-survey-message' => 'Dedique un momento a encher esta pequena enquisa.',
	'articlefeedbackv5-pitch-survey-accept' => 'Comezar a enquisa',
	'articlefeedbackv5-pitch-join-message' => 'Quere crear unha conta?',
	'articlefeedbackv5-pitch-join-body' => 'Unha conta axudará a seguir as súas edicións, participar en conversas e ser parte da comunidade.',
	'articlefeedbackv5-pitch-join-accept' => 'Crear unha conta',
	'articlefeedbackv5-pitch-join-login' => 'Rexistro',
	'articlefeedbackv5-pitch-edit-message' => 'Sabía que pode editar esta páxina?',
	'articlefeedbackv5-pitch-edit-accept' => 'Editar esta páxina',
	'articlefeedbackv5-survey-message-success' => 'Grazas por encher a enquisa.',
	'articlefeedbackv5-survey-message-error' => 'Houbo un erro.
Inténteo de novo máis tarde.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Os altos e baixos de hoxe',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Artigos coas valoracións máis altas: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Artigos coas valoracións máis baixas: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Os máis modificados esta semana',
	'articleFeedbackv5-table-caption-recentlows' => 'Últimos baixos',
	'articleFeedbackv5-table-heading-page' => 'Páxina',
	'articleFeedbackv5-table-heading-average' => 'Media',
	'articleFeedbackv5-copy-above-highlow-tables' => 'Esta é unha característica experimental. Deixe os seus comentarios na [$1 páxina de conversa].',
	'articlefeedbackv5-dashboard-bottom' => "'''Nota:''' Continuaremos experimentando diferentes xeitos de seleccionar artigos neste taboleiro. Polo de agora, os taboleiros inclúen os seguintes artigos:
* Páxinas coas mellores/peores valoracións: artigos que recibiron, polo menos, 10 avaliacións nas últimas 24 horas. As medias calcúlanse tomando a media de todas as valoracións enviadas nas últimas 24 horas.
* Os baixos máis recentes: artigos que tiveron un 70% ou menos (2 estrelas ou menos) das valoracións en calquera categoría nas últimas 24 horas. Soamente se inclúen os artigos que recibiron, polo menos, 10 avaliacións nas últimas 24 horas.",
	'articlefeedbackv5-disable-preference' => 'Non mostrar o widget de avaliación de artigos nas páxinas',
	'articlefeedbackv5-emailcapture-response-body' => 'Ola!

Grazas por expresar interese en axudar a mellorar {{SITENAME}}.

Tome un momento para confirmar o seu correo electrónico premendo na ligazón que hai a continuación: 

$1

Tamén pode visitar:

$2

E inserir o seguinte código de confirmación:

$3

Poñerémonos en contacto con vostede para informarlle sobre como axudar a mellorar {{SITENAME}}.

Se vostede non fixo esta petición, ignore esta mensaxe e non lle enviaremos máis nada.

Os mellores desexos e grazas,
O equipo de {{SITENAME}}',
);

/** Swiss German (Alemannisch)
 * @author Als-Chlämens
 * @author Als-Holder
 */
$messages['gsw'] = array(
	'articlefeedbackv5' => 'Übersichtssyte für Artikelbeurteilige',
	'articlefeedbackv5-desc' => 'Macht d Yyschetzig vu Artikel megli (Pilotversion)',
	'articlefeedbackv5-survey-question-origin' => 'Uff wellere Syte bisch gsi, wo die Umfroog aagfange hesch?',
	'articlefeedbackv5-survey-question-whyrated' => 'Bitte loss es is wisse, wurum Du dää Artikel hite yygschetzt hesch (bitte aachryzle, was zuetrifft):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Ich haa welle mitmache bi dr Yyschetzig vu däm Artikel',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Ich hoffe, ass myy Yyschetzig e positive Yyfluss het uf di chimftig Entwicklig vum Artikel',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Ich haa welle mitmache bi {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Ich tue gärn myy Meinig teile',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Ich haa hite kei Yyschetzig vorgnuu, haa aber e Ruckmäldig zue däre Funktion welle gee',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Anderi',
	'articlefeedbackv5-survey-question-useful' => 'Glaubsch, ass d Yyschetzige, wu abgee wore sin, nitzli un verständli sin?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Wurum?',
	'articlefeedbackv5-survey-question-comments' => 'Hesch no meh Aamerkige?',
	'articlefeedbackv5-survey-submit' => 'Ibertrage',
	'articlefeedbackv5-survey-title' => 'Bitte gib Antworte uf e paar Froge',
	'articlefeedbackv5-survey-thanks' => 'Dankschen fir Dyy Ruckmäldig.',
	'articlefeedbackv5-survey-disclaimer' => 'Zume mitzhälfe die Funktion z verbessre, chasch dyni Ruggmäldig anonym de Wikipedia-Gmeinschaft mitteile.',
	'articlefeedbackv5-error' => 'S het e Fähler gee. Bitte versuech s speter nomol.',
	'articlefeedbackv5-form-switch-label' => 'Die Syte yyschetze',
	'articlefeedbackv5-form-panel-title' => 'Die Syte yyschetze',
	'articlefeedbackv5-form-panel-explanation' => 'Was isch des de?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:Artikelbeurteilig',
	'articlefeedbackv5-form-panel-clear' => 'Yyschetzig uuseneh',
	'articlefeedbackv5-form-panel-expertise' => 'Ich haa umfangrychi Chänntnis zue däm Thema (optional)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Ich haa ne entsprächende Hochschuelabschluss',
	'articlefeedbackv5-form-panel-expertise-profession' => 'S isch Teil vu myym Beruef',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Ich haa ne seli stark persenlig Inträssi an däm Thema',
	'articlefeedbackv5-form-panel-expertise-other' => 'Dr Grund fir myy Chänntnis isch do nit ufgfiert',
	'articlefeedbackv5-form-panel-helpimprove' => 'Ich wott debi hälfe, {{SITENAME}} z verbessre. Schicket mir bitte es Mail. (optional)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Mir schicke dir es Bstätigungsmail. Dyni E-Mail-Adräss wird sälbstverständli aa niemern wytergee. $1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Dateschutz',
	'articlefeedbackv5-form-panel-submit' => 'Yyschetzig ibertrage',
	'articlefeedbackv5-form-panel-pending' => 'Dyni Beurteilig isch no nit veröffentlicht worde',
	'articlefeedbackv5-form-panel-success' => 'Erfolgrych gspycheret',
	'articlefeedbackv5-form-panel-expiry-title' => 'Dyy Yyschetzig isch z lang här.',
	'articlefeedbackv5-form-panel-expiry-message' => 'Bitte tue d Syte nomol beurteile un e neji yyschetzitg spychere.',
	'articlefeedbackv5-report-switch-label' => 'Yyschetzige zue däre Syte aaluege',
	'articlefeedbackv5-report-panel-title' => 'Yyschetzige vu dr Syte',
	'articlefeedbackv5-report-panel-description' => 'Aktuälli Durschnittsergebnis vu dr Yyschetzige',
	'articlefeedbackv5-report-empty' => 'Kei Yyschetzige',
	'articlefeedbackv5-report-ratings' => '$1 Yyschetzige',
	'articlefeedbackv5-field-trustworthy-label' => 'Vertröueswirdig',
	'articlefeedbackv5-field-trustworthy-tip' => 'Hesch Du dr Yydruck, ass es in däm Artikel gnue Quällenaagabe het un ass mer däne Quälle cha tröue?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Git kei zueverlässigi Quelle aa',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Git wenig zueverlässigi Quelle aa',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Git ussryychend zueverlässigi Quelle aa',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Gueti un zueverlässigi Quelle',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Git sehr zueverlässigi Quelle aa',
	'articlefeedbackv5-field-complete-label' => 'Vollständig',
	'articlefeedbackv5-field-complete-tip' => 'Hesch Du dr Yydruck, ass in däm Artikel aali Aschpäkt ufgfiert sin, wu mit däm Thema zämmehange?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Di meiste Informatione fääle no',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Enthält bstimmti Informatione',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Enthält d Hauptinformatione, aber es het no Lücke',
	'articlefeedbackv5-field-complete-tooltip-4' => 'Enthält di meiste Hauptinformatione',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Enthält umfassendi Informatione',
	'articlefeedbackv5-field-objective-label' => 'Nit voryygnuu',
	'articlefeedbackv5-field-objective-tip' => 'Hesch Du dr Yydruck, ass dää Artikel e uusgwogeni Darstellig isch vu allne Aschpäkt, wu mit däm Thema verbunde sin?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'De Inhalt isch sehr eisytig',
	'articlefeedbackv5-field-objective-tooltip-2' => 'De Inhalt isch e weng eisytig',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Fast nit eisytig',
	'articlefeedbackv5-field-objective-tooltip-4' => 'Kei offesichtlichi Eisytigkeit',
	'articlefeedbackv5-field-objective-tooltip-5' => 'De Inhalt isch nit eisytig',
	'articlefeedbackv5-field-wellwritten-label' => 'Guet gschribe',
	'articlefeedbackv5-field-wellwritten-tip' => 'Hesch Du dr Yydruck, ass dää Artikel guet strukturiert un gschribe isch?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Unverständlig',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Schwer verständlig',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Ussryychend verständlig',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Guet verständlig',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Bsunders chlar verständlig',
	'articlefeedbackv5-pitch-reject' => 'Villicht speter',
	'articlefeedbackv5-pitch-or' => 'oder',
	'articlefeedbackv5-pitch-thanks' => 'Dankschen! Dyy Yyschetzig isch gspycheret wore.',
	'articlefeedbackv5-pitch-survey-message' => 'Bitte nimm der e Momänt Zyt go bin ere churzen Umfrog mitmache.',
	'articlefeedbackv5-pitch-survey-accept' => 'Umfrog aafange',
	'articlefeedbackv5-pitch-join-message' => 'Hesch e Benutzerkonto welle aalege?',
	'articlefeedbackv5-pitch-join-body' => 'E Benutzerkonto hilft der dyni Bearbeitige besser noovollzie z chenne, eifacher bi Diskussione mitzmache un e Teil vu dr Benutzergmeinschaft z wäre.',
	'articlefeedbackv5-pitch-join-accept' => 'Benutzerkonto aalege',
	'articlefeedbackv5-pitch-join-login' => 'Aamälde',
	'articlefeedbackv5-pitch-edit-message' => 'Hesch gwisst, ass du dä Artikel chasch bearbeite?',
	'articlefeedbackv5-pitch-edit-accept' => 'Die Syte bearbeite',
	'articlefeedbackv5-survey-message-success' => 'Dankschen, ass Du bi däre Umfrog mitgmacht hesch.',
	'articlefeedbackv5-survey-message-error' => 'E Fähler isch ufträtte.
Bitte versuech s speter nomol.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Di Hööche- un Diefpunkt vo hüt',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Artikel mit de beschte Bewertige: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Artikel mit de schlächteste Bewertige: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'In derre Wuche am meiste gänderet',
	'articleFeedbackv5-table-caption-recentlows' => 'Aktuelli Diefpünkt',
	'articleFeedbackv5-table-heading-page' => 'Syte',
	'articleFeedbackv5-table-heading-average' => 'Durschnitt',
	'articleFeedbackv5-copy-above-highlow-tables' => 'Des isch e Versuechsfunktion. Bitte gib uff de [$1 Diskussionssyte] e Ruggmäldig dezue.',
	'articlefeedbackv5-dashboard-bottom' => "'''Hywyys''': Mir experimentiere wyter mit verschidne Mögligkeite zume Artikel uff dänne Übersichtssyte uffzeige. Zur Zit werde doo die Artikel aazeigt:
*Syte mit de beschte/schlächteschte Bewertige: Artikel wo derwyylischt de letschte 24 Stunde mindestens 10 Bewertige kriegt hen. De Durchschnitt wird durch alli Beurteilige in de letschte 24 Stunde berächnet.
*Aktuelli schlächti Bewertige: Artikel wo derwyylischt de letschte 24 Stunde e Bewertige vo 70% oder niidriger übercho hen (2 Stärnli oder weniger), in allene Kategorie. Numme Artikel wo derwyylscht de letschte 24 Stunde mindestens 10 Bewertige übercho hen, sin beruggsichtigt.",
	'articlefeedbackv5-disable-preference' => 'S Widget zur Syte-Beurteilig nit aazeige',
	'articlefeedbackv5-emailcapture-response-body' => 'Sali!
Merci für dyni Intress, {{SITENAME}} z verbessre!
Bitte nimm der en Augeblick, zume dyni E-Mail-Adräss z bstätige. Des goot über de Link unte:

$1

Du chasch au uff die Syte go:

$2

Un dörte de Code yygee:

$3

Mir benoochrichtige dich deno bal mit Informatione, wie du chasch {{SITENAME}} verbessre.

Wänn du im Fall die Aafroog nit gstellt hesch, no ignorier die E-Mail bitte, un mir unternämme nüüt wyters.

Viili Griess un viile Dank,
D Mitarbeiter vo {{SITENAME}}',
);

/** Hebrew (עברית)
 * @author Amire80
 * @author Nahum
 * @author YaronSh
 */
$messages['he'] = array(
	'articlefeedbackv5' => 'לוח בקרה למשוב על ערך',
	'articlefeedbackv5-desc' => 'הערכת ערך (גרסה ניסיונית)',
	'articlefeedbackv5-survey-question-origin' => 'מאיזה עמוד הגעתם לסקר הזה?',
	'articlefeedbackv5-survey-question-whyrated' => 'נא ליידע אותנו מדובר דירגת דף זה היום (יש לסמן את כל העונים לשאלה):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'ברצוני לתרום לדירוג הכללי של הדף',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'כולי תקווה שהדירוג שלי ישפיע לטובה על פיתוח הדף',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'ברצוני לתרום ל{{grammar:תחילית|{{SITENAME}}}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'מוצא חן בעיני לשתף את דעתי ברבים',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'לא סיפקתי אף דירוגים היום, אך ברצוני לתת משוב על תכונה',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'אחר',
	'articlefeedbackv5-survey-question-useful' => 'האם קיבלת את התחושה שהדירוגים שסיפקת שימושיים וברורים?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'מדוע?',
	'articlefeedbackv5-survey-question-comments' => 'האם יש לך הערות נוספות?',
	'articlefeedbackv5-survey-submit' => 'שליחה',
	'articlefeedbackv5-survey-title' => 'נא לענות על מספר שאלות',
	'articlefeedbackv5-survey-thanks' => 'תודה לך על מילוי הסקר.',
	'articlefeedbackv5-survey-disclaimer' => 'כדי לסייע בשיפור תכונה זו, המשוב שלך ישותף באופן אלמוני עם קהילת ויקיפדיה.',
	'articlefeedbackv5-error' => 'אירעה שגיאה. נא לנסות שוב מאוחר יותר.',
	'articlefeedbackv5-form-switch-label' => 'תנו הערכה לדף הזה',
	'articlefeedbackv5-form-panel-title' => 'תנו הערכה לדף הזה',
	'articlefeedbackv5-form-panel-explanation' => 'מה זה?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:משוב על דפים',
	'articlefeedbackv5-form-panel-clear' => 'הסר דירוג זה',
	'articlefeedbackv5-form-panel-expertise' => 'יש לי ידע רב על הנושא הזה (לא חובה)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'יש לי תואר אקדמי בנושא הזה',
	'articlefeedbackv5-form-panel-expertise-profession' => 'זה חלק מהמקצוע שלי',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'זה מעורר בי תשוקה רבה',
	'articlefeedbackv5-form-panel-expertise-other' => 'מקור הידע שלי לא מופיע כאן',
	'articlefeedbackv5-form-panel-helpimprove' => 'אני רוצה לעזור לשפר את ויקיפדיה, שלחו לי מכתב על זה (לא חובה)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'אנו נשלח לך מכתב אימות בדוא״ל. לא נשתף את הכתובת עם איש. $1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'הצהרת פרטיות על משוב',
	'articlefeedbackv5-form-panel-submit' => 'לשלוח דירוגים',
	'articlefeedbackv5-form-panel-pending' => 'הדירוגים שלכם לא נשלחו',
	'articlefeedbackv5-form-panel-success' => 'נשמר בהצלחה',
	'articlefeedbackv5-form-panel-expiry-title' => 'הדירוגים שלכם פגו',
	'articlefeedbackv5-form-panel-expiry-message' => 'נא להעריך את הדף מחדש ולשלוח דירוגים חדשים.',
	'articlefeedbackv5-report-switch-label' => 'להציג את ההערכות שניתנו לדף',
	'articlefeedbackv5-report-panel-title' => 'הערכות שניתנו לדף הזה',
	'articlefeedbackv5-report-panel-description' => 'ממוצע הדירוגים הנוכחי.',
	'articlefeedbackv5-report-empty' => 'אין דירוגים',
	'articlefeedbackv5-report-ratings' => '$1 דירוגים',
	'articlefeedbackv5-field-trustworthy-label' => 'אמין',
	'articlefeedbackv5-field-trustworthy-tip' => 'האם אתם מרגישים שבדף הזה יש הפניות מספיקות למקורות ושהמקורות מהימנים?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'חסרים מקורות מהימנים',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'מעט מקורות מהימנים',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'מקורות מהימנים מהימנים',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'מקורות מהימנים טובים',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'מקורות מהימנים מעולים',
	'articlefeedbackv5-field-complete-label' => 'להשלים',
	'articlefeedbackv5-field-complete-tip' => 'האם אתם מרגישים שהדף הזה סוקר את התחומים החיוניים הנוגעים בנושא?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'רוב המידע חסר',
	'articlefeedbackv5-field-complete-tooltip-2' => 'קיים חלק מהמידע',
	'articlefeedbackv5-field-complete-tooltip-3' => 'מכיל מידע עיקרי, אבל יש חוסרים',
	'articlefeedbackv5-field-complete-tooltip-4' => 'מכיל את רוב המידע העיקרי',
	'articlefeedbackv5-field-complete-tooltip-5' => 'סיקור מקיף',
	'articlefeedbackv5-field-objective-label' => 'לא מוטה',
	'articlefeedbackv5-field-objective-tip' => 'האם אתם מרגישים שהדף הזה מייצג באופן הולם את כל נקודות המבט על הנושא?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'מוטה מאוד',
	'articlefeedbackv5-field-objective-tooltip-2' => 'קיימת הטיה מסוימת',
	'articlefeedbackv5-field-objective-tooltip-3' => 'קיימת הטיה מזערית',
	'articlefeedbackv5-field-objective-tooltip-4' => 'אין הטיה מובהקת',
	'articlefeedbackv5-field-objective-tooltip-5' => 'אין שום הטיה',
	'articlefeedbackv5-field-wellwritten-label' => 'כתוב היטב',
	'articlefeedbackv5-field-wellwritten-tip' => 'האם אתם מרגישים שהדף הזה מאורגן וכתוב היטב?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'לא ברור כלל',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'קשה להבנה',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'ברור באופן סביר',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'ברור',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'ברור מאוד',
	'articlefeedbackv5-pitch-reject' => 'אולי מאוחר יותר',
	'articlefeedbackv5-pitch-or' => 'או',
	'articlefeedbackv5-pitch-thanks' => 'תודה! הדירוגים שלכם נשמרו.',
	'articlefeedbackv5-pitch-survey-message' => 'אנא הקדישו רגע למילוי שאלון קצר.',
	'articlefeedbackv5-pitch-survey-accept' => 'להתחיל את הסקר',
	'articlefeedbackv5-pitch-join-message' => 'אתם רוצים ליצור חשבון?',
	'articlefeedbackv5-pitch-join-body' => 'החשבון יעזור לכם לעקוב אחר העריכות שלכם, להיות מעורבים בדיונים ולהיות חלק מהקהילה.',
	'articlefeedbackv5-pitch-join-accept' => 'יצירת חשבון',
	'articlefeedbackv5-pitch-join-login' => 'כניסה לחשבון',
	'articlefeedbackv5-pitch-edit-message' => 'הידעתם שאתם יכולים לערוך את הדף הזה?',
	'articlefeedbackv5-pitch-edit-accept' => 'לערוך את הדף הזה',
	'articlefeedbackv5-survey-message-success' => 'תודה על מילוי הסקר.',
	'articlefeedbackv5-survey-message-error' => 'אירעה שגיאה. 
נא לנסות שוב מאוחר יותר.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'התוצאות הגבוהות והנמוכות היום',
	'articleFeedbackv5-table-caption-dailyhighs' => 'ערכים עם הדירוגים הגבוהים ביותר: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'ערכים עם הדירוגים הנמוכים ביותר: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'מה השתנה השבוע יותר מכול',
	'articleFeedbackv5-table-caption-recentlows' => 'תוצאות נמוכות לאחרונה',
	'articleFeedbackv5-table-heading-page' => 'דף',
	'articleFeedbackv5-table-heading-average' => 'ממוצע',
	'articleFeedbackv5-copy-above-highlow-tables' => 'זוהי תכונה ניסיונית. נשמח לקבל משוב ב[$1 דף השיחה].',
	'articlefeedbackv5-dashboard-bottom' => "'''שימו לב''': אנחנו נמשיך לערוך ניסויים עם דרכים שונות להציף ערכים בלוחות הבקרה האלה. כעת לוחות הברה כוללים את הערכים הבאים:
* דפים עם דירוגים גבוהים ביותר או נמוכים ביותר: ערכים שקיבלו לפחות 10 דירוגים ב־24 השעות האחרונות. הממוצעים מחושבים לפי ממוצע על הדירוגים ב־24 השעות האחרונות.
* נמוכים אחרונים: ערכים שקיבלו דירוג של 70% נמוך (2 כוכבים או פחות) בקטגוריה כלשהי ב־24 השעות האחרונות. רק ערכים שקיבלו לפחות 10 דירוגים ב־24 השעות האחרונות כלולים.",
	'articlefeedbackv5-disable-preference' => 'לא להציג את כלי דירוג הערכים בדפים',
	'articlefeedbackv5-emailcapture-response-body' => 'שלום!

תודה שהבעתם עניין בסיוע לשיפור אתר {{SITENAME}}.

אנא הקדישו רגע לאשר את הדואר האלקטרוני שלכם על־ידי לחיצה על הקישור להלן:

$1

אפשר גם לבקר בקישור הבא:

$2

ולהזין את קוד האישור הבא:

$3

נהיה בקשר לאחר זמן קצר ונספר לכם על דרכים לסייע לשפר את אתר {{SITENAME}}.

אם לא יזמת את הבקשה הזאת, נא להתעלם מהמכתב הזה ולא נשלח לך שום דבר אחר.

כל טוב, ותודה

צוות {{SITENAME}}',
);

/** Hindi (हिन्दी)
 * @author Mayur
 * @author Vibhijain
 */
$messages['hi'] = array(
	'articlefeedbackv5' => 'लेख प्रतिक्रिया डैशबोर्ड',
	'articlefeedbackv5-desc' => 'लेख सुझाव प्रतिक्रिया',
	'articlefeedbackv5-survey-question-origin' => 'आप कौनसे पृष्ठ पर थे जब आपने यह सर्वेक्षण शुरु किया था?',
	'articlefeedbackv5-survey-question-whyrated' => 'कृपया हमें बताये कि आपने क्यों आज इस पृष्ठ का मूल्यांकन किया (सभी लागु होने वाले विकल्प चुने):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'मैं पृष्ठ की समग्र रेटिंग के लिए योगदान करना चाहता था',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'मुझे आशा है कि मेरी रेटिंग पृष्ठ के सकारात्मक विकास को प्रभावित करेगी',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'मैं {{SITENAME}} को योगदान करना चाहता था।',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'मुझे मेरे विचार साझा करना पसन्द है।',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'मैंने मूल्यांकन आज नहीं प्रदान की थी, लेकिन सुविधा पर प्रतिक्रिया देना चाहता था।',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'अन्य',
	'articlefeedbackv5-survey-question-useful' => 'क्या आपको लगता है कि आपके द्वारा प्रदान की रेटिंग उपयोगी और स्पष्ट हैं?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'क्यों?',
	'articlefeedbackv5-survey-question-comments' => 'क्या आपकी कोई अतिरिक्त टिप्पणियाँ है?',
	'articlefeedbackv5-survey-submit' => 'भेजें',
	'articlefeedbackv5-survey-title' => 'कृपया कुछ सवालों के जवाब देवें',
	'articlefeedbackv5-survey-thanks' => 'सर्वेक्षण को भरने के लिए धन्यवाद।',
	'articlefeedbackv5-survey-disclaimer' => 'इस सुविधा को बेहतर बनाने में मदद करने के लिए, आपकी प्रतिक्रिया गुमनाम विकिपीडिया समुदाय के साथ साझा किया जा सकता है।',
	'articlefeedbackv5-error' => 'कोई त्रुटि उत्पन्न हुई। कृपया बाद में पुन: प्रयास करें।',
	'articlefeedbackv5-form-switch-label' => 'इस पन्ने का मूल्यांकन करे।',
	'articlefeedbackv5-form-panel-title' => 'इस पन्ने का मूल्यांकन करे।',
	'articlefeedbackv5-form-panel-explanation' => 'यह क्या है?',
	'articlefeedbackv5-form-panel-explanation-link' => 'परियोजना:विकिपीडिया आकलन',
	'articlefeedbackv5-form-panel-clear' => 'यह रेटिंग हटाये।',
	'articlefeedbackv5-form-panel-expertise' => 'मैं इस विषय (वैकल्पिक) के बारे में अत्यधिक जानकारी रखता हूँ।',
	'articlefeedbackv5-form-panel-expertise-studies' => 'मैंरे पास एक प्रासंगिक कॉलेज/ विश्वविद्यालय की डिग्री है।',
	'articlefeedbackv5-form-panel-expertise-profession' => 'यह मेरे पेशे का हिस्सा है।',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'यह एक गहरी व्यक्तिगत जुनून है।',
	'articlefeedbackv5-form-panel-expertise-other' => 'मेरी जानकारी के स्रोत यहाँ सूचीबद्ध नहीं है।',
	'articlefeedbackv5-form-panel-helpimprove' => 'मैं विकिपीडिया में सुधार करने में मदद करना चाहता हूँ, मुझे एक ई-मेल भेजें (वैकल्पिक)।',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'हम आपको एक पुष्टिकरण ई-मेल भेज देंगे। हम आपका पता किसी के साथ साझा नहीं करेंगे।$1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'गोपनीयता नीति',
	'articlefeedbackv5-form-panel-submit' => 'मूल्याँकन जमा करे।',
	'articlefeedbackv5-form-panel-pending' => 'आपके मूल्यांकन अभी तक जमा नहीं किये गये।',
	'articlefeedbackv5-form-panel-success' => 'सफलतापूर्वक सहेजा गया',
	'articlefeedbackv5-form-panel-expiry-title' => 'आपके मूल्यांकन की अवधि समाप्त हो गयी है।',
	'articlefeedbackv5-form-panel-expiry-message' => 'कृपया इस पृष्ठ को पुन जाँचकर अपना मूल्याँकन जमा करे।',
	'articlefeedbackv5-report-switch-label' => 'पृष्ठ मूल्यांकन देखें',
	'articlefeedbackv5-report-panel-title' => 'पृष्ठ रेटिंग',
	'articlefeedbackv5-report-panel-description' => 'वर्तमान औसत रेटिंग।',
	'articlefeedbackv5-report-empty' => 'कोई मूल्यांकन नहीं',
	'articlefeedbackv5-report-ratings' => '$1 रेटिंग',
	'articlefeedbackv5-field-trustworthy-label' => 'विश्वसनीय',
	'articlefeedbackv5-field-trustworthy-tip' => 'क्या आपको लगता है कि इस पृष्ठ में पर्याप्त सन्दर्भ है और वह सन्दर्भ विश्वसनीय स्त्रोतों से है।',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'सम्मानित सूत्रों का अभाव',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'सम्मानित सूत्रों का अभाव',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'पर्याप्त सम्मानित स्रोत',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'अच्छे सम्मानित स्रोत',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'महान सम्मानित स्रोत',
	'articlefeedbackv5-field-complete-label' => 'पूर्ण',
	'articlefeedbackv5-field-complete-tip' => 'क्या आपको लगता है कि यह पृष्ठ विषय से सम्बन्धित समस्त आवश्यक विषयों को समेटें हुए है।',
	'articlefeedbackv5-field-complete-tooltip-1' => 'सबसे अधिक जानकारी गुम',
	'articlefeedbackv5-field-complete-tooltip-2' => 'कुछ जानकारी शामिल है',
	'articlefeedbackv5-field-complete-tooltip-3' => 'महत्वपूर्ण जानकारी शामिल है, लेकिन अंतराल के साथ',
	'articlefeedbackv5-field-complete-tooltip-4' => 'सबसे महत्वपूर्ण जानकारी शामिल है।',
	'articlefeedbackv5-field-complete-tooltip-5' => 'व्यापक कवरेज',
	'articlefeedbackv5-field-objective-label' => 'उद्देश्य',
	'articlefeedbackv5-field-objective-tip' => 'क्या आपको लगता है कि  यह पृष्ठ समस्त प्रतिनिधित्व मुद्दों पर निष्पक्ष है।',
	'articlefeedbackv5-field-objective-tooltip-1' => 'काफि पक्षपाती',
	'articlefeedbackv5-field-objective-tooltip-2' => 'उदारवादी पूर्वाग्रह',
	'articlefeedbackv5-field-objective-tooltip-3' => 'कम से कम पूर्वाग्रह',
	'articlefeedbackv5-field-objective-tooltip-4' => 'कोई स्पष्ट पूर्वाग्रह नहीं',
	'articlefeedbackv5-field-objective-tooltip-5' => 'पूरी तरह से निष्पक्ष',
	'articlefeedbackv5-field-wellwritten-label' => 'अच्छी तरह से लिखा हुआ।',
	'articlefeedbackv5-field-wellwritten-tip' => 'क्या आपको लगता है कि यह पृष्ठ पूर्ण रुप से संगठित है अच्छी तरह से लिखा हुआ है।',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'समझ से बाहर',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'समझने के लिए मुश्किल',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'पर्याप्त स्पष्टता',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'अच्छी स्पष्टता',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'असाधारण स्पष्टता',
	'articlefeedbackv5-pitch-reject' => 'शायद बाद में',
	'articlefeedbackv5-pitch-or' => 'या',
	'articlefeedbackv5-pitch-thanks' => 'धन्यवाद! आपका मूल्याँकन सहेजा गया।',
	'articlefeedbackv5-pitch-survey-message' => 'कृपया एक संक्षिप्त सर्वेक्षण को पूरा करने के लिए एक क्षण लेवें',
	'articlefeedbackv5-pitch-survey-accept' => 'सर्वेक्षण शुरू',
	'articlefeedbackv5-pitch-join-message' => 'क्या आप एक खाता बनाना चाहते हैं?',
	'articlefeedbackv5-pitch-join-body' => 'एक खाता से आपको आपके संपादन के ट्रैक रखने, विचार विमर्श में शामिल होने और समुदाय का एक हिस्सा बनने में मदद मिलेगी।',
	'articlefeedbackv5-pitch-join-accept' => 'नया खाता बनाएँ',
	'articlefeedbackv5-pitch-join-login' => 'सत्रारंभ',
	'articlefeedbackv5-pitch-edit-message' => 'क्या आप जानते हैं कि आप इस पृष्ठ को संपादित कर सकते हैं?',
	'articlefeedbackv5-pitch-edit-accept' => 'यह पृष्ठ संपादित करें',
	'articlefeedbackv5-survey-message-success' => 'सर्वेक्षण को भरने के लिए धन्यवाद।',
	'articlefeedbackv5-survey-message-error' => 'कोई त्रुटि उत्पन्न हुई। कृपया बाद में पुन: प्रयास करें।',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'आज के उतार-चढ़ाव',
	'articleFeedbackv5-table-caption-dailyhighs' => 'सर्वोच्च रेटिंग वाले पृष्ठ:$1',
	'articleFeedbackv5-table-caption-dailylows' => 'निम्नतम् रेटिंग वाले पृष्ठ:$1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'इस सप्ताह के सबसे अधिक बदलाव',
	'articleFeedbackv5-table-caption-recentlows' => 'हाल ही के चढ़ाव',
	'articleFeedbackv5-table-heading-page' => 'पृष्ठ',
	'articleFeedbackv5-table-heading-average' => 'औसत',
	'articleFeedbackv5-copy-above-highlow-tables' => 'यह एक प्रायोगिक सुविधा है।  कृपया अपनी राय  [$1 चर्चा पृष्ठ] पर अवश्य दें',
	'articlefeedbackv5-dashboard-bottom' => "'''नोट''': हम इन डैशबोर्ड्स में लेख सरफेसिंग के विभिन्न तरीकों का प्रयोग करेंगे। वर्तमान में डैशबोर्ड्स निम्न लेख शामिल किये हुए है-
*उच्चतम एवं निम्नतम रेटिंग वाले पृष्ठ: जिन लेखों ने पिछ्हले २४ घन्टों में १० से अधिक रेटिंग प्राप्त की हैं, पिछले २४ घन्टों में प्राप्त रेटिंग के औसत से औसत मान निकाला जाता  है।
*हाल ही के उतार:जिन लेखों ने ७०% या २ से कम रेटिंग पिछले २४ घण्टों में प्राप्त की है। केवल पिछले २४ घण्टों में १० से अधिक रेटिंग प्राप्त करने वाले लेख शामिल किये गये है।",
	'articlefeedbackv5-disable-preference' => 'लेख प्रतिक्रिया विजेट पृष्ठों पर न दिखाएँ',
	'articlefeedbackv5-emailcapture-response-body' => 'नमस्कार!!एन!एन!{{SITENAME}} को बेहतर बनाने के लिए मदद करने में रुचि व्यक्त करने के लिए धन्यवाद.!एन!एन!कृपया नीचे दिए गए लिंक पर क्लिक करके अपने ई-मेल की पुष्टि करने के लिए एक क्षण ले:!एन!एन!$1!एन!एन!तुम भी यात्रा कर सकते हैं:!एन!एन!$2!एन!एन!और निम्नलिखित पुष्टिकरण कोड प्रविष्ट करें:!एन!एन!$3!एन!एन!हम शीघ्र ही आपको {{SITENAME}} में सुधार कैसे मदद कर सकते हैं के साथ संपर्क में हो जाएगा.!एन!एन!यदि आप इस अनुरोध को आरंभ नहीं किया है, कृपया इस ई-मेल पर ध्यान न दें और हम तुम कुछ और नहीं भेजेंगे.!एन!एन!शुभकामनाएं, और आपको धन्यवाद!एन!{{SITENAME}} टीम',
);

/** Croatian (Hrvatski)
 * @author Herr Mlinka
 * @author Roberta F.
 * @author SpeedyGonsales
 */
$messages['hr'] = array(
	'articlefeedbackv5' => 'Ocjenjivanje članaka',
	'articlefeedbackv5-desc' => 'Ocjenjivanje članaka (probna inačica)',
	'articlefeedbackv5-survey-question-whyrated' => 'Molimo recite nam zašto ste ocijenili danas ovu stranicu (označite sve što se može primijeniti):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Želio sam pridonijeti sveukupnoj ocjeni stranice',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Nadam se da će moja ocjena imati pozitivno uticati na razvoj stranice',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Želim pridonijeti projektu {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Volim dijeliti svoje mišljenje',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Nisam dao ocjene danas, ali sam želio dati mišljenje o dogradnji',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Ostalo',
	'articlefeedbackv5-survey-question-useful' => 'Jesu li dane ocjene korisne i jasne?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Zašto?',
	'articlefeedbackv5-survey-question-comments' => 'Imate li neki dodatni komentar?',
	'articlefeedbackv5-survey-submit' => 'Pošalji',
	'articlefeedbackv5-survey-title' => 'Molimo odgovorite na nekoliko pitanja',
	'articlefeedbackv5-survey-thanks' => 'Hvala vam na popunjavanju ankete.',
	'articlefeedbackv5-error' => 'Došlo je do pogrješke. 
Molimo, pokušajte ponovno kasnije.',
	'articlefeedbackv5-form-switch-label' => 'Ocijeni ovu stranicu',
	'articlefeedbackv5-form-panel-title' => 'Ocijeni ovu stranicu',
	'articlefeedbackv5-form-panel-clear' => 'Ukloni ovu ocijenu',
	'articlefeedbackv5-form-panel-expertise' => 'Visoko sam obrazovan o ovoj temi',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Imam odgovarajući fakultetski/sveučilišni stupanj',
	'articlefeedbackv5-form-panel-expertise-profession' => 'To je dio moje struke',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'To je duboka osobna strast',
	'articlefeedbackv5-form-panel-expertise-other' => 'Izvor moga znanja nije na ovom popisu',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Zaštita privatnosti',
	'articlefeedbackv5-form-panel-submit' => 'Pošaljite povratnu informaciju',
	'articlefeedbackv5-form-panel-success' => 'Uspješno spremljeno',
	'articlefeedbackv5-form-panel-expiry-title' => 'Vaše su ocjene istekle',
	'articlefeedbackv5-form-panel-expiry-message' => 'Molimo ponovno ocijenite ovu stranicu te pošaljite nove ocjene.',
	'articlefeedbackv5-report-switch-label' => 'Prikaži ocjene ove stranice',
	'articlefeedbackv5-report-panel-title' => 'Ocjene stranice',
	'articlefeedbackv5-report-panel-description' => 'Trenutačni prosjek ocjena.',
	'articlefeedbackv5-report-empty' => 'Nema ocjena',
	'articlefeedbackv5-report-ratings' => '$1 ocjena',
	'articlefeedbackv5-field-trustworthy-label' => 'Vjerodostojno',
	'articlefeedbackv5-field-trustworthy-tip' => 'Smatrate li da ova stranica ima dovoljno izvora i da su oni iz vjerodostojnih izvora?',
	'articlefeedbackv5-field-complete-label' => 'Zaokružena cjelina teme',
	'articlefeedbackv5-field-complete-tip' => 'Mislite li da ova stranica pokriva osnovna područja teme koja bi trebala?',
	'articlefeedbackv5-field-objective-label' => 'Nepristrano',
	'articlefeedbackv5-field-objective-tip' => 'Da li smatrate da ova stranica prikazuje neutralni prikaz iz svih perspektiva o temi?',
	'articlefeedbackv5-field-wellwritten-label' => 'Dobro napisano',
	'articlefeedbackv5-field-wellwritten-tip' => 'Mislite li da je ova stranica dobro organizirana i dobro napisana?',
	'articlefeedbackv5-pitch-reject' => 'Možda kasnije',
	'articlefeedbackv5-pitch-or' => 'ili',
	'articlefeedbackv5-pitch-thanks' => 'Hvala! Vaše su ocjene sačuvane.',
	'articlefeedbackv5-pitch-survey-message' => 'Molimo vas da odvojite trenutak kako biste dovršili kratku anketu.',
	'articlefeedbackv5-pitch-survey-accept' => 'Počni anketu',
	'articlefeedbackv5-pitch-join-message' => 'Želite li kreirati korisnički račun?',
	'articlefeedbackv5-pitch-join-body' => 'Korisnički će vam račun olakšati praćenje vaših uređivanja, uključivanje u rasprave te će te lakše postati dijelom zajednice.',
	'articlefeedbackv5-pitch-join-accept' => 'Otvori novi suradnički račun',
	'articlefeedbackv5-pitch-join-login' => 'Prijavite se',
	'articlefeedbackv5-pitch-edit-message' => 'Jeste li znali da možete uređivati ovu stranicu?',
	'articlefeedbackv5-pitch-edit-accept' => 'Uredi ovu stranicu',
	'articlefeedbackv5-survey-message-success' => 'Hvala vam na popunjavanju ankete.',
	'articlefeedbackv5-survey-message-error' => 'Došlo je do pogrješke. 
Molimo Vas, pokušajte ponovno kasnije.',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'articlefeedbackv5' => 'Přehladna strona k posudkam',
	'articlefeedbackv5-desc' => 'Pohódnoćenje nastawkow (pilotowa wersija)',
	'articlefeedbackv5-survey-question-whyrated' => 'Prošu zdźěl nam, čehodla sy tutu stronu dźensa posudźił (trjechace prošu nakřižować):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Chcych so na cyłkownym pohódnoćenju strony wobdźělić',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Nadźijam so, zo moje pohódnoćene by wuwiće strony pozitiwnje wobwliwowało',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Chcych k {{SITENAME}} přinošować',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Bych rady moje měnjenje dźělił',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Dźensa njejsym žane pohódnoćenja přewjedł, ale chcych swoje měnjenje wo tutej funkciji wuprajić.',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Druhe',
	'articlefeedbackv5-survey-question-useful' => 'Wěriš, zo podate pohódnoćenja su wužite a jasne?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Čehodla?',
	'articlefeedbackv5-survey-question-comments' => 'Maš hišće dalše komentary?',
	'articlefeedbackv5-survey-submit' => 'Wotpósłać',
	'articlefeedbackv5-survey-title' => 'Prošu wotmołw na někotre prašenja',
	'articlefeedbackv5-survey-thanks' => 'Dźakujemy so za twój posudk.',
	'articlefeedbackv5-error' => 'Zmylk je wustupił.
Prošu spytaj pozdźišo hišće raz.',
	'articlefeedbackv5-form-switch-label' => 'Tutu stronu pohódnoćić',
	'articlefeedbackv5-form-panel-title' => 'Tutu stronu pohódnoćić',
	'articlefeedbackv5-form-panel-explanation' => 'Što to je?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:ArticleFeedback',
	'articlefeedbackv5-form-panel-clear' => 'Tute pohódnoćenje wotstronić',
	'articlefeedbackv5-form-panel-expertise' => 'Mam wobšěrne znajomosće wo tutej temje (na přeće)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Sym na wotpowědnej wyšej šuli/uniwersiće studował',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Je dźěl mojeho powołanja',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Je mój konik',
	'articlefeedbackv5-form-panel-expertise-other' => 'Žórło mojich znajomosćow njeje tu podate',
	'articlefeedbackv5-form-panel-submit' => 'Posudki pósłać',
	'articlefeedbackv5-form-panel-success' => 'wuspěšnje składowany',
	'articlefeedbackv5-form-panel-expiry-title' => 'Twoje pohódnoćenja su spadnyli',
	'articlefeedbackv5-form-panel-expiry-message' => 'Prošu pohódnoć tutu stronu znowa a pósćel nowe pohódnoćenja.',
	'articlefeedbackv5-report-switch-label' => 'Pohódnoćenja strony pokazać',
	'articlefeedbackv5-report-panel-title' => 'Pohódnoćenja strony',
	'articlefeedbackv5-report-panel-description' => 'Aktualne přerězkowe pohódnoćenja.',
	'articlefeedbackv5-report-empty' => 'Žane pohódnoćenja',
	'articlefeedbackv5-report-ratings' => '$1 {{PLURAL:$1|pohódnoćenje|pohódnoćeni|pohódnoćenja|pohódnoćenjow}}',
	'articlefeedbackv5-field-trustworthy-label' => 'Dowěry hódny',
	'articlefeedbackv5-field-trustworthy-tip' => 'Měniće, zo tuta strona ma dosć citatow a zo tute citaty su z dowěry hódnych žórłow?',
	'articlefeedbackv5-field-complete-label' => 'Dospołny',
	'articlefeedbackv5-field-complete-tip' => 'Měnicé, zo tuta strona wobkedźbuje wšitke bytostne temowe pola, kotrež měła wobsahować?',
	'articlefeedbackv5-field-objective-label' => 'Wěcowny',
	'articlefeedbackv5-field-objective-tip' => 'Měniš, zo tuta strona pokazuje wurunane předstajenje wšěch perspektiwow tutoho problema?',
	'articlefeedbackv5-field-wellwritten-label' => 'Derje napisany',
	'articlefeedbackv5-field-wellwritten-tip' => 'Měniš, zo tuta strona je derje zorganizowana a derje napisana?',
	'articlefeedbackv5-pitch-reject' => 'Snano pozdźišo',
	'articlefeedbackv5-pitch-or' => 'abo',
	'articlefeedbackv5-pitch-thanks' => 'Měj dźak! Twoje pohódnoćenja su so składowali.',
	'articlefeedbackv5-pitch-survey-message' => 'Prošu bjer sej wokomik časa, zo by so na krótkim naprašowanju wobdźělił.',
	'articlefeedbackv5-pitch-survey-accept' => 'Pohódnoćenje započeć',
	'articlefeedbackv5-pitch-join-message' => 'Sy chcył konto załožić?',
	'articlefeedbackv5-pitch-join-body' => 'Konto budźe ći pomhać twoje změny slědować, so na diskusijach wobdźělić a dźěl zhromadźenstwa być.',
	'articlefeedbackv5-pitch-join-accept' => 'Konto załožić',
	'articlefeedbackv5-pitch-join-login' => 'Přizjewić',
	'articlefeedbackv5-pitch-edit-message' => 'Sy wědźał, zo móžeš tutu stronu wobdźěłać?',
	'articlefeedbackv5-pitch-edit-accept' => 'Tutu stronu wobdźěłać',
	'articlefeedbackv5-survey-message-success' => 'Dźakujemy so za wobdźělenje na naprašowanju.',
	'articlefeedbackv5-survey-message-error' => 'Zmylk je wustupił.
Prošu spytaj pozdźišo hišće raz.',
	'articleFeedbackv5-table-heading-page' => 'Strona',
	'articleFeedbackv5-table-heading-average' => 'Přerězk',
);

/** Hungarian (Magyar)
 * @author Dani
 * @author Hunyadym
 * @author Misibacsi
 * @author Tgr
 */
$messages['hu'] = array(
	'articlefeedbackv5' => 'Cikk értékelése',
	'articlefeedbackv5-desc' => 'Cikk értékelése (kísérleti változat)',
	'articlefeedbackv5-survey-question-origin' => 'Milyen oldalon voltál, amikor elkezdted ezt a felmérést?',
	'articlefeedbackv5-survey-question-whyrated' => 'Kérjük, mondd el nekünk, miért értékelted ezt az oldalt (jelöld meg ay összes megfelelőt):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Befolyásolni akartam, milyen értékelés jelenik meg',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Remélem, hogy az értékelésem pozitívan befolyásolja az oldal fejlődését',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Részt akartam venni a {{SITENAME}} készítésében',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Szeretem megosztani másokkal a véleményemet',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Nem adtam le értékelést, de szerettem volna visszajelzést küldeni erről a funkcióról',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Egyéb',
	'articlefeedbackv5-survey-question-useful' => 'Hasznosnak és világosnak érzed az értékeléseket?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Miért?',
	'articlefeedbackv5-survey-question-comments' => 'Van még további észrevételed?',
	'articlefeedbackv5-survey-submit' => 'Értékelés küldése',
	'articlefeedbackv5-survey-title' => 'Kérjük, válaszolj néhány kérdésre',
	'articlefeedbackv5-survey-thanks' => 'Köszönjük a kérdőív kitöltését!',
	'articlefeedbackv5-survey-disclaimer' => 'A szolgáltatás fejlesztésének érdekében a visszajelzésedet névtelenül megosztjuk a Wikipédia szerkesztőivel.',
	'articlefeedbackv5-error' => 'Hiba történt. Kérlek, próbálkozz később.',
	'articlefeedbackv5-form-switch-label' => 'Oldal értékelése',
	'articlefeedbackv5-form-panel-title' => 'Oldal értékelése',
	'articlefeedbackv5-form-panel-explanation' => 'Mi ez?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:Cikkértékelés',
	'articlefeedbackv5-form-panel-clear' => 'Értékelés eltávolítása',
	'articlefeedbackv5-form-panel-expertise' => 'Jól ismerem ezt a témát (nem kötelező)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Szakirányú felsőoktatási végzettségem van',
	'articlefeedbackv5-form-panel-expertise-profession' => 'A munkám része',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Szenvedélyem a téma',
	'articlefeedbackv5-form-panel-expertise-other' => 'Más okból vagyok jártas benne',
	'articlefeedbackv5-form-panel-helpimprove' => 'Szeretnék segíteni a Wikipédia fejlesztésében, küldjetek nekem egy e-mailt (nem kötelező)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Küldeni fogunk neked egy visszaigazoló e-mailt. Nem osztjuk meg senkivel sem a címedet. $1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Adatvédelmi irányelvek',
	'articlefeedbackv5-form-panel-submit' => 'Értékelés elküldése',
	'articlefeedbackv5-form-panel-pending' => 'Az értékelésed még nem lett elküldve',
	'articlefeedbackv5-form-panel-success' => 'Sikeresen elmentve',
	'articlefeedbackv5-form-panel-expiry-title' => 'Az értékelésed elavult',
	'articlefeedbackv5-form-panel-expiry-message' => 'Kérlek, olvasd át újra az oldalt, és küldd be az új értékelésedet',
	'articlefeedbackv5-report-switch-label' => 'Oldal értékelésének megtekintése',
	'articlefeedbackv5-report-panel-title' => 'Oldal értékelése',
	'articlefeedbackv5-report-panel-description' => 'Jelenlegi átlagos értékelés.',
	'articlefeedbackv5-report-empty' => 'Nincs értékelés',
	'articlefeedbackv5-report-ratings' => '$1 értékelés',
	'articlefeedbackv5-field-trustworthy-label' => 'Megbízható',
	'articlefeedbackv5-field-trustworthy-tip' => 'Elég részletesen vannak-e megadva a források, és megbízhatóak-e?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Hiányoznak a megbízható források',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Kevés a megbízható forrás',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Tűrhetően el van látva megbízható forrásokkal',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Jól el van látva megbízható forrásokkal',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Kitűnően el van látva megbízható forrásokkal',
	'articlefeedbackv5-field-complete-label' => 'Teljes',
	'articlefeedbackv5-field-complete-tip' => 'Elég alaposan tárgyalja-e a fontos témákat?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Hiányzik a legtöbb információ',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Tartalmaz némi információt',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Tartalmazza a legfontosabb információkat, de hiányosságokkal',
	'articlefeedbackv5-field-complete-tooltip-4' => 'Tartalmazza a legtöbb fontos információt',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Minden fontos informciót tartalmaz',
	'articlefeedbackv5-field-objective-label' => 'Objektív',
	'articlefeedbackv5-field-objective-tip' => 'Elfogulatlanul mutatja-e be az összes nézőpontot?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Erősen elfogult',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Mérsékelten elfogult',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Csak minimálisan elfogult',
	'articlefeedbackv5-field-objective-tooltip-4' => 'Nincs nyilvánvaló elfogultság',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Teljesen elfogulatlan',
	'articlefeedbackv5-field-wellwritten-label' => 'Jól megírt',
	'articlefeedbackv5-field-wellwritten-tip' => 'Áttekinthető és jól érthető-e az oldal?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Érthetetlen',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Nehezen érthető',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Valamennyire érthető',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Jól érthető',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Nagyon jól érthető',
	'articlefeedbackv5-pitch-reject' => 'Talán később',
	'articlefeedbackv5-pitch-or' => 'vagy',
	'articlefeedbackv5-pitch-thanks' => 'Köszönjük! Az értékelést elmentettük.',
	'articlefeedbackv5-pitch-survey-message' => 'Kérlek szánj egy kis időt egy rövid felmérés kitöltésére.',
	'articlefeedbackv5-pitch-survey-accept' => 'Felmérés megkezdése',
	'articlefeedbackv5-pitch-join-message' => 'Szerettél volna regisztrálni?',
	'articlefeedbackv5-pitch-join-body' => 'Ha regisztrálsz, könnyen nyomon tudod követni a szerkesztéseidet, jobban be tudsz kapcsolódni a megbeszélésekbe, és a közösség tagjává válhatsz.',
	'articlefeedbackv5-pitch-join-accept' => 'Fiók létrehozása',
	'articlefeedbackv5-pitch-join-login' => 'Bejelentkezés',
	'articlefeedbackv5-pitch-edit-message' => 'Tudtad, hogy szerkesztheted ezt az oldalt?',
	'articlefeedbackv5-pitch-edit-accept' => 'Oldal szerkesztése',
	'articlefeedbackv5-survey-message-success' => 'Köszönjük a kérdőív kitöltését!',
	'articlefeedbackv5-survey-message-error' => 'Hiba történt. Kérlek, próbáld meg később.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'A napi legjobbak és legrosszabbak',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Legtöbbre értékelt oldalak: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Legkevesebbre értékelt oldalak: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'A héten legtöbbet változott',
	'articleFeedbackv5-table-caption-recentlows' => 'Közelmúltbeli mélypontok',
	'articleFeedbackv5-table-heading-page' => 'Oldal',
	'articleFeedbackv5-table-heading-average' => 'Átlag',
	'articleFeedbackv5-copy-above-highlow-tables' => 'Ey egy kísérleti funkció, a [$1 vitalapján] tudoad véleményezni.',
	'articlefeedbackv5-dashboard-bottom' => "'''Megjegyzés''': Folyamatosan kísérletezni fogunk a cikkek listázásának különböző módjaival. Jelenleg a listák a következő cikkeket tartalmazzák:
* a legmagasabbra ill. legalacsonyabbra értékelt oldalakat. Az átlagba csak az elmúlt 24 órában leadott értékelések számítanak bele, és legalább tíz ilyennek kell lennie.
* Közelmúltbeli mélypontok: olyan szócikkek, amelyek valamelyik kérdésre legalább 70%-ban kaptak 1 vagy 2 csillagot az elmúlt 24 órában. Csak a legalább 10 értékelést kapott szócikkek szerepelnek.",
	'articlefeedbackv5-disable-preference' => 'Ne mutassa többet a cikkértékelő dobozt',
	'articlefeedbackv5-emailcapture-response-body' => 'Szia!

Köszönjük, hogy érdeklődtél a {{SITENAME}} fejlesztése iránt.

Kérlek, erősítsd meg az e-mail címedet az alábbi linkre kattintva:

$1

Ha ez valamiért nem működne, látogasd meg ezt az oldalt:

$2

és ott írd be az ellenőrző kódot:

$3

Rövidesen jelezzük, hogyan tudsz segíteni a {{SITENAME}} fejlesztésében.

Ha nem te kérted ezt a levelet, hagyd figyelmen kívül, és nem fogunk több levelet küldeni.

A legjobbakat kívánva
A {{SITENAME}} csapata',
);

/** Interlingua (Interlingua)
 * @author Catrope
 * @author McDutchie
 */
$messages['ia'] = array(
	'articlefeedbackv5' => 'Pannello de evalutation de articulos',
	'articlefeedbackv5-desc' => 'Evalutation de articulos (version pilota)',
	'articlefeedbackv5-survey-question-origin' => 'In qual pagina te trovava tu quando tu comenciava iste sondage?',
	'articlefeedbackv5-survey-question-whyrated' => 'Per favor dice nos proque tu ha evalutate iste pagina hodie (marca tote le optiones applicabile):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Io voleva contribuer al evalutation general del pagina',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Io spera que mi evalutation ha un effecto positive sur le disveloppamento del pagina',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Io voleva contribuer a {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Me place condivider mi opinion',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Io non dava un evalutation hodie, ma io voleva dar mi opinion super le functionalitate',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Altere',
	'articlefeedbackv5-survey-question-useful' => 'Crede tu que le evalutationes providite es utile e clar?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Proque?',
	'articlefeedbackv5-survey-question-comments' => 'Ha tu additional commentos?',
	'articlefeedbackv5-survey-submit' => 'Submitter',
	'articlefeedbackv5-survey-title' => 'Per favor responde a alcun questiones',
	'articlefeedbackv5-survey-thanks' => 'Gratias pro completar le questionario.',
	'articlefeedbackv5-survey-disclaimer' => 'Per submitter, tu te declara de accordo con transparentia sub iste [http://wikimediafoundation.org/wiki/Feedback_privacy_statement conditiones]',
	'articlefeedbackv5-error' => 'Un error ha occurrite. Per favor reproba plus tarde.',
	'articlefeedbackv5-form-switch-label' => 'Evalutar iste pagina',
	'articlefeedbackv5-form-panel-title' => 'Evalutar iste pagina',
	'articlefeedbackv5-form-panel-explanation' => 'Que es isto?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:Commentar articulos',
	'articlefeedbackv5-form-panel-clear' => 'Remover iste evalutation',
	'articlefeedbackv5-form-panel-expertise' => 'Io es multo ben informate super iste thema (optional)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Io ha un grado relevante de collegio/universitate',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Illo face parte de mi profession',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Es un passion personal profunde',
	'articlefeedbackv5-form-panel-expertise-other' => 'Le origine de mi cognoscentia non es listate hic',
	'articlefeedbackv5-form-panel-helpimprove' => 'Io volerea adjutar a meliorar Wikipedia, per favor invia me un e-mail (optional)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Nos te inviara un e-mail de confirmation. Nos non divulgara tu adresse de e-mail a exterior personas secundo nostre $1.',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'declaration de confidentialitate super le retroaction',
	'articlefeedbackv5-form-panel-submit' => 'Submitter evalutationes',
	'articlefeedbackv5-form-panel-pending' => 'Tu evalutationes non ha essite submittite',
	'articlefeedbackv5-form-panel-success' => 'Salveguardate con successo',
	'articlefeedbackv5-form-panel-expiry-title' => 'Tu evalutationes ha expirate',
	'articlefeedbackv5-form-panel-expiry-message' => 'Per favor re-evaluta iste pagina e submitte nove evalutationes.',
	'articlefeedbackv5-report-switch-label' => 'Monstrar evalutationes',
	'articlefeedbackv5-report-panel-title' => 'Evalutationes del pagina',
	'articlefeedbackv5-report-panel-description' => 'Le media actual de evalutationes.',
	'articlefeedbackv5-report-empty' => 'Nulle evalutation',
	'articlefeedbackv5-report-ratings' => '$1 evalutationes',
	'articlefeedbackv5-field-trustworthy-label' => 'Digne de fide',
	'articlefeedbackv5-field-trustworthy-tip' => 'Pensa tu que iste pagina ha sufficiente citationes e que iste citationes refere a fontes digne de fide?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Care de fontes digne de fide',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Pauc fontes digne de fide',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Adequate fontes digne de fide',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Bon fontes digne de fide',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Excellente fontes digne de fide',
	'articlefeedbackv5-field-complete-label' => 'Complete',
	'articlefeedbackv5-field-complete-tip' => 'Pensa tu que iste pagina coperi le themas essential que illo deberea coperir?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Manca le major parte del information',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Contine alcun information',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Contine information importante, ma con lacunas',
	'articlefeedbackv5-field-complete-tooltip-4' => 'Contine le major parte del information importante',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Contine information comprehensive',
	'articlefeedbackv5-field-objective-label' => 'Impartial',
	'articlefeedbackv5-field-objective-tip' => 'Pensa tu que iste pagina monstra un representation juste de tote le perspectivas super le question?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Multo partial',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Moderatemente partial',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Minimalmente partial',
	'articlefeedbackv5-field-objective-tooltip-4' => 'Non obviemente partial',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Completemente impartial',
	'articlefeedbackv5-field-wellwritten-label' => 'Ben scribite',
	'articlefeedbackv5-field-wellwritten-tip' => 'Pensa tu que iste pagina es ben organisate e ben scribite?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Incomprehensibile',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Difficile a comprender',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Adequatemente clar',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Ben clar',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Exceptionalmente clar',
	'articlefeedbackv5-pitch-reject' => 'Forsan plus tarde',
	'articlefeedbackv5-pitch-or' => 'o',
	'articlefeedbackv5-pitch-thanks' => 'Gratias! Tu evalutation ha essite salveguardate.',
	'articlefeedbackv5-pitch-survey-message' => 'Per favor prende un momento pro completar un curte questionario.',
	'articlefeedbackv5-pitch-survey-accept' => 'Comenciar sondage',
	'articlefeedbackv5-pitch-join-message' => 'Vole tu crear un conto?',
	'articlefeedbackv5-pitch-join-body' => 'Un conto te adjuta a traciar tu modificationes, a participar in discussiones e a facer parte del communitate.',
	'articlefeedbackv5-pitch-join-accept' => 'Crear conto',
	'articlefeedbackv5-pitch-join-login' => 'Aperir session',
	'articlefeedbackv5-pitch-edit-message' => 'Sapeva tu que tu pote modificar iste articulo?',
	'articlefeedbackv5-pitch-edit-accept' => 'Modificar iste articulo',
	'articlefeedbackv5-survey-message-success' => 'Gratias pro haber respondite al inquesta.',
	'articlefeedbackv5-survey-message-error' => 'Un error ha occurrite.
Per favor reproba plus tarde.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Altos e bassos de hodie',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Articulos le plus appreciate: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Articulos le minus appreciate: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Le plus modificate iste septimana',
	'articleFeedbackv5-table-caption-recentlows' => 'Bassos recente',
	'articleFeedbackv5-table-heading-page' => 'Pagina',
	'articleFeedbackv5-table-heading-average' => 'Medie',
	'articleFeedbackv5-copy-above-highlow-tables' => 'Iste function es experimental.  Per favor lassa tu opinion in le [$1 pagina de discussion].',
	'articlefeedbackv5-dashboard-bottom' => "'''Nota''': Nos continua a experimentar con differente modos de mitter articulos in evidentia in iste pannellos.  A presente, le pannellos include le sequente articulos:
* Paginas con le evalutationes le plus alte/basse: articulos que ha recipite al minus 10 evalutationes durante le ultime 24 horas.  Le media es calculate usante tote le evalutationes submittite durante le ultime 24 horas.
* Bassos recente: articulos que recipeva 70% o plus de evalutationes basse (2 stellas o minus) in qualcunque categoria durante le ultime 24 horas. Solmente le articulos que ha recipite al minus 10 evalutationes durante le ultime 24 horas es includite.",
	'articlefeedbackv5-disable-preference' => 'Non monstrar le widget de evalutation de articulos in paginas',
	'articlefeedbackv5-emailcapture-response-body' => 'Salute!

Gratias pro tu interesse in adjutar a meliorar {{SITENAME}}.

Per favor prende un momento pro confirmar tu adresse de e-mail. Clicca super le ligamine sequente:

$1

Alternativemente, visita:

$2

...e entra le sequente codice de confirmation:

$3

Nos va tosto contactar te pro explicar como tu pote adjutar a meliorar {{SITENAME}}.

Si tu non ha initiate iste requesta, per favor ignora iste e-mail e nos non te inviara altere cosa.

Optime salutes, e multe gratias,
Le equipa de {{SITENAME}}',
);

/** Indonesian (Bahasa Indonesia)
 * @author Farras
 * @author IvanLanin
 * @author Kenrick95
 */
$messages['id'] = array(
	'articlefeedbackv5' => 'Dasbor umpan balik artikel',
	'articlefeedbackv5-desc' => 'Penilaian artikel (versi percobaan)',
	'articlefeedbackv5-survey-question-origin' => 'Apa halaman yang sedang Anda lihat saat memulai survei ini?',
	'articlefeedbackv5-survey-question-whyrated' => 'Harap beritahu kami mengapa Anda menilai halaman ini hari ini (centang semua yang benar):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Saya ingin berkontribusi untuk peringkat keseluruhan halaman',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Saya harap penilaian saya akan memberi dampak positif terhadap pengembangan halaman ini',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Saya ingin berkontribusi ke {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Saya ingin berbagi pendapat',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Saya tidak memberikan penilaian hari ini, tetapi ingin memberikan umpan balik pada fitur tersebut',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Lainnya',
	'articlefeedbackv5-survey-question-useful' => 'Apakah Anda yakin bahwa peringkat yang diberikan berguna dan jelas?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Mengapa?',
	'articlefeedbackv5-survey-question-comments' => 'Apakah Anda memiliki komentar tambahan?',
	'articlefeedbackv5-survey-submit' => 'Kirim',
	'articlefeedbackv5-survey-title' => 'Silakan jawab beberapa pertanyaan',
	'articlefeedbackv5-survey-thanks' => 'Terima kasih telah mengisi survei ini.',
	'articlefeedbackv5-error' => 'Telah terjadi sebuah kesalahan. Silakan coba lagi nanti.',
	'articlefeedbackv5-form-switch-label' => 'Berikan nilai halaman ini',
	'articlefeedbackv5-form-panel-title' => 'Nilai halaman ini',
	'articlefeedbackv5-form-panel-explanation' => 'Apa ini?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:ArticleFeedback',
	'articlefeedbackv5-form-panel-clear' => 'Hapus penilaian ini',
	'articlefeedbackv5-form-panel-expertise' => 'Saya sangat mengetahui topik ini (opsional)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Saya memiliki gelar perguruan tinggi yang relevan',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Itu bagian dari profesi saya',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Itu merupakan hasrat pribadi yang mendalam',
	'articlefeedbackv5-form-panel-expertise-other' => 'Sumber pengetahuan saya tidak tercantum di sini',
	'articlefeedbackv5-form-panel-helpimprove' => 'Saya ingin membantu meningkatkan Wikipedia. Kirimi saya surel (opsional)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Kami akan mengirim surel konfirmasi. Kami tidak akan berbagi alamat Anda dengan siapa pun. $1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Kebijakan privasi',
	'articlefeedbackv5-form-panel-submit' => 'Kirim peringkat',
	'articlefeedbackv5-form-panel-pending' => 'Penilaian Anda belum dikirim',
	'articlefeedbackv5-form-panel-success' => 'Berhasil disimpan',
	'articlefeedbackv5-form-panel-expiry-title' => 'Peringkat Anda sudah kedaluwarsa',
	'articlefeedbackv5-form-panel-expiry-message' => 'Silakan evaluasi kembali halaman ini dan kirimkan peringkat baru.',
	'articlefeedbackv5-report-switch-label' => 'Lihat penilaian halaman',
	'articlefeedbackv5-report-panel-title' => 'Penilaian halaman',
	'articlefeedbackv5-report-panel-description' => 'Peringkat rata-rata saat ini',
	'articlefeedbackv5-report-empty' => 'Belum berperingkat',
	'articlefeedbackv5-report-ratings' => '$1 penilaian',
	'articlefeedbackv5-field-trustworthy-label' => 'Dapat dipercaya',
	'articlefeedbackv5-field-trustworthy-tip' => 'Apakah Anda merasa bahwa halaman ini memiliki cukup kutipan dan bahwa kutipan tersebut berasal dari sumber tepercaya?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Kekurangan sumber tepercaya',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Beberapa sumber tepercaya',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Sumber tepercaya yang memadai',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Sumber tepercaya yang baik',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Sumber tepercaya yang sangat baik',
	'articlefeedbackv5-field-complete-label' => 'Lengkap',
	'articlefeedbackv5-field-complete-tip' => 'Apakah Anda merasa bahwa halaman ini mencakup wilayah topik penting yang seharusnya?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Kekurangan sebagian besar informasi',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Berisi beberapa informasi',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Berisi informasi penting, tetapi dengan kesenjangan',
	'articlefeedbackv5-field-complete-tooltip-4' => 'Berisi sebagian besar informasi penting',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Cakupan komprehensif',
	'articlefeedbackv5-field-objective-label' => 'Tidak bias',
	'articlefeedbackv5-field-objective-tip' => 'Apakah Anda merasa bahwa halaman ini menunjukkan representasi yang adil dari semua perspektif tentang masalah ini?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Sangat menyimpang',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Menyimpang',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Menyimpang minim',
	'articlefeedbackv5-field-objective-tooltip-4' => 'Tidak ada penyimpangan',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Seluruhnya tidak menyimpang',
	'articlefeedbackv5-field-wellwritten-label' => 'Ditulis dengan baik',
	'articlefeedbackv5-field-wellwritten-tip' => 'Apakah Anda merasa bahwa halaman ini disusun dan ditulis dengan baik?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'TIdak dapat dimengerti',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Sulit dipahami',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Kejelasan memadai',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Kejelasan baik',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Kejelasan yang luar biasa',
	'articlefeedbackv5-pitch-reject' => 'Mungkin nanti',
	'articlefeedbackv5-pitch-or' => 'atau',
	'articlefeedbackv5-pitch-thanks' => 'Terima kasih! Penilaian Anda telah disimpan.',
	'articlefeedbackv5-pitch-survey-message' => 'Harap luangkan waktu untuk mengisi survei singkat.',
	'articlefeedbackv5-pitch-survey-accept' => 'Mulai survei',
	'articlefeedbackv5-pitch-join-message' => 'Apakah Anda ingin membuat akun?',
	'articlefeedbackv5-pitch-join-body' => 'Akun akan membantu Anda melacak suntingan Anda, terlibat dalam diskusi, dan menjadi bagian dari komunitas.',
	'articlefeedbackv5-pitch-join-accept' => 'Buat account',
	'articlefeedbackv5-pitch-join-login' => 'Masuk log',
	'articlefeedbackv5-pitch-edit-message' => 'Tahukah Anda bahwa Anda dapat menyunting laman ini?',
	'articlefeedbackv5-pitch-edit-accept' => 'Sunting halaman ini',
	'articlefeedbackv5-survey-message-success' => 'Terima kasih telah mengisi survei ini.',
	'articlefeedbackv5-survey-message-error' => 'Kesalahan terjadi.
Silakan coba lagi.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Kenaikan dan penurunan hari ini',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Artikel berperingkat tertinggi: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Artikel berperingkat terendah: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Paling berubah minggu ini',
	'articleFeedbackv5-table-caption-recentlows' => 'Penurunan terbaru',
	'articleFeedbackv5-table-heading-page' => 'Halaman',
	'articleFeedbackv5-table-heading-average' => 'Rata-rata',
	'articleFeedbackv5-copy-above-highlow-tables' => 'Ini adalah fitur percobaan. Harap berikan masukan pada [$1 halaman pembicaraan].',
	'articlefeedbackv5-disable-preference' => 'Jangan tampilkan widget umpan balik artikel pada halaman',
	'articlefeedbackv5-emailcapture-response-body' => 'Halo!

Terima kasih atas minat Anda untuk membantu meningkatkan {{SITENAME}}.

Harap luangkan waktu untuk mengonfirmasi surel Anda dengan mengklik pranala di bawah ini:

$1

Anda juga dapat mengunjungi:

$2

Dan masukkan kode konfirmasi berikut:

$3

Dalam waktu dekat, kami akan menghubungi Anda dan menerangkan bagaimana cara membantu peningkatan {{SITENAME}}.

Jika Anda tidak mengajukan permintaan ini, harap mengabaikan surel ini dan kami akan tidak mengirimkan apa pun.

Salam, dan terima kasih,
Tim {{SITENAME}}',
);

/** Italian (Italiano)
 * @author Beta16
 * @author Pietrodn
 */
$messages['it'] = array(
	'articlefeedbackv5' => 'Cruscotto valutazione pagine',
	'articlefeedbackv5-desc' => 'Valutazione pagina (versione pilota)',
	'articlefeedbackv5-survey-question-origin' => 'In quale pagina eravate quando avete iniziato questa indagine?',
	'articlefeedbackv5-survey-question-whyrated' => 'Esprimi il motivo per cui oggi hai valutato questa pagina (puoi selezionare più opzioni):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Ho voluto contribuire alla valutazione complessiva della pagina',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Spero che il mio giudizio influenzi positivamente lo sviluppo della pagina',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Ho voluto contribuire a {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Mi piace condividere la mia opinione',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Non ho fornito valutazioni oggi, ma ho voluto lasciare un feedback sulla funzionalità',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Altro',
	'articlefeedbackv5-survey-question-useful' => 'Pensi che le valutazioni fornite siano utili e chiare?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Perché?',
	'articlefeedbackv5-survey-question-comments' => 'Hai altri commenti?',
	'articlefeedbackv5-survey-submit' => 'Invia',
	'articlefeedbackv5-survey-title' => 'Per favore, rispondi ad alcune domande',
	'articlefeedbackv5-survey-thanks' => 'Grazie per aver compilato il questionario.',
	'articlefeedbackv5-survey-disclaimer' => 'Per migliorare questa funzionalità, il tuo feedback potrebbe essere condiviso in forma anonima con la comunità di Wikipedia.',
	'articlefeedbackv5-error' => 'Si è verificato un errore. 
Riprova più tardi.',
	'articlefeedbackv5-form-switch-label' => 'Valuta questa pagina',
	'articlefeedbackv5-form-panel-title' => 'Valuta questa pagina',
	'articlefeedbackv5-form-panel-explanation' => "Cos'è questo?",
	'articlefeedbackv5-form-panel-clear' => 'Cancella questo giudizio',
	'articlefeedbackv5-form-panel-expertise' => 'Conosco molto bene questo argomento (opzionale)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Ho una laurea pertinente alla materia',
	'articlefeedbackv5-form-panel-expertise-profession' => 'È parte della mia professione',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'È una profonda passione personale',
	'articlefeedbackv5-form-panel-expertise-other' => 'La fonte della mia conoscenza non è elencata qui',
	'articlefeedbackv5-form-panel-helpimprove' => 'Vorrei contribuire a migliorare Wikipedia, inviatemi una e-mail (facoltativo)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Ti invieremo una e-mail di conferma. Non condivideremo il tuo indirizzo con nessuno. $1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Informazioni sulla privacy',
	'articlefeedbackv5-form-panel-submit' => 'Invia voti',
	'articlefeedbackv5-form-panel-pending' => 'Le tue valutazioni non sono state ancora inviate',
	'articlefeedbackv5-form-panel-success' => 'Salvato con successo',
	'articlefeedbackv5-form-panel-expiry-title' => 'Le tue valutazioni sono obsolete',
	'articlefeedbackv5-form-panel-expiry-message' => 'Valuta nuovamente questa pagina ed inviaci i tuoi giudizi.',
	'articlefeedbackv5-report-switch-label' => 'Mostra i risultati',
	'articlefeedbackv5-report-panel-title' => 'Giudizio pagina',
	'articlefeedbackv5-report-panel-description' => 'Valutazione media attuale.',
	'articlefeedbackv5-report-empty' => 'Nessuna valutazione',
	'articlefeedbackv5-report-ratings' => '$1 voti',
	'articlefeedbackv5-field-trustworthy-label' => 'Affidabile',
	'articlefeedbackv5-field-trustworthy-tip' => 'Ritieni che questa pagina abbia citazioni sufficienti e che queste citazioni provengano da fonti attendibili?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Manca di fonti affidabili',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Poche fonti affidabili',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Adeguate fonti affidabili',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Buone fonti affidabili',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Eccellenti fonti affidabili',
	'articlefeedbackv5-field-complete-label' => 'Completa',
	'articlefeedbackv5-field-complete-tip' => 'Ritieni che questa pagina copra le aree tematiche essenziali che dovrebbe?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Manca la maggior parte delle informazioni',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Contiene alcune informazioni',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Contiene le informazioni chiave, ma con lacune',
	'articlefeedbackv5-field-complete-tooltip-4' => 'Contiene la maggior parte delle informazioni chiave',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Trattazione completa',
	'articlefeedbackv5-field-objective-label' => 'Obiettiva',
	'articlefeedbackv5-field-objective-tip' => 'Ritieni che questa pagina mostri una rappresentazione equa di tutti i punti di vista sul tema?',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Completamente imparziale',
	'articlefeedbackv5-field-wellwritten-label' => 'Ben scritta',
	'articlefeedbackv5-field-wellwritten-tip' => 'Ritieni che questa pagina sia ben organizzata e ben scritta?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Incomprensibile',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Difficile da capire',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Abbastanza chiaro',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Molto chiaro',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Eccezionalmente comprensibile',
	'articlefeedbackv5-pitch-reject' => 'Forse più tardi',
	'articlefeedbackv5-pitch-or' => 'o',
	'articlefeedbackv5-pitch-thanks' => 'Grazie! Le tue valutazioni sono state salvate.',
	'articlefeedbackv5-pitch-survey-message' => 'Spendi un momento per completare un breve sondaggio.',
	'articlefeedbackv5-pitch-survey-accept' => 'Inizia sondaggio',
	'articlefeedbackv5-pitch-join-message' => 'Vuoi creare un account?',
	'articlefeedbackv5-pitch-join-body' => 'Un account ti aiuterà a tenere traccia delle tue modifiche, a partecipare a discussioni e ad essere parte della comunità.',
	'articlefeedbackv5-pitch-join-accept' => 'Crea un nuovo utente',
	'articlefeedbackv5-pitch-join-login' => 'Entra',
	'articlefeedbackv5-pitch-edit-message' => 'Sai che è possibile modificare questa pagina?',
	'articlefeedbackv5-pitch-edit-accept' => 'Modifica questa pagina',
	'articlefeedbackv5-survey-message-success' => 'Grazie per aver compilato il questionario.',
	'articlefeedbackv5-survey-message-error' => 'Si è verificato un errore. 
Riprova più tardi.',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Articoli con punteggi più alti: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Articoli con punteggi più bassi: $1',
	'articleFeedbackv5-table-heading-page' => 'Pagina',
	'articleFeedbackv5-table-heading-average' => 'Media',
	'articleFeedbackv5-copy-above-highlow-tables' => 'Questa è una funzione sperimentale. Lascia un feedback sulla [$1 pagina di discussione].',
	'articlefeedbackv5-disable-preference' => 'Non mostrare il widget di valutazione sulle pagine (Article Feedback)',
);

/** Japanese (日本語)
 * @author Fryed-peach
 * @author Marine-Blue
 * @author Ohgi
 * @author Schu
 * @author Whym
 * @author Yanajin66
 * @author 青子守歌
 */
$messages['ja'] = array(
	'articlefeedbackv5' => '記事のフィードバックのダッシュ​​ボード',
	'articlefeedbackv5-desc' => '記事の評価',
	'articlefeedbackv5-survey-question-origin' => 'このアンケートを始めたときにいたページはどのページですか？',
	'articlefeedbackv5-survey-question-whyrated' => '今日、なぜこのページを評価したか教えてください（該当するものすべてにチェックを入れてください）：',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'ページの総合的評価を投稿したかった',
	'articlefeedbackv5-survey-answer-whyrated-development' => '自分の評価が、このページの成長に良い影響を与えることを望んでいる',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => '{{SITENAME}}に貢献したい',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => '意見を共有したい',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => '今日は評価しなかったが、この機能に関するフィードバックをしたかった。',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'その他',
	'articlefeedbackv5-survey-question-useful' => 'これらの評価は、分かりやすく、役に立つものだと思いますか？',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'なぜですか？',
	'articlefeedbackv5-survey-question-comments' => '他に追加すべきコメントがありますか？',
	'articlefeedbackv5-survey-submit' => '送信',
	'articlefeedbackv5-survey-title' => '質問に少しお答えください',
	'articlefeedbackv5-survey-thanks' => '調査に記入していただき、ありがとうございます。',
	'articlefeedbackv5-survey-disclaimer' => 'この機能を改善する助けとするために、お寄せいただいたご意見は匿名でウィキペディアコミュニティに共有される場合があります。',
	'articlefeedbackv5-error' => 'エラーが発生しました。後でもう一度試してください。',
	'articlefeedbackv5-form-switch-label' => 'このページを評価',
	'articlefeedbackv5-form-panel-title' => 'このページを評価',
	'articlefeedbackv5-form-panel-explanation' => 'これは何？',
	'articlefeedbackv5-form-panel-clear' => 'この評価を除去する',
	'articlefeedbackv5-form-panel-expertise' => 'この話題について、高度な知識を持っている（自由選択）',
	'articlefeedbackv5-form-panel-expertise-studies' => '関連する大学の学位を持っている',
	'articlefeedbackv5-form-panel-expertise-profession' => '自分の職業の一部である',
	'articlefeedbackv5-form-panel-expertise-hobby' => '個人的に深い情熱を注いでいる',
	'articlefeedbackv5-form-panel-expertise-other' => '自分の知識源はこの中にない',
	'articlefeedbackv5-form-panel-helpimprove' => 'ウィキペディアを改善するための電子メールを受信する（自由選択）',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'プライバシー・ポリシー',
	'articlefeedbackv5-form-panel-submit' => '評価を送信',
	'articlefeedbackv5-form-panel-pending' => 'あなたの評価がまだ送信されていません',
	'articlefeedbackv5-form-panel-success' => '保存に成功',
	'articlefeedbackv5-form-panel-expiry-title' => 'あなたの評価の有効期限が切れました',
	'articlefeedbackv5-form-panel-expiry-message' => 'このページを再評価して、新しい評価を送信してください。',
	'articlefeedbackv5-report-switch-label' => 'ページの評価を見る',
	'articlefeedbackv5-report-panel-title' => 'ページの評価',
	'articlefeedbackv5-report-panel-description' => '現在の評価の平均。',
	'articlefeedbackv5-report-empty' => '評価なし',
	'articlefeedbackv5-report-ratings' => '$1 の評価',
	'articlefeedbackv5-field-trustworthy-label' => '信頼性',
	'articlefeedbackv5-field-trustworthy-tip' => 'このページは、十分な出典があり、それらの出典は信頼できる情報源によるものですか？',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => '信頼できる情報源を欠いている',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'いくつかの信頼できる情報源',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => '十分な信頼できる情報源',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => '優良な信頼できる情報源',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'たいへん信頼できる情報源',
	'articlefeedbackv5-field-complete-label' => '網羅性',
	'articlefeedbackv5-field-complete-tip' => 'この記事は、含まれるべき重要な話題が含まれていると感じられますか？',
	'articlefeedbackv5-field-complete-tooltip-1' => 'ほとんどの情報が欠落している',
	'articlefeedbackv5-field-complete-tooltip-2' => 'いくつかの情報が含まれている',
	'articlefeedbackv5-field-complete-tooltip-3' => '主要な情報が含まれているが、食い違いがある',
	'articlefeedbackv5-field-complete-tooltip-4' => '多くの重要な情報が含まれている',
	'articlefeedbackv5-field-complete-tooltip-5' => '包括的に網羅している',
	'articlefeedbackv5-field-objective-label' => '客観性',
	'articlefeedbackv5-field-objective-tip' => 'このページは、ある問題に対する全ての観点を平等に説明していると思いますか？',
	'articlefeedbackv5-field-objective-tooltip-1' => '大きく偏っている',
	'articlefeedbackv5-field-objective-tooltip-2' => 'ある程度の偏りがある',
	'articlefeedbackv5-field-objective-tooltip-3' => '少しながら偏りがある',
	'articlefeedbackv5-field-objective-tooltip-4' => '明らかな偏りはない',
	'articlefeedbackv5-field-objective-tooltip-5' => '完全に公平です',
	'articlefeedbackv5-field-wellwritten-label' => '文章力',
	'articlefeedbackv5-field-wellwritten-tip' => 'この記事は、良く整理され、良く書かれていると思いますか？',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => '理解できない',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => '理解することは困難',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => '十分に明快',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => '優れた明快さ',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => '例外的な明快さ',
	'articlefeedbackv5-pitch-reject' => 'たぶん後で行なう',
	'articlefeedbackv5-pitch-or' => 'または',
	'articlefeedbackv5-pitch-thanks' => 'ありがとうございました。評価は保存されました。',
	'articlefeedbackv5-pitch-survey-message' => '短いアンケートにご協力ください。',
	'articlefeedbackv5-pitch-survey-accept' => 'アンケートを開始',
	'articlefeedbackv5-pitch-join-message' => 'アカウントを作成しませんか。',
	'articlefeedbackv5-pitch-join-body' => 'アカウントを作成することで、自分自身の編集を振り返ることが容易になり、議論に参加しやすくなり、コミュニティの一員にもなれます。',
	'articlefeedbackv5-pitch-join-accept' => 'アカウント作成',
	'articlefeedbackv5-pitch-join-login' => 'ログイン',
	'articlefeedbackv5-pitch-edit-message' => 'このページを編集できることをご存じですか。',
	'articlefeedbackv5-pitch-edit-accept' => 'このページを編集',
	'articlefeedbackv5-survey-message-success' => 'アンケートに記入していただきありがとうございます。',
	'articlefeedbackv5-survey-message-error' => 'エラーが発生しました。
後でもう一度試してください。',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => '今日の最高値と最低値',
	'articleFeedbackv5-table-caption-dailyhighs' => '最も高い評価があるページ：$1',
	'articleFeedbackv5-table-caption-dailylows' => '最も低い評価があるページ：$1',
	'articleFeedbackv5-table-heading-page' => 'ページ',
	'articleFeedbackv5-table-heading-average' => '平均',
);

/** Georgian (ქართული)
 * @author BRUTE
 * @author David1010
 * @author Dawid Deutschland
 * @author ITshnik
 */
$messages['ka'] = array(
	'articlefeedbackv5' => 'სტატიის შეფასება',
	'articlefeedbackv5-desc' => 'სტატიის შეფასება',
	'articlefeedbackv5-survey-question-whyrated' => 'გთხოვთ შეგვატყობინეთ, თუ რატომ შეაფასეთ დღეს ეს სტატია (შეამოწმეთ სისწორე)',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'მე ვისურვებდი სტატიის შეფასებაში მონაწილეობის მიღებას',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'ვიმედოვნებ, რომ ჩემი შეფასება დადებითად აისახება სტატიის მომავალ განვითარებაზე',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'მე ვისურვებდი {{SITENAME}}-ში მონაწილეობას',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'მე სიამოვნებით გაგიზიარებთ ჩემს აზრს',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'სხვა',
	'articlefeedbackv5-survey-question-useful' => 'გჯერათ, რომ მოცემული შეფასებები გამოსაყენებელი და გასაგებია?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'რატომ?',
	'articlefeedbackv5-survey-question-comments' => 'კიდევ დაამატებთ რამეს?',
	'articlefeedbackv5-survey-submit' => 'შენახვა',
	'articlefeedbackv5-survey-title' => 'გთხოვთ, გვიპასუხეთ რამდენიმე შეკითხვაზე',
	'articlefeedbackv5-survey-thanks' => 'გმადლობთ საპასუხო შეტყობინებისათვის',
	'articlefeedbackv5-error' => 'წარმოიშვა რაღაც შეცდომა. გთხოვთ სცადეთ მოგვიანებით.',
	'articlefeedbackv5-form-switch-label' => 'ამ გვერდის შეფასება',
	'articlefeedbackv5-form-panel-title' => 'ამ გვერდის შეფასება',
	'articlefeedbackv5-form-panel-clear' => 'შეფასება წაიშალა',
	'articlefeedbackv5-form-panel-expertise' => 'მე მაქვს წინასწარი ცოდნა ამ თემის შესახებ (არასავალდებულო)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'მე ეს კოლეჯში/უმაღლეს სასწავლებელში ვისწავლე',
	'articlefeedbackv5-form-panel-expertise-profession' => 'ეს ჩემი პროფესიის ნაწილია',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'ამ თემასთან დაკავშირებით მე ღრმა პირადული ინტერესები მაქვს',
	'articlefeedbackv5-form-panel-expertise-other' => 'ჩემი ცოდნის წყარო აქ მოცემული არაა',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'ანონიმურობის პოლიტიკა',
	'articlefeedbackv5-form-panel-submit' => 'თანხმობა შეფასებაზე',
	'articlefeedbackv5-form-panel-success' => 'შენახულია წარმატებით',
	'articlefeedbackv5-report-switch-label' => 'გვერდის შეფასებების ხილვა',
	'articlefeedbackv5-report-panel-title' => 'ამ გვერდის შეფასებები',
	'articlefeedbackv5-report-panel-description' => 'შეფასების ამჟამინდელი შედეგები',
	'articlefeedbackv5-report-empty' => 'შეფასებები არაა',
	'articlefeedbackv5-report-ratings' => '$1 შეფასება',
	'articlefeedbackv5-field-trustworthy-label' => 'სანდო',
	'articlefeedbackv5-field-trustworthy-tip' => 'ფიქრობთ, რომ ეს სტატია საკმარისი რაოდენობით შეიცავს სანდო წყაროებს?',
	'articlefeedbackv5-field-complete-label' => 'დასრულებულია',
	'articlefeedbackv5-field-complete-tip' => 'მიგაჩნიათ, რომ ეს სტატია შეიცავს მისივე შინაარსთან დაკავშირებულ ყველა მნიშვნელოვან ასპექტს?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'ინფორმაციის დიდი ნაწილი დაკარგულია',
	'articlefeedbackv5-field-objective-label' => 'მიუკერძოებელია',
	'articlefeedbackv5-field-objective-tip' => 'მიგაჩნიათ, რომ ეს სტატია შეიცავს მისივე თემასთან დაკავშირებული წარმოდგენის შესახებ მიუკერძოებელ ინფორმაციას?',
	'articlefeedbackv5-field-wellwritten-label' => 'კარგად დაწერილი',
	'articlefeedbackv5-field-wellwritten-tip' => 'მიგაჩნიათ, რომ ეს სტატია კარგი სტრუქტურისაა და კარგადაა დაწერილი?',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'გასაგებად ძნელი',
	'articlefeedbackv5-pitch-reject' => 'იქნებ მოგვიანებით',
	'articlefeedbackv5-pitch-or' => 'ან',
	'articlefeedbackv5-pitch-thanks' => 'გმადლობთ! თქვენი შეფასება შენახულია.',
	'articlefeedbackv5-pitch-survey-message' => 'გთხოვთ, გამონახეთ მცირე დრო პატარა გამოკითხვაში მონაწილეობის მისაღებად.',
	'articlefeedbackv5-pitch-survey-accept' => 'გამოკითხვის დაწყება',
	'articlefeedbackv5-pitch-join-message' => 'იცით, რომ თქვენ შეგიძლიათ სამომხმარებლო ანგარიშის შექმნა?',
	'articlefeedbackv5-pitch-join-accept' => 'გახსენი ანგარიში',
	'articlefeedbackv5-pitch-join-login' => 'შესვლა',
	'articlefeedbackv5-pitch-edit-message' => 'იცით, რომ თქვენ ამ სტატიის რედაქტირება შეგიძლიათ?',
	'articlefeedbackv5-pitch-edit-accept' => 'ამ გვერდის რედაქტირება',
	'articlefeedbackv5-survey-message-success' => 'გმადლობთ გამოკითხვაში მონაწილეობისათვის.',
	'articlefeedbackv5-survey-message-error' => 'წარმოიშვა რაღაც შეცდომა.
გთხოვთ სცადეთ მოგვიანებით.',
);

/** Korean (한국어)
 * @author Klutzy
 * @author Kwj2772
 * @author Ricolyuki
 */
$messages['ko'] = array(
	'articlefeedbackv5' => '문서 평가 현황',
	'articlefeedbackv5-desc' => '문서 평가 (파일럿 버전)',
	'articlefeedbackv5-survey-question-origin' => '이 설문 조사를 시작할 때에 어느 문서를 보고 있었나요?',
	'articlefeedbackv5-survey-question-whyrated' => '오늘 이 문서를 왜 평가했는지 알려주십시오 (해당되는 모든 항목에 체크해주세요):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => '이 문서에 대한 전체적인 평가에 기여하고 싶어서',
	'articlefeedbackv5-survey-answer-whyrated-development' => '내가 한 평가가 문서 발전에 긍정적인 영향을 줄 수 있다고 생각해서',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => '{{SITENAME}}에 기여하고 싶어서',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => '내 의견을 공유하고 싶어서',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => '오늘 평가를 하지는 않았지만 이 기능에 대해 피드백을 남기고 싶어서',
	'articlefeedbackv5-survey-answer-whyrated-other' => '기타',
	'articlefeedbackv5-survey-question-useful' => '당신은 평가한 것이 유용하고 명확할 것이라 생각하십니까?',
	'articlefeedbackv5-survey-question-useful-iffalse' => '왜 그렇게 생각하십니까?',
	'articlefeedbackv5-survey-question-comments' => '다른 의견이 있으십니까?',
	'articlefeedbackv5-survey-submit' => '제출',
	'articlefeedbackv5-survey-title' => '몇 가지 질문에 답해 주시기 바랍니다.',
	'articlefeedbackv5-survey-thanks' => '설문에 응해 주셔서 감사합니다.',
	'articlefeedbackv5-error' => '오류가 발생했습니다. 나중에 다시 시도해주세요.',
	'articlefeedbackv5-form-switch-label' => '문서 평가하기',
	'articlefeedbackv5-form-panel-title' => '문서 평가하기',
	'articlefeedbackv5-form-panel-explanation' => '어떤 기능인가요?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:문서 평가',
	'articlefeedbackv5-form-panel-clear' => '평가 제거하기',
	'articlefeedbackv5-form-panel-expertise' => '이 문서에 대해 전문적인 지식이 있습니다(선택사항)',
	'articlefeedbackv5-form-panel-expertise-studies' => '관련 대학 학위를 가지고 있습니다',
	'articlefeedbackv5-form-panel-expertise-profession' => '직업과 관련되어 있습니다',
	'articlefeedbackv5-form-panel-expertise-hobby' => '개인적으로 큰 관심이 있습니다',
	'articlefeedbackv5-form-panel-expertise-other' => '기타',
	'articlefeedbackv5-form-panel-helpimprove' => '위키백과 개선을 위한 이메일을 받습니다(선택사항)',
	'articlefeedbackv5-form-panel-helpimprove-note' => '확인 메일을 보냈습니다. 이 메일 주소는 어디에도 공개되지 않습니다. $1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => '개인정보 정책',
	'articlefeedbackv5-form-panel-submit' => '평가 제출',
	'articlefeedbackv5-form-panel-pending' => '평가를 제출하지 않았습니다',
	'articlefeedbackv5-form-panel-success' => '저장 완료',
	'articlefeedbackv5-form-panel-expiry-title' => '평가 유효 기간이 지났습니다',
	'articlefeedbackv5-form-panel-expiry-message' => '문서를 다시 평가한 다음 제출해주세요.',
	'articlefeedbackv5-report-switch-label' => '문서 평가 보기',
	'articlefeedbackv5-report-panel-title' => '문서 평가',
	'articlefeedbackv5-report-panel-description' => '평가 평균값입니다.',
	'articlefeedbackv5-report-empty' => '평가 없음',
	'articlefeedbackv5-report-ratings' => '평가 $1개',
	'articlefeedbackv5-field-trustworthy-label' => '신뢰성',
	'articlefeedbackv5-field-trustworthy-tip' => '이 문서를 뒷받침하는 충분한 출처가 있고, 그 출처가 믿을 수 있다고 생각하시나요?',
	'articlefeedbackv5-field-complete-label' => '완전성',
	'articlefeedbackv5-field-complete-tip' => '이 문서에서 다루어야 하는 내용을 충분히 담고 있다고 생각하시나요?',
	'articlefeedbackv5-field-objective-label' => '객관성',
	'articlefeedbackv5-field-objective-tip' => '이 문서는 여러 관점을 공정하게 다루고 있다고 생각하시나요?',
	'articlefeedbackv5-field-wellwritten-label' => '가독성',
	'articlefeedbackv5-field-wellwritten-tip' => '이 문서가 짜임새있게 잘 구성되어있다고 생각하시나요?',
	'articlefeedbackv5-pitch-reject' => '나중에 평가하기',
	'articlefeedbackv5-pitch-or' => '또는',
	'articlefeedbackv5-pitch-thanks' => '감사합니다! 평가가 저장되었습니다.',
	'articlefeedbackv5-pitch-survey-message' => '간단한 설문조사에 참여해주세요.',
	'articlefeedbackv5-pitch-survey-accept' => '설문조사 시작',
	'articlefeedbackv5-pitch-join-message' => '계정을 만들고 싶으신가요?',
	'articlefeedbackv5-pitch-join-body' => '계정을 만들면 편집 내역을 확인하고 토론에 참여하거나, 커뮤니티의 일원으로 활동하기 쉬워집니다.',
	'articlefeedbackv5-pitch-join-accept' => '계정 만들기',
	'articlefeedbackv5-pitch-join-login' => '로그인',
	'articlefeedbackv5-pitch-edit-message' => '이 문서를 직접 편집할 수 있다는 사실을 알고 계셨나요?',
	'articlefeedbackv5-pitch-edit-accept' => '이 문서 편집하기',
	'articlefeedbackv5-survey-message-success' => '설문을 작성해 주셔서 감사합니다.',
	'articlefeedbackv5-survey-message-error' => '오류가 발생했습니다.
잠시 후 다시 시도해주세요.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => '오늘의 최고값과 최저값',
	'articleFeedbackv5-table-caption-dailyhighs' => '가장 높은 평가를 받은 문서: $1',
	'articleFeedbackv5-table-caption-dailylows' => '가장 낮은 평가를 받은 문서: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => '이번 주에 가장 많이 바뀐 문서',
	'articleFeedbackv5-table-caption-recentlows' => '최근의 평점 낮은 문서',
	'articleFeedbackv5-table-heading-page' => '문서',
	'articleFeedbackv5-table-heading-average' => '평균',
	'articleFeedbackv5-copy-above-highlow-tables' => '실험적인 기능입니다. 기능에 대한 의견을 [$1 토론란]에 남겨 주세요.',
	'articlefeedbackv5-disable-preference' => '문서에 평가 도구 표시하지 않기',
);

/** Colognian (Ripoarisch)
 * @author Purodha
 */
$messages['ksh'] = array(
	'articlefeedbackv5' => 'Enschäzonge för Sigge — Övverbleck',
	'articlefeedbackv5-desc' => 'Enschäzonge för Sigge',
	'articlefeedbackv5-survey-question-origin' => 'Op wat för en Sigg bes De jewääse, wi De aanjefange häs, op heh di Froore ze antwoote?',
	'articlefeedbackv5-survey-question-whyrated' => 'Bes esu joot, un lohß ons weße, woröm De hück för heh di Sigg en Enschäzong affjejovve häs, un maach e Krüzje övverall, woh_t paß:',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Esch wullt jät beidraare zo all dä Enschäzonge för heh di Sigg',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Esch hoffen, dat ming Enschäzong för di Sigg dozoh beidrääht, dat se bäßer jemaat weed',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Esch wullt jät {{GRAMMAR:zo Dativ|{{SITENAME}}}} beidraare',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Esch jävven jäähn ming Meinong of',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Esch han hück kein Enschäzong afjejovve, wullt ävver en Röckmäldong övver et Enschäze vun Sigge afjävve',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Söns jet',
	'articlefeedbackv5-survey-question-useful' => 'Meins De, dat di Enschäzonge, di_et bes jäz jit, ze bruche sin un kloh?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Woröm?',
	'articlefeedbackv5-survey-question-comments' => 'Häs De sönß noch jet ze saare?',
	'articlefeedbackv5-survey-submit' => 'Faßhallde',
	'articlefeedbackv5-survey-title' => 'Bes esu joot, un jivv e paa Antowwote',
	'articlefeedbackv5-survey-thanks' => 'Mer donn und bedanke för et Ußfölle!',
	'articlefeedbackv5-error' => 'Ene Fähler es dozwesche jukumme.
Versöhg et shpääder norr_ens.',
	'articlefeedbackv5-form-switch-label' => 'Heh di Sigg enschäze',
	'articlefeedbackv5-form-panel-title' => 'Heh di Sigg enschäze',
	'articlefeedbackv5-form-panel-explanation' => 'Wat sul dat heh bedügge?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:{{int:articlefeedbackv5-desc}}',
	'articlefeedbackv5-form-panel-clear' => 'Enschäzong fott nämme',
	'articlefeedbackv5-form-panel-expertise' => 'Esch han en joode un vill Ahnong vun däm Theema',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Esch han dat aan ene Huhscholl udder aan der Univäsitäät shtudeet, un han ene Afschloß jemaat',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Et jehöt bei minge Beroof',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Esch han e deef Inträße aan dä Saach',
	'articlefeedbackv5-form-panel-expertise-other' => 'Söns jät, wat heh nit opjeföhrd es',
	'articlefeedbackv5-form-panel-helpimprove' => 'Esch däät jähn methällfe, {{GRAMMAR:Nominativ|{{SITENAME}}}} bäßer ze maache. Doht mer en <i lang="en">e-mail</i> schecke.',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Mr schecke Der en <i lang="en">e-mail</i> zum Beschtäteje.
Mer jävve Ding Adräß för de <i lang="en">e-mail</i> aan Keine wigger.
$1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Rääjelle för der Daateschotz un de Jeheimhaldung',
	'articlefeedbackv5-form-panel-submit' => 'Lohß jonn!',
	'articlefeedbackv5-form-panel-pending' => 'Din Enschäzonge sin noch nicht huhjelaade',
	'articlefeedbackv5-form-panel-success' => 'Afjeshpeishert.',
	'articlefeedbackv5-form-panel-expiry-title' => 'Ding Enschäzonge sen enzwesche övverhollt',
	'articlefeedbackv5-form-panel-expiry-message' => 'Donn di Sigg heh neu Enschaäze, bes esu joot,',
	'articlefeedbackv5-report-switch-label' => 'Enschäzunge vun heh dä Sigg beloore',
	'articlefeedbackv5-report-panel-title' => 'Enschäzunge vun heh dä Sigg',
	'articlefeedbackv5-report-panel-description' => 'De dorschnettlesche Enschäzunge.',
	'articlefeedbackv5-report-empty' => 'Kein Enschäzunge',
	'articlefeedbackv5-report-ratings' => '{{PLURAL:$1|Ein Enschäzung|$1 Enschäzunge|Kein Enschäzung}}',
	'articlefeedbackv5-field-trustworthy-label' => 'Verdent Vertroue',
	'articlefeedbackv5-field-trustworthy-tip' => 'Meins De, dat heh di Sigg jenooch Quälle aanjitt, un dat mer dänne jläuve kann?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Kein verläßlesche Quelle aanjejovve',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Nit vill verläßlesche Quelle aanjejovve',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Jraad jenoch verläßlesche Quelle aanjejovve',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Joode un verläßlesche Quelle aanjejovve',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Fantastesche un verläßlesche Quelle aanjejovve',
	'articlefeedbackv5-field-complete-label' => 'Kumplätt',
	'articlefeedbackv5-field-complete-tip' => 'Meins De, dat heh di Sigg all dat enthallde deiht, wat weeshtesh un nüüdesch is, dat nix draan fählt?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Et fählt et mießte, wat mer lässe well',
	'articlefeedbackv5-field-complete-tooltip-2' => 'E beßje es doh, wat mer lässe well',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Et Weschteschßte es doh, wat mer lässe well, ävver et fählt och öhnlesch jät',
	'articlefeedbackv5-field-complete-tooltip-4' => 'Vun de Houpsaache es et miehßte doh',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Üßföhrlesch',
	'articlefeedbackv5-field-objective-label' => 'Opjäktiev',
	'articlefeedbackv5-field-objective-tip' => 'Meins De, dat heh di Sigg ob en aanschtändije un ußjewoore Aat all de Aanseshte un Bedraachtungswiese vun der iehrem Teema widderjitt?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Es övverhoup nit opjäktiev',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Es nit besönders opjäktiev',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Es nit esu janz opjäktiev, ävver et jeiht esu',
	'articlefeedbackv5-field-objective-tooltip-4' => 'Süüd opjäktiev uß',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Es janz opjäktiev',
	'articlefeedbackv5-field-wellwritten-label' => 'Joot jeschrevve',
	'articlefeedbackv5-field-wellwritten-tip' => 'Fengks De heh di Sigg joot zosamme_jeschtalld un joot jeschrevve?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Verschteiht mer nit',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Schwer ze verschtonn',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Verschtändlesch jenooch',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Joot ze verschtonn',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Ußerjewöhlesch joot ze verschtonn',
	'articlefeedbackv5-pitch-reject' => 'Shpääder velleish',
	'articlefeedbackv5-pitch-or' => 'udder',
	'articlefeedbackv5-pitch-thanks' => 'Mer donn uns bedangke. Ding Enschäzonge sin faßjehallde.',
	'articlefeedbackv5-pitch-survey-message' => 'Nämm Der koot Zigg för en Ömfrooch.',
	'articlefeedbackv5-pitch-survey-accept' => 'Met dä Ömfrooch aanfange',
	'articlefeedbackv5-pitch-join-message' => 'Wells De Desch aanmällde?',
	'articlefeedbackv5-pitch-join-body' => 'Med en Aanmälldong kanns De leisch Ding eije Beidrääsch verfollje, beim Klaafe metmaache un e Deil vun der Jemeinschaff sin.',
	'articlefeedbackv5-pitch-join-accept' => 'Aaanmälde',
	'articlefeedbackv5-pitch-join-login' => 'Enlogge',
	'articlefeedbackv5-pitch-edit-message' => 'Häß De jewoß, dat De heh di Sigg ändere kanns?',
	'articlefeedbackv5-pitch-edit-accept' => 'Donn heh di Sigg ändere',
	'articlefeedbackv5-survey-message-success' => 'Merci för et Ußfölle!',
	'articlefeedbackv5-survey-message-error' => 'Ene Fähler es dozwesche jukumme.
Versöhg et shpääder norr_enß.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Hühje un Deefe vun hück',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Sigge met de beste Enschäzonge: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Sigge met de schläächteste Enschäzonge: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Diß Woch et miehtß jeändert',
	'articleFeedbackv5-table-caption-recentlows' => 'Köözlejje Deefe',
	'articleFeedbackv5-table-heading-page' => 'Sigg',
	'articleFeedbackv5-table-heading-average' => 'Dorschnett',
	'articleFeedbackv5-copy-above-highlow-tables' => 'Mer sin dat heeh aam upropeere.
Doht uns op di [$1 Klaafsigg] schrieve, wad Er dovun hallde doht.',
	'articlefeedbackv5-dashboard-bottom' => "'''Opjepaß''': Mer donn ongerscheidlijje Aate ußprobeere, Atikelle heh en dä Övverseeschte ze zeije. Em Momang sin dobei:
* Sigge met de hühßte un de deefste Enschäzonge - die mieh wie zehn Mohl en de verjangene 24 Schtonde enjeschäz woode sen. Der Dorschnett weed us alle Enschäzonge us dä 24 Schtonde ußjerääschnet.
* Sigge met de deefste Enschäzonge köözlesch - die mieh wie 70% Mohl en de verjangene 24 Schtonde schlääsch enjeschäz woode sen, met 2 Schtähnscher udder winnijer. Bloß Atikelle met zehn Enschäzonge us dä 24 Schtonde sen met dobei.",
	'articlefeedbackv5-disable-preference' => 'Donn dä Knopp zum Sigge Enschäze nit op de Sigge aanzeije',
	'articlefeedbackv5-emailcapture-response-body' => 'Ene schönne Daach,

mer bedangke uns för Ding Enträße, {{GRAMMAR:Akkusativ|{{SITENAME}}}} bäßer ze maache.

Nemm Der ene Momang, öm Ding e-mail Adräß ze beschtääteje, un donn däm Lingk heh follje:

$1

Do kanns och op heh di Sigg jonn:

$2

un dann dä Kood heh enjävve:

$3

Mer mälde ons bahl bei Der, wi de met {{GRAMMAR:Dativ|{{SITENAME}}}} hälfe kanns.

Wann De dat heh sällver nit aanjschtüße häs, donn nix, un mer don Der och nix mieh schecke.

Ene schööne Jrohß!

De Jemeinschaff vun {{GRAMMAR:Nominativ|{{SITENAME}}}}',
);

/** Kurdish (Latin) (Kurdî (Latin))
 * @author George Animal
 */
$messages['ku-latn'] = array(
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Çima?',
	'articlefeedbackv5-report-switch-label' => 'Encaman nîşan bide',
	'articleFeedbackv5-table-heading-page' => 'Rûpel',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Catrope
 * @author Robby
 */
$messages['lb'] = array(
	'articlefeedbackv5' => 'Iwwerbléck-Säit Artikelbewäertung',
	'articlefeedbackv5-desc' => 'Artikelaschätzung Pilotversioun',
	'articlefeedbackv5-survey-question-origin' => 'Op wat fir enger Säit war Dir wéi Dir mat der Ëmfro ugefaang huet?',
	'articlefeedbackv5-survey-question-whyrated' => 'Sot eis w.e.g. firwat datt Dir dës säit bewäert hutt (klickt alles u wat zoutrëfft):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Ech wollt zur allgemenger Bewäertung vun der Säit bedroen',
	'articlefeedbackv5-survey-answer-whyrated-development' => "Ech hoffen datt meng Bewäertung d'Entwécklung vun der Säit positiv beaflosst",
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Ech wollt mech un {{SITENAME}} bedeelegen',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Ech deele meng Meenung gäre mat',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Ech hunn haut keng Bewäertung ofginn, awer ech wollt mäi Feedback zu dëser Fonctionalitéit ginn',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Anerer',
	'articlefeedbackv5-survey-question-useful' => "Mengt Dir datt d'Bewäertungen hei nëtzlech a kloer sinn?",
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Firwat?',
	'articlefeedbackv5-survey-question-comments' => 'Hutt Dir nach aner Bemierkungen?',
	'articlefeedbackv5-survey-submit' => 'Späicheren',
	'articlefeedbackv5-survey-title' => 'Beäntwert w.e.g. e puer Froen',
	'articlefeedbackv5-survey-thanks' => 'Merci datt Dir eis Ëmfro ausgefëllt hutt.',
	'articlefeedbackv5-survey-disclaimer' => 'Wann Dir Äre Feedback gitt sidd Dir mat der Transparenz esou wéi se op $1 gewise gëtt averstan.',
	'articlefeedbackv5-error' => 'Et ass e Feeler geschitt. Probéiert w.e.g. méi spéit nach emol.',
	'articlefeedbackv5-form-switch-label' => 'Dës Säit bewäerten',
	'articlefeedbackv5-form-panel-title' => 'Dës Säit bewäerten',
	'articlefeedbackv5-form-panel-explanation' => 'Wat ass dat?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:Artikel-Feedback',
	'articlefeedbackv5-form-panel-clear' => 'Dës Bewäertung ewechhuelen',
	'articlefeedbackv5-form-panel-expertise' => 'Ech weess gutt iwwer dëse Sujet Bescheed (fakultativ)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Ech hunn een Ofschloss an der Schoul/op der Universitéit zu deem Sujet',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Et ass en Deel vu mengem Beruff',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Ech si passionéiert vun deem Sujet',
	'articlefeedbackv5-form-panel-expertise-other' => "D'Quell vu mengem Wëssen ass hei net opgezielt",
	'articlefeedbackv5-form-panel-helpimprove' => 'Ech wëll hëllefe fir {{SITENAME}} ze verbesseren, schéckt mir eng E-Mail (fakultativ)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Mir schécken Iech eng Confirmatiouns-Mail. Mir ginn Är E-Mailadress u kee weider esou wéi an eise(n) $1 virgesinn ass.',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Dateschutz vum Feedback',
	'articlefeedbackv5-form-panel-submit' => 'Bewäertunge schécken',
	'articlefeedbackv5-form-panel-pending' => 'Äre Bewäertunge goufen nach net ageschéckt',
	'articlefeedbackv5-form-panel-success' => 'Gespäichert',
	'articlefeedbackv5-form-panel-expiry-title' => 'Är Bewäertung ass ofgelaf',
	'articlefeedbackv5-form-panel-expiry-message' => 'Bewäert dëse Säit w.e.g. nach emol a späichert déi nei Bewäertung.',
	'articlefeedbackv5-report-switch-label' => 'Bewäertunge vun der Säit weisen',
	'articlefeedbackv5-report-panel-title' => 'Bewäertunge vun der Säit',
	'articlefeedbackv5-report-panel-description' => 'Aktuell duerchschnëttlech Bewäertung.',
	'articlefeedbackv5-report-empty' => 'Keng Bewäertungen',
	'articlefeedbackv5-report-ratings' => '$1 Bewäertungen',
	'articlefeedbackv5-field-trustworthy-label' => 'Vertrauenswürdeg',
	'articlefeedbackv5-field-trustworthy-tip' => 'Hutt Dir den Androck datt dës Säit genuch Zitater huet an datt dës Zitater aus vertrauenswierdege Quelle kommen?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Seriéis Quelle feelen',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Wéineg seriéis Quellen',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Adequat seriéis Quellen',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Genuch seriéis Quellen',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Vill seriéis Quellen',
	'articlefeedbackv5-field-complete-label' => 'Komplett',
	'articlefeedbackv5-field-complete-tip' => 'Hutt dir den Androck datt dës Säit déi wesentlech Aspekter vun dësem Sujet behandelt déi solle beliicht ginn?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Kaum Informatiounen',
	'articlefeedbackv5-field-complete-tooltip-2' => 'E puer Informatiounen',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Wichteg Informatiounen awer net komplett',
	'articlefeedbackv5-field-complete-tooltip-4' => 'All wichteg Informatioune stinn dran',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Komplett Informatiounen',
	'articlefeedbackv5-field-objective-label' => 'Net virageholl',
	'articlefeedbackv5-field-objective-tip' => 'Hutt Dir den Androck datt dës Säit eng ausgeglache Presentatioun vun alle Perspektive vun dësem Thema weist?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Staark eesäiteg',
	'articlefeedbackv5-field-objective-tooltip-2' => 'E bëssen eesäiteg',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Eng Grëtz eesäiteg',
	'articlefeedbackv5-field-objective-tooltip-4' => 'Net offensichtlech eesäiteg',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Guer net eesäiteg',
	'articlefeedbackv5-field-wellwritten-label' => 'Gutt geschriwwen',
	'articlefeedbackv5-field-wellwritten-tip' => 'Hutt Dir den Androck datt dës Säit gutt strukturéiert a gutt geschriwwen ass?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Onverständlech',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Schwéier ze verstoen',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Kloer',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Ganz kloer',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Aussergewéinlech kloer',
	'articlefeedbackv5-pitch-reject' => 'Vläicht méi spéit',
	'articlefeedbackv5-pitch-or' => 'oder',
	'articlefeedbackv5-pitch-thanks' => 'Merci! Är Bewäertung gouf gespäichert.',
	'articlefeedbackv5-pitch-survey-message' => 'Huelt Iech w.e.g. een Ament fir eng kuerz Ëmfro auszefëllen.',
	'articlefeedbackv5-pitch-survey-accept' => 'Ëmfro ufänken',
	'articlefeedbackv5-pitch-join-message' => 'Wollt Dir e Benotzerkont opmaachen?',
	'articlefeedbackv5-pitch-join-body' => 'E Benotzerkont hëlleft Iech Är Ännerungen am Aen ze behalen, Iech méi einfach un Diskussiounen ze bedeelegen an en Deel vun der Gemeinschaft ze sinn.',
	'articlefeedbackv5-pitch-join-accept' => 'Benotzerkont opmaachen',
	'articlefeedbackv5-pitch-join-login' => 'Aloggen',
	'articlefeedbackv5-pitch-edit-message' => 'Wosst Dir datt Dir dës Säit ännere kënnt?',
	'articlefeedbackv5-pitch-edit-accept' => 'Dës Säit änneren',
	'articlefeedbackv5-survey-message-success' => "Merci datt Dir d'Ëmfro ausgefëllt hutt.",
	'articlefeedbackv5-survey-message-error' => 'Et ass e Feeler geschitt.
Probéiert w.e.g. méi spéit nach emol.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => "D'Héichten an d'Déifte vun haut",
	'articleFeedbackv5-table-caption-dailyhighs' => 'Säite mat den héchste Bewäertungen: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Säite mat den niddregste Bewäertungen: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Déi gréisst Ännerungen an dëser Woch',
	'articleFeedbackv5-table-caption-recentlows' => 'Rezent schlecht Bewäertungen',
	'articleFeedbackv5-table-heading-page' => 'Säit',
	'articleFeedbackv5-table-heading-average' => 'Duerchschnëtt',
	'articleFeedbackv5-copy-above-highlow-tables' => 'Dëst ass eng experimentell Fonctioun. Gitt eis w.e.g. Äre Feedback op der [$1 Diskussiouns-Säit].',
	'articlefeedbackv5-dashboard-bottom' => "'''Informatioun:''' Mir probéiere weider ënnerschiddlech Méiglechkeeten aus fir Artikelen op dësen Arbechts- an Iwwersichtsäiten ze weisen. Momentan ginn hei dës Artikele gewisen:
* Säite mat de beschten / schlechtste Bewäertungen: Artikel déi mindestens zéng Bewäertungen während de leschte 24 Stonne kritt hunn. D'Durchschnëttswäerter sinn dobäi de Mëttelwäert vun alle Bewäertunge vun de leschte 24 Stonnen.
* Aktuell schlechte Bewäertungen: Artikel déi während de leschte 24 Stonne 70 % oder méi schlecht Bewäertungen (zwee Stären oder manner) an enger Kategorien kritt hunn. Nëmmen Artikel mat mindestens zéng Bewäertunge während de leschte 24 Stonne ginn dobäi berücksichtegt",
	'articlefeedbackv5-disable-preference' => 'De Widget vun der Artikelbewäertung net op de Säite weisen',
	'articlefeedbackv5-emailcapture-response-body' => 'Bonjour! 

Merci fir Ären Interessie fir {{SITENAME}} ze verbesseren.

Huelt Iech w.e.g. een Ament Zäit fir Är Mailadress ze confirméieren, andeem Dir op dëse Link klickt:

$1

Dir kënnt och dës Säit besichen:

$2

Gitt do dëse Confirmatiouns-Code an:

$3

Mir mellen eis geschwënn, fir Iech ze soe, wéi Dir hëllefe kënnt fir {{SITENAME}} ze verbesseren.

Wann Dir dës Ufro net ausgeléist hutt, ignoréiert dës Mail einfach. Mir schécken Iech dann och näischt méi.

E schéine Bonjour a villmools Merci,
Är {{SITENAME}}-Equipe',
);

/** Limburgish (Limburgs)
 * @author Ooswesthoesbes
 */
$messages['li'] = array(
	'articlefeedbackv5' => 'Paginabeoordeiling',
	'articlefeedbackv5-desc' => 'Paginabeoordeiling (tesversie)',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Anges',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Wróm?',
);

/** Lithuanian (Lietuvių)
 * @author Eitvys200
 * @author Ignas693
 * @author Perkunas
 */
$messages['lt'] = array(
	'articlefeedbackv5' => 'Straipsnis atsiliepimus Panel',
	'articlefeedbackv5-desc' => 'Straipsnio atsiliepimai',
	'articlefeedbackv5-survey-question-origin' => 'Kokiame puslapyje jus buvote kai pradėjote šia apklausą?',
	'articlefeedbackv5-survey-question-whyrated' => 'Prašome pranešti mums, kodėl jus įvertino šį puslapį šiandien (pažymėkite visus tinkamus):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Aš norėjau prisidėti prie puslapio bendras vertinimas',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Tikiuosi, kad mano įvertinimas duos teigiamos įtakos puslapiui',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Aš norėjau prisidėti prie {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Man patinka dalintis savo nuomonę',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Šiandien nepateikė reitingai, bet norėjo duoti atsiliepimus apie funkciją',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Kita',
	'articlefeedbackv5-survey-question-useful' => 'Ar manote, kad reitingai yra naudingi ir aiškus?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Kodėl?',
	'articlefeedbackv5-survey-question-comments' => 'Ar turite papildomų komentarų?',
	'articlefeedbackv5-survey-submit' => 'Siųsti',
	'articlefeedbackv5-survey-title' => 'Prašome atsakyti į kelis klausimus',
	'articlefeedbackv5-survey-thanks' => 'Dėkojame, kad užpildėte apklausa.',
	'articlefeedbackv5-survey-disclaimer' => 'Padedant gerinant šia galimybę jūsų atsiliepimai gali būti anonimiškai pasidalinti su Vikipedija.',
	'articlefeedbackv5-error' => 'Įvyko klaida. Bandykite dar kartą vėliau.',
	'articlefeedbackv5-form-switch-label' => 'Įvertinti šį puslapį',
	'articlefeedbackv5-form-panel-title' => 'Įvertinti šį puslapį',
	'articlefeedbackv5-form-panel-explanation' => 'Kas tai?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:ArticleFeedback',
	'articlefeedbackv5-form-panel-clear' => 'Pašalinti šį įvertinimą',
	'articlefeedbackv5-form-panel-expertise' => 'Aš labai gerai nusimanau apie šią temą (neprivaloma)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Turiu atitinkamą kolegijos / universiteto diplomą',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Tai dalis mano profesijos',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Tai yra asmeninė aistra',
	'articlefeedbackv5-form-panel-expertise-other' => 'Mano žinių šaltinio čia nėra',
	'articlefeedbackv5-form-panel-helpimprove' => 'Norėčiau padėti pagerinti Vikipediją, siųskite man e-mail (neprivaloma)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Mes jums atsiųsime patvirtinimą elektroniniu paštu. Mes nesidaliname Jūsų adresu su bet kuo. $1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Privatumo politika',
	'articlefeedbackv5-form-panel-submit' => 'Pateikti įvertinimus',
	'articlefeedbackv5-form-panel-pending' => 'Jūsų įvertinimai nebuvo pateikti',
	'articlefeedbackv5-form-panel-success' => 'Išsaugota sėkmingai',
	'articlefeedbackv5-form-panel-expiry-title' => 'Jūsų įvertinimai baigėsi',
	'articlefeedbackv5-form-panel-expiry-message' => 'Prašome reevaluate šiame puslapyje ir pateikti naują reitingai.',
	'articlefeedbackv5-report-switch-label' => 'Peržiūrėti puslapio reitinus',
	'articlefeedbackv5-report-panel-title' => 'Puslapio reitingai',
	'articlefeedbackv5-report-panel-description' => 'Dabartinis vidutinis reitingai.',
	'articlefeedbackv5-report-empty' => 'Nėra vertinimų',
	'articlefeedbackv5-report-ratings' => '$1 vertinimas',
	'articlefeedbackv5-field-trustworthy-label' => 'Patikimas',
	'articlefeedbackv5-field-trustworthy-tip' => 'Jūs manote, šiame puslapyje yra pakankamai citatos ir šių citatų yra iš patikimų šaltinių?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Trūksta patikimų šaltinių',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Trūksta patikimų šaltinių',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Pakankamai patikimi šaltiniai',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Trūksta patikimų šaltinių',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Pakankamai patikimi šaltiniai',
	'articlefeedbackv5-field-complete-label' => 'Užbaigti',
	'articlefeedbackv5-field-complete-tip' => 'Ar manote, kad šis puslapis apima esminius temas, kad ji turėtų?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Trūksta daugumos informacijos',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Yra informacijos',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Yra svarbiausia informacija, tačiau su spragas',
	'articlefeedbackv5-field-complete-tooltip-4' => 'Yra informacijos',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Visapusiška',
	'articlefeedbackv5-field-objective-label' => 'Tikslas',
	'articlefeedbackv5-field-objective-tip' => 'Ar manote, kad šis puslapis rodo tikrosios atstovavimo visų perspektyvų klausimu?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Labai neobjektyvūs',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Vidutinis šališkumo',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Minimalus poslinkis',
	'articlefeedbackv5-field-objective-tooltip-4' => 'Akivaizdus įstrižinės',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Visiškai nešališkas',
	'articlefeedbackv5-field-wellwritten-label' => 'Gerai parašyta',
	'articlefeedbackv5-field-wellwritten-tip' => 'Ar manote, kad šis puslapis yra gerai organizuotas ir gerai parašytas?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Nesuprantamas',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Sunku suprasti',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Tinkamą aiškumo',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Gera aiškumo',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Išimtiniais aiškumo',
	'articlefeedbackv5-pitch-reject' => 'Galbūt vėliau',
	'articlefeedbackv5-pitch-or' => 'arba',
	'articlefeedbackv5-pitch-thanks' => 'Ačiū! Jūsų įvertinimai buvo išsaugoti.',
	'articlefeedbackv5-pitch-survey-message' => 'Prašome skirkite truputi laiko kad užpildytumėte trumpą apklausą.',
	'articlefeedbackv5-pitch-survey-accept' => 'Pradėti apklausą',
	'articlefeedbackv5-pitch-join-message' => 'Ar norėjote sukurti paskyrą?',
	'articlefeedbackv5-pitch-join-body' => 'Sąskaitą padės jums stebėti savo redagavimo, įsitraukti į diskusijas, ir Bendrijos dalis.',
	'articlefeedbackv5-pitch-join-accept' => 'Sukurti paskyrą',
	'articlefeedbackv5-pitch-join-login' => 'Prisijungti',
	'articlefeedbackv5-pitch-edit-message' => 'Ar žinote, kad galite redaguoti šį puslapį?',
	'articlefeedbackv5-pitch-edit-accept' => 'Redaguoti šį puslapį',
	'articlefeedbackv5-survey-message-success' => 'Dėkojame, kad užpildėte apklausa.',
	'articlefeedbackv5-survey-message-error' => 'Įvyko klaida.
Pabandykite vėliau.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Šiandienos aukštų ir rekordinį lygį',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Straipsniai su aukščiausiais įvertinimais: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Straipsniai su žemiausiais įvertinimais: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Šią savaitę labiausiai pasikeitę',
	'articleFeedbackv5-table-caption-recentlows' => 'Neseniai rekordinį lygį',
	'articleFeedbackv5-table-heading-page' => 'Puslapis',
	'articleFeedbackv5-table-heading-average' => 'Vidurkis',
	'articleFeedbackv5-copy-above-highlow-tables' => 'Tai eksperimentinė funkcija. Prašome pateikti atsiliepimus [$1 discussion page].',
	'articlefeedbackv5-dashboard-bottom' => '"" Pastaba "": mes ir toliau eksperimentuoti su įvairiais būdais dangos straipsniuose, šių skelbimų lentos.  Šiuo metu informacijos skydus įtraukti šie straipsniai:
 * puslapių su didžiausią ir mažiausią reitingai: straipsniai, kurie yra gavę ne mažiau kaip 10 reitingai per paskutines 24 valandas.  Vidurkiai apskaičiuojami imant visų reitingai, per paskutines 24 valandas vidurkis.
 * neseniai rekordinį lygį: straipsniai, kad gavo 70 % arba daugiau žemas (2 žvaigždučių arba mažesnis) klases į bet kurią kategoriją, paskutines 24 valandas. Čia taip pat įtraukiami tik straipsniuose, gavo ne mažiau kaip 10 reitingai per paskutines 24 valandas.',
	'articlefeedbackv5-disable-preference' => 'Nerodyti straipsnio atsiliepimus valdikliui puslapiuose',
	'articlefeedbackv5-emailcapture-response-body' => 'labas!
N!Dėkojame už susidomėjimą padedant didinti {{SITENAME}}.
N!Prašome Skirkite laiko patvirtinti savo el. pašto spustelėję žemiau esančią nuorodą:
N!$1
N!Jūs taip pat gali aplankyti:
N!$2
N!Ir įveskite šiuos patvirtinimo kodas:
N!$3
N!Mes bus susisiekti netrukus su kaip jūs galite padėti pagerinti {{SITENAME}}.
N!Jei jūs nepradėjo šį prašymą, prašome ignoruoti šį el. laišką ir mes ne išsiųs jums nieko kito.
N!Geriausias pageidavimus, ir Dėkojame jums
{{SITENAME}} komanda',
);

/** Latvian (Latviešu)
 * @author GreenZeb
 * @author Papuass
 */
$messages['lv'] = array(
	'articlefeedbackv5' => 'Atsauksme par rakstu',
	'articlefeedbackv5-desc' => 'Atsauksme par rakstu',
	'articlefeedbackv5-survey-question-origin' => 'Kādas lapas Jūs apmeklējāt pirms sākāt šo aptauju?',
	'articlefeedbackv5-survey-question-whyrated' => 'Lūdzu pasakiet, kādēļ Jūs šodien novērtējāt šo lapu (atzīmējiet visas atbilstošās atbildes):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Es vēlējos dot ieguldījumu kopējā lapas vērtējumā',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Es cerēju, ka mans vērtējums pozitīvu ietekmēs lapas tālāku pilnveidošanu',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Es vēlējos dot ieguldījumu {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Man patīk dalīties ar viedokli',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Es šodien neiesniedzu vērtējumu, bet vēlējos dot atsauksmi',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Cits',
	'articlefeedbackv5-survey-question-useful' => 'Vai Jūs uzskatāt, ka iesniegtie vērtējumi ir lietderīgi un skaidri?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Kāpēc?',
	'articlefeedbackv5-survey-question-comments' => 'Vai tev ir kādi papildus komentāri?',
	'articlefeedbackv5-survey-submit' => 'Iesniegt',
	'articlefeedbackv5-survey-title' => 'Lūdzu, atbildi uz dažiem jautājumiem',
	'articlefeedbackv5-survey-thanks' => 'Paldies par piedalīšanos aptaujā.',
	'articlefeedbackv5-error' => 'Radusies kļūda. Lūdzu, mēģiniet vēlāk vēlreiz.',
	'articlefeedbackv5-form-switch-label' => 'Novērtējiet šo lapu',
	'articlefeedbackv5-form-panel-title' => 'Novērtējiet šo lapu',
	'articlefeedbackv5-form-panel-explanation' => 'Kas tas ir?',
	'articlefeedbackv5-form-panel-clear' => 'Noņemt šo vērtējumu',
	'articlefeedbackv5-form-panel-expertise' => 'Es esmu ļoti zinošs par šo tēmu (atzīmēt pēc izvēles)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Man ir attiecīgās jomas augstākās izglītības grāds',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Tā ir daļa no mana amata',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Tā ir dziļa personiska aizraušanās',
	'articlefeedbackv5-form-panel-expertise-other' => 'Manu zināšanu ieguves veids nav šajā sarakstā',
	'articlefeedbackv5-form-panel-helpimprove' => 'Es vēlētos palīdzēt uzlabot Vikipēdiju, sūtiet man e-pastu (atzīmēt pēc izvēles)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Mēs Jums nosūtīsim apstiprinājuma e-pastu. Mēs citām personām nedarīsim zināmu Jūsu adresi. $1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Privātuma politika',
	'articlefeedbackv5-form-panel-submit' => 'Iesniegt vērtējumus',
	'articlefeedbackv5-form-panel-pending' => 'Jūsu vērtējumi vēl nav iesniegti',
	'articlefeedbackv5-form-panel-success' => 'Veiksmīgi saglabāts',
	'articlefeedbackv5-form-panel-expiry-title' => 'Jūsu vērtējuma derīguma termiņš ir beidzies',
	'articlefeedbackv5-form-panel-expiry-message' => 'Lūdzu, pārskatiet šo lapu un iesniedziet jaunus vērtējumus.',
	'articlefeedbackv5-report-switch-label' => 'Skatīt lapas vērtējumus',
	'articlefeedbackv5-report-panel-title' => 'Lapas vērtējumi',
	'articlefeedbackv5-report-panel-description' => 'Pašreizējais vidējais vērtējums.',
	'articlefeedbackv5-report-empty' => 'Nav vērtējumu',
	'articlefeedbackv5-report-ratings' => '$1 vērtējumi',
	'articlefeedbackv5-field-trustworthy-label' => 'Uzticamība',
	'articlefeedbackv5-field-trustworthy-tip' => 'Vai Jums šķiet, ka lapai ir diezgan daudz citātu un ka šie citāti nāk no uzticamiem avotiem?',
	'articlefeedbackv5-field-complete-label' => 'Pabeigtība',
	'articlefeedbackv5-field-complete-tip' => 'Vai Jums šķiet, ka šī lapa apskata visas nepieciešamās temata jomas, ko būtu nepieciešams pieminēt?',
	'articlefeedbackv5-field-objective-label' => 'Objektivitāte',
	'articlefeedbackv5-field-objective-tip' => 'Vai Jums šķiet, ka šī lapa parāda pareizu satura attēlojumu no visiem šī jautājuma skatījumiem?',
	'articlefeedbackv5-field-wellwritten-label' => 'Informācijas izklāsts',
	'articlefeedbackv5-field-wellwritten-tip' => 'Vai Jums šķiet, ka šī lapa ir labi strukturēta un informatīva?',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Grūti saprast',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Atbilstoša skaidrība',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Laba skaidrība',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Izcila skaidrība',
	'articlefeedbackv5-pitch-reject' => 'Varbūt vēlāk',
	'articlefeedbackv5-pitch-or' => 'vai',
	'articlefeedbackv5-pitch-thanks' => 'Paldies! Jūsu vērtējumi ir saglabāti.',
	'articlefeedbackv5-pitch-survey-message' => 'Lūdzu, veltiet laiku, lai aizpildītu īsu aptauju.',
	'articlefeedbackv5-pitch-survey-accept' => 'Sākt aptauju',
	'articlefeedbackv5-pitch-join-message' => 'Vai vēlaties izveidot kontu?',
	'articlefeedbackv5-pitch-join-body' => 'Konts palīdzēs Jums pārskatīt savus labojumus, sekmīgāk piedalīties diskusijās un iekļauties kopienā.',
	'articlefeedbackv5-pitch-join-accept' => 'Izveidot kontu',
	'articlefeedbackv5-pitch-join-login' => 'Pieteikties',
	'articlefeedbackv5-pitch-edit-message' => 'Vai Jūs zināt, ka varat rediģēt šo lapu?',
	'articlefeedbackv5-pitch-edit-accept' => 'Izmainīt šo lapu',
	'articlefeedbackv5-survey-message-success' => 'Paldies, ka aizpildījās aptauju!',
	'articlefeedbackv5-survey-message-error' => 'Radusies kļūda.
Lūdzu, mēģiniet vēlāk vēlreiz.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Šodienas kāpumi un kritumi',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Lapas ar visaugstāko vērtējumu: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Lapas ar viszemāko vērtējumu: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Šajā nedēļā visvairāk mainītie',
	'articleFeedbackv5-table-caption-recentlows' => 'Pēdējie kritumi',
	'articleFeedbackv5-table-heading-page' => 'Lapa',
	'articleFeedbackv5-table-heading-average' => 'Vidēji',
);

/** Macedonian (Македонски)
 * @author Bjankuloski06
 */
$messages['mk'] = array(
	'articlefeedbackv5' => 'Табла за оценување на статија',
	'articlefeedbackv5-desc' => 'Пилотна верзија на Оценување на статија',
	'articlefeedbackv5-survey-question-origin' => 'На која страница бевте кога ја започнавте анкетава?',
	'articlefeedbackv5-survey-question-whyrated' => 'Кажете ни зошто ја оценивте страницава денес (штиклирајте ги сите релевантни одговори)',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Сакав да придонесам кон севкупната оцена на страницата',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Се надевам дека мојата оценка ќе влијае позитивно на развојот на страницата',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Сакав да придонесам кон {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Сакам да го искажувам моето мислење',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Не оценував денес, туку сакав да искажам мое мислење за функцијата',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Друго',
	'articlefeedbackv5-survey-question-useful' => 'Дали сметате дека дадените оценки се полезни и јасни?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Зошто?',
	'articlefeedbackv5-survey-question-comments' => 'Имате некои други забелешки?',
	'articlefeedbackv5-survey-submit' => 'Поднеси',
	'articlefeedbackv5-survey-title' => 'Ве молиме одговорете на неколку прашања',
	'articlefeedbackv5-survey-thanks' => 'Ви благодариме што ја пополнивте анкетата.',
	'articlefeedbackv5-survey-disclaimer' => 'Поднесувајќи го ова, се согласувате на транспарентноста што ја налагаат овие [http://wikimediafoundation.org/wiki/Feedback_privacy_statement?uselang=mk услови]',
	'articlefeedbackv5-error' => 'Се појави грешка. Обидете се повторно.',
	'articlefeedbackv5-form-switch-label' => 'Оценете ја страницава',
	'articlefeedbackv5-form-panel-title' => 'Оценете ја страницава',
	'articlefeedbackv5-form-panel-explanation' => 'Што е ова?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:ОценувањеНаСтатии',
	'articlefeedbackv5-form-panel-clear' => 'Отстрани ја оценкава',
	'articlefeedbackv5-form-panel-expertise' => 'Имам големи познавања на оваа тематика (незадолжително)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Имам релевантно више/факултетско образование',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Ова е во полето на мојата професија',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Ова е моја длабока лична пасија',
	'articlefeedbackv5-form-panel-expertise-other' => 'Изворот на моите сознанија не е наведен тука',
	'articlefeedbackv5-form-panel-helpimprove' => 'Сакам да помогнам во подобрувањето на Википедија - испратете ми е-пошта (незадолжително)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Ќе ви испратиме порака за потврда. Вашата адреса не ја даваме никому, согласно одредбите на нашите $1.',
	'articlefeedbackv5-form-panel-helpimprove-email-placeholder' => 'eposta@primer.org',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'заштита на личните податоци кога искажувате мислења',
	'articlefeedbackv5-form-panel-submit' => 'Поднеси оценки',
	'articlefeedbackv5-form-panel-pending' => 'Вашите оценки не се поднесени',
	'articlefeedbackv5-form-panel-success' => 'Успешно зачувано',
	'articlefeedbackv5-form-panel-expiry-title' => 'Вашите оценки истекоа',
	'articlefeedbackv5-form-panel-expiry-message' => 'Прегледајте ја страницава повторно и дајте ѝ нови оценки.',
	'articlefeedbackv5-report-switch-label' => 'Оценки за страницата',
	'articlefeedbackv5-report-panel-title' => 'Оценки за страницата',
	'articlefeedbackv5-report-panel-description' => 'Тековни просечи оценки.',
	'articlefeedbackv5-report-empty' => 'Нема оценки',
	'articlefeedbackv5-report-ratings' => '$1 оценки',
	'articlefeedbackv5-field-trustworthy-label' => 'Веродостојност',
	'articlefeedbackv5-field-trustworthy-tip' => 'Дали сметате дека страницава има доволно наводи и дека изворите се веродостојни?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Нема меродавни извори',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Малку меродавни извори',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Достатни меродавни извори',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Солидни меродавни извори',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Одлични меродавни извори',
	'articlefeedbackv5-field-complete-label' => 'Исцрпност',
	'articlefeedbackv5-field-complete-tip' => 'Дали сметате дека статијава ги обработува најважните основни теми што треба да се обработат?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Недостасуваат највеќето информации',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Содржи извесни информации',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Содржи клучни информации, но со празнини или пропусти',
	'articlefeedbackv5-field-complete-tooltip-4' => 'Го содржи поголемиот дел клучните информации',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Сеопфатна покриеност',
	'articlefeedbackv5-field-objective-label' => 'Непристрасност',
	'articlefeedbackv5-field-objective-tip' => 'Дали сметате дека статијава на праведен начин ги застапува сите гледишта по оваа проблематика?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Многу пристрасна',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Умерено пристрасна',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Минимално пристрасна',
	'articlefeedbackv5-field-objective-tooltip-4' => 'Нема воочлива пристрасност',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Сосема непристрасна',
	'articlefeedbackv5-field-wellwritten-label' => 'Добро напишана',
	'articlefeedbackv5-field-wellwritten-tip' => 'Дали сметате дека страницава е добро организирана и убаво напишана?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Неразбирлива',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Тешко се разбира',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Достатно јасна',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Мошне јасна',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Исклучително јасна',
	'articlefeedbackv5-pitch-reject' => 'Можеби подоцна',
	'articlefeedbackv5-pitch-or' => 'или',
	'articlefeedbackv5-pitch-thanks' => 'Ви благодариме! Вашите оценки се зачувани.',
	'articlefeedbackv5-pitch-survey-message' => 'Пополнете ја оваа кратка анкета.',
	'articlefeedbackv5-pitch-survey-accept' => 'Почни',
	'articlefeedbackv5-pitch-join-message' => 'Дали сакате да создадете сметка?',
	'articlefeedbackv5-pitch-join-body' => 'Ако имате сметка ќе можете да ги следите вашите уредувања, да се вклучувате во дискусии и да бидете дел од заедницата',
	'articlefeedbackv5-pitch-join-accept' => 'Направи сметка',
	'articlefeedbackv5-pitch-join-login' => 'Најавете се',
	'articlefeedbackv5-pitch-edit-message' => 'Дали знаете дека можете да ја уредите страницава?',
	'articlefeedbackv5-pitch-edit-accept' => 'Уреди ја страницава',
	'articlefeedbackv5-survey-message-success' => 'Ви благодариме што ја пополнивте анкетата.',
	'articlefeedbackv5-survey-message-error' => 'Се појави грешка.
Обидете се подоцна.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Издигања и падови за денес',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Статии со највисоки оценки: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Статии со најниски оценки: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Најизменети за неделава',
	'articleFeedbackv5-table-caption-recentlows' => 'Скорешни падови',
	'articleFeedbackv5-table-heading-page' => 'Страница',
	'articleFeedbackv5-table-heading-average' => 'Просечно',
	'articleFeedbackv5-copy-above-highlow-tables' => 'Ова е експериментална функција. Искажете го вашето мислење на [$1 страницатата за разговор].',
	'articlefeedbackv5-dashboard-bottom' => "'''Напомена''': Ќе продолжиме да експериментираме со различни начини на истакнување на статии во овие контролни табли.  Моментално таблите ги истакнуваат следниве статии:
* Страници со највисоки/најниски оценки: статии што добиле барем 10 оценки во текот на последните 24 часа.  Просекот се пресметува со наоѓање на средината на сите оценки дадени во последните 24 часа.
* Неодамна нискооценети: статии со 70% или повеќе ниски оценки (2 ѕвезди и помалку) во било која категорија во последните 24 часа. Се бројат само статии со барем 10 оценки добиени во последните 24 часа.",
	'articlefeedbackv5-disable-preference' => 'Не го прикажувај прилогот „Оценување на статии“ во страниците',
	'articlefeedbackv5-emailcapture-response-body' => 'Здраво!

Ви благодариме што изразивте интерес да помогнете во развојот на {{SITENAME}}.

Потврдете ја вашата е-пошта на следнава врска: 

$1

Можете да ја посетите и страницата:

$2

Внесете го следниов потврден кон:

$3

Набргу ќе ви пишеме како можете да помогнете во подобрувањето на {{SITENAME}}.

Ако го немате побарано ова, занемарате ја поракава, и ние повеќе ништо нема да ви испраќаме.

Ви благодариме и сè најдобро,
Екипата на {{SITENAME}}',
);

/** Malayalam (മലയാളം)
 * @author Praveenp
 */
$messages['ml'] = array(
	'articlefeedbackv5' => 'ലേഖനത്തിന്റെ മൂല്യനിർണ്ണയ നിയന്ത്രണോപാധികൾ',
	'articlefeedbackv5-desc' => 'ലേഖനത്തിന്റെ മൂല്യനിർണ്ണയം (പ്രാരംഭ പതിപ്പ്)',
	'articlefeedbackv5-survey-question-origin' => 'താങ്കൾ ഈ സർവേ ഉപയോഗിക്കാൻ തുടങ്ങിയപ്പോൾ ഏത് താളിലായിരുന്നു?',
	'articlefeedbackv5-survey-question-whyrated' => 'ഈ താളിന് താങ്കൾ ഇന്ന് നിലവാരമിട്ടതെന്തുകൊണ്ടാണെന്ന് ദയവായി പറയാമോ (ബാധകമാകുന്ന എല്ലാം തിരഞ്ഞെടുക്കുക):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'താളിന്റെ ആകെ നിലവാരം നിർണ്ണയിക്കാൻ ഞാനാഗ്രഹിക്കുന്നു',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'ഞാനിട്ട നിലവാരം താളിന്റെ വികസനത്തിൽ ക്രിയാത്മകമായ ഫലങ്ങൾ സൃഷ്ടിക്കുമെന്ന് കരുതുന്നു',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'ഞാൻ {{SITENAME}} സംരംഭത്തിൽ സംഭാവന ചെയ്യാൻ ആഗ്രഹിക്കുന്നു',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'എന്റെ അഭിപ്രായം പങ്ക് വെയ്ക്കുന്നതിൽ സന്തോഷമേയുള്ളു',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'ഞാനിന്ന് നിലവാരനിർണ്ണയം നടത്തിയിട്ടില്ല, പക്ഷേ ഈ സൗകര്യം സംബന്ധിച്ച അഭിപ്രായം അറിയിക്കാൻ ആഗ്രഹിക്കുന്നു',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'മറ്റുള്ളവ',
	'articlefeedbackv5-survey-question-useful' => 'നൽകിയിരിക്കുന്ന നിലവാരം ഉപകാരപ്രദവും വ്യക്തവുമാണെന്ന് താങ്കൾ കരുതുന്നുണ്ടോ?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'എന്തുകൊണ്ട്?',
	'articlefeedbackv5-survey-question-comments' => 'താങ്കൾക്ക് മറ്റെന്തെങ്കിലും അഭിപ്രായങ്ങൾ പങ്ക് വെയ്ക്കാനുണ്ടോ?',
	'articlefeedbackv5-survey-submit' => 'സമർപ്പിക്കുക',
	'articlefeedbackv5-survey-title' => 'ദയവായി ഏതാനം ചോദ്യങ്ങൾക്ക് ഉത്തരം നൽകുക',
	'articlefeedbackv5-survey-thanks' => 'സർവേ പൂരിപ്പിച്ചതിനു നന്ദി',
	'articlefeedbackv5-survey-disclaimer' => 'ഈ വിശേഷഗുണം മെച്ചപ്പെടുത്താനായി, താങ്കളുടെ അഭിപ്രായങ്ങൾ വിക്കിപീഡിയ സമൂഹവുമായി പേരു വെളിപ്പെടുത്താതെ പങ്കുവെയ്ക്കപ്പെട്ടേക്കാം.',
	'articlefeedbackv5-error' => 'എന്തോ പിഴവുണ്ടായിരിക്കുന്നു. ദയവായി പിന്നീട് വീണ്ടും ശ്രമിക്കുക.',
	'articlefeedbackv5-form-switch-label' => 'ഈ താളിനു നിലവാരമിടുക',
	'articlefeedbackv5-form-panel-title' => 'ഈ താളിനു നിലവാരമിടുക',
	'articlefeedbackv5-form-panel-explanation' => 'എന്താണിത്?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:ലേഖനാഭിപ്രായം',
	'articlefeedbackv5-form-panel-clear' => 'ഈ നിലവാരമിടൽ നീക്കം ചെയ്യുക',
	'articlefeedbackv5-form-panel-expertise' => 'എനിക്ക് ഈ വിഷയത്തിൽ വളരെ അറിവുണ്ട് (ഐച്ഛികം)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'എനിക്ക് ബന്ധപ്പെട്ട വിഷയത്തിൽ കലാലയ/യൂണിവേഴ്സിറ്റി ബിരുദമുണ്ട്',
	'articlefeedbackv5-form-panel-expertise-profession' => 'ഇതെന്റെ ജോലിയുടെ ഭാഗമാണ്',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'ഇതെനിക്ക് അഗാധ താത്പര്യമുള്ളവയിൽ പെടുന്നു',
	'articlefeedbackv5-form-panel-expertise-other' => 'എന്റെ അറിവിന്റെ ഉറവിടം ഇവിടെ നൽകിയിട്ടില്ല',
	'articlefeedbackv5-form-panel-helpimprove' => 'വിക്കിപീഡിയ മെച്ചപ്പെടുത്താൻ ഞാനാഗ്രഹിക്കുന്നു, ഇമെയിൽ അയച്ചു തരിക (ഐച്ഛികം)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'ഞങ്ങൾ താങ്കൾക്ക് ഒരു സ്ഥിരീകരണ ഇമെയിൽ അയയ്ക്കുന്നതാണ്. താങ്കളുടെ വിലാസം ആരുമായും പങ്കുവെയ്ക്കുന്നതല്ല. $1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'സ്വകാര്യതാനയം',
	'articlefeedbackv5-form-panel-submit' => 'നിലവാരമിടലുകൾ സമർപ്പിക്കുക',
	'articlefeedbackv5-form-panel-pending' => 'താങ്കളുടെ നിലവാരമിടലുകൾ സമർപ്പിക്കപ്പെട്ടിട്ടില്ല',
	'articlefeedbackv5-form-panel-success' => 'വിജയകരമായി സേവ് ചെയ്തിരിക്കുന്നു',
	'articlefeedbackv5-form-panel-expiry-title' => 'താങ്കളിട്ട നിലവാരങ്ങൾ കാലഹരണപ്പെട്ടിരിക്കുന്നു',
	'articlefeedbackv5-form-panel-expiry-message' => 'ദയവായി ഈ താൾ പുനർമൂല്യനിർണ്ണയം ചെയ്ത് പുതിയ നിലവാരമിടലുകൾ സമർപ്പിക്കുക.',
	'articlefeedbackv5-report-switch-label' => 'ഈ താളിനു ലഭിച്ച നിലവാരം കാണുക',
	'articlefeedbackv5-report-panel-title' => 'താളിന്റെ നിലവാരം',
	'articlefeedbackv5-report-panel-description' => 'ഇപ്പോഴത്തെ നിലവാരമിടലുകളുടെ ശരാശരി.',
	'articlefeedbackv5-report-empty' => 'നിലവാരമിടലുകൾ ഒന്നുമില്ല',
	'articlefeedbackv5-report-ratings' => '$1 നിലവാരമിടലുകൾ',
	'articlefeedbackv5-field-trustworthy-label' => 'വിശ്വാസയോഗ്യം',
	'articlefeedbackv5-field-trustworthy-tip' => 'ഈ താളിൽ വിശ്വസനീയങ്ങളായ സ്രോതസ്സുകളെ ആശ്രയിക്കുന്ന ആവശ്യമായത്ര അവലംബങ്ങൾ ഉണ്ടെന്ന് താങ്കൾ കരുതുന്നുണ്ടോ?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'ഗണനീയമായ സ്രോതസ്സുകളില്ല',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'ഗണനീയമായ കുറച്ച് സ്രോതസ്സുകൾ മാത്രം',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'ഗണനീയമായ സ്രോതസ്സുകൾ സാമാന്യമുണ്ട്',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'ഗണനീയമായ സ്രോതസ്സുകൾ നല്ലവണ്ണമുണ്ട്',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'ഗണനീയമായ സ്രോതസ്സുകൾ നിരവധി',
	'articlefeedbackv5-field-complete-label' => 'സമ്പൂർണ്ണം',
	'articlefeedbackv5-field-complete-tip' => 'ഈ താൾ അത് ഉൾക്കൊള്ളേണ്ട എല്ലാ മേഖലകളും ഉൾക്കൊള്ളുന്നതായി താങ്കൾ കരുതുന്നുണ്ടോ?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'ബഹുഭൂരിഭാഗം വിവരങ്ങളും ഇല്ല',
	'articlefeedbackv5-field-complete-tooltip-2' => 'കുറച്ചു വിവരങ്ങൾ മാത്രം',
	'articlefeedbackv5-field-complete-tooltip-3' => 'അടിസ്ഥാന വിവരങ്ങളുണ്ട്, പക്ഷേ തുടർച്ചയില്ല',
	'articlefeedbackv5-field-complete-tooltip-4' => 'ബഹുഭൂരിഭാഗം അടിസ്ഥാനവിവരങ്ങളും ഉണ്ട്',
	'articlefeedbackv5-field-complete-tooltip-5' => 'വിസ്തൃതമായ പരിധിയിലുൾപ്പെടുത്തിയിട്ടുണ്ട്.',
	'articlefeedbackv5-field-objective-label' => 'പക്ഷപാതരഹിതം',
	'articlefeedbackv5-field-objective-tip' => 'ഈ താളിൽ വിഷയത്തിന്റെ എല്ലാ വശത്തിനും അർഹമായ പ്രാതിനിധ്യം ലഭിച്ചതായി താങ്കൾ കരുതുന്നുണ്ടോ?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'അത്യധികം പക്ഷപാതമുണ്ട്',
	'articlefeedbackv5-field-objective-tooltip-2' => 'സാമാന്യം പക്ഷപാതമുണ്ട്',
	'articlefeedbackv5-field-objective-tooltip-3' => 'കുറച്ചു പക്ഷപാതമുണ്ട്',
	'articlefeedbackv5-field-objective-tooltip-4' => 'പക്ഷപാതം വ്യക്തമല്ല',
	'articlefeedbackv5-field-objective-tooltip-5' => 'പൂർണ്ണമായും പക്ഷപാതരഹിതം',
	'articlefeedbackv5-field-wellwritten-label' => 'നന്നായി രചിച്ചത്',
	'articlefeedbackv5-field-wellwritten-tip' => 'ഈ താൾ നന്നായി ക്രമീകരിക്കപ്പെട്ടതും നന്നായി എഴുതപ്പെട്ടതുമാണെന്ന് താങ്കൾ കരുതുന്നുണ്ടോ?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'തീർത്തും ദുർഗ്രഹം',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'മനസ്സിലാക്കാൻ ബുദ്ധിമുട്ടുള്ളത്',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'ആവശ്യത്തിനു വ്യക്തതയുണ്ട്',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'നല്ല വ്യക്തതയുണ്ട്',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'അസാമാന്യ വ്യക്തതയുണ്ട്',
	'articlefeedbackv5-pitch-reject' => 'പിന്നീട് ആലോചിക്കാം',
	'articlefeedbackv5-pitch-or' => 'അഥവാ',
	'articlefeedbackv5-pitch-thanks' => 'നന്ദി! താങ്കൾ നടത്തിയ മൂല്യനിർണ്ണയം സേവ് ചെയ്തിരിക്കുന്നു.',
	'articlefeedbackv5-pitch-survey-message' => 'ഈ ചെറിയ സർവ്വേ പൂർത്തിയാക്കാൻ ഒരു നിമിഷം ചിലവഴിക്കുക.',
	'articlefeedbackv5-pitch-survey-accept' => 'സർവ്വേ തുടങ്ങുക',
	'articlefeedbackv5-pitch-join-message' => 'താങ്കൾക്കും അംഗത്വം എടുക്കണമെന്നില്ലേ?',
	'articlefeedbackv5-pitch-join-body' => 'അംഗത്വമെടുക്കുന്നത് താങ്കളുടെ തിരുത്തലുകൾ പിന്തുടരാനും, ചർച്ചകളിൽ പങ്കാളിയാകാനും, സമൂഹത്തിന്റെ ഭാഗമാകാനും സഹായമാകും.',
	'articlefeedbackv5-pitch-join-accept' => 'അംഗത്വമെടുക്കുക',
	'articlefeedbackv5-pitch-join-login' => 'പ്രവേശിക്കുക',
	'articlefeedbackv5-pitch-edit-message' => 'ഈ താൾ തിരുത്താനാവും എന്ന് താങ്കൾക്കറിയാമോ?',
	'articlefeedbackv5-pitch-edit-accept' => 'ഈ താൾ തിരുത്തുക',
	'articlefeedbackv5-survey-message-success' => 'സർവേ പൂരിപ്പിച്ചതിനു നന്ദി',
	'articlefeedbackv5-survey-message-error' => 'എന്തോ പിഴവുണ്ടായിരിക്കുന്നു.
ദയവായി വീണ്ടും ശ്രമിക്കുക.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'ഇന്നത്തെ കയറ്റിറക്കങ്ങൾ',
	'articleFeedbackv5-table-caption-dailyhighs' => 'ഉയർന്ന നിലവാരമിട്ട ലേഖനങ്ങൾ: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'താഴ്ന്ന നിലവാരമിട്ട ലേഖനങ്ങൾ: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'ഈ ആഴ്ചയിൽ ഏറ്റവുമധികം മാറിയത്',
	'articleFeedbackv5-table-caption-recentlows' => 'സമീപകാല ഇറക്കങ്ങൾ',
	'articleFeedbackv5-table-heading-page' => 'താൾ',
	'articleFeedbackv5-table-heading-average' => 'ശരാശരി',
	'articleFeedbackv5-copy-above-highlow-tables' => 'ഇത് പരീക്ഷണാടിസ്ഥാനത്തിലുള്ള സൗകര്യമാണ്. അഭിപ്രായങ്ങൾ [$1 സംവാദം താളിൽ] തീർച്ചയായും അറിയിക്കുക.',
	'articlefeedbackv5-emailcapture-response-body' => 'നമസ്കാരം!

{{SITENAME}} മെച്ചപ്പെടുത്താനുള്ള സഹായം ചെയ്യാൻ സന്നദ്ധത പ്രകടിപ്പിച്ചതിന് ആത്മാർത്ഥമായ നന്ദി.

താഴെ നൽകിയിരിക്കുന്ന കണ്ണിയിൽ ഞെക്കി താങ്കളുടെ ഇമെയിൽ ദയവായി സ്ഥിരീകരിക്കുക: 

$1

താങ്കൾക്ക് ഇതും സന്ദർശിക്കാവുന്നതാണ്:

$2

എന്നിട്ട് താഴെ കൊടുത്തിരിക്കുന്ന സ്ഥിരീകരണ കോഡ് നൽകുക:

$3

{{SITENAME}} സംരംഭം മെച്ചപ്പെടുത്താൻ താങ്കൾക്ക് എങ്ങനെ സഹായിക്കാനാകും എന്ന് തീരുമാനിക്കാൻ ഞങ്ങൾ താങ്കളുമായി ഉടനെ ബന്ധപ്പെടുന്നതായിരിക്കും.

താങ്കളുടെ ഇച്ഛ പ്രകാരം അല്ല ഈ അഭ്യർത്ഥനയെങ്കിൽ, ഈ ഇമെയിൽ അവഗണിക്കുക, ഞങ്ങൾ താങ്കൾക്ക് പിന്നീടൊന്നും അയച്ച് ബുദ്ധിമുട്ടിയ്ക്കില്ല.

ആശംസകൾ, നന്ദി,
{{SITENAME}} സ്നേഹിതർ',
);

/** Mongolian (Монгол)
 * @author Chinneeb
 */
$messages['mn'] = array(
	'articlefeedbackv5-survey-submit' => 'Явуулах',
);

/** Malay (Bahasa Melayu)
 * @author Anakmalaysia
 * @author Aviator
 */
$messages['ms'] = array(
	'articlefeedbackv5' => 'Papan pemuka maklum balas rencana',
	'articlefeedbackv5-desc' => 'Pentaksiran rencana (versi percubaan)',
	'articlefeedbackv5-survey-question-origin' => 'Di laman yang manakah anda berada ketika anda memulakan pantauan ini?',
	'articlefeedbackv5-survey-question-whyrated' => 'Sila maklumkan kami sebab anda menilai laman ini hari ini (tandai semua yang berkenaan):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Saya ingin menyumbang kepada penilaian keseluruhan laman ini',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Saya berharap agar penilaian saya akan memperbaiki perkembangan dalam laman',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Saya ingin menyumbang kepada {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Saya ingin berkongsi pendapat',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Saya tidak menyumbangkan apa-apa penilaian hari ini, tetapi hendak memberi maklum bakas kepada ciri ini',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Lain',
	'articlefeedbackv5-survey-question-useful' => 'Adakah anda setuju bahawa penilaian yang diberikan ini adalah berguna dan mudah difahami?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Мезекс?',
	'articlefeedbackv5-survey-question-comments' => 'Adakah anda mempunyai sebarang komen tambahan?',
	'articlefeedbackv5-survey-submit' => 'Serahkan',
	'articlefeedbackv5-survey-title' => 'Sila jawab beberapa soalan',
	'articlefeedbackv5-survey-thanks' => 'Terima kasih kerana membalas tinjauan kami.',
	'articlefeedbackv5-survey-disclaimer' => 'Dengan penyerahan ini, anda bersetuju dengan ketelusan mengikut [http://wikimediafoundation.org/wiki/Feedback_privacy_statement syarat-syarat] ini',
	'articlefeedbackv5-error' => 'Berlakunya ralat. Sila cuba lagi kemudian.',
	'articlefeedbackv5-form-switch-label' => 'Nilai laman ini',
	'articlefeedbackv5-form-panel-title' => 'Nilai laman ini',
	'articlefeedbackv5-form-panel-explanation' => 'Apakah ini?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:MaklumBalasRencana',
	'articlefeedbackv5-form-panel-clear' => 'Tarik balik markah ini',
	'articlefeedbackv5-form-panel-expertise' => 'Saya berpengetahuan tinggi tentang topik ini (pilihan)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Saya memegang ijazah kolej/maktab/universiti yang berkenaan',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Kerjaya saya menyentuh tentang topik ini',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Saya amat berminat dengan topik ini secara peribadi',
	'articlefeedbackv5-form-panel-expertise-other' => 'Sumber pengetahuan saya tidak tersenarai di sini',
	'articlefeedbackv5-form-panel-helpimprove' => 'Saya ingin membantu mempertingkat Wikipedia, hantarkan saya e-mel (pilihan)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Kami akan menghantar e-mel pengesahan kepada anda. Kami tidak akan berkongsi alamat anda dengan pihak luar mengikut $1 kami.',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'kenyataan keperibadian maklum balas',
	'articlefeedbackv5-form-panel-submit' => 'Serahkan penilaian',
	'articlefeedbackv5-form-panel-pending' => 'Penilaian anda belum diserahkan',
	'articlefeedbackv5-form-panel-success' => 'Berjaya disimpan',
	'articlefeedbackv5-form-panel-expiry-title' => 'Penilaian anda telah luput',
	'articlefeedbackv5-form-panel-expiry-message' => 'Sila nilai semula laman ini dan serahkan penilaian baru.',
	'articlefeedbackv5-report-switch-label' => 'Lihat penilaian laman',
	'articlefeedbackv5-report-panel-title' => 'Penilaian laman',
	'articlefeedbackv5-report-panel-description' => 'Purata penilaian semasa.',
	'articlefeedbackv5-report-empty' => 'Tiada penilaian',
	'articlefeedbackv5-report-ratings' => '$1 penilaian',
	'articlefeedbackv5-field-trustworthy-label' => 'Boleh dipercayai',
	'articlefeedbackv5-field-trustworthy-tip' => 'Adakah anda berpendapat bahawa laman ini mempunyai petikan yang mencukupi, dan petikan-petikan itu datang dari sumber-sumber yang boleh dipercayai?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Ketandusan sumber yang bereputasi baik',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Kekurangan sumber yang bereputasi baik',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Cukup sumber yang bereputasi baik',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Banyak sumber yang bereputasi baik',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Banyak sekali sumber yang bereputasi baik',
	'articlefeedbackv5-field-complete-label' => 'Lengkap',
	'articlefeedbackv5-field-complete-tip' => 'Adakah anda berpendapat bahawa laman ini merangkumi bahan-bahan topik terpenting yang sewajarnya?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Ketandusan maklumat',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Sedikit maklumat',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Mengandungi maklumat penting, tetapi berlompang',
	'articlefeedbackv5-field-complete-tooltip-4' => 'Mengandungi kebanyakan maklumat penting',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Liputan menyeluruh',
	'articlefeedbackv5-field-objective-label' => 'Objektif',
	'articlefeedbackv5-field-objective-tip' => 'Adakah anda berpendapat bahawa laman ini menunjukkan pernyataan yang adil daripada semua sudut pandangan tentang isu ini?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Terlalu berat sebelah',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Sederhana berat sebelah',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Sedikit berat sebelah',
	'articlefeedbackv5-field-objective-tooltip-4' => 'Tiada berat sebelah yang ketara',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Langsung tidak berat sebelah',
	'articlefeedbackv5-field-wellwritten-label' => 'Kemas',
	'articlefeedbackv5-field-wellwritten-tip' => 'Adakah anda berpendapat bahawa laman ini disusun dan ditulis dengan kemas?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Tidak boleh difahami',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Sukar difahami',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Cukup boleh difahami',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Mudah difahami',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Amat mudah difahami',
	'articlefeedbackv5-pitch-reject' => 'Lain kalilah',
	'articlefeedbackv5-pitch-or' => 'atau',
	'articlefeedbackv5-pitch-thanks' => 'Terima kasih! Penilaian anda telah disimpan.',
	'articlefeedbackv5-pitch-survey-message' => 'Sila mengambil sedikit masa untuk melengkapkan tinjauan yang ringkas ini.',
	'articlefeedbackv5-pitch-survey-accept' => 'Mulakan tinjauan',
	'articlefeedbackv5-pitch-join-message' => 'Adakah anda ingin membuka akaun?',
	'articlefeedbackv5-pitch-join-body' => 'Akaun akan membantu anda menjejaki suntingan anda, melibatkan diri dalam perbincangan, dan menyertai komuniti.',
	'articlefeedbackv5-pitch-join-accept' => 'Buka akaun',
	'articlefeedbackv5-pitch-join-login' => 'Log masuk',
	'articlefeedbackv5-pitch-edit-message' => 'Tahukah anda bahawa anda boleh menyunting laman ini?',
	'articlefeedbackv5-pitch-edit-accept' => 'Sunting laman ini',
	'articlefeedbackv5-survey-message-success' => 'Terima kasih kerana membalas tinjauan kami.',
	'articlefeedbackv5-survey-message-error' => 'Berlakunya ralat.
 Sila cuba lagi kemudian.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Penilaian tertinggi dan terendah hari ini',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Laman yang tertinggi penilaiannya: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Laman yang terendah penilaiannya: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Laman yang paling banyak berubah minggu ini',
	'articleFeedbackv5-table-caption-recentlows' => 'Penilaian terendah terkini',
	'articleFeedbackv5-table-heading-page' => 'Laman',
	'articleFeedbackv5-table-heading-average' => 'Purata',
	'articleFeedbackv5-copy-above-highlow-tables' => 'Ciri ini sedang diuji kaji. Sila berikan maklum balas di [$1 laman perbincangan].',
	'articlefeedbackv5-dashboard-bottom' => "'''Perhatian''': Kami akan terus menguji kaji cara-cara lain untuk menimbulkan rencana di papan pemuka ini. Ketika ini, papan pemuka merangkumi rencana-rencana berikut:
* Laman yang tertinggi/terendah penilaiannya: rencana yang menerima sekurang-kurangnya 10 penilaian dalam masa 24 jam yang lalu.  Puratanya dihitung dengan mengambil min semua penilaian yang diterima dalam masa 24 jam yang lalu.
* Penilaian terendah terkini: rencana yang menerima 70% atau lebih penilaian rendah (2 bintang ke bawah) dalam mana-mana kategori dalam masa 24 jam yang lalu; hanya mengambil kira rencana yang menerima sekurang-kurangnya 10 penilaian dalam masa 24 jam yang lalu.",
	'articlefeedbackv5-disable-preference' => 'Jangan tunjukkan widget Maklum balas rencana pada laman',
	'articlefeedbackv5-emailcapture-response-body' => 'Selamat sejahtera!

Terima kasih kerana menunjukkan minat untuk membantu mempertingkatkan {{SITENAME}}.

Sila luangkan sedikit masa untuk mengesahkan e-mel anda dengan mengklik pautan berikut: 

$1

Anda juga boleh melawati:

$2

Dan isikan kod pengesahan yang berikut:

$3

Kami akan menghubungi anda sebentar lagi dengan cara-cara untuk anda mempertingkat mutu {{SITENAME}}.

Jika bukan anda yang membuat permohonan ini, sila abaikan e-mel ini dan kami tidak akan menghantar apa-apa lagi kepada anda.

Sekian, terima kasih,
Pasukan {{SITENAME}}',
);

/** Maltese (Malti)
 * @author Chrisportelli
 */
$messages['mt'] = array(
	'articlefeedbackv5-desc' => 'Rispons tal-artiklu',
	'articlefeedbackv5-survey-question-origin' => "F'liema paġna kont meta bdejt dan l-istħarriġ?",
	'articlefeedbackv5-survey-question-whyrated' => "Jekk jogħġbok għarrafna għaliex ivvalutajt din il-paġna illum (tista' tagħżel iktar minn waħda):",
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Ridt nikkontribwixxi fil-valutazzjoni ġenerali tal-paġna',
	'articlefeedbackv5-survey-answer-whyrated-development' => "Nittama li l-valutazzjoni tiegħek taffettwa b'mod pożittiv l-iżvilupp tal-paġna",
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Xtaqt nikkontribwixxi fuq {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Nieħu gost naqsam l-opinjoni tiegħi',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Ma tajtx valutazzjoni illum, imma ridt nagħti rispons fuq din il-funzjonalità',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Oħrajn',
	'articlefeedbackv5-survey-question-useful' => 'Inti temmen li l-valutazzjoni mogħtija hi utli u ċara?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Għaliex?',
	'articlefeedbackv5-survey-question-comments' => 'Għandek xi kummenti oħra?',
	'articlefeedbackv5-survey-submit' => 'Ibgħat',
	'articlefeedbackv5-survey-title' => 'Jekk jogħġbok wieġeb xi ftit mistoqsijiet',
	'articlefeedbackv5-survey-thanks' => 'Grazzi talli komplejt dan l-istħarriġ.',
	'articlefeedbackv5-survey-disclaimer' => "Sabiex tgħin ittejjeb din il-funzjonalità, ir-rispons tiegħek jista' jiġi maqsum b'mod anonimu mal-komunità tal-Wikipedija.",
	'articlefeedbackv5-error' => 'Kien hemm żball. Jekk jogħġbok, ipprova iktar tard.',
	'articlefeedbackv5-form-switch-label' => 'Ivvaluta din il-paġna',
	'articlefeedbackv5-form-panel-title' => 'Ivvaluta din il-paġna',
	'articlefeedbackv5-form-panel-explanation' => "X'inhi din?",
	'articlefeedbackv5-form-panel-clear' => 'Neħħi din il-valutazzjoni',
	'articlefeedbackv5-form-panel-expertise' => 'Għandi għarfien tajjeb ħafna dwar dan is-suġġet (mhux obbligatorju)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Għandi grad minn kulleġġ/università dwar is-suġġett',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Hija parti mix-xogħol tiegħi',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Hija passjoni profonda personali',
	'articlefeedbackv5-form-panel-expertise-other' => 'Is-sors tal-għarfien tiegħi mhux imniżżla hawnhekk',
	'articlefeedbackv5-form-panel-helpimprove' => 'Nixtieq ngħin lill-Wikipedija, ibgħatuli ittra-e (mhux obbligatorju)',
	'articlefeedbackv5-form-panel-helpimprove-note' => "Aħna nibgħatulek ittra-e ta' konferma. Mhux se nqassmu l-indirizz tiegħek ma' ħadd. $1",
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Politika dwar il-privatezza',
	'articlefeedbackv5-form-panel-submit' => 'Ibgħat il-voti',
	'articlefeedbackv5-form-panel-pending' => 'Il-valutazzjoni tiegħek għadhom ma ntbagħtux',
	'articlefeedbackv5-form-panel-success' => 'Salvati korrettament',
	'articlefeedbackv5-form-panel-expiry-title' => 'Il-voti tiegħek skadew',
	'articlefeedbackv5-form-panel-expiry-message' => "Erġa' agħti l-valutazzjoni tiegħek u ibgħat voti ġodda.",
	'articlefeedbackv5-report-switch-label' => 'Ara l-valutazzjoni tal-paġna',
	'articlefeedbackv5-report-panel-title' => 'Valutazzjoni tal-paġna',
	'articlefeedbackv5-report-panel-description' => 'Medja tal-valutazzjoni attwali.',
	'articlefeedbackv5-report-empty' => 'L-ebda vot',
	'articlefeedbackv5-report-ratings' => '$1 voti',
	'articlefeedbackv5-field-trustworthy-label' => 'Affidabbli',
	'articlefeedbackv5-field-trustworthy-tip' => 'Tħoss li din l-paġna għandha biżżejjed referenzi u li dawn ir-reerenzi ġejjin minn sorsi affidabbli?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Nieqes minn sorsi affidabbli',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Ftit sorsi affidabbli',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Sorsi affidabbli adegwati',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Sorsi affidabbli tajbin',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Sorsi affidabbli eċċellenti',
	'articlefeedbackv5-field-complete-label' => 'Kompluta',
	'articlefeedbackv5-field-complete-tip' => 'Tħoss li din il-paġna tkopri l-oqsma essenzjali tas-suġġett?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Nieqsa ħafna mill-informazzjoni',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Għandha ftit informazzjoni',
	'articlefeedbackv5-field-complete-tooltip-3' => "Għandha l-informazzjoni prinċipali, imma b'ċerti nuqqasijiet",
	'articlefeedbackv5-field-complete-tooltip-4' => 'Għandha l-parti prinċipali tal-informazzjoni importanti',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Kopertura komprensiva',
	'articlefeedbackv5-field-objective-label' => 'Objettiva',
	'articlefeedbackv5-field-objective-tip' => 'Tħoss li din il-paġna turi rappreżentazzjoni ġusta tal-perspettivi kollha tal-punti di vista fuq is-suġġett?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Preġudikata ħafna',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Preġudizzju moderat',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Preġudizzju minimu',
	'articlefeedbackv5-field-objective-tooltip-4' => 'L-ebda preġudizzju ovvju',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Kompletament imparzjali',
	'articlefeedbackv5-field-wellwritten-label' => 'Kitba tajba',
	'articlefeedbackv5-field-wellwritten-tip' => 'Tħoss li din il-paġna hi organizzata u miktuba tajjeb?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Inkomprensibbli',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Diffiċli biex tifimha',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Ċara biżżejjed',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Ċara ħafna',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Ċarezza eċċezzjonali',
	'articlefeedbackv5-pitch-reject' => 'Forsi iktar tard',
	'articlefeedbackv5-pitch-or' => 'jew',
	'articlefeedbackv5-pitch-thanks' => 'Grazzi! Il-valutazzjoni tiegħek ġiet salvata.',
	'articlefeedbackv5-pitch-survey-message' => 'Jekk jogħġbok ħu mument sabiex tkompli dan l-istħarriġ qasir.',
	'articlefeedbackv5-pitch-survey-accept' => 'Ibda l-istħarriġ',
	'articlefeedbackv5-pitch-join-message' => 'Ridt toħloq kont?',
	'articlefeedbackv5-pitch-join-body' => "Kont iħallik iżomm traċċa tal-modifiki tiegħek, tipparteċipa f'diskussjonijiet u li tkun parti mill-komunità.",
	'articlefeedbackv5-pitch-join-accept' => 'Oħloq kont',
	'articlefeedbackv5-pitch-join-login' => 'Idħol',
	'articlefeedbackv5-pitch-edit-message' => "Kont taf li tista' timmodifika din il-paġna?",
	'articlefeedbackv5-pitch-edit-accept' => 'Immodifika din il-paġna',
	'articlefeedbackv5-survey-message-success' => 'Grazzi talli komplet dan l-istħarriġ.',
	'articlefeedbackv5-survey-message-error' => 'Kien hemm żball. Jekk jogħġbok, ipprova iktar tard.',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Paġni bl-ogħla valutazzjoni: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Paġni bl-inqas valutazzjoni: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'L-iktar li mbiddlu fil-ġimgħa',
	'articleFeedbackv5-table-heading-page' => 'Paġna',
	'articleFeedbackv5-table-heading-average' => 'Medja',
	'articleFeedbackv5-copy-above-highlow-tables' => "Din hija funzjoni sperimentali. Ħalli r-rispons tiegħek fil-[$1 paġna ta' diskussjoni].",
	'articlefeedbackv5-disable-preference' => "Turix il-''widget'' tal-valutazzjoni fuq il-paġni (Article Feedback)",
	'articlefeedbackv5-emailcapture-response-body' => "Grazzi talli wrejt interess li ttejjeb lil {{SITENAME}}.

Ħu mument sabiex tiċċekkja l-indirizz elettroniku tiegħek billi tagħfas fuq il-ħoloqa t'hawn taħt:

$1

Tista' wkoll iżżur:

$2

U ddaħħal dan il-kodiċi ta' konferma:

$3

Aħna nkunu f'kuntatt miegħek ma ndumux fuq kif tista' tgħin ittejjeb lil {{SITENAME}}.

Jekk m'għamiltx din ir-rikjesta, injora din il-posta u aħna mhux se nibgħatulek xejn iktar.

Xewqat sbieħ u grazzi,
It-tim ta' {{SITENAME}}",
);

/** Erzya (Эрзянь)
 * @author Botuzhaleny-sodamo
 */
$messages['myv'] = array(
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Лия',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Мезекс?',
	'articlefeedbackv5-survey-submit' => 'Максомс',
	'articlefeedbackv5-field-wellwritten-label' => 'Парсте сёрмадозь',
	'articlefeedbackv5-pitch-or' => 'эли',
	'articlefeedbackv5-pitch-edit-accept' => 'Витнемс-петнемс те лопанть',
	'articleFeedbackv5-table-heading-page' => 'Лопазо',
);

/** Nahuatl (Nāhuatl)
 * @author Teòtlalili
 */
$messages['nah'] = array(
	'articlefeedbackv5-pitch-or' => 'nòso',
);

/** Nepali (नेपाली)
 * @author Bhawani Gautam
 * @author Bhawani Gautam Rhk
 * @author सरोज कुमार ढकाल
 */
$messages['ne'] = array(
	'articlefeedbackv5-desc' => 'लेखकोबारेमा पृष्ठपोषण',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => ' {{SITENAME}}मा योगदान गर्न मन लागेको थियो ।',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'मलाई मेरो बिचार बाड्न मन पर्छ \\',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'किन?',
	'articlefeedbackv5-survey-question-comments' => 'तपाईंसित अरु कुनै अतिरिक्त टिप्पणीहरु छन्?',
	'articlefeedbackv5-survey-submit' => 'बुझाउने',
	'articlefeedbackv5-form-panel-title' => 'यस पृष्ठको मूल्य निर्धारण गर्ने',
	'articlefeedbackv5-form-panel-success' => 'सफलता पूर्वक संग्रह गरियो',
	'articlefeedbackv5-field-trustworthy-label' => 'विश्वस्त',
	'articlefeedbackv5-field-complete-label' => 'पूर्ण',
	'articlefeedbackv5-pitch-or' => 'अथवा',
	'articlefeedbackv5-pitch-survey-accept' => 'सर्वेक्षण सुरु गर्ने',
	'articlefeedbackv5-pitch-join-message' => 'के  तपाईं खाता बनाउन चाहनुहुन्थ्यो?',
	'articlefeedbackv5-pitch-join-accept' => 'खाता खोल्ने',
	'articlefeedbackv5-pitch-join-login' => 'प्रवेश गर्ने',
	'articlefeedbackv5-pitch-edit-message' => 'तपाईं यो पृष्ठलाई सम्पादन गर्न सक्नुहुन्छ भनेर  तपाईंलाई थाह थियो?',
	'articlefeedbackv5-pitch-edit-accept' => 'यो पृष्ट सम्पादन गर्ने',
	'articlefeedbackv5-survey-message-success' => 'सर्वेक्षण भर्नु भएकोमा धन्यवाद',
	'articlefeedbackv5-survey-message-error' => 'एउटा त्रुटि भएकोछ
कृपया फेरि प्रयास गर्नुहोस्।',
);

/** Dutch (Nederlands)
 * @author Catrope
 * @author McDutchie
 * @author SPQRobin
 * @author Siebrand
 * @author Tjcool007
 */
$messages['nl'] = array(
	'articlefeedbackv5' => 'Dashboard voor paginawaardering',
	'articlefeedbackv5-desc' => 'Paginabeoordeling (testversie)',
	'articlefeedbackv5-survey-question-origin' => 'Op welke pagina was u toen u aan deze vragenlijst bent begonnen?',
	'articlefeedbackv5-survey-question-whyrated' => 'Laat ons weten waarom u deze pagina vandaag hebt beoordeeld (kies alle redenen die van toepassing zijn):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Ik wil bijdragen aan de beoordelingen van de pagina',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Ik hoop dat mijn beoordeling een positief effect heeft op de ontwikkeling van de pagina',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Ik wilde bijdragen aan {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Ik vind het fijn om mijn mening te delen',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => "Ik heb vandaag geen pagina's beoordeeld, maar in de toekomst wil ik wel terugkoppeling geven",
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Anders',
	'articlefeedbackv5-survey-question-useful' => 'Vindt u dat de beoordelingen bruikbaar en duidelijk zijn?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Waarom?',
	'articlefeedbackv5-survey-question-comments' => 'Hebt u nog opmerkingen?',
	'articlefeedbackv5-survey-submit' => 'Opslaan',
	'articlefeedbackv5-survey-title' => 'Beantwoord alstublieft een paar vragen',
	'articlefeedbackv5-survey-thanks' => 'Bedankt voor het beantwoorden van de vragen.',
	'articlefeedbackv5-survey-disclaimer' => 'Door op te slaan, gaat u akkoord met transparantie onze deze $1.',
	'articlefeedbackv5-survey-disclaimerlink' => 'voorwaarden',
	'articlefeedbackv5-error' => 'Er is een fout opgetreden.
Probeer het later opnieuw.',
	'articlefeedbackv5-form-switch-label' => 'Deze pagina waarderen',
	'articlefeedbackv5-form-panel-title' => 'Deze pagina waarderen',
	'articlefeedbackv5-form-panel-explanation' => 'Wat is dit?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:Paginaterugkoppeling',
	'articlefeedbackv5-form-panel-clear' => 'Deze beoordeling verwijderen',
	'articlefeedbackv5-form-panel-expertise' => 'Ik ben zeer goed geïnformeerd over dit onderwerp (optioneel)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Ik heb een relevante opleiding gevolgd op HBO/WO-niveau',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Dit onderwerp is onderdeel van mijn beroep',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Dit is een diepgevoelde persoonlijke passie',
	'articlefeedbackv5-form-panel-expertise-other' => 'De bron van mijn kennis is geen keuzeoptie',
	'articlefeedbackv5-form-panel-helpimprove' => 'Ik wil graag helpen Wikipedia te verbeteren, stuur me een e-mail (optioneel)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'We sturen u een bevestigingse-mail. We delen uw e-mailadres niet met externe partijen per ons $1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'privacyverklaring over terugkoppeling',
	'articlefeedbackv5-form-panel-submit' => 'Beoordelingen opslaan',
	'articlefeedbackv5-form-panel-pending' => 'Uw waarderingen zijn niet opgeslagen',
	'articlefeedbackv5-form-panel-success' => 'Opgeslagen',
	'articlefeedbackv5-form-panel-expiry-title' => 'Uw beoordelingen zijn verlopen',
	'articlefeedbackv5-form-panel-expiry-message' => 'Beoordeel deze pagina alstublieft opnieuw en sla uw beoordeling op.',
	'articlefeedbackv5-report-switch-label' => 'Paginawaarderingen weergeven',
	'articlefeedbackv5-report-panel-title' => 'Paginawaarderingen',
	'articlefeedbackv5-report-panel-description' => 'Huidige gemiddelde beoordelingen.',
	'articlefeedbackv5-report-empty' => 'Geen beoordelingen',
	'articlefeedbackv5-report-ratings' => '$1 beoordelingen',
	'articlefeedbackv5-field-trustworthy-label' => 'Betrouwbaar',
	'articlefeedbackv5-field-trustworthy-tip' => 'Vindt u dat deze pagina voldoende bronvermeldingen heeft en dat de bronvermeldingen betrouwbaar zijn?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Zonder betrouwbare bronnen',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Weinig betrouwbare bronnen',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Voldoende betrouwbare bronnen',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Goede betrouwbare bronnen',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Uitstekend betrouwbare bronnen',
	'articlefeedbackv5-field-complete-label' => 'Afgerond',
	'articlefeedbackv5-field-complete-tip' => 'Vindt u dat deze pagina de essentie van dit onderwerp bestrijkt?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Meeste informatie ontbreekt',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Bevat enige informatie',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Bevat belangrijke informatie, maar met gaten',
	'articlefeedbackv5-field-complete-tooltip-4' => 'Bevat de meeste belangrijke informatie',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Uitgebreide dekking',
	'articlefeedbackv5-field-objective-label' => 'Onbevooroordeeld',
	'articlefeedbackv5-field-objective-tip' => 'Vindt u dat deze pagina een eerlijke weergave is van alle invalshoeken voor dit onderwerp?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Zeer partijdig',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Matig partijdig',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Beetje partijdig',
	'articlefeedbackv5-field-objective-tooltip-4' => 'Geen duidelijke partijdigheid',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Helemaal niet partijdig',
	'articlefeedbackv5-field-wellwritten-label' => 'Goed geschreven',
	'articlefeedbackv5-field-wellwritten-tip' => 'Vindt u dat deze pagina een correcte opbouw heeft een goed is geschreven?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Onbegrijpelijk',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Moeilijk te begrijpen',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Voldoende duidelijk',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Heel duidelijk',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Uitzonderlijk duidelijk',
	'articlefeedbackv5-pitch-reject' => 'Nu niet',
	'articlefeedbackv5-pitch-or' => 'of',
	'articlefeedbackv5-pitch-thanks' => 'Bedankt!
Uw beoordeling is opgeslagen.',
	'articlefeedbackv5-pitch-survey-message' => 'Neem alstublieft even de tijd om een korte vragenlijst in te vullen.',
	'articlefeedbackv5-pitch-survey-accept' => 'Vragenlijst starten',
	'articlefeedbackv5-pitch-join-message' => 'Wilt u een gebruiker aanmaken?',
	'articlefeedbackv5-pitch-join-body' => 'Als u een gebruiker hebt, kunt u uw bewerkingen beter volgen, meedoen aan overleg en een vollediger onderdeel zijn van de gemeenschap.',
	'articlefeedbackv5-pitch-join-accept' => 'Gebruiker aanmaken',
	'articlefeedbackv5-pitch-join-login' => 'Aanmelden',
	'articlefeedbackv5-pitch-edit-message' => 'Wist u dat u deze pagina kunt bewerken?',
	'articlefeedbackv5-pitch-edit-accept' => 'Deze pagina bewerken',
	'articlefeedbackv5-survey-message-success' => 'Bedankt voor het beantwoorden van de vragen.',
	'articlefeedbackv5-survey-message-error' => 'Er is een fout opgetreden.
Probeer het later opnieuw.',
	'articlefeedbackv5-privacyurl' => 'http://wikimediafoundation.org/wiki/Feedback_privacy_statement/nl',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Hoogte- en dieptepunten van vandaag',
	'articleFeedbackv5-table-caption-dailyhighs' => "Pagina's met de hoogste waarderingen: $1",
	'articleFeedbackv5-table-caption-dailylows' => "Pagina's met de laagste waarderingen: $1",
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Deze week de meeste wijzigingen',
	'articleFeedbackv5-table-caption-recentlows' => 'Recente dieptepunten',
	'articleFeedbackv5-table-heading-page' => 'Pagina',
	'articleFeedbackv5-table-heading-average' => 'Gemiddelde',
	'articleFeedbackv5-copy-above-highlow-tables' => 'Dit is experimentele functionaliteit. Geef alstublieft terugkoppeling op de [$1 overlegpagina].',
	'articlefeedbackv5-dashboard-bottom' => "'''Let op''': We gaan door met experimenteren met verschillende manieren van het weergeven van pagina's in deze dashboards. Op dit moment bevatten de dashboards onder meer de volgende pagina's:
* Pagina's met de hoogste / laagste waarderingen: pagina's die ten minste tien waarderingen hebben gekregen in de afgelopen 24 uur. Gemiddelden worden berekend door het gemiddelde te nemen van alle waarderingen in de afgelopen 24 uur.
* Recente dieptepunten: pagina's die 70% of meer laag (twee sterren of lager) worden gewaardeerd in elke categorie in de afgelopen 24 uur. Alleen pagina's die op zijn minst tien waarderingen hebben gekregen in de afgelopen 24 uur worden opgenomen.",
	'articlefeedbackv5-disable-preference' => "Widget paginaterugkoppeling niet op pagina's weergeven",
	'articlefeedbackv5-emailcapture-response-body' => 'Hallo!

Dank u wel voor uw interesse in het verbeteren van {{SITENAME}}.

Bevestig alstublieft uw e-mailadres door op de volgende verwijziging te klikken:

$1

U kunt ook gaan naar:

$2

En daar de volgende bevestigingscode invoeren:

$3

We nemen binnenkort contact met u op over hoe u kunt helpen {{SITENAME}} te verbeteren.

Als u niet hebt gevraagd om dit bericht, negeer deze e-mail dan en dan krijgt u geen e-mail meer van ons.

Dank u!

Met vriendelijke groet,

Het team van {{SITENAME}}',
);

/** ‪Nederlands (informeel)‬ (‪Nederlands (informeel)‬)
 * @author Siebrand
 */
$messages['nl-informal'] = array(
	'articlefeedbackv5-survey-question-origin' => 'Op welke pagina was je toen je aan deze vragenlijst bent begonnen?',
	'articlefeedbackv5-survey-question-whyrated' => 'Laat ons weten waarom je deze pagina vandaag hebt beoordeeld (kies alle redenen die van toepassing zijn):',
	'articlefeedbackv5-survey-question-useful' => 'Vind je dat de beoordelingen bruikbaar en duidelijk zijn?',
	'articlefeedbackv5-survey-question-comments' => 'Hebt je nog opmerkingen?',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'We sturen je een bevestigingse-mail. We delen je adres verder met niemand. $1',
	'articlefeedbackv5-form-panel-expiry-title' => 'Je beoordelingen zijn verlopen',
	'articlefeedbackv5-field-trustworthy-tip' => 'Vind je dat deze pagina voldoende bronvermeldingen heeft en dat de bronvermeldingen betrouwbaar zijn?',
	'articlefeedbackv5-field-complete-tip' => 'Vind je dat deze pagina de essentie van dit onderwerp bestrijkt?',
	'articlefeedbackv5-field-objective-tip' => 'Vind je dat deze pagina een eerlijke weergave is van alle invalshoeken voor dit onderwerp?',
	'articlefeedbackv5-field-wellwritten-tip' => 'Vind je dat deze pagina een correcte opbouw heeft een goed is geschreven?',
	'articlefeedbackv5-pitch-thanks' => 'Bedankt!
Je beoordeling is opgeslagen.',
	'articlefeedbackv5-pitch-join-message' => 'Wil je een gebruiker aanmaken?',
	'articlefeedbackv5-pitch-join-body' => 'Als je een gebruiker hebt, kan je je bewerkingen beter volgen, meedoen aan overleg en een vollediger onderdeel zijn van de gemeenschap.',
	'articlefeedbackv5-pitch-edit-message' => 'Wist je dat je deze pagina kunt bewerken?',
	'articlefeedbackv5-emailcapture-response-body' => 'Hallo!

Dank je wel voor je interesse in het verbeteren van {{SITENAME}}.

Bevestig alsjeblieft je e-mailadres door op de volgende verwijziging te klikken:

$1

Je kunt ook gaan naar:

$2

En daar de volgende bevestigingscode invoeren:

$3

We nemen binnenkort contact met je op over hoe u kunt helpen {{SITENAME}} te verbeteren.

Als je niet hebt gevraagd om dit bericht, negeer deze e-mail dan en dan krijg je geen e-mail meer van ons.

Dank je!

Groetjes,

Het team van {{SITENAME}}',
);

/** Norwegian Nynorsk (‪Norsk (nynorsk)‬)
 * @author Nghtwlkr
 */
$messages['nn'] = array(
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Kvifor?',
	'articlefeedbackv5-survey-submit' => 'Send',
	'articlefeedbackv5-pitch-or' => 'eller',
	'articlefeedbackv5-pitch-join-login' => 'Logg inn',
);

/** Norwegian (bokmål)‬ (‪Norsk (bokmål)‬)
 * @author Event
 * @author Nghtwlkr
 * @author Sjurhamre
 */
$messages['no'] = array(
	'articlefeedbackv5' => 'Panelbord for artikkelvurdering',
	'articlefeedbackv5-desc' => 'Artikkelvurdering (pilotversjon)',
	'articlefeedbackv5-survey-question-origin' => 'Hvilken side var du på når du startet denne undersøkelsen?',
	'articlefeedbackv5-survey-question-whyrated' => 'Gi oss beskjed om hvorfor du vurderte denne siden idag (huk av alle som passer):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Jeg ønsket å bidra til den generelle vurderingen av denne siden',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Jeg håper at min vurdering vil påvirke utviklingen av siden positivt',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Jeg ønsket å bidra til {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Jeg liker å dele min mening',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Jeg ga ingen vurderinger idag, men ønsket å gi tilbakemelding på denne funksjonen',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Annet',
	'articlefeedbackv5-survey-question-useful' => 'Tror du at vurderingene som blir gitt er nyttige og klare?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Hvorfor?',
	'articlefeedbackv5-survey-question-comments' => 'Har du noen ytterligere kommentarer?',
	'articlefeedbackv5-survey-submit' => 'Send',
	'articlefeedbackv5-survey-title' => 'Svar på noen få spørsmål',
	'articlefeedbackv5-survey-thanks' => 'Takk for at du fylte ut undersøkelsen.',
	'articlefeedbackv5-survey-disclaimer' => 'For å stimulere til å forbedre denne funksjonaliteten kan din tilbakemelding deles anonymt med Wikipedia-samfunnet.',
	'articlefeedbackv5-error' => 'En feil har oppstått. Prøv igjen senere.',
	'articlefeedbackv5-form-switch-label' => 'Vurder denne siden',
	'articlefeedbackv5-form-panel-title' => 'Vurder denne siden',
	'articlefeedbackv5-form-panel-explanation' => 'Hva er dette?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:ArticleFeedback',
	'articlefeedbackv5-form-panel-clear' => 'Fjern denne vurderingen',
	'articlefeedbackv5-form-panel-expertise' => 'Jeg er svært kunnskapsrik på dette området (valgfritt)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Jeg har en relevant høyskole-/universitetsgrad',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Det er en del av yrket mitt',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Det er en dypt personlig hobby/lidenskap',
	'articlefeedbackv5-form-panel-expertise-other' => 'Kilden til min kunnskap er ikke listet opp her',
	'articlefeedbackv5-form-panel-helpimprove' => 'Jeg ønsker å bidra til å forbedre Wikipedia, send meg en e-post (valgfritt)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Vi vil sende deg en bekreftelse på e-post. Vi vil ikke dele adressen din med noen andre. $1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Retningslinjer for personvern',
	'articlefeedbackv5-form-panel-submit' => 'Send vurdering',
	'articlefeedbackv5-form-panel-pending' => 'Vurderingene dine har ikke blitt sendt inn',
	'articlefeedbackv5-form-panel-success' => 'Lagret',
	'articlefeedbackv5-form-panel-expiry-title' => 'Vurderingen din har utløpt',
	'articlefeedbackv5-form-panel-expiry-message' => 'Revurder denne siden og send inn din nye vurdering.',
	'articlefeedbackv5-report-switch-label' => 'Vis sidevurderinger',
	'articlefeedbackv5-report-panel-title' => 'Sidevurderinger',
	'articlefeedbackv5-report-panel-description' => 'Gjeldende gjennomsnittskarakter.',
	'articlefeedbackv5-report-empty' => 'Ingen vurderinger',
	'articlefeedbackv5-report-ratings' => '$1 vurderinger',
	'articlefeedbackv5-field-trustworthy-label' => 'Pålitelig',
	'articlefeedbackv5-field-trustworthy-tip' => 'Føler du at denne siden har tilstrekkelig med siteringer og at disse siteringene kommer fra pålitelige kilder?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Mangler troverdige kilder',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Få troverdige kilder',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Tilstrekkelig troverdige kilder',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Godt anerkjente kilder',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Spesielt anerkjente kilder',
	'articlefeedbackv5-field-complete-label' => 'Fullfør',
	'articlefeedbackv5-field-complete-tip' => 'Føler du at denne siden dekker de vesentlige emneområdene som den burde?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Mangler det meste av informasjonen',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Inneholder noe informasjon',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Inneholder viktig informasjon, med noen mangler',
	'articlefeedbackv5-field-complete-tooltip-4' => 'Inneholder det meste av nøkkelinformasjonen',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Omfattende dekning',
	'articlefeedbackv5-field-objective-label' => 'Objektiv',
	'articlefeedbackv5-field-objective-tip' => 'Føler du at denne siden viser en rettferdig representasjon av alle perspektiv på problemet?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Sterkt subjektivt',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Moderat subjektivt',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Minimalt subjektivt',
	'articlefeedbackv5-field-objective-tooltip-4' => 'Ingen åpenbar subjektivitet',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Helt objektivt',
	'articlefeedbackv5-field-wellwritten-label' => 'Velskrevet',
	'articlefeedbackv5-field-wellwritten-tip' => 'Føler du at denne siden er godt organisert og godt skrevet?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Uforståelig',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Vanskelig å forstå',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Tilstrekkelig klart',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'God klarhet',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Enestående klarhet',
	'articlefeedbackv5-pitch-reject' => 'Kanskje senere',
	'articlefeedbackv5-pitch-or' => 'eller',
	'articlefeedbackv5-pitch-thanks' => 'Takk. Dine vurderinger har blitt lagret.',
	'articlefeedbackv5-pitch-survey-message' => 'Ta et øyeblikk til å fullføre en kort undersøkelse.',
	'articlefeedbackv5-pitch-survey-accept' => 'Start undersøkelsen',
	'articlefeedbackv5-pitch-join-message' => 'Ville du opprette en konto?',
	'articlefeedbackv5-pitch-join-body' => 'En konto hjelper deg å spore dine endringer, bli involvert i diskusjoner og være en del av fellesskapet.',
	'articlefeedbackv5-pitch-join-accept' => 'Opprett konto',
	'articlefeedbackv5-pitch-join-login' => 'Logg inn',
	'articlefeedbackv5-pitch-edit-message' => 'Visste du at du kan redigere denne siden?',
	'articlefeedbackv5-pitch-edit-accept' => 'Rediger denne siden',
	'articlefeedbackv5-survey-message-success' => 'Takk for at du fylte ut undersøkelsen.',
	'articlefeedbackv5-survey-message-error' => 'En feil har oppstått.
Prøv igjen senere.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Dagens oppturer og nedturer',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Artikler med høyest vurdering: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Artikler med lavest vurdering: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Mest endret denne uken',
	'articleFeedbackv5-table-caption-recentlows' => 'Ukens nedturer',
	'articleFeedbackv5-table-heading-page' => 'Side',
	'articleFeedbackv5-table-heading-average' => 'Gjennomsnitt',
	'articleFeedbackv5-copy-above-highlow-tables' => 'Dette er en eksperimentell funksjon.  Vennligst gi tilbakemelding på [$1 diskusjonssiden].',
	'articlefeedbackv5-dashboard-bottom' => "'''NB''': Vi vil fortsette å eksperimentere med forskjellige måter å eksponere artikler i disse oversiktene.  For tiden inkluderer oversiktene følgende artikler:
 * sider med høyeste/laveste rangering: artikler som har mottatt minst 10 rangeringer innen de siste 24 timene.  Middelverdien blir beregnet fra alle rangeringer mottatt det siste døgnet.
 * siste lavrangeringer: artikler som har fått 70% eller lavere (2 stjerner eller lavere) klassifisering i vilkårlig kategori de siste 24 timene. Bare artikler som har mottatt minst 10 rangeringer de siste 24 timene blir inkludert.",
	'articlefeedbackv5-disable-preference' => 'Skjul Artikkeltilbakemelding på sidene',
	'articlefeedbackv5-emailcapture-response-body' => 'Hei!

Takk for din interesse i å hjelpe oss med å forbedre {{SITENAME}}. Vennligst bekreft e-posten din ved å klikke på lenken under:

$1

Du kan også besøke:

$2

Og angi følgende bekreftelseskode:

$3

Vi tar snart kontakt for å forklare hvordan du kan forbedre {{SITENAME}}.

Om du ikke har bedt om denne e-posten, vennligst ignorer den. Den blir i så fall den siste du får fra oss.


Takk skal du ha og lykke til!

Hilsen {{SITENAME}}-teamet',
);

/** Oriya (ଓଡ଼ିଆ)
 * @author Odisha1
 */
$messages['or'] = array(
	'articlefeedbackv5-survey-submit' => 'ଦାଖଲକରିବା',
	'articleFeedbackv5-table-heading-page' => 'ପୃଷ୍ଠା',
	'articleFeedbackv5-table-heading-average' => 'ହାରାହାରି',
);

/** Ossetic (Ирон)
 * @author Amikeco
 */
$messages['os'] = array(
	'articleFeedbackv5-table-heading-average' => 'Рæстæмбис',
);

/** Polish (Polski)
 * @author Sp5uhe
 */
$messages['pl'] = array(
	'articlefeedbackv5' => 'Ocena artykułu',
	'articlefeedbackv5-desc' => 'Ocena artykułu (wersja pilotażowa)',
	'articlefeedbackv5-survey-question-origin' => 'Na jakie strony {{GENDER:|wchodziłeś|wchodziłaś}} od momentu rozpoczęcia ankiety?',
	'articlefeedbackv5-survey-question-whyrated' => 'Dlaczego oceniłeś dziś tę stronę (zaznacz wszystkie pasujące):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Chciałem mieć wpływ na ogólną ocenę strony',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Mam nadzieję, że moja ocena pozytywnie wpłynie na rozwój strony',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Chciałem mieć swój wkład w rozwój {{GRAMMAR:D.lp|{{SITENAME}}}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Lubię dzielić się swoją opinią',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Nie oceniałem dziś, ale chcę podzielić się swoją opinią na temat tego rozszerzenia',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Inny powód',
	'articlefeedbackv5-survey-question-useful' => 'Czy uważasz, że taka metoda oceniania jest użyteczna i czytelna?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Dlaczego?',
	'articlefeedbackv5-survey-question-comments' => 'Czy masz jakieś dodatkowe uwagi?',
	'articlefeedbackv5-survey-submit' => 'Zapisz',
	'articlefeedbackv5-survey-title' => 'Proszę udzielić odpowiedzi na kilka pytań',
	'articlefeedbackv5-survey-thanks' => 'Dziękujemy za wypełnienie ankiety.',
	'articlefeedbackv5-survey-disclaimer' => 'Aby umożliwić poprawienie tej funkcji, Twoja opinia może zostać udostępniona anonimowo społeczności Wikipedii.',
	'articlefeedbackv5-error' => 'Wystąpił błąd. Proszę spróbować ponownie później.',
	'articlefeedbackv5-form-switch-label' => 'Oceń tę stronę',
	'articlefeedbackv5-form-panel-title' => 'Oceń tę stronę',
	'articlefeedbackv5-form-panel-explanation' => 'Co to jest?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:Ocena artykułu',
	'articlefeedbackv5-form-panel-clear' => 'Usuń ranking',
	'articlefeedbackv5-form-panel-expertise' => 'Posiadam szeroką wiedzę w tym temacie (opcjonalne)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Znam to zagadnienie ze szkoły średniej lub ze studiów',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Zagadnienie związane jest z moim zawodem',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Bardzo wnikliwie interesuję się tym tematem',
	'articlefeedbackv5-form-panel-expertise-other' => 'Źródła mojej wiedzy nie ma na liście',
	'articlefeedbackv5-form-panel-helpimprove' => 'Chciałbym pomóc rozwijać Wikipedię – wyślij do mnie e‐maila (opcjonalne)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Otrzymasz od nas e‐maila potwierdzającego. Nie udostępnimy nikomu Twojego adresu. $1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Zasady ochrony prywatności',
	'articlefeedbackv5-form-panel-submit' => 'Prześlij opinię',
	'articlefeedbackv5-form-panel-pending' => 'Twoja ocena nie została jeszcze zapisana',
	'articlefeedbackv5-form-panel-success' => 'Zapisano',
	'articlefeedbackv5-form-panel-expiry-title' => 'Twoje oceny są nieaktualne',
	'articlefeedbackv5-form-panel-expiry-message' => 'Dokonaj ponownej oceny tej strony i zapisz jej wynik',
	'articlefeedbackv5-report-switch-label' => 'Jak strona była oceniana',
	'articlefeedbackv5-report-panel-title' => 'Ocena strony',
	'articlefeedbackv5-report-panel-description' => 'Aktualna średnia ocen.',
	'articlefeedbackv5-report-empty' => 'Brak ocen',
	'articlefeedbackv5-report-ratings' => '$1 {{PLURAL:$1|ocena|oceny|ocen}}',
	'articlefeedbackv5-field-trustworthy-label' => 'Godna zaufania',
	'articlefeedbackv5-field-trustworthy-tip' => 'Czy uważasz, że strona ma wystarczającą liczbę odnośników i że odnoszą się one do wiarygodnych źródeł?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Brak wiarygodnych źródeł',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Niewiele wiarygodnych źródeł',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Wystarczająca liczba wiarygodnych źródeł',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Dobra liczba wiarygodnych źródeł',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Bardzo wiele wiarygodnych źródeł',
	'articlefeedbackv5-field-complete-label' => 'Wyczerpanie tematu',
	'articlefeedbackv5-field-complete-tip' => 'Czy uważasz, że strona porusza wszystkie istotne aspekty, które powinna?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Brak wielu informacji',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Zawiera trochę informacji',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Zawiera najważniejsze informacje, ale ma braki',
	'articlefeedbackv5-field-complete-tooltip-4' => 'Zawiera większość najważniejszych informacji',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Wyczerpuje temat',
	'articlefeedbackv5-field-objective-label' => 'Neutralna',
	'articlefeedbackv5-field-objective-tip' => 'Czy uważasz, że strona prezentuje wszystkie punkty widzenia na to zagadnienie?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Bardzo tendencyjna',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Umiarkowanie tendencyjna',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Minimalnie tendencyjna',
	'articlefeedbackv5-field-objective-tooltip-4' => 'Brak jednoznacznej tendencyjności',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Całkowicie bezstronny',
	'articlefeedbackv5-field-wellwritten-label' => 'Dobrze napisana',
	'articlefeedbackv5-field-wellwritten-tip' => 'Czy uważasz, że strona jest właściwie sformatowana oraz zrozumiale napisana?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Niezrozumiała',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Trudna do zrozumienia',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'W miarę zrozumiała',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Dobrze zrozumiała',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Wyjątkowo dobrze zrozumiała',
	'articlefeedbackv5-pitch-reject' => 'Może później',
	'articlefeedbackv5-pitch-or' => 'lub',
	'articlefeedbackv5-pitch-thanks' => 'Dziękujemy! Wystawione przez Ciebie oceny zostały zapisane.',
	'articlefeedbackv5-pitch-survey-message' => 'Poświęć chwilę na wypełnienie krótkiej ankiety.',
	'articlefeedbackv5-pitch-survey-accept' => 'Rozpocznij ankietę',
	'articlefeedbackv5-pitch-join-message' => 'Czy chcesz utworzyć konto?',
	'articlefeedbackv5-pitch-join-body' => 'Posiadanie konta ułatwia śledzenie wprowadzanych zmian, udział w dyskusjach oraz integrację ze społecznością.',
	'articlefeedbackv5-pitch-join-accept' => 'Utwórz konto',
	'articlefeedbackv5-pitch-join-login' => 'Zaloguj się',
	'articlefeedbackv5-pitch-edit-message' => 'Czy wiesz, że możesz edytować tę stronę?',
	'articlefeedbackv5-pitch-edit-accept' => 'Edytuj tę stronę',
	'articlefeedbackv5-survey-message-success' => 'Dziękujemy za wypełnienie ankiety.',
	'articlefeedbackv5-survey-message-error' => 'Wystąpił błąd.
Proszę spróbować ponownie później.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Najwyższe i najniższe w dniu dzisiejszym',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Artykuły najwyżej oceniane: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Artykuły najniżej oceniane: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Najczęściej zmieniane w tym tygodniu',
	'articleFeedbackv5-table-caption-recentlows' => 'Najniższe ostatnio',
	'articleFeedbackv5-table-heading-page' => 'Strona',
	'articleFeedbackv5-table-heading-average' => 'Średnio',
	'articleFeedbackv5-copy-above-highlow-tables' => 'To jest rozwiązanie eksperymentalne. Wyraź swoją opinię na jego temat na [$1 stronie dyskusji].',
	'articlefeedbackv5-dashboard-bottom' => "'''Uwaga''' – będziemy nadal eksperymentować z różnymi metodami poprawiania artykułów. Obecnie pracujemy nad następującymi artykułami:
 * Strony oceniane najwyżej i najniżej – artykuły, które zostały co najmniej 10 razy ocenione w ciągu ostatnich 24 godzin. Wartości średnie są obliczane poprzez wyciągnięcie średniej ze wszystkich ocen z ostatnich 24 godzin.
 * Ostatnio słabe – artykuły, które uzyskały 70% lub więcej ocen negatywnych (2 gwiazdki lub mniej) w każdej kategorii, w ciągu ostatnich 24 godzin. Uwzględniane są tylko te artykuły, które otrzymały przynajmniej 10 ocen w ostatnich 24 godzinach.",
	'articlefeedbackv5-disable-preference' => 'Nie pokazuj na stronach widżetu opinii o artykule',
	'articlefeedbackv5-emailcapture-response-body' => 'Witaj!

Dziękujemy za zainteresowanie udoskonalaniem {{GRAMMAR:D.lp|{{SITENAME}}}}.

Poświęć chwilę na potwierdzenie swojego adres e‐mail – kliknij link

$1

Możesz również odwiedzić

$2

i wprowadzić kod potwierdzający

$3

Będziemy nadal współpracować, aby udoskonalić {{GRAMMAR:B.lp|{{SITENAME}}}}.

Jeśli to nie Ty spowodowałeś wysłanie tego e‐maila, wystarczy że go zignorujesz – niczego więcej do Ciebie nie wyślemy.

Pozdrawiamy i dziękujemy,
zespół {{GRAMMAR:D.lp|{{SITENAME}}}}.',
);

/** Piedmontese (Piemontèis)
 * @author Borichèt
 * @author Dragonòt
 */
$messages['pms'] = array(
	'articlefeedbackv5' => "Cruscòt ëd valutassion ëd j'artìcoj",
	'articlefeedbackv5-desc' => "Version pilòta dla valutassion ëd j'artìcoj",
	'articlefeedbackv5-survey-question-origin' => "Ansima a che pàgina a l'era quand a l'ha ancaminà costa valutassion?",
	'articlefeedbackv5-survey-question-whyrated' => "Për piasì, ch'an fasa savèj përchè a l'ha valutà costa pàgina ancheuj (ch'a marca tut lòn ch'a-i intra):",
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'I vorìa contribuì a la valutassion global ëd la pàgina',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'I spero che mia valutassion a peussa toché positivament ël dësvlup ëd la pàgina',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'I veui contribuì a {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Am pias condivide mia opinion',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => "I l'heu pa dàit ëd valutassion ancheuj, ma i vorìa dé un coment an sla fonsionalità",
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Àutr',
	'articlefeedbackv5-survey-question-useful' => 'Chërdës-to che le valutassion dàite a sio ùtij e ciàire?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Përchè?',
	'articlefeedbackv5-survey-question-comments' => "Ha-lo d'àutri coment?",
	'articlefeedbackv5-survey-submit' => 'Spediss',
	'articlefeedbackv5-survey-title' => "Për piasì, ch'a risponda a chèich chestion",
	'articlefeedbackv5-survey-thanks' => "Mersì d'avèj compilà ël questionari.",
	'articlefeedbackv5-survey-disclaimer' => 'Për giuté a amelioré sta funsionalità, ij sò sugeriment a peulo esse partagià anonimament con la comunità ëd Wikipedia.',
	'articlefeedbackv5-error' => "A l'é capitaje n'eror. Për piasì preuva pi tard.",
	'articlefeedbackv5-form-switch-label' => 'Valuté costa pàgina',
	'articlefeedbackv5-form-panel-title' => 'Valuté costa pàgina',
	'articlefeedbackv5-form-panel-explanation' => "Lòn ch'a l'é sossì?",
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:ArticleFeedback',
	'articlefeedbackv5-form-panel-clear' => 'Gava sta valutassion',
	'articlefeedbackv5-form-panel-expertise' => 'Mi i conòsso pròpe bin cost argoment (opsional)',
	'articlefeedbackv5-form-panel-expertise-studies' => "Mi i l'heu un tìtol dë studi universitari pertinent",
	'articlefeedbackv5-form-panel-expertise-profession' => "A l'é part ëd mia profession",
	'articlefeedbackv5-form-panel-expertise-hobby' => "A l'é na passion përsonal ancreusa",
	'articlefeedbackv5-form-panel-expertise-other' => "La sorziss ëd mia conossensa a l'é pa listà ambelessì",
	'articlefeedbackv5-form-panel-helpimprove' => 'Am piasrìa giuté a amelioré Wikipedia, mandeme un mëssagi an pòsta eletrònica (opsional)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'I-j mandroma un mëssagi ëd confirmassion. I condividroma soa adrëssa con gnun. $1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => "Régole d'arzervatëssa",
	'articlefeedbackv5-form-panel-submit' => 'Spedì le valutassion',
	'articlefeedbackv5-form-panel-pending' => "Toe valutassion a son pa anco' stàite mandà",
	'articlefeedbackv5-form-panel-success' => 'Salvà për da bin',
	'articlefeedbackv5-form-panel-expiry-title' => 'Toe valutassion a son scadùe',
	'articlefeedbackv5-form-panel-expiry-message' => "Për piasì, ch'a vàluta torna costa pagina e ch'a spedissa soa neuva valutassion.",
	'articlefeedbackv5-report-switch-label' => 'Vëdde le valutassion ëd le pàgine',
	'articlefeedbackv5-report-panel-title' => 'Valutassion ëd le pàgine',
	'articlefeedbackv5-report-panel-description' => 'Valutassion medie atuaj.',
	'articlefeedbackv5-report-empty' => 'Gnun-a valutassion',
	'articlefeedbackv5-report-ratings' => '$1 valutassion',
	'articlefeedbackv5-field-trustworthy-label' => 'Fidà',
	'articlefeedbackv5-field-trustworthy-tip' => "A pensa che costa pàgina a l'abia a basta ëd citassion e che coste citassion a rivo da 'd sorgiss fidà?",
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'A manco ëd sorgiss sigure',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Pòche sorziss sigure',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Bastansa sorgiss sigure',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Bon-e sorziss sigure',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Sorgiss sigure motobin bon-e',
	'articlefeedbackv5-field-complete-label' => 'Completa',
	'articlefeedbackv5-field-complete-tip' => "A pensa che costa pàgina a coata ij tema essensiaj dl'argoment com a dovrìa?",
	'articlefeedbackv5-field-complete-tooltip-1' => "A manca la pi part dj'anformassion",
	'articlefeedbackv5-field-complete-tooltip-2' => 'A conten quàiche anformassion',
	'articlefeedbackv5-field-complete-tooltip-3' => "A conten d'anformassion ciav, ma con dij përtus",
	'articlefeedbackv5-field-complete-tooltip-4' => "A conten la pè part dj'anformassion ciav",
	'articlefeedbackv5-field-complete-tooltip-5' => 'Covertura completa',
	'articlefeedbackv5-field-objective-label' => 'Obietiv',
	'articlefeedbackv5-field-objective-tip' => 'A pensa che costa pàgina a smon na giusta rapresentassion ëd tute le prospetive dël problema?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Pesantement parsial',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Moderatament parsial',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Minimament parsial',
	'articlefeedbackv5-field-objective-tooltip-4' => 'Gnun-a parsialità evidenta',
	'articlefeedbackv5-field-objective-tooltip-5' => "Nen d'autut parsial",
	'articlefeedbackv5-field-wellwritten-label' => 'Scrivù bin',
	'articlefeedbackv5-field-wellwritten-tip' => 'A pensa che costa pàgina a sia bin organisà e bin ëscrivùa?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Pa comprensìbil',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Malfé capì',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Ciarëssa adeguà',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Ciarëssa bon-a',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Ciarëssa ecessional',
	'articlefeedbackv5-pitch-reject' => 'Miraco pì tard',
	'articlefeedbackv5-pitch-or' => 'o',
	'articlefeedbackv5-pitch-thanks' => 'Mersì! Soe valutassion a son ëstàite salvà.',
	'articlefeedbackv5-pitch-survey-message' => 'Për piasì pija un moment për completé un curt sondagi.',
	'articlefeedbackv5-pitch-survey-accept' => 'Ancaminé ël sondagi',
	'articlefeedbackv5-pitch-join-message' => 'Veus-to creé un cont?',
	'articlefeedbackv5-pitch-join-body' => 'Un cont a-j giutrà a steje dapress a soe modìfiche, a lo amplichërà ant le discussion e lo farà part ëd la comunità.',
	'articlefeedbackv5-pitch-join-accept' => 'Crea un cont',
	'articlefeedbackv5-pitch-join-login' => 'Intra',
	'articlefeedbackv5-pitch-edit-message' => "A lo savìa ch'a peul modifiché costa pàgina?",
	'articlefeedbackv5-pitch-edit-accept' => "Modìfica st'artìcol-sì",
	'articlefeedbackv5-survey-message-success' => "Mersì d'avèj compilà ël questionari.",
	'articlefeedbackv5-survey-message-error' => "A l'é capitaje n'eror. 
Për piasì preuva torna pi tard.",
	'articleFeedbackv5-table-caption-dailyhighsandlows' => "J'àut e ij bass d'ancheuj",
	'articleFeedbackv5-table-caption-dailyhighs' => 'Pàgine con le mej valutassion: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Pàgine con le pes valutassion: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Ij pì modificà dë sta sman-a',
	'articleFeedbackv5-table-caption-recentlows' => 'Bass recent',
	'articleFeedbackv5-table-heading-page' => 'Pàgina',
	'articleFeedbackv5-table-heading-average' => 'Media',
	'articleFeedbackv5-copy-above-highlow-tables' => "Costa a l'é na funsionalità sperimental. Për piasì, ch'a fasa ij sò coment an sla [pàgina ëd discussion ëd $1]",
	'articlefeedbackv5-dashboard-bottom' => "'''Nòta''': Noi i continuëroma a sperimenté ëd manere diferente d'arpresenté j'artìcoj andrinta a coste tablò. Al present, ël tablò a conten costi artìcoj:
* Pàgine con le mej/pes valutassion: artìcoj che a l'han arseivù almanch 10 valutassion ant j'ùltime 24 ore. Le medie a son calcolà an pijand la media ëd tute le valutassion spedìe ant j'ùltime 24 ore.
* Pi bass recent: artìcoj ch'a l'han pijà lë 70% o valutassion pi basse (2 stèile o men) an tute le categorìe ant j'ùltime 24 ore. Mach j'artìcoj ch'a l'han arseivù almanch 10 valutassion ant j'ùltime 24 ore a son comprèis.",
	'articlefeedbackv5-disable-preference' => "Smon-e nen la tàula ëd valutassion ëd j'Artìcol an sle pàgine",
	'articlefeedbackv5-emailcapture-response-body' => "Cerea!

Mersì për avèj signalà sò anteresse a giuté a amelioré {{SITENAME}}.

Për piasì, ch'a treuva un moment për confirmé soa adrëssa ëd pòsta eletrònica an sgnacand an sla liura sì-sota:

$1

A peul ëdcò visité:

$2

E anserì ël còdes ëd confirmassion sì-sota:

$3

I saroma tòst an contat con la manera ëd coma a peul giuté a amelioré {{SITENAME}}.

S'a l'ha pa ancaminà chiel costa arcesta, për piasì ch'a lassa perde 's mëssagi e noi i-j manderoma pi gnente d'àutr.

Tante bele ròbe, e mersì,
L'echip ëd {{SITENAME}}",
);

/** Pashto (پښتو)
 * @author Ahmed-Najib-Biabani-Ibrahimkhel
 */
$messages['ps'] = array(
	'articlefeedbackv5-survey-answer-whyrated-other' => 'نور',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'ولې؟',
	'articlefeedbackv5-survey-submit' => 'سپارل',
	'articlefeedbackv5-form-panel-explanation' => 'دا څه دی؟',
	'articlefeedbackv5-field-complete-label' => 'بشپړ',
	'articlefeedbackv5-pitch-reject' => 'کېدای شي وروسته',
	'articlefeedbackv5-pitch-or' => 'يا',
	'articlefeedbackv5-pitch-join-accept' => 'يو ګڼون جوړول',
	'articlefeedbackv5-pitch-join-login' => 'ننوتل',
	'articlefeedbackv5-pitch-edit-accept' => 'دا مخ سمول',
	'articleFeedbackv5-table-heading-page' => 'مخ',
);

/** Portuguese (Português)
 * @author Giro720
 * @author Hamilton Abreu
 * @author Helder.wiki
 * @author Waldir
 */
$messages['pt'] = array(
	'articlefeedbackv5' => 'Painel de avaliação de artigos',
	'articlefeedbackv5-desc' => 'Avaliação de artigos',
	'articlefeedbackv5-survey-question-origin' => 'Em que página estava quando iniciou esta avaliação?',
	'articlefeedbackv5-survey-question-whyrated' => 'Diga-nos porque é que avaliou esta página hoje (marque todas as opções verdadeiras):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Queria contribuir para a avaliação global da página',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Espero que a minha avaliação afecte positivamente o desenvolvimento da página',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Queria colaborar com a {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Gosto de dar a minha opinião',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Hoje não avaliei páginas, mas queria deixar o meu comentário sobre a funcionalidade',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Outra',
	'articlefeedbackv5-survey-question-useful' => 'Acredita que as avaliações dadas são úteis e claras?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Porquê?',
	'articlefeedbackv5-survey-question-comments' => 'Tem mais comentários?',
	'articlefeedbackv5-survey-submit' => 'Enviar',
	'articlefeedbackv5-survey-title' => 'Por favor, responda a algumas perguntas',
	'articlefeedbackv5-survey-thanks' => 'Obrigado por preencher o inquérito.',
	'articlefeedbackv5-survey-disclaimer' => 'Para ajudar a melhorar esta funcionalidade, os seus comentários poderão ser anonimizados e partilhados com a comunidade da Wikipédia.',
	'articlefeedbackv5-error' => 'Ocorreu um erro. Tente novamente mais tarde, por favor.',
	'articlefeedbackv5-form-switch-label' => 'Avaliar esta página',
	'articlefeedbackv5-form-panel-title' => 'Avaliar esta página',
	'articlefeedbackv5-form-panel-explanation' => 'O que é isto?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:Avaliação de Artigos',
	'articlefeedbackv5-form-panel-clear' => 'Remover essa avaliação',
	'articlefeedbackv5-form-panel-expertise' => 'Conheço este assunto muito profundamente (opcional)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Tenho estudos relevantes do secundário ou universidade',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Faz parte dos meus conhecimentos profissionais',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'É uma das minhas paixões pessoais',
	'articlefeedbackv5-form-panel-expertise-other' => 'A fonte do meu conhecimento não está listada aqui',
	'articlefeedbackv5-form-panel-helpimprove' => 'Gostava de ajudar a melhorar a Wikipédia; enviem-me um correio electrónico (opcional)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Irá receber uma mensagem de confirmação por correio electrónico. O seu endereço de correio electrónico não será partilhado com ninguém. $1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Política de privacidade',
	'articlefeedbackv5-form-panel-submit' => 'Enviar avaliações',
	'articlefeedbackv5-form-panel-pending' => 'As suas avaliações não foram enviadas',
	'articlefeedbackv5-form-panel-success' => 'Gravado',
	'articlefeedbackv5-form-panel-expiry-title' => 'As suas avaliações expiraram',
	'articlefeedbackv5-form-panel-expiry-message' => 'Volte a avaliar esta página e envie as novas avaliações, por favor.',
	'articlefeedbackv5-report-switch-label' => 'Ver avaliações',
	'articlefeedbackv5-report-panel-title' => 'Avaliações',
	'articlefeedbackv5-report-panel-description' => 'Avaliações médias actuais.',
	'articlefeedbackv5-report-empty' => 'Não existem avaliações',
	'articlefeedbackv5-report-ratings' => '$1 avaliações',
	'articlefeedbackv5-field-trustworthy-label' => 'De confiança',
	'articlefeedbackv5-field-trustworthy-tip' => 'Considera que esta página tem citações suficientes e que essas citações provêm de fontes fidedignas?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Não tem fontes fidedignas',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Tem poucas fontes fidedignas',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Adequada em fontes fidedignas',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Bom em fontes fidedignas',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Excelente em fontes fidedignas',
	'articlefeedbackv5-field-complete-label' => 'Completa',
	'articlefeedbackv5-field-complete-tip' => 'Considera que esta página aborda os temas essenciais que deviam ser cobertos?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Falta grande parte da informação',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Contém alguma informação',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Contém a informação importante, mas com falhas',
	'articlefeedbackv5-field-complete-tooltip-4' => 'Contém a maior parte da informação importante',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Cobre o assunto de forma abrangente',
	'articlefeedbackv5-field-objective-label' => 'Objectiva',
	'articlefeedbackv5-field-objective-tip' => 'Acha que esta página representa, de forma equilibrada, todos os pontos de vista sobre o assunto?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Muito parcial',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Moderadamente parcial',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Minimamente parcial',
	'articlefeedbackv5-field-objective-tooltip-4' => 'Sem parcialidades óbvias',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Completamente imparcial',
	'articlefeedbackv5-field-wellwritten-label' => 'Bem escrita',
	'articlefeedbackv5-field-wellwritten-tip' => 'Acha que esta página está bem organizada e bem escrita?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Incompreensível',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Difícil de entender',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Adequadamente clara',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Bastante clara',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Extremamente clara',
	'articlefeedbackv5-pitch-reject' => 'Talvez mais tarde',
	'articlefeedbackv5-pitch-or' => 'ou',
	'articlefeedbackv5-pitch-thanks' => 'Obrigado! As suas avaliações foram gravadas.',
	'articlefeedbackv5-pitch-survey-message' => 'Por favor, dedique um momento para responder a um pequeno inquérito.',
	'articlefeedbackv5-pitch-survey-accept' => 'Começar inquérito',
	'articlefeedbackv5-pitch-join-message' => 'Queria criar uma conta?',
	'articlefeedbackv5-pitch-join-body' => 'Uma conta permite-lhe seguir as suas edições, participar nos debates e fazer parte da comunidade.',
	'articlefeedbackv5-pitch-join-accept' => 'Criar conta',
	'articlefeedbackv5-pitch-join-login' => 'Autenticação',
	'articlefeedbackv5-pitch-edit-message' => 'Sabia que pode editar esta página?',
	'articlefeedbackv5-pitch-edit-accept' => 'Editar esta página',
	'articlefeedbackv5-survey-message-success' => 'Obrigado por preencher o inquérito.',
	'articlefeedbackv5-survey-message-error' => 'Ocorreu um erro. 
Tente novamente mais tarde, por favor.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'As melhores e piores de hoje',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Páginas com as avaliações mais elevadas: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Páginas com as avaliações mais baixas: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Os mais alterados da semana',
	'articleFeedbackv5-table-caption-recentlows' => 'As piores mais recentes',
	'articleFeedbackv5-table-heading-page' => 'Página',
	'articleFeedbackv5-table-heading-average' => 'Média',
	'articleFeedbackv5-copy-above-highlow-tables' => 'Esta funcionalidade é experimental. Deixe os seus comentários na [$1 página de discussão], por favor.',
	'articlefeedbackv5-dashboard-bottom' => "'''Nota''': Continuaremos a experimentar diferentes critérios de selecção de artigos para estes painéis. De momento, os painéis incluem os seguintes:
* Páginas com as avaliações mais altas e mais baixas: artigos que receberam pelo menos 10 avaliações nas últimas 24 horas. As médias são calculadas pela média de todas as avaliações recebidas nas últimas 24 horas.
* Os piores mais recentes: artigos com 70% ou mais de avaliações baixas (2 estrelas ou menos) em qualquer categoria nas últimas 24 horas. Só são incluídos os artigos que receberam pelo menos 10 avaliações nas últimas 24 horas.",
	'articlefeedbackv5-disable-preference' => 'Não mostrar nas páginas o widget da avaliação de artigos',
	'articlefeedbackv5-emailcapture-response-body' => 'Olá,

Obrigado por expressar interesse em ajudar a melhorar a {{SITENAME}}.

Confirme o seu endereço de correio electrónico, clicando o link abaixo, por favor:

$1

Também pode visitar:

$2

E introduzir o seguinte código de confirmação:

$3

Em breve irá receber informações sobre como poderá ajudar a melhorar a {{SITENAME}}.

Se não iniciou este pedido, ignore esta mensagem e não voltará a ser contactado.

Cumprimentos,
A equipa da {{SITENAME}}',
);

/** Brazilian Portuguese (Português do Brasil)
 * @author 555
 * @author Giro720
 * @author MetalBrasil
 * @author Raylton P. Sousa
 */
$messages['pt-br'] = array(
	'articlefeedbackv5' => 'Painel de avaliação de artigos',
	'articlefeedbackv5-desc' => 'Avaliação do artigo (versão de testes)',
	'articlefeedbackv5-survey-question-origin' => 'Em que página você estava quando começou a responder esta pesquisa?',
	'articlefeedbackv5-survey-question-whyrated' => 'Diga-nos porque é que classificou esta página hoje, por favor (marque todas as opções as quais se aplicam):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Eu queria contribuir para a classificação global da página',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Eu espero que a minha classificação afete positivamente o desenvolvimento da página',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Eu queria colaborar com a {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Eu gosto de dar a minha opinião',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Hoje não classifiquei páginas, mas queria deixar o meu comentário sobre a funcionalidade',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Outra',
	'articlefeedbackv5-survey-question-useful' => 'Você acredita que as classificações dadas são úteis e claras?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Por quê?',
	'articlefeedbackv5-survey-question-comments' => 'Você tem mais algum comentário?',
	'articlefeedbackv5-survey-submit' => 'Enviar',
	'articlefeedbackv5-survey-title' => 'Por favor, responda a algumas perguntas',
	'articlefeedbackv5-survey-thanks' => 'Obrigado por preencher o questionário.',
	'articlefeedbackv5-survey-disclaimer' => 'Para ajudar a melhorar esse recurso, o feedback pode ser compartilhado anonimamente com a comunidade Wikipédia.',
	'articlefeedbackv5-error' => 'Ocorreu um erro. Por favor, tente novamente mais tarde.',
	'articlefeedbackv5-form-switch-label' => 'Avaliar esta página',
	'articlefeedbackv5-form-panel-title' => 'Avaliar esta página',
	'articlefeedbackv5-form-panel-explanation' => 'O que é isso?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:Avaliação de Artigos',
	'articlefeedbackv5-form-panel-clear' => 'Remover esta avaliação',
	'articlefeedbackv5-form-panel-expertise' => 'Estou muito bem informado sobre este tema (opcional)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Tenho um título universitário relacionado',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Faz parte dos meus conhecimentos profissionais',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'É uma das minhas paixões pessoais',
	'articlefeedbackv5-form-panel-expertise-other' => 'A fonte dos meus conhecimentos, não está listada aqui',
	'articlefeedbackv5-form-panel-helpimprove' => 'Eu gostaria de ajudar a melhorar a Wikipédia; enviem-me um e-mail (opcional)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Nós enviaremos a você um e-mail de confirmação. O seu endereço de e-mail não será partilhado com ninguém. $1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Política de privacidade',
	'articlefeedbackv5-form-panel-submit' => 'Enviar avaliações',
	'articlefeedbackv5-form-panel-pending' => 'As suas avaliações não foram enviadas',
	'articlefeedbackv5-form-panel-success' => 'Gravado com sucesso',
	'articlefeedbackv5-form-panel-expiry-title' => 'As suas avaliações expiraram',
	'articlefeedbackv5-form-panel-expiry-message' => 'Volte a avaliar esta página e envie as novas avaliações, por favor.',
	'articlefeedbackv5-report-switch-label' => 'Ver avaliações',
	'articlefeedbackv5-report-panel-title' => 'Avaliações',
	'articlefeedbackv5-report-panel-description' => 'Classificações médias atuais.',
	'articlefeedbackv5-report-empty' => 'Não existem avaliações',
	'articlefeedbackv5-report-ratings' => '$1 avaliações',
	'articlefeedbackv5-field-trustworthy-label' => 'Confiável',
	'articlefeedbackv5-field-trustworthy-tip' => 'Você considera que esta página tem citações suficientes e que essas citações provêm de fontes fiáveis?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Carece de fontes respeitáveis',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Poucas fontes confiáveis',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Adequada em fontes confiáveis',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Fontes de boa procedência',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Fontes muito confiáveis',
	'articlefeedbackv5-field-complete-label' => 'Completa',
	'articlefeedbackv5-field-complete-tip' => 'Você considera que esta página aborda os temas essenciais que deviam ser cobertos?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Falta grande parte da informação',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Contém alguma informação',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Contém a informação principal, mas com falhas',
	'articlefeedbackv5-field-complete-tooltip-4' => 'Contém a maior parte da informação importante',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Cobre o assunto de forma abrangente',
	'articlefeedbackv5-field-objective-label' => 'Imparcial',
	'articlefeedbackv5-field-objective-tip' => 'Você acha que esta página representa, de forma equilibrada, todos os pontos de vista sobre o assunto?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Muito parcial',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Moderadamente parcial',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Minimamente parcial',
	'articlefeedbackv5-field-objective-tooltip-4' => 'Sem parcialidades óbvias',
	'articlefeedbackv5-field-objective-tooltip-5' => 'completamente imparcial',
	'articlefeedbackv5-field-wellwritten-label' => 'Bem escrito',
	'articlefeedbackv5-field-wellwritten-tip' => 'Acha que esta página está bem organizada e bem escrita?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Imcompreensível',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Difícil de entender',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Clareza adequada',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Boa clareza',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Clareza excepcional',
	'articlefeedbackv5-pitch-reject' => 'Talvez mais tarde',
	'articlefeedbackv5-pitch-or' => 'ou',
	'articlefeedbackv5-pitch-thanks' => 'Obrigado! As suas avaliações foram salvas.',
	'articlefeedbackv5-pitch-survey-message' => 'Por favor, dedique um momento para responder a um pequeno questionário.',
	'articlefeedbackv5-pitch-survey-accept' => 'Começar questionário',
	'articlefeedbackv5-pitch-join-message' => 'Você queria criar uma conta?',
	'articlefeedbackv5-pitch-join-body' => 'Uma conta permite-lhe seguir as suas edições, participar nos debates e fazer parte da comunidade.',
	'articlefeedbackv5-pitch-join-accept' => 'Criar conta',
	'articlefeedbackv5-pitch-join-login' => 'Autenticação',
	'articlefeedbackv5-pitch-edit-message' => 'Sabia que pode editar esta página?',
	'articlefeedbackv5-pitch-edit-accept' => 'Editar esta página',
	'articlefeedbackv5-survey-message-success' => 'Obrigado por preencher o questionário.',
	'articlefeedbackv5-survey-message-error' => 'Ocorreu um erro. 
Tente novamente mais tarde, por favor.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Os melhores e piores de hoje',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Artigos com as avaliações mais elevadas: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Artigos com as avaliações mais baixas: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Os mais alterados da semana',
	'articleFeedbackv5-table-caption-recentlows' => 'Os piores mais recentes',
	'articleFeedbackv5-table-heading-page' => 'Página',
	'articleFeedbackv5-table-heading-average' => 'Média',
	'articleFeedbackv5-copy-above-highlow-tables' => 'Esta funcionalidade é experimental. Deixe os seus comentários na [$1 página de discussão], por favor.',
	'articlefeedbackv5-dashboard-bottom' => "'''Nota''': Continuaremos a experimentar diferentes critérios de selecção de artigos para estes painéis. De momento, os painéis incluem os seguintes:
* Páginas com as avaliações mais altas e mais baixas: artigos que receberam pelo menos 10 avaliações nas últimas 24 horas. As médias são calculadas pela média de todas as avaliações recebidas nas últimas 24 horas.
* Os piores mais recentes: artigos com 70% ou mais de avaliações baixas (2 estrelas ou menos) em qualquer categoria nas últimas 24 horas. Só são incluídos os artigos que receberam pelo menos 10 avaliações nas últimas 24 horas.",
	'articlefeedbackv5-disable-preference' => 'Não mostrar nas páginas o widget da avaliação de artigos',
	'articlefeedbackv5-emailcapture-response-body' => 'Olá,

Obrigado por expressar interesse em ajudar a melhorar a {{SITENAME}}.

Confirme o seu endereço de e-mail, clicando o link abaixo, por favor:

$1

Você também pode visitar:

$2

E, então, introduzir o seguinte código de confirmação:

$3

Em breve você irá receber informações sobre como você poderá ajudar a melhorar a {{SITENAME}}.

Se você não iniciou este pedido, ignore esta mensagem e não voltará a ser contactado.

Cumprimentos,
A equipe da {{SITENAME}}',
);

/** Romanian (Română)
 * @author Firilacroco
 * @author Minisarm
 * @author Stelistcristi
 * @author Strainu
 */
$messages['ro'] = array(
	'articlefeedbackv5' => 'Panou de control evaluare articol',
	'articlefeedbackv5-desc' => 'Evaluare articol',
	'articlefeedbackv5-survey-question-origin' => 'Care a fost ultima pagină vizitată înainte de a începe acest sondaj?',
	'articlefeedbackv5-survey-question-whyrated' => 'Vă rugăm să ne spuneți de ce ați evaluat această pagină astăzi (bifați tot ce se aplică):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Am vrut să contribui la evaluarea paginii',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Sper ca evaluarea mea să afecteze pozitiv dezvoltarea paginii',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Am vrut să contribui la {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Îmi place să îmi împărtășesc opinia',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Nu am furnizat evaluări astăzi, însă am dorit să ofer reacții pe viitor',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Altceva',
	'articlefeedbackv5-survey-question-useful' => 'Considerați că evaluările furnizate sunt folositoare și clare?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'De ce?',
	'articlefeedbackv5-survey-question-comments' => 'Aveți comentarii suplimentare?',
	'articlefeedbackv5-survey-submit' => 'Trimite',
	'articlefeedbackv5-survey-title' => 'Vă rugăm să răspundeți la câteva întrebări',
	'articlefeedbackv5-survey-thanks' => 'Vă mulțumim pentru completarea sondajului.',
	'articlefeedbackv5-survey-disclaimer' => 'Prin trimitere, sunteți de acord cu acești $1.',
	'articlefeedbackv5-survey-disclaimerlink' => 'termeni',
	'articlefeedbackv5-error' => 'A apărut o eroare. Vă rugăm să reîncercați mai târziu.',
	'articlefeedbackv5-form-switch-label' => 'Evaluează această pagină',
	'articlefeedbackv5-form-panel-title' => 'Evaluare pagină',
	'articlefeedbackv5-form-panel-explanation' => 'Ce este acesta?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:Evaluare_articol',
	'articlefeedbackv5-form-panel-clear' => 'Elimină această evaluare',
	'articlefeedbackv5-form-panel-expertise' => 'Dețin cunoștințe solide despre acest subiect (opțional)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Am o diplomă relevantă la nivel de colegiu/universitate',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Este parte din profesia mea',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Este o pasiune personală puternică',
	'articlefeedbackv5-form-panel-expertise-other' => 'Nivelul cunoștințelor mele nu se află în această listă',
	'articlefeedbackv5-form-panel-helpimprove' => 'Aș dori să contribui la îmbunătățirea Wikipediei; trimite-mi un e-mail (opțional)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Vă vom trimite un e-mail de confirmare. Nu vom face cunoscută adresa dumneavoastră de e-mail altor părți, conform $1.',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'declarației noastre de confidențialitate privind feedback-ul',
	'articlefeedbackv5-form-panel-submit' => 'Trimite evaluările',
	'articlefeedbackv5-form-panel-pending' => 'Evaluările dumneavoastră nu au fost încă trimise',
	'articlefeedbackv5-form-panel-success' => 'Salvat cu succes',
	'articlefeedbackv5-form-panel-expiry-title' => 'Evaluările dumneavoastră au expirat',
	'articlefeedbackv5-form-panel-expiry-message' => 'Vă rugăm să reevaluați această pagină și să trimiteți noi clasificări.',
	'articlefeedbackv5-report-switch-label' => 'Vezi evaluările paginii',
	'articlefeedbackv5-report-panel-title' => 'Evaluări pagină',
	'articlefeedbackv5-report-panel-description' => 'Media evaluărilor actuale.',
	'articlefeedbackv5-report-empty' => 'Nu există evaluări',
	'articlefeedbackv5-report-ratings' => '{{PLURAL:$1|evaluare|$1 evaluări}}',
	'articlefeedbackv5-field-trustworthy-label' => 'De încredere',
	'articlefeedbackv5-field-trustworthy-tip' => 'Credeți că pagina de față conține suficiente referințe și că acestea provin din surse de încredere?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Îi lipsesc sursele respectabile',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Doar câteva surse respectabile',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Surse respectabile adecvate',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Surse respectabile bune',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Surse respectabile foarte bune',
	'articlefeedbackv5-field-complete-label' => 'Completă',
	'articlefeedbackv5-field-complete-tip' => 'Credeți că pagina de față acoperă subiectul într-o manieră satisfăcătoare?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Îi lipsește mare parte din informație',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Conține câteva informații',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Conține informații esențiale, dar cu lipsuri',
	'articlefeedbackv5-field-complete-tooltip-4' => 'Conține mare parte din informațiile esențiale',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Acoperă foarte bine subiectul',
	'articlefeedbackv5-field-objective-label' => 'Obiectivă',
	'articlefeedbackv5-field-objective-tip' => 'Credeți că pagina de față tratează echitabil toate perspectivele și opiniile cu privire la subiect?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Puternic părtinitoare',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Complet imparțial',
	'articlefeedbackv5-field-wellwritten-label' => 'Bine scrisă',
	'articlefeedbackv5-field-wellwritten-tip' => 'Credeți că pagina de față este bine organizată și bine redactată?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'De neînțeles',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Dificil de înțeles',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Claritate adecvată',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Claritate bună',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Claritate excepțională',
	'articlefeedbackv5-pitch-reject' => 'Poate mai târziu',
	'articlefeedbackv5-pitch-or' => 'sau',
	'articlefeedbackv5-pitch-thanks' => 'Vă mulțumim! Evaluările dumneavoastră au fost contorizate.',
	'articlefeedbackv5-pitch-survey-message' => 'Vă rugăm să acordați câteva momente completării unui scurt chestionar.',
	'articlefeedbackv5-pitch-survey-accept' => 'Pornește sondajul',
	'articlefeedbackv5-pitch-join-message' => 'Ați dori să vă creați un cont?',
	'articlefeedbackv5-pitch-join-body' => 'Un cont de utilizator v-ar ajuta să țineți evidența contribuțiile dumneavoastră, să luați parte la discuții și să faceți parte din comunitate.',
	'articlefeedbackv5-pitch-join-accept' => 'Creează un cont',
	'articlefeedbackv5-pitch-join-login' => 'Autentificare',
	'articlefeedbackv5-pitch-edit-message' => 'Știați că puteți modifica această pagină?',
	'articlefeedbackv5-pitch-edit-accept' => 'Modifică această pagină',
	'articlefeedbackv5-survey-message-success' => 'Vă mulțumim că ați completat chestionarul.',
	'articlefeedbackv5-survey-message-error' => 'A apărut o eroare.
Vă rugăm să reîncercați mai târziu.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Cele mai bune și cele mai slabe evaluări de astăzi',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Paginile cu cele mai bune evaluări: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Paginile cu cele mai slabe evaluări: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Cea mai modificată din această săptămână',
	'articleFeedbackv5-table-caption-recentlows' => 'Minime recente',
	'articleFeedbackv5-table-heading-page' => 'Pagina',
	'articleFeedbackv5-table-heading-average' => 'Medie',
	'articleFeedbackv5-copy-above-highlow-tables' => 'Aceasta este o unealtă experimentală. Vă rugăm să ne oferiți reacții pe [$1 pagina de discuție].',
	'articlefeedbackv5-dashboard-bottom' => "'''Notă''': Vom continua să experimentăm diferite moduri de reprezentare ale articolului în aceste tablouri de bord. În prezent conțin articolele următoare:
* Pagini cu cel mai mare și cel mai mic calificativ: articole care au fost evaluate de cel puțin 10 ori în ultimele 24 de ore. Mediile sunt calculate luând în considerare toate evaluările trimise în ultimele 24 de ore.
* Recent scăzute: articole care au primit cel puțin 70% calificative slabe (2 stele sau mai puțin) în orice categorie în ultimele 24 de ore. Numai articolele care au primit cel puțin 10 evaluări în ultimele 24 de ore sunt incluse.",
	'articlefeedbackv5-disable-preference' => 'Nu afișa widgetul pentru evaluarea articolelor în cadrul paginilor',
	'articlefeedbackv5-emailcapture-response-body' => 'Bună ziua!

Vă mulțumim pentru interesul arătat față de procesul de îmbunătățire al proiectului {{SITENAME}}.

Vă rugăm să vă confirmați adresa de e-mail accesând legătura de mai jos: 

$1

Ați putea vizita de asemenea și:

$2

Și să introduceți următorul cod de confirmare:

$3

Vă vom contacta curând în legătură cu modul în care vă puteți implica în procesul de îmbunătățire al proiectului {{SITENAME}}.

Dacă nu sunteți dumneavoastră persoana care a cerut aceste indicații, vă rugăm să ignorați acest e-mail; nu vă vom mai trimite alte mesaje.

Vă mulțumim și vă urăm toate cele bune,
Echipa proiectului {{SITENAME}}',
);

/** Tarandíne (Tarandíne)
 * @author Joetaras
 * @author Reder
 */
$messages['roa-tara'] = array(
	'articlefeedbackv5' => "Cruscotte d'a valutazione de le vôsce",
	'articlefeedbackv5-desc' => 'Artichele de valutazione (versiune guidate)',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => "Ije vogghie condrebbuische a 'u pundegge totale d'a pàgene",
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => "Ije amm'a condrebbuì a {{SITENAME}}",
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => "Me chiace dìcere 'u penziere mèje",
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Otre',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Purcé?',
	'articlefeedbackv5-survey-question-comments' => 'Tìne otre commende?',
	'articlefeedbackv5-survey-submit' => 'Conferme',
	'articlefeedbackv5-survey-title' => 'Se preghe de responnere a quacche dumanne',
	'articlefeedbackv5-survey-thanks' => "Grazzie pè avè combilate 'u sondagge.",
	'articlefeedbackv5-error' => "'N'errore s'a verificate. Pe piacere pruève arrete.",
	'articlefeedbackv5-form-switch-label' => 'Valute sta pàgene',
	'articlefeedbackv5-form-panel-title' => 'Valute sta pàgene',
	'articlefeedbackv5-form-panel-explanation' => 'Ce jè quiste?',
	'articlefeedbackv5-form-panel-explanation-link' => "Project:FeedbackD'aVôsce",
	'articlefeedbackv5-form-panel-clear' => 'Live stu pundegge',
	'articlefeedbackv5-form-panel-expertise-studies' => "Tènghe 'nu grade de scole/università 'mbortande",
	'articlefeedbackv5-form-panel-expertise-profession' => "Jè parte d'a professiona meje",
	'articlefeedbackv5-form-panel-expertise-hobby' => "Queste jè 'na passiona profonda meje",
	'articlefeedbackv5-form-panel-helpimprove-privacy' => "Regole p'a privacy",
	'articlefeedbackv5-form-panel-submit' => 'Conferme le pundegge',
	'articlefeedbackv5-form-panel-pending' => "'U vote tune non g'ha state confermate",
	'articlefeedbackv5-form-panel-success' => 'Reggistrate cu successe',
	'articlefeedbackv5-form-panel-expiry-title' => 'Le pundegge tune onne scadute',
	'articlefeedbackv5-report-switch-label' => "Vide 'u pundegge d'a pàgene",
	'articlefeedbackv5-report-panel-title' => "Pundegge d'a pàgene",
	'articlefeedbackv5-report-panel-description' => 'Pundegge medie corrende.',
	'articlefeedbackv5-report-empty' => 'Nisciune pundegge',
	'articlefeedbackv5-report-ratings' => '$1 pundegge',
	'articlefeedbackv5-field-trustworthy-label' => 'Avveramende affidabbele',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => "Assenze de sorgende cu 'na reputazione",
	'articlefeedbackv5-field-trustworthy-tooltip-2' => "Sorgende cu 'na reputazione so picche",
	'articlefeedbackv5-field-trustworthy-tooltip-3' => "Sorgende cu 'na reputazione sonde adeguate",
	'articlefeedbackv5-field-trustworthy-tooltip-4' => "Bbuène sorgende cu 'na reputazione",
	'articlefeedbackv5-field-trustworthy-tooltip-5' => "Sorgende cu 'na reputazione granne granne",
	'articlefeedbackv5-field-complete-label' => 'Comblete',
	'articlefeedbackv5-field-complete-tooltip-1' => "Mangante assaije 'mbormaziune",
	'articlefeedbackv5-field-complete-tooltip-2' => "Tène quacche 'mbormazione",
	'articlefeedbackv5-field-complete-tooltip-3' => "Tène 'mbormaziune chiave, ma cu le bochere",
	'articlefeedbackv5-field-complete-tooltip-4' => "Tène assaije 'mbormaziune chiave",
	'articlefeedbackv5-field-complete-tooltip-5' => 'Coperture combrensive',
	'articlefeedbackv5-field-objective-label' => 'Obbiettive',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Assaije de parte',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Moderatamende de parte',
	'articlefeedbackv5-field-objective-tooltip-3' => "'Nu picche de parte",
	'articlefeedbackv5-field-objective-tooltip-4' => 'Quase quase obbiettive',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Combletamende obbiettive',
	'articlefeedbackv5-field-wellwritten-label' => 'Scritte bbuène',
	'articlefeedbackv5-field-wellwritten-tip' => 'Vuè ca sta pàgene jè organizzata bbuène e scritta bbone?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Incombrensibbele',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Difficele da capìe',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Chiarezze adeguate',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Chiarezza bbone',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Eccezzionalmende chiare',
	'articlefeedbackv5-pitch-reject' => 'Forse cchiù tarde',
	'articlefeedbackv5-pitch-or' => 'o',
	'articlefeedbackv5-pitch-thanks' => "Grazie! 'U vote tune ha state reggistrate.",
	'articlefeedbackv5-pitch-survey-message' => "Pe piacere pigghiate 'nu mumende pe combletà 'u sondagge curte.",
	'articlefeedbackv5-pitch-survey-accept' => "Accuminze 'u sondagge",
	'articlefeedbackv5-pitch-join-message' => "Vu è ccu ccreje 'nu cunde?",
	'articlefeedbackv5-pitch-join-accept' => "Ccreje 'nu cunde utende",
	'articlefeedbackv5-pitch-join-login' => 'Tràse',
	'articlefeedbackv5-pitch-edit-accept' => 'Cange sta pàgene',
	'articlefeedbackv5-survey-message-success' => "Grazzie pè avè combilate 'u sondagge.",
	'articlefeedbackv5-survey-message-error' => "'N'errore s'a verificate.
Pe piacere pruève arrete.",
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Le megghie e le pesce de osce',
	'articleFeedbackv5-table-caption-dailyhighs' => "Pàggene cu 'u pundegge cchiù ierte: $1",
	'articleFeedbackv5-table-caption-dailylows' => "Pàggene cu 'u pundegge cchiù vasce: $1",
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Le cangiaminde maggiore de sta sumàne',
	'articleFeedbackv5-table-caption-recentlows' => 'Urteme discese',
	'articleFeedbackv5-table-heading-page' => 'Pàgene',
	'articleFeedbackv5-table-heading-average' => 'Medie',
	'articleFeedbackv5-copy-above-highlow-tables' => "Quiste jè 'na caratteristeche sperimendale. Pe piacere vide ce manne 'nu feedback sus a [$1 pàgene de le 'ngazzaminde].",
);

/** Russian (Русский)
 * @author AlexSm
 * @author Assele
 * @author Catrope
 * @author Dim Grits
 * @author MaxSem
 * @author Александр Сигачёв
 * @author Сrower
 */
$messages['ru'] = array(
	'articlefeedbackv5' => 'Панель оценок статьи',
	'articlefeedbackv5-desc' => 'Оценка статьи (экспериментальный вариант)',
	'articlefeedbackv5-survey-question-origin' => 'На какой странице вы находились, когда начали этот опрос?',
	'articlefeedbackv5-survey-question-whyrated' => 'Пожалуйста, дайте нам знать, почему вы сегодня дали оценку этой странице (отметьте все подходящие варианты):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Я хотел повлиять на итоговый рейтинг этой страницы',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Я надеюсь, что моя оценка положительно повлияет на развитие этой страницы',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Я хочу содействовать развитию {{GRAMMAR:genitive|{{SITENAME}}}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Мне нравится делиться своим мнением',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Я не поставил сегодня оценку, но хочу оставить отзыв о данной функции',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Иное',
	'articlefeedbackv5-survey-question-useful' => 'Считаете ли вы, что проставленные оценки являются полезными и понятными?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Почему?',
	'articlefeedbackv5-survey-question-comments' => 'Есть ли у вас какие-либо дополнительные замечания?',
	'articlefeedbackv5-survey-submit' => 'Отправить',
	'articlefeedbackv5-survey-title' => 'Пожалуйста, ответьте на несколько вопросов',
	'articlefeedbackv5-survey-thanks' => 'Спасибо за участие в опросе.',
	'articlefeedbackv5-survey-disclaimer' => 'В целях улучшения этой функции, ваш отзыв может быть анонимно передан сообществу Википедии.',
	'articlefeedbackv5-error' => 'Произошла ошибка. Пожалуйста, повторите попытку позже.',
	'articlefeedbackv5-form-switch-label' => 'Оцените эту страницу',
	'articlefeedbackv5-form-panel-title' => 'Оцените эту страницу',
	'articlefeedbackv5-form-panel-explanation' => 'Что это?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:ArticleFeedback',
	'articlefeedbackv5-form-panel-clear' => 'Удалить эту оценку',
	'articlefeedbackv5-form-panel-expertise' => 'Я хорошо знаком с этой темой (опционально)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'По данной теме я получил образование в колледже / университете',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Это часть моей профессии',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Это моё большое личное увлечение',
	'articlefeedbackv5-form-panel-expertise-other' => 'Источник моих знаний здесь не указан',
	'articlefeedbackv5-form-panel-helpimprove' => 'Я хотел бы помочь улучшить Википедию, отправьте мне письмо (опционально)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Мы отправим вам письмо с подтверждением. Мы не передадим ваш адрес кому-либо ещё. $1',
	'articlefeedbackv5-form-panel-helpimprove-email-placeholder' => 'email@example.org',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Политика конфиденциальности',
	'articlefeedbackv5-form-panel-submit' => 'Отправить оценку',
	'articlefeedbackv5-form-panel-pending' => 'Ваши оценки ещё не были отправлены',
	'articlefeedbackv5-form-panel-success' => 'Информация сохранена',
	'articlefeedbackv5-form-panel-expiry-title' => 'Ваши оценки устарели',
	'articlefeedbackv5-form-panel-expiry-message' => 'Пожалуйста, пересмотрите эту страницу и укажите новые оценки.',
	'articlefeedbackv5-report-switch-label' => 'Показать оценки страницы',
	'articlefeedbackv5-report-panel-title' => 'Оценки страницы',
	'articlefeedbackv5-report-panel-description' => 'Текущие средние оценки.',
	'articlefeedbackv5-report-empty' => 'Нет оценок',
	'articlefeedbackv5-report-ratings' => 'оценок: $1',
	'articlefeedbackv5-field-trustworthy-label' => 'Достоверность',
	'articlefeedbackv5-field-trustworthy-tip' => 'Считаете ли вы, что на этой странице достаточно ссылок на источники, что источники являются достоверными?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Нет авторитетных источников',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Мало авторитетных источников',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Адекватные авторитетные источники',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Хорошие авторитетные источники',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Отличные авторитетные источники',
	'articlefeedbackv5-field-complete-label' => 'Полнота',
	'articlefeedbackv5-field-complete-tip' => 'Считаете ли вы, что эта страница в достаточной мере раскрывает основные вопросы темы?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Отсутствует большая часть сведений',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Содержит некоторые сведения',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Содержит основные сведения, есть пропуски',
	'articlefeedbackv5-field-complete-tooltip-4' => 'Содержит основные сведения',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Всеобъемлющий охват',
	'articlefeedbackv5-field-objective-label' => 'Беспристрастность',
	'articlefeedbackv5-field-objective-tip' => 'Считаете ли вы, что эта страница объективно отражает все точки зрения по данной теме?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Сильно предвзятая',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Умеренно предвзятая',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Минимально предвзятая',
	'articlefeedbackv5-field-objective-tooltip-4' => 'Нет очевидной предвзятости',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Полностью беспристрастная',
	'articlefeedbackv5-field-wellwritten-label' => 'Стиль изложения',
	'articlefeedbackv5-field-wellwritten-tip' => 'Считаете ли вы, что эта страница хорошо организована и хорошо написана?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Непонятная',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Сложная для понимания',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Нормальная ясность изложения',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Хорошая ясность изложения',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Исключительная ясность изложения',
	'articlefeedbackv5-pitch-reject' => 'Может быть, позже',
	'articlefeedbackv5-pitch-or' => 'или',
	'articlefeedbackv5-pitch-thanks' => 'Спасибо! Ваши оценки сохранены.',
	'articlefeedbackv5-pitch-survey-message' => 'Пожалуйста, найдите время для выполнения краткой оценки.',
	'articlefeedbackv5-pitch-survey-accept' => 'Начать опрос',
	'articlefeedbackv5-pitch-join-message' => 'Вы хотели бы создать учётную запись?',
	'articlefeedbackv5-pitch-join-body' => 'Учётная запись поможет вам отслеживать изменения, участвовать в обсуждениях, быть частью сообщества.',
	'articlefeedbackv5-pitch-join-accept' => 'Создать учётную запись',
	'articlefeedbackv5-pitch-join-login' => 'Представиться',
	'articlefeedbackv5-pitch-edit-message' => 'Знаете ли вы, что эту страницу можно редактировать?',
	'articlefeedbackv5-pitch-edit-accept' => 'Править эту страницу',
	'articlefeedbackv5-survey-message-success' => 'Спасибо за участие в опросе.',
	'articlefeedbackv5-survey-message-error' => 'Произошла ошибка. 
Пожалуйста, повторите попытку позже.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Сегодняшние взлёты и падения',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Статьи с наивысшими оценками: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Статьи с самыми низкими оценками: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Наиболее изменившиеся на этой неделе',
	'articleFeedbackv5-table-caption-recentlows' => 'Недавние падения',
	'articleFeedbackv5-table-heading-page' => 'Страница',
	'articleFeedbackv5-table-heading-average' => 'Среднее',
	'articlefeedbackv5-table-noratings' => '-',
	'articleFeedbackv5-copy-above-highlow-tables' => 'Это экспериментальная возможность. Пожалуйста, оставьте отзыв на [$1 странице обсуждения].',
	'articlefeedbackv5-dashboard-bottom' => "'''Примечание'''. Мы будем продолжать экспериментировать с различными способами наполнения этой панели. Сейчас на неё попадают следующие статьи:
* Страницы с самым высокими/низкими оценками: статьи, получившие не менее 10 оценок за последние 24 часа. Средние значения рассчитываются после обработки всех оценок за последние 24 часа.
* Последние минимумы: статьи, получившие 70% и ниже (2 звезды и ниже) в любой из категорий за последние 24 часа. Учитываются только статьи, получившие не менее 10 оценок за последние 24 часа.",
	'articlefeedbackv5-disable-preference' => 'Не показывать на страницах виджет обратной связи',
	'articlefeedbackv5-emailcapture-response-body' => 'Здравствуйте!

Спасибо за интерес к улучшению проекта {{SITENAME}}.

Пожалуйста, потратьте несколько секунд, чтобы подтвердить адрес электронной почты, нажав на ссылку ниже:

$1

Вы можете также посетить:

$2

И ввести следующий код подтверждения:

$3

Вскоре мы сообщим вам, как можно помочь в улучшении проекта {{SITENAME}}.

Если вы не отправляли подобного запроса, пожалуйста, проигнорируйте это сообщение, и мы больше не будем вас тревожить.

С наилучшими пожеланиями и благодарностью
Команда проекта {{SITENAME}}',
);

/** Rusyn (Русиньскый)
 * @author Dim Grits
 * @author Gazeb
 */
$messages['rue'] = array(
	'articlefeedbackv5' => 'Панель оцінок статї',
	'articlefeedbackv5-desc' => 'Оцінка статї (експеріменталный варіант)',
	'articlefeedbackv5-survey-question-origin' => 'З котрой сторінкы сьте {{gender:|пришов|пришла|пришли}} на тото вызвідуваня?',
	'articlefeedbackv5-survey-question-whyrated' => 'Чом сьте днесь оцінили тоту сторінку (зачаркните вшыткы платны можности):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Хотїв єм овпливнити цалкову оцінку сторінкы',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Сподїваю ся, же мій рейтінґ буде позітівно впливати на вывой сторінкы',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Хотїв єм помочі {{grammar:3sg|{{SITENAME}}}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Люблю здїляти свій назор',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Днесь єм не оцінёвав, але хотїв єм додати свій назор на тоту функцію',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Інше',
	'articlefeedbackv5-survey-question-useful' => 'Думаєте собі, же доданы оцінкы суть хосновны і зрозумітельны?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Чом?',
	'articlefeedbackv5-survey-question-comments' => 'Маєте даякы додаточны коментарї?',
	'articlefeedbackv5-survey-submit' => 'Одослати',
	'articlefeedbackv5-survey-title' => 'Просиме, одповіджте на пару вопросів',
	'articlefeedbackv5-survey-thanks' => 'Дякуєме за выповнїня звідованя.',
	'articlefeedbackv5-survey-disclaimer' => 'Жебы вылїпшыти тоту функцію, ваша одозва може быти анонімно здїляна з Вікіпедія комунітов.',
	'articlefeedbackv5-error' => 'Дішло ку хыбі. Просиме, попробуйте пізнїше.',
	'articlefeedbackv5-form-switch-label' => 'Оцїнити тоту сторінку',
	'articlefeedbackv5-form-panel-title' => 'Оцїньте тоту сторінку',
	'articlefeedbackv5-form-panel-explanation' => 'Што є тото?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:Годночіня статей',
	'articlefeedbackv5-form-panel-clear' => 'Одстранити годночіня',
	'articlefeedbackv5-form-panel-expertise' => 'Мам россягле знаня о тій темі (волительне)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Мам прислушный высокошкольскый тітул',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Іде о часть моёй професії',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Є то моє велике гобі',
	'articlefeedbackv5-form-panel-expertise-other' => 'Жрідло мого знаня гев не є зазначене',
	'articlefeedbackv5-form-panel-helpimprove' => 'Хотїв бы єм помочі вылїпшыти Вікіпедію, пошлите мі імейл (волительне)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Пошлеме вам потверджовачій імейл. Вашу імейлову адресу никому не даме. $1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Політіка охраны особных дат',
	'articlefeedbackv5-form-panel-submit' => 'Одослати оцїнку',
	'articlefeedbackv5-form-panel-pending' => 'Ваша оцїнка іщі не была одослана',
	'articlefeedbackv5-form-panel-success' => 'Успішно уложене',
	'articlefeedbackv5-form-panel-expiry-title' => 'Вашы оцїнкы застарїли',
	'articlefeedbackv5-form-panel-expiry-message' => 'Просиме оцїньте сторінку знова і зазначте новый рейтінґ.',
	'articlefeedbackv5-report-switch-label' => 'Указати рейтінґ сторінкы',
	'articlefeedbackv5-report-panel-title' => 'Рейтінґ сторінкы',
	'articlefeedbackv5-report-panel-description' => 'Актуалны середнї рейтінґы.',
	'articlefeedbackv5-report-empty' => 'Без оцїнкы',
	'articlefeedbackv5-report-ratings' => '$1 оцїнок',
	'articlefeedbackv5-field-trustworthy-label' => 'Достовірность',
	'articlefeedbackv5-field-trustworthy-tip' => 'Маєте чутя, же тота сторінка достаточно одказує на жрідла і хоснованы жрідла суть способны довірованя?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Хыбують авторітны жрідла',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Недостаток достовірных жрідел',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Адекватны авторітны жрідла',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Дорбы авторітны жрідла',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Чудовы авторітны жрідла',
	'articlefeedbackv5-field-complete-label' => 'Комплетность',
	'articlefeedbackv5-field-complete-tip' => 'Маєте чутя, же тота сторінка покрывать вшыткы важны части темы?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Хыбує велика часть інформацій',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Обсягує даяку інформацію',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Обсягує ключову інформацію, але з недостатками',
	'articlefeedbackv5-field-complete-tooltip-4' => 'Обсягує найключовішу інформацію',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Комплексне покрытя темы',
	'articlefeedbackv5-field-objective-label' => 'Обєктівіта',
	'articlefeedbackv5-field-objective-tip' => 'Маєте чутя, же тота сторінка справедливо покрывать вшыткы погляды на даны темы?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Силно фалошне',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Мірно фалошне',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Маленько фалошне',
	'articlefeedbackv5-field-objective-tooltip-4' => 'Без ясных фалешных інформацій',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Абсолутно непредвзяте',
	'articlefeedbackv5-field-wellwritten-label' => 'Написане добрым штілом',
	'articlefeedbackv5-field-wellwritten-tip' => 'Маєте чутя, же тота сторінка є правилно орґанізована о добрї написана?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Незрозуміле',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Тяжко порозуміти',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Достаточно зрозуміле',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Добрї зрозуміле',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Вынятково легко ся чітать',
	'articlefeedbackv5-pitch-reject' => 'Може пізнїше',
	'articlefeedbackv5-pitch-or' => 'або',
	'articlefeedbackv5-pitch-thanks' => 'Дякуєме! Вашы оцїнкы были уложены.',
	'articlefeedbackv5-pitch-survey-message' => 'Просиме, найдьте собі минутку про выповнїня куртого звідованя.',
	'articlefeedbackv5-pitch-survey-accept' => 'Почати звідованя',
	'articlefeedbackv5-pitch-join-message' => 'Хотїли бы сьте створити конто хоснователя?',
	'articlefeedbackv5-pitch-join-body' => 'Конто вам уможнить слїдовати вашы едітованя, брати участь на діскузіях і стати ся частёв комуніты.',
	'articlefeedbackv5-pitch-join-accept' => 'Вытворити конто',
	'articlefeedbackv5-pitch-join-login' => 'Приголосити ся',
	'articlefeedbackv5-pitch-edit-message' => 'Ці вы знали, же можете управити тоту сторінку?',
	'articlefeedbackv5-pitch-edit-accept' => 'Едітовату тоту сторінку',
	'articlefeedbackv5-survey-message-success' => 'Дякуєме за выповнїня звідованя.',
	'articlefeedbackv5-survey-message-error' => 'Дішло ку хыбі. 
Просиме, попробуйте пізнїше.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Днешнї максіма і мініма',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Сторінкы з найвысшыма оцїнками: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Сторінкы з найнизшыма оцїнками: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Найвекшы зміны того тыждня',
	'articleFeedbackv5-table-caption-recentlows' => 'Недавны мініма',
	'articleFeedbackv5-table-heading-page' => 'Сторінка',
	'articleFeedbackv5-table-heading-average' => 'Середнє',
	'articleFeedbackv5-copy-above-highlow-tables' => 'Тото є експеріментална функція. Дайте знати ваш назор на [$1 діскузній сторінцї].',
	'articlefeedbackv5-dashboard-bottom' => "'''Позначка''': Будеме продовжовати експеріментованя з різныма способами наповнїня статей на тім панелї. Теперь панел обсягує наступны статї:
* Статї з найвысшым/найнизшым рейтінґом: Статї, котры обтримали холем 10 рейтінґів почас остатнїх 24 годин. Середня годнота є рахована по спрацованю вшыткых рейтінґів за остатнїх 24 годин.
* Чінны оутсайдеры: Статї, котры обтримали ниже 70% і ниже (2 звіздочкы і ниже) оцїнкы в будь-якій катеґорії за остатнї 24 годины. Рахують ся лем статї, котры обтримали холем 10 оцїнок за остатнїх 24 годин.",
	'articlefeedbackv5-disable-preference' => 'Не указовати на статях компоненту про оцїнку сторінок',
	'articlefeedbackv5-emailcapture-response-body' => 'Добрый день!

Дякуєме за выядрїня інтересу помочі вылїпшыти {{grammar:4sg|{{SITENAME}}}}.

Просиме, найдьте собі минутку на потверджіня вашой імейловой адресы кликнутём на наступный одказ:

$1

Можете тыж навщівити:

$2

І задати наступный код потверджіня:

$3

Дораз ся вам озвеме з інформаціями, як можете помочі {{grammar:4sg|{{SITENAME}}}} вылїпшыти.

Кідь тота жадость не походить од вас, іґноруйте тот імейл, ніч веце вам засылати не будеме.

Дякуєме і поздравуєме
тім {{grammar:2sg|{{SITENAME}}}}',
);

/** Sakha (Саха тыла)
 * @author HalanTul
 */
$messages['sah'] = array(
	'articlefeedbackv5' => 'Ыстатыйаны сыаналааһын хаптаһына',
	'articlefeedbackv5-desc' => 'Ыстатыйаны сыаналааһын (тургутуллар барыла)',
	'articlefeedbackv5-survey-question-origin' => 'Бу ыйытыгы саҕалыыргар ханнык сирэйи көрө олорбуккунуй?',
	'articlefeedbackv5-survey-question-whyrated' => 'Бука диэн эт эрэ, тоҕо бүгүн бу сирэйи сыаналаатыҥ (туох баар сөп түбэһэр барыллары бэлиэтээ):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Бу сирэй түмүк рейтинин уларытаары',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Сыанам бу сирэй тупсарыгар көмөлөһүө диэн санааттан',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => '{{GRAMMAR:genitive|{{SITENAME}}}} сайдыытыгар көмөлөһүөхпүн баҕарабын',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Бэйэм санаабын дьоҥҥо биллэрэрбин сөбүлүүбүн',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Бүгүн сыана бирбэтим, ол эрээри бу функция туһунан суруйуохпун баҕарабын',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Атын',
	'articlefeedbackv5-survey-question-useful' => 'Баар сыанабыллар туһаланы аҕалыахтара дуо, өйдөнөллөр дуо?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Тоҕо?',
	'articlefeedbackv5-survey-question-comments' => 'Ханнык эмит эбии этиилээххин дуо?',
	'articlefeedbackv5-survey-submit' => 'Ыытарга',
	'articlefeedbackv5-survey-title' => 'Бука диэн аҕыйах ыйытыыга хоруйдаа эрэ',
	'articlefeedbackv5-survey-thanks' => 'Ыйытыыларга хоруйдаабыккар махтанабыт.',
	'articlefeedbackv5-error' => 'Туох эрэ алҕас таҕыста. Хойутуу боруобалаар.',
	'articlefeedbackv5-form-switch-label' => 'Бу сирэйи сыаналаа',
	'articlefeedbackv5-form-panel-title' => 'Бу сирэйи сыаналаа',
	'articlefeedbackv5-form-panel-explanation' => 'Бу тугуй?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:ArticleFeedback',
	'articlefeedbackv5-form-panel-clear' => 'Бу сыананы сот',
	'articlefeedbackv5-form-panel-expertise' => 'Бу тиэмэни бэркэ билэбин (толоруу булгуччута суох)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Бу тиэмэни колледжка/университекка үөрэппитим',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Идэм сорҕото',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Мин дьарыктанар үлүһүйүүм, сүрүн дьулҕаным',
	'articlefeedbackv5-form-panel-expertise-other' => 'Туох сыһыаннааҕым туһунан манна ыйыллыбатах',
	'articlefeedbackv5-form-panel-helpimprove' => 'Бикипиэдьийэни тупсарарга көмө буолуом этэ, сурукта ыытыҥ (толорор булгуччута суох)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Бигэргэтэр сурук ыытыахпыт. Аадырыскын кимиэхэ да биэриэхпит суоҕа. $1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Кистээһин сиэрэ',
	'articlefeedbackv5-form-panel-submit' => 'Сыанабылы ыытыы',
	'articlefeedbackv5-form-panel-pending' => 'Эн сыанабылыҥ өссө да ыытылла илик',
	'articlefeedbackv5-form-panel-success' => 'Бигэргэтилиннэ',
	'articlefeedbackv5-form-panel-expiry-title' => 'Сыанабылыҥ эргэрбит',
	'articlefeedbackv5-form-panel-expiry-message' => 'Бука диэн бу сирэйи хат көр уонна саҥа сыаната быс.',
	'articlefeedbackv5-report-switch-label' => 'Сирэй сыанабылларын көрдөр',
	'articlefeedbackv5-report-panel-title' => 'Сирэйи сыаналааһын',
	'articlefeedbackv5-report-panel-description' => 'Билиҥҥи орто сыанабыллар.',
	'articlefeedbackv5-report-empty' => 'Сыанабыл суох',
	'articlefeedbackv5-report-ratings' => '$1 сыанабыл',
	'articlefeedbackv5-field-trustworthy-label' => 'Итэҕэтиилээҕэ',
	'articlefeedbackv5-field-complete-label' => 'Толорута',
	'articlefeedbackv5-field-complete-tip' => 'Бу сирэй тиэмэ сүрүн ис хоһоонун арыйар дуо?',
	'articlefeedbackv5-field-objective-label' => 'Тутулуга суоҕа',
	'articlefeedbackv5-field-objective-tip' => 'Бу сирэй араас көрүүлэри тэҥҥэ, тугу да күөмчүлээбэккэ көрдөрөр дии саныыгын дуо?',
	'articlefeedbackv5-field-wellwritten-label' => 'Суруйуу истиилэ',
	'articlefeedbackv5-field-wellwritten-tip' => 'Бу сирэй бэркэ сааһыланан суруллубут дии саныыгын дуо?',
	'articlefeedbackv5-pitch-reject' => 'Баҕар кэлин',
	'articlefeedbackv5-pitch-or' => 'эбэтэр',
	'articlefeedbackv5-pitch-thanks' => 'Махтал! Эн сыанабылыҥ бигэргэтилиннэ.',
	'articlefeedbackv5-pitch-survey-message' => 'Бука диэн, кылгас сыана биэрэрдии толкуйдан эрэ.',
	'articlefeedbackv5-pitch-survey-accept' => 'Ыйытыгы саҕалыырга',
	'articlefeedbackv5-pitch-join-message' => 'Манна бэлиэтэниэххин баҕараҕын дуо?',
	'articlefeedbackv5-pitch-join-body' => 'Ааккын бэлиэтээтэххинэ уларытыылары кэтээн көрөр, ырытыыларга кыттар уонна маннааҕы дьон сорҕото буолар кыахтаныаҥ.',
	'articlefeedbackv5-pitch-join-accept' => 'Саҥа ааты бэлиэтииргэ',
	'articlefeedbackv5-pitch-join-login' => 'Ааккын эт',
	'articlefeedbackv5-pitch-edit-message' => 'Бу сирэйи уларытар кыахтааххын ээ.',
	'articlefeedbackv5-pitch-edit-accept' => 'Бу сирэйи уларыт',
	'articlefeedbackv5-survey-message-success' => 'Ыйытыыларга хоруйдаабыккар махтанабыт.',
	'articlefeedbackv5-survey-message-error' => 'Алҕас таҕыста.
Бука диэн хойутуу хос боруобалаар.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Бүгүү тахсыылар уонна түһүүлэр',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Уһулуччу сыанабылы ылбыт ыстатыйалар: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Саамай намыһах сыанабылы ылбыт ыстатыйалар: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Бу нэдиэлэҕэ саамай элбэхтэ уларыйбыттар',
	'articleFeedbackv5-table-caption-recentlows' => 'Соторутааҥы түһүүлэр',
	'articleFeedbackv5-table-heading-page' => 'Сирэй',
	'articleFeedbackv5-table-heading-average' => 'Орто',
	'articleFeedbackv5-copy-above-highlow-tables' => 'Бу кыах тургутулла турар. Бука диэн, санааҕын [$1 сирэйгэ] суруй.',
);

/** Sicilian (Sicilianu)
 * @author Aushulz
 */
$messages['scn'] = array(
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Àutru',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Picchì?',
	'articlefeedbackv5-survey-question-comments' => 'Vò diri autri cosi?',
	'articlefeedbackv5-survey-title' => "Arrispunni a 'na pocu di dumanni",
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'articlefeedbackv5' => 'Hodnotenie článku',
	'articlefeedbackv5-desc' => 'Hodnotenie článku (pilotná verzia)',
	'articlefeedbackv5-survey-question-origin' => 'Na ktorej stránke ste sa nachádzali, keď ste spustili tento prieskum?',
	'articlefeedbackv5-survey-question-whyrated' => 'Prosím, dajte nám vedieť prečo ste dnes ohodnotili túto stránku (zaškrtnite všetky možnosti, ktoré považujete za pravdivé):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Chcel som prispieť k celkovému ohodnoteniu stránky',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Dúfam, že moje hodnotenie pozitívne ovplyvní vývoj stránky',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Chcel som prispieť do {{GRAMMAR:genitív|{{SITENAME}}}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Rád sa delím o svoj názor',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Dnes som neposkytol hodnotenie, ale chcel som okomentovať túto možnosť',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Iné',
	'articlefeedbackv5-survey-question-useful' => 'Veríte, že poskytnuté hodnotenia sú užitočné a jasné?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Prečo?',
	'articlefeedbackv5-survey-question-comments' => 'Máte nejaké ďalšie komentáre?',
	'articlefeedbackv5-survey-submit' => 'Odoslať',
	'articlefeedbackv5-survey-title' => 'Prosím, zodpovedajte niekoľko otázok',
	'articlefeedbackv5-survey-thanks' => 'Ďakujeme za vyplnenie dotazníka.',
	'articlefeedbackv5-error' => 'Vyskytla sa chyba. Prosím, skúste to neskôr.',
	'articlefeedbackv5-form-switch-label' => 'Ohodnotiť túto stránku',
	'articlefeedbackv5-form-panel-title' => 'Ohodnotiť túto stránku',
	'articlefeedbackv5-form-panel-explanation' => 'Čo je toto?',
	'articlefeedbackv5-form-panel-clear' => 'Odstrániť toto hodnotenie',
	'articlefeedbackv5-form-panel-expertise' => 'Mám veľké vedomosti o tejto téme (nepovinné)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Mám v tejto oblasti univerzitný titul',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Je to súčasť mojej profesie',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Je to moja hlboká osobná vášeň',
	'articlefeedbackv5-form-panel-expertise-other' => 'Zdroj mojich vedomostí tu nie je uvedený',
	'articlefeedbackv5-form-panel-helpimprove' => 'Chcel by som pomôcť zlepšeniu Wikipédie, pošlite mi e-mail (nepovinné)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Pošleme vám potvrdzovací email. Vašu adresu neposkytneme nikomu inému. $1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Ochrana súkromia',
	'articlefeedbackv5-form-panel-submit' => 'Odoslať hodnotenie',
	'articlefeedbackv5-form-panel-success' => 'Úspešne uložené',
	'articlefeedbackv5-form-panel-expiry-title' => 'Platnosť vášho hodnotenia vypršala',
	'articlefeedbackv5-form-panel-expiry-message' => 'Prosím, znova vyhodnoťte túto stránku a odošlite nové hodnotenie.',
	'articlefeedbackv5-report-switch-label' => 'Zobraziť hodnotenia stránky',
	'articlefeedbackv5-report-panel-title' => 'Hodnotenia stránky',
	'articlefeedbackv5-report-panel-description' => 'Súčasné priemerné hodnotenie.',
	'articlefeedbackv5-report-empty' => 'Bez hodnotenia',
	'articlefeedbackv5-report-ratings' => '$1 {{PLURAL:$1|hodnotenie|hodnotenia|hodnotení}}',
	'articlefeedbackv5-field-trustworthy-label' => 'Dôveryhodná',
	'articlefeedbackv5-field-trustworthy-tip' => 'Máte pocit, že táto stránka má dostatočné citácie a že tieto citácie pochádzajú z dôveryhodných zdrojov?',
	'articlefeedbackv5-field-complete-label' => 'Úplná',
	'articlefeedbackv5-field-complete-tip' => 'Máte pocit, že táto stránka pokrýva základné tematické oblasti, ktoré by mala?',
	'articlefeedbackv5-field-objective-label' => 'Objektívna',
	'articlefeedbackv5-field-objective-tip' => 'Máte pocit, že táto stránka zobrazuje spravodlivé zastúpenie všetkých pohľadov na problematiku?',
	'articlefeedbackv5-field-wellwritten-label' => 'Dobre napísaná',
	'articlefeedbackv5-field-wellwritten-tip' => 'Máte pocit, že táto stránka je dobre organizovaná a dobre napísaná?',
	'articlefeedbackv5-pitch-reject' => 'Možno neskôr',
	'articlefeedbackv5-pitch-or' => 'alebo',
	'articlefeedbackv5-pitch-thanks' => 'Vďaka! Vaše hodnotenie bolo uložené.',
	'articlefeedbackv5-pitch-survey-message' => 'Prosím, venujte chvíľku vyplneniu krátkeho prieskumu.',
	'articlefeedbackv5-pitch-survey-accept' => 'Spustiť prieskum',
	'articlefeedbackv5-pitch-join-message' => 'Chceli ste si vytvoriť účet?',
	'articlefeedbackv5-pitch-join-body' => 'Účtu vám pomôže sledovať vaše úpravy, zapojiť sa do diskusií a stať sa súčasťou komunity.',
	'articlefeedbackv5-pitch-join-accept' => 'Vytvoriť účet',
	'articlefeedbackv5-pitch-join-login' => 'Prihlásiť sa',
	'articlefeedbackv5-pitch-edit-message' => 'Vedeli ste, že môžete túto stránku upravovať?',
	'articlefeedbackv5-pitch-edit-accept' => 'Upraviť túto stránku',
	'articlefeedbackv5-survey-message-success' => 'Ďakujeme za vyplnenie dotazníka.',
	'articlefeedbackv5-survey-message-error' => 'Vyskytla sa chyba.
Prosím, skúste to neskôr.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Dnešné maximá a minimá',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Tento týždeň sa najviac menil',
	'articleFeedbackv5-table-caption-recentlows' => 'Nedávne minimá',
	'articleFeedbackv5-table-heading-page' => 'Stránka',
	'articleFeedbackv5-table-heading-average' => 'Priemer',
);

/** Slovenian (Slovenščina)
 * @author Dbc334
 */
$messages['sl'] = array(
	'articlefeedbackv5' => 'Pregledna plošča povratnih informacij člankov',
	'articlefeedbackv5-desc' => 'Povratna informacija članka',
	'articlefeedbackv5-survey-question-origin' => 'Na kateri strani ste bili, ko ste začeli s to anketo?',
	'articlefeedbackv5-survey-question-whyrated' => 'Prosimo, povejte nam, zakaj ste danes ocenili to stran (izberite vse, kar ustreza):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Želel sem prispevati splošni oceni strani',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Upam, da bo moja ocena dobro vplivala na razvoj strani',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Želel sem prispevati k projektu {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Rad delim svoje mnenje',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Danes nisem podal ocene, ampak sem želel podati povratno informacijo o funkciji',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Drugo',
	'articlefeedbackv5-survey-question-useful' => 'Ali verjamete, da so posredovane ocene uporabne in jasne?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Zakaj?',
	'articlefeedbackv5-survey-question-comments' => 'Imate kakšne dodatne pripombe?',
	'articlefeedbackv5-survey-submit' => 'Pošlji',
	'articlefeedbackv5-survey-title' => 'Prosimo, odgovorite na nekaj vprašanj',
	'articlefeedbackv5-survey-thanks' => 'Zahvaljujemo se vam za izpolnitev vprašalnika.',
	'articlefeedbackv5-survey-disclaimer' => 'S potrditvijo se strinjate s preglednostjo pod temi $1.',
	'articlefeedbackv5-survey-disclaimerlink' => 'pogoji',
	'articlefeedbackv5-error' => 'Prišlo je do napake. Prosimo, poskusite znova pozneje.',
	'articlefeedbackv5-form-switch-label' => 'Ocenite to stran',
	'articlefeedbackv5-form-panel-title' => 'Ocenite to stran',
	'articlefeedbackv5-form-panel-explanation' => 'Kaj je to?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:PovratnaInformacijaOČlankih',
	'articlefeedbackv5-form-panel-clear' => 'Odstrani oceno',
	'articlefeedbackv5-form-panel-expertise' => 'S to temo sem zelo dobro seznanjen (neobvezno)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Imam ustrezno fakultetno/univerzitetno diplomo',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Je del mojega poklica',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'To je globoka osebna strast',
	'articlefeedbackv5-form-panel-expertise-other' => 'Vir mojega znanja tukaj ni naveden',
	'articlefeedbackv5-form-panel-helpimprove' => 'Rad bi pomagal izboljšati Wikipedijo, zato mi pošljite e-pošto (neobvezno)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Poslali vam bomo potrditveno e-pošto. Vašega naslova v skladu z našo $1 ne bomo delili z zunanjimi strankami.',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'izjavo o zasebnosti povratnih informacij',
	'articlefeedbackv5-form-panel-submit' => 'Pošlji ocene',
	'articlefeedbackv5-form-panel-pending' => 'Vaše ocene niso bile poslane',
	'articlefeedbackv5-form-panel-success' => 'Uspešno shranjeno',
	'articlefeedbackv5-form-panel-expiry-title' => 'Vaše ocene so potekle',
	'articlefeedbackv5-form-panel-expiry-message' => 'Prosimo, ponovno ocenite to stran in pošljite nove ocene.',
	'articlefeedbackv5-report-switch-label' => 'Prikaži ocene strani',
	'articlefeedbackv5-report-panel-title' => 'Ocene strani',
	'articlefeedbackv5-report-panel-description' => 'Trenutne povprečne ocene.',
	'articlefeedbackv5-report-empty' => 'Brez ocen',
	'articlefeedbackv5-report-ratings' => '$1 ocen',
	'articlefeedbackv5-field-trustworthy-label' => 'Zanesljivo',
	'articlefeedbackv5-field-trustworthy-tip' => 'Menite, da ima ta stran dovolj navedkov in da ta navajanja prihajajo iz zanesljivih virov?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Manjkajo ugledni viri',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Nekaj uglednih virov',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Ustrezno število uglednih virov',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Dobri ugledni viri',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Odlični ugledni viri',
	'articlefeedbackv5-field-complete-label' => 'Celovito',
	'articlefeedbackv5-field-complete-tip' => 'Menite, da ta stran zajema temeljna tematska področja, ki bi jih naj?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Manjka večina informacij',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Vsebuje nekatere informacije',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Vsebuje ključne informacije, vendar z vrzelmi',
	'articlefeedbackv5-field-complete-tooltip-4' => 'Vsebuje večino ključnih informacij',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Celovita pokritost',
	'articlefeedbackv5-field-objective-label' => 'Nepristransko',
	'articlefeedbackv5-field-objective-tip' => 'Menite, da ta stran prikazuje pravično zastopanost vseh pogledov na obravnavano temo?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Močno pristransko',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Zmerno pristransko',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Minimalno pristransko',
	'articlefeedbackv5-field-objective-tooltip-4' => 'Brez očitne pristranskosti',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Popolnoma nepristransko',
	'articlefeedbackv5-field-wellwritten-label' => 'Dobro napisano',
	'articlefeedbackv5-field-wellwritten-tip' => 'Menite, da je ta stran dobro organizirana in dobro napisana?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Nerazumljivo',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Težko razumljivo',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Zadostno jasno',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Dobro jasno',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Izjemno jasno',
	'articlefeedbackv5-pitch-reject' => 'Morda kasneje',
	'articlefeedbackv5-pitch-or' => 'ali',
	'articlefeedbackv5-pitch-thanks' => 'Hvala! Vaše ocene so zabeležene.',
	'articlefeedbackv5-pitch-survey-message' => 'Prosimo, vzemite si trenutek, da izpolnite kratko anketo.',
	'articlefeedbackv5-pitch-survey-accept' => 'Začni z anketo',
	'articlefeedbackv5-pitch-join-message' => 'Ste želeli ustvariti račun?',
	'articlefeedbackv5-pitch-join-body' => 'Račun vam bo pomagal slediti vašim urejanjem, se vključiti v razpravo in biti del skupnosti.',
	'articlefeedbackv5-pitch-join-accept' => 'Ustvari račun',
	'articlefeedbackv5-pitch-join-login' => 'Prijavite se',
	'articlefeedbackv5-pitch-edit-message' => 'Ali ste vedeli, da lahko uredite ta članek?',
	'articlefeedbackv5-pitch-edit-accept' => 'Uredi ta članek',
	'articlefeedbackv5-survey-message-success' => 'Zahvaljujemo se vam za izpolnitev vprašalnika.',
	'articlefeedbackv5-survey-message-error' => 'Prišlo je do napake.
Prosimo, poskusite znova pozneje.',
	'articlefeedbackv5-privacyurl' => 'http://wikimediafoundation.org/wiki/Feedback_privacy_statement',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Današnji vzponi in padci',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Članki z najvišjimi ocenami: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Članki z najnižjimi ocenami: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Ta teden najbolj spremenjeno',
	'articleFeedbackv5-table-caption-recentlows' => 'Nedavni padci',
	'articleFeedbackv5-table-heading-page' => 'Stran',
	'articleFeedbackv5-table-heading-average' => 'Povprečje',
	'articleFeedbackv5-copy-above-highlow-tables' => 'To je preizkusna funkcija. Prosimo, podajte povratno informacijo na [$1 pogovorni strani].',
	'articlefeedbackv5-dashboard-bottom' => "'''Opomba''': Nadaljevali bomo z raziskovanjem različnih načinov prikazovanja člankov na teh preglednih ploščah. Pregledna plošča trenutno vključuje naslednje članke:
* Strani z najvišjimi/najnižjimi ocenami: članki, ki so v zadnjih 24 urah prejeli vsaj 10 ocen. Povprečja predstavljajo sredino vseh ocen, podanih v zadnjih 24 urah.
* Nedavni padci: članki, ki so v zadnjih 24 urah prejeli 70&nbsp;% ali več nizkih (dve zvezdici ali manj) ocen v kateri koli kategoriji. Vključeni so samo članki, ki so v zadnjih 24 urah prejeli vsaj 10 ocen.",
	'articlefeedbackv5-disable-preference' => 'Na strani ne pokaži gradnika Povratna informacija članka',
	'articlefeedbackv5-emailcapture-response-body' => 'Pozdravljeni!

Zahvaljujemo se vam za izkazano zanimanje za pomoč pri izboljševanju {{GRAMMAR:rodilnik|{{SITENAME}}}}.

Prosimo, vzemite si trenutek in potrdite vaš e-poštni naslov s klikom na spodnjo povezavo:

$1

Obiščete lahko tudi:

$2

in vnesete spodnjo potrditveno kodo:

$3

Kmalu vam bomo sporočili, kako lahko pomagate izboljšati {{GRAMMAR:tožilnik|{{SITENAME}}}}.

Če tega niste zahtevali, prosimo, prezrite to e-pošto in ničesar več vam ne bomo poslali.

Hvala in najlepše želje,
ekipa {{GRAMMAR:rodilnik|{{SITENAME}}}}',
);

/** Serbian (Cyrillic script) (‪Српски (ћирилица)‬)
 * @author Rancher
 * @author Sasa Stefanovic
 * @author Михајло Анђелковић
 */
$messages['sr-ec'] = array(
	'articlefeedbackv5' => 'Табла за оцењивање чланака',
	'articlefeedbackv5-desc' => 'Оцењивање чланака',
	'articlefeedbackv5-survey-question-origin' => 'На којој страници сте били када сте започели ову анкету?',
	'articlefeedbackv5-survey-question-whyrated' => 'Реците нам зашто сте данас оценили ову страницу (означити све што одговара):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Желео/ла сам да учествујем у свеукупној оцени странице',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Надам се да ће моја оцена позитивно утицати на даљи развој странице',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Желим да допринесем пројекту {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Волим да са свима поделим моје мишљење',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Данас нисам желео/ла да оцењујем, али сам желео/ла да дам повратну информацију о самом алату за оцењивање.',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Остало',
	'articlefeedbackv5-survey-question-useful' => 'Да ли верујете да су могуће оцене корисне и јасне?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Зашто?',
	'articlefeedbackv5-survey-question-comments' => 'Имате ли још коментара?',
	'articlefeedbackv5-survey-submit' => 'Пошаљи',
	'articlefeedbackv5-survey-title' => 'Молимо вас да одговорите на неколико питања',
	'articlefeedbackv5-survey-thanks' => 'Хвала вам што сте попунили упитник.',
	'articlefeedbackv5-error' => 'Дошло је до грешке. Покушајте поново.',
	'articlefeedbackv5-form-switch-label' => 'Оцени ову страницу',
	'articlefeedbackv5-form-panel-title' => 'Оцењивање странице',
	'articlefeedbackv5-form-panel-clear' => 'Уклони ову оцену',
	'articlefeedbackv5-form-panel-expertise' => 'Добро сам упознат/а са овом темом (необавезно)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Имам релевантну универзитетску диплому',
	'articlefeedbackv5-form-panel-expertise-profession' => 'То је део моје струке',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Ово спада у домен мојих хобија',
	'articlefeedbackv5-form-panel-expertise-other' => 'Извор мог знања о теми није наведен овде',
	'articlefeedbackv5-form-panel-helpimprove' => 'Волео/ла бих да помажем унапређење Википедије, пошаљи ми једну електронску поруку (необавезно)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Послаћемо вам поруку за потврду е-адресе. Не делимо е-адресе ни с ким. $1',
	'articlefeedbackv5-form-panel-helpimprove-email-placeholder' => 'eposta@primer.org',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Политика приватности',
	'articlefeedbackv5-form-panel-submit' => 'Пошаљи оцене',
	'articlefeedbackv5-form-panel-pending' => 'Ваше оцене још увек нису послате',
	'articlefeedbackv5-form-panel-success' => 'Успешно сачувано',
	'articlefeedbackv5-form-panel-expiry-title' => 'Ваше оцене су истекле',
	'articlefeedbackv5-form-panel-expiry-message' => 'Поново оцените страницу и пошаљите нове оцене.',
	'articlefeedbackv5-report-switch-label' => 'Преглед оцена странице',
	'articlefeedbackv5-report-panel-title' => 'Оцене странице',
	'articlefeedbackv5-report-panel-description' => 'Тренутне средње оцене',
	'articlefeedbackv5-report-empty' => 'Нема оцена.',
	'articlefeedbackv5-report-ratings' => '$1 оцена',
	'articlefeedbackv5-field-trustworthy-label' => 'Веродостојно',
	'articlefeedbackv5-field-trustworthy-tip' => 'Сматрате ли да ова страница има довољно извора и да су они из поверљивих извора?',
	'articlefeedbackv5-field-complete-label' => 'Комплетност',
	'articlefeedbackv5-field-complete-tip' => 'Сматрате ли да ова страница покрива основне делове теме коју обрађује?',
	'articlefeedbackv5-field-objective-label' => 'Непристрано',
	'articlefeedbackv5-field-objective-tip' => 'Сматрате ли да су на овој страници све тачке гледишта равноправно приказане?',
	'articlefeedbackv5-field-wellwritten-label' => 'Добро написано',
	'articlefeedbackv5-field-wellwritten-tip' => 'Мислите ли да је ова страница добро организована и добро написана?',
	'articlefeedbackv5-pitch-reject' => 'Можда касније',
	'articlefeedbackv5-pitch-or' => 'или',
	'articlefeedbackv5-pitch-thanks' => 'Хвала! Ваше оцене су сачуване.',
	'articlefeedbackv5-pitch-survey-message' => 'Одвојите тренутак да довршите кратку анкету.',
	'articlefeedbackv5-pitch-survey-accept' => 'Почни упитник',
	'articlefeedbackv5-pitch-join-message' => 'Желите ли да отворите налог?',
	'articlefeedbackv5-pitch-join-body' => 'Налог ће вам помоћи да пратите своје измене, да се укључите у разговоре и да будете део заједнице.',
	'articlefeedbackv5-pitch-join-accept' => 'Отвори налог',
	'articlefeedbackv5-pitch-join-login' => 'Пријави ме',
	'articlefeedbackv5-pitch-edit-message' => 'Јесте ли знали да можете да уређујете ову страницу?',
	'articlefeedbackv5-pitch-edit-accept' => 'Уреди ову страницу',
	'articlefeedbackv5-survey-message-success' => 'Хвала вам што сте попунили упитник.',
	'articlefeedbackv5-survey-message-error' => 'Дошло је до грешке.
Покушајте касније.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Данашње високе и ниске оцене',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Странице с највишим оценама: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Странице с најнижим оценама: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Највише мењани ове недеље',
	'articleFeedbackv5-table-caption-recentlows' => 'Скорашње ниске оцене',
	'articleFeedbackv5-table-heading-page' => 'Страница',
	'articleFeedbackv5-table-heading-average' => 'Просек',
	'articlefeedbackv5-table-noratings' => '-',
	'articleFeedbackv5-copy-above-highlow-tables' => 'Ово је експериментални додатак. Молимо вас да пошаљете повратну информацију на [$1 страници за разговор].',
	'articlefeedbackv5-emailcapture-response-body' => 'Здраво!

Хвала вам што сте показали жељу да помогнете у унапређењу пројекта {{SITENAME}}.

Одвојите тренутак да потврдите вашу е-адресу кликом на везу испод:

$1

Можете посетити и:

$2

Након тога, унесите следећи потврдни код:

$3

Ускоро ћемо вас обавестити о томе како нам можете помоћи.

Ако сте добили ову поруку грешком, само је занемарите.

Све најлепше,
{{SITENAME}}',
);

/** Swedish (Svenska)
 * @author Ainali
 * @author Fluff
 * @author Lokal Profil
 * @author Tobulos1
 * @author WikiPhoenix
 */
$messages['sv'] = array(
	'articlefeedbackv5' => 'Instrumentpanel för artikelbedömning',
	'articlefeedbackv5-desc' => 'Artikelbedömning (pilotversion)',
	'articlefeedbackv5-survey-question-origin' => 'Vilken sida var du på när du startade denna undersökning?',
	'articlefeedbackv5-survey-question-whyrated' => 'Låt oss gärna veta varför du betygsatte denna sida i dag (markera alla som gäller):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Jag ville bidra till den generella bedömningen av sidan',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Jag hoppas att min bedömning skulle påverka utvecklingen av sidan positivt',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Jag ville bidra till {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Jag gillar att ge min åsikt',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Jag har inte gjort en betygsättning idag, men ville ge en bedömning på denna funktion',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Övrigt',
	'articlefeedbackv5-survey-question-useful' => 'Tror du att bedömningarna är användbara och tydliga?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Varför?',
	'articlefeedbackv5-survey-question-comments' => 'Har du några ytterligare kommentarer?',
	'articlefeedbackv5-survey-submit' => 'Skicka',
	'articlefeedbackv5-survey-title' => 'Svara på några få frågor',
	'articlefeedbackv5-survey-thanks' => 'Tack för att du fyllde i enkäten.',
	'articlefeedbackv5-survey-disclaimer' => 'För att förbättra denna funktion kommer din feedback kanske anonymt delas med Wikipedia-gemenskapen.',
	'articlefeedbackv5-error' => 'Ett fel har uppstått. Försök igen senare.',
	'articlefeedbackv5-form-switch-label' => 'Betygsätt denna sida',
	'articlefeedbackv5-form-panel-title' => 'Betygsätt denna sida',
	'articlefeedbackv5-form-panel-explanation' => 'Vad är detta?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:Artikelbedömning',
	'articlefeedbackv5-form-panel-clear' => 'Ta bort detta betyg',
	'articlefeedbackv5-form-panel-expertise' => 'Jag är mycket kunnig i detta ämne (valfritt)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Jag har en relevant högskole-/universitetsexamen',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Det är en del av mitt yrke',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Det är en djupt personlig passion',
	'articlefeedbackv5-form-panel-expertise-other' => 'Källan till min kunskap inte är listad här',
	'articlefeedbackv5-form-panel-helpimprove' => 'Jag skulle vilja bidra till att förbättra Wikipedia, skicka mig ett e-post (valfritt)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Vi skickar en bekräftelse via e-post. Vi delar inte ut din adress till någon annan. $1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Integritetspolicy',
	'articlefeedbackv5-form-panel-submit' => 'Skicka betyg',
	'articlefeedbackv5-form-panel-pending' => 'Ditt betyg har inte skickats in ännu',
	'articlefeedbackv5-form-panel-success' => 'Sparat',
	'articlefeedbackv5-form-panel-expiry-title' => 'Dina betyg har gått ut',
	'articlefeedbackv5-form-panel-expiry-message' => 'Vänligen omvärdera denna sida och skicka nya omdömen.',
	'articlefeedbackv5-report-switch-label' => 'Visa sidbetyg',
	'articlefeedbackv5-report-panel-title' => 'Sidbetyg',
	'articlefeedbackv5-report-panel-description' => 'Nuvarande genomsnittliga betyg.',
	'articlefeedbackv5-report-empty' => 'Inga betyg',
	'articlefeedbackv5-report-ratings' => '$1 betyg',
	'articlefeedbackv5-field-trustworthy-label' => 'Trovärdig',
	'articlefeedbackv5-field-trustworthy-tip' => 'Känner du att denna sida har tillräckliga citat och att dessa citat kommer från pålitliga källor?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Saknar ansedda källor',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Få ansedda källor',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Tillräckligt ansedda källor',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Bra ansedda källor',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Fantastiskt ansedda källor',
	'articlefeedbackv5-field-complete-label' => 'Heltäckande',
	'articlefeedbackv5-field-complete-tip' => 'Känner du att den här sidan täcker de väsentliga ämnesområden som det ska?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Saknar mest information',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Innehåller en del information',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Innehåller nyckelinformation, men har luckor',
	'articlefeedbackv5-field-complete-tooltip-4' => 'Innehåller mest nyckelinformation',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Heltäckande innehåll',
	'articlefeedbackv5-field-objective-label' => 'Objektiv',
	'articlefeedbackv5-field-objective-tip' => 'Känner du att den här sidan visar en rättvis representation av alla perspektiv på frågan?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Starkt ensidig',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Måttlig ensidig',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Minimalt ensidig',
	'articlefeedbackv5-field-objective-tooltip-4' => 'Inte märkbart ensidig',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Helt ensidig',
	'articlefeedbackv5-field-wellwritten-label' => 'Välskriven',
	'articlefeedbackv5-field-wellwritten-tip' => 'Tycker du att den här sidan är väl organiserad och välskriven?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Obegriplig',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Svårt att förstå',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Tillräcklig klarhet',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Bra klarhet',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Exceptionell klarhet',
	'articlefeedbackv5-pitch-reject' => 'Kanske senare',
	'articlefeedbackv5-pitch-or' => 'eller',
	'articlefeedbackv5-pitch-thanks' => 'Tack! Ditt betyg har sparats.',
	'articlefeedbackv5-pitch-survey-message' => 'Vänligen ta en stund att fylla i en kort enkät.',
	'articlefeedbackv5-pitch-survey-accept' => 'Starta undersökning',
	'articlefeedbackv5-pitch-join-message' => 'Ville du skapa ett konto?',
	'articlefeedbackv5-pitch-join-body' => 'Ett konto kommer att hjälpa dig att spåra ändringar, engagera dig i diskussioner, och vara en del av samhället.',
	'articlefeedbackv5-pitch-join-accept' => 'Skapa ett konto',
	'articlefeedbackv5-pitch-join-login' => 'Logga in',
	'articlefeedbackv5-pitch-edit-message' => 'Visste du att du kan redigera denna sida?',
	'articlefeedbackv5-pitch-edit-accept' => 'Redigera denna sida',
	'articlefeedbackv5-survey-message-success' => 'Tack för att du fyllde i undersökningen.',
	'articlefeedbackv5-survey-message-error' => 'Ett fel har uppstått. 
Försök igen senare.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Dagens toppar och dalar',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Sidor med högst betyg: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Sidor med lägst betyg: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Veckans mest ändrade',
	'articleFeedbackv5-table-caption-recentlows' => 'Senaste dalar',
	'articleFeedbackv5-table-heading-page' => 'Sida',
	'articleFeedbackv5-table-heading-average' => 'Genomsnittlig',
	'articleFeedbackv5-copy-above-highlow-tables' => 'Detta är en experimentell funktion. Lämna feedback på [$1 diskussionssidan].',
	'articlefeedbackv5-dashboard-bottom' => "'''OBS''': Vi kommer att fortsätta experimentera med olika sätt att belysa artiklar i dessa instrumentpaneler. För närvarande inkluderar instrumentpanelen följande artiklar:
* Sidor med den högst/lägst betyg: Artiklar som har fått minst tio betygsättningar inom de senaste 24 timmarna. Medelvärden räknas ut genom att ta genomsnittet av alla betygssättningar som har skickats in inom de senaste 24 timmarna.
* Nyliga bottenrekord: Artiklar som fått 70 % eller fler låga (två stjärnor eller lägre) betygssättningar i någon kategori under de senaste 24 timmarna. Endast artiklar som fått minst tio betygssättningar inom de senaste 24 timmarna inkluderas.",
	'articlefeedbackv5-disable-preference' => 'Visa inte artikelbedömnings-widget på sidor',
	'articlefeedbackv5-emailcapture-response-body' => 'Hej!

Tack för att ha uttryckt intresse av att hjälpa till att förbättra {{SITENAME}}.

Var god ta en stund att bekräfta din e-post genom att klicka på länken nedan:

$1

Du kan också besöka:

$2

Och ange följande bekräftelsekod:

$3

Vi kommer att kontakta dig inom kort med hur du kan förbättra {{SITENAME}}.

Om du inte påbörjade denna begäran, ignorera detta e-postmeddelande och vi kommer inte skicka någonting annat.

Tack och lycka till!
{{SITENAME}}-teamet',
);

/** Tamil (தமிழ்)
 * @author TRYPPN
 */
$messages['ta'] = array(
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'இந்த தளத்திற்கு நான் பங்களிக்க வேண்டும் {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'நான் என்னுடைய கருத்துக்களை மற்றவர்களுடன் பகிர்ந்துகொள்ள விரும்புகிறேன்',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'மற்றவை',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'ஏன் ?',
	'articlefeedbackv5-survey-question-comments' => 'தாங்கள் மேலும் அதிகமான கருத்துக்களை கூற விரும்புகிறீர்களா ?',
	'articlefeedbackv5-survey-submit' => 'சமர்ப்பி',
	'articlefeedbackv5-survey-title' => 'தயவு செய்து ஒரு சில கேள்விகளுக்கு பதில் அளியுங்கள்',
	'articlefeedbackv5-survey-thanks' => 'ஆய்வுக்கான படிவத்தை பூர்த்தி செய்தமைக்கு நன்றி.',
);

/** Telugu (తెలుగు)
 * @author Veeven
 */
$messages['te'] = array(
	'articlefeedbackv5' => 'వ్యాసపు మూల్యాంకన',
	'articlefeedbackv5-survey-question-whyrated' => 'ఈ పుటని ఈరోజు మీరు ఎందుకు మూల్యాంకన చేసారో మాకు దయచేసి తెలియజేయండి (వర్తించే వాటినన్నీ ఎంచుకోండి):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'నేను ఈ పుట యొక్క స్థూల మూల్యాంకనకి తోడ్పాలనుకున్నాను',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'నా మూల్యాంకన ఈ పుట యొక్క అభివృద్ధికి సానుకూలంగా ప్రభావితం చేస్తుందని ఆశిస్తున్నాను',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'నేను {{SITENAME}}కి తోడ్పడాలనుకున్నాను',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'నా అభిప్రాయాన్ని పంచుకోవడం నాకిష్టం',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'నేను ఈ రోజు మాల్యాంకన చేయలేదు, కానీ ఈ సౌలభ్యంపై నా ప్రతిస్పందనని తెలియజేయాలనుకున్నాను',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'ఇతర',
	'articlefeedbackv5-survey-question-useful' => 'ఈ మూల్యాంకనలు ఉపయోగకరంగా మరియు స్పష్టంగా ఉన్నాయని మీరు నమ్ముతున్నారా?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'ఎందుకు?',
	'articlefeedbackv5-survey-question-comments' => 'అదనపు వ్యాఖ్యలు ఏమైనా ఉన్నాయా?',
	'articlefeedbackv5-survey-submit' => 'దాఖలుచెయ్యి',
	'articlefeedbackv5-survey-title' => 'దయచేసి కొన్ని ప్రశ్నలకి సమాధానమివ్వండి',
	'articlefeedbackv5-survey-thanks' => 'ఈ సర్వేని పూరించినందుకు కృతజ్ఞతలు.',
	'articlefeedbackv5-form-panel-explanation' => 'ఇది ఏమిటి?',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'గోప్యతా విధానం',
	'articlefeedbackv5-report-panel-title' => 'పుట మూల్యాంకన',
	'articlefeedbackv5-report-ratings' => '$1 మూల్యాంకనలు',
	'articlefeedbackv5-pitch-or' => 'లేదా',
	'articlefeedbackv5-pitch-join-login' => 'ప్రవేశించండి',
	'articlefeedbackv5-pitch-edit-accept' => 'ఈ పుటని మార్చండి',
	'articleFeedbackv5-table-heading-page' => 'పుట',
	'articleFeedbackv5-table-heading-average' => 'సగటు',
);

/** Tetum (Tetun)
 * @author MF-Warburg
 */
$messages['tet'] = array(
	'articleFeedbackv5-table-heading-page' => 'Pájina',
);

/** Turkmen (Türkmençe)
 * @author Hanberke
 */
$messages['tk'] = array(
	'articlefeedbackv5' => 'Makala berlen baha',
	'articlefeedbackv5-desc' => 'Makala berlen baha (synag warianty)',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Men sahypanyň umumy derejesine goşant goşmak isledim.',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => '{{SITENAME}} saýtyna goşant goşmak isledim.',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Öz pikirimi paýlaşmagy halaýaryn.',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Başga',
	'articlefeedbackv5-survey-question-useful' => 'Berlen derejeleriň peýdalydygyna we düşnüklidigine ynanýarsyňyzmy?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Näme üçin?',
	'articlefeedbackv5-survey-question-comments' => 'Goşmaça bellikleriňiz barmy?',
	'articlefeedbackv5-survey-submit' => 'Tabşyr',
	'articlefeedbackv5-survey-title' => 'Käbir soraglara jogap beriň',
	'articlefeedbackv5-survey-thanks' => 'Soragnamany dolduranyňyz üçin sag boluň.',
);

/** Tagalog (Tagalog)
 * @author AnakngAraw
 */
$messages['tl'] = array(
	'articlefeedbackv5' => 'Pisarang-dunggulan ng katugunang-puna na panglathalain',
	'articlefeedbackv5-desc' => 'Pagsusuri ng lathalain (paunang bersyon)',
	'articlefeedbackv5-survey-question-origin' => 'Anong pahina ang kinaroroonan mo noong simulan mo ang pagtatanung-tanong na ito?',
	'articlefeedbackv5-survey-question-whyrated' => 'Mangyari sabihin sa amin kung bakit mo inantasan ng ganito ang pahinang ito ngayon (lagyan ng tsek ang lahat ng maaari):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Nais kong umambag sa pangkalahatang kaantasan ng pahina',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Umaasa ako na ang aking pag-aantas ay positibong makakaapekto sa pagpapaunlad ng pahina',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Nais kong makapag-ambag sa {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Nais ko ang pagpapamahagi ng aking opinyon',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Hindi ako nagbigay ng pag-aantas ngayon, subalit nais kong magbigay ng puna sa hinaharap',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Iba pa',
	'articlefeedbackv5-survey-question-useful' => 'Naniniwala ka ba na ang mga pag-aantas na ibinigay ay magagamit at malinaw?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Bakit?',
	'articlefeedbackv5-survey-question-comments' => 'Mayroon ka pa bang karagdagang mga puna?',
	'articlefeedbackv5-survey-submit' => 'Ipasa',
	'articlefeedbackv5-survey-title' => 'Pakisagot ang ilang mga katanungan',
	'articlefeedbackv5-survey-thanks' => 'Salamat sa pagsagot sa mga pagtatanong.',
	'articlefeedbackv5-error' => 'Naganap ang isang kamalian.  Paki subukan uli mamaya.',
	'articlefeedbackv5-form-switch-label' => 'Antasan ang pahinang ito',
	'articlefeedbackv5-form-panel-title' => 'Antasan ang pahinang ito',
	'articlefeedbackv5-form-panel-explanation' => 'Ano ba ito?',
	'articlefeedbackv5-form-panel-clear' => 'Alisin ang antas na ito',
	'articlefeedbackv5-form-panel-expertise' => 'Talagang maalam ako hinggil sa paksang ito (maaaring wala ito)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Mayroon akong kaugnay na baitang sa dalubhasaan/pamantasan',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Bahagi ito ng aking propesyon',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Isa itong malalim na pansariling hilig',
	'articlefeedbackv5-form-panel-expertise-other' => 'Hindi nakatala rito ang pinagmulan ng aking kaalaman',
	'articlefeedbackv5-form-panel-helpimprove' => 'Nais kong painamin ang Wikipedia, padalhan ako ng isang e-liham (maaaring wala ito)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Padadalhan ka namin ng isang e-liham ng pagtitiyak. Hindi namin ibabahagi ang tirahan mo kaninuman. $1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Patakaran sa paglilihim',
	'articlefeedbackv5-form-panel-submit' => 'Ipadala ang mga antas',
	'articlefeedbackv5-form-panel-pending' => 'Hindi pa naipapasa ang mga pag-aantas mo',
	'articlefeedbackv5-form-panel-success' => 'Matagumpay na nasagip',
	'articlefeedbackv5-form-panel-expiry-title' => 'Paso na ang mga pag-aantas mo',
	'articlefeedbackv5-form-panel-expiry-message' => 'Mangyaring pakisuring muli ang pahinang ito at magpasa ng bagong mga antas.',
	'articlefeedbackv5-report-switch-label' => 'Tingnan ang mga antas ng pahina',
	'articlefeedbackv5-report-panel-title' => 'Mga antas ng pahina',
	'articlefeedbackv5-report-panel-description' => 'Pangkasalukuyang pangkaraniwang mga antas.',
	'articlefeedbackv5-report-empty' => 'Walang mga antas',
	'articlefeedbackv5-report-ratings' => '$1 mga antas',
	'articlefeedbackv5-field-trustworthy-label' => 'Mapagkakatiwalaan',
	'articlefeedbackv5-field-trustworthy-tip' => 'Pakiramdam mo ba na ang pahinang ito ay may sapat na mga pagbabanggit ng pinagsipian at ang mga pagbabanggit na ito ay mula sa mapagkakatiwalaang mga pinagkunan?',
	'articlefeedbackv5-field-complete-label' => 'Buo',
	'articlefeedbackv5-field-complete-tip' => 'Sa tingin mo ba ang pahinang ito ay sumasakop sa mahahalagang mga lugar ng paksang nararapat?',
	'articlefeedbackv5-field-objective-label' => 'Palayunin',
	'articlefeedbackv5-field-objective-tip' => 'Nararamdaman mo ba na ang pahinang ito ay nagpapakita ng patas na pagkatawan sa lahat ng mga pananaw hinggil sa paksa?',
	'articlefeedbackv5-field-wellwritten-label' => 'Mainam ang pagkakasulat',
	'articlefeedbackv5-field-wellwritten-tip' => 'Sa tingin mo ba ang pahinang ito ay maayos ang pagkakabuo at mabuti ang pagkakasulat?',
	'articlefeedbackv5-pitch-reject' => 'Maaaring mamaya',
	'articlefeedbackv5-pitch-or' => 'o',
	'articlefeedbackv5-pitch-thanks' => 'Salamat! Nasagip na ang iyong mga pag-aantas.',
	'articlefeedbackv5-pitch-survey-message' => 'Mangyaring maglaan ng isang sandali upang buuin ang iyong maikling pagbibigay-tugon.',
	'articlefeedbackv5-pitch-survey-accept' => 'Simulan ang pagtugon',
	'articlefeedbackv5-pitch-join-message' => 'Ninais mo bang makalikha ng isang akawnt?',
	'articlefeedbackv5-pitch-join-body' => 'Ang isang akawnt ay makakatulong sa iyong masubaybayan ang mga binago mo, makalahok sa mga usapan, at maging isang bahagi ng pamayanan.',
	'articlefeedbackv5-pitch-join-accept' => 'Lumikha ng isang akawnt',
	'articlefeedbackv5-pitch-join-login' => 'Lumagdang papasok',
	'articlefeedbackv5-pitch-edit-message' => 'Alam mo bang mababago mo ang pahinang ito?',
	'articlefeedbackv5-pitch-edit-accept' => 'Baguhin ang pahinang ito',
	'articlefeedbackv5-survey-message-success' => 'Salamat sa pagpuno ng tugon.',
	'articlefeedbackv5-survey-message-error' => 'Naganap ang isang kamalian.
Paki subukan uli mamaya.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Mga matataas at mga mabababa sa araw na ito',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Mga artikulong may pinakamataas na mga kaantasan: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Mga artikulong may pinakamababang mga kaantasan: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Pinaka nabago sa linggong ito',
	'articleFeedbackv5-table-caption-recentlows' => 'Kamakailang mga mabababa',
	'articleFeedbackv5-table-heading-page' => 'Pahina',
	'articleFeedbackv5-table-heading-average' => 'Karaniwan',
	'articlefeedbackv5-emailcapture-response-body' => 'Kumusta!

Salamat sa pagpapahayag mo ng pagnanais na makatulong sa pagpapainam ng {{SITENAME}}.

Mangyaring kumuha ng isang sandli upang tiyakin ang iyong e-liham sa pamamagitan ng pagpindot sa kawing na nasa ibaba: 

$1

Maaari mo ring dalawin ang:

$2

At ipasok ang sumusunod na kodigo ng pagtitiyak:

$3

Makikipag-ugnayan kami sa loob ng ilang mga sandali sa kung paano ka makakatulong sa pagpapainam ng {{SITENAME}}.

Kung hindi ikaw ang nagpasimula ng kahilingang ito, mangyaring huwag pansinin ang e-liham na ito at hindi na kami magpapadala ng iba pa.

Pinakamainam na mga mithiin para sa iyo at nagpapasalamat,
Ang pangkat ng {{SITENAME}}',
);

/** Turkish (Türkçe)
 * @author 82-145
 * @author CnkALTDS
 * @author Emperyan
 * @author Joseph
 * @author Karduelis
 * @author Reedy
 * @author Stultiwikia
 */
$messages['tr'] = array(
	'articlefeedbackv5' => 'Madde değerlendirmesi',
	'articlefeedbackv5-desc' => 'Madde geribildirimi',
	'articlefeedbackv5-survey-question-origin' => 'Bu ankete başladığında hangi sayfadaydınız?',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Sayfanın genel değerlendirilmesine katkıda bulunmak istedim',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Değerlendirmemin sayfanın gelişimini olumlu yönde etkileyeceğini düşünüyorum',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => '{{SITENAME}} sitesine katkıda bulunmak istedim.',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Fikirlerimi paylaşmayı seviyorum',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Diğer',
	'articlefeedbackv5-survey-question-useful' => 'Mevcut değerlendirmelerin kullanışlı ve anlaşılır olduğunu düşünüyor musunuz?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Neden?',
	'articlefeedbackv5-survey-question-comments' => 'Herhangi ek bir yorumunuz var mı?',
	'articlefeedbackv5-survey-submit' => 'Gönder',
	'articlefeedbackv5-survey-title' => 'Lütfen birkaç soruya yanıt verin',
	'articlefeedbackv5-survey-thanks' => 'Anketi doldurduğunuz için teşekkür ederiz.',
	'articlefeedbackv5-form-switch-label' => 'Bu sayfayı değerlendirin',
	'articlefeedbackv5-form-panel-title' => 'Bu sayfayı değerlendirin',
	'articlefeedbackv5-form-panel-explanation' => 'Bu nedir?',
	'articlefeedbackv5-form-panel-clear' => 'Bu değerlendirmeyi kaldır',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Mesleğimin bir parçasıdır',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Gizlilik ilkesi',
	'articlefeedbackv5-form-panel-submit' => 'Değerlendirmeleri kaydet',
	'articlefeedbackv5-form-panel-pending' => 'Değerlendirmeleriniz henüz kaydedilmedi',
	'articlefeedbackv5-form-panel-success' => 'Başarıyla kaydedildi',
	'articlefeedbackv5-report-switch-label' => 'Sayfa değerlendirmelerini görüntüle',
	'articlefeedbackv5-report-panel-title' => 'Sayfa değerlendirmeleri',
	'articlefeedbackv5-report-panel-description' => 'Şu anki değerlendirme ortalaması',
	'articlefeedbackv5-report-empty' => 'Değerlendirme yok',
	'articlefeedbackv5-field-trustworthy-label' => 'Güvenilir',
	'articlefeedbackv5-field-complete-label' => 'Tamamlanmış',
	'articlefeedbackv5-field-complete-tip' => 'Bu sayfada konuyla ilgili yer alması gerekn tüm bilgilerin yer aldığını düşünüyor musunuz?',
	'articlefeedbackv5-field-objective-label' => 'Tarafsız',
	'articlefeedbackv5-field-objective-tip' => 'Bu sayfanın konu hakkındaki tüm bakış açılarını iyi bir şekilde yansıttığını düşünüyor musunuz?',
	'articlefeedbackv5-field-wellwritten-label' => 'İyi yazılmış',
	'articlefeedbackv5-pitch-reject' => 'Belki ileride',
	'articlefeedbackv5-pitch-or' => 'veya',
	'articlefeedbackv5-pitch-thanks' => 'Teşekkürler! Değerlendirmeleriniz kaydedildi.',
	'articlefeedbackv5-pitch-survey-message' => 'Lütfen kısa bir anketi doldurmak için bir dakikanızı ayırın.',
	'articlefeedbackv5-pitch-survey-accept' => 'Ankete başla',
	'articlefeedbackv5-pitch-join-message' => 'Bir kullanıcı hesabı edinmek istiyor musunuz?',
	'articlefeedbackv5-pitch-join-accept' => 'Yeni hesap edin',
	'articlefeedbackv5-pitch-join-login' => 'Oturum aç',
	'articlefeedbackv5-pitch-edit-message' => 'Bu sayfayı değiştirebileceğinizi biliyor muydunuz?',
	'articlefeedbackv5-pitch-edit-accept' => 'Bu sayfayı değiştir',
	'articlefeedbackv5-survey-message-success' => 'Anketi doldurduğunuz için teşekkür ederiz.',
	'articlefeedbackv5-survey-message-error' => 'Bir hata meydana geldi.
Lütfen daha sonra tekrar deneyin.',
	'articleFeedbackv5-table-heading-page' => 'Madde',
	'articleFeedbackv5-table-heading-average' => 'Ortalama',
);

/** Ukrainian (Українська)
 * @author Arturyatsko
 * @author Dim Grits
 * @author Microcell
 * @author Тест
 */
$messages['uk'] = array(
	'articlefeedbackv5' => 'Панель оцінювання статті',
	'articlefeedbackv5-desc' => 'Оцінка статті',
	'articlefeedbackv5-survey-question-origin' => 'На якій сторінці ви були, коли почали це опитування?',
	'articlefeedbackv5-survey-question-whyrated' => 'Будь ласка, розкажіть нам, чому Ви оцінили цю сторінку сьогодні (позначте всі підходящі варіанти):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Я хотів внести свій внесок у загальний рейтинг сторінки',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Я сподіваюся, що моя оцінка позитивно вплине на розвиток цієї сторінки',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Я хотів би зробити внесок до {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Мені подобається ділитись власними думками',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Я не оцінив сьогодні сторінку, але хочу залишити відгук про цю функцію',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Інше',
	'articlefeedbackv5-survey-question-useful' => 'Чи вважаєте Ви оцінювання корисним та зрозумілим?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Чому?',
	'articlefeedbackv5-survey-question-comments' => 'Чи маєте Ви ще якийсь коментар?',
	'articlefeedbackv5-survey-submit' => 'Відправити',
	'articlefeedbackv5-survey-title' => 'Будь ласка, дайте відповідь на кілька запитань',
	'articlefeedbackv5-survey-thanks' => 'Дякуємо за участь в опитуванні.',
	'articlefeedbackv5-survey-disclaimer' => 'Щоб покращити цю функцію, ваш відгук може бути анонімно наданий спільноті Вікіпедії.',
	'articlefeedbackv5-error' => 'Сталася помилка. Будь ласка, спробуйте пізніше.',
	'articlefeedbackv5-form-switch-label' => 'Оцінить цю сторінку',
	'articlefeedbackv5-form-panel-title' => 'Оцініть цю сторінку',
	'articlefeedbackv5-form-panel-explanation' => 'Що це таке?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:ArticleFeedback',
	'articlefeedbackv5-form-panel-clear' => 'Вилучити оцінку',
	'articlefeedbackv5-form-panel-expertise' => "Я досить обізнаний в цій темі (необов'язково)",
	'articlefeedbackv5-form-panel-expertise-studies' => 'Маю відповідну спеціальну освіту',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Це стосується моєї професії',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Це моє палке особисте захоплення',
	'articlefeedbackv5-form-panel-expertise-other' => 'Джерело моїх знань не зазначене в списку',
	'articlefeedbackv5-form-panel-helpimprove' => 'Я хотів би допомогти в поліпшенні Вікіпедії, надішліть мені електронного листа (за бажанням)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Ми надішлемо вам підтвердження електронною поштою. Ми не будемо передавати вашу адресу будь-кому. $1',
	'articlefeedbackv5-form-panel-helpimprove-email-placeholder' => 'email@example.org',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Політика конфіденційності',
	'articlefeedbackv5-form-panel-submit' => 'Надіслати оцінки',
	'articlefeedbackv5-form-panel-pending' => 'Ваші оцінки ще не були відправлені',
	'articlefeedbackv5-form-panel-success' => 'Успішно збережено',
	'articlefeedbackv5-form-panel-expiry-title' => 'Ваші оцінки застарілі',
	'articlefeedbackv5-form-panel-expiry-message' => 'Будь ласка, перегляньте сторінку та поставте нові оцінки.',
	'articlefeedbackv5-report-switch-label' => 'Показати оцінки сторінки',
	'articlefeedbackv5-report-panel-title' => 'Рейтинг сторінки',
	'articlefeedbackv5-report-panel-description' => 'Поточні середні оцінки.',
	'articlefeedbackv5-report-empty' => 'Не оцінювалася',
	'articlefeedbackv5-report-ratings' => 'Кількість оцінок: $1',
	'articlefeedbackv5-field-trustworthy-label' => 'Достовірність',
	'articlefeedbackv5-field-trustworthy-tip' => 'Як ви вважаєте, чи достатньо ця сторінка має цитат, чи узяті вони з надійних джерел?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Авторитетні джерела відсутні',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Недостатньо достовірних джерел',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Адекватні авторитетні джерела',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Гарні авторитетні джерела',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Чудові авторитетні джерела',
	'articlefeedbackv5-field-complete-label' => 'Повнота',
	'articlefeedbackv5-field-complete-tip' => 'Чи вважаєте ви, що ця сторінка в достатній мірі висвітлює основні питання з цієї теми?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Відсутня велика частина інформації',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Містить деяку інформацію',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Містить ключову інформацію, але з прогалинами',
	'articlefeedbackv5-field-complete-tooltip-4' => 'Містить загальну інформацію',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Всебічне висвітлення теми',
	'articlefeedbackv5-field-objective-label' => 'Нейтральність',
	'articlefeedbackv5-field-objective-tip' => "Чи вважаєте ви, що на цій сторінці об'єктивно висвітлений предмет з усіх точок зору?",
	'articlefeedbackv5-field-objective-tooltip-1' => 'Досить упереджена',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Помірно упереджена',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Мінімально упереджена',
	'articlefeedbackv5-field-objective-tooltip-4' => 'Немає вочевидь упереджених речень',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Абсолютно неупереджена',
	'articlefeedbackv5-field-wellwritten-label' => 'Стиль',
	'articlefeedbackv5-field-wellwritten-tip' => 'Чи вважаєте ви, що ця сторінка добре структурована і має гарний стиль викладення матеріалу?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Незрозуміла',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Важке сприйняття',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Адекватна ясність викладення матеріалу',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Легко читається',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Винятково легко читається',
	'articlefeedbackv5-pitch-reject' => 'Можливо, пізніше',
	'articlefeedbackv5-pitch-or' => 'або',
	'articlefeedbackv5-pitch-thanks' => 'Дякуємо! Ваші оцінки були збережені.',
	'articlefeedbackv5-pitch-survey-message' => 'Будь ласка, знайдіть хвилинку, щоб оцінити статтю.',
	'articlefeedbackv5-pitch-survey-accept' => 'Почати опитування',
	'articlefeedbackv5-pitch-join-message' => 'Ви хочете створити обліковий запис?',
	'articlefeedbackv5-pitch-join-body' => 'Обліковий запис допоможе вам відстежувати зміни, брати участь в обговореннях і бути частиною спільноти.',
	'articlefeedbackv5-pitch-join-accept' => 'Створити обліковий запис',
	'articlefeedbackv5-pitch-join-login' => 'Увійти до системи',
	'articlefeedbackv5-pitch-edit-message' => 'Чи знаєте ви, що цю сторінку можна редагувати?',
	'articlefeedbackv5-pitch-edit-accept' => 'Редагувати цю сторінку',
	'articlefeedbackv5-survey-message-success' => 'Дякуємо за участь в опитуванні.',
	'articlefeedbackv5-survey-message-error' => 'Сталася помилка. Будь ласка, повторіть спробу пізніше.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Лідери та аутсайдери цього дня.',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Сторінки з найвищими оцінками: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Сторінки з найнижчими оцінками: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'На цьому тижні найбільш змінилися',
	'articleFeedbackv5-table-caption-recentlows' => 'Останні зниження рейтингу',
	'articleFeedbackv5-table-heading-page' => 'Сторінка',
	'articleFeedbackv5-table-heading-average' => 'Середнє значення',
	'articlefeedbackv5-table-noratings' => '-',
	'articleFeedbackv5-copy-above-highlow-tables' => 'Це експериментальна можливість. Прохання висловлювати коментарі на [$1 сторінці обговорення].',
	'articlefeedbackv5-dashboard-bottom' => "'''Примітка''': Ми будемо продовжувати експериментувати з різними способами наповнення цієї панелі. На даний час панель включає такі статті:
* Сторінки з високим/низьким рейтингом: статті, які отримали щонайменше 10 оцінок протягом останніх 24 годин. Середня оцінка розраховується після обробки усіх оцінок за останні 24 години.
* Чинні аутсайдери: Статті, які отримали оцінку нижчу за 70% (1-2 зірки) у будь-якій категорії за останні 24 години. Враховуються тільки статті, які отримали щонайменше 10 оцінок за останні 24 години.",
	'articlefeedbackv5-disable-preference' => 'Не показувати на сторінках віджет оцінювання сторінок',
	'articlefeedbackv5-emailcapture-response-body' => 'Привіт! 
Дякуємо за інтерес до {{SITENAME}}! Будь ласка, знайдіть декілька секунд, щоб підтвердити адресу електронної пошти, натиснувши на посилання нижче:
$1
Ви також можете відвідати: 
$2
і ввести наступний код підтвердження:
$3
Ми повідомимо вам як можна допомогти у поліпшенні {{SITENAME}}.
Якщо ви не відправляли цей запит, не звертайте уваги на цей лист, і ми не потурбуємо вас більше.
З найкращими побажаннями, команда {{SITENAME}}.',
);

/** Vèneto (Vèneto)
 * @author Candalua
 */
$messages['vec'] = array(
	'articlefeedbackv5' => 'Valutassion pagina',
	'articlefeedbackv5-desc' => 'Valutassion pagina (version de prova)',
	'articlefeedbackv5-survey-question-whyrated' => 'Dine el motivo par cui te ghè valutà sta pagina (te poli selessionar più opzioni):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Voléa contribuir a la valutassion conplessiva de la pagina',
	'articlefeedbackv5-survey-answer-whyrated-development' => "Spero che el me giudissio l'influensa positivamente el svilupo de sta pagina",
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Go vossù contribuire a {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Me piase condivìdar la me opinion',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'No go dato valutassion uncuò, ma go volù lassar un comento su la funsionalità',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Altro',
	'articlefeedbackv5-survey-question-useful' => 'Pensito che le valutassion fornìe le sia utili e ciare?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Parché?',
	'articlefeedbackv5-survey-question-comments' => 'Gheto altre robe da dir?',
	'articlefeedbackv5-survey-submit' => 'Manda',
	'articlefeedbackv5-survey-title' => 'Par piaser, rispondi a qualche domanda',
	'articlefeedbackv5-survey-thanks' => 'Grassie de aver conpilà el questionario.',
);

/** Vietnamese (Tiếng Việt)
 * @author Minh Nguyen
 */
$messages['vi'] = array(
	'articlefeedbackv5' => 'Bảng phản hồi bài',
	'articlefeedbackv5-desc' => 'Phản hồi bài',
	'articlefeedbackv5-survey-question-origin' => 'Bạn đang xem trang nào lúc khi bắt đầu cuộc khảo sát này?',
	'articlefeedbackv5-survey-question-whyrated' => 'Xin hãy cho chúng tôi biết lý do tại sao bạn đánh giá trang này hôm nay (kiểm tra các hộp thích hợp):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => 'Tôi muốn có ảnh hưởng đến đánh giá tổng cộng của trang',
	'articlefeedbackv5-survey-answer-whyrated-development' => 'Tôi hy vọng rằng đánh giá của tôi sẽ có ảnh hưởng tích cực đến sự phát triển của trang',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => 'Tôi muốn đóng góp vào {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => 'Tôi thích đưa ý kiến của tôi',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => 'Tôi không đánh giá hôm nay, nhưng vẫn muốn phản hồi về tính năng',
	'articlefeedbackv5-survey-answer-whyrated-other' => 'Khác',
	'articlefeedbackv5-survey-question-useful' => 'Bạn có tin rằng các đánh giá được cung cấp là hữu ích và dễ hiểu?',
	'articlefeedbackv5-survey-question-useful-iffalse' => 'Tại sao?',
	'articlefeedbackv5-survey-question-comments' => 'Bạn có ý kiến bổ sung?',
	'articlefeedbackv5-survey-submit' => 'Gửi',
	'articlefeedbackv5-survey-title' => 'Xin vui lòng trả lời một số câu hỏi',
	'articlefeedbackv5-survey-thanks' => 'Cám ơn bạn đã điền khảo sát.',
	'articlefeedbackv5-survey-disclaimer' => 'Để giúp cải thiện tính năng này, thông tin phản hồi của bạn có thể được chia sẻ nặc danh với cộng đồng Wikipedia.',
	'articlefeedbackv5-error' => 'Đã gặp lỗi. Xin vui lòng thử lại sau.',
	'articlefeedbackv5-form-switch-label' => 'Đánh giá trang này',
	'articlefeedbackv5-form-panel-title' => 'Đánh giá trang này',
	'articlefeedbackv5-form-panel-explanation' => 'Này là gì?',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:Phản hồi bài',
	'articlefeedbackv5-form-panel-clear' => 'Hủy đánh giá này',
	'articlefeedbackv5-form-panel-expertise' => 'Tôi rất am hiểu về đề tài này (tùy chọn)',
	'articlefeedbackv5-form-panel-expertise-studies' => 'Tôi đã lấy bằng có liên quan tại trường cao đẳng / đại học',
	'articlefeedbackv5-form-panel-expertise-profession' => 'Nó thuộc về nghề nghiệp của tôi',
	'articlefeedbackv5-form-panel-expertise-hobby' => 'Tôi quan tâm một cách thiết tha về đề tài này',
	'articlefeedbackv5-form-panel-expertise-other' => 'Tôi hiểu về đề tài này vì lý do khác',
	'articlefeedbackv5-form-panel-helpimprove' => 'Tôi muốn giúp cải tiến Wikipedia – gửi cho tôi một thư điện tử (tùy chọn)',
	'articlefeedbackv5-form-panel-helpimprove-note' => 'Chúng tôi sẽ gửi cho bạn một thư điện tử xác nhận. Chúng tôi sẽ không chia sẽ địa chỉ của bạn với bất cứ ai. $1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => 'Chính sách về sự riêng tư',
	'articlefeedbackv5-form-panel-submit' => 'Gửi đánh giá',
	'articlefeedbackv5-form-panel-pending' => 'Các đánh giá của bạn chưa được gửi',
	'articlefeedbackv5-form-panel-success' => 'Lưu thành công',
	'articlefeedbackv5-form-panel-expiry-title' => 'Các đánh giá của bạn đã hết hạn',
	'articlefeedbackv5-form-panel-expiry-message' => 'Xin vui lòng coi lại và đánh giá lại trang này.',
	'articlefeedbackv5-report-switch-label' => 'Xem các đánh giá của trang',
	'articlefeedbackv5-report-panel-title' => 'Đánh giá của trang',
	'articlefeedbackv5-report-panel-description' => 'Đánh giá trung bình hiện tại',
	'articlefeedbackv5-report-empty' => 'Không có đánh giá',
	'articlefeedbackv5-report-ratings' => '$1 đánh giá',
	'articlefeedbackv5-field-trustworthy-label' => 'Đáng tin',
	'articlefeedbackv5-field-trustworthy-tip' => 'Bạn có cảm thấy rằng bày này chú thích nguồn gốc đầy đủ và đáng tin các nguồn?',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => 'Thiếu những nguồn đáng tin',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => 'Ít nguồn đáng tin',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => 'Đủ nguồn đáng tin',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => 'Nhiều nguồn đáng tin',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => 'Rất nhiều nguồn đáng tin',
	'articlefeedbackv5-field-complete-label' => 'Đầy đủ',
	'articlefeedbackv5-field-complete-tip' => 'Bạn có cảm thấy rằng bài này bao gồm các đề tài cần thiết?',
	'articlefeedbackv5-field-complete-tooltip-1' => 'Thiếu hầu hết thông tin',
	'articlefeedbackv5-field-complete-tooltip-2' => 'Có một số thông tin',
	'articlefeedbackv5-field-complete-tooltip-3' => 'Có những thông tin quan trọng nhưng với một số lỗ hổng',
	'articlefeedbackv5-field-complete-tooltip-4' => 'Có phần nhiều thông tin quan trọng',
	'articlefeedbackv5-field-complete-tooltip-5' => 'Có thông tin đầy đủ',
	'articlefeedbackv5-field-objective-label' => 'Trung lập',
	'articlefeedbackv5-field-objective-tip' => 'Bạn có cảm thấy rằng bài này đại diện công bằng cho tất cả các quan điểm về các vấn đề?',
	'articlefeedbackv5-field-objective-tooltip-1' => 'Hoàn toàn mang tính thiên vị',
	'articlefeedbackv5-field-objective-tooltip-2' => 'Mang tính thiên vị vừa vừa',
	'articlefeedbackv5-field-objective-tooltip-3' => 'Ít mang tính thiên vị',
	'articlefeedbackv5-field-objective-tooltip-4' => 'Không rõ ràng mang tính thiên vị',
	'articlefeedbackv5-field-objective-tooltip-5' => 'Hoàn toàn không có mang tính thiên vị',
	'articlefeedbackv5-field-wellwritten-label' => 'Viết hay',
	'articlefeedbackv5-field-wellwritten-tip' => 'Bạn có cảm thấy rằng bài này được sắp xếp đàng hoàng có văn bản hay?',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => 'Không thể hiểu nổi',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => 'Khó hiểu',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => 'Đủ rõ ràng',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => 'Khá rõ ràng',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => 'Rất là rõ ràng',
	'articlefeedbackv5-pitch-reject' => 'Không bây giờ',
	'articlefeedbackv5-pitch-or' => 'hoặc',
	'articlefeedbackv5-pitch-thanks' => 'Cám ơn! Đánh giá của bạn đã được lưu.',
	'articlefeedbackv5-pitch-survey-message' => 'Hãy dành một chút thời gian để phản hồi một cuộc khảo sát ngắn.',
	'articlefeedbackv5-pitch-survey-accept' => 'Bắt đầu trả lời',
	'articlefeedbackv5-pitch-join-message' => 'Bạn có muốn mở tài khoản tại đây?',
	'articlefeedbackv5-pitch-join-body' => 'Một tài khoản sẽ giúp bạn theo dõi các trang mà bạn sửa đổi và tham gia các cuộc thảo luận và hoạt động của cộng đồng.',
	'articlefeedbackv5-pitch-join-accept' => 'Mở tài khoản',
	'articlefeedbackv5-pitch-join-login' => 'Đăng nhập',
	'articlefeedbackv5-pitch-edit-message' => 'Bạn có biết rằng bạn có thể sửa đổi trang này?',
	'articlefeedbackv5-pitch-edit-accept' => 'Sửa đổi trang này',
	'articlefeedbackv5-survey-message-success' => 'Cám ơn bạn đã điền khảo sát.',
	'articlefeedbackv5-survey-message-error' => 'Đã gặp lỗi.
Xin hãy thử lại sau.',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => 'Các điểm cao và thấp nhất hôm nay',
	'articleFeedbackv5-table-caption-dailyhighs' => 'Các bài đánh giá cao nhất: $1',
	'articleFeedbackv5-table-caption-dailylows' => 'Các bài đánh giá thấp nhất: $1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => 'Các điểm thay đổi nhiều nhất vào tuần này',
	'articleFeedbackv5-table-caption-recentlows' => 'Các điểm thấp gần đây',
	'articleFeedbackv5-table-heading-page' => 'Trang',
	'articleFeedbackv5-table-heading-average' => 'Trung bình',
	'articleFeedbackv5-copy-above-highlow-tables' => 'Đây là một tính năng thử nghiệm. Xin vui lòng đưa ra phản hồi tại [$1 trang thảo luận].',
	'articlefeedbackv5-dashboard-bottom' => "'''Lưu ý:''' Chúng tôi sẽ tiếp tục thử nghiệm những cách chọn lọc bài trong cách bảng điều khiển. Hiện nay các bảng điều khiển bao gồm các bài sau:
* Các trang được đánh giá cao nhất hoặc thấp nhất: các bài đã được đánh giá 10 lần trở lên trong 24 giờ trước. Trung bình tính tất cả các đánh giá được nhận trong 24 giờ trước.
* Các điểm thấp gần đây: các bài được đánh giá 70% (2 sao) trở xuống trong thể loại này trong 24 giờ trước. Chỉ tính các bài được đánh giá 10 lần trở lên trong 24 giờ trước.",
	'articlefeedbackv5-disable-preference' => 'Ẩn bảng Phản hồi bài khỏi các trang',
	'articlefeedbackv5-emailcapture-response-body' => 'Xin chào!

Cám ơn bạn đã bày tỏ quan tâm về việc giúp cải tiến {{SITENAME}}.

Xin vui lòng dành một chút thời gian để xác nhận địa chỉ thư điện tử của bạn dùng liên kết dưới đây:

$1

Bạn cũng có thể ghé vào:

$2

và nhập mã xác nhận sau:

$3

Chúng tôi sẽ sớm liên lạc với bạn với thông tin về giúp cải tiến {{SITENAME}}.

Nếu bạn không phải là người yêu cầu thông tin này, xin vui lòng kệ thông điệp này và chúng tôi sẽ không gửi cho bạn bất cứ gì nữa.

Thân mến và cám ơn,
Nhóm {{SITENAME}}',
);

/** Yoruba (Yorùbá)
 * @author Demmy
 */
$messages['yo'] = array(
	'articlefeedbackv5' => 'Ibi èsì àyọkà',
	'articlefeedbackv5-desc' => '条目评级（测试版）',
	'articlefeedbackv5-survey-question-whyrated' => '请告诉我们今天你为何评价了此页面(选择所有符合的):',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => '我想对网页的总体评价作贡献',
	'articlefeedbackv5-survey-answer-whyrated-development' => '我希望我的评价能给此网页带来正面的影响',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => '我想对{{SITENAME}}做出贡献',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => '我愿意共享我的观点',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => '我今天没有进行评价，但我希望对特性进行反馈。',
	'articlefeedbackv5-survey-answer-whyrated-other' => '其他',
	'articlefeedbackv5-survey-question-useful' => '你认为提供的评价有用并清晰吗？',
	'articlefeedbackv5-survey-question-useful-iffalse' => '为什么？',
	'articlefeedbackv5-survey-question-comments' => '你还有什么想说的吗？',
	'articlefeedbackv5-survey-submit' => 'Fúnsílẹ̀',
	'articlefeedbackv5-survey-title' => '请回答几个问题',
	'articlefeedbackv5-survey-thanks' => '谢谢您回答问卷。',
	'articlefeedbackv5-form-switch-label' => 'Wọn ojúewé yìí',
	'articlefeedbackv5-form-panel-title' => 'Wọn ojúewé yìí',
	'articlefeedbackv5-form-panel-submit' => 'Ìkóólẹ̀ ìdíyelé',
	'articlefeedbackv5-field-complete-label' => '完成',
);

/** Simplified Chinese (‪中文(简体)‬)
 * @author Bencmq
 * @author Hydra
 * @author PhiLiP
 * @author Shizhao
 * @author Xiaomingyan
 * @author 阿pp
 */
$messages['zh-hans'] = array(
	'articlefeedbackv5' => '条目评分面板',
	'articlefeedbackv5-desc' => '条目评分',
	'articlefeedbackv5-survey-question-origin' => '当你开始这项统计调查的时候正在访问哪个页面？',
	'articlefeedbackv5-survey-question-whyrated' => '请告诉我们你今天为此页打分的原因（选择所有合适的选项）：',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => '我想对页面的总体评价作贡献',
	'articlefeedbackv5-survey-answer-whyrated-development' => '我希望我的评价能给此页带来正面的影响',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => '我想对{{SITENAME}}做出贡献',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => '我愿意共享我的观点',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => '我今天没有进行评价，但我希望对本功能作出反馈。',
	'articlefeedbackv5-survey-answer-whyrated-other' => '其他',
	'articlefeedbackv5-survey-question-useful' => '你认为提供的评分有用并清晰吗？',
	'articlefeedbackv5-survey-question-useful-iffalse' => '为什么？',
	'articlefeedbackv5-survey-question-comments' => '你还有什么想说的吗？',
	'articlefeedbackv5-survey-submit' => '提交',
	'articlefeedbackv5-survey-title' => '请回答几个问题',
	'articlefeedbackv5-survey-thanks' => '谢谢您回答问卷。',
	'articlefeedbackv5-survey-disclaimer' => '若要帮助我们改善此功能，您可以将您的反馈意见匿名分享给维基百科社区。',
	'articlefeedbackv5-error' => '发生了一个错误。请稍后重试。',
	'articlefeedbackv5-form-switch-label' => '给本文评分',
	'articlefeedbackv5-form-panel-title' => '给本文评分',
	'articlefeedbackv5-form-panel-explanation' => '这是什么？',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:条目评分工具',
	'articlefeedbackv5-form-panel-clear' => '移除该评分',
	'articlefeedbackv5-form-panel-expertise' => '我非常了解与本主题相关的知识（可选）',
	'articlefeedbackv5-form-panel-expertise-studies' => '我有与其有关的大学学位',
	'articlefeedbackv5-form-panel-expertise-profession' => '这是我专业的一部分',
	'articlefeedbackv5-form-panel-expertise-hobby' => '这是个人隐私',
	'articlefeedbackv5-form-panel-expertise-other' => '此处未列出我的知识的来源',
	'articlefeedbackv5-form-panel-helpimprove' => '我想帮助改善维基百科，请给我发送一封电子邮件（可选）',
	'articlefeedbackv5-form-panel-helpimprove-note' => '我们将向您发送确认电子邮件。我们不会与任何人共享您的地址。$1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => '隐私方针',
	'articlefeedbackv5-form-panel-submit' => '提交评分',
	'articlefeedbackv5-form-panel-pending' => '你的评分尚未提交',
	'articlefeedbackv5-form-panel-success' => '保存成功',
	'articlefeedbackv5-form-panel-expiry-title' => '你的评分已过期',
	'articlefeedbackv5-form-panel-expiry-message' => '请重新评估本页并重新评分。',
	'articlefeedbackv5-report-switch-label' => '查看条目评分',
	'articlefeedbackv5-report-panel-title' => '条目评分',
	'articlefeedbackv5-report-panel-description' => '当前平均分。',
	'articlefeedbackv5-report-empty' => '无评分',
	'articlefeedbackv5-report-ratings' => '$1人评分',
	'articlefeedbackv5-field-trustworthy-label' => '可信度',
	'articlefeedbackv5-field-trustworthy-tip' => '你觉得本条目有足够的参考文献，并且这些文献的来源可靠吗？',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => '缺乏可靠来源',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => '可靠来源很少',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => '有很多可靠来源',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => '来源相当可靠',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => '来源绝对可靠',
	'articlefeedbackv5-field-complete-label' => '完整性',
	'articlefeedbackv5-field-complete-tip' => '您觉得本条目内容是否基本上全面涵盖了该主题所涉及的领域？',
	'articlefeedbackv5-field-complete-tooltip-1' => '缺少绝大多数信息',
	'articlefeedbackv5-field-complete-tooltip-2' => '只含有少量信息',
	'articlefeedbackv5-field-complete-tooltip-3' => '包括了主要的信息，但是还缺少很多',
	'articlefeedbackv5-field-complete-tooltip-4' => '包括了大部分主要的信息',
	'articlefeedbackv5-field-complete-tooltip-5' => '完整全面',
	'articlefeedbackv5-field-objective-label' => '客观性',
	'articlefeedbackv5-field-objective-tip' => '你觉得本条目所描述的所有观点对相关问题的表述是否公平合理，具有代表性？',
	'articlefeedbackv5-field-objective-tooltip-1' => '存在严重的偏见',
	'articlefeedbackv5-field-objective-tooltip-2' => '有一定偏见',
	'articlefeedbackv5-field-objective-tooltip-3' => '稍有偏见',
	'articlefeedbackv5-field-objective-tooltip-4' => '没有明显的偏见',
	'articlefeedbackv5-field-objective-tooltip-5' => '完全没有偏见',
	'articlefeedbackv5-field-wellwritten-label' => '可读性',
	'articlefeedbackv5-field-wellwritten-tip' => '你觉得本条目内容的组织和撰写是否精心完美？',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => '不知所云',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => '难以理解',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => '比较清晰',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => '相当清晰',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => '非常清晰',
	'articlefeedbackv5-pitch-reject' => '以后再说',
	'articlefeedbackv5-pitch-or' => '或者',
	'articlefeedbackv5-pitch-thanks' => '谢谢！你的评分已保存。',
	'articlefeedbackv5-pitch-survey-message' => '请花些时间完成简短的调查。',
	'articlefeedbackv5-pitch-survey-accept' => '开始调查',
	'articlefeedbackv5-pitch-join-message' => '您要创建帐户吗？',
	'articlefeedbackv5-pitch-join-body' => '帐户将帮助您跟踪您所做的编辑，参与讨论，并成为社群的一分子。',
	'articlefeedbackv5-pitch-join-accept' => '创建帐户',
	'articlefeedbackv5-pitch-join-login' => '登录',
	'articlefeedbackv5-pitch-edit-message' => '您知道您可以编辑此页吗？',
	'articlefeedbackv5-pitch-edit-accept' => '编辑本页',
	'articlefeedbackv5-survey-message-success' => '谢谢您回答问卷。',
	'articlefeedbackv5-survey-message-error' => '出现错误。
请稍后再试。',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => '今日评分动态',
	'articleFeedbackv5-table-caption-dailyhighs' => '评分最高的条目：$1',
	'articleFeedbackv5-table-caption-dailylows' => '评分最低的条目：$1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => '本周最多更改',
	'articleFeedbackv5-table-caption-recentlows' => '近期低分',
	'articleFeedbackv5-table-heading-page' => '页面',
	'articleFeedbackv5-table-heading-average' => '平均',
	'articleFeedbackv5-copy-above-highlow-tables' => '这是一个实验性功能。请在 [$1 讨论页] 提供反馈意见。',
	'articlefeedbackv5-dashboard-bottom' => "'''注意'''：我们仍将尝试用各种不同的方式在面板上组织条目。目前，此面板包括下列条目：
* 最高或最低分的条目：在过去24小时内至少得到10次评分的条目。平均值计算以过去24小时内提交的所有评分为准。
* 近期低分：过去24小时内，在任何类别得到过70%或低分（2星或更低）的条目。只会展示在过去24小时内至少得到10次评分的条目。",
	'articlefeedbackv5-disable-preference' => '不在页面上显示条目评分工具',
	'articlefeedbackv5-emailcapture-response-body' => '您好！

谢谢您表示愿意帮助我们改善{{SITENAME}}。

请花一点时间，点击下面的链接来确认您的电子邮件：

$1

您还可以访问：

$2

然后输入下列确认码：

$3

我们会在短期内联系您，并向您介绍帮助我们改善{{SITENAME}}的方式。

如果这项请求并非由您发起，请忽略这封电子邮件，我们不会再向您发送任何邮件。

祝好，致谢，
{{SITENAME}}团队',
);

/** Traditional Chinese (‪中文(繁體)‬)
 * @author Anakmalaysia
 * @author Hydra
 * @author Mark85296341
 * @author Shizhao
 * @author Waihorace
 */
$messages['zh-hant'] = array(
	'articlefeedbackv5' => '條目評分公告板',
	'articlefeedbackv5-desc' => '條目評分',
	'articlefeedbackv5-survey-question-origin' => '在你開始這個調查的時候你在哪一頁？',
	'articlefeedbackv5-survey-question-whyrated' => '請讓我們知道你為什麼今天要評價本頁（選擇所有適用的項目）：',
	'articlefeedbackv5-survey-answer-whyrated-contribute-rating' => '我想對整體評分作出貢獻',
	'articlefeedbackv5-survey-answer-whyrated-development' => '我希望我的評分將積極影響發展的頁面',
	'articlefeedbackv5-survey-answer-whyrated-contribute-wiki' => '我想幫助 {{SITENAME}}',
	'articlefeedbackv5-survey-answer-whyrated-sharing-opinion' => '我喜歡分享我的意見',
	'articlefeedbackv5-survey-answer-whyrated-didntrate' => '我今天沒有進行評價，但我希望對此功能進行評價',
	'articlefeedbackv5-survey-answer-whyrated-other' => '其他',
	'articlefeedbackv5-survey-question-useful' => '你是否相信你提供的評價是有用而且清楚的？',
	'articlefeedbackv5-survey-question-useful-iffalse' => '為什麼？',
	'articlefeedbackv5-survey-question-comments' => '你有什麼其他意見？',
	'articlefeedbackv5-survey-submit' => '提交',
	'articlefeedbackv5-survey-title' => '請回答幾個問題',
	'articlefeedbackv5-survey-thanks' => '感謝您填寫此調查。',
	'articlefeedbackv5-survey-disclaimer' => '若要幫助我們改善此功能，您可以將您的反饋意見匿名分享給維基百科社區。',
	'articlefeedbackv5-error' => '發生了錯誤。請稍後再試。',
	'articlefeedbackv5-form-switch-label' => '評價本頁',
	'articlefeedbackv5-form-panel-title' => '評價本頁',
	'articlefeedbackv5-form-panel-explanation' => '這是什麼？',
	'articlefeedbackv5-form-panel-explanation-link' => 'Project:条目评分工具',
	'articlefeedbackv5-form-panel-clear' => '刪除本次評分',
	'articlefeedbackv5-form-panel-expertise' => '我非常了解與本主題相關的知識（可選）',
	'articlefeedbackv5-form-panel-expertise-studies' => '我有與其有關學院/大學學位',
	'articlefeedbackv5-form-panel-expertise-profession' => '這是我專業的一部分',
	'articlefeedbackv5-form-panel-expertise-hobby' => '這是一個深刻個人興趣',
	'articlefeedbackv5-form-panel-expertise-other' => '我的知識來源不在此列',
	'articlefeedbackv5-form-panel-helpimprove' => '我想幫助改善維基百科，請給我發送一封電子郵件（可選）',
	'articlefeedbackv5-form-panel-helpimprove-note' => '我們將向您發送確認電子郵件。我們不會與任何人分享您的地址。$1',
	'articlefeedbackv5-form-panel-helpimprove-privacy' => '隱私權政策',
	'articlefeedbackv5-form-panel-submit' => '提交評分',
	'articlefeedbackv5-form-panel-pending' => '你的評分尚未提交',
	'articlefeedbackv5-form-panel-success' => '保存成功',
	'articlefeedbackv5-form-panel-expiry-title' => '你的評分已過期',
	'articlefeedbackv5-form-panel-expiry-message' => '請重新評估本頁並重新評分。',
	'articlefeedbackv5-report-switch-label' => '查看本頁評分',
	'articlefeedbackv5-report-panel-title' => '本頁評分',
	'articlefeedbackv5-report-panel-description' => '目前平均評分。',
	'articlefeedbackv5-report-empty' => '無評分',
	'articlefeedbackv5-report-ratings' => '$1 評級',
	'articlefeedbackv5-field-trustworthy-label' => '可靠',
	'articlefeedbackv5-field-trustworthy-tip' => '你覺得這個頁面是否已經有足夠引文，以及這些引文是來自可靠來源嗎？',
	'articlefeedbackv5-field-trustworthy-tooltip-1' => '缺乏可靠來源',
	'articlefeedbackv5-field-trustworthy-tooltip-2' => '很少可靠来源',
	'articlefeedbackv5-field-trustworthy-tooltip-3' => '充足可靠來源',
	'articlefeedbackv5-field-trustworthy-tooltip-4' => '優質可靠來源',
	'articlefeedbackv5-field-trustworthy-tooltip-5' => '完美可靠来源',
	'articlefeedbackv5-field-complete-label' => '完成',
	'articlefeedbackv5-field-complete-tip' => '您覺得此頁內容基本上是否已經全面涵蓋了該主題相關的內容？',
	'articlefeedbackv5-field-complete-tooltip-1' => '缺少絕大多數信息',
	'articlefeedbackv5-field-complete-tooltip-2' => '包含一些信息',
	'articlefeedbackv5-field-complete-tooltip-3' => '包含關鍵信息，但還有缺少',
	'articlefeedbackv5-field-complete-tooltip-4' => '包含大部分關鍵的信息',
	'articlefeedbackv5-field-complete-tooltip-5' => '全面覆盖',
	'articlefeedbackv5-field-objective-label' => '客觀性',
	'articlefeedbackv5-field-objective-tip' => '你覺得本頁所顯示的觀點是否對本主題公平，能反映多方的意見？',
	'articlefeedbackv5-field-objective-tooltip-1' => '嚴重偏見',
	'articlefeedbackv5-field-objective-tooltip-2' => '有些偏見',
	'articlefeedbackv5-field-objective-tooltip-3' => '稍有偏見',
	'articlefeedbackv5-field-objective-tooltip-4' => '沒有明顯的偏見',
	'articlefeedbackv5-field-objective-tooltip-5' => '完全不帶偏見',
	'articlefeedbackv5-field-wellwritten-label' => '可讀性',
	'articlefeedbackv5-field-wellwritten-tip' => '你覺得此頁內容組織和撰寫是否完美？',
	'articlefeedbackv5-field-wellwritten-tooltip-1' => '不可理解',
	'articlefeedbackv5-field-wellwritten-tooltip-2' => '很難理解',
	'articlefeedbackv5-field-wellwritten-tooltip-3' => '足够清晰',
	'articlefeedbackv5-field-wellwritten-tooltip-4' => '清楚明確',
	'articlefeedbackv5-field-wellwritten-tooltip-5' => '非常清晰',
	'articlefeedbackv5-pitch-reject' => '也許以後再說',
	'articlefeedbackv5-pitch-or' => '或者',
	'articlefeedbackv5-pitch-thanks' => '謝謝！您的評分已保存。',
	'articlefeedbackv5-pitch-survey-message' => '請花一點時間來完成簡短的調查。',
	'articlefeedbackv5-pitch-survey-accept' => '開始調查',
	'articlefeedbackv5-pitch-join-message' => '你想要創建帳戶嗎？',
	'articlefeedbackv5-pitch-join-body' => '帳戶將幫助您跟蹤您所做的編輯，參與討論，並成為社區的一部分。',
	'articlefeedbackv5-pitch-join-accept' => '創建帳戶',
	'articlefeedbackv5-pitch-join-login' => '登入',
	'articlefeedbackv5-pitch-edit-message' => '您知道您可以編輯此頁嗎？',
	'articlefeedbackv5-pitch-edit-accept' => '編輯此頁',
	'articlefeedbackv5-survey-message-success' => '謝謝您回答問卷。',
	'articlefeedbackv5-survey-message-error' => '出現錯誤！
請稍後再試。',
	'articleFeedbackv5-table-caption-dailyhighsandlows' => '今天的新鮮事',
	'articleFeedbackv5-table-caption-dailyhighs' => '最高評級的頁面：$1',
	'articleFeedbackv5-table-caption-dailylows' => '最低評級的頁面：$1',
	'articleFeedbackv5-table-caption-weeklymostchanged' => '本週最多改變',
	'articleFeedbackv5-table-caption-recentlows' => '近期低點',
	'articleFeedbackv5-table-heading-page' => '頁面',
	'articleFeedbackv5-table-heading-average' => '平均',
	'articleFeedbackv5-copy-above-highlow-tables' => '這是一個試驗性的功能。請在[$1 討論頁]提供反饋意見。',
	'articlefeedbackv5-dashboard-bottom' => "'''注意'''：我們仍將嘗試用各種不同的方式在面板上組織條目。目前，此面板包括下列條目：
* 最高或最低分的頁面：在過去24小時內至少得到10次評分的條目。平均值計算以過去24小時內提交的所有評分為準。
* 近期低分：過去24小時內，在任何類別得到過70%或低分（2星或更低）的條目。只會展示在過去24小時內至少得到10次評分的條目。",
	'articlefeedbackv5-disable-preference' => '不在頁面顯示條目反饋部件',
	'articlefeedbackv5-emailcapture-response-body' => '您好！

謝謝您表示願意幫助我們改善{{SITENAME}}。

請花一點時間，點擊下面的鏈接來確認您的電子郵件：

$1

您還可以訪問：

$2

然後輸入下列確認碼：

$3

我們會在短期內聯繫您，並向您介紹幫助我們改善{{SITENAME}}的方式。

如果這項請求並非由您發起，請忽略這封電子郵件，我們不會再向您發送任何郵件。

祝好，致謝，
{{SITENAME}}團隊',
);

