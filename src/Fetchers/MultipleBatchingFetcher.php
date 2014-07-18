<?php

namespace BatchingIterator\Fetchers;

use BatchingIterator\BatchingFetcher;
use InvalidArgumentException;

/**
 * A fetcher that sequentially fetches values from its child fetchers.
 * Comparable to @see MultipleIterator
 *
 * Recursion might be used over the fetchers. So a big amount of fetchers
 * can cause the stack to be blown.
 *
 * @since 1.0
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MultipleBatchingFetcher implements BatchingFetcher {

	/**
	 * @var BatchingFetcher[]
	 */
	private $fetchers;

	/**
	 * @param BatchingFetcher $firstFetcher
	 * @param BatchingFetcher ...
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( BatchingFetcher $firstFetcher ) {
		foreach ( func_get_args() as $fetcher ) {
			if ( !( $fetcher instanceof BatchingFetcher ) ) {
				throw new InvalidArgumentException( 'All constructor arguments should implement BatchingFetcher' );
			}

			$this->attachFetcher( $fetcher );
		}
	}

	private function attachFetcher( BatchingFetcher $fetcher ) {
		$this->fetchers[] = $fetcher;
	}

	/**
	 * @see BatchingFetcher::fetchNext
	 *
	 * @param int $maxFetchCount
	 *
	 * @return mixed[]
	 */
	public function fetchNext( $maxFetchCount ) {
		if ( key( $this->fetchers ) === null ) {
			return array();
		}

		/**
		 * @var BatchingFetcher $fetcher
		 */
		$fetcher = current( $this->fetchers );

		$results = $fetcher->fetchNext( $maxFetchCount );

		if ( !empty( $results ) ) {
			return $results;
		}

		next( $this->fetchers );
		return $this->fetchNext( $maxFetchCount );
	}

	/**
	 * @see BatchingFetcher::rewind
	 */
	public function rewind() {
		foreach ( $this->fetchers as $fetcher ) {
			$fetcher->rewind();
		}

		reset( $this->fetchers );
	}

}