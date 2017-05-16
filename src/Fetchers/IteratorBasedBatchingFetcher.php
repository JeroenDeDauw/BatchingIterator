<?php

declare( strict_types = 1 );

namespace BatchingIterator\Fetchers;

use BatchingIterator\BatchingFetcher;

/**
 * Trivial implementation of BatchingFetcher that gets its values from an iterator.
 * Values are returned in first in, first out fashion.
 * (In other words: an adapter from Iterator to BatchingFetcher)
 *
 * @since 2.0
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class IteratorBasedBatchingFetcher implements BatchingFetcher {

	private $iterator;

	public function __construct( \Iterator $iterator ) {
		$this->iterator = $iterator;
	}

	/**
	 * @see BatchingFetcher::fetchNext
	 *
	 * @param int $maxFetchCount
	 *
	 * @return mixed[]
	 */
	public function fetchNext( int $maxFetchCount ): array {
		$values = [];

		while ( !is_null( $this->iterator->key() ) && --$maxFetchCount >= 0 ) {
			$values[] = $this->iterator->current();
			$this->iterator->next();
		}

		return $values;
	}

	/**
	 * @see BatchingFetcher::rewind
	 */
	public function rewind() {
		$this->iterator->rewind();
	}

}