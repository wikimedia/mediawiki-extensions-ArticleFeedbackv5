ALTER TABLE /*_*/aft_article_feedback
DROP INDEX af_user_id_user_ip_created;

CREATE INDEX /*i*/af_user_id_user_ip ON /*_*/aft_article_feedback (af_user_id, af_user_ip);
