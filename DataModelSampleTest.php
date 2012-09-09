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
		for ( $i = 0; $i < 5; $i++ ) {
			$sample = new DataModelSample;
			$sample->shard = $i;
			$sample->title = "Test #$i";
			$sample->email = "mmullie@wikimedia.org";
			$sample->visible = rand( 0, 1 );

			$sample->insert();
		}

		// attempt inserting a couple of valid entries, using save()
		for ( $i = 5; $i < 10; $i++ ) {
			$sample = new DataModelSample;
			$sample->shard = $i;
			$sample->title = "Test #$i";
			$sample->email = "mmullie@wikimedia.org";
			$sample->visible = rand( 0, 1 );

			$sample->save();
		}

		// attempt fetching a couple of entries
		for ( $i = 1; $i < 3; $i++ ) {
			$sample = DataModelSample::loadFromId( $i, $i );
			var_dump($sample);
		}

		// attempt updating a couple of valid entries, using update()
		for ( $i = 1; $i < 3; $i++ ) {
			$sample = DataModelSample::loadFromId( $i, $i );
			$sample->title = "Test #$i, revised";

			$sample->update();
		}

		// attempt updating a couple of valid entries, using save()
		for ( $i = 5; $i < 8; $i++ ) {
			$sample = DataModelSample::loadFromId( $i, $i );
			$sample->title = "Test #$i, revised";

			$sample->save();
		}

		// attempt fetching a batch of hidden entries, sorted by timestamp DESC
		$list = DataModelSample::getList( 'hidden', 0, 'DESC' );
		var_dump( $list );

		// attempt to change all hidden entries to visible
		foreach ( $list as $sample ) {
			$sample->visible = 1;

			$sample->save();
		}

		// attempt fetching a batch of hidden entries, sorted by timestamp DESC
		$list = DataModelSample::getList( 'hidden', 0, 'DESC' );
		var_dump( $list );
	}
}
