ALTER TABLE /*_*/aft_feedback
  ADD COLUMN aft_percentage_helpful integer NOT NULL DEFAULT 0;

UPDATE /*_*/aft_feedback
  SET aft_percentage_helpful = IF(aft_helpful + aft_unhelpful > 0, aft_helpful / ( aft_helpful + aft_unhelpful ) * 100, 0);
