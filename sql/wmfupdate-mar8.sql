CREATE UNIQUE INDEX /*i*/afi_name ON /*_*/aft_article_field (afi_name);

ALTER TABLE aft_article_feedback
	DROP INDEX af_net_helpfulness,
	ADD INDEX af_page_net_helpfulness_af_id (af_page_id, af_id, af_net_helpfulness),
	CHANGE COLUMN af_needs_oversight af_oversight_count integer unsigned NOT NULL DEFAULT 0,
	ADD COLUMN af_has_comment BOOLEAN NOT NULL DEFAULT FALSE,
	ADD COLUMN af_is_unhidden BOOLEAN NOT NULL DEFAULT FALSE,
	ADD COLUMN af_is_undeleted BOOLEAN NOT NULL DEFAULT FALSE,
	ADD COLUMN af_is_declined BOOLEAN NOT NULL DEFAULT FALSE,
	ADD COLUMN af_activity_count integer unsigned NOT NULL DEFAULT 0,
	ADD COLUMN af_hide_user_id integer unsigned NOT NULL DEFAULT 0,
	ADD COLUMN af_oversight_user_id integer unsigned NOT NULL DEFAULT 0,
	ADD COLUMN af_hide_timestamp binary(14) NOT NULL DEFAULT '',
	ADD COLUMN af_oversight_timestamp binary(14) NOT NULL DEFAULT '';

-- Do this with a batched query:
--UPDATE aft_article_feedback SET af_net_helpfulness = CONVERT(af_helpful_count, SIGNED) - CONVERT(af_unhelpful_count, SIGNED);
--UPDATE aft_article_feedback, aft_article_answer SET af_has_comment = TRUE WHERE af_bucket_id = 1 AND af_id = aa_feedback_id AND aa_response_text IS NOT NULL;

