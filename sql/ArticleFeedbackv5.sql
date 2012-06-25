-- Stores feedback records: "user X submitted feedback on page Y, at time Z"
CREATE TABLE IF NOT EXISTS /*_*/aft_article_feedback (
  -- Row ID (primary key)
  af_id              integer unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
  -- Foreign key to page.page_id
  af_page_id         integer unsigned NOT NULL,
  -- User Id (0 if anon), and ip address
  af_user_id         integer NOT NULL,
  af_user_ip         varchar(32) NULL,
  -- Unique token for anonymous users (to facilitate ratings from multiple users on the same IP)
  af_user_anon_token varbinary(32) NOT NULL DEFAULT '',
  -- Foreign key to revision.rev_id
  af_revision_id     integer unsigned NOT NULL,
  -- Which feedback widget the user was given. Default of 0 is "none".
  af_form_id         integer unsigned NOT NULL DEFAULT 0,
  -- Which CTA widget was displayed to the user. 0 is "none",
  -- Which would come up if they got the edit page CTA, and couldn't edit.
  af_cta_id          integer unsigned NOT NULL DEFAULT 0,
  -- Which link the user clicked on to get to the widget. Default of 0 is "none".
  af_link_id         integer unsigned NOT NULL DEFAULT 0,
  -- Which experiment this feedback is a part of (matches clicktracking).
  af_experiment      varchar(32) NULL,
  -- Creation timetamp
  af_created         binary(14) NOT NULL DEFAULT '',
  -- Number of times the feedback was hidden or marked as abusive.
  -- or flagged as helpful or unhelpful.
  af_abuse_count     integer unsigned NOT NULL DEFAULT 0,
  af_helpful_count   integer unsigned NOT NULL DEFAULT 0,
  af_unhelpful_count integer unsigned NOT NULL DEFAULT 0,
  -- Net helpfulness (helpful - unhelpful). Used in fetch query.
  af_net_helpfulness integer NOT NULL DEFAULT 0,
  -- Keep track of requests for oversight on the item
  af_oversight_count integer unsigned NOT NULL DEFAULT 0,
  -- Flag a message as being hidden or being deleted
  af_is_deleted      boolean NOT NULL DEFAULT FALSE,
  af_is_hidden       boolean NOT NULL DEFAULT FALSE,
  -- Keep track of items that have been unhidden, undeleted (unoversighted)
  -- or had oversight declined - note this is cleared when the item is
  -- rehidden, reoversighted, or has oversight requested again
  af_is_unhidden      boolean NOT NULL DEFAULT FALSE,
  af_is_undeleted     boolean NOT NULL DEFAULT FALSE,
  af_is_declined      boolean NOT NULL DEFAULT FALSE,
  af_is_unrequested   boolean NOT NULL DEFAULT FALSE,
  -- keep track of "this has a comment" for filtering purposes (avoids a join)
  af_has_comment      boolean NOT NULL DEFAULT FALSE,
  -- Keep track of number of activities (hide/show/flag/unflag)
  -- or suppress log items
  -- should be equivalent to counting rows in logging table
  af_activity_count  integer unsigned NOT NULL DEFAULT 0,
  af_suppress_count  integer unsigned NOT NULL DEFAULT 0,
  -- keep track of flagging for feature/unfeature resolve/unresolve
  af_is_featured BOOLEAN NOT NULL DEFAULT FALSE,
  af_is_unfeatured BOOLEAN NOT NULL DEFAULT FALSE,
  af_is_resolved BOOLEAN NOT NULL DEFAULT FALSE,
  af_is_unresolved BOOLEAN NOT NULL DEFAULT FALSE,
  -- TWO relevance scores here, a positive and a negative version so we can do a proper sort index
  af_relevance_score integer signed NOT NULL DEFAULT 0,
  af_relevance_sort integer signed NOT NULL DEFAULT 0,
  -- keep the user id of the status event of the feedback
  -- only registered users can do this, which is why no ips
  -- data used on the overlay status line
  af_last_status varchar(16) NULL,
  af_last_status_user_id integer unsigned NOT NULL DEFAULT 0,
  af_last_status_timestamp binary(14) NOT NULL DEFAULT '',
  af_last_status_notes varchar(255) NULL,
  -- flag if this was an autohide operation
  af_is_autohide boolean NOT NULL DEFAULT FALSE
) /*$wgDBTableOptions*/;
CREATE INDEX /*i*/af_page_user_token_id ON /*_*/aft_article_feedback (af_page_id, af_user_id, af_user_anon_token, af_id);
CREATE INDEX /*i*/af_revision_id ON /*_*/aft_article_feedback (af_revision_id);
-- Create an index on the article_feedback.af_timestamp field
CREATE INDEX /*i*/article_feedback_timestamp ON /*_*/aft_article_feedback (af_created);
CREATE INDEX /*i*/af_page_id ON /*_*/aft_article_feedback (af_page_id, af_created);
CREATE INDEX /*i*/af_page_feedback_id ON /*_*/aft_article_feedback (af_page_id, af_id);
CREATE INDEX /*i*/af_page_net_helpfulness_af_id ON /*_*/aft_article_feedback (af_page_id, af_net_helpfulness, af_id);
CREATE INDEX /*i*/af_relevance_sort_af_id ON /*_*/aft_article_feedback (af_relevance_sort, af_id);
CREATE INDEX /*i*/af_user_id_user_ip_created ON /*_*/aft_article_feedback (af_user_id, af_user_ip, af_created);

-- Allows for organizing fields into fieldsets, for reporting or rendering.
-- A group is just a name and an ID.
CREATE TABLE IF NOT EXISTS /*_*/aft_article_field_group (
  afg_id   integer unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
  afg_name varchar(255) NOT NULL UNIQUE
) /*$wgDBTableOptions*/;

-- Stores article fields, zero or more of which are used by each feedback widget
-- We already used af_ as a prefix above, so this is afi_ instead
CREATE TABLE IF NOT EXISTS /*_*/aft_article_field (
  afi_id        integer unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
  afi_name      varchar(255) NOT NULL,
  -- Allowed data types - relates directly to which aa_response_* field gets 
  -- set in aft_article_answer, and where we check for answers when fetching
  afi_data_type ENUM('text', 'boolean', 'rating', 'option_id'),
  -- FKey to article_field_groups.group_id
  afi_group_id  integer unsigned NULL,
  -- Which 'bucket' this field should be rendered in.
  afi_bucket_id integer unsigned NOT NULL
) /*$wgDBTableOptions*/;
CREATE INDEX /*i*/afi_name ON /*_*/aft_article_field (afi_name);

-- Stores options for multi-value feedback fields (ie, select boxes) 
CREATE TABLE IF NOT EXISTS /*_*/aft_article_field_option (
  afo_option_id integer unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
  -- foreign key to aft_article_field.afi_id
  afo_field_id  integer unsigned NOT NULL,
  afo_name      varchar(255) NOT NULL
) /*$wgDBTableOptions*/;
CREATE INDEX /*i*/afo_field_id ON /*_*/aft_article_field_option (afo_field_id);

-- Stores individual answers for each feedback record - for a given feedback 
-- record, what did the user answer for each individual question/input on 
-- the form with which they were presented.
CREATE TABLE IF NOT EXISTS /*_*/aft_article_answer (
  -- FKEY to article_feedback.af_id)
  aa_feedback_id        integer unsigned NOT NULL,
  -- FKEY to article_field.afi_id)
  aa_field_id           integer unsigned NOT NULL,
  -- Only one of these four columns will be non-null, based on the afi_data_type
  -- of the aa_field_id related to this record.
  aa_response_rating    integer NULL,
  aa_response_text      varchar(255) NULL,
  -- FKey to aft_article_answer_text.aat_id)
  aat_id                integer unsigned NULL,
  aa_response_boolean   boolean NULL,
  -- FKey to article_field_options.afo_option_id)
  aa_response_option_id integer unsigned NULL,
  -- Only allow one answer per field per feedback ID.
  PRIMARY KEY (aa_feedback_id, aa_field_id)
) /*$wgDBTableOptions*/;

-- Stores only long feedback text (> 255 bytes) for a given feedback record.
-- aat_response_text contains the content that under normal conditions (short
-- comment of under 255 bytes) is inserted in aft_article_answer.aa_response_text
CREATE TABLE IF NOT EXISTS /*_*/aft_article_answer_text (
  aat_id            integer unsigned NOT NULL AUTO_INCREMENT,
  aat_response_text text NOT NULL,
  PRIMARY KEY (aat_id)
) /*$wgDBTableOptions*/;

-- These next four are rollup tables used by the articlefeedback special page.
-- The revision tables store per-revision numers, as we (in meetings with WMF)
-- agreed that per-revision numbers could be useful in reporting, though
-- they aren't currently used on the feedback page. The page-level ones only
-- count back to wgArticleFeedbackv5RatingLifetime, so they're a rolling window.
--
-- There are tables for ratings and select (ratings includes booleans as well),
-- because while the value of the rating/boolean is important (Rated 3/5), for
-- selects we only want the count for each input, not the value of that input or
-- the sum of the values (which will be numerical option_ids, not meaningful 
-- rating values). The queries were sufficiently different that we deemed multiple
-- tables worthwhile.
CREATE TABLE IF NOT EXISTS /*_*/aft_article_feedback_ratings_rollup (
  arr_page_id   integer unsigned NOT NULL,
  arr_field_id  integer unsigned NOT NULL,
  arr_total     integer unsigned NOT NULL,
  arr_count     integer unsigned NOT NULL,
  PRIMARY KEY (arr_page_id, arr_field_id)
) /*$wgDBTableOptions*/;

CREATE TABLE IF NOT EXISTS /*_*/aft_article_revision_feedback_ratings_rollup (
  afrr_page_id      integer unsigned NOT NULL,
  afrr_revision_id  integer unsigned NOT NULL,
  afrr_field_id     integer unsigned NOT NULL,
  afrr_total        integer unsigned NOT NULL,
  afrr_count        integer unsigned NOT NULL,
  PRIMARY KEY (afrr_page_id, afrr_field_id, afrr_revision_id)
) /*$wgDBTableOptions*/;

CREATE TABLE IF NOT EXISTS /*_*/aft_article_feedback_select_rollup (
  afsr_page_id   integer unsigned NOT NULL,
  afsr_option_id integer unsigned NOT NULL,
  afsr_field_id  integer unsigned NOT NULL,
  afsr_total     integer unsigned NOT NULL,
  afsr_count     integer unsigned NOT NULL,
  PRIMARY KEY (afsr_page_id, afsr_option_id)
) /*$wgDBTableOptions*/;

CREATE TABLE IF NOT EXISTS /*_*/aft_article_revision_feedback_select_rollup (
  arfsr_page_id     integer unsigned NOT NULL,
  arfsr_revision_id integer unsigned NOT NULL,
  arfsr_option_id   integer unsigned NOT NULL,
  arfsr_field_id    integer unsigned NOT NULL,
  arfsr_total       integer unsigned NOT NULL,
  arfsr_count       integer unsigned NOT NULL,
  PRIMARY KEY (arfsr_page_id, arfsr_field_id, arfsr_revision_id, arfsr_option_id)
) /*$wgDBTableOptions*/;

-- Exists to provide counts on filters for the feedback page, and toggle the "more" button"
CREATE TABLE IF NOT EXISTS /*_*/aft_article_filter_count (
  -- Keys to page.page_id
  afc_page_id      integer unsigned NOT NULL,
  -- The name of the filter (must be matched by the fitler select on feedback page)
  afc_filter_name  varchar(64) NOT NULL,
  -- Number of aft_article_feedback records that match this filter.
  afc_filter_count integer unsigned NOT NULL,
  PRIMARY KEY (afc_page_id, afc_filter_name)
);

-- Directly taken from AFTv4
CREATE TABLE IF NOT EXISTS /*_*/aft_article_feedback_properties (
  -- Keys to article_feedback.aa_id
  afp_feedback_id integer unsigned NOT NULL,
  -- Key/value pair - allow text or numerical metadata
  afp_key        varbinary(255) NOT NULL,
  afp_value_int  integer signed NOT NULL,
  afp_value_text varbinary(255) DEFAULT '' NOT NULL,
  PRIMARY KEY (afp_feedback_id, afp_key)
) /*$wgDBTableOptions*/;

CREATE INDEX /*i*/af_page_revision ON /*_*/aft_article_feedback (af_page_id, af_revision_id);
CREATE INDEX /*i*/afi_data_type ON /*_*/aft_article_field (afi_data_type);
CREATE INDEX /*i*/aa_feedback_field_option ON /*_*/aft_article_answer (aa_feedback_id, aa_field_id, aa_response_option_id);

INSERT INTO /*_*/aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('found', 'boolean', 1);
INSERT INTO /*_*/aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('comment', 'text', 1);
INSERT INTO /*_*/aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('tag', 'option_id', 2);
INSERT INTO /*_*/aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('comment', 'text', 2);
INSERT INTO /*_*/aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('rating', 'rating', 3);
INSERT INTO /*_*/aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('comment', 'text', 3);
INSERT INTO /*_*/aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('trustworthy', 'rating', 5);
INSERT INTO /*_*/aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('objective', 'rating', 5);
INSERT INTO /*_*/aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('complete', 'rating', 5);
INSERT INTO /*_*/aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('wellwritten', 'rating', 5);
INSERT INTO /*_*/aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('expertise-general', 'boolean', 5);
INSERT INTO /*_*/aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('expertise-studies', 'boolean', 5);
INSERT INTO /*_*/aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('expertise-profession', 'boolean', 5);
INSERT INTO /*_*/aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('expertise-hobby', 'boolean', 5);
INSERT INTO /*_*/aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('expertise-other', 'boolean', 5);
INSERT INTO /*_*/aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('found', 'boolean', 6);
INSERT INTO /*_*/aft_article_field(afi_name, afi_data_type, afi_bucket_id) VALUES ('comment', 'text', 6);

INSERT INTO /*_*/aft_article_field_option (afo_field_id, afo_name)
	SELECT afi_id, 'suggestion' FROM /*_*/aft_article_field WHERE afi_name = 'tag' AND afi_bucket_id = 2;
INSERT INTO /*_*/aft_article_field_option (afo_field_id, afo_name)
	SELECT afi_id, 'question' FROM /*_*/aft_article_field WHERE afi_name = 'tag' AND afi_bucket_id = 2;
INSERT INTO /*_*/aft_article_field_option (afo_field_id, afo_name)
	SELECT afi_id, 'problem' FROM /*_*/aft_article_field WHERE afi_name = 'tag' AND afi_bucket_id = 2;
INSERT INTO /*_*/aft_article_field_option (afo_field_id, afo_name)
	SELECT afi_id, 'praise' FROM /*_*/aft_article_field WHERE afi_name = 'tag' AND afi_bucket_id = 2;

