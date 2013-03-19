-- legacy AFTv5 entries had auto-incrementing ids
-- current entries' ids are built using UIDGenerator::newTimestampedUID128( 16 )
-- both will result in a value that increases as time increases, but to make
-- sure that such id-based sort resembles a time-based sort, we have to
-- left-pad the legacy entries with null bytes (instead of right-pad, in which
-- case 789\0\0\0\0\0\0... would be > than 45b6e2349...)
-- Even only to accurately sort the legacy ids, this needs to be done, or
-- 123\0\0\0\0\0\0 would be < than 45\0\0\0\0\0\0\0
UPDATE aft_feedback SET aft_id = LPAD(TRIM(TRAILING "\0" FROM aft_id), 32, "\0");
