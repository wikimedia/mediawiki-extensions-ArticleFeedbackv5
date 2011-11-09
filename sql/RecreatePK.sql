-- Add aa_id as the new primary key to article_feedback and drop the old one.
-- Also change the indexing while we're at it

-- In order to safely change the primary key even in replicated environments,
-- we have to create a new table with the new structure, copy over the data,
-- then rename the table. This is to ensure that the values of aa_id are
-- consistent across all slaves.

-- Create new table
-- Would've used CREATE TABLE ... LIKE here but SQLite doesn't support ALTER TABLE ... DROP PRIMARY KEY
-- so we're stuck with duplicating the table structure.
CREATE TABLE /*_*/article_feedback2 (
  aa_id integer unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
  aa_page_id integer unsigned NOT NULL,
  aa_user_id integer NOT NULL,
  aa_user_text varbinary(255) NOT NULL,
  aa_user_anon_token varbinary(32) NOT NULL DEFAULT '',
  aa_revision integer unsigned NOT NULL,
  aa_timestamp binary(14) NOT NULL DEFAULT '',
  aa_rating_id int unsigned NOT NULL,
  aa_rating_value int unsigned NOT NULL,
  aa_design_bucket int unsigned NOT NULL DEFAULT 0
) /*$wgDBTableOptions*/;
CREATE INDEX /*i*/aa_page_user_token_id ON /*_*/article_feedback2 (aa_page_id, aa_user_text, aa_user_anon_token, aa_id);
CREATE INDEX /*i*/aa_revision ON /*_*/article_feedback2 (aa_revision);
CREATE INDEX /*i*/article_feedback_timestamp ON /*_*/article_feedback2 (aa_timestamp);

-- Copy the data, ordered by the old primary key
-- Need to specify the fields explicitly to avoid confusion with aa_id
INSERT INTO /*_*/article_feedback2
	(aa_page_id, aa_user_id, aa_user_text, aa_user_anon_token, aa_revision, aa_timestamp, aa_rating_id, aa_rating_value, aa_design_bucket)
	SELECT aa_page_id, aa_user_id, aa_user_text, aa_user_anon_token, aa_revision, aa_timestamp, aa_rating_id, aa_rating_value, aa_design_bucket
	FROM /*_*/article_feedback
	ORDER BY aa_revision, aa_user_text, aa_rating_id, aa_user_anon_token;

-- Drop the old table and rename the new table to the old name
DROP TABLE /*_*/article_feedback;
ALTER TABLE /*_*/article_feedback2 RENAME TO /*_*/article_feedback;
