-- move oversight items into suppress log
UPDATE logging SET log_type = 'suppress' WHERE log_type = 'articlefeedbackv5' AND log_action IN ('oversight', 'unoversight', 'decline', 'request', 'unrequest');

-- update counts for feedback - note this might run awhile
UPDATE aft_article_feedback
   INNER JOIN page ON af_page_id=page_id
   SET af_activity_count = COALESCE((SELECT COUNT(*) FROM logging
                                    WHERE log_type = 'articlefeedbackv5'
                                      AND log_namespace = -1
                                      AND log_title = CONCAT('ArticleFeedbackv5/', page_title, '/', af_id)
                                      GROUP BY log_title), 0)
    WHERE page_namespace = 0;

UPDATE aft_article_feedback
   INNER JOIN page ON af_page_id=page_id
   SET af_activity_count = COALESCE((SELECT COUNT(*) FROM logging
                                    WHERE log_type = 'suppress'
                                      AND log_action IN ('oversight', 'unoversight', 'decline', 'request', 'unrequest')
                                      AND log_namespace = -1
                                      AND log_title = CONCAT('ArticleFeedbackv5/', page_title, '/', af_id)
                                      GROUP BY log_title), 0)
    WHERE page_namespace = 0;
