<?php

declare( strict_types = 1 );

namespace Tests\BatchingIterator;

use BatchingIterator\Fetchers\IteratorBasedBatchingFetcher;
use PHPUnit\Framework\TestCase;

/**
 * @covers BatchingIterator\Fetchers\IteratorBasedBatchingFetcher
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class IteratorBasedBatchingFetcherTest extends TestCase {

	/**
	 * @dataProvider fetchCountProvider
	 */
	public function testGivenNoValues_fetchNextReturnEmptyArray( $fetchCount ) {
		$fetcher = new IteratorBasedBatchingFetcher( new \ArrayIterator() );

		$this->assertSame(
			[],
			$fetcher->fetchNext( $fetchCount )
		);
	}

	public function fetchCountProvider() {
		return [
			[ 1 ],
			[ 2 ],
			[ 3 ],
			[ 10 ],
			[ 9001 ],
		];
	}

	public function testGivenOneValue_onlyFirstFetchReturnsIt() {
		$fetcher = new IteratorBasedBatchingFetcher( new \ArrayIterator( [ 'foo' ] ) );

		$this->assertSame( [ 'foo' ], $fetcher->fetchNext( 1 ) );
		$this->assertSame( [], $fetcher->fetchNext( 1 ) );
	}

	public function testGivenOneValue_fetchingTwoReturnsOnlyOne() {
		$fetcher = new IteratorBasedBatchingFetcher( new \ArrayIterator( [ 'foo' ] ) );

		$this->assertSame( [ 'foo' ], $fetcher->fetchNext( 2 ) );
	}

	public function testSuccessiveFetchesGivenMultipleValues() {
		$fetcher = new IteratorBasedBatchingFetcher( new \ArrayIterator( [ 'foo', 'bar', 'baz', 'bah' ] ) );

		$this->assertSame( [ 'foo' ], $fetcher->fetchNext( 1 ) );
		$this->assertSame( [ 'bar', 'baz' ], $fetcher->fetchNext( 2 ) );
		$this->assertSame( [ 'bah' ], $fetcher->fetchNext( 3 ) );
		$this->assertSame( [], $fetcher->fetchNext( 2 ) );
	}

	public function testWhenResultsHaveRunOut_rewindGoesToFirstElement() {
		$fetcher = new IteratorBasedBatchingFetcher( new \ArrayIterator( [ 'foo', 'bar', 'baz', 'bah' ] ) );

		$fetcher->fetchNext( 10 );
		$fetcher->rewind();

		$this->assertSame( [ 'foo' ], $fetcher->fetchNext( 1 ) );
	}

	public function testWhenSomeFetchingHasHappened_rewindGoesToFirstElement() {
		$fetcher = new IteratorBasedBatchingFetcher( new \ArrayIterator( [ 'foo', 'bar', 'baz', 'bah' ] ) );

		$fetcher->fetchNext( 2 );
		$fetcher->rewind();

		$this->assertSame( [ 'foo' ], $fetcher->fetchNext( 1 ) );
	}

	public function testWhenNoFetchingHasHappened_rewindStaysAtTheFirstElement() {
		$fetcher = new IteratorBasedBatchingFetcher( new \ArrayIterator( [ 'foo', 'bar', 'baz', 'bah' ] ) );

		$fetcher->rewind();

		$this->assertSame( [ 'foo' ], $fetcher->fetchNext( 1 ) );
	}

}
