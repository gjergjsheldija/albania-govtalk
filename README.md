# PHP GovTalk

**A library for applications which interface with the Albanian Government Gateway**

[![Build Status](https://travis-ci.org/gjergjsheldija/albania-govtalk.png?branch=master)](https://travis-ci.org/gjergjsheldija/albania-govtalk)
[![Latest Stable Version](https://poser.pugx.org/justinbusschau/php-govtalk/version.png)](https://packagist.org/packages/gjergjsheldija/albania-govtalk)
[![Total Downloads](https://poser.pugx.org/justinbusschau/php-govtalk/d/total.png)](https://packagist.org/packages/gjergjsheldija/albania-govtalk)
[![License](https://poser.pugx.org/justinbusschau/php-govtalk/license.svg)](https://packagist.org/packages/gjergjsheldija/albania-govtalk)

The GovTalk Message Envelope is a standard developed by the United Kingdom government as a means of encapsulating
a range of government XML services in a single standard data format.

This project was originally forked from [Ignited](https://github.com/ignited/php-govtalk). GovTalk and the DPGJ
class is preserved in this library. This library can be used whenever you need to build something that interfaces with any
of the services that use the Government Gateway.

## Installation

The library can be installed via [Composer](http://getcomposer.org/). To install, simply add
it to your `composer.json` file:

```json
{
    "require": {
        "gjergjsheldija/albania-govtalk": "0.*"
    }
}
```

And run composer to update your dependencies:

$ curl -s http://getcomposer.org/installer | php
$ php composer.phar update


## Basic usage

This library can be extended and used with any one of the gateways that use the GovTalk Message Envelope and the
Document Submission Protocol.
