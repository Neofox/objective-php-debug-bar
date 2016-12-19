# Objective PHP / DebugBar Package [![Build Status](https://secure.travis-ci.org/Neofox/objective-php-debug-bar.png?branch=master)](http://travis-ci.org/Neofox/objective-php-debug-bar)

## Project introduction

This package allow to add a php debugbar in Objective PHP

## Requirement
For the moment this package will require two steps in your application:
- Route
- Rendering

Steps names will be configurable in a futur releases.

## Installation

### Manual

You can clone our Github repository by running:

```
git clone http://github.com/Neofox/objective-php-debug-bar
```

If you're to proceed this way, you probably don't need more explanation about how to use the library :)

### Composer

The easiest way to install the library and get ready to play with it is by using Composer. Run the following command in an empty folder you just created for Primitives:

```
composer require --dev neofox/objective-php-debug-bar:dev-master 
```
### Usage
Now that you get the package, you need to plug it in your application (Application.php):
```
$this->getStep('bootstrap')->plug(DebugBarPackage::class)
```
And you're done!

## How to test the work in progress?

### Run unit tests

First of all, please always run the unit tests suite. Our tests are written using PHPUnit, and can be run as follow:

```
composer test
```

### Configure the package

To be continued!




