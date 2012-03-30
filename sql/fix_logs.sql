-- move oversight items into suppress log
UPDATE logging SET log_type = 'suppress' WHERE log_type = 'articlefeedbackv5' AND log_action IN ('oversight', 'unoversight', 'decline', 'request', 'unrequest');

-- update counts for feedback - note this might run awhile
UPDATE aft_article_feedback f1
   INNER JOIN page p3 ON f1.af_page_id=p3.page_id
   SET f1.af_activity_count = COALESCE((SELECT COUNT(*) FROM logging l2
                                    WHERE log_type = 'articlefeedbackv5'
                                      AND l2.log_title REGEXP CONCAT('/', p3.page_title, '/', f1.af_id, '$')
                                      GROUP BY l2.log_title), 0);

UPDATE aft_article_feedback f1
   INNER JOIN page p3 ON f1.af_page_id=p3.page_id
   SET f1.af_suppress_count = COALESCE((SELECT COUNT(*) FROM logging l2
                                    WHERE l2.log_type = 'suppress'
                                       AND l2.log_action IN ('oversight', 'unoversight', 'decline', 'request', 'unrequest')
                                       AND l2.log_title REGEXP CONCAT('/', p3.page_title, '/', f1.af_id, '$')
                                      GROUP BY l2.log_title), 0);
