<?php

namespace BatchingIterator;

/**
 * @since 1.0
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface BatchingFetcher {

	/**
	 * Fetches the next n values for a BatchingFetcher.
	 * Should return an empty array when there are no further results.
	 * Values are not allowed to be null.
	 *
	 * @param int $maxFetchCount
	 *
	 * @return mixed[]
	 */
	public function fetchNext( $maxFetchCount );

	/**
	 * Rewind the BatchingFetcher to the first element.
	 */
	public function rewind();

}
