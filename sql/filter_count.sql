DELETE FROM aft_article_filter_count;

-- relevant (featured OR comment OR helpful)
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'visible-relevant', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND ((af_is_featured IS TRUE OR af_has_comment IS TRUE OR af_net_helpfulness > 0) AND af_relevance_score > -5) AND af_is_deleted IS FALSE AND af_is_hidden IS FALSE GROUP BY af_page_id;

-- featured
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'visible-featured', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_is_featured IS TRUE AND af_is_deleted IS FALSE AND af_is_hidden IS FALSE GROUP BY af_page_id;

-- unfeatured
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'visible-unfeatured', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_is_unfeatured IS TRUE AND af_is_deleted IS FALSE AND af_is_hidden IS FALSE GROUP BY af_page_id;

-- resolved
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'visible-resolved', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_is_resolved IS TRUE AND af_is_deleted IS FALSE AND af_is_hidden IS FALSE GROUP BY af_page_id;

-- unresolved
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'visible-unresolved', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_is_unresolved IS TRUE AND af_is_deleted IS FALSE AND af_is_hidden IS FALSE GROUP BY af_page_id;

-- visible-comment
UPDATE aft_article_feedback, aft_article_answer SET af_has_comment = TRUE WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_id = aa_feedback_id AND aa_response_text IS NOT NULL;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'visible-comment', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_has_comment IS TRUE AND af_is_deleted IS FALSE AND af_is_hidden IS FALSE GROUP BY af_page_id;

-- helpful
UPDATE aft_article_feedback SET af_net_helpfulness = CONVERT(af_helpful_count, SIGNED) - CONVERT(af_unhelpful_count, SIGNED);
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'visible-helpful', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_net_helpfulness > 0 AND af_is_deleted IS FALSE AND af_is_hidden IS FALSE GROUP BY af_page_id;

-- unhelpful
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'visible-unhelpful', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_net_helpfulness > 0 AND af_is_deleted IS FALSE AND af_is_hidden IS FALSE GROUP BY af_page_id;

-- abusive
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'visible-abusive', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_abuse_count > 0 AND af_is_deleted IS FALSE AND af_is_hidden IS FALSE GROUP BY af_page_id;

-- hidden
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'notdeleted-hidden', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_is_hidden IS TRUE AND af_is_deleted IS FALSE GROUP BY af_page_id;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'all-hidden', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_is_hidden IS TRUE GROUP BY af_page_id;

-- unhidden
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'notdeleted-unhidden', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_is_unhidden IS TRUE AND af_is_deleted IS FALSE GROUP BY af_page_id;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'all-unhidden', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_is_unhidden IS TRUE GROUP BY af_page_id;

-- requested
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'notdeleted-requested', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_oversight_count > 0 AND af_is_deleted IS FALSE GROUP BY af_page_id;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'all-requested', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_oversight_count > 0 GROUP BY af_page_id;

-- unrequested
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'notdeleted-unrequested', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_is_unrequested IS TRUE AND af_is_deleted IS FALSE GROUP BY af_page_id;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'all-unrequested', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_is_unrequested IS TRUE GROUP BY af_page_id;

-- declined
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'notdeleted-declined', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_is_declined IS TRUE AND af_is_deleted IS FALSE GROUP BY af_page_id;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'all-declined', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_is_declined IS TRUE GROUP BY af_page_id;

-- oversighted
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'all-oversighted', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_is_deleted IS TRUE GROUP BY af_page_id;

-- unoversighted
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'all-unoversighted', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_is_undeleted IS TRUE GROUP BY af_page_id;

-- all/notdeleted/visible
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'visible', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_is_deleted IS FALSE AND af_is_hidden IS FALSE GROUP BY af_page_id;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'notdeleted', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_is_deleted IS FALSE GROUP BY af_page_id;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'all', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) GROUP BY af_page_id;

--
-- Central feedback page filters (page id = 0)
--

-- relevant (featured OR comment OR helpful)
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT 0, 'visible-relevant', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND ((af_is_featured IS TRUE OR af_has_comment IS TRUE OR af_net_helpfulness > 0) AND af_relevance_score > -5) AND af_is_deleted IS FALSE AND af_is_hidden IS FALSE;

-- featured
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT 0, 'visible-featured', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_is_featured IS TRUE AND af_is_deleted IS FALSE AND af_is_hidden IS FALSE;

-- unfeatured
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT 0, 'visible-unfeatured', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_is_unfeatured IS TRUE AND af_is_deleted IS FALSE AND af_is_hidden IS FALSE;

-- resolved
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT 0, 'visible-resolved', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_is_resolved IS TRUE AND af_is_deleted IS FALSE AND af_is_hidden IS FALSE;

-- unresolved
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT 0, 'visible-unresolved', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_is_unresolved IS TRUE AND af_is_deleted IS FALSE AND af_is_hidden IS FALSE;

-- visible-comment
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT 0, 'visible-comment', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_has_comment IS TRUE AND af_is_deleted IS FALSE AND af_is_hidden IS FALSE;

-- helpful
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT 0, 'visible-helpful', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_net_helpfulness > 0 AND af_is_deleted IS FALSE AND af_is_hidden IS FALSE;

-- unhelpful
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT 0, 'visible-unhelpful', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_net_helpfulness > 0 AND af_is_deleted IS FALSE AND af_is_hidden IS FALSE;

-- abusive
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT 0, 'visible-abusive', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_abuse_count > 0 AND af_is_deleted IS FALSE AND af_is_hidden IS FALSE;

-- hidden
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT 0, 'notdeleted-hidden', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_is_hidden IS TRUE AND af_is_deleted IS FALSE;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT 0, 'all-hidden', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_is_hidden IS TRUE;

-- unhidden
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT 0, 'notdeleted-unhidden', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_is_unhidden IS TRUE AND af_is_deleted IS FALSE;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT 0, 'all-unhidden', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_is_unhidden IS TRUE;

-- requested
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT 0, 'notdeleted-requested', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_oversight_count > 0 AND af_is_deleted IS FALSE;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT 0, 'all-requested', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_oversight_count > 0;

-- unrequested
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT 0, 'notdeleted-unrequested', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_is_unrequested IS TRUE AND af_is_deleted IS FALSE;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT 0, 'all-unrequested', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_is_unrequested IS TRUE;

-- declined
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT 0, 'notdeleted-declined', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_is_declined IS TRUE AND af_is_deleted IS FALSE;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT 0, 'all-declined', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_is_declined IS TRUE;

-- oversighted
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT 0, 'all-oversighted', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_is_deleted IS TRUE;

-- unoversighted
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT 0, 'all-unoversighted', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_is_undeleted IS TRUE;

-- all/notdeleted/visible
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT 0, 'visible', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_is_deleted IS FALSE AND af_is_hidden IS FALSE;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT 0, 'notdeleted', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 ) AND af_is_deleted IS FALSE;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT 0, 'all', COUNT(*) FROM aft_article_feedback WHERE ( af_form_id = 1 OR af_form_id = 6 );
