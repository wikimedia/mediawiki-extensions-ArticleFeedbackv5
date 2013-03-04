CREATE TABLE IF NOT EXISTS /*_*/aft_feedback (
  -- id is no auto-increment, but a in PHP generated unique value
  aft_id binary(32) NOT NULL PRIMARY KEY,
  aft_page integer unsigned NOT NULL,
  aft_page_revision integer unsigned NOT NULL,
  aft_user integer unsigned NOT NULL,
  aft_user_text varchar(255) binary NOT NULL DEFAULT '',
  aft_user_token varbinary(32) NOT NULL DEFAULT '',
  aft_form binary(1) NOT NULL DEFAULT '',
  aft_cta binary(1) NOT NULL DEFAULT '',
  aft_link binary(1) NOT NULL DEFAULT '',
  aft_rating boolean NOT NULL,
  aft_comment mediumblob NOT NULL DEFAULT '',
  aft_timestamp varbinary(14) NOT NULL DEFAULT '',
  aft_oversight boolean NOT NULL DEFAULT 0,
  aft_decline boolean NOT NULL DEFAULT 0,
  aft_request boolean NOT NULL DEFAULT 0,
  aft_hide boolean NOT NULL DEFAULT 0,
  aft_autohide boolean NOT NULL DEFAULT 0,
  aft_flag integer unsigned NOT NULL DEFAULT 0,
  aft_autoflag boolean NOT NULL DEFAULT 0,
  aft_feature boolean NOT NULL DEFAULT 0,
  aft_resolve boolean NOT NULL DEFAULT 0,
  aft_noaction boolean NOT NULL DEFAULT 0,
  aft_helpful integer unsigned NOT NULL DEFAULT 0,
  aft_unhelpful integer unsigned NOT NULL DEFAULT 0,
  aft_has_comment boolean NOT NULL DEFAULT 0,
  aft_net_helpful integer NOT NULL DEFAULT 0,
  aft_relevance_score integer NOT NULL DEFAULT 0
) /*$wgDBTableOptions*/;

-- sort indexes
CREATE INDEX /*i*/relevance ON /*_*/aft_feedback (aft_relevance_score, aft_id, aft_has_comment, aft_oversight, aft_hide);
CREATE INDEX /*i*/age ON /*_*/aft_feedback (aft_timestamp, aft_id, aft_has_comment, aft_oversight, aft_hide);
CREATE INDEX /*i*/helpful ON /*_*/aft_feedback (aft_net_helpful, aft_id, aft_has_comment, aft_oversight, aft_hide);
