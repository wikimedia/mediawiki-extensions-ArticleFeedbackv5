<?php
/**
 * This class will test the datamodel sample.
 *
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 */
class DataModelSampleTest {
	// @todo: refactor this shit into nice unit tests ;)
	public function __construct() {
		// attempt inserting a couple of valid entries, using insert()
		for ( $i = 1; $i <= 5; $i++ ) {
			$sample = new DataModelSample;
			$sample->shard = $i % 3;
			$sample->title = "Test #$i";
			$sample->email = "mmullie@wikimedia.org";
			$sample->visible = rand( 0, 1 );

			$sample->insert();
		}

		// attempt inserting a couple of valid entries, using save()
		for ( $i = 6; $i <= 10; $i++ ) {
			$sample = new DataModelSample;
			$sample->shard = $i % 3;
			$sample->title = "Test #$i";
			$sample->email = "mmullie@wikimedia.org";
			$sample->visible = rand( 0, 1 );

			$sample->save();
		}

		// attempt fetching a couple of entries
		echo 'fetching 3 entries:';
		for ( $i = 1; $i <= 3; $i++ ) {
			$sample = DataModelSample::get( $i, $i % 3 );
			var_dump($sample);
		}

		// attempt updating a couple of valid entries, using update()
		for ( $i = 1; $i <= 3; $i++ ) {
			$sample = DataModelSample::get( $i, $i % 3 );

			if ( $sample ) {
				$sample->title = "Test #$i, revised";

				$sample->update();
			}
		}

		// attempt updating a couple of valid entries, using save()
		for ( $i = 5; $i < 8; $i++ ) {
			$sample = DataModelSample::get( $i, $i % 3 );

			if ( $sample ) {
				$sample->title = "Test #$i, revised";

				$sample->save();
			}
		}

		// attempt fetching a batch of hidden entries, sorted by timestamp DESC
		echo 'fetching a batch of hidden entries (should contain some):';
		$list = DataModelSample::getList( 'hidden', null, 0, 'DESC' );
		var_dump( $list );

		// attempt to change all hidden entries to visible
		foreach ( $list as $sample ) {
			$sample->visible = 1;

			$sample->save();
		}

		// attempt fetching a batch of hidden entries, sorted by timestamp DESC
		echo 'fetching a batch of hidden entries: (should contain none)';
		$list = DataModelSample::getList( 'hidden', null, 0, 'DESC' );
		var_dump( $list );

		// attempt fetching all entries on shard 1, sorted by title ASC
		echo 'fetching a batch of entries on shard 1: (should contain 4)';
		$list = DataModelSample::getList( 'all', 1, 0, 'ASC' );
		var_dump( $list );

		// attempt to get the amount of entries
		echo 'fetching the total amount of entries: (should be 10)';
		$count = DataModelSample::getCount( 'all' );
		var_dump( $count );

		// attempt to get the amount of visible entries on shard 1
		echo 'fetching the total amount of visible entries on shard 1: (should be 4)';
		$count = DataModelSample::getCount( 'visible', 1 );
		var_dump( $count );
	}
}
