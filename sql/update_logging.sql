-- updates the AFT entries in logging table after late August's update to the data being logged
UPDATE logging
INNER JOIN page ON page_namespace = 0 AND page_title = SUBSTRING_INDEX(REPLACE(log_title, 'ArticleFeedbackv5/', ''), '/', 1)
SET log_params = CONCAT('a:2:{s:10:"feedbackId";i:', log_params, ';s:6:"pageId";i:', page_id, ';}')
WHERE log_title LIKE 'ArticleFeedbackv5/%' AND SUBSTR(log_params, 1, 5) <> 'a:2:{';
