-- This is used to reset our relevance scores for all items

-- reset to 0
UPDATE aft_article_feedback SET af_relevance_score = 0;

-- first we apply a negative relevance to everything without featured, unfeatured, resolved, unresolved, helpful, unhelpful or abuse count
-- this will push old items down if they don't have correct flagging
-- TODO: should this be limited by date so only old stuff gets the push down?
UPDATE aft_article_feedback
	SET af_relevance_score = -50
	WHERE (
		af_is_featured IS NOT TRUE OR
		af_is_unfeatured IS NOT TRUE OR
		af_is_resolved IS NOT TRUE OR
		af_is_unresolved IS NOT TRUE OR
		af_helpful_count > 0 OR
		af_unhelpful_count > 0 OR
		af_abuse_count > 0);

-- featured goes up 50
UPDATE aft_article_feedback SET af_relevance_score = 50 WHERE af_is_featured IS TRUE AND af_is_unfeatured IS FALSE;

-- If it's unfeatured we can ignore it, that would be -50 after 50 for featured which would be 0

-- resolved goes down -45
UPDATE aft_article_feedback SET af_relevance_score = af_relevance_score -45 WHERE af_is_resolved IS TRUE AND af_is_unresolved IS FALSE;

-- If it's unresolved we can ignore it, that would be 45 after -45 for featured which would be 0

-- add helpfulness, subtract unhelpfulness
UPDATE aft_article_feedback SET af_relevance_score = af_relevance_score + CONVERT(af_helpful_count, SIGNED) - CONVERT(af_unhelpful_count, SIGNED);

-- we can't tell which of the abusive flags were autoflags (it's not a separate value) so autoflags will count as regular flags for this
UPDATE aft_article_feedback SET af_relevance_score = af_relevance_score - (CONVERT(af_abuse_count, SIGNED) * 5);

-- now we fill the relevance_sort value with the flip of the relevance_score value
UPDATE aft_article_feedback SET af_relevance_sort = - af_relevance_score;