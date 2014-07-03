<?php

namespace Tests\BatchingIterator;

use BatchingIterator\InMemoryEntitiesFetcher;

/**
 * @covers BatchingIterator\InMemoryEntitiesFetcher
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class InMemoryEntitiesFetcherTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider fetchCountProvider
	 */
	public function testGivenNoValues_fetchNextReturnEmptyArray( $fetchCount ) {
		$fetcher = new InMemoryEntitiesFetcher( array() );

		$this->assertSame(
			array(),
			$fetcher->fetchNext( $fetchCount )
		);
	}

	public function fetchCountProvider() {
		return array(
			array( 1 ),
			array( 2 ),
			array( 3 ),
			array( 10 ),
			array( 9001 ),
		);
	}

	public function testGivenOneValue_onlyFirstFetchReturnsIt() {
		$fetcher = new InMemoryEntitiesFetcher( array( 'foo' ) );

		$this->assertSame( array( 'foo' ), $fetcher->fetchNext( 1 ) );
		$this->assertSame( array(), $fetcher->fetchNext( 1 ) );
	}

	public function testGivenOneValue_fetchingTwoReturnsOnlyOne() {
		$fetcher = new InMemoryEntitiesFetcher( array( 'foo' ) );

		$this->assertSame( array( 'foo' ), $fetcher->fetchNext( 2 ) );
	}

	public function testSuccessiveFetchesGivenMultipleValues() {
		$fetcher = new InMemoryEntitiesFetcher( array( 'foo', 'bar', 'baz', 'bah' ) );

		$this->assertSame( array( 'foo' ), $fetcher->fetchNext( 1 ) );
		$this->assertSame( array( 'bar', 'baz' ), $fetcher->fetchNext( 2 ) );
		$this->assertSame( array( 'bah' ), $fetcher->fetchNext( 3 ) );
		$this->assertSame( array(), $fetcher->fetchNext( 2 ) );
	}

}
