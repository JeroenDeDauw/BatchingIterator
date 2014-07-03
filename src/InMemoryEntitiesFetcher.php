<?php

namespace BatchingIterator;

/**
 * Trivial implementation of BatchingFetcher that uses an in-memory array.
 * Values are returned in first in, first out fashion.
 *
 * @since 1.0
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class InMemoryEntitiesFetcher implements BatchingFetcher {

	private $values;

	public function __construct( $values ) {
		$this->values = $values;
	}

	/**
	 * @see EntitiesFetcher::fetchNext
	 *
	 * @param int $maxFetchCount
	 *
	 * @return mixed[]
	 */
	public function fetchNext( $maxFetchCount ) {
		$values = array();

		while ( !empty( $this->values ) && --$maxFetchCount >= 0 ) {
			$values[] = array_shift( $this->values );
		}

		return $values;
	}

}