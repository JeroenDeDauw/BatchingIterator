<?php

declare( strict_types = 1 );

namespace BatchingIterator;

/**
 * @since 3.0
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface BatchingFetcher {

	/**
	 * Returns up to $maxFetchCount values.
	 * An empty array indicates there are no further results.
	 * Values are NOT ALLOWED TO BE NULL.
	 *
	 * @param int $maxFetchCount
	 *
	 * @return array
	 */
	public function fetchNext( int $maxFetchCount ): array;

	/**
	 * Rewind the BatchingFetcher to the first element.
	 */
	public function rewind();

}
