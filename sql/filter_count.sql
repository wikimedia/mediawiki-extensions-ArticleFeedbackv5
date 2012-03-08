DELETE FROM aft_article_filter_count;

-- all includes oversighted and hidden
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'all', COUNT(*) FROM aft_article_feedback WHERE af_bucket_id = 1 GROUP BY af_page_id;

-- notdeleted includes all but oversighted
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'notdeleted', COUNT(*) FROM aft_article_feedback WHERE af_bucket_id = 1 AND af_is_deleted IS FALSE GROUP BY af_page_id;

-- has text in the comment
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'comment', COUNT(*) FROM aft_article_feedback, aft_article_answer WHERE af_bucket_id = 1 AND af_id = aa_feedback_id AND aa_response_text IS NOT NULL  AND af_is_hidden IS FALSE AND af_is_deleted IS FALSE GROUP BY af_page_id;

-- oversighted
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'deleted', COUNT(*) FROM aft_article_feedback WHERE af_bucket_id = 1 AND af_is_deleted IS TRUE GROUP BY af_page_id;
-- unoversighted
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'undeleted', COUNT(*) FROM aft_article_feedback WHERE af_bucket_id = 1 AND af_is_deleted IS FALSE AND af_is_undeleted IS TRUE GROUP BY af_page_id;

-- visible (all not hidden)
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'visible', COUNT(*) FROM aft_article_feedback WHERE af_bucket_id = 1 AND af_is_hidden IS FALSE AND af_is_deleted IS FALSE GROUP BY af_page_id;
-- invisible (hidden)
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'invisible', COUNT(*) FROM aft_article_feedback WHERE af_bucket_id = 1 AND af_is_hidden IS TRUE GROUP BY af_page_id;
-- once was hidden, now is visible (unhidden)
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'unhidden', COUNT(*) FROM aft_article_feedback WHERE af_bucket_id = 1 AND af_is_hidden IS FALSE AND af_is_unhidden IS TRUE GROUP BY af_page_id;

-- abusive - not hidden/deleted and has flag count
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'abusive', COUNT(*) FROM aft_article_feedback WHERE af_bucket_id = 1 AND af_abuse_count > 0 AND af_is_hidden IS FALSE AND af_is_deleted IS FALSE GROUP BY af_page_id;

-- needs oversight - has at least one oversight request
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'needsoversight', COUNT(*) FROM aft_article_feedback WHERE af_bucket_id = 1 AND af_oversight_count > 0 GROUP BY af_page_id;

-- declined - had oversight declined 
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'declined', COUNT(*) FROM aft_article_feedback WHERE af_bucket_id = 1 AND af_is_declined IS TRUE GROUP BY af_page_id;

-- helpful and unhelpful counts
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'helpful', COUNT(*) FROM aft_article_feedback WHERE af_bucket_id = 1 AND CONVERT(af_helpful_count, SIGNED) - CONVERT(af_unhelpful_count, SIGNED) > 0  AND af_is_hidden IS FALSE AND af_is_deleted IS FALSE GROUP BY af_page_id;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'unhelpful', COUNT(*) FROM aft_article_feedback WHERE af_bucket_id = 1 AND CONVERT(af_helpful_count, SIGNED) - CONVERT(af_unhelpful_count, SIGNED) < 0  AND af_is_hidden IS FALSE AND af_is_deleted IS FALSE GROUP BY af_page_id;
