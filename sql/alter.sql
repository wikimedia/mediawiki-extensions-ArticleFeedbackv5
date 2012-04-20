-- 
--  This is a temporary file containing the changes necessary to bring prototype
--  up to speed; it will be removed when the upgrade process is built.
-- 

ALTER TABLE aft_article_field CHANGE afi_data_type afi_data_type ENUM('text', 'boolean', 'rating', 'option_id');
ALTER TABLE aft_article_field_option ADD COLUMN afo_field_id integer unsigned NOT NULL;

INSERT INTO aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('tag', 'option_id', 2);
INSERT INTO aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('comment', 'text', 2);
INSERT INTO aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('rating', 'rating', 3);
INSERT INTO aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('comment', 'text', 3);

INSERT INTO aft_article_field_option (afo_field_id, afo_name)
	SELECT afi_id, 'suggestion' FROM aft_article_field WHERE afi_name = 'tag' AND afi_bucket_id = 2;
INSERT INTO aft_article_field_option (afo_field_id, afo_name)
	SELECT afi_id, 'question' FROM aft_article_field WHERE afi_name = 'tag' AND afi_bucket_id = 2;
INSERT INTO aft_article_field_option (afo_field_id, afo_name)
	SELECT afi_id, 'problem' FROM aft_article_field WHERE afi_name = 'tag' AND afi_bucket_id = 2;
INSERT INTO aft_article_field_option (afo_field_id, afo_name)
	SELECT afi_id, 'praise' FROM aft_article_field WHERE afi_name = 'tag' AND afi_bucket_id = 2;

ALTER TABLE aft_article_feedback ADD COLUMN af_link_id integer unsigned NOT NULL DEFAULT 0;

ALTER TABLE aft_article_feedback ADD COLUMN af_abuse_count integer unsigned NOT NULL DEFAULT 0;
ALTER TABLE aft_article_feedback ADD COLUMN af_hide_count integer unsigned NOT NULL DEFAULT 0;

ALTER TABLE aft_article_feedback ADD COLUMN af_user_ip varchar(32); 
UPDATE aft_article_feedback SET af_user_ip = af_user_text WHERE af_user_text REGEXP '[0-9\.]+';
ALTER TABLE aft_article_feedback DROP COLUMN af_user_text;
ALTER TABLE aft_article_feedback DROP COLUMN af_modified;
ALTER TABLE aft_article_feedback MODIFY COLUMN af_created binary(14) NOT NULL DEFAULT '';

-- added 12/8 (greg)
ALTER TABLE aft_article_revision_feedback_select_rollup ADD COLUMN arfsr_field_id int NOT NULL;
ALTER TABLE aft_article_revision_feedback_ratings_rollup CHANGE COLUMN afrr_rating_id afrr_field_id integer unsigned NOT NULL;
ALTER TABLE aft_article_feedback_ratings_rollup CHANGE COLUMN arr_rating_id arr_field_id integer unsigned NOT NULL;
ALTER TABLE aft_article_feedback_select_rollup ADD COLUMN afsr_field_id int NOT NULL;

-- added 12/8 (later)
CREATE INDEX /*i*/af_page_feedback_id ON /*_*/aft_article_feedback (af_page_id, af_id);

-- aded 12/15
ALTER TABLE aft_article_revision_feedback_select_rollup DROP PRIMARY KEY;
ALTER TABLE aft_article_revision_feedback_select_rollup ADD PRIMARY KEY (arfsr_page_id, arfsr_field_id, arfsr_revision_id, arfsr_option_id);
ALTER TABLE aft_article_revision_feedback_ratings_rollup DROP PRIMARY KEY;
ALTER TABLE aft_article_revision_feedback_ratings_rollup ADD PRIMARY KEY (afrr_page_id, afrr_field_id, afrr_revision_id);

-- added 12/16 (Roan)
ALTER TABLE aft_article_feedback MODIFY COLUMN af_user_ip varchar(32) NULL;

-- added 1/13 (greg)
CREATE TABLE IF NOT EXISTS /*_*/aft_article_filter_count (
  afc_page_id      integer unsigned NOT NULL,
  afc_filter_name  varchar(64) NOT NULL,
  afc_filter_count integer unsigned NOT NULL,
  PRIMARY KEY (afc_page_id, afc_filter_name)
);

-- added 1/16 (greg)
ALTER TABLE aft_article_feedback ADD COLUMN af_helpful_count integer unsigned NOT NULL DEFAULT 0;
ALTER TABLE aft_article_feedback ADD COLUMN af_delete_count integer unsigned NOT NULL DEFAULT 0;


-- added 1/19 (greg)
ALTER TABLE aft_article_feedback ADD COLUMN af_unhelpful_count integer unsigned NOT NULL DEFAULT 0;

-- added  or updated 1/24 (greg)
ALTER TABLE aft_article_feedback ADD COLUMN af_needs_oversight boolean NOT NULL DEFAULT FALSE;

DELETE FROM aft_article_filter_count;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'helpful', COUNT(*) FROM aft_article_feedback WHERE CONVERT(af_helpful_count, SIGNED) - CONVERT(af_unhelpful_count, SIGNED) > 0 GROUP BY af_page_id;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'abusive', COUNT(*) FROM aft_article_feedback WHERE af_abuse_count > 0 GROUP BY af_page_id;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'invisible', COUNT(*) FROM aft_article_feedback WHERE af_hide_count > 0 GROUP BY af_page_id;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'visible', COUNT(*) FROM aft_article_feedback WHERE af_hide_count = 0 GROUP BY af_page_id;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'all', COUNT(*) FROM aft_article_feedback GROUP BY af_page_id;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'comment', COUNT(*) FROM aft_article_feedback, aft_article_answer WHERE af_id = aa_feedback_id AND aa_response_text IS NOT NULL GROUP BY af_page_id;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'deleted', COUNT(*) FROM aft_article_feedback WHERE af_delete_count > 0 GROUP BY af_page_id;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'unhelpful', COUNT(*) FROM aft_article_feedback WHERE CONVERT(af_helpful_count, SIGNED) - CONVERT(af_unhelpful_count, SIGNED) < 0 GROUP BY af_page_id;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'needsoversight', COUNT(*) FROM aft_article_feedback WHERE af_needs_oversight IS TRUE GROUP BY af_page_id;

-- added 1/26 (greg) - obviates much of the above from 1/24.
DELETE FROM aft_article_filter_count;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'helpful', COUNT(*) FROM aft_article_feedback WHERE af_bucket_id = 1 AND CONVERT(af_helpful_count, SIGNED) - CONVERT(af_unhelpful_count, SIGNED) > 0 GROUP BY af_page_id;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'abusive', COUNT(*) FROM aft_article_feedback WHERE af_bucket_id = 1 AND af_abuse_count > 0 GROUP BY af_page_id;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'invisible', COUNT(*) FROM aft_article_feedback WHERE af_bucket_id = 1 AND af_hide_count > 0 GROUP BY af_page_id;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'visible', COUNT(*) FROM aft_article_feedback WHERE af_bucket_id = 1 AND af_hide_count = 0 GROUP BY af_page_id;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'all', COUNT(*) FROM aft_article_feedback WHERE af_bucket_id = 1 GROUP BY af_page_id;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'comment', COUNT(*) FROM aft_article_feedback, aft_article_answer WHERE af_bucket_id = 1 AND af_id = aa_feedback_id AND aa_response_text IS NOT NULL GROUP BY af_page_id;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'deleted', COUNT(*) FROM aft_article_feedback WHERE af_bucket_id = 1 AND af_delete_count > 0 GROUP BY af_page_id;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'unhelpful', COUNT(*) FROM aft_article_feedback WHERE af_bucket_id = 1 AND CONVERT(af_helpful_count, SIGNED) - CONVERT(af_unhelpful_count, SIGNED) < 0 GROUP BY af_page_id;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'needsoversight', COUNT(*) FROM aft_article_feedback WHERE af_bucket_id = 1 AND af_needs_oversight IS TRUE GROUP BY af_page_id;

-- Note that this ignores the select rollups, since bucket 1 doesn't have any 
-- selects in it. Those tables can be truncated, or possibly dropped entirely,
-- if bucket 1 remains the only bucket. Holding off on that decision for now.
DELETE FROM aft_article_feedback_ratings_rollup;
DELETE FROM aft_article_revision_feedback_ratings_rollup;
INSERT INTO aft_article_revision_feedback_ratings_rollup (afrr_page_id, afrr_revision_id, afrr_field_id, afrr_total, afrr_count)  
SELECT af_page_id, af_revision_id, aa_field_id, SUM(aa_response_boolean), COUNT(aa_response_boolean) 
FROM aft_article_feedback, aft_article_answer
WHERE af_bucket_id = 1 AND af_id = aa_feedback_id AND aa_response_boolean IS NOT NULL
GROUP BY af_page_id, af_revision_id;
INSERT INTO aft_article_feedback_ratings_rollup (arr_page_id, arr_field_id, arr_total, arr_count)
SELECT afrr_page_id, afrr_field_id, SUM(afrr_total), SUM(afrr_count)
FROM aft_article_revision_feedback_ratings_rollup
GROUP BY afrr_page_id;

-- Added 1/27 (greg)
ALTER TABLE aft_article_feedback CHANGE COLUMN af_delete_count af_is_deleted BOOLEAN NOT NULL DEFAULT FALSE;
ALTER TABLE aft_article_feedback CHANGE COLUMN af_hide_count af_is_hidden BOOLEAN NOT NULL DEFAULT FALSE;

-- Added later 1/27 (greg) 
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'invisible', COUNT(*) FROM aft_article_feedback WHERE af_bucket_id = 1 AND af_is_hidden IS TRUE GROUP BY af_page_id;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'visible', COUNT(*) FROM aft_article_feedback WHERE af_bucket_id = 1 AND af_is_hidden IS FALSE AND af_is_deleted IS FALSE GROUP BY af_page_id;
INSERT INTO aft_article_filter_count(afc_page_id, afc_filter_name, afc_filter_count) SELECT af_page_id, 'deleted', COUNT(*) FROM aft_article_feedback WHERE af_bucket_id = 1 AND af_is_deleted IS TRUE GROUP BY af_page_id;

-- Added 1/31 (Roan)
CREATE INDEX /*i*/afo_field_id ON /*_*/aft_article_field_option (afo_field_id);

-- Added 1/31 (greg)
ALTER TABLE /*_*/aft_article_feedback ADD COLUMN af_net_helpfulness integer NOT NULL DEFAULT 0;
CREATE INDEX /*i*/af_net_helpfulness ON /*_*/aft_article_feedback (af_net_helpfulness);
UPDATE aft_article_feedback SET af_net_helpfulness = CONVERT(af_helpful_count, SIGNED) - CONVERT(af_unhelpful_count, SIGNED);

-- Added 2/1 (greg)
CREATE INDEX /*_*/af_net_helpfulness_af_id ON /*_*/aft_article_feedback (af_id, af_net_helpfulness);

-- Added 2/14 (emsmith)
ALTER TABLE /*_*/aft_article_feedback CHANGE COLUMN af_needs_oversight af_oversight_count integer unsigned NOT NULL DEFAULT 0;
ALTER TABLE /*_*/aft_article_feedback ADD COLUMN af_has_comment BOOLEAN NOT NULL DEFAULT FALSE;
ALTER TABLE /*_*/aft_article_feedback ADD COLUMN af_is_unhidden BOOLEAN NOT NULL DEFAULT FALSE;
ALTER TABLE /*_*/aft_article_feedback ADD COLUMN af_is_undeleted BOOLEAN NOT NULL DEFAULT FALSE;
ALTER TABLE /*_*/aft_article_feedback ADD COLUMN af_is_declined BOOLEAN NOT NULL DEFAULT FALSE;
ALTER TABLE /*_*/aft_article_feedback ADD COLUMN af_activity_count integer unsigned NOT NULL DEFAULT 0;
ALTER TABLE /*_*/aft_article_feedback ADD COLUMN af_hide_user_id integer unsigned NOT NULL DEFAULT 0;
ALTER TABLE /*_*/aft_article_feedback ADD COLUMN af_oversight_user_id integer unsigned NOT NULL DEFAULT 0;
ALTER TABLE /*_*/aft_article_feedback ADD COLUMN af_hide_timestamp binary(14) NOT NULL DEFAULT '';
ALTER TABLE /*_*/aft_article_feedback ADD COLUMN af_oversight_timestamp binary(14) NOT NULL DEFAULT '';

-- set has_comment appropriately from current values
UPDATE aft_article_feedback, aft_article_answer SET af_has_comment = TRUE WHERE af_bucket_id = 1 AND af_id = aa_feedback_id AND aa_response_text IS NOT NULL;

-- Added 3/5 (emsmith)\
DROP INDEX /*_*/af_net_helpfulness_af_id;
CREATE INDEX /*_*/af_net_helpfulness_af_id ON /*_*/aft_article_feedback (af_net_helpfulness, af_id);

-- make sure all old feedback has dates, even if they're wrong
UPDATE aft_article_feedback SET af_hide_timestamp = NOW() WHERE af_is_hidden IS TRUE AND af_hide_timestamp = '';
UPDATE aft_article_feedback SET af_oversight_timestamp = NOW() WHERE af_is_deleted IS TRUE AND af_oversight_timestamp = '';

-- Added 3/29 (reha)
ALTER TABLE /*_*/aft_article_feedback CHANGE COLUMN af_bucket_id af_form_id INTEGER UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE /*_*/aft_article_feedback ADD COLUMN af_experiment varchar(32) NULL;
CREATE INDEX /*_*/af_experiment ON /*_*/aft_article_feedback (af_experiment);
UPDATE /*_*/aft_article_feedback SET af_experiment = af_form_id WHERE DATE(af_created) <= '2012-03-21';
UPDATE /*_*/aft_article_feedback SET af_experiment = CONCAT(af_form_id, 'A') WHERE DATE(af_created) > '2012-03-21' AND af_link_id = 1;
UPDATE /*_*/aft_article_feedback SET af_experiment = CONCAT(af_form_id, 'E') WHERE DATE(af_created) > '2012-03-21' AND af_link_id = 5;
UPDATE /*_*/aft_article_feedback SET af_experiment = CONCAT(af_form_id, '?') WHERE DATE(af_created) > '2012-03-21' AND af_link_id = 0;

-- Added 3/30 (emsmith)
ALTER TABLE /*_*/aft_article_feedback ADD COLUMN af_suppress_count integer unsigned NOT NULL DEFAULT 0;

-- Added 4/3 (emsmith)
ALTER TABLE /*_*/aft_article_feedback ADD COLUMN af_last_status enum('hidden', 'autohide', 'unhidden', 'deleted', 'undeleted', 'request', 'unrequest', 'declined', 'autoflag') NULL;
ALTER TABLE /*_*/aft_article_feedback ADD COLUMN af_last_status_user_id integer unsigned NOT NULL DEFAULT 0;
ALTER TABLE /*_*/aft_article_feedback ADD COLUMN af_last_status_timestamp binary(14) NOT NULL DEFAULT '';

UPDATE /*_*/aft_article_feedback SET af_last_status_timestamp = af_oversight_timestamp, af_last_status_user_id = af_oversight_user_id, af_last_status = 'deleted' WHERE af_is_deleted IS TRUE;
UPDATE /*_*/aft_article_feedback SET af_last_status_timestamp = af_hide_timestamp, af_last_status_user_id = af_hide_user_id, af_last_status = 'hidden' WHERE af_is_hidden IS TRUE;

ALTER TABLE /*_*/aft_article_feedback ADD COLUMN af_is_autohide BOOLEAN NOT NULL DEFAULT FALSE;
ALTER TABLE /*_*/aft_article_feedback ADD COLUMN af_is_unrequested BOOLEAN NOT NULL DEFAULT FALSE;
UPDATE /*_*/aft_article_feedback SET af_is_autohide = 1 WHERE af_is_hidden IS TRUE AND af_hide_user_id < 1;
UPDATE /*_*/aft_article_feedback SET af_last_status = 'autohide' WHERE af_is_autohide IS TRUE;

ALTER TABLE /*_*/aft_article_feedback DROP COLUMN af_hide_timestamp;
ALTER TABLE /*_*/aft_article_feedback DROP COLUMN af_hide_user_id;
ALTER TABLE /*_*/aft_article_feedback DROP COLUMN af_oversight_timestamp;
ALTER TABLE /*_*/aft_article_feedback DROP COLUMN af_oversight_user_id;

-- Added 4/17 (reha)
ALTER TABLE /*_*/aft_article_feedback CHANGE COLUMN af_last_status af_last_status varchar(16) NULL;

-- Added 4/5 (emsmith)
ALTER TABLE /*_*/aft_article_feedback ADD COLUMN af_is_featured BOOLEAN NOT NULL DEFAULT FALSE;
ALTER TABLE /*_*/aft_article_feedback ADD COLUMN af_is_unfeatured BOOLEAN NOT NULL DEFAULT FALSE;
ALTER TABLE /*_*/aft_article_feedback ADD COLUMN af_is_resolved BOOLEAN NOT NULL DEFAULT FALSE;
ALTER TABLE /*_*/aft_article_feedback ADD COLUMN af_is_unresolved BOOLEAN NOT NULL DEFAULT FALSE;
ALTER TABLE /*_*/aft_article_feedback ADD COLUMN af_relevance_score integer signed NOT NULL DEFAULT 0;
ALTER TABLE /*_*/aft_article_feedback ADD COLUMN af_relevance_sort integer signed NOT NULL DEFAULT 0;
CREATE INDEX /*i*/af_relevance_sort_af_id ON /*_*/aft_article_feedback (af_relevance_sort, af_id);