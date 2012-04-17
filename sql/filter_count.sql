DELETE FROM aft_article_filter_count;

-- visible-comment
UPDATE aft_article_feedback, aft_article_answer SET af_has_comment = TRUE WHERE af_form_id = 1 AND af_id = aa_feedback_id AND aa_response_text IS NOT NULL;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'visible-comment', COUNT(*) FROM aft_article_feedback WHERE af_form_id = 1 AND af_has_comment IS TRUE AND af_is_deleted IS FALSE AND af_is_hidden IS FALSE GROUP BY af_page_id;

-- helpful
UPDATE aft_article_feedback SET af_net_helpfulness = CONVERT(af_helpful_count, SIGNED) - CONVERT(af_unhelpful_count, SIGNED);
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'visible-helpful', COUNT(*) FROM aft_article_feedback WHERE af_form_id = 1 AND af_net_helpfulness > 0 AND af_is_deleted IS FALSE AND af_is_hidden IS FALSE GROUP BY af_page_id;

-- unhelpful
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'visible-unhelpful', COUNT(*) FROM aft_article_feedback WHERE af_form_id = 1 AND af_net_helpfulness > 0 AND af_is_deleted IS FALSE AND af_is_hidden IS FALSE GROUP BY af_page_id;

-- abusive
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'visible-abusive', COUNT(*) FROM aft_article_feedback WHERE af_form_id = 1 AND af_abuse_count > 0 AND af_is_deleted IS FALSE AND af_is_hidden IS FALSE GROUP BY af_page_id;

-- hidden
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'notdeleted-hidden', COUNT(*) FROM aft_article_feedback WHERE af_form_id = 1 AND af_is_hidden IS TRUE AND af_is_deleted IS FALSE GROUP BY af_page_id;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'all-hidden', COUNT(*) FROM aft_article_feedback WHERE af_form_id = 1 AND af_is_hidden IS TRUE GROUP BY af_page_id;

-- unhidden
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'notdeleted-unhidden', COUNT(*) FROM aft_article_feedback WHERE af_form_id = 1 AND af_is_unhidden IS TRUE AND af_is_deleted IS FALSE GROUP BY af_page_id;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'all-unhidden', COUNT(*) FROM aft_article_feedback WHERE af_form_id = 1 AND af_is_unhidden IS TRUE GROUP BY af_page_id;

-- requested
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'notdeleted-requested', COUNT(*) FROM aft_article_feedback WHERE af_form_id = 1 AND af_oversight_count > 0 AND af_is_deleted IS FALSE GROUP BY af_page_id;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'all-requested', COUNT(*) FROM aft_article_feedback WHERE af_form_id = 1 AND af_oversight_count > 0 GROUP BY af_page_id;

-- unrequested
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'notdeleted-unrequested', COUNT(*) FROM aft_article_feedback WHERE af_form_id = 1 AND af_is_unrequested IS TRUE AND af_is_deleted IS FALSE GROUP BY af_page_id;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'all-unrequested', COUNT(*) FROM aft_article_feedback WHERE af_form_id = 1 AND af_is_unrequested IS TRUE GROUP BY af_page_id;

-- declined
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'notdeleted-declined', COUNT(*) FROM aft_article_feedback WHERE af_form_id = 1 AND af_is_declined IS TRUE AND af_is_deleted IS FALSE GROUP BY af_page_id;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'all-declined', COUNT(*) FROM aft_article_feedback WHERE af_form_id = 1 AND af_is_declined IS TRUE GROUP BY af_page_id;

-- oversighted
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'all-oversighted', COUNT(*) FROM aft_article_feedback WHERE af_form_id = 1 AND af_is_deleted IS TRUE GROUP BY af_page_id;

-- unoversighted
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'all-unoversighted', COUNT(*) FROM aft_article_feedback WHERE af_form_id = 1 AND af_is_undeleted IS TRUE GROUP BY af_page_id;

-- all/notdeleted/visible
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'visible', COUNT(*) FROM aft_article_feedback WHERE af_form_id = 1 AND af_is_deleted IS FALSE AND af_is_hidden IS FALSE GROUP BY af_page_id;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'notdeleted', COUNT(*) FROM aft_article_feedback WHERE af_form_id = 1 AND af_is_deleted IS FALSE GROUP BY af_page_id;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'all', COUNT(*) FROM aft_article_feedback WHERE af_form_id = 1 GROUP BY af_page_id;
