<?php

namespace Tests\BatchingIterator;

use BatchingIterator\Fetchers\InMemoryBatchingFetcher;
use BatchingIterator\Fetchers\MultipleBatchingFetcher;
use PHPUnit\Framework\TestCase;

/**
 * @covers BatchingIterator\Fetchers\MultipleBatchingFetcher
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MultipleBatchingFetcherTest extends TestCase {

	public function testGivenNonFetcher_constructorThrowsException() {
		$this->expectException( 'InvalidArgumentException' );

		new MultipleBatchingFetcher(
			new InMemoryBatchingFetcher( [] ),
			new InMemoryBatchingFetcher( [] ),
			null,
			new InMemoryBatchingFetcher( [] )
		);
	}

	public function testGivenOneFetcher_behaviourIsNotAltered() {
		$values = [ 'foo', 'bar', 'baz' ];

		$fetcher = new MultipleBatchingFetcher( new InMemoryBatchingFetcher( $values ) );

		$this->assertEquals( $values, $fetcher->fetchNext( 4 ) );
		$this->assertEquals( [], $fetcher->fetchNext( 4 ) );
	}

	public function testGivenMultipleFetchers_allValuesCanBeFetched() {
		$fetcher = new MultipleBatchingFetcher(
			new InMemoryBatchingFetcher( [ 'foo', 'bar', 'baz' ] ),
			new InMemoryBatchingFetcher( [ '0', '1' ] ),
			new InMemoryBatchingFetcher( [] ),
			new InMemoryBatchingFetcher( [ '2' ] )
		);

		$this->assertEquals( [ 'foo', 'bar' ], $fetcher->fetchNext( 2 ) );
		$this->assertEquals( [ 'baz' ], $fetcher->fetchNext( 2 ) );
		$this->assertEquals( [ '0', '1' ], $fetcher->fetchNext( 3 ) );
		$this->assertEquals( [ '2' ], $fetcher->fetchNext( 3 ) );
		$this->assertEquals( [], $fetcher->fetchNext( 3 ) );
	}

	public function testGivenMultipleFetchers_rewindWorksCorrectly() {
		$fetcher = new MultipleBatchingFetcher(
			new InMemoryBatchingFetcher( [ 'foo', 'bar', 'baz' ] ),
			new InMemoryBatchingFetcher( [] ),
			new InMemoryBatchingFetcher( [ '0', '1' ] ),
			new InMemoryBatchingFetcher( [ '2' ] )
		);

		$fetcher->fetchNext( 2 );
		$fetcher->fetchNext( 2 );
		$fetcher->fetchNext( 2 );

		$fetcher->rewind();

		$this->assertEquals( [ 'foo' ], $fetcher->fetchNext( 1 ) );
	}

	public function testCanConstructWithArray() {
		new MultipleBatchingFetcher( [
			new InMemoryBatchingFetcher( [] ),
			new InMemoryBatchingFetcher( [] )
		] );

		$this->assertTrue( true );
	}

	public function testGivenArrayAndFetchers_constructorThrowsException() {
		$this->expectException( 'InvalidArgumentException' );

		new MultipleBatchingFetcher(
			[
				new InMemoryBatchingFetcher( [] ),
				new InMemoryBatchingFetcher( [] )
			],
			new InMemoryBatchingFetcher( [] )
		);
	}

	public function testGivenArrayWithNonFetcher_constructorThrowsException() {
		$this->expectException( 'InvalidArgumentException' );

		new MultipleBatchingFetcher(
			[
				new InMemoryBatchingFetcher( [] ),
				new InMemoryBatchingFetcher( [] ),
				null,
				new InMemoryBatchingFetcher( [] )
			]
		);
	}

}
