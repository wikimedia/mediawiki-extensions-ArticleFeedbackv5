ALTER TABLE /*_*/aft_feedback
  ADD COLUMN aft_noaction boolean NOT NULL DEFAULT 0 AFTER aft_feature;
