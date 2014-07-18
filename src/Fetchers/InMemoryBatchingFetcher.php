<?php

namespace BatchingIterator\Fetchers;

use BatchingIterator\BatchingFetcher;

/**
 * Trivial implementation of BatchingFetcher that uses an in-memory array.
 * Values are returned in first in, first out fashion.
 * (In other words: an adapter from array to BatchingFetcher)
 *
 * @since 1.0
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class InMemoryBatchingFetcher implements BatchingFetcher {

	private $values;

	public function __construct( array $values ) {
		$this->values = $values;
	}

	/**
	 * @see BatchingFetcher::fetchNext
	 *
	 * @param int $maxFetchCount
	 *
	 * @return mixed[]
	 */
	public function fetchNext( $maxFetchCount ) {
		$values = array();

		while ( !is_null( key( $this->values ) ) && --$maxFetchCount >= 0 ) {
			$values[] = current( $this->values );
			next( $this->values );
		}

		return $values;
	}

	/**
	 * @see BatchingFetcher::rewind
	 */
	public function rewind() {
		reset( $this->values );
	}

}