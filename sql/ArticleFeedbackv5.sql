CREATE TABLE IF NOT EXISTS /*_*/aft_feedback (
  -- id is basically auto-increment, but we'll handle that ourselves in PHP
  -- because the data may be sharded over multiple servers (and then we don't
  -- want both to generate their own id's)
  id integer unsigned NOT NULL PRIMARY KEY,
  page integer unsigned NOT NULL,
  page_revision integer unsigned NOT NULL,
  user integer unsigned NOT NULL,
  user_text varchar(255) NOT NULL DEFAULT '',
  user_token varbinary(32) NOT NULL DEFAULT '',
  form varchar(1) NOT NULL DEFAULT '',
  cta varchar(1) NOT NULL DEFAULT '',
  link varchar(1) NOT NULL DEFAULT '',
  rating tinyint(1) NOT NULL,
  comment mediumblob NOT NULL DEFAULT '',
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
  relevance_score integer NOT NULL DEFAULT 0
) /*$wgDBTableOptions*/;

-- don't need additional indexes; data will always be fetched based on ID;
-- selections (e.g. "all oversighted entries") will not be made by performing
-- queries on this table, but by - upon saving data - evaluating all possible
-- selection criteria in php & save the data in another, datamodel-lists, table
