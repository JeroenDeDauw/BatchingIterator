<?php

namespace Tests\BatchingIterator;

use BatchingIterator\BatchingIterator;
use BatchingIterator\InMemoryEntitiesFetcher;

/**
 * @covers BatchingIterator\BatchingIterator
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class BatchingIteratorTest extends \PHPUnit_Framework_TestCase {

	public function testWhenFetcherReturnsEmptyRightAway_iteratorIsEmpty() {
		$fetcher = $this->getMock( 'BatchingIterator\BatchingFetcher' );

		$fetcher->expects( $this->once() )
			->method( 'fetchNext' )
			->will( $this->returnValue( array() ) );

		$iterator = new BatchingIterator( $fetcher );

		$this->assertSame( array(), iterator_to_array( $iterator ) );
	}

	/**
	 * @dataProvider batchSizeProvider
	 */
	public function testAllValuesEndUpInTheIterator( $batchSize ) {
		$values = array( 'foo', 'bar', 'baz', 'bah' );

		$fetcher = new InMemoryEntitiesFetcher( $values );
		$iterator = new BatchingIterator( $fetcher );
		$iterator->setMaxBatchSize( $batchSize );

		$this->assertSame( $values, iterator_to_array( $iterator ) );
	}

	public function batchSizeProvider() {
		return array(
			array( 1 ),
			array( 2 ),
			array( 3 ),
			array( 10 ),
			array( 9001 ),
		);
	}

	public function testCorrectCallsAreMadeToTheBatchingFetcher() {
		$fetcher = $this->getMock( 'BatchingIterator\BatchingFetcher' );

		$fetcher->expects( $this->at( 0 ) )
			->method( 'fetchNext' )
			->with( $this->equalTo( 2 ) )
			->will( $this->returnValue( array( 'foo', 'bar' ) ) );

		$fetcher->expects( $this->at( 1 ) )
			->method( 'fetchNext' )
			->with( $this->equalTo( 2 ) )
			->will( $this->returnValue( array( 'baz' ) ) );

		$fetcher->expects( $this->at( 2 ) )
			->method( 'fetchNext' )
			->with( $this->equalTo( 2 ) )
			->will( $this->returnValue( array() ) );

		$iterator = new BatchingIterator( $fetcher );
		$iterator->setMaxBatchSize( 2 );

		$this->assertSame( array( 'foo', 'bar', 'baz' ), iterator_to_array( $iterator ) );
	}

	/**
	 * @dataProvider invalidBatchSizeProvider
	 */
	public function testSettingInvalidMaxBatchSizeCausesException( $invalidBatchSize ) {
		$iterator = new BatchingIterator( $this->getMock( 'BatchingIterator\BatchingFetcher' ) );

		$this->setExpectedException( 'InvalidArgumentException' );
		$iterator->setMaxBatchSize( $invalidBatchSize );
	}

	public function invalidBatchSizeProvider() {
		return array(
			array( 0 ),
			array( -5 ),
			array( 4.2 ),
			array( '1' ),
			array( null ),
			array( array() ),
		);
	}

}
