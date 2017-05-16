<?php

declare( strict_types = 1 );

namespace Tests\BatchingIterator;

use BatchingIterator\BatchingIterator;
use BatchingIterator\Fetchers\InMemoryBatchingFetcher;
use PHPUnit\Framework\TestCase;

/**
 * @covers BatchingIterator\BatchingIterator
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class BatchingIteratorTest extends TestCase {

	public function testWhenFetcherReturnsEmptyRightAway_iteratorIsEmpty() {
		$fetcher = $this->createMock( 'BatchingIterator\BatchingFetcher' );

		$fetcher->expects( $this->once() )
			->method( 'fetchNext' )
			->will( $this->returnValue( [] ) );

		$iterator = new BatchingIterator( $fetcher );

		$this->assertSame( [], iterator_to_array( $iterator ) );
	}

	/**
	 * @dataProvider batchSizeProvider
	 */
	public function testAllValuesEndUpInTheIterator( $batchSize ) {
		$values = [ 'foo', 'bar', 'baz', 'bah' ];

		$fetcher = new InMemoryBatchingFetcher( $values );
		$iterator = new BatchingIterator( $fetcher );
		$iterator->setMaxBatchSize( $batchSize );

		$this->assertSame( $values, iterator_to_array( $iterator ) );
	}

	public function batchSizeProvider() {
		return [
			[ 1 ],
			[ 2 ],
			[ 3 ],
			[ 10 ],
			[ 9001 ],
		];
	}

	public function testCorrectCallsAreMadeToTheBatchingFetcher() {
		$fetcher = $this->createMock( 'BatchingIterator\BatchingFetcher' );

		$fetcher->expects( $this->at( 0 ) )
			->method( 'rewind' );

		$fetcher->expects( $this->at( 1 ) )
			->method( 'fetchNext' )
			->with( $this->equalTo( 2 ) )
			->will( $this->returnValue( [ 'foo', 'bar' ] ) );

		$fetcher->expects( $this->at( 2 ) )
			->method( 'fetchNext' )
			->with( $this->equalTo( 2 ) )
			->will( $this->returnValue( [ 'baz' ] ) );

		$fetcher->expects( $this->at( 3 ) )
			->method( 'fetchNext' )
			->with( $this->equalTo( 2 ) )
			->will( $this->returnValue( [] ) );

		$iterator = new BatchingIterator( $fetcher );
		$iterator->setMaxBatchSize( 2 );

		$this->assertSame( [ 'foo', 'bar', 'baz' ], iterator_to_array( $iterator ) );
	}

	/**
	 * @dataProvider invalidBatchSizeProvider
	 */
	public function testSettingInvalidMaxBatchSizeCausesException( $invalidBatchSize ) {
		$iterator = new BatchingIterator( $this->createMock( 'BatchingIterator\BatchingFetcher' ) );

		$this->expectException( 'InvalidArgumentException' );
		$iterator->setMaxBatchSize( $invalidBatchSize );
	}

	public function invalidBatchSizeProvider() {
		return [
			[ 0 ],
			[ -5 ],
			[ 4.2 ],
			[ '1' ],
			[ null ],
			[ [] ],
		];
	}

	public function testWhenRewindingTheIterator_theFetcherIsAlsoRewinded() {
		$fetcher = $this->createMock( 'BatchingIterator\BatchingFetcher' );

		$fetcher->expects( $this->once() )
			->method( 'rewind' );

		$iterator = new BatchingIterator( $fetcher );

		$iterator->rewind();
	}

	public function testMultipleIteration() {
		$values = [ 'foo', 'bar', 'baz' ];

		$iterator = new BatchingIterator( new InMemoryBatchingFetcher( $values ) );

		$this->assertEquals( $values, iterator_to_array( $iterator ) );
		$this->assertEquals( $values, iterator_to_array( $iterator ) );
	}

}
