<?php
/**
 * This class represents "a list of data entries".
 *
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 */
class DataModelList extends Wikimedia\Rdbms\FakeResultWrapper {
	/**
	 * @var string
	 */
	protected $nextOffset = '';

	/**
	 * @var string
	 */
	protected $className;

	/**
	 * @var User
	 */
	protected $user;

	/**
	 * @param array $data Should be formed like array( array( 'id' => [id], 'shard' => [shard] ), ... )
	 * @param string $className the DataModel class
	 * @param User $user
	 */
	public function __construct( $data, $className, User $user ) {
		$this->result = $data;
		$this->className = $className;
		$this->user = $user;

		$this->preload();
	}

	public function __wakeup() {
		$this->preload();
	}

	protected function preload() {
		/** @var DataModel $class */
		$class = $this->className;
		$class::preload( $this->result, $this->user );
	}

	/**
	 * @return bool
	 */
	public function hasMore() {
		return $this->nextOffset !== '';
	}

	/**
	 * @return string
	 */
	public function nextOffset() {
		return $this->nextOffset;
	}

	/**
	 * @param string $offset
	 */
	public function setNextOffset( $offset ) {
		$this->nextOffset = $offset;
	}

	/**
	 * @return array|false
	 */
	public function doFetchRow() {
		$object = $this->doFetchObject();
		if ( $object ) {
			return $object->toArray();
		}
		return false;
	}

	/**
	 * @return DataModel|bool
	 */
	public function doFetchObject() {
		if ( isset( $this->result[$this->currentPos] ) ) {
			$this->currentRow = $this->result[$this->currentPos];

			$class = $this->className;
			return $class::get( $this->currentRow['id'], $this->currentRow['shard'] );
		}
		return false;
	}
}
