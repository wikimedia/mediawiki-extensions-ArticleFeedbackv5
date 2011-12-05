-- TODO: Take the drops out before release, these are just for convenience while we're developing.
DROP TABLE IF EXISTS /*_*/aft_article_feedback;
DROP TABLE IF EXISTS /*_*/aft_article_field_group;
DROP TABLE IF EXISTS /*_*/aft_article_field;
DROP TABLE IF EXISTS /*_*/aft_article_field_option;
DROP TABLE IF EXISTS /*_*/aft_article_answer;
DROP TABLE IF EXISTS /*_*/aft_article_feedback_ratings_rollup;
DROP TABLE IF EXISTS /*_*/aft_article_revision_feedback_ratings_rollup;
DROP TABLE IF EXISTS /*_*/aft_article_feedback_select_rollup;
DROP TABLE IF EXISTS /*_*/aft_article_revision_feedback_select_rollup;
DROP TABLE IF EXISTS /*_*/aft_article_hits;
DROP TABLE IF EXISTS /*_*/aft_article_feedback_properties;

CREATE TABLE IF NOT EXISTS /*_*/aft_article_feedback (
  -- Row ID (primary key)
  af_id               integer unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
  -- Foreign key to page.page_id
  af_page_id         integer unsigned NOT NULL,
  -- User Id (0 if anon)
  af_user_id         integer NOT NULL,
  -- Username or IP address
  af_user_text       varbinary(255) NOT NULL,
  -- Unique token for anonymous users (to facilitate ratings from multiple users on the same IP)
  af_user_anon_token varbinary(32) NOT NULL DEFAULT '',
  -- Foreign key to revision.rev_id
  af_revision_id     integer unsigned NOT NULL,
  -- Which feedback widget the user was given. Default of 0 is "none".
  af_bucket_id       integer unsigned NOT NULL DEFAULT 0,
  -- Which CTA widget was displayed to the user. 0 is "none",
  -- Which would come up if they got the edit page CTA, and couldn't edit.
  af_cta_id          integer unsigned NOT NULL DEFAULT 0,
  -- Which link the user clicked on to get to the widget. Default of 0 is "none".
  af_link_id         integer unsigned NOT NULL DEFAULT 0,
  af_created         timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  af_modified        timestamp NULL
) /*$wgDBTableOptions*/;
CREATE INDEX /*i*/af_page_user_token_id ON /*_*/aft_article_feedback (af_page_id, af_user_text, af_user_anon_token, af_id);
CREATE INDEX /*i*/af_revision_id ON /*_*/aft_article_feedback (af_revision_id);
-- Create an index on the article_feedback.af_timestamp field
CREATE INDEX /*i*/article_feedback_timestamp ON /*_*/aft_article_feedback (af_created);
CREATE INDEX /*i*/af_page_id ON /*_*/aft_article_feedback (af_page_id, af_created);

CREATE TABLE IF NOT EXISTS /*_*/aft_article_field_group (
  afg_id   integer unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
  afg_name varchar(255) NOT NULL UNIQUE
) /*$wgDBTableOptions*/;

-- We already used af_ above, so this is ArticleFIeld instead
CREATE TABLE IF NOT EXISTS /*_*/aft_article_field (
  afi_id        integer unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
  afi_name      varchar(255) NOT NULL,
  afi_data_type ENUM('text', 'boolean', 'rating', 'option_id'),
  -- FKey to article_field_groups.group_id
  afi_group_id  integer unsigned NULL,
  afi_bucket_id integer unsigned NOT NULL
) /*$wgDBTableOptions*/;

CREATE TABLE IF NOT EXISTS /*_*/aft_article_field_option (
  afo_option_id integer unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
  afo_field_id  integer unsigned NOT NULL,
  afo_name      varchar(255) NOT NULL
) /*$wgDBTableOptions*/;

CREATE TABLE IF NOT EXISTS /*_*/aft_article_answer (
  -- FKEY to article_feedback.af_id)
  aa_feedback_id        integer unsigned NOT NULL,
  -- FKEY to article_field.afi_id)
  aa_field_id           integer unsigned NOT NULL,
  aa_response_rating    integer NULL,
  aa_response_text      text NULL,
  aa_response_boolean   boolean NULL,
  -- FKey to article_field_options.afo_option_id)
  aa_response_option_id integer unsigned NULL,
  PRIMARY KEY (aa_feedback_id, aa_field_id)
) /*$wgDBTableOptions*/;

CREATE TABLE IF NOT EXISTS /*_*/aft_article_feedback_ratings_rollup (
  arr_page_id   integer unsigned NOT NULL,
  arr_rating_id integer unsigned NOT NULL,
  arr_total     integer unsigned NOT NULL,
  arr_count     integer unsigned NOT NULL,
  PRIMARY KEY (arr_page_id, arr_rating_id)
) /*$wgDBTableOptions*/;

CREATE TABLE IF NOT EXISTS /*_*/aft_article_revision_feedback_ratings_rollup (
  afrr_page_id      integer unsigned NOT NULL,
  afrr_revision_id  integer unsigned NOT NULL,
  afrr_rating_id    integer unsigned NOT NULL,
  afrr_total        integer unsigned NOT NULL,
  afrr_count        integer unsigned NOT NULL,
  PRIMARY KEY (afrr_page_id, afrr_rating_id, afrr_revision_id)
) /*$wgDBTableOptions*/;

CREATE TABLE IF NOT EXISTS /*_*/aft_article_feedback_select_rollup (
  afsr_page_id   integer unsigned NOT NULL,
  afsr_option_id integer unsigned NOT NULL,
  afsr_total     integer unsigned NOT NULL,
  afsr_count     integer unsigned NOT NULL,
  PRIMARY KEY (afsr_page_id, afsr_option_id)
) /*$wgDBTableOptions*/;

CREATE TABLE IF NOT EXISTS /*_*/aft_article_revision_feedback_select_rollup (
  arfsr_page_id     integer unsigned NOT NULL,
  arfsr_revision_id integer unsigned NOT NULL,
  arfsr_option_id   integer unsigned NOT NULL,
  arfsr_total       integer unsigned NOT NULL,
  arfsr_count       integer unsigned NOT NULL,
  PRIMARY KEY (arfsr_revision_id, arfsr_option_id)
) /*$wgDBTableOptions*/;

-- Mostly taken from AFTv4
CREATE TABLE IF NOT EXISTS /*_*/aft_article_feedback_properties (
  -- Keys to article_feedback.aa_id
  afp_feedback_id integer unsigned NOT NULL,
  -- Key/value pair - allow text or numerical metadata
  afp_key        varbinary(255) NOT NULL,
  afp_value_int  integer signed NOT NULL,
  afp_value_text varbinary(255) DEFAULT '' NOT NULL,
  PRIMARY KEY (afp_feedback_id, afp_key)
) /*$wgDBTableOptions*/;

-- TODO: Add indices

INSERT INTO aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('found', 'boolean', 1);
INSERT INTO aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('comment', 'text', 1);
INSERT INTO aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('tag', 'option_id', 2);
INSERT INTO aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('comment', 'text', 2);
INSERT INTO aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('rating', 'rating', 3);
INSERT INTO aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('comment', 'text', 3);
INSERT INTO aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('trustworthy', 'rating', 5);
INSERT INTO aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('objective', 'rating', 5);
INSERT INTO aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('complete', 'rating', 5);
INSERT INTO aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('wellwritten', 'rating', 5);
INSERT INTO aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('expertise-general', 'boolean', 5);
INSERT INTO aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('expertise-studies', 'boolean', 5);
INSERT INTO aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('expertise-profession', 'boolean', 5);
INSERT INTO aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('expertise-hobby', 'boolean', 5);
INSERT INTO aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('expertise-other', 'boolean', 5);

INSERT INTO aft_article_field_option (afo_field_id, afo_name)
	SELECT afi_id, 'suggestion' FROM aft_article_field WHERE afi_name = 'tag' AND afi_bucket_id = 2;
INSERT INTO aft_article_field_option (afo_field_id, afo_name)
	SELECT afi_id, 'question' FROM aft_article_field WHERE afi_name = 'tag' AND afi_bucket_id = 2;
INSERT INTO aft_article_field_option (afo_field_id, afo_name)
	SELECT afi_id, 'problem' FROM aft_article_field WHERE afi_name = 'tag' AND afi_bucket_id = 2;
INSERT INTO aft_article_field_option (afo_field_id, afo_name)
	SELECT afi_id, 'praise' FROM aft_article_field WHERE afi_name = 'tag' AND afi_bucket_id = 2;

