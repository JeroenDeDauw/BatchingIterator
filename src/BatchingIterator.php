<?php

namespace BatchingIterator;

use InvalidArgumentException;

/**
 * Iterator that batches calls for additional values.
 *
 * This is useful as a foundation for iterators over data that is
 * in an expensive to access location, such as a database or a web API.
 *
 * @since 1.0
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class BatchingIterator implements \Iterator {

	/**
	 * @var BatchingFetcher
	 */
	private $fetcher;

	/**
	 * @var int
	 */
	private $maxBatchSize = 10;

	/**
	 * @var mixed|null
	 */
	private $current = null;

	/**
	 * @var mixed[]
	 */
	private $currentBatch = [];

	/**
	 * @var int
	 */
	private $key;

	/**
	 * @param BatchingFetcher $fetcher
	 */
	public function __construct( BatchingFetcher $fetcher ) {
		$this->fetcher = $fetcher;
	}

	/**
	 * @param int $maxBatchSize
	 *
	 * @throws InvalidArgumentException
	 */
	public function setMaxBatchSize( $maxBatchSize ) {
		if ( !is_int( $maxBatchSize ) || $maxBatchSize < 1 ) {
			throw new InvalidArgumentException( '$maxBatchSize should be an int bigger than 0.' );
		}

		$this->maxBatchSize = $maxBatchSize;
	}

	/**
	 * Returns a value of the type returned by the BatchingFetcher,
	 * or null if there are no further values.
	 *
	 * @return mixed|null
	 */
	public function current() {
		return $this->current;
	}

	public function next() {
		$value = array_shift( $this->currentBatch );

		if ( $value === null ) {
			$this->nextValueFromNewBatch();
		}
		else {
			$this->current = $value;
			$this->key++;
		}
	}

	private function nextValueFromNewBatch() {
		$this->currentBatch = $this->fetcher->fetchNext( $this->maxBatchSize );

		if ( empty( $this->currentBatch ) ) {
			$this->current = null;
		}
		else {
			$this->next();
		}
	}

	/**
	 * @return int
	 */
	public function key() {
		return $this->key;
	}

	/**
	 * @return bool
	 */
	public function valid() {
		return $this->current !== null;
	}

	public function rewind() {
		$this->fetcher->rewind();
		$this->key = -1;
		$this->next();
	}

}
