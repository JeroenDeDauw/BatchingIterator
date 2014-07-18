<?php

namespace Tests\BatchingIterator;

use BatchingIterator\Fetchers\InMemoryBatchingFetcher;
use BatchingIterator\Fetchers\MultipleBatchingFetcher;

/**
 * @covers BatchingIterator\Fetchers\MultipleBatchingFetcher
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MultipleBatchingFetcherTest extends \PHPUnit_Framework_TestCase {

	public function testGivenNonFetcher_constructorThrowsException() {
		$this->setExpectedException( 'InvalidArgumentException' );

		new MultipleBatchingFetcher(
			new InMemoryBatchingFetcher( array() ),
			new InMemoryBatchingFetcher( array() ),
			null,
			new InMemoryBatchingFetcher( array() )
		);
	}

	public function testGivenOneFetcher_behaviourIsNotAltered() {
		$values = array( 'foo', 'bar', 'baz' );

		$fetcher = new MultipleBatchingFetcher( new InMemoryBatchingFetcher( $values ) );

		$this->assertEquals( $values, $fetcher->fetchNext( 4 ) );
		$this->assertEquals( array(), $fetcher->fetchNext( 4 ) );
	}

	public function testGivenMultipleFetchers_allValuesCanBeFetched() {
		$fetcher = new MultipleBatchingFetcher(
			new InMemoryBatchingFetcher( array( 'foo', 'bar', 'baz' ) ),
			new InMemoryBatchingFetcher( array( '0', '1' ) ),
			new InMemoryBatchingFetcher( array() ),
			new InMemoryBatchingFetcher( array( '2' ) )
		);

		$this->assertEquals( array( 'foo', 'bar' ), $fetcher->fetchNext( 2 ) );
		$this->assertEquals( array( 'baz' ), $fetcher->fetchNext( 2 ) );
		$this->assertEquals( array( '0', '1' ), $fetcher->fetchNext( 3 ) );
		$this->assertEquals( array( '2' ), $fetcher->fetchNext( 3 ) );
		$this->assertEquals( array(), $fetcher->fetchNext( 3 ) );
	}

	public function testGivenMultipleFetchers_rewindWorksCorrectly() {
		$fetcher = new MultipleBatchingFetcher(
			new InMemoryBatchingFetcher( array( 'foo', 'bar', 'baz' ) ),
			new InMemoryBatchingFetcher( array() ),
			new InMemoryBatchingFetcher( array( '0', '1' ) ),
			new InMemoryBatchingFetcher( array( '2' ) )
		);

		$fetcher->fetchNext( 2 );
		$fetcher->fetchNext( 2 );
		$fetcher->fetchNext( 2 );

		$fetcher->rewind();

		$this->assertEquals( array( 'foo' ), $fetcher->fetchNext( 1 ) );
	}

}
