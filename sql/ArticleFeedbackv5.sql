CREATE TABLE IF NOT EXISTS /*_*/aft_feedback (
  -- id is no auto-increment, but a in PHP generated unique value
  id varbinary(32) NOT NULL PRIMARY KEY,
  page integer unsigned NOT NULL,
  page_revision integer unsigned NOT NULL,
  user integer unsigned NOT NULL,
  user_text varbinary(255) NOT NULL DEFAULT '',
  user_token varbinary(32) NOT NULL DEFAULT '',
  form varchar(1) NOT NULL DEFAULT '',
  cta varchar(1) NOT NULL DEFAULT '',
  link varchar(1) NOT NULL DEFAULT '',
  rating tinyint(1) NOT NULL,
  comment varbinary(255) NOT NULL DEFAULT '',
  timestamp binary(14) NOT NULL DEFAULT '',
  oversight integer unsigned NOT NULL DEFAULT 0,
  decline integer unsigned NOT NULL DEFAULT 0,
  request integer unsigned NOT NULL DEFAULT 0,
  hide integer unsigned NOT NULL DEFAULT 0,
  autohide integer unsigned NOT NULL DEFAULT 0,
  flag integer unsigned NOT NULL DEFAULT 0,
  autoflag integer unsigned NOT NULL DEFAULT 0,
  feature integer unsigned NOT NULL DEFAULT 0,
  resolve integer unsigned NOT NULL DEFAULT 0,
  helpful integer unsigned NOT NULL DEFAULT 0,
  unhelpful integer unsigned NOT NULL DEFAULT 0,
  net_helpful integer NOT NULL DEFAULT 0
  relevance_score integer NOT NULL DEFAULT 0
) /*$wgDBTableOptions*/;

-- sort indexes
CREATE INDEX /*i*/relevance ON /*_*/aft_feedback (relevance_score, id);
CREATE INDEX /*i*/age ON /*_*/aft_feedback (timestamp, id);
CREATE INDEX /*i*/helpful ON /*_*/aft_feedback (net_helpful, id);

-- separate table to hold longer text comments
CREATE TABLE IF NOT EXISTS /*_*/aft_feedback_blob (
  -- id is no auto-increment, but a in PHP generated unique value
  id varbinary(32) NOT NULL PRIMARY KEY,
  comment mediumblob NOT NULL DEFAULT ''
) /*$wgDBTableOptions*/;
