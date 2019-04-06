<?php
/**
 * This class represents "a list of data entries".
 *
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 */
class DataModelList extends FakeResultWrapper {
	/**
	 * @var string
	 */
	protected $nextOffset = '';

	/**
	 * @var string
	 */
	protected $className;

	/**
	 * @param array $data Should be formed like array( array( 'id' => [id], 'shard' => [shard] ), ... )
	 * @param string $className the DataModel class
	 */
	public function __construct( $data, $className ) {
		$this->result = $data;
		$this->className = $className;

		$this->preload();
	}

	public function __wakeup() {
		$this->preload();
	}

	protected function preload() {
		$class = $this->className;
		$class::preload( $this->result );
	}

	/**
	 * @return bool
	 */
	public function hasMore() {
		return $this->nextOffset !== '';
	}

	/**
	 * @return int
	 */
	public function nextOffset() {
		return $this->nextOffset;
	}

	/**
	 * @param int $offset
	 */
	public function setNextOffset( $offset ) {
		$this->nextOffset = $offset;
	}

	function next() {
		$this->pos++;
		return $this->fetchObject();
	}

	/**
	 * @return int
	 */
	function current() {
		return $this->fetchObject();
	}

	/**
	 * @return array|bool
	 */
	public function fetchRow() {
		$object = $this->fetchObject();
		if ( $object ) {
			return $object->toArray();
		}
		return false;
	}

	/**
	 * @return DataModel|bool
	 */
	public function fetchObject() {
		if ( isset( $this->result[$this->pos] ) ) {
			$this->currentRow = $this->result[$this->pos];

			$class = $this->className;
			return $class::get( $this->currentRow['id'], $this->currentRow['shard'] );
		}
		return false;
	}
}
