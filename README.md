# BatchingIterator

[![Build Status](https://secure.travis-ci.org/JeroenDeDauw/BatchingIterator.png?branch=master)](http://travis-ci.org/JeroenDeDauw/BatchingIterator)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/JeroenDeDauw/BatchingIterator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/JeroenDeDauw/BatchingIterator/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/JeroenDeDauw/BatchingIterator/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/JeroenDeDauw/BatchingIterator/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/jeroen-de-dauw/batching-iterator/version.png)](https://packagist.org/packages/jeroen-de-dauw/batching-iterator)
[![Download count](https://poser.pugx.org/jeroen-de-dauw/batching-iterator/d/total.png)](https://packagist.org/packages/jeroen-de-dauw/batching-iterator)

Iterator that batches requests for additional values. This is useful as a foundation for iterators over data that is in an expensive to access location, such as a database or a web API.

## Usage

Create a service that uses an `Iterator`.

```php
class TweetImporter {

    public function importTweets( Iterator $tweets ) {
        foreach ( $tweets as $tweet ) {
            $this->tweetStore->saveTweet( $tweet );
        }
    }

}
```

Note how this service only depends on Iterator. It is not aware of how the Iterator provides its results. You thus decoupled the service from who retrieves the results, and from when this happens. They could be comming from values already in memory, wrapped in an `ArrayIterator`, or be pulled from a web service as iteration happens. Using an `ArrayIterator` is very helpful for testing.

Implement the `BarchingFetcher` interface. If you already have a service to retrieve the data, this can be a simple wrapper.

```php
class BatchingTweetFetcher implements BatchingFetcher {

    public function fetchNext( $maxFetchCount ) {
        // Make a call to some external service to fetch $tweets
        return $tweets;
    }

    public function rewind() {
        // Go back to the first tweet
    }

}
```

Now you can easily instantiate the service, have the batching optimization, and have all responsibilities nicely decoupled.

```php
class TweetImportCli {

    public function importTweets() {
        $tweetIterator = new BatchingIterator( new BatchingTweetFetcher() );
        $tweetIterator->setMaxBatchSize( 42 );
        
        $this->tweetImporter->importTweets( $tweetIterator );
    }

}
```

## Installation

You can use [Composer](http://getcomposer.org/) to download and install
this package as well as its dependencies.

To add this package as a local, per-project dependency to your project, simply add a
dependency on `jeroen-de-dauw/batching-iterator` to your project's `composer.json` file.
Here is a minimal example of a `composer.json` file that just defines a dependency on
BatchingIterator 1.0:

```js
{
    "require": {
        "jeroen-de-dauw/batching-iterator": "1.0.*"
    }
}
```

## Release notes

#### Version 2.0 (dev)

* The `BatchingFetcher` now has a `rewind` method

#### Version 1.0 (2014-07-03)

Initial release with

* `BatchingIterator` class
* `BatchingFetcher` interface
* `InMemoryBatchingFetcher` trivial implementation of `BatchingFetcher`
