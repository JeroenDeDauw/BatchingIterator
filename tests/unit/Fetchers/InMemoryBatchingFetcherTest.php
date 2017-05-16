<?php

namespace Tests\BatchingIterator;

use BatchingIterator\Fetchers\InMemoryBatchingFetcher;
use PHPUnit\Framework\TestCase;

/**
 * @covers BatchingIterator\Fetchers\InMemoryBatchingFetcher
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class InMemoryBatchingFetcherTest extends TestCase {

	/**
	 * @dataProvider fetchCountProvider
	 */
	public function testGivenNoValues_fetchNextReturnEmptyArray( $fetchCount ) {
		$fetcher = new InMemoryBatchingFetcher( [] );

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
		$fetcher = new InMemoryBatchingFetcher( [ 'foo' ] );

		$this->assertSame( [ 'foo' ], $fetcher->fetchNext( 1 ) );
		$this->assertSame( [], $fetcher->fetchNext( 1 ) );
	}

	public function testGivenOneValue_fetchingTwoReturnsOnlyOne() {
		$fetcher = new InMemoryBatchingFetcher( [ 'foo' ] );

		$this->assertSame( [ 'foo' ], $fetcher->fetchNext( 2 ) );
	}

	public function testSuccessiveFetchesGivenMultipleValues() {
		$fetcher = new InMemoryBatchingFetcher( [ 'foo', 'bar', 'baz', 'bah' ] );

		$this->assertSame( [ 'foo' ], $fetcher->fetchNext( 1 ) );
		$this->assertSame( [ 'bar', 'baz' ], $fetcher->fetchNext( 2 ) );
		$this->assertSame( [ 'bah' ], $fetcher->fetchNext( 3 ) );
		$this->assertSame( [], $fetcher->fetchNext( 2 ) );
	}

	public function testWhenResultsHaveRunOut_rewindGoesToFirstElement() {
		$fetcher = new InMemoryBatchingFetcher( [ 'foo', 'bar', 'baz', 'bah' ] );

		$fetcher->fetchNext( 10 );
		$fetcher->rewind();

		$this->assertSame( [ 'foo' ], $fetcher->fetchNext( 1 ) );
	}

	public function testWhenSomeFetchingHasHappened_rewindGoesToFirstElement() {
		$fetcher = new InMemoryBatchingFetcher( [ 'foo', 'bar', 'baz', 'bah' ] );

		$fetcher->fetchNext( 2 );
		$fetcher->rewind();

		$this->assertSame( [ 'foo' ], $fetcher->fetchNext( 1 ) );
	}

	public function testWhenNoFetchingHasHappened_rewindStaysAtTheFirstElement() {
		$fetcher = new InMemoryBatchingFetcher( [ 'foo', 'bar', 'baz', 'bah' ] );

		$fetcher->rewind();

		$this->assertSame( [ 'foo' ], $fetcher->fetchNext( 1 ) );
	}

}
