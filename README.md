# BatchingIterator

[![Build Status](https://secure.travis-ci.org/JeroenDeDauw/BatchingIterator.png?branch=master)](http://travis-ci.org/JeroenDeDauw/BatchingIterator)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/JeroenDeDauw/BatchingIterator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/JeroenDeDauw/BatchingIterator/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/JeroenDeDauw/BatchingIterator/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/JeroenDeDauw/BatchingIterator/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/jeroen-de-dauw/batching-iterator/version.png)](https://packagist.org/packages/jeroen-de-dauw/batching-iterator)
[![Download count](https://poser.pugx.org/jeroen-de-dauw/batching-iterator/d/total.png)](https://packagist.org/packages/jeroen-de-dauw/batching-iterator)

Iterator that batches requests for more values

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


#### Version 1.0 (2014-07-03)

Initial release with

* `BatchingIterator` class
* `BatchingFetcher` interface
* `InMemoryBatchingFetcher` trivial implementation of `BatchingFetcher`
